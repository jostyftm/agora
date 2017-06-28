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
		$this->query = "SELECT e.idstudents, e.primer_apellido AS primer_ape_alu, e.segundo_apellido AS segundo_ape_alu, e.primer_nombre AS primer_nom_alu, e.segundo_nombre AS segundo_nom_alu
						FROM students e
						WHERE e.idstudents={$id_student}";

		return $this->getResultsFromQuery();	
	}
}
?>