<?php

namespace App\Models;

use mysqli;

class Model
{
    protected $db_host = DB_HOST;
    protected $db_user = DB_USER;
    protected $db_pass = DB_PASS;
    protected $db_name = DB_NAME;

    protected $connection;
    protected $query;
    protected $table;

    protected $sql, $data = [], $params = null;

    protected $orderBy = "";

    public function __construct()
    {
        $this->connection();
    }

    public function connection()
    {
        $this->connection = new mysqli($this->db_host, $this->db_user, $this->db_pass, $this->db_name);

        if ($this->connection->connect_error) {
            die('Error de conexión: ' . $this->connection->connect_error);
        }
    }

    public function query($sql, $data = [], $params = null)
    {
        if ($data) {
            if ($params == null) {
                $params = str_repeat('s', count($data));
            }

            $stmt = $this->connection->prepare($sql);
            $stmt->bind_param($params, ...$data);
            $stmt->execute();

            $this->query = $stmt->get_result();
        } else {
            $this->query = $this->connection->query($sql);
        }

        return $this;
    }

    public function orderBy($column, $order = 'ASC'){
        if(empty($this->orderBy)){
            $this->orderBy = " ORDER BY {$column} {$order}";
        } else {
            $this->orderBy .= ", {$column} {$order}";
        }
        

        return $this;
    }

    public function first()
    {
        if (empty($this->query)) {
            
            if(empty($this->sql)){
                $this->sql = "SELECT * FROM {$this->table}";
            }
            
            $this->sql .= $this->orderBy;
            
            $this->query($this->sql, $this->data, $this->params);
        }

        return $this->query->fetch_assoc();
    }

    public function get()
    {
        if (empty($this->query)) {

            if(empty($this->sql)){
                $this->sql = "SELECT * FROM {$this->table}";
            }
            
            $this->sql .= $this->orderBy;
            
            $this->query($this->sql, $this->data, $this->params);
        }

        return $this->query->fetch_all(MYSQLI_ASSOC);
    }

    public function paginate($cant = 15)
    {
        $page = isset($_GET['page']) ? $_GET['page'] : 1;

        if ($this->sql) {
            $sql = $this->sql . ($this->orderBy ?? '') . " LIMIT " . ($page - 1) * $cant . ",{$cant}";
            $data = $this->query($sql, $this->data, $this->params)->get();
        } else {
            $sql = "SELECT SQL_CALC_FOUND_ROWS * FROM {$this->table} " . ($this->orderBy ?? '') . " LIMIT " . ($page - 1) * $cant . ",{$cant}";
            
            $data = $this->query($sql)->get();
        }


        $total = $this->query('SELECT FOUND_ROWS() as total')->first()['total'];

        $uri = $_SERVER['REQUEST_URI'];
        $uri = trim($uri, '/');

        if (strpos($uri, '?')) {
            $uri = substr($uri, 0, strpos($uri, '?'));
        }

        $last_page = ceil($total / $cant);

        return [
            'total' => $total,
            'from' => ($page - 1) * $cant + 1,
            'to' => ($page - 1) * $cant + count($data),
            'current_page' => $page,
            'last_page' => $last_page,
            'next_page_url' => $page < $last_page ? '/' . $uri . '?page=' . $page + 1 : null,
            'prev_page_url' => $page > 1 ? '/' . $uri . '?page=' . $page - 1 : null,
            'data' => $data,
        ];
    }
    
    //consultas
    public function all()
    {
        //SELECT * FROM table
        $sql = "SELECT * FROM {$this->table}";

        return $this->query($sql)->get();
    }

    public function find($id)
    {
        //SELECT * FROM table WHERE id = 1
        $sql = "SELECT * FROM {$this->table} WHERE id = ?";

        return $this->query($sql, [$id], 'i')->first();
    }

    public function where($column, $operator, $value = null)
    {
        if ($value == null) {
            $value = $operator;
            $operator = '=';
        }

        if (empty($this->sql)) {
            $this->sql = "SELECT SQL_CALC_FOUND_ROWS * FROM {$this->table} WHERE {$column} {$operator} ?";
            $this->data[] = $value;
        } else {
            $this->sql .= " AND {$column} {$operator} ?";
            $this->data[] = $value;
        }

        //SELECT * FROM table WHERE name = 'value'


        /* $this->query($sql, [$value]); */

        return $this;
    }

    public function create($data)
    {
        //INSERT INTO table (value1, value2, value3) VALUES (?, ?, ?)

        $columns = implode(', ', array_keys($data));
        $values = array_values($data);

        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES (" . str_repeat('?, ', count($values) - 1) . " ?)";

        $this->query($sql, $values);

        $insert_id = $this->connection->insert_id;

        return $this->find($insert_id);
    }

    public function update($id, $data)
    {
        //UPDATE table SET name1 = ?, name2 = ?, name3 = ? WHERE id = 1
        $fields = [];
        foreach ($data as $key => $value) {
            $fields[] = "{$key} = ?";
        }

        $fields = implode(', ', $fields);

        $sql = "UPDATE {$this->table} SET {$fields} WHERE id = ?";

        $values = array_values($data);
        $values[] = $id;

        $this->query($sql, $values);

        return $this->find($id);
    }

    public function delete($id)
    {
        //DELETE FROM table WHERE id = 1

        $sql = "DELETE FROM {$this->table} WHERE id = ?";

        $this->query($sql, [$id], 'i');
    }
}
