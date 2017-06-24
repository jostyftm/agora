<?php

namespace App\Controller;

use App\Model\GroupModel as Group;
use App\Model\TeacherModel as Teacher;
use App\Model\InstitutionModel as Institution;
use App\ModelPDF\PlanillaAsistencia as PlanillaAsistencia;
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
}

?>