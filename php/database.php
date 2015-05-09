<?php

defined('_JEXEC') or die('Restricted access');

class Database {

    var $mysqli;
    public static $DATE_FORMAT = "d-m-Y H:i:s";
    public static $DATE_SQL_FORMAT = "Y-m-d H:i:s";
    private static $factory = null;

    /**
     * get an instance of the factory
     * @return BootFactory
     */
    public static function inst() {
        if (self::$factory == null) {
            self::$factory = new Database();
        }
        return self::$factory;
    }

    var $sql = null;

    //inits db if first time and returns connection handle 
    private function __construct() {
        //Create connection
        if ($this->mysqli == null) {
            $this->mysqli = new mysqli(
                    Config::$host
                    , Config::$user
                    , Config::$password
                    , Config::$db);

            //Check connection
            if (mysqli_connect_errno($this->mysqli)) {
                echo "Error: Failed to connect to MySQL: " . mysqli_connect_error();
                echo "host" . Config::$host;
                die();
            }
        }
    }

    static function closeDbo() {
        mysqli_close(self::init());
    }

    public function loadObjectList($sql, $id = null) {

        $result = mysqli_query($this->mysqli, $sql);
        $sites = array();
        if ($result) {

            while ($row = mysqli_fetch_object($result)) {
                if($id==null){
                    $sites[] = $row;
                }else{
                    $sites[$row->{$id}] = $row;
                }
            }
        }


        return $sites;
    }

    private function escape($value) {
        return mysqli_real_escape_string($this->mysqli, $value);
    }

    public function loadObject($sql) {
        $sites = $this->loadObjectList($sql);
        if ($sites !== null && count($sites) > 0) {
            return $sites[0];
        } else {
            return null;
        }
    }

    public function loadResult($sql) {
        $result = mysqli_query($this->mysqli, $sql);
        if ($result) {
            $field = mysqli_fetch_field($result);
            $row = mysqli_fetch_assoc($result);
            $value = $row[$field->name];
        }
        return $value;
    }

    /**
     * performs the sql statement expecting the full insert stament. returns the id of  the insert
     * @param type $sql
     */
    public function insert($sql) {
        $this->sql = $sql; //preserving statement
        $this->mysqli->query($this->sql);
        $id = $this->mysqli->insert_id;
        return $id;
    }

    /**
     * simply execute a stament without a return value
     * @param type $sql
     */
    public function execute($sql) {
        $this->sql = $sql; //preserving statement
        $this->mysqli->query($this->sql);
    }
    
    public function insertObject($object, $table, $id = "id") {
        $vars = get_object_vars($object);
        $values = array();
        $cols = array();
        foreach ($vars as $key => $value) {
            if ($value !== null) {
                $cols[] = $key;
                $values[] = sprintf("'%s'", Database::escape($value));
            }
        }
        $v = implode(",", $values);
        $c = implode(",", $cols);
        $this->sql = sprintf("insert into %s (%s) values (%s)", $table, $c, $v);
        $this->mysqli->query($this->sql);
        $object->{$id} = $this->mysqli->insert_id;

        return $object;
    }

    public function updateObject($object, $table, $id = "id") {
        $vars = get_object_vars($object);

        $values = array();
        foreach ($vars as $key => $value) {
            if ($value !== null) {
                if ($key == $id) {
                    $id_value = $value;
                } else {
                    $values[] = sprintf("%s = '%s'", $key, Database::escape($value));
                }
            }
        }
        $v = implode(",", $values);

        $this->sql = sprintf("update %s set %s where %s = %s ", $table, $v, $id, $id_value);
        $this->mysqli->query($this->sql);
        $object->{$id} = $this->mysqli->insert_id;

        return $object;
    }

}
