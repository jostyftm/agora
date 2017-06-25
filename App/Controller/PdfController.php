<?php

namespace App\Controller;

use App\Model\GroupModel as Group;
use App\Model\TeacherModel as Teacher;
use App\Model\InstitutionModel as Institution;
use App\Model\PerformanceModel as Performance;
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
}

?>