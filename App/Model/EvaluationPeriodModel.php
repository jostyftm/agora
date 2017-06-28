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

	public function getGradeBookBySudent($id_student, $id_grade, $periods=array())
	{
		$this->query = "SELECT DISTINCT asi.asignatura, e.idstudents, CONCAT(e.primer_apellido,' ',e.segundo_apellido,' ',e.primer_nombre) AS estudiante, asi.id_asignatura, ar.area, aa.int_horaria AS ihs";

		foreach ($periods as $key => $value) {
			$this->query .= ", ev.inasistencia_p".($key+1).", p".($key+1).".peso AS periodo_".($key+1)."_peso, ev.".$value." periodo".($key+1)." ";
		}

		$this->query .= "FROM students e 
						INNER JOIN t_evaluacion ev ON e.idstudents=ev.id_estudiante 
						INNER JOIN t_asignaturas asi ON asi.id_asignatura=ev.id_asignatura 
						INNER JOIN t_area ar ON ar.id_area=ev.id_area
						INNER JOIN t_asignatura_x_area aa ON aa.id_asignatura=asi.id_asignatura AND aa.id_area=ar.id_area";

		foreach ($periods as $key => $value) { 
		
		$this->query .= "
						INNER JOIN periodos p".($key+1)." ON p".($key+1).".periodos=".($key+1)." ";
		}

		$this->query .= "WHERE e.idstudents={$id_student} AND aa.id_grado = {$id_grade}
						ORDER BY ar.order_area";
		

		// return $this->query;
		$data = $this->getResultsFromQuery()['data'];

		$calculoAreas = $this->filterBestResultsByGrade($id_student, $id_grade)['data'];
		
		$areas =  $this->resolveAreas($data);

		return array_merge(
			array(
				'data' => $data,
				'areas'	=> $areas,
				'calAreas'	=>	$calculoAreas
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

	// 
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
		
		return $areas;
	}

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
	// 
	public function filterBestResultsByGrade($id_student, $id_grade)
	{
		$this->query = "
						SELECT 
t_evaluacion.id_estudiante, t_evaluacion.primer_apellido, t_evaluacion.primer_nombre, t_evaluacion.segundo_nombre, t_evaluacion.id_area,t_evaluacion.id_asignatura
, t_evaluacion.id_grado, t_evaluacion.id_grupo, t_asignatura_x_area.peso_frente_area as Peso,
 (SELECT t_area.area FROM t_area WHERE t_area.id_area = t_evaluacion.id_area) as Area
,IF((t_asignatura_x_area.peso_frente_area > 0)
, ROUND(SUM(t_evaluacion.eval_1_per * (t_asignatura_x_area.peso_frente_area / 100)),1), 
ROUND((sum(t_evaluacion.eval_1_per /(SELECT count(DISTINCT t_asignatura_x_area.id_asignatura) FROM t_asignatura_x_area where t_asignatura_x_area.id_area = t_evaluacion.id_area  and t_evaluacion.id_grado = t_asignatura_x_area.id_grado) )),2)) as Valoracion
FROM t_evaluacion
INNER JOIN t_asignatura_x_area ON t_asignatura_x_area.id_area  = t_evaluacion.id_area
and t_asignatura_x_area.id_asignatura = t_evaluacion.id_asignatura AND
t_evaluacion.id_grado = t_asignatura_x_area.id_grado and t_evaluacion.id_grado = {$id_grade} and t_evaluacion.id_estudiante={$id_student} GROUP BY t_evaluacion.id_estudiante, t_evaluacion.id_area ORDER BY t_evaluacion.primer_apellido, t_evaluacion.primer_nombre DESC;
		";

		return $this->getResultsFromQuery();
	}

	public function orderBestPerformancesByGroup($id_grade='', $id_group='')
	{
		$this->query = "
		SELECT  t_evaluacion.primer_apellido, t_evaluacion.primer_nombre,  
		sum(t_evaluacion.eval_1_per >= (SELECT minimo from valoracion WHERE valoracion = 'Superior') and t_evaluacion.eval_1_per <=(SELECT maximo from valoracion WHERE valoracion = 'Superior'))   as S ,
		sum(t_evaluacion.eval_1_per >=  (SELECT minimo from valoracion WHERE valoracion = 'Alto') and t_evaluacion.eval_1_per <= (SELECT maximo from valoracion WHERE valoracion = 'Alto')) as A ,
		sum(t_evaluacion.eval_1_per >= (SELECT minimo from valoracion WHERE valoracion = 'Basico') and t_evaluacion.eval_1_per <=(SELECT maximo from valoracion WHERE valoracion = 'Basico')) as B , 
		sum(t_evaluacion.eval_1_per <= (SELECT maximo from valoracion WHERE valoracion = 'Bajo') ) as V ,
		count(t_evaluacion.eval_1_per>0) as TAV,  
		ROUND(((SUM(t_evaluacion.eval_1_per)) / count(t_evaluacion.eval_1_per)),1) as Promedio,
		(SELECT valoracion.val FROM valoracion WHERE 
		ROUND(((SUM(t_evaluacion.eval_1_per)) / count(t_evaluacion.eval_1_per)),1)
		BETWEEN   valoracion.minimo AND  valoracion.maximo) as Desempeño
		FROM t_evaluacion
		WHERE t_evaluacion.id_estudiante IN
		(SELECT DISTINCT
		t_evaluacion.id_estudiante
		FROM t_evaluacion
		INNER JOIN t_asignatura_x_area ON t_asignatura_x_area.id_area  = t_evaluacion.id_area
		and t_asignatura_x_area.id_asignatura = t_evaluacion.id_asignatura 
		INNER JOIN t_grados ON t_evaluacion.id_grado = t_grados.id_grado and t_grados.id_grado = 15 and t_evaluacion.id_grupo = 19)
		GROUP BY t_evaluacion.id_estudiante ORDER BY Promedio DESC;	
		";
	}
}
?>