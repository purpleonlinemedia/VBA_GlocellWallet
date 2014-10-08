<?php
include_once 'functions.php';
/**
 * Database connections
 * Database queries
 *
 */
class Databases 
{

    public $conn;

    public function __construct() {
        include('dbconfig.php');
        
        $this->conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

        if ($this->conn->connect_errno) {            
            writelog("log.log", "[MYSQL] Failed to dbconnect to MySQL: (" . $this->conn->dbconnect_errno . ") " . $this->conn->connect_error);
            //writelog(date('m/d/Y h:i:s a', time()), "Failed to dbconnect to MySQL:(" . $this->dbconn->connect_errno . ")" . $this->dbconn->connect_error);
        }else{
            writelog("log.log", "[MYSQL] Connected to database");
        }
    }
    
    function dbNextRecord($results)
    {
     $nxtRec = @mysqli_fetch_assoc($results);
     return $nxtRec;
    }     

    function runQuery($query)
    {
        
        $this->res = mysqli_query($this->conn,$query,MYSQLI_STORE_RESULT);
         if(mysqli_errno($this->conn) != 0)
         {                
                writelog("log.log", "[MYSQL] Failed to run query. MySQL error: " .mysqli_error($this->conn));
         }
        return $this->res;
    } 

}
