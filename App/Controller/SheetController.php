<?php
namespace App\Controller;

use App\Model\SheetModel as Sheet;
/**
* 
*/
class SheetController
{
	private $_sheet;

	/**
	*
	*
	*/
	function __construct()
	{
		$this->_sheet = new Sheet(DB);
	}

	/**
	* @author
	* @param
	* @return
	*/
	public function attendanceAction()
	{
		// Preguntamos si el array POST NO esta vacio
		if(!empty($_POST) && isset($_POST['groups'])):

			$path = './'.time().'/';

			$this->_sheet->setPath($path);

			if(!file_exists($path))
				mkdir($path);

			foreach($_POST['groups'] as $key => $group):
				
				$id_asignature = split('-', $group)[0];
				$id_group = split('-', $group)[1];

				$this->_sheet->createSheet($id_asignature, $id_group, 'studentAttendance');
			endforeach;

			// 
			$this->_sheet->merge('l');
		else:

			echo "Vacio";
		endif;
	}
}
?>