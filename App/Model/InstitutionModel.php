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

		public function getGroups($id_sede)
		{
			$this->query = "SELECT g.id_grupo, g.nombre_grupo
							FROM t_grupos g
							WHERE g.id_sede = {$id_sede}";

			return $this->getResultsFromQuery();
		}
	}
?>