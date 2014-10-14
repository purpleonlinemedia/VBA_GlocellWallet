<?php
include("includes/db_conn.php");
$link = mysqli_connect($db_host,$db_user,$db_pass,$db_name);
if(mysqli_connect_errno($link)){
    echo "Failed to connect to database: ".mysqli_connect_error();
}

if(isset($_POST["customerid"])){
    $customerid = $_POST["customerid"];
}else{
    echo "Error: Customer ID Null";
    exit;
}

//if(isset($_POST["sessionid"])){
//    $sessionid = $_POST["sessionid"];
//}else{
//    echo "Error: Session Error";
//    exit;
//}

if(isset($_POST["pgc010"])){
    $pgc010 = $_POST["pgc010"];
}else{
    echo "Error: No PGC010";
    exit;
}
if(isset($_POST["pgc020"])){
    $pgc020 = $_POST["pgc020"];
}else{
    echo "Error: No PGC020";
    exit;
}
if(isset($_POST["pgc050"])){
    $pgc050 = $_POST["pgc050"];
}else{
    echo "Error: No PGC050";
    exit;
}
if(isset($_POST["pgc100"])){
    $pgc100 = $_POST["pgc100"];
}else{
    echo "Error: No PGC100";
    exit;
}
if(isset($_POST["orderval"])){
    $ordervalue = $_POST["orderval"];
}else{
    echo "Error: No Order Value";
    exit;
}
if(isset($_POST["totalprice"])){
    $orderprice = $_POST["totalprice"];
}else{
    echo "Error: No Total Price";
    exit;
}

orderVoucher($customerid,$pgc010,$pgc020,$pgc050,$pgc100);

function orderVoucher($uid,$gc10,$gc20,$gc50,$gc100){
    
    global $link;
    
    $stmnt = "SELECT * FROM t_voucherorders ORDER BY order_num DESC LIMIT 1;"
                        or die ("Error fetching..." . mysqli_error($link));
    $rslt = mysqli_query($link, $stmnt);
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
    
    echo "Processing...<br/>";
    
    $releasedate = "0000-00-00 00:00:00";
    
    $stmnt2 = $link->prepare("INSERT INTO t_voucherorders(`customer_id`,`order_num`,`value`,`request_date`,`PGC010`,`PGC020`,`PGC050`,`PGC100`,`release_date`)
                            VALUES(?,?,?,now(),?,?,?,?,?);");
    $stmnt2->bind_param('sissssss',$uid,$ordernum2,$gcval,$gc10,$gc20,$gc50,$gc100,$releasedate);
    $stmnt2->execute();
    $stmnt2->close();
    
    if(mysqli_affected_rows($link) !== 0){
        
        $stmnt3 = "SELECT * FROM t_voucherorders WHERE customer_id = '".$uid."' ORDER BY order_num DESC LIMIT 1;"
                        or die ("Error fetching..." . mysqli_error($link));
        $rslt3 = mysqli_query($link, $stmnt3);
        $rsltarray3 = $rslt3->fetch_array(MYSQLI_ASSOC);
        
        ////Generate Each PGC010 Voucher Here
        for($i = 0; $i < $rsltarray3['PGC010']; $i++ ){
            $pgval = $rsltarray3['PGC010'];
                    
            $ordernum = $rsltarray3['order_num'];
            $newnumber = genVoucher($uid,$ordernum,$pgval);
            
            $stmnt5 = "SELECT * FROM t_voucher WHERE voucher_number = '".mysqli_real_escape_string($link,$newnumber)."';"
                                or die ("Error fetching..." . mysqli_error($link));
            $rslt5 = mysqli_query($link, $stmnt5);
            $rsltarray5 = $rslt5->fetch_array(MYSQLI_ASSOC);

            if($rsltarray5['voucher_number'] != "" || $rsltarray5['voucher_number'] != ""){
                echo "Duplicate Found";
                $newnumber = genVoucher($uid,$ordernum,$pgval);
            }
            
            $stmnt4 = "SELECT * FROM t_voucherorders WHERE order_num = '".mysqli_real_escape_string($link,$ordernum)."';"
                                or die ("Error fetching..." . mysqli_error($link));
            $rslt4 = mysqli_query($link, $stmnt3);
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
                
                $stmnt10 = $link->prepare("INSERT INTO t_voucher VALUES (?,?,?,?,?,now(),?,?,?,?,?,?);");
                $stmnt10->bind_param('isissssssss',$newnumber,$serialnum,$ordernum,$PGCvalue,$PGCprod,$custid,$nodate,$blank,$nodate,$blank,$blank);
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
            
            $stmnt5 = "SELECT * FROM t_voucher WHERE voucher_number = '".mysqli_real_escape_string($link,$newnumber)."';"
                                or die ("Error fetching..." . mysqli_error($link));
            $rslt5 = mysqli_query($link, $stmnt5);
            $rsltarray5 = $rslt5->fetch_array(MYSQLI_ASSOC);

            if($rsltarray5['voucher_number'] != "" || $rsltarray5['voucher_number'] != ""){
                echo "Duplicate Found";
                $newnumber = genVoucher($uid,$ordernum,$pgval);
            }
            
            $stmnt4 = "SELECT * FROM t_voucherorders WHERE order_num = '".mysqli_real_escape_string($link,$ordernum)."';"
                                or die ("Error fetching..." . mysqli_error($link));
            $rslt4 = mysqli_query($link, $stmnt3);
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
                
                $stmnt10 = $link->prepare("INSERT INTO t_voucher VALUES (?,?,?,?,?,now(),?,?,?,?,?,?);");
                $stmnt10->bind_param('isissssssss',$newnumber,$serialnum,$ordernum,$PGCvalue,$PGCprod,$custid,$nodate,$blank,$nodate,$blank,$blank);
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
            
            $stmnt5 = "SELECT * FROM t_voucher WHERE voucher_number = '".mysqli_real_escape_string($link,$newnumber)."';"
                                or die ("Error fetching..." . mysqli_error($link));
            $rslt5 = mysqli_query($link, $stmnt5);
            $rsltarray5 = $rslt5->fetch_array(MYSQLI_ASSOC);

            if($rsltarray5['voucher_number'] != "" || $rsltarray5['voucher_number'] != ""){
                echo "Duplicate Found";
                $newnumber = genVoucher($uid,$ordernum,$pgval);
            }
            
            $stmnt4 = "SELECT * FROM t_voucherorders WHERE order_num = '".mysqli_real_escape_string($link,$ordernum)."';"
                                or die ("Error fetching..." . mysqli_error($link));
            $rslt4 = mysqli_query($link, $stmnt3);
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
                
                $stmnt10 = $link->prepare("INSERT INTO t_voucher VALUES (?,?,?,?,?,now(),?,?,?,?,?,?);");
                $stmnt10->bind_param('isissssssss',$newnumber,$serialnum,$ordernum,$PGCvalue,$PGCprod,$custid,$nodate,$blank,$nodate,$blank,$blank);
                
                //$stmnt10 = $link->prepare("INSERT INTO t_voucher VALUES (?,?,?,?,?,now(),?,now(),?,now(),?,?);");
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
            
            $stmnt5 = "SELECT * FROM t_voucher WHERE voucher_number = '".mysqli_real_escape_string($link,$newnumber)."';"
                                or die ("Error fetching..." . mysqli_error($link));
            $rslt5 = mysqli_query($link, $stmnt5);
            $rsltarray5 = $rslt5->fetch_array(MYSQLI_ASSOC);

            if($rsltarray5['voucher_number'] != "" || $rsltarray5['voucher_number'] != ""){
                echo "Duplicate Found";
                $newnumber = genVoucher($uid,$ordernum,$pgval);
            }
            
            $stmnt4 = "SELECT * FROM t_voucherorders WHERE order_num = '".mysqli_real_escape_string($link,$ordernum)."';"
                                or die ("Error fetching..." . mysqli_error($link));
            $rslt4 = mysqli_query($link, $stmnt3);
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
                
                $stmnt10 = $link->prepare("INSERT INTO t_voucher VALUES (?,?,?,?,?,now(),?,?,?,?,?,?);");
                $stmnt10->bind_param('isissssssss',$newnumber,$serialnum,$ordernum,$PGCvalue,$PGCprod,$custid,$nodate,$blank,$nodate,$blank,$blank);
                
                //$stmnt10 = $link->prepare("INSERT INTO t_voucher VALUES (?,?,?,?,?,now(),?,now(),?,now(),?,?);");
                //$stmnt10->bind_param('isissssss',$newnumber,$blank,$ordernum,$PGCvalue,$PGCprod,$custid,$blank,$blank,$blank);
                $stmnt10->execute();
                $stmnt10->close();

                //echo "Generated Voucher: ".$newnumber."<br/><br/>";
            }
        }
        
        echo "Vouchers Successfully Generated<br/><br/>";
    }else{
        
        $stmnt2->rollback();
        echo "Error: No rows affected";
        exit();
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
    global $link;
    
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
    $stmnt = "SELECT * FROM t_voucher WHERE issue_cust_id = '".$uid."' ORDER BY voucher_number DESC LIMIT 1;"
                        or die ("Error fetching..." . mysqli_error($link));
    $rslt = mysqli_query($link, $stmnt);
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
}

///Luhn Function To Check Number
function Luhn($number,$ordernum,$sequencenum,$pgval){
    global $link;
    
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

?>