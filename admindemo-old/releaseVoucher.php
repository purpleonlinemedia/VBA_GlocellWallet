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
    $expirydate = date('d-m-Y',strtotime(date("d-m-Y", time())." + 365 day"));
    
    $stmnt4 = "SELECT * FROM t_voucher WHERE order_num = '".mysqli_real_escape_string($link,$ordernum)."';"
            or die ("Error fetching..." . mysqli_error($link));
    $rslt4 = mysqli_query($link, $stmnt4);
        
    $stmnt5 = "SELECT *,SUM(PGC010+PGC020+PGC050+PGC100) AS totalval FROM t_voucherorders WHERE order_num = '".mysqli_real_escape_string($link,$ordernum)."' GROUP BY PGC010;"
            or die ("Error fetching..." . mysqli_error($link));
    $rslt5 = mysqli_query($link, $stmnt5);
    $rsltarray5 = $rslt5->fetch_array(MYSQLI_ASSOC);
    
////Iterate files for PGC codes
    $stmnt100 = "SELECT code FROM t_codes WHERE code LIKE 'PGC%' ORDER BY code ASC;"
            or die ("Error fetching..." . mysqli_error($link));
    $rslt100 = mysqli_query($link, $stmnt100);
    
    while($rsltarray100 = $rslt100->fetch_array(MYSQLI_ASSOC)){
        
        $stmnt50 = "SELECT * FROM t_voucher WHERE order_num = '".mysqli_real_escape_string($link,$ordernum)."' AND product ='".$rsltarray100['code']."';"
                or die ("Error fetching..." . mysqli_error($link));
        $rslt50 = mysqli_query($link, $stmnt50);

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

        echo "Batch File Generated: <a href='".$dllink."' target='_new'>Click To Download The ".$rsltarray100['code']." Voucher Batch</a><br/>";
    }
}
?>