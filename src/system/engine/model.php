<?php
abstract class Model {
	protected $registry;
	
	public function __construct($registry) {
		$this->registry = $registry;
	}
	
	public function __get($key) {
		return $this->registry->get($key);
	}
	
	public function __set($key, $value) {
		$this->registry->set($key, $value);
	}

    public function getAllEnum($table, $column) {
        $query = $this->db->query("SELECT COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '" . DB_DATABASE . "' AND TABLE_NAME = '" . $table . "' AND COLUMN_NAME = '" . $column . "'" );
        
        $value = $query->row['COLUMN_TYPE'];
        
        $value = substr($value, 5); //skip enum(
        $pos = strrpos($value, ')');
        $value = substr($value, 0, $pos);
        
        $enums =  explode(",", $value);
        $enums2 = array();
        foreach($enums as $enum) {
            $enums2 []= substr($enum, 1, strlen($enum)-2);
        }
        
        return $enums2;
    }
}

?>