<?php
require_once '/include/functions.php';
require_once '/include/db_access.php';

$dbObject = new Databases();

$action = $_POST['action'];

switch($action) { //Switch case for value of action
  case "login": 
    login();       
  break;
  case "get_balance": 
    get_balance();       
  break;
}
 
function login()
{                  
    $resultset = $GLOBALS['dbObject']->runQuery("SELECT * FROM t_customers WHERE login_str = '".mysqli_real_escape_string($GLOBALS['dbObject']->conn,$_POST['login_str'])."' AND password = '".mysqli_real_escape_string($GLOBALS['dbObject']->conn,$_POST['password'])."' LIMIT 1;");    
    
    if(mysqli_num_rows($resultset) === 0)
    {
        //Failed login
        $jsonResponse = array('status' => 'Fail', 'error' => 'Invalid request');
        $jsonResponse = json_encode($jsonResponse);
        
        echo $jsonResponse;
        exit;
    }else{
        $row = $resultset->fetch_array(MYSQLI_ASSOC);
        
        //Generare session id
        $random = substr(number_format(time() * mt_rand(),0,'',''),0,10);
        $str = $row['cust_id'].$row['company_name'].$row['contact_num'].$random;
        $hashString = md5($str);
        
        //Insert session id        
        if (!$GLOBALS['dbObject']->runQuery("INSERT INTO t_customerslogin (session_id,cust_id,expire_date) VALUES ('".$hashString."','".$row['cust_id']."',DATE_ADD(now(), INTERVAL 1 DAY));")) 
        {            
            $jsonResponse = array('status' => 'Fail', 'error' => 'Failed to generate session, please contact support');
            $jsonResponse = json_encode($jsonResponse);
            writelog("log.log", "[LOGIN] Failed to insert sessionid: " .mysqli_error($this->conn));
            exit;
        }
        
        //Success login        
        $jsonResponse = array('status' => 'Success', 'error' => '','session_id'=>$hashString);
        $jsonResponse = json_encode($jsonResponse);
        
        echo $jsonResponse;
        exit;
    }
  //echo json_encode($return);
}

function get_balance()
{    
    if(!checksessionid($_POST['session_id']))
    {
        $jsonResponse = array('status' => 'Fail', 'error' => 'Invalid request');
        $jsonResponse = json_encode($jsonResponse);
        
        echo $jsonResponse;
        exit;
    }    
    
    //{"action":"get_balance","session_id":$('#sessionid').val(),"userid":$('#userid').val()}
    //Get balances for this user
    $resultset = $GLOBALS['dbObject']->runQuery("SELECT * FROM t_wallet WHERE uid = '".mysqli_real_escape_string($GLOBALS['dbObject']->conn,$_POST['uid'])."' LIMIT 1;");            
    $row = $resultset->fetch_array(MYSQLI_ASSOC); 
    
    if(mysqli_num_rows($resultset) === 0)
    {        
        $jsonResponse = array('status' => 'Fail', 'error' => 'Could not find this userid:'.$_POST['uid'].'');
        $jsonResponse = json_encode($jsonResponse);
        
        echo $jsonResponse;
        exit;
    }else{                               
        log_wallet_audit('wa0004',$_POST['uid'],'','','','');
                
        $jsonResponse = array('status' => 'Success', 'error' => '','pool_balance' => $row['pool_balance'],'kcm_wallet_balance' => $row['kcm_wallet'], 'glo_wallet_balance' => $row['glo_wallet']);                                                  
        $jsonResponse = json_encode($jsonResponse);
        
        echo $jsonResponse;
        exit;
    }
}


//Check sessionid validity
function checksessionid($sessionid)
{
    $resultset = $GLOBALS['dbObject']->runQuery("SELECT count(*) FROM t_customerslogin WHERE session_id = '".mysqli_real_escape_string($GLOBALS['dbObject']->conn,$sessionid)."' AND (DATEDIFF(`expire_date`,now())) > 0 LIMIT 1;");
    
    if(mysqli_num_rows($resultset) === 0)
    {
        return false;
    }else{
        return true;
    }
}

function log_wallet_audit($action,$uid,$value,$sourcepool,$destinationpool,$voucherserial)
{
    if (!$GLOBALS['dbObject']->runQuery("INSERT INTO t_walletaudit (uid,action,value,source_pool,destination_pool,voucher_serial_number) VALUES ('".$uid."','".$action.",'".$value.",'".$sourcepool.",'".$destinationpool.",'".$voucherserial.";")) 
    {                   
        writelog("log.log", "[AUDIT LOG] Failed to write to audit log");
        exit;
    }else{
        $resultset = $GLOBALS['dbObject']->runQuery("SELECT LAST_INSERT_ID() as id FROM t_walletaudit;");            
        $row = $resultset->fetch_array(MYSQLI_ASSOC); 
        
        $GLOBALS['dbObject']->runQuery("UPDATE t_walletaudit SET w_reference='".str_pad($row['id'], 6, '0', STR_PAD_LEFT)."' WHERE Id='".$row['id']."';");    
    }
}