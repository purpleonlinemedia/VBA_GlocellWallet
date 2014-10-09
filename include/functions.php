<?php
//Write log function
function writelog($log,$data)
{
    file_put_contents($log,$data."\n", FILE_APPEND|LOCK_EX);
}

//Function to check if the request is an AJAX request
function is_ajax() {
  return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
}

