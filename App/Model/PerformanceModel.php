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

	public function getPerformanceByGroup($id_group)
	{
		$this->query = "SELECT DISTINCT d.*, dp.posicion, dp.id_grupo, dp.periodo, dp.id_asign AS id_asignatura
						FROM rel_desemp_posicion dp
						INNER JOIN desempeno d ON dp.cod_desemp=d.codigo
						WHERE dp.id_grupo={$id_group}
						ORDER BY dp.posicion";

		return $this->getResultsFromQuery();
	}

	/**

	**/ 

	public function getEvaluationParametersAndIndicators()
	{
		$response = array();

		$eParameters = $this->getEvaluationParameters();

		if($eParameters['state']):

			foreach($eParameters['data'] as $key => $parameter):

				array_push(
					$response, 
					array(
						'id_parametro'	=>	$parameter['id_parametro_evaluacion'],
						'parametro'		=>	utf8_encode(
							$parameter['parametro']
						),
						'prefix'		=>	$parameter['prefix'],
						'peso'			=>	$parameter['peso'],
						'indicadores'	=>	$this->resolveIndicators(
							$parameter['id_parametro_evaluacion']
						)
					)
				);
			endforeach;

		endif;

		return $response;
	}

	/**
	*
	*
	*/
	private function resolveIndicators($id_parameter)
	{
		$response = array();

		$indicators = $this->getPerformanceIndicators($id_parameter);
		
		if($indicators['state']):

			foreach($indicators['data'] as $key => $indicator):

				$percentage = (isset($indicator['porcentaje'])) ? $indicator['porcentaje'] : 0 ;

				if($indicator['id_parametro_evaluacion'] == $id_parameter):

					array_push(
						$response, 
						array(
							'id'	=>	$indicator['id_indicadores'],
							'indicator'	=>	utf8_encode($indicator['indicador']),
							'abbreviation'	=>	$indicator['abreviacion'],
							'percentage'	=>	$percentage
						)
					);
				endif;

			endforeach;
		else:

			for($i=0; $i < 5; $i++){
				array_push(
					$response, 
					array(
						'id'=>0,
						'indicator'=>'',
						'abbreviation'=>'',
						'percentage'=> 0
					)
				);
			}
		endif;

		return $response;
	}
}
?>