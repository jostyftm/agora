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

		$this->execute_single_query();

		if($this->isError()){
			throw new \Exception("Error ".$this->getErrorMessage());

		}else if($this->results->num_rows > 0){

			$this->get_result_query();
				
			return array(
				'message' 	=> 'Consulta exitosa',
				'state'		=>	true,
				'data'		=>	$this->rows
			);

		}else{
			return array(
				'message' 	=> 'no hay resultados',
				'state'		=>	false,
				'data'		=> array()
			);
		}
	}

	public function getAsignaturesAndGroups($id_teacher)
	{

		$this->query = "SELECT g.id_grupo, g.nombre_grupo, a.id_asignatura, a.asignatura
						FROM t_asignaturas a
						INNER JOIN grupo_x_asig_x_doce ad ON a.id_asignatura=ad.id_asignatura
						INNER JOIN t_grupos g ON ad.id_grupo=g.id_grupo
						WHERE ad.id_docente = '{$id_teacher}' ";

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