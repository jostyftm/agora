<?php
namespace App\Controller;

use App\Config\View as View;
use App\Model\GroupModel as Group;
use App\Model\TeacherModel as Teacher;
use App\Model\PerformanceModel as Performance;
use App\Model\InstitutionModel as Institution;
use App\Model\EvaluationPeriodModel as Evaluation;
/**
* 
*/
class AjaxController
{
	
	function __construct()
	{
		
	}

	public function indexAction(){
		echo "string";
	}

	public function getPeriodoAction($column, $id_asignature, $id_group){

		$evaluation = new Evaluation(DB);

		$response = $evaluation->getPeriodsWithOutEvaluating($column, $id_asignature, $id_group)['data'];

		$view = new View(
			'teacher',
			'formEvaluatePeriodRender',
			[
				'info'	=> $response,
				'periodo'	=>	$column
			]
		);

		$view->execute();
	}

	public function updatePeriodAction($period, $id_student, $id_asignature, $value)
	{
		$evaluation = new Evaluation(DB);

		echo $evaluation->updatePeriod($period, $id_student, $id_asignature, $value);
	}

	public function getEvaluationSheetAction(
		$db='', 
		$maxPeriod, 
		$id_asignature, 
		$id_group
	){

		$performance = new Performance(DB);
		$evaluation = new Evaluation(DB);

		$evaluation_parameters = $performance->getEvaluationParameters()['data'];

		$ind_DP = $performance->getPerformanceIndicadors(2)['data'];
		$ind_DS = $performance->getPerformanceIndicadors(3)['data'];


		$resp = $evaluation->getPeriods(
					split('_', $maxPeriod)[1],
					$id_asignature,
					$id_group
				)['data'];

		$view = new View(
			'reportPDF',
			'evaluationSheetRender',
			[
				'EP'	 => $evaluation_parameters,
				'DP'	 =>	$ind_DP,
				'DS'	 =>	$ind_DS,
				'periodo'=>	split('_', $maxPeriod)[1],
				'datos'	 =>	$resp,
				'info'	 =>	$resp[0]
			]
		);

		$view->execute();
	}

	public function getGroupsAction($id_sede, $db='')
	{
		$institution = new Institution(DB);

		$groups = $institution->getGroups($id_sede)['data'];

		foreach ($groups as $key => $value) {
			echo "<option value='".$value['id_grupo']."'>".utf8_encode($value['nombre_grupo'])."</option>";	
		}
	}

	public function getStudentsAction($id_group, $db='')
	{
		$group = new Group(DB);

		$students = $group->getClassRoomList($id_group)['data'];

		foreach ($students as $key => $value) 
		{
			echo "<option value='".$value['idstudents']."'>".
					utf8_encode(
						$value['primer_ape_alu']." ".
						$value['segundo_ape_alu']." ".
						$value['primer_nom_alu']." ".
						$value['segundo_nom_alu']).
				"</option>";	
		}
	}

	public function getDocentesAction($db='', $id_sede)
	{
		$institution = new Institution($db);

		$docentes = $institution->getDocentes($id_sede)['data'];

		foreach ($docentes as $key => $value) 
		{
			echo "<option value='".$value['id_docente']."'>".utf8_encode(
					 $value['segundo_apellido'].' '.$value['primer_apellido'].' '.$value['primer_nombre'].' '.$value['segundo_nombre'])."</option>";	
		}
			
		// foreach ($docentes as $key => $value) 
		// 	echo json_encode(
		// 		array( 'data' => array(
		// 			'id_docente' => $value['id_docente'],
		// 			'docente'	=> $value['segundo_apellido'].' '.$value['primer_apellido'].' '.$value['primer_nombre'].' '.$value['segundo_nombre'],
		// 			'id_sede'	=> $value['id_sede']
		// 		))
		// 	);
		
	}

	public function getAsignaturesByTeacherAction($db, $id_teacher)
	{
		$teacher = new Teacher($db);
		$asignatures = $teacher->getAsignaturesAndGroups($id_teacher)['data'];

		foreach ($asignatures as $key => $value) 
		{
			echo "<option value='".$value['id_asignatura']."-".$value['id_grupo']."'>".utf8_encode($value['nombre_grupo'].' - '.
					 $value['asignatura'])."</option>";	
		}
	}
}
?>