<?php

namespace App\Model;

use App\Config\DataBase as DB;
/**
* 
*/
class GroupModel extends DB
{
	
	private $table = 't_grupos';

	function __construct($db='')
	{
		if(!$db)
			throw new \Exception("La clase ".get_class($this)." no encontro la base de datos", 1);
		else
			parent::__construct($db);
	}

	public function getInfo($id_group){
		$this->query = "SELECT g.id_grupo, g.nombre_grupo, CONCAT(d.primer_apellido,' ', d.segundo_apellido,' ',d.segundo_nombre,' ',d.primer_nombre,' ',d.segundo_nombre) AS director_grupo, j.jornada, s.sede
						FROM docentes d
						INNER JOIN t_grupos g ON g.id_director_grupo=d.id_docente AND g.id_grupo={$id_group}
						INNER JOIN jornadas j on g.jornada=j.id_jornada
						INNER JOIN sedes s ON g.id_sede=s.id_sede";

		return $this->getResultsFromQuery();
	}

	public function getClassRoomList($id_group){
		$this->query = "SELECT CONCAT(e.primer_nombre,' ',e.segundo_nombre,' ',e.primer_apellido,' ',e.segundo_apellido) AS estudiante, e.estatus 
						FROM t_estudiante_grupo eg 
						INNER JOIN students e ON eg.idstudent=e.idstudents AND eg.id_grupo ={$id_group} 
						ORDER BY e.primer_apellido";

		return $this->getResultsFromQuery();
	}
}
?>