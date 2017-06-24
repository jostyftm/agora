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

		public function getInfo(){
			$this->query = "SELECT * FROM {$this->table}";

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
	}
?>