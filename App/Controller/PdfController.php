<?php

namespace App\Controller;

use Lib\merge\FPDI as FPDI;
use App\Model\GroupModel as Group;
use App\Model\TeacherModel as Teacher;
use App\Model\StudentModel as Student;
use App\Model\InstitutionModel as Institution;
use App\Model\PerformanceModel as Performance;
use App\ModelPDF\GradeBookPDF	as GradeBookPDF;
use App\Model\ReportPeriodModel as ReportPeriod; //Cambio
use App\Model\EvaluationPeriodModel as Evaluation;
use App\ModelPDF\PlanillaAsistencia as PlanillaAsistencia;
use App\ModelPDF\EvaluationSheetPDF as EvaluationSheetPDF;
use App\ModelPDF\GeneralPeriodReportPDF as GeneralPeriodReportPDF; //Cambio
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
		$maxPeriod, 
		$id_asignature, 
		$id_group
	){
		$teacher = new Teacher(DB);
		$evaluation = new Evaluation(DB);
		$performance = new Performance(DB);
		$institution = new Institution(DB);
		$pdf = new EvaluationSheetPDF('landscape', 'mm', 'A4');

		// Parametros de evaluacion
		$evaluation_parameters = $performance->getEvaluationParameters()['data'];
		$ind_DC = $performance->getPerformanceIndicators(1)['data'];
		$ind_DP = $performance->getPerformanceIndicators(2)['data'];
		$ind_DS = $performance->getPerformanceIndicators(3)['data'];

		// Informacion de la institucion, salon de clase y el grupo
		$infoIns = $institution->getInfo()['data'][0];
		$infoAsignatureAndGroup = $teacher->getInfoAsignatureAndGroup($id_asignature, $id_group)['data'][0];

		$resp = $evaluation->getPeriods(
					split('_', $maxPeriod)[1],
					$id_asignature,
					$id_group
				)['data'];

		// print_r($resp);
		$pdf->maxPeriod = split('_', $maxPeriod)[1];
		$pdf->institution = $infoIns;
		$pdf->infoGroupAndAsig = $infoAsignatureAndGroup;
		$pdf->evaluation_parameters = $evaluation_parameters;
		$pdf->DC = $ind_DC;
		$pdf->DP = $ind_DP;
		$pdf->DS = $ind_DS;
		$pdf->AddPage();
		$pdf->showData($resp);
		$pdf->SetFont('Arial','B',16);
		$pdf->Output('pdf/lista-'.$pdf->infoGroupAndAsig['nombre_grupo'].'.pdf', 'I');
	}

	// Cambio
	private function generateGeneralPeriodReport($content, $path, $student=array(), $institution=array(), $group=array())
	{

		$pdf = new GeneralPeriodReportPDF('P', 'mm', 'A4');
		$pdf->institution = $institution;
		$pdf->infoStudent = $student;
		$pdf->infoGroupAndAsig = $group;
		$pdf->content = (empty($content)) ? 'No hay reporte' : $content;
		$pdf->date = (isset($_POST['fecha']) && $_POST['fecha'] != '') ? date('d-m-Y', strtotime($_POST['fecha'])) : date('d-m-Y');
		$pdf->createReportGeneralPeriod();
		$pdf->Output($path.'informe.pdf', 'F');
	}

	public function generateGradeBookByStudentAction()
	{	
		$group = new Group(DB);
		$student = new Student(DB);
		$report = new ReportPeriod(DB);
		$evaluation = new Evaluation(DB);
		$performance = new Performance(DB);
		$institution = new Institution(DB);
		
		$infoIns = $institution->getInfo()['data'][0];
		$periods = $institution->getPeriods()['data'];
		$valoration = $evaluation->getValoration()['data'];
		$path = './'.time();


		if(isset($_POST['btn_p_superacion']))
		{
			$infoGroup = $group->getInfo($_POST['grupo'])['data'][0];
			$performances = $performance->getPerformanceByGroup($infoGroup['id_grupo'], 1)['data'];

			if(!file_exists($path))
			{	
				mkdir($path);
			}

			foreach ($_POST['students'] as $key => $value) 
			{
				$infoStudent = $student->getStudent($value)['data'][0];
				$fileName = substr($infoStudent['primer_ape_alu'], 0,2).
							substr($infoStudent['segundo_ape_alu'], 0,2).
							substr($infoStudent['primer_nom_alu'], 0,2).
							substr($infoStudent['segundo_nom_alu'], 0,2).
							$infoStudent['idstudents'];

				if(isset($_POST['reportDisable']) && $infoGroup['id_grado'] == 4){
					$reportData = $report->getReportPeriodByStudent($infoStudent['idstudents'], 1);

					if($reportData['state'])
					{
						$content = $reportData['data'][0]['observaciones'];
						
						$ruta = $path."/".$fileName;
						$this->generateGeneralPeriodReport($content, $ruta, $infoStudent,$infoIns, $infoGroup);
					}
				}
				
				$gradeBook = $evaluation->getGradeBookBySudent($value, $infoGroup['id_grado'], $_POST['periodo']);

				if($evaluation->decideGradeBook($gradeBook, 1))
				{	
					$pdf = new GradeBookPDF('P', 'mm', 'A4');
					$pdf->periods = $periods;

					$pdf->institution = $infoIns;
					$pdf->valoration = $valoration;
					$pdf->infoStudent = $infoStudent;
					$pdf->areas = $gradeBook['areas'];
					$pdf->infoGroupAndAsig = $infoGroup;
					$pdf->gradeBook = $gradeBook['data'];
					$pdf->performancesData = $performances;
					$pdf->calAreas = $gradeBook['calAreas'];
					$pdf->date = (isset($_POST['fecha']) && $_POST['fecha'] != '') ? date('d-m-Y', strtotime($_POST['fecha'])) : date('d-m-Y');

					$pdf->ImpDobleCara = (isset($_POST['debleCara'])) ? true : false;
					$pdf->Impescala = (isset($_POST['escalaVAlorativa'])) ? true : false;
					$pdf->AreasDisable = (isset($_POST['areasDisabled'])) ? true : false;
					$pdf->DesemDisable = (isset($_POST['MosDesem'])) ? true : false;
					$pdf->DoceDisabled = (isset($_POST['MosDoc'])) ? true : false;

					$pdf->createGradeBook();
					$pdf->SetFont('Arial','B',16);
					$pdf->Output($path.'/'.$fileName.'boletin.pdf', 'F');
				}
				
			}

			$this->mergePDF($path);
		}	
	}

	private function mergePDF($path, $orientation='p')
	{	
		$pdi = new FPDI();

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
				$pdi->addPage($orientation); 

				$pdi->useTemplate($tpl); 
			}
		}

		ob_clean();
		$buffer = $pdi->Output('I','merged.pdf');

		system('rm -rf ' . escapeshellarg($path), $retval);
	}

	public function testPdfAction()
	{
		if(isset($_POST['btn_p_pe']))
		{
			$teacher = new Teacher($_POST['db']);
			$evaluation = new Evaluation($_POST['db']);
			$performance = new Performance($_POST['db']);
			$institution = new Institution($_POST['db']);
			
			$path = './'.time();

			$Resp_eP = $performance->getEvaluationParameters()['data'];
			$evaluation_parameters = array();

			foreach ($Resp_eP as $key => $value) 
			{
				array_push($evaluation_parameters, 
					array(
						'id_parametro' => $value['id_parametro_evaluacion'],
						'parametro' => $value['parametro'],
						'indicadores' => $performance->getPerformanceIndicators($value['id_parametro_evaluacion'])['data']
					)
				);
			}

			// Informacion de la institucion, salon de clase y el grupo
			$infoIns = $institution->getInfo()['data'][0];

			if(!file_exists($path))
			{	
				mkdir($path);
			}

			foreach ($_POST['grupos'] as $key => $value) 
			{
				$pdf = new EvaluationSheetPDF($_POST['opcion']['orientacion'], 'mm', $_POST['opcion']['papel']);
				// $pdf = new EvaluationSheetPDF('L', 'mm', 'A4');
				$id_asignature = split('-', $value)[0];
				$id_group = split('-', $value)[1];
			
				$infoAsignatureAndGroup = $teacher->getInfoAsignatureAndGroup($id_asignature, $id_group)['data'][0];

				$resp = $evaluation->getPeriods(
							split('_', $_POST['periodo'])[1],
							$id_asignature,
							$id_group
						)['data'];
				$pdf->maxPeriod = split('_', $_POST['periodo'])[1];
				$pdf->evaluation_parameters = $evaluation_parameters;
				$pdf->institution = $infoIns;
				$pdf->infoGroupAndAsig = $infoAsignatureAndGroup;
				$pdf->AddPage();
				$pdf->showData($resp);
				$pdf->Output($path.'/lista-'.$pdf->infoGroupAndAsig['nombre_grupo'].'.pdf', 'F');
			}
			$this->mergePDF($path, $_POST['opcion']['orientacion']);
		}
	}
}

?>