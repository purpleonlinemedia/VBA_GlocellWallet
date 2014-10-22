<?php
ini_set('max_execution_time', 600);

require_once 'includes/functions.php';
require_once 'includes/db_access.php';

//Composer auto loader
require_once 'includes/vendor/autoload.php';
//Composer package Respect/Validator
use Respect\Validation\Validator as v;

$dbObject = new Databases();

$dataPOST = trim(file_get_contents('php://input'));
$xml = simplexml_load_string($dataPOST);
$action = $xml->action;

switch($action) { //Switch case for value of action
  case "login": 
    login($xml);       
  break;
  case "generateGcashVoucher": 
    generate_voucher($xml);       
  break;
  case "releaseGcashVoucher": 
    release_voucher($xml);       
  break;
  case "createCustomer": 
    create_customer($xml);       
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
}

function generate_voucher($xml)
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
    
    $uid = $xml->uid;
    $pgc010 = $xml->pgc010;
    $pgc020 = $xml->pgc020;
    $pgc050 = $xml->pgc050;
    $pgc100 = $xml->pgc100;
    
    ///Generate the ordernumber
    $response = orderVoucher($uid,$pgc010,$pgc020,$pgc050,$pgc100);
    
    $responseString = <<<XML
<?xml version='1.0'?>
<serviceResponse>
 <status>Success</status>
 <message>$response</message>
 <error></error>
</serviceResponse>
XML;

    $xml = new SimpleXMLElement($responseString);

    echo $xml->asXML();
    exit;
}

function release_voucher($xml)
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
    
    $ordernum = $xml->ordernum;
    
    $response = updateVoucher($ordernum);
    
    $responseString = <<<XML
<?xml version='1.0'?>
<serviceResponse>
 <status>Success</status>
 <message>$response</message>
 <error></error>
</serviceResponse>
XML;

    $xml = new SimpleXMLElement($responseString);

    echo $xml->asXML();
    exit;
}

function create_customer($xml)
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
    
    if((!v::phone()->validate($xml->contactnum)) && (!v::numeric()->validate($xml->rebate)))////Validate contact number
    {
        $responseString = <<<XML
<?xml version='1.0'?>
<serviceResponse>
 <status>Fail</status>
 <error>Invalid number format</error>
</serviceResponse>
XML;

        $xml = new SimpleXMLElement($responseString);

        echo $xml->asXML();
        exit;
    }
    
    $sanitized = filter_var($xml->contactemail, FILTER_SANITIZE_EMAIL);
    if(!v::email()->validate($sanitized))////Validate email
    {
        $responseString = <<<XML
<?xml version='1.0'?>
<serviceResponse>
 <status>Fail</status>
 <error>Invalid email format</error>
</serviceResponse>
XML;

        $xml = new SimpleXMLElement($responseString);

        echo $xml->asXML();
        exit;
    }
    
    $acctype = $xml->acctype;
    $company = $xml->company;
    $branch = $xml->branch;
    $contact = $xml->contact;
    $contactemail = $xml->contactemail;
    $contactnum = $xml->contactnum;
    $rebate = $xml->rebate;
    $loginname = $xml->loginname;
    $loginpass = $xml->loginpass;
    
    $response = createCustomer($acctype,$company,$branch,$contact,$contactemail,$contactnum,$rebate,$loginname,$loginpass);
    
    $responseString = <<<XML
<?xml version='1.0'?>
<serviceResponse>
 <status>Success</status>
 <message>$response</message>
 <error></error>
</serviceResponse>
XML;

    $xml = new SimpleXMLElement($responseString);

    echo $xml->asXML();
    exit;
    
}

//Check sessionid validity
function checksessionid($sessionid)
{
    $resultset = $GLOBALS['dbObject']->runQuery("SELECT count(*) FROM t_customerslogin WHERE session_id = '".mysqli_real_escape_string($GLOBALS['dbObject']->conn,$sessionid)."' AND (DATEDIFF(`expire_date`,now())) > 0 LIMIT 1;");
    
    if(mysqli_num_rows($resultset) === 0)
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
    }else{
        return true;
    }    
}

//if(isset($_POST["customerid"])){
//    $customerid = $_POST["customerid"];
//}else{
//    echo "Error: Customer ID Null";
//    exit;
//}
//
////if(isset($_POST["sessionid"])){
////    $sessionid = $_POST["sessionid"];
////}else{
////    echo "Error: Session Error";
////    exit;
////}
//
//if(isset($_POST["pgc010"])){
//    $pgc010 = $_POST["pgc010"];
//}else{
//    echo "Error: No PGC010";
//    exit;
//}
//if(isset($_POST["pgc020"])){
//    $pgc020 = $_POST["pgc020"];
//}else{
//    echo "Error: No PGC020";
//    exit;
//}
//if(isset($_POST["pgc050"])){
//    $pgc050 = $_POST["pgc050"];
//}else{
//    echo "Error: No PGC050";
//    exit;
//}
//if(isset($_POST["pgc100"])){
//    $pgc100 = $_POST["pgc100"];
//}else{
//    echo "Error: No PGC100";
//    exit;
//}
//if(isset($_POST["orderval"])){
//    $ordervalue = $_POST["orderval"];
//}else{
//    echo "Error: No Order Value";
//    exit;
//}
//if(isset($_POST["totalprice"])){
//    $orderprice = $_POST["totalprice"];
//}else{
//    echo "Error: No Total Price";
//    exit;
//}
//
//orderVoucher($customerid,$pgc010,$pgc020,$pgc050,$pgc100);

function orderVoucher($uid,$gc10,$gc20,$gc50,$gc100){
    
    //global $GLOBALS['dbObject']->conn;
    
    $stmnt = "SELECT * FROM t_voucherorders ORDER BY order_num DESC LIMIT 1;"
                        or die ("Error fetching..." . mysqli_error($GLOBALS['dbObject']->conn));
    $rslt = mysqli_query($GLOBALS['dbObject']->conn, $stmnt);
    $rsltarray = $rslt->fetch_array(MYSQLI_ASSOC);
    $ordernum1 = $rsltarray['order_num'];
    $ordernum2 = $ordernum1 + 1;
    
    $gcval = 0;
    if($gc10 != "0"){
        for($g = 0; $g < $gc10; $g++){
            $gcval = $gcval + 10;
        }
    }
    
    if($gc20 != "0"){
        for($g = 0; $g < $gc20; $g++){
            $gcval = $gcval + 20;
        }
    }
    
    if($gc50 != "0"){
        for($g = 0; $g < $gc50; $g++){
            $gcval = $gcval + 50;
        }
    }
    
    if($gc100 != "0"){
        for($g = 0; $g < $gc100; $g++){
            $gcval = $gcval + 100;
        }
    }
    
    $releasedate = "0000-00-00 00:00:00";
    
    $stmnt2 = $GLOBALS['dbObject']->conn->prepare("INSERT INTO t_voucherorders(`customer_id`,`order_num`,`value`,`request_date`,`PGC010`,`PGC020`,`PGC050`,`PGC100`,`release_date`)
                            VALUES(?,?,?,now(),?,?,?,?,?);");
    $stmnt2->bind_param('sissssss',$uid,$ordernum2,$gcval,$gc10,$gc20,$gc50,$gc100,$releasedate);
    $stmnt2->execute();
    $stmnt2->close();
    
    if(mysqli_affected_rows($GLOBALS['dbObject']->conn) !== 0){
        
        $stmnt3 = "SELECT * FROM t_voucherorders WHERE customer_id = '".$uid."' ORDER BY order_num DESC LIMIT 1;"
                        or die ("Error fetching..." . mysqli_error($GLOBALS['dbObject']->conn));
        $rslt3 = mysqli_query($GLOBALS['dbObject']->conn, $stmnt3);
        $rsltarray3 = $rslt3->fetch_array(MYSQLI_ASSOC);
        
        ////Generate Each PGC010 Voucher Here
        for($i = 0; $i < $rsltarray3['PGC010']; $i++ ){
            $pgval = $rsltarray3['PGC010'];
                    
            $ordernum = $rsltarray3['order_num'];
            $newnumber = genVoucher($uid,$ordernum,$pgval);
            
            $stmnt5 = "SELECT * FROM t_voucher WHERE voucher_number = '".mysqli_real_escape_string($GLOBALS['dbObject']->conn,$newnumber)."';"
                                or die ("Error fetching..." . mysqli_error($GLOBALS['dbObject']->conn));
            $rslt5 = mysqli_query($GLOBALS['dbObject']->conn, $stmnt5);
            $rsltarray5 = $rslt5->fetch_array(MYSQLI_ASSOC);

            if($rsltarray5['voucher_number'] != "" || $rsltarray5['voucher_number'] != ""){
                //echo "Duplicate Found";
                $newnumber = genVoucher($uid,$ordernum,$pgval);
            }
            
            $stmnt4 = "SELECT * FROM t_voucherorders WHERE order_num = '".mysqli_real_escape_string($GLOBALS['dbObject']->conn,$ordernum)."';"
                                or die ("Error fetching..." . mysqli_error($GLOBALS['dbObject']->conn));
            $rslt4 = mysqli_query($GLOBALS['dbObject']->conn, $stmnt3);
            $rsltarray4 = $rslt3->fetch_array(MYSQLI_ASSOC);

            ////Insert Into PGC010 Vouchers
            if($rsltarray4['PGC010'] != "0"){

                //echo "<font color='red'><b>PGC010:".$rsltarray4['PGC010']."</b></font><br/>";

                $blank = "";
                $serialnum = generateRandomString();
                $nodate = "0000-00-00 00:00:00";
                $PGCvalue = "10";
                $PGCprod = "PGC010";
                $custid = $rsltarray3['customer_id'];
                
                $stmnt10 = $GLOBALS['dbObject']->conn->prepare("INSERT INTO t_voucher VALUES (?,?,?,?,?,now(),?,?,?,?,?,?,?);");
                $stmnt10->bind_param('isissssssssi',$newnumber,$serialnum,$ordernum,$PGCvalue,$PGCprod,$custid,$nodate,$blank,$nodate,$blank,$blank,$blank);
                $stmnt10->execute();
                $stmnt10->close();

                //echo "Generated Voucher: ".$newnumber."<br/><br/>";
            }
        }
        
        ////Generate Each PGC020 Voucher Here
        for($i = 0; $i < $rsltarray3['PGC020']; $i++ ){
            $pgval = $rsltarray3['PGC020'];
                    
            $ordernum = $rsltarray3['order_num'];
            $newnumber = genVoucher($uid,$ordernum,$pgval);
            
            $stmnt5 = "SELECT * FROM t_voucher WHERE voucher_number = '".mysqli_real_escape_string($GLOBALS['dbObject']->conn,$newnumber)."';"
                                or die ("Error fetching..." . mysqli_error($GLOBALS['dbObject']->conn));
            $rslt5 = mysqli_query($GLOBALS['dbObject']->conn, $stmnt5);
            $rsltarray5 = $rslt5->fetch_array(MYSQLI_ASSOC);

            if($rsltarray5['voucher_number'] != "" || $rsltarray5['voucher_number'] != ""){
                //echo "Duplicate Found";
                $newnumber = genVoucher($uid,$ordernum,$pgval);
            }
            
            $stmnt4 = "SELECT * FROM t_voucherorders WHERE order_num = '".mysqli_real_escape_string($GLOBALS['dbObject']->conn,$ordernum)."';"
                                or die ("Error fetching..." . mysqli_error($GLOBALS['dbObject']->conn));
            $rslt4 = mysqli_query($GLOBALS['dbObject']->conn, $stmnt3);
            $rsltarray4 = $rslt3->fetch_array(MYSQLI_ASSOC);

            ////Insert Into PGC020 Vouchers
            if($rsltarray4['PGC020'] != "0"){

                //echo "<font color='red'><b>PGC020:".$rsltarray4['PGC020']."</b></font><br/>";

                $blank = "";
                $serialnum = generateRandomString();
                $nodate = "0000-00-00 00:00:00";
                $PGCvalue = "20";
                $PGCprod = "PGC020";
                $custid = $rsltarray3['customer_id'];
                
                $stmnt10 = $GLOBALS['dbObject']->conn->prepare("INSERT INTO t_voucher VALUES (?,?,?,?,?,now(),?,?,?,?,?,?,?);");
                $stmnt10->bind_param('isissssssssi',$newnumber,$serialnum,$ordernum,$PGCvalue,$PGCprod,$custid,$nodate,$blank,$nodate,$blank,$blank,$blank);
                $stmnt10->execute();
                $stmnt10->close();

                //echo "Generated Voucher: ".$newnumber."<br/><br/>";
            }
        }
        
        ////Generate Each PGC050 Voucher Here
        for($i = 0; $i < $rsltarray3['PGC050']; $i++ ){
            $pgval = $rsltarray3['PGC050'];
            
            $ordernum = $rsltarray3['order_num'];
            $newnumber = genVoucher($uid,$ordernum,$pgval);
            
            $stmnt5 = "SELECT * FROM t_voucher WHERE voucher_number = '".mysqli_real_escape_string($GLOBALS['dbObject']->conn,$newnumber)."';"
                                or die ("Error fetching..." . mysqli_error($GLOBALS['dbObject']->conn));
            $rslt5 = mysqli_query($GLOBALS['dbObject']->conn, $stmnt5);
            $rsltarray5 = $rslt5->fetch_array(MYSQLI_ASSOC);

            if($rsltarray5['voucher_number'] != "" || $rsltarray5['voucher_number'] != ""){
                //echo "Duplicate Found";
                $newnumber = genVoucher($uid,$ordernum,$pgval);
            }
            
            $stmnt4 = "SELECT * FROM t_voucherorders WHERE order_num = '".mysqli_real_escape_string($GLOBALS['dbObject']->conn,$ordernum)."';"
                                or die ("Error fetching..." . mysqli_error($GLOBALS['dbObject']->conn));
            $rslt4 = mysqli_query($GLOBALS['dbObject']->conn, $stmnt3);
            $rsltarray4 = $rslt3->fetch_array(MYSQLI_ASSOC);
            
            ////Insert Into PGC050 Vouchers
            if($rsltarray4['PGC050'] != "0"){
            //if($rsltarray3['PGC050'] != "0"){

                //echo "<font color='red'><b>PGC050:".$rsltarray3['PGC050']."</b></font><br/>";

                $blank = "";
                $serialnum = generateRandomString();
                $nodate = "0000-00-00 00:00:00";
                $PGCvalue = "50";
                $PGCprod = "PGC050";
                $custid = $rsltarray3['customer_id'];
                
                $stmnt10 = $GLOBALS['dbObject']->conn->prepare("INSERT INTO t_voucher VALUES (?,?,?,?,?,now(),?,?,?,?,?,?,?);");
                $stmnt10->bind_param('isissssssssi',$newnumber,$serialnum,$ordernum,$PGCvalue,$PGCprod,$custid,$nodate,$blank,$nodate,$blank,$blank,$blank);
                
                //$stmnt10 = $GLOBALS['dbObject']->conn->prepare("INSERT INTO t_voucher VALUES (?,?,?,?,?,now(),?,now(),?,now(),?,?);");
                //$stmnt10->bind_param('isissssss',$newnumber,$blank,$ordernum,$PGCvalue,$PGCprod,$custid,$blank,$blank,$blank);
                $stmnt10->execute();
                $stmnt10->close();

                //echo "Generated Voucher: ".$newnumber."<br/><br/>";
            }
        }
        
        ////Generate Each PGC100 Voucher Here
        for($i = 0; $i < $rsltarray3['PGC100']; $i++ ){
            $pgval = $rsltarray3['PGC100'];
            
            $ordernum = $rsltarray3['order_num'];
            $newnumber = genVoucher($uid,$ordernum,$pgval);
            
            $stmnt5 = "SELECT * FROM t_voucher WHERE voucher_number = '".mysqli_real_escape_string($GLOBALS['dbObject']->conn,$newnumber)."';"
                                or die ("Error fetching..." . mysqli_error($GLOBALS['dbObject']->conn));
            $rslt5 = mysqli_query($GLOBALS['dbObject']->conn, $stmnt5);
            $rsltarray5 = $rslt5->fetch_array(MYSQLI_ASSOC);

            if($rsltarray5['voucher_number'] != "" || $rsltarray5['voucher_number'] != ""){
                //echo "Duplicate Found";
                $newnumber = genVoucher($uid,$ordernum,$pgval);
            }
            
            $stmnt4 = "SELECT * FROM t_voucherorders WHERE order_num = '".mysqli_real_escape_string($GLOBALS['dbObject']->conn,$ordernum)."';"
                                or die ("Error fetching..." . mysqli_error($GLOBALS['dbObject']->conn));
            $rslt4 = mysqli_query($GLOBALS['dbObject']->conn, $stmnt3);
            $rsltarray4 = $rslt3->fetch_array(MYSQLI_ASSOC);
            
            ////Insert Into PGC100 Vouchers
            if($rsltarray4['PGC100'] != "0"){
            //if($rsltarray3['PGC050'] != "0"){

                //echo "<font color='red'><b>PGC100:".$rsltarray3['PGC100']."</b></font><br/>";

                $blank = "";
                $serialnum = generateRandomString();
                $nodate = "0000-00-00 00:00:00";
                $PGCvalue = "100";
                $PGCprod = "PGC100";
                $custid = $rsltarray3['customer_id'];
                
                $stmnt10 = $GLOBALS['dbObject']->conn->prepare("INSERT INTO t_voucher VALUES (?,?,?,?,?,now(),?,?,?,?,?,?,?);");
                $stmnt10->bind_param('isissssssssi',$newnumber,$serialnum,$ordernum,$PGCvalue,$PGCprod,$custid,$nodate,$blank,$nodate,$blank,$blank,$blank);
                
                //$stmnt10 = $GLOBALS['dbObject']->conn->prepare("INSERT INTO t_voucher VALUES (?,?,?,?,?,now(),?,now(),?,now(),?,?);");
                //$stmnt10->bind_param('isissssss',$newnumber,$blank,$ordernum,$PGCvalue,$PGCprod,$custid,$blank,$blank,$blank);
                $stmnt10->execute();
                $stmnt10->close();

                //echo "Generated Voucher: ".$newnumber."<br/><br/>";
            }
        }
        
        return "Vouchers Successfully Generated<br/>";
    }else{
        
        $stmnt2->rollback();
        return "Error: No rows affected";
        //exit();
    }
}

function generateRandomString($length = 9) {
    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}

///Generate The Voucher Numbers Function
function genVoucher($uid,$ordernum,$pgval){
    //global $GLOBALS['dbObject']->conn;
    
    ////Generate Random Number
    $char1 = array("0","1","2","3","4","5","6","7","8","9");
    $keys1 = array();

    while(count($keys1) < 4){
        $x = mt_rand(0, count($char1)-1);

        if(!in_array($x, $keys1)) {
           $keys1[] = $x;
        }
    }

    $random_chars = "";

    foreach($keys1 as $key){    
       $random_chars .= $char1[$key];
    }

    ////Generate Sequential Number
    //$stmnt = "SELECT * FROM t_voucher WHERE issue_cust_id = '".$uid."' ORDER BY voucher_number DESC LIMIT 1;"
    $stmnt = "SELECT * FROM t_voucher ORDER BY voucher_number DESC LIMIT 1;"
                        or die ("Error fetching..." . mysqli_error($GLOBALS['dbObject']->conn));
    $rslt = mysqli_query($GLOBALS['dbObject']->conn, $stmnt);
    $rsltarray = $rslt->fetch_array(MYSQLI_ASSOC);
    
    if($rsltarray['voucher_number'] != ""){

        $sequence = $rsltarray['voucher_number'];
        $numarr = substr($sequence,-5,-1);

        ///Increment Sequential Number
        $num = $numarr + 1;
        $padnum = sprintf('%1$04d',$num, $random_chars);

        $newnumber = Luhn($padnum,$ordernum,$random_chars,$pgval);
        
        if($newnumber != ""){
            return $newnumber;
        }else{
            return "0";
        }
    }
//    }else{
//        ///Increment Sequential Number
//        $padnum = '0001';
//        //$padnum = sprintf('%1$04d',$num, $random_chars);
//        
//        $newnumber = Luhn($padnum,$ordernum,$random_chars,$pgval);
//        
//        echo($newnumber);
//        exit;
//        
//        if($newnumber != ""){
//            return $newnumber;
//        }else{
//            return "0";
//        }
//    }
}

///Luhn Function To Check Number
function Luhn($number,$ordernum,$sequencenum,$pgval){
    //global $GLOBALS['dbObject']->conn;
    
    $stack = 0;
    $number = str_split(strrev($number));

    foreach ($number as $key => $value){

        if ($key % 2 == 0){
            $value = array_sum(str_split($value * 2));
        }

        $stack += $value;
    }
    $stack %= 10;

    if ($stack != 0){
        $stack -= 10;     $stack = abs($stack);
    }

    $number = implode('', array_reverse($number));
    $number = $number . strval($stack);
    
    $newnumber = $sequencenum."".$number;
    
    return $newnumber;
}

function createCustomer($acctype,$company,$branch,$contact,$contactemail,$contactnum,$rebate,$lname,$lpass){
    
    //global $GLOBALS['dbObject']->conn;
    
//    if($company === "" || $branch === "" || $contact === "" || $contactemail === "" || $contactnum === "" || $rebate === "" || $lname === "" || $lpass === ""){
//        return "ERROR: Please enter values in all required fields";
//        //exit;
//    }else{
        $stmnt = "SELECT * FROM t_customers WHERE company_name = '".$company."' ORDER BY cust_id ASC;"
                        or die ("Error fetching..." . mysqli_error($GLOBALS['dbObject']->conn));
        $rslt = mysqli_query($GLOBALS['dbObject']->conn, $stmnt);
        $rsltarray = $rslt->fetch_array(MYSQLI_ASSOC);
        $custid = $rsltarray['cust_id'];
        
        if($custid != "" || $custid != null){
            $stmnt100 = "SELECT * FROM t_customers WHERE company_name = '".$company."' ORDER BY cust_id DESC LIMIT 1;"
                        or die ("Error fetching..." . mysqli_error($GLOBALS['dbObject']->conn));
            $rslt100 = mysqli_query($GLOBALS['dbObject']->conn, $stmnt100);
            $rsltarray100 = $rslt100->fetch_array(MYSQLI_ASSOC);
            
            $coname = $rsltarray100['cust_id'];
            $abbrev = substr($coname,0,3);
            $abbrevnum = substr($coname,-3,3);
            $abbrevnum = $abbrevnum + 1;
            $abbrevstr = strlen($abbrevnum);

            if($abbrevstr == '1'){
                $abbrevnum = "00".$abbrevnum;
            }else if($abbrevstr == '2'){
                $abbrevnum = "0".$abbrevnum;
            }else{
                $abbrevnum = $abbrevnum;
            }
            
            $newcustid = $abbrev."".$abbrevnum;
        }else{
            $coname = $company;
            $abbrev = substr($coname,0,3);
            $newabbrev = strtoupper($abbrev);
            $newcustid = $newabbrev."001";
        }
        
        $placeholder = "+";
        $stmnt2 = $GLOBALS['dbObject']->conn->prepare("INSERT INTO`t_customers`(`cust_id`,`acc_type`,`company_name`,`branch_name`,`contact_person`,`contact_email`,`contact_num`,`ip_add`,`sales_rebate`,`login_str`,`password`)
                                VALUES (?,?,?,?,?,?,?,?,?,?,?);");
        $stmnt2->bind_param('ssssssssiss',$newcustid,$acctype,$company,$branch,$contact,$contactemail,$contactnum,$placeholder,$rebate,$lname,$lpass);
        $stmnt2->execute();
        $stmnt2->close();
        
        if(mysqli_affected_rows($GLOBALS['dbObject']->conn) == 0){
            return "Failed to execute query.";
            //exit;
        }else{
            return "Customer Successfully Created.";
            //exit;
        }
    //}
}

function updateVoucher($ordernum){
    //global $GLOBALS['dbObject']->conn;
    
    $stmnt = "SELECT COUNT(*) FROM t_voucherorders WHERE order_num = '".mysqli_real_escape_string($GLOBALS['dbObject']->conn,$ordernum)."';"
                        or die ("Error fetching..." . mysqli_error($GLOBALS['dbObject']->conn));
    $rslt = mysqli_query($GLOBALS['dbObject']->conn, $stmnt);
    $rsltarray = $rslt->fetch_row();
    
    if($rsltarray[0] > '0'){
    
        $stmnt2 = $GLOBALS['dbObject']->conn->prepare("UPDATE t_voucherorders SET release_date = now() WHERE order_num = ?;");
        $stmnt2->bind_param('i',$ordernum);
        $stmnt2->execute();
        $stmnt2->close();
        
        $stmnt3 = "SELECT COUNT(*) FROM t_voucher WHERE order_num = '".mysqli_real_escape_string($GLOBALS['dbObject']->conn,$ordernum)."';"
                        or die ("Error fetching..." . mysqli_error($GLOBALS['dbObject']->conn));
        $rslt3 = mysqli_query($GLOBALS['dbObject']->conn, $stmnt3);
        $rsltarray3 = $rslt3->fetch_row();
        
        if($rsltarray3[0] > '0'){
            //$generated = generateFile($ordernum);
            generateFile($ordernum);
        }   
    }else{
        return "ERROR";
        //exit;
    }
}

function generateFile($ordernum){
    //global $GLOBALS['dbObject']->conn;
    
    date_default_timezone_set('Africa/Johannesburg');
    $currentday = date('d');
    $currentmonth = date('m');
    $currentyear = date('Y');
    $today = date("Y-m-d H:i:s");
    $expirydate = date('d-m-Y',strtotime(date("d-m-Y", time())." + 365 day"));
    
    $stmnt4 = "SELECT * FROM t_voucher WHERE order_num = '".mysqli_real_escape_string($GLOBALS['dbObject']->conn,$ordernum)."';"
            or die ("Error fetching..." . mysqli_error($GLOBALS['dbObject']->conn));
    $rslt4 = mysqli_query($GLOBALS['dbObject']->conn, $stmnt4);
        
    $stmnt5 = "SELECT *,SUM(PGC010+PGC020+PGC050+PGC100) AS totalval FROM t_voucherorders WHERE order_num = '".mysqli_real_escape_string($GLOBALS['dbObject']->conn,$ordernum)."' GROUP BY PGC010;"
            or die ("Error fetching..." . mysqli_error($GLOBALS['dbObject']->conn));
    $rslt5 = mysqli_query($GLOBALS['dbObject']->conn, $stmnt5);
    $rsltarray5 = $rslt5->fetch_array(MYSQLI_ASSOC);
    
////Iterate files for PGC codes
    $stmnt100 = "SELECT code FROM t_codes WHERE code LIKE 'PGC%' ORDER BY code ASC;"
            or die ("Error fetching..." . mysqli_error($GLOBALS['dbObject']->conn));
    $rslt100 = mysqli_query($GLOBALS['dbObject']->conn, $stmnt100);
    
    while($rsltarray100 = $rslt100->fetch_array(MYSQLI_ASSOC)){
        
        $stmnt50 = "SELECT * FROM t_voucher WHERE order_num = '".mysqli_real_escape_string($GLOBALS['dbObject']->conn,$ordernum)."' AND product ='".$rsltarray100['code']."';"
                or die ("Error fetching..." . mysqli_error($GLOBALS['dbObject']->conn));
        $rslt50 = mysqli_query($GLOBALS['dbObject']->conn, $stmnt50);

        $count = 0;
////////Create the Text File
        $content = "H|".$ordernum."|".$rsltarray5['request_date']."|".$ordernum."|".$ordernum."|".$today." \r\n";
        
        $pgcval = $rsltarray100['code'];
        $newpgc = substr($pgcval,-3,3);
        
        while($rsltarray50 = $rslt50->fetch_array(MYSQLI_ASSOC)){
            $content .= "D|PGC000".$newpgc."|".$rsltarray50['value']."|unlimited|".$expirydate."|".$ordernum."|".$rsltarray50['serial_number']."|".$rsltarray50['voucher_number']." \r\n";
            $count = $count + 1;
        }

        $value = $count * 10;
        $value = number_format($value, 2, '.', '');
        $clientname = $rsltarray5['customer_id'];
        $docclientname = substr($clientname,0,3);
        $docclientid = substr($clientname,-3,3);

        $content .= "F|".$count."|R ".$value."|Hash Total|".$rsltarray100['code'].":".$count;
        $fp = fopen("orders/".$docclientname."000".$docclientid."STD_PO".$ordernum."_".$rsltarray100['code']."_".$count."_".$currentday.".".$currentmonth.".".$currentyear.".txt","wb");
        fwrite($fp,$content);
        fclose($fp);
        $dllink = "orders/".$docclientname."000".$docclientid."STD_PO".$ordernum."_".$rsltarray100['code']."_".$count."_".$currentday.".".$currentmonth.".".$currentyear.".txt";

        return "Batch File Generated: <a href='".$dllink."' target='_new'>Click To Download The ".$rsltarray100['code']." Voucher Batch</a><br/>";
    }
}
?>