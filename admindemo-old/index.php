<script type="text/javascript" src="includes/jquery-1.8.0.js"></script>
<?php
include("includes/db_conn.php");
$link = mysqli_connect($db_host,$db_user,$db_pass,$db_name);
if(mysqli_connect_errno($link)){
    echo "Failed to connect to database: ".mysqli_connect_error();
}

$adminid = "";
$adminpassword = "";

////Generare session id
//$random = substr(number_format(time() * mt_rand(),0,'',''),0,10);
//$str = $row['cust_id'].$row['company_name'].$row['contact_num'].$random;
//$hashString = md5($str);

///UPDATE t_customerslogin‏

if(isset($_GET['ref'])){
    $action = $_GET['ref'];
}else{
    $action = "";
}

if(isset($_GET['id'])){
    $custid = $_GET['id'];
}else{
    $custid = "GLO001";
}

if(isset($_GET['type'])){
    
    $stmnt110 = "SELECT acc_type FROM t_customers WHERE cust_id = '".$custid."';";
    $rslt110 = mysqli_query($link, $stmnt110);
    $rsltarray110 = $rslt110->fetch_array(MYSQLI_ASSOC);
    $acctype = $rsltarray110['acc_type'];
    //$acctype = $_GET['type'];
    
    
}else{
    $acctype = "Voucher Sales";
}

if(isset($_GET['report'])){
    $reporttype = $_GET['report'];
}else{
    $reporttype = "";
}

?>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>Trans-X Admin</title>
        <link rel='stylesheet' type='text/css' href='style/adminstyle.css'>
        <meta name='robots' content='noindex,follow' />
    </head>
    <body>
        <div id='admin-container'>
            <div id='admin-header'>
                <div id='header'>TransX Dashboard</div>
                <div id='logout'>
                    Log Out
                </div>
            </div>
            <div id='login-body'>
                <div id='login-panel'>
                    <div id='login-error'></div>
                    <label><strong>User Name</strong></label>
                    <br/>
                    <input type='text' id='user_login' value='' />
                    <br/>
                    <br/>
                    <label><strong>Password</strong></label>
                    <br/>
                    <input type='password' id='user_pass' value='' />
                    <br/>
                    <br/>
                    <input type="button" id='loginBtn' value='Login' />
                    <input type="button" id='reenableBtn' value='Demo: Click To Enable' />
                </div>
            </div>
            <div id='admin-body'>
                <input type="button" id='generateBtn' value='+Create Voucher Batch' />                
                <input type="button" id='releaseBtn' value='Release Voucher Batch' />
                <input type="button" id='reportsBtn' value='Sales Reports' />
                <input type="button" id='viewBtn' value='Vouchers & Customers' />
                <br/>
                <br/>
<?php
if($action === "" || $action === "customer"){
    print "     <div id='display-vouchers' class='voucheroptions' style='display:block;'>";
}else{
    print "     <div id='display-vouchers' class='voucheroptions'>";
}
?>
                    <div id='create-div'>
                        <h1>Welcome</h1>
                        <br/>          
<?php 
////Voucher Display Area
print               "<table id='voucher-table'>";
////Header Area
print               "   <tr>";
print               "       <td class='header'>";
print               "           <strong>Order No.<strong>";                                
print               "       </td>";
print               "       <td class='header'>";
print               "           <strong>Value<strong>";                                
print               "       </td>";
print               "       <td class='header'>";
print               "           <strong>Total Vouchers<strong>";                                
print               "       </td>";
print               "       <td class='header'>";
print               "           <strong>G-Cash10<strong>";                                
print               "       </td>";
print               "       <td class='header'>";
print               "           <strong>G-Cash20<strong>";                                
print               "       </td>";
print               "       <td class='header'>";
print               "           <strong>G-Cash50<strong>";                                
print               "       </td>";
print               "       <td class='header'>";
print               "           <strong>G-Cash100<strong>";                                
print               "       </td>";
print               "   </tr>";
////Voucher Display Area

$stmnt = "SELECT *, SUM(PGC010+PGC020+PGC050+PGC100) AS totalVouchers FROM t_voucherorders GROUP BY order_num ORDER BY request_date DESC LIMIT 10;";
$rslt = mysqli_query($link, $stmnt);

while($rsltarray = $rslt->fetch_array(MYSQLI_ASSOC)){
    print           "   <tr>";
    print           "       <td>";
    print "                     {$rsltarray['order_num']}";
    print           "       </td>";
    print           "       <td>";
    print "                     R{$rsltarray['value']}";
    print           "       </td>";
    print           "       <td>";
    print "                     {$rsltarray['totalVouchers']}";
    print           "       </td>";
    print           "       <td>";
    print "                     {$rsltarray['PGC010']}";
    print           "       </td>";
    print           "       <td>";
    print "                     {$rsltarray['PGC020']}";
    print           "       </td>";
    print           "       <td>";
    print "                     {$rsltarray['PGC050']}";
    print           "       </td>";
    print           "       <td>";
    print "                     {$rsltarray['PGC100']}";
    print           "       </td>";
    print           "   </tr>";
}

print               "</table>";
?>
                        <br/>
                        <br/>
                        <div id='customer-status'></div>
                        <h1>Create New Customer</h1>
                        <br/>
                        <div id='custpanel'>
                            <label><strong>Account Type</strong></label>
                            <br/>
                            <select id='acc_type' value=''>
<?php
$stmnt30 = "SELECT DISTINCT acc_type FROM t_customers ORDER BY acc_type DESC;"
                        or die ("Error fetching..." . mysqli_error($link));
$rslt30 = mysqli_query($link, $stmnt30);

while($rsltarray30 = $rslt30->fetch_array(MYSQLI_ASSOC)){
    print "                     <option value='".$rsltarray30['acc_type']."'>".$rsltarray30['acc_type']."</option>";
}
?>
                            </select>
                            <br/>
                            <br/>
                            <label><strong>Company Name</strong></label>
                            <br/>
                            <input type='text' id='company_name' value=''  />
                            <br/>
                            <br/>
                            <label><strong>Branch Name</strong></label>
                            <br/>
                            <input type='text' id='branch_name' value=''  />
                            <br/>
                            <br/>
                            <label><strong>Contact Person</strong></label>
                            <br/>
                            <input type='text' id='contact_person' value=''  />
                            <br/>
                            <br/>
                            <label><strong>Contact Email</strong></label>
                            <br/>
                            <input type='text' id='contact_email' value=''  />
                            <br/>
                            <br/>
                            <label><strong>Contact Number</strong></label>
                            <br/>
                            <input type='text' id='contact_number' value=''  />
                            <br/>
                            <br/>
                            <label><strong>Sales Rebate Rate(%)</strong></label>
                            <br/>
                            <select id='sales_rebate' style='width:5%;'>
<?php                                
                                for($i=0; $i < 101;$i++){
print "                             <option value='".$i."'>".$i."</option>";
                                }
?>                                
                            </select>
                            <br/>
                            <br/>
                            <label><strong>Log In Name</strong></label>
                            <br/>
                            <input type='text' id='login_name' value=''  />
                            <br/>
                            <br/>
                            <label><strong>Log In Name</strong></label>
                            <br/>
                            <input type='password' id='login_pass' value=''  />
                            <br/>
                            <br/>
                            <br/>
                            <br/>
                            <input type='button' id='create-customer' value='Create New Customer' />
                            <br/>
                            <br/>
                            <br/>
                            <br/>
                        </div>
                    </div>
                </div>

<?php
if($action == "create"){
    print "     <div id='create-vouchers' class='voucheroptions' style='display:block;'>";
}else{
    print "     <div id='create-vouchers' class='voucheroptions'>";
}
?>
                    <div id='create-div'>
                        <h1>Create Voucher Batch</h1>
                        <p>
                            <div id='batch-status'></div>
                            <div id='panel'>
                                <div id='gcashpanel'>
                                    <label><strong>Customer</strong></label>
                                    <select id='customer-select'>
<?php
    $stmnt2 = "SELECT * FROM t_customers;";
    $rslt2 = mysqli_query($link, $stmnt2);

    while($rsltarray2 = $rslt2->fetch_array(MYSQLI_ASSOC)){
    
        print "                         <option value='".$rsltarray2['cust_id']."'>".$rsltarray2['company_name']." - ".$rsltarray2['branch_name']."</option>";
    }
?>
                                
                                    </select>
                                    &nbsp;&nbsp;
                                    <label><strong>G-Cash10</strong></label> <input type='text' id='pgc010-val' value='0' />
                                    &nbsp;&nbsp;
                                    <label><strong>G-Cash20</strong></label> <input type='text' id='pgc020-val' value='0' />
                                    &nbsp;&nbsp;
                                    <label><strong>G-Cash50</strong></label> <input type='text' id='pgc050-val' value='0' />
                                    &nbsp;&nbsp;
                                    <label><strong>G-Cash100</strong></label> <input type='text' id='pgc100-val' value='0' />
                                </div>
                                <br/>
                                <br/>
                                <label><strong>No. Vouchers</strong></label>
                                <br/>
                                <input type='text' id='totalpgc-val' value='0' style='text-align:left; border:0;' readonly="readonly" />
                                <br/>
                                <br/>
                                <label><strong>Amount Due</strong></label>
                                <br/>
                                R<input type='text' id='totalpgc-price' value='0.00' style='text-align:left; border:0; width:auto;' readonly="readonly" />
                                <br/>
                                <br/>
                                <input type='button' id='calculate-batch' value='Calculate Voucher Totals' style='float:left;' />
                                <input type='button' id='create-batch' value='Create Vouchers' style='display:none;float:left;' />
                                <br/>
                                <br/>
                                <br/>
                                <br/>
                            </div>
                        </p>
                    </div>
                </div>
<?php
if($action == "report"){
    print "     <div id='voucher-reports' class='voucheroptions' style='display:block;'>";
}else{
    print "     <div id='voucher-reports' class='voucheroptions' >";
}
?>
                    <div id='create-div'>
                        <h1>Voucher Reports</h1>
                        <br/>
                        <input type="button" id='alltimereport' class='reportselect' value='All Time' />
                        <input type="button" id='yesterdayreport' class='reportselect' value='Yesterday' />
                        <br/>
                        <br/>
                        <strong>Customer</strong>
                        <select id='voucherreport-customer' style='width:auto;'>
<?php
$stmnt10 = "SELECT * FROM t_customers;";
$rslt10 = mysqli_query($link, $stmnt10);

while($rsltarray10 = $rslt10->fetch_array(MYSQLI_ASSOC)){
    
    if($custid === $rsltarray10['cust_id']){
        print "                 <option value='".$rsltarray10['cust_id']."' selected>".$rsltarray10['company_name']." - ".$rsltarray10['branch_name']."</option>";
    }else{
        print "                 <option value='".$rsltarray10['cust_id']."'>".$rsltarray10['company_name']." - ".$rsltarray10['branch_name']."</option>";
    }
}
?>                          
                        </select>
                        &nbsp;&nbsp;
<?php
print "                 <input type='hidden' id='customer-acc' value='".$acctype."' />";
print "                 <label>Account Type: ".$acctype."</label>";                        
////Voucher Display Area
print "                 <table id='salesreports-table'>";
print "                     <tr>";
print "                         <td class='header'>";
print "                             <strong>Customer ID<strong>";                                
print "                         </td>";
print "                         <td class='header'>";
print "                             <strong>Branch ID<strong>";                                
print "                         </td>";
print "                         <td class='header'>";
print "                             <strong>Total Vouchers<strong>";                                
print "                         </td>";
print "                         <td class='header'>";
print "                             <strong>Total Vouchers Value<strong>";                                
print "                         </td>";
print "                         <td class='header'>";
print "                             <strong>Rebate<strong>";                                
print "                         </td>";
print "                     </tr>";

$stmnt11 = "SELECT * FROM t_customers WHERE acc_type = '".$acctype."' AND cust_id = '".$custid."';";
$rslt11 = mysqli_query($link, $stmnt11);

while($rsltarray11 = $rslt11->fetch_array(MYSQLI_ASSOC)){
    
    print "                 <tr>";
    print "                     <td>";
    print "                         ".$rsltarray11['cust_id']."";
    print "                     </td>";
    print "                     <td>";
    print "                         ".$rsltarray11['branch_name']."";
    print "                     </td>";
    
    if($reporttype === "" || $reporttype === "alltimereport"){
        $stmnt12 = "SELECT * FROM t_voucherorders WHERE customer_id = '".$rsltarray11['cust_id']."';";
    }else{
        $stmnt12 = "SELECT *,DATE(request_date) AS requestdate FROM t_voucherorders WHERE customer_id = '".$rsltarray11['cust_id']."' AND DATE(request_date) = DATE(NOW()-INTERVAL 1 DAY);";
    }
    
    $rslt12 = mysqli_query($link, $stmnt12);
    $vouchercount = 0;
    
    while($rsltarray12 = $rslt12->fetch_array(MYSQLI_ASSOC)){

        $stmnt13 = "SELECT * FROM t_voucher WHERE order_num = '".$rsltarray12['order_num']."';";
        $rslt13 = mysqli_query($link, $stmnt13);
        
        while($rsltarray13 = $rslt13->fetch_array(MYSQLI_ASSOC)){
            $vouchercount = $vouchercount + 1;
        }
    }
    
    print "                     <td>";  
    print "                         $vouchercount";                                
    print "                     </td>";
    
    if($reporttype === "" || $reporttype === "alltimereport"){
        $stmnt14 = "SELECT SUM(value) AS total FROM t_voucherorders WHERE customer_id = '".$rsltarray11['cust_id']."';";
    }else{
        $stmnt14 = "SELECT SUM(value) AS total FROM t_voucherorders WHERE customer_id = '".$rsltarray11['cust_id']."' AND DATE(request_date) = DATE(NOW()-INTERVAL 1 DAY);";
    }
    
    
    $rslt14 = mysqli_query($link, $stmnt14);
    $rsltarray14 = $rslt14->fetch_array(MYSQLI_ASSOC);
    
    print "                     <td>";
    
    if($rsltarray14['total'] === "" || $rsltarray14['total'] === null){
        print "                     R0.00";
    }else{
        print "                     R".$rsltarray14['total']."";
    }
    
    print "                     </td>";
    print "                     <td>";
    
    $rebate = $rsltarray11['sales_rebate'];
    $calc = $rebate*($rsltarray14['total']/100);
    $rebateVal = number_format($calc, 2, '.', '');
    
    print "                         R$rebateVal";                                
    print "                     </td>";
    print "                 </tr>";
}

print "                 </table>";
?>
                    </div>
                </div>
<?php
if($action == "release"){
    print "     <div id='release-vouchers' class='voucheroptions' style='display:block;'>";
}else{
    print "     <div id='release-vouchers' class='voucheroptions'>";
}
?>
                
                    <div id='create-div'>
                        <h1>Release Voucher Batch</h1>
                        <br/>
                        <div id='release-status'></div>
                        <div id='release-table' style='display:block;'>
                    
<?php 
////Voucher Display Area
print               "<table id='voucher-table'>";
////Header Area
print               "   <tr>";
print               "       <td class='header'>";
print               "           <strong>Order No.<strong>";                                
print               "       </td>";
print               "       <td class='header'>";
print               "           <strong>Value<strong>";                                
print               "       </td>";
print               "       <td class='header'>";
print               "           <strong>Total Vouchers<strong>";                                
print               "       </td>";
print               "       <td class='header'>";
print               "           <strong>G-Cash10<strong>";                                
print               "       </td>";
print               "       <td class='header'>";
print               "           <strong>G-Cash20<strong>";                                
print               "       </td>";
print               "       <td class='header'>";
print               "           <strong>G-Cash50<strong>";                                
print               "       </td>";
print               "       <td class='header'>";
print               "           <strong>G-Cash100<strong>";                                
print               "       </td>";
print               "       <td class='header'>";
print               "           <strong>Release<strong>";                                
print               "       </td>";
print               "   </tr>";
////Voucher Display Area

$stmnt = "SELECT *, SUM(PGC010+PGC020+PGC050+PGC100) AS totalVouchers FROM t_voucherorders GROUP BY order_num ORDER BY request_date DESC LIMIT 10;";
$rslt = mysqli_query($link, $stmnt);

while($rsltarray = $rslt->fetch_array(MYSQLI_ASSOC)){
    print           "   <tr>";
    print           "       <td>";
    print "                     {$rsltarray['order_num']}";
    print           "       </td>";
    print           "       <td>";
    print "                     R{$rsltarray['value']}";
    print           "       </td>";
    print           "       <td>";
    print "                     {$rsltarray['totalVouchers']}";
    print           "       </td>";
    print           "       <td>";
    print "                     {$rsltarray['PGC010']}";
    print           "       </td>";
    print           "       <td>";
    print "                     {$rsltarray['PGC020']}";
    print           "       </td>";
    print           "       <td>";
    print "                     {$rsltarray['PGC050']}";
    print           "       </td>";
    print           "       <td>";
    print "                     {$rsltarray['PGC100']}";
    print           "       </td>";
    print           "       <td style='text-align:center;'>";
    
    if($rsltarray['release_date'] === "0000-00-00 00:00:00"){
        print "                 <input type='button' id='".$rsltarray['order_num']."' class='releasebatchBtn' value='Release Batch' />";
    }else{
        print "                 <label style='color:green;'>Batch Released</label>";
    }
    print           "       </td>";
    print           "   </tr>";
}

print               "</table>";
?>
                    
                            </div>
<!--                        <p>
                            <label><strong>Customer</strong></label>
                            <br/>
                        </p>-->
                    </div>
                </div>
            </div>
            <div id='admin-footer'></div>
        </div>
    </body>
</html>
<script src='includes/jquery-cookie-master/src/jquery.cookie.js'></script>
<script type='text/javascript'>
    $(document).ready(function(){
        var logincount = 0;
        var visited = $.cookie('loggedin');
        
        checkLogin(visited);
        
        function checkLogin(logincheck){
            
            if(logincheck == '1'){
                //alert('Logging In');
                $("#login-body").hide(0);
                $("#admin-body").show(0);
                $("#logout").show(0);
            }
        }
        
        $("#logout").click(function(){
            
            $("#logout").slideUp(1000);
            
            $.cookie("loggedin", null,{ 
                path: '/' 
            });
            document.location.href='index.php';
        });
        
        $("#loginBtn").click(function(){
            var username = $("#user_login").val();
            var userpass = $("#user_pass").val();
            
            if((username === "admin" && userpass === "admin") || visited == '1'){
                $("#login-body").slideUp('200');
                $("#admin-body").slideDown('700');
                $("#logout").show(0);
               
                $.cookie('loggedin', '1',{ // create a cookie with all available options
                    expires: 1, // expires in one day
                    path: '/', // accessible from the whole site...
                    domain: '127.0.0.1',
                    secure: false // ...true for cookies on a secure connection
                });
                
                var login = 1;
            }else if((username !== "admin" || userpass !== "admin") && logincount >= 5){
                $("#user_login").val("Locked Out");
                $("#user_login").attr('disabled','disabled');
                $("#user_pass").val("");
                $("#user_pass").attr('disabled','disabled');
                $("#loginBtn").attr('disabled','disabled');
                $("#reenableBtn").show();
            }else{
                logincount = logincount + 1;
                
                $("#login-error").html("<div>Error: Your login credentials are incorrect. After 5 failed log in attempts your account will be locked and you will need to contact your Website Administrator to unlock your account.</div>");
                $("#login-error").fadeIn("1000").delay("5000");
                $("#login-error").delay("1000").fadeOut("1000");
            }
        });
        
        $("#reenableBtn").click(function(){
            var logincount = 0;
            
            $("#user_login").val("");
            $("#user_login").removeAttr('disabled');
            $("#user_pass").val("");
            $("#user_pass").removeAttr('disabled');
            $("#loginBtn").removeAttr('disabled');
            $("#reenableBtn").hide();
            
            alert("Admin Site Enabled");
        });
        
        $("#generateBtn").click(function(){
            $(".voucheroptions").hide(0);
            //$("#create-vouchers").slideDown(50);
            document.location.href='index.php?ref=create';
        });
        
        $("#releaseBtn").click(function(){
            $(".voucheroptions").hide(0);
            //$("#release-vouchers").slideDown(50);
            document.location.href='index.php?ref=release';
        });
        
        $("#reportsBtn").click(function(){
            $(".voucheroptions").hide(0);
            //$("#voucher-reports").slideDown(50);
            document.location.href='index.php?ref=report';
        });
        
        $("#viewBtn").click(function(){
            $(".voucheroptions").hide(0);
            //$("#display-vouchers").slideDown(50);
            document.location.href='index.php?ref=customer';
        });
        
        $(".reportselect").click(function(){
            var reporttype = $(this).attr('id');
            
            var cusid = $("#voucherreport-customer").val();
            var accounttype = $("#customer-acc").val();
            
            document.location.href='index.php?ref=report&type='+accounttype+'&report='+reporttype+'&id='+cusid;
            
        });
        
       
        $(".releasebatchBtn").click(function(){
           var ordernum = $(this).attr('id');
           
           var dataString = "ordernum=" + ordernum;
           
           var batch = confirm("Are you sure you want to release this voucher batch?");
           
           if(batch == true){
               $.ajax({
                    type: "POST",   
                    async: false,
                    url: "releaseVoucher.php",
                    data: dataString, 
                    success: function(data){
                        $("#release-status").html(data);
                        $("#release-status").show(500).slideDown(1500).delay(5000);
                    }
                }); 
            }
       });
       
        $("#calculate-batch").click(function(){
            var pgc10val = $("#pgc010-val").val();
            var pgc20val = $("#pgc020-val").val();
            var pgc50val = $("#pgc050-val").val();
            var pgc100val = $("#pgc100-val").val();
            var pgctotal = $("#totalpgc-val").val();
            var pgcprice = $("#totalpgc-price").val();
            
            pgc10total = parseInt(pgctotal)+parseInt(pgc10val);
            pgc20total = parseInt(pgctotal)+parseInt(pgc20val);
            pgc50total = parseInt(pgctotal)+parseInt(pgc50val);
            pgc100total = parseInt(pgctotal)+parseInt(pgc100val);
            
            pgc10price = (parseInt(pgc10val)+parseInt(pgcprice))*10;
            pgc20price = (parseInt(pgc20val)+parseInt(pgcprice))*20;
            pgc50price = (parseInt(pgc50val)+parseInt(pgcprice))*50;
            pgc100price = (parseInt(pgc100val)+parseInt(pgcprice))*100;
            
            var ordervalue = parseInt(pgc10total) + parseInt(pgc20total) + parseInt(pgc50total) + parseInt(pgc100total);
            var orderprice = parseInt(pgc10price) + parseInt(pgc20price) + parseInt(pgc50price) + parseInt(pgc100price);
            
            $("#totalpgc-val").val(ordervalue);
            $("#totalpgc-price").val(orderprice.toFixed(2));
            $("#calculate-batch").hide();
            $("#create-batch").show();
        });
        
        $("#create-batch").click(function(){
            var customer = $("#customer-select").val();
            //var pgc10val = $("#pgc010-val").val();
            var pgc10val = $("#pgc010-val").val();
            var pgc20val = $("#pgc020-val").val();
            var pgc50val = $("#pgc050-val").val();
            var pgc100val = $("#pgc100-val").val();
            var ordervalue = $("#totalpgc-val").val();
            var orderprice = $("#totalpgc-price").val();
            
            var dataString = "customerid=" + customer + "&pgc010=" + pgc10val + "&pgc020=" + pgc20val + "&pgc050=" + pgc50val +
                            "&pgc100=" + pgc100val + "&orderval=" + ordervalue + "&totalprice=" + orderprice;
            
            $("#batch_status").html("Processing. Please wait...<br/><br/>").delay(5000).hide(0);
            $("#batch_status").show(500);
            
            
            $.ajax({
                type: "POST",   
                async: true,
                url: "generateVoucher.php",
                data: dataString, 
                success: function(data){
                    $("#batch-status").html(data);
                    $("#batch-status").show(500).slideDown(1500).delay(5000);                
                    //$("#passwordedit-area").slideUp(500).hide(1500);
                }
            }); 
        });
        
        $("#create-customer").click(function(){
            var acctype = $("#acc_type").val();
            var companyname = $("#company_name").val();
            var branchname = $("#branch_name").val(); 
            var cusname = $("#contact_person").val();
            var cusemail = $("#contact_email").val();
            var cusnum = $("#contact_number").val();
            var rebate = $("#sales_rebate").val();
            var loginname  = $("#login_name").val();
            var loginpass = $("#login_pass").val();
            
            var dataString  = "acctype=" + acctype + "&company=" + companyname + "&branch=" + branchname + "&contact=" + cusname + 
                            "&contactemail=" + cusemail + "&contactnum=" + cusnum + "&rebate=" + rebate + "&loginname=" + loginname + "&loginpass=" + loginpass;
            
            $.ajax({
                type: "POST",   
                async: false,
                url: "createCustomer.php",
                data: dataString, 
                success: function(data){
                    $("#customer-status").html(data);
                    $("#customer-status").show(500).slideDown(1500).delay(10000).hide(500);
                    $(document.body).animate({
                      scrollTop: 0
                    }), 1000;
                    //$("#passwordedit-area").slideUp(500).hide(1500);
                }
            }); 
        });
    });
</script>