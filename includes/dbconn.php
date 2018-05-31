<?php
/*apperently having a class and a method with the same name is not good as
I had continued to get warning popping up. Changing here */
class Connection {
  protected $db;
  public function Connection() {
    $conn = NULL;
      try {
        $conn = new PDO("mysql:host=localhost;dbname=REDACTED", "REDACTED", "REDACTED");
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      } catch(PDOException $e) {
        echo "ERROR" . $e->getMessage();
      }
        $this->db = $conn;
      }
      public function getConnection() {
        return $this->db;
      }
  }

 ?>
