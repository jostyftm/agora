<?php
namespace App\Model;

use App\Config\DataBase as DB;

/**
* 
*/
class ValorationModel extends DB
{
	private $_table = 'valoracion';

	function __construct($db='')
	{
		if(!$db)
			throw new \Exception("La clase ".get_class($this)." no encontro la base de datos", 1);
		else
			parent::__construct($db);
	}

	public function all()
	{
		$this->query = "SELECT *
						FROM {$this->_table}";

		return $this->getResultsFromQuery();
	}

	public function find($valoration)
	{
		$this->query = "SELECT * 
						FROM {$this->_table}
						WHERE valoracion='{$valoration}' ";

		return $this->getResultsFromQuery();
	}
}
?>