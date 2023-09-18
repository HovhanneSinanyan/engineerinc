<?php

namespace Kernel;

use Exception;

class Builder
{
    private $connection;
    protected $result;
    protected $fields;
    protected $query = [];
    protected $bindings = [];
    protected $sql = '';
    protected $table;
    protected $add = '';
    protected $limit;

    private $preventWheres = false;


    public function __construct()
    {
        $pdo = new DBConnect();
        $this->connection =$pdo->getConnection();
    }


    public function table($tableName)
    {
        $this->table = $tableName;
        return $this;
    }

    public function save()
    {
        try {
            $this->runQuery();
            return $this->lastId();
        } catch(Exception $e) {
            throw $e;
        }
         
    }

    public function select($args = '*')
    {
        if (is_array($args)) {
            $args = "`". implode('` , `', $args) . "`";
        }
        $this->query['action'] = 'SELECT';
        $this->query['args'] = ' ' . trim($args) . ' ';
        return $this;
    }

    public function create($fields = [])
    {
        $useFields = !empty($fields) ? $fields: $this->fields;
        $this->query['action'] = 'INSERT';
        $this->query['args'] = array_keys($useFields);
        $this->bindings = array_values($useFields);
        return $this;
    }
    
    public function update($fields = [])
    {
        $useFields = !empty($fields) ? $fields: $this->fields;
        $this->query['action'] = 'UPDATE';
        $fields = $this->fields;
        $args = array_keys($useFields);
        foreach ($args as $key => $value){
            $args[$key] = $value . '=?';
        }
        $this->query['args'] = array_keys($useFields);
        $this->bindings = array_values($useFields);

        if($this->timestamps) {
            $this->query['args'][] = 'updated_at';
            $this->bindings[] = date('Y-m-d H:i:s');
        }
        return $this;
    }

    
    public function delete()
    {
        $this->query['action'] = 'DELETE';
        $this->query['args'] = '';
        return $this->runQuery()->rowCount();
    }

    public function where($column, $operator, $value)
    {
        $this->query['wheres'][] = '`'.$column.'`' . ' ' . $operator . ' ?';
        $this->bindings[] = $value;
        return $this;
    }

    public function generateSql()
    {

        if (!isset($this->query['action'])) {
            $this->select();
        }

        switch ($this->query['action']) {
            case 'INSERT':
                $argList = implode('`, `', $this->query['args']);
                $this->sql = 'INSERT INTO ' . $this->table .
                    ' ( `'.$argList.'` ) VAlUES (' .
                    str_repeat('?,', (count($this->query['args'])) - 1). '?)';
                break;
            case 'UPDATE':
                $argList = implode('=? ,', $this->query['args']) . '=? ' ;
                $this->sql = $this->query['action'] . ' ' . $this->table . ' SET ' .
                $argList;
                break;
            default:
                $this->sql = $this->query['action'] . ' ' . $this->query['args'] . 'FROM ' . $this->table . ' ';
                break;

        }
        $this->attachWheres();
        $this->addFilters();

        return $this;
    }


    public function get()
    {
        $caller = get_called_class();
        $query = $this->runQuery();
        $result = [];
        while ($row = $query->fetchObject($caller)) {
            $result[] = $row->fields;
        }
        return $result;
    }

    public function first()
    {
        $this->limit = 1;
        $query = $this->runQuery();
        return $query->fetchObject(get_called_class());
    }

    public function toSql()
    {
        $this->generateSql();
        return [
            'sql' => $this->sql,
            'bindings' => $this->bindings,
        ];
    }

    private function attachWheres()
    {
        if (isset($this->query['wheres']) && !$this->preventWheres) {
            $this->sql .= 'WHERE ' . implode(' AND ', $this->query['wheres']);
        }
        return $this;
    }

    private function addFilters()
    {
        if($this->limit){
            $this->sql .= ' LIMIT ' .$this->limit;
        }
    }

    private function runQuery(){
        $this->generateSql();
        $query = $this->connection->prepare($this->sql);

        foreach ($this->bindings as $key => $binding) {
            $key++;
            $query->bindParam($key, $binding);
        }

        $query->execute($this->bindings);
        $this->flush();
        return $query;
    }

    protected function lastId(){
        return $this->connection->lastInsertId();
    }

    private function flush() {
        $this->fields = [];
        $this->query = [];
        $this->bindings = [];
        $this->sql = '';
        $this->add = '';
        $this->limit = null;

        $this->preventWheres = false;
    }

    public function toArray() {
        return json_decode(json_encode($this->fields));
    }
}