<?php
include("includes/db_conn.php");
$link = mysqli_connect($db_host,$db_user,$db_pass,$db_name);
if(mysqli_connect_errno($link)){
    echo "Failed to connect to database: ".mysqli_connect_error();
}

if(isset($_POST["acctype"])){
    $acctype = $_POST["acctype"];
}else{
    echo "Error: Null Value";
    exit;
}
if(isset($_POST["company"])){
    $company = $_POST["company"];
}else{
    echo "Error: Null Value";
    exit;
}
if(isset($_POST["branch"])){
    $branch = $_POST["branch"];
}else{
    echo "Error: Null Value";
    exit;
}
if(isset($_POST["contact"])){
    $contact = $_POST["contact"];
}else{
    echo "Error: Null Value";
    exit;
}
if(isset($_POST["contactemail"])){
    $contactemail = $_POST["contactemail"];
}else{
    echo "Error: Null Value";
    exit;
}
if(isset($_POST["contactnum"])){
    $contactnum = $_POST["contactnum"];
}else{
    echo "Error: Null Value";
    exit;
}
if(isset($_POST["rebate"])){
    $rebate = $_POST["rebate"];
}else{
    echo "Error: Null Value";
    exit;
}
if(isset($_POST["loginname"])){
    $loginname = $_POST["loginname"];
}else{
    echo "Error: Null Value";
    exit;
}
if(isset($_POST["loginpass"])){
    $loginpass = $_POST["loginpass"];
}else{
    echo "Error: Null Value";
    exit;
}

createCustomer($acctype,$company,$branch,$contact,$contactemail,$contactnum,$rebate,$loginname,$loginpass);

function createCustomer($acctype,$company,$branch,$contact,$contactemail,$contactnum,$rebate,$lname,$lpass){
    
    global $link;
    
    if($company === "" || $branch === "" || $contact === "" || $contactemail === "" || $contactnum === "" || $rebate === "" || $lname === "" || $lpass === ""){
        echo "ERROR: Please enter values in all required fields";
        exit;
    }else{
        $stmnt = "SELECT * FROM t_customers WHERE company_name = '".$company."' ORDER BY cust_id ASC;"
                        or die ("Error fetching..." . mysqli_error($link));
        $rslt = mysqli_query($link, $stmnt);
        $rsltarray = $rslt->fetch_array(MYSQLI_ASSOC);
        $custid = $rsltarray['cust_id'];
        
        if($custid != "" || $custid != null){
            $newcustid = "Gotta Generate an ID based on a Previous one";
        }else{
            $coname = $company;
            $abbrev = substr($coname,0,3);
            $newabbrev = strtoupper($abbrev);
            $newcustid = $newabbrev."001";
        }
        
        $placeholder = "+";
        $stmnt2 = $link->prepare("INSERT INTO`t_customers`(`cust_id`,`acc_type`,`company_name`,`branch_name`,`contact_person`,`contact_email`,`contact_num`,`ip_add`,`sales_rebate`,`login_str`,`password`)
                                VALUES (?,?,?,?,?,?,?,?,?,?,?);");
        $stmnt2->bind_param('ssssssssiss',$newcustid,$acctype,$company,$branch,$contact,$contactemail,$contactnum,$placeholder,$rebate,$lname,$lpass);
        $stmnt2->execute();
        $stmnt2->close();
        
        if(mysqli_affected_rows($link) == 0){
            echo "Failed to execute query.";
            exit;
        }else{
            echo "Customer Successfully Created.";
            exit;
        }
    }
}

function createLogin(){
}

?>