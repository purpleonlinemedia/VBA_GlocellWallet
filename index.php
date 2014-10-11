<?php
require_once 'include/functions.php';
require_once 'include/db_access.php';

$dbObject = new Databases();

$dataPOST = trim(file_get_contents('php://input'));

$xml = simplexml_load_string($dataPOST);

$action = $xml->action;

switch($action) { //Switch case for value of action
  case "login": 
    login($xml);       
  break;
  case "get_balance": 
    get_balance($xml);       
  break;
  case "get_statement": 
    get_statement($xml);       
  break;
  case "funds_transfer":
      funds_transfer($xml);       
  break;
  case "load_gcash":
      load_gcash($xml);
  break;
}
 
function login($xml)
{                  
    $resultset = $GLOBALS['dbObject']->runQuery("SELECT * FROM t_customers WHERE login_str = '".mysqli_real_escape_string($GLOBALS['dbObject']->conn,$xml->login_str)."' AND password = '".mysqli_real_escape_string($GLOBALS['dbObject']->conn,$xml->password)."' LIMIT 1;");


    if(mysqli_num_rows($resultset) === 0)
    {
        //Failed login
        $responseString = <<<XML
<?xml version='1.0'?>
<serviceResponse>                 
 <status>Fail</status>
 <error>Invalid request</error> 
</serviceResponse>
XML;
        
        $xml = new SimpleXMLElement($responseString);

        echo $xml->asXML();
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
            $responseString = <<<XML
<?xml version='1.0'?>
<serviceResponse>                 
 <status>Fail</status>
 <error>Failed to generate session, please contact support</error> 
</serviceResponse>
XML;
        
        $xml = new SimpleXMLElement($responseString);

        echo $xml->asXML();
        exit;                       
        }
        
        $responseString = <<<XML
<?xml version='1.0'?>
<serviceResponse>                 
 <status>Success</status>
 <error></error>
 <session_id>$hashString</session_id>
</serviceResponse>
XML;

        $xml = new SimpleXMLElement($responseString);

        echo $xml->asXML();
        exit;            
    }
  //echo json_encode($return);
}

function get_balance($xml)
{    
    if(!checksessionid($xml->session_id))
    {
        $responseString = <<<XML
<?xml version='1.0'?>
<serviceResponse>                 
 <status>Fail</status>
 <error>Invalid request</error> 
</serviceResponse>
XML;
        
        $xml = new SimpleXMLElement($responseString);

        echo $xml->asXML();
        exit;
                
    }    
        
    //Get balances for this user
    $resultset = $GLOBALS['dbObject']->runQuery("SELECT * FROM t_wallet WHERE uid = '".mysqli_real_escape_string($GLOBALS['dbObject']->conn,$xml->uid)."' LIMIT 1;");            
    $row = $resultset->fetch_array(MYSQLI_ASSOC); 
    
    if(mysqli_num_rows($resultset) === 0)
    {        
        $responseString = <<<XML
<?xml version='1.0'?>
<serviceResponse>                 
 <status>Fail</status>
 <error>Could not find this userid:$xml->uid</error> 
</serviceResponse>
XML;
        
        $xml = new SimpleXMLElement($responseString);

        echo $xml->asXML();
        exit;        
    }else{                               
        log_wallet_audit('wa0004',$xml->uid,'','','','');
        
        $poolbalance = $row['pool_balance'];
        $kcm_wallet = $row['kcm_wallet'];
        $glo_wallet = $row['glo_wallet'];
        
        $responseString = <<<XML
<?xml version='1.0'?>
<serviceResponse>                 
 <status>Success</status>
 <error></error> 
 <pool_balance>$poolbalance</pool_balance> 
 <kcm_wallet_balance>$kcm_wallet</kcm_wallet_balance>                 
 <glo_wallet_balance>$glo_wallet</glo_wallet_balance>                 
</serviceResponse>
XML;
        
        $xml = new SimpleXMLElement($responseString);

        echo $xml->asXML();
        exit;        
    }
}

function get_statement($xml)
{
    if(!checksessionid($xml->session_id))
    {
        $responseString = <<<XML
<?xml version='1.0'?>
<serviceResponse>                 
 <status>Fail</status>
 <error>Invalid request</error> 
</serviceResponse>
XML;
        
        $xml = new SimpleXMLElement($responseString);

        echo $xml->asXML();
        exit;
                
    } 
    
    //Get mini statement for this user
    $resultset = $GLOBALS['dbObject']->runQuery("SELECT tw.trans_date,tc.shortdesc,tw.value FROM t_walletaudit tw,t_codes tc WHERE tw.uid = '".mysqli_real_escape_string($GLOBALS['dbObject']->conn,$xml->uid)."' AND tw.action=tc.code ORDER BY tw.trans_date DESC LIMIT 5;");                    

    if(mysqli_num_rows($resultset) === 0)
    {
        $responseString = <<<XML
<?xml version='1.0'?>
<serviceResponse>
 <status>Fail</status>
 <error>Could not transactions for this userid:$xml->uid</error>
</serviceResponse>
XML;

        $xml = new SimpleXMLElement($responseString);

        echo $xml->asXML();
        exit;
    }else{                               
        log_wallet_audit('wa0005',$xml->uid,'','','','');
             
        $statement = "";
        
        while($row = $resultset->fetch_array(MYSQLI_ASSOC))
        {
            $statement .= $row['trans_date'].",".$row['shortdesc'].",".$row['value']."|";
        }

        $statement = rtrim($statement, "|");

        $responseString = <<<XML
<?xml version='1.0'?>
<serviceResponse>
 <status>Success</status>
 <error></error>
 <statement>$statement</statement>
</serviceResponse>
XML;

        $xml = new SimpleXMLElement($responseString);

        echo $xml->asXML();
        exit;
    }
}

function load_gcash($xml)
{
    if(!checksessionid($xml->session_id))
    {
        $responseString = <<<XML
<?xml version='1.0'?>
<serviceResponse>
 <status>Fail</status>
 <error>Invalid request</error>
</serviceResponse>
XML;

        $xml = new SimpleXMLElement($responseString);

        echo $xml->asXML();
        exit;

    }

    //Check if voucher exists
    $resultset = $GLOBALS['dbObject']->runQuery("SELECT value,product FROM t_voucher WHERE voucher_number = '".mysqli_real_escape_string($GLOBALS['dbObject']->conn,$xml->voucher_number)."' AND allocated = 0;");

    if(mysqli_num_rows($resultset) === 0)
    {
        $responseString = <<<XML
<?xml version='1.0'?>
<serviceResponse>
 <status>Fail</status>
 <error>Invalid voucher number</error>
</serviceResponse>
XML;

        $xml = new SimpleXMLElement($responseString);

        echo $xml->asXML();
        exit;
    }else{
        $row = $resultset->fetch_array(MYSQLI_ASSOC);

        #Audit
        $wreference = log_wallet_audit($row['product'],$xml->uid,$row['value'],'','',$xml->voucher_number);

        //Update the voucher #w_reference_allocated, redeemed_date and w_reference_redeemed
        $GLOBALS['dbObject']->runQuery("UPDATE t_voucher SET `w_reference_allocated` = '$wreference',`w_reference_redeemed` = '$wreference',redeem_date=now() WHERE voucher_number = '".mysqli_real_escape_string($GLOBALS['dbObject']->conn,$xml->voucher_number)."';");

        //Update his pool balance
        $GLOBALS['dbObject']->runQuery("UPDATE t_wallet SET `pool_balance` = (`pool_balance`+".$row['value']."),last_transaction_date=now() WHERE uid = '".mysqli_real_escape_string($GLOBALS['dbObject']->conn,$xml->uid)."';");


        $responseString = <<<XML
<?xml version='1.0'?>
<serviceResponse>
 <status>Success</status>
 <error></error>
</serviceResponse>
XML;

        $xml = new SimpleXMLElement($responseString);

        echo $xml->asXML();
        exit;
    }
}

function funds_transfer($xml)
{
    if(!checksessionid($xml->session_id))
    {
        $responseString = <<<XML
<?xml version='1.0'?>
<serviceResponse>                 
 <status>Fail</status>
 <error>Invalid request</error> 
</serviceResponse>
XML;
        
        $xml = new SimpleXMLElement($responseString);

        echo $xml->asXML();
        exit;
                
    }
    
    $resultset = $GLOBALS['dbObject']->runQuery("SELECT count(*) as walletexists FROM t_wallet WHERE uid = '".mysqli_real_escape_string($GLOBALS['dbObject']->conn,$xml->uid)."';");

    $row = $resultset->fetch_array(MYSQLI_ASSOC);
            
    if($row['walletexists'] === 0)
    {
        $responseString = <<<XML
<?xml version='1.0'?>
<serviceResponse>
 <status>Fail</status>
 <error>Could not find wallet for this userid:$xml->uid</error>
</serviceResponse>
XML;

        $xml = new SimpleXMLElement($responseString);

        echo $xml->asXML();
        exit;
    }

    if($xml->destacc == 'pool')
    {
        $destacc = 'pool_balance';
    }
    if($xml->destacc == 'glocell')
    {
        $destacc = 'glo_wallet';
    }
    if($xml->destacc == 'kcmobile')
    {
        $destacc = 'kcm_wallet';
    }

    if($xml->sourceacc == 'pool')
    {
        $sourceacc = 'pool_balance';
        $resultset = $GLOBALS['dbObject']->runQuery("SELECT * FROM t_wallet WHERE pool_balance >= '".mysqli_real_escape_string($GLOBALS['dbObject']->conn,$xml->value)."' AND uid = '".mysqli_real_escape_string($GLOBALS['dbObject']->conn,$xml->uid)."';");

        if(mysqli_num_rows($resultset) === 0)
        {
            $responseString = <<<XML
<?xml version='1.0'?>
<serviceResponse>
 <status>Fail</status>
 <error>Your source account does not have enough funds to complete this transaction</error>
</serviceResponse>
XML;

            $xml = new SimpleXMLElement($responseString);

            echo $xml->asXML();
            exit;
            
        }else{
            #Update balances
            $GLOBALS['dbObject']->runQuery("UPDATE t_wallet SET `$destacc` = (`$destacc`+".$xml->value."),`$sourceacc` = (`$sourceacc`-".$xml->value."),last_transaction_date=now() WHERE uid = '".mysqli_real_escape_string($GLOBALS['dbObject']->conn,$xml->uid)."';");

            #Audit
            log_wallet_audit('wa0006',$xml->uid,$xml->value,$sourceacc,$destacc,'');

            $responseString = <<<XML
<?xml version='1.0'?>
<serviceResponse>
 <status>Success</status>
 <error></error>
</serviceResponse>
XML;

            $xml = new SimpleXMLElement($responseString);

            echo $xml->asXML();
            exit;
        }
    }
    else if($xml->sourceacc == 'glocell')
    {
        $sourceacc = 'glo_wallet';
        $resultset = $GLOBALS['dbObject']->runQuery("SELECT * FROM t_wallet WHERE glo_wallet >= '".mysqli_real_escape_string($GLOBALS['dbObject']->conn,$xml->value)."' AND uid = '".mysqli_real_escape_string($GLOBALS['dbObject']->conn,$xml->uid)."';");
        
        if(mysqli_num_rows($resultset) === 0)
        {
            $responseString = <<<XML
<?xml version='1.0'?>
<serviceResponse>
 <status>Fail</status>
 <error>Your source account does not have enough funds to complete this transaction</error>
</serviceResponse>
XML;

            $xml = new SimpleXMLElement($responseString);

            echo $xml->asXML();
            exit;
            
        }else{
            #Update balances
            $GLOBALS['dbObject']->runQuery("UPDATE t_wallet SET `$destacc` = (`$destacc`+".$xml->value."),`$sourceacc` = (`$sourceacc`-".$xml->value."),last_transaction_date=now() WHERE uid = '".mysqli_real_escape_string($GLOBALS['dbObject']->conn,$xml->uid)."';");
            
            #Audit
            log_wallet_audit('wa0006',$xml->uid,$xml->value,$sourceacc,$destacc,'');

            $responseString = <<<XML
<?xml version='1.0'?>
<serviceResponse>
 <status>Success</status>
 <error></error>
</serviceResponse>
XML;

            $xml = new SimpleXMLElement($responseString);

            echo $xml->asXML();
            exit;
        }
    }
    else if($xml->sourceacc == 'kcmobile')
    {         
        $sourceacc = 'kcm_wallet';
        $resultset = $GLOBALS['dbObject']->runQuery("SELECT * FROM t_wallet WHERE kcm_wallet >= '".mysqli_real_escape_string($GLOBALS['dbObject']->conn,$xml->value)."' AND uid = '".mysqli_real_escape_string($GLOBALS['dbObject']->conn,$xml->uid)."';");
        
        if(mysqli_num_rows($resultset) === 0)
        {
            $responseString = <<<XML
<?xml version='1.0'?>
<serviceResponse>
 <status>Fail</status>
 <error>Your source account does not have enough funds to complete this transaction</error>
</serviceResponse>
XML;

            $xml = new SimpleXMLElement($responseString);

            echo $xml->asXML();
            exit;
            
        }else{            
            #Update balances
            $GLOBALS['dbObject']->runQuery("UPDATE t_wallet SET `$destacc` = (`$destacc`+".$xml->value."),`$sourceacc` = (`$sourceacc`-".$xml->value."),last_transaction_date=now() WHERE uid = '".mysqli_real_escape_string($GLOBALS['dbObject']->conn,$xml->uid)."';");
            
            #Audit
            log_wallet_audit('wa0006',$xml->uid,$xml->value,$sourceacc,$destacc,'');

            $responseString = <<<XML
<?xml version='1.0'?>
<serviceResponse>
 <status>Success</status>
 <error></error>
</serviceResponse>
XML;

            $xml = new SimpleXMLElement($responseString);

            echo $xml->asXML();
            exit;
        }
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
    if (!$GLOBALS['dbObject']->runQuery("INSERT INTO t_walletaudit (uid,action,value,source_pool,destination_pool,voucher_serial_number) VALUES ('".$uid."','".$action."','".$value."','".$sourcepool."','".$destinationpool."','".$voucherserial."');")) 
    {                   
        writelog("[AUDIT LOG] Failed to write to audit log");
        exit;
    }else{
        $resultset = $GLOBALS['dbObject']->runQuery("SELECT LAST_INSERT_ID() as id FROM t_walletaudit;");            
        $row = $resultset->fetch_array(MYSQLI_ASSOC);

        $w_reference=str_pad($row['id'], 6, '0', STR_PAD_LEFT);
        
        $GLOBALS['dbObject']->runQuery("UPDATE t_walletaudit SET w_reference='$w_reference' WHERE Id='".$row['id']."';");

        return $w_reference;
    }
}