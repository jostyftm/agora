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
		if(isset($_POST['btn_p_superacion']))
		{
			// 
			$group = new Group($_POST['db']);
			$student = new Student($_POST['db']);
			$report = new ReportPeriod($_POST['db']);
			$evaluation = new Evaluation($_POST['db']);
			$performance = new Performance($_POST['db']);
			$institution = new Institution($_POST['db']);
			
			$infoIns = $institution->getInfo()['data'][0];
			$periods = $institution->getPeriods()['data'];
			$valoration = $evaluation->getValoration()['data'];

			// 
			$infoGroup = $group->getInfo($_POST['grupo'])['data'][0];
			$performances = $performance->getPerformanceByGroup($infoGroup['id_grupo'], 1)['data'];

			$positions = $evaluation->getPositionGradeBook(
				1,
				$_POST['grupo']
			);

			// foreach($evaluation->getAllPositions($_POST['grupo']) as $key => $value):
			// 	print_r($value); echo "<br /><br /><br />";
			// endforeach;  
			$path = './'.time().'-'.$_POST['db'].'-boletin';

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
					$pdf = new GradeBookPDF('P', 'mm', 'Letter');
					$pdf->periods = $periods;

					$pdf->institution = $infoIns;
					$pdf->valoration = $valoration;
					$pdf->infoStudent = $infoStudent;
					$pdf->areas = $gradeBook['areas'];
					$pdf->infoGroupAndAsig = $infoGroup;
					$pdf->gradeBook = $gradeBook['data'];
					$pdf->positions = $positions;
					$pdf->performancesData = $performances;
					$pdf->calAreas = $gradeBook['calAreas'];
					$pdf->date = (isset($_POST['fecha']) && $_POST['fecha'] != '') ? date('d-m-Y', strtotime($_POST['fecha'])) : date('d-m-Y');

					$pdf->ImpDobleCara = (isset($_POST['debleCara'])) ? true : false;
					$pdf->Impescala = (isset($_POST['escalaVAlorativa'])) ? true : false;
					$pdf->AreasDisable = (isset($_POST['areasDisabled'])) ? true : false;
					$pdf->DesemDisable = (isset($_POST['MosDesem'])) ? true : false;
					$pdf->DoceDisabled = (isset($_POST['MosDoc'])) ? true : false;
					$pdf->perdioFace = (isset($_POST['periodFace'])) ? true : false;
					$pdf->CombinedEvaluation = (isset($_POST['CombinedEvaluation'])) ? true : false;

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
}

?>