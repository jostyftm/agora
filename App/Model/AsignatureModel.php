<?php

namespace App\Model;

use App\Config\DataBase as DB;

/**
* 
*/
class AsignatureModel extends DB
{
	private $table = 't_asignaturas';
	
	function __construct($db='')
	{	
		if(!$db)
			throw new \Exception("La clase ".get_class($this)." no encontro la base de datos", 1);
		else
			parent::__construct($db);
	}

	public function getInfo($id_asignature)
	{
		$this->query = "SELECT * FROM {$this->table} WHERE id_asignatura='{$id_asignature}'";
		return $this->getResultsFromQuery();
	}
}
?>