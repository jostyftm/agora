<?php
namespace App\Model;

use App\Config\DataBase as DB;
/**
* 
*/
class GeneralReportPeriodModel extends DB
{
	
	private $table = 'informe_general_periodo';
	
	function __construct($db='')
	{	
		if(!$db)
			throw new \Exception("La clase ".get_class($this)." no encontro la base de datos", 1);
		else
			parent::__construct($db);
	}

	/*
	 *	Funcion que devuelve las observaciones generales
	 *
	 * @param $id_teacher
	 * @return result query
	*/
	public function find($id_report)
	{
		$this->query = "SELECT igp.id_informe_general_periodo AS id_reporte, s.idstudents, s.primer_apellido AS p_a_alu, s.segundo_apellido AS s_a_alu, s.primer_nombre AS p_n_alu, s.segundo_nombre AS s_n_alu, igp.id_periodo, igp.observaciones,g.nombre_grupo 
						FROM {$this->table} igp 
						INNER JOIN students s ON igp.id_estudiante=s.idstudents 
						INNER JOIN t_grupos g ON igp.id_grupo=g.id_grupo 
						WHERE igp.id_informe_general_periodo={$id_report}
						ORDER BY p_n_alu";

		return $this->getResultsFromQuery();
	}

	/*
	 *	Funcion que devuelve las observaciones generales
	 *
	 * @param $id_teacher
	 * @return result query
	*/
	public function save($data=array())
	{
		$this->query = "INSERT INTO {$this->table}
						(id_estudiante, id_grupo, id_periodo, id_director_grupo, observaciones)
						VALUES 
						({$data['id_student']}, {$data['id_group']}, {$data['id_period']}, {$data['id_group_director']},'{$data["observations"]}')";

		return $this->executeQuerySingle();
	}

	/*
	 *	Funcion que devuelve las observaciones generales
	 *
	 * @param $id_teacher
	 * @return result query
	*/
	public function update($data=array())
	{
		$this->query = "UPDATE {$this->table}
						SET observaciones = '{$data["observations"]}'
						WHERE id_informe_general_periodo={$data['id_report']}";

		return $this->executeQuerySingle();
	}

	/*
	 *	Funcion que devuelve las observaciones generales
	 *
	 * @param $id_teacher
	 * @return result query
	*/
	public function delete($id_report)
	{
		$this->query = "DELETE
						FROM {$this->table}
						WHERE id_informe_general_periodo={$id_report}";

		return $this->executeQuerySingle();
	}


	/*
	 *	Funcion que devuelve las observaciones generales
	 *
	 * @param $id_teacher
	 * @return result query
	*/
	public function getGeneralReportPeriodByTeacher($id_teacher)
	{
		$this->query = "SELECT igp.id_informe_general_periodo AS id_reporte, s.idstudents, s.primer_apellido AS p_a_alu, s.segundo_apellido AS s_a_alu, s.primer_nombre AS p_n_alu, s.segundo_nombre AS s_n_alu, igp.id_periodo, igp.observaciones,g.nombre_grupo 
						FROM {$this->table} igp 
						INNER JOIN students s ON igp.id_estudiante=s.idstudents 
						INNER JOIN t_grupos g ON igp.id_grupo=g.id_grupo 
						WHERE igp.id_director_grupo={$id_teacher}
						ORDER BY p_a_alu";

		return $this->getResultsFromQuery();
	}
}