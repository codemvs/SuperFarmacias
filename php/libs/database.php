<?php
include_once '../config/config.php';
class Database {

    public static $instance;

    private $host;
    private $db;
    private $user;
    private $password;
    private $charset;

    private function __construct(){
        $this->host     = constant('HOST');
        $this->db       = constant('DB');
        $this->user     = constant('USER');
        $this->password = constant('PASSWORD');
        $this->charset  = constant('CHARSET');
    }

    function connect(){
    
        try{
            
            $connection = "mysql:host=" . $this->host . ";dbname=" . $this->db . ";charset=" . $this->charset;
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];
            $pdo = new PDO($connection, $this->user, $this->password, $options);
    
            return $pdo;

        }catch(PDOException $e){            
            throw new Exception('Ocurrio un error en la conexión a la base de datos.');
        }   
    }

    public static function getInstance(){
        if(!isset( Database::$instance ) ) {
            Database::$instance = new Database();
        }
        return Database::$instance;
    }
}

?>