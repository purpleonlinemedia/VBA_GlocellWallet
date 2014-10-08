<?php
require_once './include/functions.php';
require_once './include/db_access.php';

$dbObject = new Databases();

$action = $_POST['action'];

switch($action) { //Switch case for value of action
  case "login": 
    login();       
  break;
  case "getBalance": 
    getBalance();       
  break;
}
 
function login()
{                  
    $resultset = $GLOBALS['dbObject']->runQuery("SELECT * FROM t_customers WHERE login_str = '".mysqli_real_escape_string($GLOBALS['dbObject']->conn,$_POST['login_str'])."' AND password = '".mysqli_real_escape_string($GLOBALS['dbObject']->conn,$_POST['password'])."' LIMIT 1;");
    $row = $resultset->fetch_array(MYSQLI_ASSOC);
    
    if(mysqli_num_rows($resultset) === 0)
    {
        //Failed login
        $jsonResponse = array('status' => 'Fail', 'error' => 'Invalid request');
        $jsonResponse = json_encode($jsonResponse);
        
        echo $jsonResponse;
        exit;
    }else{
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

function getBalance()
{
    if(!checksessionid($_POST['sessionid']))
    {
        $jsonResponse = array('status' => 'Fail', 'error' => 'Invalid request');
        $jsonResponse = json_encode($jsonResponse);
        
        echo $jsonResponse;
        exit;
    }
}