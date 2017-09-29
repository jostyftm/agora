<?php

namespace App\Model;

use App\Config\DataBase as DB;

/**
* 
*/
class AsignatureModel extends DB
{
	private $table = 't_asignaturas';
	private $table_observation = 'observacion_asignatura';
	
	function __construct($db='')
	{	
		if(!$db)
			throw new \Exception("La clase ".get_class($this)." no encontro la base de datos", 1);
		else
			parent::__construct($db);
	}

	public function find($id_asignature)
	{
		$this->query = "SELECT * FROM {$this->table} WHERE id_asignatura='{$id_asignature}'";
		return $this->getResultsFromQuery();
	}

	/**
	*
	*
	*
	*/
	public function getObservationByStudent($id_student, $id_asignature, $period)
	{
		$this->query = "SELECT *
						FROM {$this->table_observation}
						WHERE id_estudiante={$id_student} AND id_asignatura={$id_asignature} AND periodo={$period}";

		return $this->getResultsFromQuery();
	}
}
?>