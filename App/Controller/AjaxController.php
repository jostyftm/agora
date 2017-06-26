<?php

namespace App\Controller;

use App\Config\View as View;
use App\Model\GroupModel as Group;
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
		$model, 
		$maxPeriod, 
		$id_asignature, 
		$id_group
	){

		$performance = new Performance(DB);
		$evaluation = new Evaluation(DB);

		$ind_DP = $performance->getPerformanceIndicadors(2)['data'];
		$ind_DS = $performance->getPerformanceIndicadors(3)['data'];

		$resp = $evaluation->getPeriods(
					split('_', $maxPeriod)[1],
					$id_asignature,
					$id_group
				)['data'];

		$view = new View(
			'reportPDF',
			'evaluationSheet-'.$model.'-render',
			[
				'DP'	 =>	$ind_DP,
				'DS'	 =>	$ind_DS,
				'periodo'=>	split('_', $maxPeriod)[1],
				'datos'	 =>	$resp,
				'info'	 =>	$resp[0],
				'model'	 =>	$model
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

		foreach ($students as $key => $value) {
			echo "<option value='".$value['idstudents']."'>".utf8_encode($value['estudiante'])."</option>";	
		}
	}
}
?>