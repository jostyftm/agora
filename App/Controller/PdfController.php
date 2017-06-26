<?php

namespace App\Controller;

use Lib\merge\FPDI as FPDI;
use App\Model\GroupModel as Group;
use App\Model\TeacherModel as Teacher;
use App\Model\StudentModel as Student;
use App\Model\InstitutionModel as Institution;
use App\Model\PerformanceModel as Performance;
use App\ModelPDF\GradeBookPDF	as GradeBookPDF;
use App\Model\EvaluationPeriodModel as Evaluation;
use App\ModelPDF\PlanillaAsistencia as PlanillaAsistencia;
use App\ModelPDF\EvaluationSheetPDF as EvaluationSheetPDF;
/**
* 
*/
class PdfController
{
	
	function __construct()
	{
		# code...
	}

	public function indexAction(){

	}

	// Planilla para la lista de asistencia
	public function studentAttendanceAction($id_asignature, $id_group){

		$group = new Group(DB);
		$teacher = new Teacher(DB);
		$institution = new Institution(DB);
		$pdf = new PlanillaAsistencia('landscape', 'mm', 'A4');

		$infoIns = $institution->getInfo()['data'][0];
		$infoAsignatureAndGroup = $teacher->getInfoAsignatureAndGroup($id_asignature, $id_group)['data'][0];
		$classRoomList = $group->getClassRoomList($id_group)['data'];

		// print_r($classRoomList);
		$pdf->institution = $infoIns;
		$pdf->infoGroupAndAsig = $infoAsignatureAndGroup;
		$pdf->SetMargins(3, 3, 3);
		$pdf->AddPage();
		$pdf->showData($classRoomList);
		$pdf->SetFont('Arial','B',16);
		$pdf->Output('pdf/lista-'.$pdf->infoGroupAndAsig['nombre_grupo'].'.pdf', 'I');

	}

	public function evaluationSheetAction(
		$db='', 
		$model, 
		$maxPeriod, 
		$id_asignature, 
		$id_group
	){
		$teacher = new Teacher(DB);
		$evaluation = new Evaluation(DB);
		$performance = new Performance(DB);
		$institution = new Institution(DB);
		$pdf = new EvaluationSheetPDF('landscape', 'mm', 'A4');

		$ind_DP = $performance->getPerformanceIndicadors(2)['data'];
		$ind_DS = $performance->getPerformanceIndicadors(3)['data'];
		
		$infoIns = $institution->getInfo()['data'][0];
		$infoAsignatureAndGroup = $teacher->getInfoAsignatureAndGroup($id_asignature, $id_group)['data'][0];

		$resp = $evaluation->getPeriods(
					split('_', $maxPeriod)[1],
					$id_asignature,
					$id_group
				)['data'];

		$pdf->model = $model;
		$pdf->maxPeriod = split('_', $maxPeriod)[1];
		$pdf->institution = $infoIns;
		$pdf->infoGroupAndAsig = $infoAsignatureAndGroup;
		$pdf->SetMargins(3, 3, 3);
		$pdf->AddPage();
		$pdf->showData($resp);
		$pdf->SetFont('Arial','B',16);
		$pdf->Output('pdf/lista-'.$pdf->infoGroupAndAsig['nombre_grupo'].'.pdf', 'I');
	}

	public function generateGradeBookByStudentAction()
	{	
		$group = new Group(DB);
		$student = new Student(DB);
		$evaluation = new Evaluation(DB);
		$performance = new Performance(DB);
		$institution = new Institution(DB);
		
		$infoIns = $institution->getInfo()['data'][0];
		$performances = $performance->getAll()['data'];
		$periods = $institution->getPeriods()['data'];
		$valoration = $evaluation->getValoration()['data'];
		$path = './'.$infoIns['nit'];

		if(isset($_POST['btn_p_superacion']))
		{

			foreach ($_POST['students'] as $key => $value) 
			{
				$pdf = new GradeBookPDF('P', 'mm', 'A4');
				$gradeBook = $evaluation->getGradeBookBySudent($value, $_POST['periodo']);
				
				$infoGroup = $group->getInfo($_POST['grupo'])['data'][0];
				$infoStudent = $student->getStudent($value)['data'][0];
				
				$pdf->periods = $periods;
				$pdf->institution = $infoIns;
				$pdf->valoration = $valoration;
				$pdf->infoStudent = $infoStudent;
				$pdf->areas = $gradeBook['areas'];
				$pdf->infoGroupAndAsig = $infoGroup;
				$pdf->gradeBook = $gradeBook['data'];
				$pdf->performancesData = $performances;
				$pdf->ImpDobleCara = (isset($_POST['debleCara'])) ? true : false;
				$pdf->Impescala = (isset($_POST['escalaVAlorativa'])) ? true : false;
				$pdf->AreasDisable = (isset($_POST['areasDisabled'])) ? true : false;

				$pdf->SetMargins(3, 3, 3);
				$pdf->createGradeBook();
				$pdf->SetFont('Arial','B',16);
				$pdf->Output($path.'/'.$infoStudent['idstudents'].'boletin.pdf', 'F');
			}

			$this->mergePDF($path);
		}	
	}

	private function mergePDF($path)
	{	
		rmdir (str_replace('./', '', $path).'/');
		rmdir($path);
		$pdi = new FPDI();

		if(!file_exists($path))
		{	
			mkdir($path);
		}

		$dir = opendir($path);
		$files = array();
		while ($archivo = readdir($dir)) {
				
			if (!is_dir($archivo)){
				echo $archivo."<br />";
				array_push($files, $archivo);
			}
		}

		foreach ($files as $file) 
		{ 
			$pageCount = $pdi->setSourceFile($path.'/'.$file); 

			for ($i=1; $i <= $pageCount; $i++) { 
				
				$tpl = $pdi->importPage($i);
				$pdi->addPage(); 

				$pdi->useTemplate($tpl); 
			}
		}

		ob_clean();
		$buffer = $pdi->Output('I','merged.pdf');

		sleep(2);
		system('rm -rf ' . escapeshellarg($path), $retval);
	}
}

?>