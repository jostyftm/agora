<?php
namespace App\Controller;
use App\Config\View as View;
use App\Model\EvaluationPeriodModel as Evaluation;
/**
* 
*/
class EvaluationController
{
	
	function __construct()
	{	
	
	}

	public function indexAction($db='agoranet_ieag'){
		$evaluation = new Evaluation($db);
		$resp = $evaluation->getPositionGradeBook(1, 19);

		foreach($resp as $position){

			print_r($position); echo "<br /><br />";
		}
	}


}
?>