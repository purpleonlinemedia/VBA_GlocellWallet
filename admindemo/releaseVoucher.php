<?php
include("includes/db_conn.php");
$link = mysqli_connect($db_host,$db_user,$db_pass,$db_name);
if(mysqli_connect_errno($link)){
    echo "Failed to connect to database: ".mysqli_connect_error();
}

//if(isset($_POST["sessionid"])){
//    $sessionid = $_POST["sessionid"];
//}else{
//    echo "Error: Session Error";
//    exit;
//}

$session_id = "1";

if(isset($_POST["ordernum"])){
    $ordernum = $_POST["ordernum"];
}else{
    echo "Error: Order Number Null";
    exit;
}

//$value;

updateVoucher($session_id,$ordernum);

function updateVoucher($session,$ordernum){
    global $link;
    
    $stmnt = "SELECT COUNT(*) FROM t_voucherorders WHERE order_num = '".mysqli_real_escape_string($link,$ordernum)."';"
                        or die ("Error fetching..." . mysqli_error($link));
    $rslt = mysqli_query($link, $stmnt);
    $rsltarray = $rslt->fetch_row();
    
    if($rsltarray[0] > '0'){
    
        $stmnt2 = $link->prepare("UPDATE t_voucherorders SET release_date = now() WHERE order_num = ?;");
        $stmnt2->bind_param('i',$ordernum);
        $stmnt2->execute();
        $stmnt2->close();
        
        $stmnt3 = "SELECT COUNT(*) FROM t_voucher WHERE order_num = '".mysqli_real_escape_string($link,$ordernum)."';"
                        or die ("Error fetching..." . mysqli_error($link));
        $rslt3 = mysqli_query($link, $stmnt3);
        $rsltarray3 = $rslt3->fetch_row();
        
        if($rsltarray3[0] > '0'){
            //$generated = generateFile($ordernum);
            generateFile($ordernum);
        }   
    }else{
        echo "ERROR";
        exit;
    }
}

function generateFile($ordernum){
    global $link;
    
    date_default_timezone_set('Africa/Johannesburg');
    $currentday = date('d');
    $currentmonth = date('m');
    $currentyear = date('Y');
    $today = date("Y-m-d H:i:s");
    
    $stmnt4 = "SELECT * FROM t_voucher WHERE order_num = '".mysqli_real_escape_string($link,$ordernum)."';"
            or die ("Error fetching..." . mysqli_error($link));
    $rslt4 = mysqli_query($link, $stmnt4);
    
    $stmnt5 = "SELECT *,SUM(PGC010+PGC020+PGC050+PGC100) AS totalval FROM t_voucherorders WHERE order_num = '".mysqli_real_escape_string($link,$ordernum)."' GROUP BY PGC010;"
                        or die ("Error fetching..." . mysqli_error($link));
    $rslt5 = mysqli_query($link, $stmnt5);
    $rsltarray5 = $rslt5->fetch_array(MYSQLI_ASSOC);
    
////Create the Text File
    $content = "H|".$ordernum."|".$rsltarray5['request_date']."|orderid|generationno|".$today." \r\n";
    $count = 0;
    
    while($rsltarray4 = $rslt4->fetch_array(MYSQLI_ASSOC)){
        $content .= "D|UPN000020|".$rsltarray4['value']."|windowperiod|expirydate|supplierbatchnumber|".$rsltarray4['serial_number']."|voucherpinnumber \r\n";
        $count = $count + 1;
    }
    $content .= "F|".$count."|R ".$rsltarray5['value']."|".$rsltarray5['totalval']."|quantity";
    $fp = fopen("orders/TPC000020STD_PO".$ordernum."_tel50_1_".$currentday.".".$currentmonth.".".$currentyear.".txt","wb");
    fwrite($fp,$content);
    fclose($fp);
    
    $dllink = "orders/TPC000020STD_PO".$ordernum."_tel50_1_".$currentday.".".$currentmonth.".".$currentyear.".txt";
    
    echo "Batch File Generated<br/><a href='".$dllink."' target='_new'>Click Here To Download</a> ";
    exit;
}
?>