<?php

namespace App\Model;

use App\Config\DataBase as DB;

/**
* 
*/
class EvaluationPeriodModel extends DB
{
	protected $table = 't_evaluacion';

	private $_periods = array();


	function __construct($db='')
	{
		if(!$db)
			throw new \Exception("La clase ".get_class($this)." no encontro la base de datos", 1);
		else
			parent::__construct($db);
	}

	public function getPeriodWithOutEvaluating($id_group, $id_asignature)
	{
		$this->query = "SELECT e.*, a.asignatura, g.nombre_grupo 
						FROM {$this->table} e
						INNER JOIN t_grupos g ON g.id_grupo= '{$id_group}' AND e.id_grupo=g.id_grupo
						INNER JOIN t_asignaturas a ON a.id_asignatura= '{$id_asignature}' AND e.id_asignatura=a.id_asignatura
						WHERE e.eval_1_per IS NULL OR e.eval_2_per IS NULL OR e.eval_3_per IS NULL OR e.eval_4_per IS NULL
						ORDER BY e.primer_apellido";

		return $this->getResultsFromQuery();
	}

	public function getPeriodsWithOutEvaluating($column, $id_asignature, $id_group){
		$this->query = "SELECT e.$column AS periodo, e.id_estudiante, CONCAT(e.primer_apellido,' ',e.segundo_apellido,' ',e.primer_nombre,' ',e.segundo_nombre) AS estudiante, a.id_asignatura, a.asignatura, g.nombre_grupo, g.id_grupo 
						FROM {$this->table} e 
						INNER JOIN t_grupos g ON g.id_grupo={$id_group} AND e.id_grupo=g.id_grupo 
						INNER JOIN t_asignaturas a ON a.id_asignatura={$id_asignature} AND e.id_asignatura=a.id_asignatura 
						WHERE e.$column IS NULL OR e.$column = 0 
						ORDER BY e.primer_apellido";

		return $this->getResultsFromQuery();
	}

	public function updatePeriod($period,$id_student, $id_asignature, $value){
			$this->query = "UPDATE {$this->table}
							SET {$period}={$value}
							WHERE id_estudiante={$id_student} AND id_asignatura={$id_asignature}";

			return $this->execute_single_query();
	}

	public function getPeriods($maxPeriod, $id_asignature, $id_group)
	{
		$this->query = "SELECT CONCAT(e.primer_apellido,' ', e.segundo_apellido,' ', e.primer_nombre,' ', e.segundo_nombre) AS estudiante, CONCAT(d.primer_apellido,' ', d.segundo_apellido,' ', d.primer_nombre,' ', d.segundo_apellido) AS director_grupo, g.id_grupo, g.nombre_grupo, a.id_asignatura, a.asignatura, e.novedad, e.estatus";

		for ($i=0; $i < $maxPeriod; $i++) { 
			$this->query .= ", e.eval_".($i+1)."_per periodo".($i+1)." ";
		}

		$this->query .= " FROM t_evaluacion e
						INNER JOIN t_grupos g ON e.id_grupo=g.id_grupo AND g.id_grupo={$id_group}
						INNER JOIN t_asignaturas a ON e.id_asignatura=a.id_asignatura AND a.id_asignatura={$id_asignature}
						INNER JOIN docentes d ON g.id_director_grupo=d.id_docente
						ORDER BY e.primer_apellido";

		return $this->getResultsFromQuery();
	}

	public function getGradeBookBySudent($id_student, $periods=array())
	{
		$this->query = "SELECT e.idstudents, CONCAT(e.primer_apellido,' ',e.segundo_apellido,' ',e.primer_nombre,' ',e.segundo_nombre) AS estudiante, asi.id_asignatura, asi.asignatura, ar.area, ev.ihs";

		foreach ($periods as $key => $value) {
			$this->query .= ", ev.inasistencia_p".($key+1).", p".($key+1).".peso AS periodo_".($key+1)."_peso, ev.".$value." periodo".($key+1)." ";
		}

		$this->query .= "FROM students e 
						INNER JOIN t_evaluacion ev ON e.idstudents=ev.id_estudiante 
						INNER JOIN t_asignaturas asi ON asi.id_asignatura=ev.id_asignatura 
						INNER JOIN t_area ar ON ar.id_area=ev.id_area";

		foreach ($periods as $key => $value) { 
		
		$this->query .= "
						INNER JOIN periodos p".($key+1)." ON p".($key+1).".periodos=".($key+1)." ";
		}

		$this->query .= "WHERE e.idstudents={$id_student}
						ORDER BY ar.area";
		
		$data = $this->getResultsFromQuery()['data'];

		
		$areas =  $this->resolveAreas($data);

		return array_merge(
			array(
				'data' => $data,
				'areas'	=> $areas
			)
		);
	}

	// 
	public function getValoration()
	{
		$this->query = "SELECT *
						FROM valoracion";

		return $this->getResultsFromQuery();
	}

	// Funciones que no son de consultas SQL
	private function resolveAreas($data=array())
	{	
		$areas = array();

		foreach($data as $key => $value)
		{
			if(!$this->esta($areas, utf8_encode($value['area'])))
			{
				array_push($areas, utf8_encode($value['area']));
			}
		}

		// $areasFirst = array();
		// $areasLast = array();
		// foreach ($areas as $clave => $valor) {
		// 	$area = array_shift($areas);

		// 	if(
		// 		strstr($areas, utf8_encode('AMBIENTAL')) 	||
		// 		strstr($areas, utf8_encode('SOCIALES'))		||
		// 		strstr($areas, utf8_encode('CULTURAL'))		||
		// 		strstr($areas, utf8_encode('ETICA'))		||
		// 		strstr($areas, utf8_encode('DEPORTES'))		||
		// 		strstr($areas, utf8_encode('RELIGIOSA'))	||
		// 		strstr($areas, utf8_encode('HUMANIDADES'))	||
		// 		strstr($areas, utf8_encode('MATEMÁTICAS'))	||
		// 		strstr($areas, utf8_encode('INFORMÁTICA'))
		// 	)
		// 	{
		// 		echo $areas." first";
		// 		// array_push($areasFirst, $area);
		// 	}else{
		// 		echo $areas." last";
		// 		// array_push($areasLast, $area);
		// 	}
		// 	echo "<br />";
		// }

		return $areas;
	}

	// 
	private function esta($data=array(), $info)
	{

		if(empty($data))
		{
			return false;
		}

		foreach ($data as $key => $value) 
		{
			
			if($value == $info)
			{
				return true;
			}
		}

		return false;

	}



}
?>