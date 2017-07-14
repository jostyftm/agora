<?php
namespace App\Model;

use Lib\merge\FPDI as FPDI;
use App\Config\DataBase as DB;
use App\Model\GroupModel as Group;
use App\Model\TeacherModel as Teacher;
use App\Model\InstitutionModel as Institution;
use App\ModelPDF\StudentAttendancePDF as StudentAttendance;
/**
* 
*/
class SheetModel extends DB
{
	
	public $_path = '';

	// Objectos a utilizar
	private $_group;
	private $_teacher;
	private $_institution;

	// 
	private $_pdi;
	private $_pdf;

	/**
	*
	*
	*/
	function __construct($db='')
	{	
		if(!$db)
			throw new \Exception("La clase ".get_class($this)." no encontro la base de datos", 1);
		else{
			parent::__construct($db);

			$this->_group = new Group($db);
			$this->_teacher = new Teacher($db);
			$this->_institution = new Institution($db);
			$this->_pdi = new FPDI();
		}
	}

	/**
	*
	*
	*/
	public function setPath($path)
	{	
		$this->_path = $path;
	}

	/**
	*
	*
	*/
	public function createSheet($id_asignature, $id_group, $type)
	{		
		if(method_exists($this, $type)):

			call_user_func_array([$this, $type], [$id_asignature, $id_group]);
		else:

		endif;
	}

	/**
	*
	*
	*/
	public function studentAttendance($id_asignature, $id_group)
	{	
		
		$this->_pdf = new StudentAttendance('landscape', 'mm', 'A4');
		$this->_pdf->institution = $this->_institution->getInfo()['data'][0];
		$this->_pdf->infoGroupAndAsig = $this->_teacher->getInfoAsignatureAndGroup($id_asignature, $id_group)['data'][0];
		$this->_pdf->showData($this->_group->getClassRoomList($id_group)['data']);
		$this->_pdf->Output($this->_path.'lista-'.$id_asignature.'-'.$id_group.'.pdf', 'F');
	}

	/**
	*
	*
	*/
	public function merge($orientation='p')
	{	
		
		$dir = opendir($this->_path);
		$files = array();
		while ($archivo = readdir($dir)) {
				
			if (!is_dir($archivo)){
				echo $archivo."<br />";
				array_push($files, $archivo);
			}
		}

		foreach ($files as $file) 
		{ 
			$pageCount = $this->_pdi->setSourceFile($this->_path.$file); 

			for ($i=1; $i <= $pageCount; $i++) { 
				
				$tpl = $this->_pdi->importPage($i);
				$this->_pdi->addPage($orientation); 

				$this->_pdi->useTemplate($tpl); 
			}
		}

		ob_clean();
		$this->_pdi->Output('I','merged.pdf');

		system('rm -rf ' . escapeshellarg($this->_path), $retval);
	}
}
?>