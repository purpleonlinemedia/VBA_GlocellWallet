<?php
require_once './db_access.php';

$dbObjectFunctions = new Databases();

//Write log function
function writelog($log,$data)
{
    file_put_contents($log,$data."\n", FILE_APPEND|LOCK_EX);
}

//Function to check if the request is an AJAX request
function is_ajax() {
  return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
}

//Check sessionid validity
function checksessionid($sessionid)
{
    $resultset = $GLOBALS['dbObjectFunctions']->runQuery("SELECT count(*) FROM t_customerslogin WHERE session_id = '".mysqli_real_escape_string($GLOBALS['dbObject']->conn,$sessionid)."' AND (DATEDIFF(`expire_date`,now())) > 0 LIMIT 1;");
    
    if(mysqli_num_rows($resultset) === 0)
    {
        return false;
    }else{
        return true;
    }
}