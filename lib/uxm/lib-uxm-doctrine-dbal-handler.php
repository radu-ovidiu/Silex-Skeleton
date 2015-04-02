<?php
// Db Interface for Silex / Doctrine Dbal
// author: Radu Ilies
// 2015-02-21
// License: BSD

namespace UXM;

class Db {

	private $connection;


	public function __construct($connection) {
		//--
		$this->connection = $connection;
		//--
	} //END FUNCTION


	public function readQuery($query, $values='') {
		//--
		if(!is_array($values)) {
			$values = array();
		} //end if
		//--
		$query = $this->connection->executeQuery($query, $values);
		//--
		return (array) $query->fetchAll();
		//--
	} //END FUNCTION


	public function countQuery($query, $values='') {
		//--
		if(!is_array($values)) {
			$values = array();
		} //end if
		//--
		$query = $this->connection->executeQuery($query, $values);
		$arr = (array) $query->fetchAll();
		//--
		$count = 0;
		//--
		if(is_array($arr[0])) {
			foreach($arr[0] as $key => $val) {
				$count = (int) $val; // find first row and first column value
				break;
			} //end if
		} //end if
		//--
		return (int) $count;
		//--
	} //END FUNCTION


	public function writeQuery($query, $values='') {
		//--
		if(!is_array($values)) {
			$values = array();
		} //end if
		//--
		$query = $this->connection->executeQuery($query, $values);
		//--
		return (int) $query->rowCount();
		//--
	} //END FUNCTION


} //END CLASS

?>