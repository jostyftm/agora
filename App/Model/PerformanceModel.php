<?php

namespace App\Model;

use App\Config\DataBase as DB;

/**
* 
*/
class PerformanceModel extends DB
{
	
	function __construct($db='')
	{	
		if(!$db)
			throw new \Exception("La clase ".get_class($this)." no encontro la base de datos", 1);
		else
			parent::__construct($db);
	}

	public function getPerformanceIndicadors($id_performance)
	{
		$this->query = "SELECT *
						FROM new_indicadores_desempeno
						WHERE id_parametro_evaluacion={$id_performance} ";

		return $this->getResultsFromQuery();
	}

	public function getPerformanceToPeriod($id_grade, $id_asignature, $period)
	{
		$this->query = "SELECT *
						FROM desempeno
						WHERE id_grado={$id_grade} AND id_asignatura={$id_asignature} AND periodos={$period}";
						
		return $this->getResultsFromQuery();
	}
}
?>