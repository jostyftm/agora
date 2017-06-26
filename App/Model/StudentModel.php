<?php

namespace App\Model;

use App\Config\DataBase as DB;
/**
* 
*/
class StudentModel extends DB
{
	
	function __construct($db='')
	{
		if(!$db)
		{
			throw new \Exception("La clase ".get_class($this)." no encontro la base de datos", 1);
		}
		else
		{
			parent::__construct($db);
		}
		
	}

	public function getStudent($id_student)
	{
		$this->query = "SELECT e.idstudents, CONCAT	(e.primer_apellido,' ',e.segundo_apellido,' ',e.primer_nombre,' ',e.segundo_nombre) AS estudiante
						FROM students e
						WHERE e.idstudents={$id_student}";

		return $this->getResultsFromQuery();	
	}
}
?>