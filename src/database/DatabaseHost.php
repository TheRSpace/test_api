<?php

namespace app\database;

use app\core\Application;
use app\core\Response;
use PDO;

class DatabaseHost
{
   private $db_host;
   private $db_name;
   private $db_username;
   private $db_password;
   private $db_port;
   private $connection;

   public function __construct(array $config)
   {
      $this->setDbHost($config['host'] ?? '');
      $this->setDbName($config['database'] ?? '');
      $this->setDbUsername($config['username'] ?? '');
      $this->setDbPassword($config['password'] ?? '');
      $this->setDbPort($config['port'] ?? '');
      $this->connect();
   }
   private function connect()
   {
      $conn = null;
      try {
         $conn = new PDO(
            'mysql:host=' . $this->db_host .
               ';dbname=' . $this->db_name,
            $this->db_username,
            $this->db_password
         );
         $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
         $conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
         $conn->setAttribute(PDO::ATTR_STRINGIFY_FETCHES, false);

         $this->connection = $conn;
      } catch (\PDOException $e) {
         $response = ["Database connection failed:" . $e->getMessage()];
         $response = new Response(['error' => $response], 400);
         $response->send();
         exit;
      }
   }
   public function getConnection(): PDO
   {
      return $this->connection;
   }
   public function setDbHost($db_host)
   {
      $this->db_host = $db_host;
   }
   public function setDbName($db_name)
   {
      $this->db_name = $db_name;
   }
   public function setDbUsername($db_username)
   {
      $this->db_username = $db_username;
   }
   public function setDbPassword($db_password)
   {
      $this->db_password = $db_password;
   }
   public function setDbPort($db_port)
   {
      $this->db_port = $db_port;
   }
   public function getDbHost()
   {
      return $this->db_host;
   }
   public function getDbName()
   {
      return $this->db_name;
   }
   public function getDbUsername()
   {
      return $this->db_username;
   }
   public function getDbPassword()
   {
      return $this->db_password;
   }
   public function getDbPort()
   {
      return $this->db_port;
   }
}
