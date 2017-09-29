<?php

namespace App\Controller;

use Lib\merge\FPDI as FPDI;
use App\Model\GroupModel as Group;
use App\Model\TeacherModel as Teacher;
use App\Model\StudentModel as Student;
use App\Model\GradeBookModel as GradeBook;
use App\Model\InstitutionModel as Institution;
use App\Model\PerformanceModel as Performance;
use App\ModelPDF\GradeBookPDF as GradeBookPDF;
use App\ModelPDF\GradeBook2PDF as GradeBook2PDF;
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

		asort($files);
		
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

	public function getAveragesByGroupAction($db, $period, $id_group){
		$evaluation = new Evaluation($db);
		$gradeBook = new GradeBook($db);

		echo json_encode($gradeBook->resolvePeriod(100137, $id_group, $period, true));
	}

	public function gradeBookAction(){

		$group = new Group($_POST['db']);
		$evaluation = new Evaluation($_POST['db']);
		$institution = new Institution($_POST['db']);
		$performance = new Performance($_POST['db']);
		$gradeBook = new GradeBook($_POST['db'], $_POST);

		$info_inst = $institution->getInfo()['data'][0];
		$info_group = $group->getInfo($_POST['grupo'])['data'][0];
		$valorations = $evaluation->getValoration()['data'];
		$eParameters = $performance->getEvaluationParametersAndIndicators();

		$gradeBook->periods = $institution->getPeriods()['data'];
		$gradeBook->grade = $group->getGrade($_POST['grupo'])['data'][0];
		$gradeBook->valorations = $valorations;
		$gradeBook->eParameters = $eParameters;
		$gradeBook->performances = $performance->getPerformanceByGroup(
			$_POST['grupo'], $_POST['period']
		)['data'];

		$positions = $gradeBook->getAllPositionOfThePeriod($_POST['grupo']);
		

		$path = 'pdf/'.time().'-'.$_POST['db'].'-boletin';

		if(!file_exists($path))
		{	
			mkdir($path);
		}


		foreach ($_POST['students'] as $key => $value) 
		{	
			
			// BOLETIN
			$pdfGradeBook = new GradeBook2PDF('P', 'mm', 'Letter');
			$pdfGradeBook->institution = $info_inst;
			$pdfGradeBook->group = $info_group;
			$pdfGradeBook->positions = $positions;
			$pdfGradeBook->valorations = $valorations;

			$cal = $gradeBook->getAllByStudent(
				$value, 
				$_POST['fecha'],
				$_POST['grupo'],
				false
			);

			// echo json_encode($cal);
			
			$fileName = str_replace(' ', '', $gradeBook->studentName);

			// 
			if($gradeBook->decideGradeBook($cal, $_POST['period'])):

				$pdfGradeBook->gradeBook = $cal;
				$pdfGradeBook->createGradeBook();
				$pdfGradeBook->Output($path.'/'.$fileName.'boletin.pdf', 'F');

			endif;

			
			if(isset($_POST['generalReportPeriod'])):

				$report = new ReportPeriod($_POST['db']);

				$reportData = $report->getReportPeriodByStudent($value, $_POST['period']);

				if($reportData['state']):
					$content = $reportData['data'][0]['observaciones'];
					$contentArray = explode('<p>', $content);

					// INFORME GENERAL DEL PERIODO
					$pdfReport = new GeneralPeriodReportPDF('P', 'mm', 'Letter');
					$pdfReport->institution = $info_inst;
					$pdfReport->period = $_POST['period'];
					$pdfReport->options = $_POST;
					$pdfReport->infoGroupAndAsig = $info_group;
					$pdfReport->infoStudent = $gradeBook->studentName;
					$pdfReport->content = $contentArray;
					$pdfReport->createReportGeneralPeriod();
					$pdfReport->Output($path.'/'.$fileName.'informe.pdf', 'F');
				
				endif;

			endif;
		}	

		$this->mergePDF($path);
	}
}

?>