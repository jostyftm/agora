<?php
namespace App\Model;

use App\Config\DataBase as DB;
use App\Model\StudentModel as Student;
use App\Model\AsignatureModel as Asignature;
use App\Model\EvaluationPeriodModel as Evaluation;
use App\Model\GeneralObservationModel as GeneralObservation;
/**
* 
*/
class GradeBookModel extends DB
{
	
	private $id_group;

	public $periods;
	public $info_inst = array();
	public $info_group = array();
	public $valorations = array();
	public $performances = array();
	public $studentName = '';
	
	private $current_period=1;

	private $request = array();
	private $_config = array();

	private $_student;
	private $_evaluation;
	private $_asignature;
	private $_gObservation;
	
	function __construct($db='', $request=array())
	{	
		if(!$db)
			throw new \Exception("La clase ".get_class($this)." no encontro la base de datos", 1);
		else{
			parent::__construct($db);
			$this->_student = new Student($db);
			$this->_asignature = new Asignature($db);
			$this->_evaluation = new Evaluation($db);
			$this->_gObservation = new GeneralObservation($db);
			// 
			$this->request = $request;

			$this->init();
		}
	}

	/**
	*
	*
	*/
	private function init()
	{
		$this->current_period = $this->request['period'];
		$this->id_group = $this->request['grupo'];

		$this->config();
	}

	/**
	*
	*/
	private function config()
	{
		array_push($this->_config,
			array(
				'showTeacher'			=>	(isset($this->request['showTeacher'])) ? true : false,
				'valorationScale'		=>	(isset($this->request['valorationScale'])) ? true : false,
				'showPerformance'		=>	(isset($this->request['showPerformance'])) ? 'indicators' : 'asignature',
				'areasDisabled'			=>	(isset($this->request['areasDisabled'])) ? true : false,
				'doubleFace'			=>	(isset($this->request['doubleFace'])) ? true : false,
				'generalReportPeriod'	=>	(isset($this->request['generalReportPeriod'])) ? true : false,
				'showFaces'				=>	(isset($this->request['showFaces'])) ? true : false,
				'combinedEvaluation'	=>	(isset($this->request['CombinedEvaluation'])) ? true : false,
			)
		);
	}

	/**
	*
	*
	*/
	public function getAllByStudent($id_student, $date, $id_group, $academic){

		$student = $this->_student->getStudent($id_student)['data'][0];
		$this->studentName = $student['primer_ape_alu']." ".$student['segundo_ape_alu']." ".$student['primer_nom_alu']." ".$student['segundo_nom_alu'];

		$response = array(
			'tittle'	=>	'INFORME DESCRIPTIVO Y VALORATIVO',
			'current_period'	=>	$this->current_period,
			'date'		=>	($date == '') ? date("Y-m-d") : $date,
			'student'	=>	array(
				'id'	=>	$student['idstudents'],
				'name'	=>	utf8_encode($this->studentName)
			),
			'periods'	=>	array(),
			'valorations'	=> $this->valorations,
			'config'	=>	$this->_config,
			'observation'	=>	$this->findObservationByStudent(
				$id_student, $id_group, $this->current_period
			)

		);

		
		foreach($this->periods as $key => $period):

			if($period['peso'] != 0 && $period['periodos']):

				array_push(
					$response['periods'], 
					$this->resolvePeriod(
						$id_student, $id_group, $period['periodos'], $academic
					)
				);
				
			endif;

		endforeach;

		return $response;
	}

	/**
	*
	*
	*/
	public function resolvePeriod($id_student, $id_group, $period, $academic){

		$resp = $this->getPositionByStudent($id_student, $id_group, $period);

		foreach($this->periods as $periodKey => $period2):

			if($period == $period2['periodos']):

				$response = array(
					'period'	=> $period,
					'position'	=> $resp['position'],
					'pgg'		=> $resp['pgg'],
					'percentage'	=>	$period2['peso'].' %',
					'areas'		=>	$this->resolveAverageArea(
						$id_student, 
						$id_group,
						$period,
						$academic
					)
				);

			endif;

		endforeach;
		

		return $response;
	}

	/**
	*
	*
	*/
	public function getPositionByStudent($id_student, $id_group, $per){
		$resp = $this->getPositionGradeBook($per, $id_group);

		foreach($resp as $keyP => $position):
			if($position['id_student'] == $id_student && $position['period'] == $per)

				return $position;

		endforeach;
		
		return array(
			'position'=>'',
			'pgg'	=>'',
			'peso'	=> '',
		);
	}

	/**
	*
	*
	*/
	public function getPositionGradeBook($period='', $id_group=''){

		$resp = $this->_evaluation->getBetterAverages($period, $id_group);

		$puestos = array();

		if($resp['state']):
			$position = 1;
			$pggAux = 0;

			$infoStudent = array('id_student'=>'', 'period'=>'','position'=>'','pgg'=>'', 'peso'=>'');

			foreach($resp['data'] as $student){

				if($student['pgg'] == $pggAux){
					$infoStudent['id_student'] = $student['id_estudiante'];
					$infoStudent['period'] = $period;
					$infoStudent['position'] = $position-1;
					$infoStudent['pgg'] = $student['pgg'];
					$infoStudent['peso'] = $student['peso'];

				}else{
					$pggAux = $student['pgg'];
					
					$infoStudent['id_student'] = $student['id_estudiante'];
					$infoStudent['period'] = $period;
					$infoStudent['position'] = $position;
					$infoStudent['pgg'] = $student['pgg'];
					$infoStudent['peso'] = $student['peso'];
					
					$position++;

				}

				array_push($puestos, $infoStudent);
			}
		endif;

		return $puestos;
	}

	/**
	*
	*
	*/
	public function resolveAverageArea(
		$id_student='', 
		$grupo='', 
		$periodo='', 
		$academica=''
	){

		$areas = $this->_evaluation->getAverageAreas(
			$id_student, $grupo, $periodo, $academica
		);

		$resp = array();

		if($areas['data']):

			foreach($areas['data'] as $key => $area):
				$note = $area['Valoracion'];
				$valoration = $this->getValoration($note);

				array_push(
					$resp,
					array(
						'id_area'		=>	$area['id_area'],
						'name'			=>	utf8_encode($area['Area']),
						'order'			=>	$area['order_area'],
						'nota'			=>	$note,
						'valoration'	=>	$valoration,
						'asignatures'	=>	$this->resolveAsignatures(
							$id_student, $grupo, $periodo, $area['id_area']
						)
					)
				);
			endforeach;
		endif;

		return $resp;
	}

	/**
	*
	*
	*/
	public function resolveAsignatures($id_student, $id_group, $period, $id_area)
	{
		$asignatures = $this->_evaluation->getByStudent(
			$id_student, $id_group, $period, $id_area
		);

		$resp = array();

		if($asignatures['data']):
			$id_asignature = 0;
			foreach($asignatures['data'] as $key => $asignature):

				if($asignature['id_area'] == $id_area && $id_asignature != $asignature['id_asignatura']):

					$id_asignature = $asignature['id_asignatura'];

					array_push($resp, 
						array(
							'id_asignatura'	=> $asignature['id_asignatura'],
							'name'			=> utf8_encode(
								$asignature['asignatura']
							),
							'ihs'	=>	$asignature['ihs'],
							'teacher'		=> utf8_encode(
								$asignature['doc_primer_ape']." ".
								$asignature['doc_segundo_ape']." ".
								$asignature['doc_primer_nomb']." ".
								$asignature['doc_segundo_nomb']
							),
							'inasistencia'	=>	($asignature['inasistencia_p'.$period] == 0) ? '' : $asignature['inasistencia_p'.$period],
							'nota'	=> $this->roundNumber(
								$asignature['eval_'.$period.'_per'], 2
							),
							'recovery' => $this->resolveRecovery(
								$id_student, $id_group, $asignature['id_asignatura'], $period
							),
							'valoration' => $asignature['valoracion'],
							'performances'	=>	array(
								'asignature'	=>	$this->resolvePerformanceByAsignature(
									$asignature['id_asignatura'],
									$asignature['valoracion'],
									$period
								),
								'indicators'	=>	$this->resolvePerformanceByIndicators(
									$asignature, $period
								)
							),
							'observations'	=>	$this->resolveObservationByAsignature(
								$id_student, $asignature['id_asignatura'], $period
							)
						)
					);
				endif;
			endforeach;
		endif;

		return $resp;
	}

	/**
	*
	*
	*/
	public function resolvePerformanceByAsignature(
		$id_asignature, 
		$valoration, 
		$period
	)
	{

		$response = array();
		foreach($this->performances as $key => $performance):

			if($this->id_group == $performance['id_grupo'] && $id_asignature == $performance['id_asignatura'] && $performance['periodos'] == $period):

				array_push($response, 
					array(
						'codigo'		=> $performance['codigo'],
						'valoration'	=>	$valoration,
						'observation'	=> utf8_encode(
							$performance[strtolower($valoration)]
						)
					)
				);
			endif;
		endforeach;

		return $response;
	}

	/**
	*
	*
	*/
	public function resolvePerformanceByIndicators($asignature=array(), $period)
	{

		$response = array();
		$performance_note = 0;
		foreach($this->performances as $key => $performance):

			if($asignature['id_asignatura'] == $performance['id_asignatura']):
				$performance_note = $asignature[
										$performance['posicion'].'_'.$period
									];
				$valoration = $this->getValoration($performance_note);

				array_push($response, 

					array(
						'codigo'	=>	$performance['codigo'],
						'note'	=>	$performance_note,
						'valoration'	=>	$valoration,
						'observation'	=>	utf8_encode(
							(isset($performance[strtolower($valoration)])) ? $performance[strtolower($valoration)] : ''
						)
					)
				);
			endif;

		endforeach;

		return $response;
	}

	/**
	*
	*
	*/
	public function resolveObservationByAsignature($id_student, $id_asignature, $period){

		$observations = $this->_asignature->getObservationByStudent(
			$id_student, $id_asignature, $period
		)['data'];

		$response = array();

		foreach($observations as $key => $observation):

			array_push($response, 
				array(
					'observation'	=>	$observation['observacion']
				)
			);
		endforeach;

		return $response;
	}

	/**
	*
	*
	*/
	private function resolveRecovery(
		$id_student, 
		$id_group, 
		$id_asignature,
		$period
	)
	{	

		$response = array(
			'recovery_note'	=> 0,
			'old_note'		=> 0,
		);

		$resp  = $this->_evaluation->getRecovery(
			$id_student,
			$id_group,
			$id_asignature,
			$period
		);	

		if($resp['state']):

			$response['recovery_note'] = $this->roundNumber( 
				$resp['data'][0]['nota'], 2
			);

			$response['old_note'] = $this->roundNumber(
				$resp['data'][0]['nota_evaluacion'], 2
			);

		endif;

		return $response;

	}

	/**
	*
	*
	*/
	private function getValoration($note)
	{
		$response = '';
		foreach ($this->valorations as $key => $valoration) 
		{
								
			if($note >= $valoration['minimo'] && $note <= $valoration['maximo'])
				$response = $valoration['valoracion'];
			
			else if($note == NULL || $note == 0)
					$response = 'Bajo';
		}

		return $response;
	}

	/**
	*
	*
	*/
	private function roundNumber($number, $size)
	{	
		
		if(strlen($number) > ($size+1)):
			return substr($number, 0, ($size+1) );
		
		elseif(strlen($number) == ($size+1)):

			return $number;

		elseif(strlen($number) == 1):

			return $number.'.0';
		endif;
	}

	/**
	*
	*
	*/
	private function findObservationByStudent($id_student, $id_group, $period)
	{
		return $this->_gObservation->findByStudent($id_student, $id_group, $period)['data'];
	}

}
?>