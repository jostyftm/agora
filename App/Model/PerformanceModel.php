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

	public function getPerformanceIndicators($id_performance)
	{
		$this->query = "SELECT *
						FROM new_indicadores_desempeno
						WHERE id_parametro_evaluacion={$id_performance} ";

		return $this->getResultsFromQuery();
	}


	public function getEvaluationParameters()
	{
		$this->query = "SELECT * FROM new_parametro_evaluacion";

		return $this->getResultsFromQuery();
	}

	public function getPerformanceToPeriod($id_grade, $id_asignature, $period)
	{
		$this->query = "SELECT *
						FROM desempeno
						WHERE id_grado={$id_grade} AND id_asignatura={$id_asignature} AND periodos={$period}";
						
		return $this->getResultsFromQuery();
	}

	public function getAll()
	{
		$this->query = "SELECT * 
						FROM desempeno";

		return $this->getResultsFromQuery();
	}

	public function getPerformanceByGroup($id_group, $period)
	{
		$this->query = "SELECT DISTINCT d.*, dp.posicion, dp.id_grupo
						FROM rel_desemp_posicion dp
						INNER JOIN desempeno d ON dp.cod_desemp=d.codigo
						WHERE dp.id_grupo={$id_group} AND d.periodos ={$period}
						ORDER BY d.codigo";

		return $this->getResultsFromQuery();
	}
}
?>