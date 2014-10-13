<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>Login | KCMOBILE</title>
        <link href='http://fonts.googleapis.com/css?family=Jura' rel='stylesheet' type='text/css'>
        <link rel='stylesheet' type='text/css' href='style/terminal.css'>
        <meta name='robots' content='noindex,follow' />
    </head>
    <body>
<?php

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
    
$sequence = "576100026";
$numarr = substr($sequence,-5,-1);

///Increment Sequential Number
$num = $numarr + 1;
$padnum = sprintf('%1$04d',$num, $random_chars);

echo $padnum;
?>
    </body>
</html>