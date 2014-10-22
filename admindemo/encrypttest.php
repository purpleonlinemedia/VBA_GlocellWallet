<?php
include("includes/db_conn.php");
$link = mysqli_connect($db_host,$db_user,$db_pass,$db_name);
if(mysqli_connect_errno($link)){
    echo "Failed to connect to database: ".mysqli_connect_error();
}

require_once '../../php/Crypt/GPG.php';
//require_once '/home/gaccvba/php/GPG.php';

?>