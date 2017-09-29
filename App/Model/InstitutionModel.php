<?php
	
namespace App\Model;

use App\Config\DataBase as DB;
/**
* 
*/
class InstitutionModel extends DB
{
	
	private $table = 'datos_institucion';

	function __construct($db='')
	{
		if(!$db)
			throw new \Exception("La clase ".get_class($this)." no encontro la base de datos", 1);
		else
			parent::__construct($db);
	}

	public function getInfo()
	{
		$this->query = "SELECT * FROM {$this->table}";

		return $this->getResultsFromQuery();
	}

	public function getSedes()
	{
		$this->query = "SELECT * FROM sedes";

		return $this->getResultsFromQuery();
	}

	public function getJourneys()
	{
		$this->query = "SELECT *
						FROM jornadas";

		return $this->getResultsFromQuery();
	}

	public function getGroups($id_sede)
	{
		$this->query = "SELECT g.id_grupo, g.nombre_grupo
						FROM t_grupos g
						WHERE g.id_sede = {$id_sede}";

		return $this->getResultsFromQuery();
	}

	public function getGrades()
	{
		$this->query = "SELECT *
						FROM t_grados";

		return $this->getResultsFromQuery();
	}

	public function getPeriods()
	{
		$this->query = "SELECT *
						FROM periodos p
						WHERE p.peso > 0";

		return $this->getResultsFromQuery();
	}

	public function getDocentes($id_sede)
	{
		$this->query = "SELECT DISTINCTROW d.id_docente, d.primer_apellido, d.segundo_apellido, d.primer_nombre, d.segundo_nombre, g.id_sede
						FROM docentes d
						INNER JOIN grupo_x_asig_x_doce asig ON asig.id_docente=d.id_docente
						INNER JOIN t_grupos g ON asig.id_grupo=g.id_grupo AND g.id_sede = {$id_sede}
						ORDER BY d.primer_apellido";
						
		return $this->getResultsFromQuery();
	}
}
?>