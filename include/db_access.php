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
            databaseErrorLog("[MYSQL] Failed to dbconnect to MySQL: (" . $this->conn->dbconnect_errno . ") " . $this->conn->connect_error);
            //writelog(date('m/d/Y h:i:s a', time()), "Failed to dbconnect to MySQL:(" . $this->dbconn->connect_errno . ")" . $this->dbconn->connect_error);
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
                databaseErrorLog("[MYSQL] Failed to run query. MySQL error: " .mysqli_error($this->conn));
         }
        return $this->res;
    } 
    
    function logXMLIn($data,$host)
    {
        $query = "INSERT INTO t_xmllog (xmlIn,xmlOut,hostAddr) VALUES ('".mysqli_real_escape_string($this->conn,$data)."','','$host');";
        mysqli_query($this->conn,$query,MYSQLI_STORE_RESULT);
        if(mysqli_errno($this->conn) != 0)
        {                
               databaseErrorLog("[MYSQL] Failed to insert XML in. MySQL error: " .mysqli_error($this->conn));
        }
        
        $this->res = mysqli_query($this->conn,"SELECT LAST_INSERT_ID() as lastRecord;",MYSQLI_STORE_RESULT);
        
        $row = $this->res->fetch_array(MYSQLI_ASSOC);
        
        return $row['lastRecord'];
    } 
    
    function logXMLout($data,$id)
    {
        $query = "UPDATE t_xmllog SET xmlOut = '".mysqli_real_escape_string($this->conn,$data)."' WHERE id = '$id';";
        mysqli_query($this->conn,$query,MYSQLI_STORE_RESULT);
        if(mysqli_errno($this->conn) != 0)
        {                
               databaseErrorLog("[MYSQL] Failed to update XML out. MySQL error: " .mysqli_error($this->conn));
        }
                
    }

}
