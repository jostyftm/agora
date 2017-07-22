<?php
namespace App\Model;

use App\Config\DataBase as DB;
/**
* 
*/
class NoveltyModel extends DB
{
	
	private $_table = 'novedades';

	function __construct($db='')
	{	
		if(!$db)
			throw new \Exception("La clase ".get_class($this)." no encontro la base de datos", 1);
		else
			parent::__construct($db);
	}

	/**
	 *
	 *
	 *
	*/
	public function getByYear($year)
	{
		$this->query = "SELECT n.*, ne.idstudents, ne.fecha
						FROM novedades_x_estudiante_fecha ne
						INNER JOIN {$this->_table} n ON ne.id_novedad=n.id_novedad
						WHERE YEAR(ne.fecha) = {$year}";
		return $this->getResultsFromQuery();
	}


}
?>