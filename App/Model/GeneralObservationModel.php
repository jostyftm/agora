<?php
namespace App\Model;

use App\Config\DataBase as DB;
/**
* 
*/
class GeneralObservationModel extends DB
{
	
	private $table = 'observ_generales_periodo';
	
	function __construct($db='')
	{	
		if(!$db)
			throw new \Exception("La clase ".get_class($this)." no encontro la base de datos", 1);
		else
			parent::__construct($db);
	}

	/*
	 *
	*/
	public function find($id_observation)
	{
		$this->query = "SELECT ogp.id_observ_generales_periodo AS id_observacion, s.idstudents, s.primer_apellido AS p_a_alu, s.segundo_apellido AS s_a_alu, s.primer_nombre AS p_n_alu, s.segundo_nombre AS s_n_alu, ogp.id_periodo, ogp.observaciones,g.nombre_grupo 
						FROM {$this->table} ogp 
						INNER JOIN students s ON ogp.id_estudiante=s.idstudents 
						INNER JOIN t_grupos g ON ogp.id_grupo=g.id_grupo 
						WHERE ogp.id_observ_generales_periodo={$id_observation}
						ORDER BY p_n_alu";

		return $this->getResultsFromQuery();
	}

	/*
	 *
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
	 *
	*/
	public function update($data=array())
	{
		$this->query = "UPDATE {$this->table}
						SET observaciones = '{$data["observations"]}'
						WHERE id_observ_generales_periodo={$data['id_observation']}";

		return $this->executeQuerySingle();
	}

	/*
	 *
	*/
	public function delete($id_observation)
	{
		$this->query = "DELETE
						FROM {$this->table}
						WHERE id_observ_generales_periodo={$id_observation}";

		return $this->executeQuerySingle();
	}
}
?>