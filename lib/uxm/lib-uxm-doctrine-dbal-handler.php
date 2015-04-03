<?php
// Db Interface for Silex / Doctrine Dbal
// author: Radu Ilies
// 2015-04-03
// License: BSD

namespace UXM;

class Db {

	private $connection;


	public function __construct(\Doctrine\DBAL\Connection $connection) {
		//--
		$this->connection = $connection;
		//--
	} //END FUNCTION


	public function readQuery($query, array $values=array()) {
		//--
		if(!is_array($values)) {
			throw new \Exception('ERROR: '.get_class($this).'->'.__FUNCTION__.'() expects array for parameters');
		} //end if
		//--
		$query = $this->connection->executeQuery($query, $values);
		//--
		return (array) $query->fetchAll();
		//--
	} //END FUNCTION


	public function countQuery($query, array $values=array()) {
		//--
		if(!is_array($values)) {
			throw new \Exception('ERROR: '.get_class($this).'->'.__FUNCTION__.'() expects array for parameters');
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


	public function writeQuery($query, array $values=array()) {
		//--
		if(!is_array($values)) {
			throw new \Exception('ERROR: '.get_class($this).'->'.__FUNCTION__.'() expects array for parameters');
		} //end if
		//--
		$query = $this->connection->executeQuery($query, $values);
		//--
		return (int) $query->rowCount();
		//--
	} //END FUNCTION


} //END CLASS

?>