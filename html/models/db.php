<?php

class DB
{
    private PDO $conn;

    /**
     * Connects to the database or creates a database with $init_file_paths. 
     */
    function __construct(string $db_path, array|null $init_file_paths = null)
    {
        $new_db = !file_exists($db_path);
        if ($new_db) {
            $f = fopen($db_path, 'w');
            if ($f === false) throw new Exception('couldnt create database file');
            fclose($f);
        }
        $this->conn = new PDO('sqlite:' . $db_path);
        $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        if ($new_db && $init_file_paths !== null) {
            foreach ($init_file_paths as $f)
                $this->execute_file($f);
        }
    }

    private function execute_file(string $file_path): void
    {
        $sql = file_get_contents($file_path);
        $this->conn->exec($sql);
    }

    /**
     * @return PDOStatement|bool PDOStatement of success, false otherwise
     */
    public function execute(string $query, array $params = []): PDOStatement|bool
    {
        $statement = $this->conn->prepare($query);

        foreach ($params as $name => $value) {
            if (is_array($value))
                $statement->bindValue($name, $value[0], $value[1]);
            else
                $statement->bindValue($name, $value);
        }

        if (!$statement->execute())
            return false;

        return $statement;
    }

    public function get_last_insert_id(): string|false
    {
        return $this->conn->lastInsertId();
    }

    public function parse_date_time(string $date_time): DateTime|null
    {
        $res = DateTime::createFromFormat('Y-m-d H:i', $date_time);
        if ($res == false)
            return null;
        return $res;
    }
    public function format_date_time(DateTime $date_time): string
    {
        return $date_time->format('Y-m-d H:i');
    }
}