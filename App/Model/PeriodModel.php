<?php
namespace App\Model;

use App\Config\DataBase as DB;
/**
* 
*/
class PeriodModel extends DB
{
	private $table = "periodos";

	function __construct($db='')
	{
		if(!$db)
			throw new \Exception("La clase ".get_class($this)." no encontro la base de datos", 1);
		else
			parent::__construct($db);
	}

	public function all()
	{
		$this->query = "SELECT * FROM {$this->table}";

		return $this->getResultsFromQuery();
	}
}
?>