<?php
class User
{
    private $conn;
    private $table_name = "usuarios";

    public $id;
    public $name;
    public $email;
    public $created_at;

    public function __construct($db)
    {
        $this->conn = $db;
    }

   public function create()
   {
    $query = "INSERT INTO " . $this->table_name . " 
              SET n_control=:n_control, name=:name, entry_date=:entry_date";

    $stmt = $this->conn->prepare($query);

    // Limpieza de datos
    $this->n_control = htmlspecialchars(strip_tags($this->n_control));
    $this->name = htmlspecialchars(strip_tags($this->name));
    $this->entry_date = date('Y-m-d H:i:s');

    // Enlace de parámetros
    $stmt->bindParam(":n_control", $this->n_control);
    $stmt->bindParam(":name", $this->name);
    $stmt->bindParam(":entry_date", $this->entry_date);

    if ($stmt->execute()) {
        $this->id = $this->conn->lastInsertId();
        return true;
    }
    return false;
    }

    public function read()
    {
    $query = "SELECT id, n_control, name, entry_date 
              FROM " . $this->table_name . " 
              ORDER BY entry_date DESC";

    $stmt = $this->conn->prepare($query);
    $stmt->execute();
    return $stmt;
    }

    public function readOne()
    {
    $query = "SELECT id, n_control, name, entry_date 
              FROM " . $this->table_name . " 
              WHERE id = :id 
              LIMIT 1";

    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(":id", $this->id);
    $stmt->execute();

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        $this->n_control = $row['n_control'];
        $this->name = $row['name'];
        $this->entry_date = $row['entry_date'];
        return true;
    }
    return false;
    }

    public function update()
    {
    $query = "UPDATE " . $this->table_name . " 
              SET n_control = :n_control, name = :name 
              WHERE id = :id";

    $stmt = $this->conn->prepare($query);

    $this->n_control = htmlspecialchars(strip_tags($this->n_control));
    $this->name = htmlspecialchars(strip_tags($this->name));
    $this->id = htmlspecialchars(strip_tags($this->id));

    $stmt->bindParam(':n_control', $this->n_control);
    $stmt->bindParam(':name', $this->name);
    $stmt->bindParam(':id', $this->id);

    if ($stmt->execute()) {
        return true;
    }
    return false;
    }
    
    public function delete()
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $this->id = htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(':id', $this->id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}
?>