<?php

namespace App\Model;

use App\Config\DataBase as DB;
/**
* 
*/
class TeacherModel extends DB
{
	private $table = 'docentes';
	
	function __construct($db='')
	{	
		if(!$db)
			throw new \Exception("La clase ".get_class($this)." no encontro la base de datos", 1);
		else
			parent::__construct($db);
	}

	public function get($id)
	{
		$this->query = "SELECT * FROM {$this->table} WHERE id_docente={$id}";

		return $this->getResultsFromQuery();
	}

	/*
	 *	Funcion para obtener las asignturas y grupos que dictan un profesor
	 *
	 *	@param $id_teacher
	 *  @return query results
	*/
	public function getAsignaturesAndGroups($id_teacher)
	{

		$this->query = "SELECT g.id_grupo, g.nombre_grupo, a.id_asignatura, a.asignatura
						FROM t_asignaturas a
						INNER JOIN grupo_x_asig_x_doce ad ON a.id_asignatura=ad.id_asignatura
						INNER JOIN t_grupos g ON ad.id_grupo=g.id_grupo
						WHERE ad.id_docente = '{$id_teacher}' 
						ORDER BY a.asignatura";

		return $this->getResultsFromQuery();

	}

	/*
	 * Funcion que obtiene todos los grupos de un director de curso
	 *
	 * @param $id_teacher
	 * @return query results
	*/
	public function getGroupByDirector($id_teacher)
	{
		$this->query = "SELECT *
						FROM t_grupos g
						WHERE g.id_director_grupo = '{$id_teacher}' 
						ORDER BY g.nombre_grupo";

		return $this->getResultsFromQuery();
	}

	/*
	 *	Funcion que determina si un profesor es director de grupo
	 *
	 * @param $id_teacher
	 * @return boolean
	*/
	public function isDirector($id_teacher)
	{
		return $this->getGroupByDirector($id_teacher)['state'];
			
	}


	/*
	 *	Funcion que devuelve las observaciones generales
	 *
	 * @param $id_teacher
	 * @return result query
	*/
	public function getGeneralObservations($id_teacher)
	{

		$this->query = "SELECT ogp.id_observ_generales_periodo AS id_observacion, s.idstudents, s.primer_apellido AS p_a_alu, s.segundo_apellido AS s_a_alu, s.primer_nombre AS p_n_alu, s.segundo_nombre AS s_n_alu, ogp.id_periodo, ogp.observaciones,g.nombre_grupo 
						FROM observ_generales_periodo ogp 
						INNER JOIN students s ON ogp.id_estudiante=s.idstudents 
						INNER JOIN t_grupos g ON ogp.id_grupo=g.id_grupo 
						WHERE ogp.id_director_grupo={$id_teacher}
						ORDER BY p_n_alu";

		return $this->getResultsFromQuery();
	}

	/*
	 *	Funcion que devuelve las observaciones generales
	 *
	 * @param $id_teacher
	 * @return result query
	*/
	public function getGeneralReportPeriod($id_teacher)
	{
		$this->query = "SELECT s.idstudents, s.primer_apellido AS p_a_alu, s.segundo_apellido AS s_a_alu, s.primer_nombre AS p_n_alu, s.segundo_nombre AS s_n_alu, igp.id_periodo, igp.observaciones,g.nombre_grupo 
						FROM informe_general_periodo igp 
						INNER JOIN students s ON igp.id_estudiante=s.idstudents 
						INNER JOIN t_grupos g ON igp.id_grupo=g.id_grupo 
						WHERE igp.id_director_grupo={$id_teacher}
						ORDER BY p_n_alu";

		return $this->getResultsFromQuery();
	}





	public function getInfoAsignatureAndGroup($id_asignature, $id_group){
		$this->query = "SELECT CONCAT(d.primer_apellido,' ',d.segundo_apellido,' ',d.primer_nombre,' ',d.segundo_nombre) AS docente, CONCAT(dir.primer_apellido,' ', dir.segundo_apellido,' ',dir.segundo_nombre,' ',dir.primer_nombre,' ',dir.segundo_nombre) AS director_grupo, a.id_asignatura, a.asignatura, g.id_grupo, g.nombre_grupo, s.sede, j.jornada
						FROM docentes d
						INNER JOIN grupo_x_asig_x_doce ad ON d.id_docente=ad.id_docente
						INNER JOIN t_asignaturas a ON ad.id_asignatura=a.id_asignatura
						INNER JOIN t_grupos g ON ad.id_grupo=g.id_grupo
						INNER JOIN docentes dir ON g.id_director_grupo=dir.id_docente
						INNER JOIN sedes s ON g.id_sede=s.id_sede
						INNER JOIN jornadas j on g.jornada=j.id_jornada
						WHERE a.id_asignatura={$id_asignature} AND g.id_grupo={$id_group}";

		return $this->getResultsFromQuery();
	}
}
?>