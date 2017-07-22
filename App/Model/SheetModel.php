<?php
namespace App\Model;

use Lib\merge\FPDI as FPDI;
use App\Config\DataBase as DB;
use App\Model\GroupModel as Group;
use App\Model\PeriodModel as Period;
use App\Model\TeacherModel as Teacher;
use App\Model\NoveltyModel as Novelty;
use App\Model\InstitutionModel as Institution;
use App\Model\EvaluationPeriodModel as Evaluation;
use App\ModelPDF\EvaluationSheetPDF as EvaluationSheetPDF;
use App\ModelPDF\StudentAttendancePDF as StudentAttendance;
/**
* 
*/
class SheetModel extends DB
{
	
	public $_path = '';

	// Objectos a utilizar
	private $_group;
	private $_period;
	private $_teacher;
	private $_institution;
	private $_evaluation;
	private $_novelty;

	// 
	private $_pdi;
	private $_pdf;

	// 
	public $options = array();

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

			$this->_pdi = new FPDI();
			$this->_group = new Group($db);
			$this->_period = new Period($db);
			$this->_teacher = new Teacher($db);
			$this->_novelty = new Novelty($db);
			$this->_evaluation = new Evaluation($db);
			$this->_institution = new Institution($db);
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
	public function setOptions($options=array())
	{
		$this->options = $options;
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
	public function studentAttendanceSheet($id_asignature, $id_group)
	{	
		$respGroup = $this->_teacher->getInfoAsignatureAndGroup($id_asignature, $id_group);

		if($respGroup['state']):
			$this->_pdf = new StudentAttendance('landscape', 'mm', 'letter');

			$this->_pdf->institution = $this->_institution->getInfo()['data'][0];
			$this->_pdf->infoGroupAndAsig = $respGroup['data'][0];
			$this->_pdf->novelties = $this->_novelty->getByYear(Date('Y'))['data'];
			$this->_pdf->showData($this->_group->getClassRoomList($id_group)['data']);
			$this->_pdf->Output($this->_path.'lista-'.
				substr($this->_pdf->infoGroupAndAsig['doc_primer_nomb'],0,3)." ".
				substr($this->_pdf->infoGroupAndAsig['doc_segundo_nomb'],0,3)." ".
				substr($this->_pdf->infoGroupAndAsig['doc_primer_ape'],0,3)." ".
				substr($this->_pdf->infoGroupAndAsig['doc_segundo_ape'],0,3).
				$id_asignature.'-'.$id_group.'.pdf', 'F');
		endif;
	}

	public function evaluactionSheet($period, $id_asignature, $id_group)
	{
		$respGroup = $this->_teacher->getInfoAsignatureAndGroup($id_asignature, $id_group);

		if($respGroup['state']):
			$this->_pdf = new EvaluationSheetPDF(
				$this->options['orientation'],
				'mm', 
				$this->options['papper']
			);

			$resp = $this->_evaluation->getPeriods($period, $id_asignature, $id_group)['data'];

			$this->_pdf->maxPeriod = $period;
			$this->_pdf->institution = $this->_institution->getInfo()['data'][0];
			$this->_pdf->evaluation_parameters = $this->options['e_parameters'];
			$this->_pdf->infoGroupAndAsig = $respGroup['data'][0];
			$this->_pdf->novelties = $this->_novelty->getByYear(Date('Y'))['data'];
			$this->_pdf->AddPage();
			$this->_pdf->showData($resp);

			ob_clean();
			$this->_pdf->Output($this->_path.'l_e-'.
					substr($this->_pdf->infoGroupAndAsig['doc_primer_nomb'],0,3).
					substr($this->_pdf->infoGroupAndAsig['doc_segundo_nomb'],0,3).
					substr($this->_pdf->infoGroupAndAsig['doc_primer_ape'],0,3).
					substr($this->_pdf->infoGroupAndAsig['doc_segundo_ape'],0,3).
					$id_asignature.'-'.$id_group.'.pdf', 'F');
		endif;
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
				array_push($files, $archivo);
			}
		}

		asort($files);

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