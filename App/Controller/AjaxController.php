<?php

namespace App\Controller;

use App\Config\View as View;
use App\Model\PerformanceModel as Performance;
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

	public function getEvaluationSheetAction($db='', $model, $id_asignature, $id_group){
		$performance = new Performance(DB);

		$ind_DP = $performance->getPerformanceIndicadors(2)['data'];
		$ind_DS = $performance->getPerformanceIndicadors(3)['data'];

		print_r($ind_DP);
	}
}
?>