<?php
namespace App\Model;

use App\Config\DataBase as DB;
/**
* 
*/
class ReportPeriodModel extends DB
{
	private $table = 'informe_general_periodo';

	function __construct($db='')
	{
		if(!$db)
			throw new \Exception("La clase ".get_class($this)." no encontro la base de datos", 1);
		else
			parent::__construct($db);
	}

	public function getReportPeriodByStudent($id_student, $id_period)
	{
		$this->query = "SELECT * 
						FROM {$this->table} 
						WHERE id_estudiante = {$id_student} AND id_periodo = {$id_period}";
		
		return $this->getResultsFromQuery();
	}
}
?>