<?php
namespace App\Model;

use App\Config\DataBase as DB;
use App\Model\StudentModel as Student;
use App\Model\AsignatureModel as Asignature;
use App\Model\EvaluationPeriodModel as Evaluation;
use App\Model\GeneralObservationModel as GeneralObservation;
use App\Model\GroupModel as Group;
/**
* 
*/
class GradeBookModel extends DB
{
	
	private $id_group;

	public $periods;
	public $grade = 0;
	public $info_inst = array();
	public $info_group = array();
	public $valorations = array();
	public $performances = array();
	public $studentName = '';
	public $eParameters = array();
	
	private $current_period=1;

	private $request = array();
	private $_config = array();

	private $_group;
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
			$this->_group = new Group($db);
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
	*/
	private function init()
	{
		$this->current_period = $this->request['period'];
		$this->id_group = $this->request['grupo'];

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
				'NumberValoration'	=>	(isset($this->request['NumberValoration'])) ? true : false,
				'tableDetail'	=>	(isset($this->request['tableDetail'])) ? true : false,
				'performanceRating'	=>	(isset($this->request['performanceRating'])) ? true : false
			)
		);
	}

	/**
	*
	*
	*/
	public function getAllPositionOfThePeriod($id_group)
	{
		$response = array();

		foreach($this->periods as $key => $period):

			if($period['peso'] != 0):

				array_push(
					$response, 
					$this->getPositionGradeBook($period['periodos'], $id_group)
				);
				
			endif;

		endforeach;

		return $response;
	}

	/**
	*
	*
	*/
	public function decideGradeBook($gradeBook=array(), $per=1)
	{

		foreach($gradeBook['periods'] as $key => $period):

			if($per == $period['period']):

				if(empty($period['areas'])):
					return false;
				endif;

			endif;

		endforeach;

		return true;
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
			'grade'	=>	$this->grade['id_grado'],
			'periods'	=>	array(),
			'config'	=>	$this->_config,
			'observation'	=>	$this->findObservationByStudent(
				$id_student, $id_group, $this->current_period
			),
			'eParameters'	=>	$this->eParameters

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

		// $resp = $this->getPositionByStudent($id_student, $id_group, $period)

		foreach($this->periods as $periodKey => $period2):

			if($period == $period2['periodos']):

				$response = array(
					'period'	=> $period,
					'percentage'	=>	$period2['peso'].' %',
					'inasistencia'=> $this->_evaluation->getTotalAttendanceByPeriod(
						$id_student, $id_group, $period
					)['data'][0]['inasistencia'],
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

		if($resp['state']):
			$contador=0;
			$puestos= array();
			$tavs = array();

			foreach ($resp['data'] as $key => $value) {
				$estudiante = array(
					'id' => $value['id_estudiante'], 
					'pgg' => $value['pgg'],
					'promedio' =>$value['pgg'],
					'TAV' => $value['TAV'] 
				);
				$puestos[$contador] = $estudiante;
				$tavs[$contador]= $value['TAV'];
				$contador++;
			}

			$max = max($tavs);
			$contador=0;
			$puestosDef = array();

			foreach ($puestos as $value) {
				$estudiante = array(
					'id' => $value['id'], 
					'pgg' => (($value['pgg']*$value['TAV'])/$max),
					'promedio' =>$value['pgg']
				);
				$puestosDef[$contador] = $estudiante;
				$contador++;
			}

			$puestosDef = $this->orderMultiDimensionalArray(
				$puestosDef, 'pgg', true
			);

			return $this->generarPuesto($puestosDef, $period);

		endif;
	}

	/**
	*
	*
	*/
	private function orderMultiDimensionalArray(
		$toOrderArray, 
		$field, 
		$inverse = false
	)
	{
		$position = array();
		$newRow = array();
		foreach ($toOrderArray as $key => $row) {
			$position[$key]  = $row[$field];
			$newRow[$key] = $row;
		}
		if ($inverse) {
			arsort($position);
		}
		else {
			asort($position);
		}
		$returnArray = array();
		foreach ($position as $key => $pos) {     
			$returnArray[] = $newRow[$key];
		}
		return $returnArray;
	}

	/**
	*
	*
	*/
	private function generarPuesto($estudiante, $period){
		$contador=1;
		$contadorAux=1;
		$pggAux=0;
		$puestos = array();
		foreach ($estudiante as $key => $value) {

			if($value['pgg']>$pggAux){
				$estudiantePgg = array(
					'id_student' => $value['id'], 
					'position' => $contadorAux , 
					'pgg' => $value['pgg'],
					'average' => $value['promedio'],
					'period'	=> $period
				);
				$pggAux = $value['pgg'];
				$puestos[$contador]= $estudiantePgg;
				$contadorAux++;
			}
			if($value['pgg']==$pggAux){
				$estudiantePgg = array(
					'id_student' => $value['id'],
					'position' => $contadorAux-1, 
					'pgg' => $value['pgg'],
					'period'	=> $period,
					'average' => $value['promedio']
				);
				$pggAux=$value['pgg'];
				$puestos[$contador] = $estudiantePgg;
			}
			if($value['pgg']<$pggAux){
				$estudiantePgg = array(
					'id_student' => $value['id'], 
					'position' => $contadorAux, 
					'pgg' => $value['pgg'],
					'period'	=> $period,
					'average' => $value['promedio']
				);
				$pggAux=$value['pgg'];
				$puestos[$contador] = $estudiantePgg;
				$contadorAux++;
			}
			$contador++;
		}

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
				// $note = $area['Valoracion'];
				$valoration = $this->getValoration($area['Valoracion']);

				array_push(
					$resp,
					array(
						'id_area'		=>	$area['id_area'],
						'name'			=>	utf8_encode($area['Area']),
						'order'			=>	$area['order_area'],
						'nota'			=>	$area['Valoracion'],
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
		$group = $this->_group->find($id_group)['data'][0];

		$asignatures = $this->_evaluation->getByStudent(
			$id_student, $group['id_grado'], $period, $id_area
		);

		// return $asignatures['query'];

		$resp = array();

		if($asignatures['data']):
			$id_asignature = 0;
			$note = 0;
			$valoration = '';
			foreach($asignatures['data'] as $key => $asignature):

				if($group['id_grupo'] == $asignature['id_grupo']):

					$id_asignature = $asignature['id_asignatura'];
					$note = $this->roundNumber(
						$asignature['eval_'.$period.'_per'], 2
					);
					$valoration = $this->getValoration($note);
					array_push($resp, 
						array(
							'id_asignatura'	=> $asignature['id_asignatura'],
							'name'			=> utf8_encode(
								$asignature['asignatura']
							),
							'ihs'	=>	$asignature['ihs'],
							'teacher'		=> ' ',
							'inasistencia'	=>	($asignature['inasistencia_p'.$period] == 0) ? '' : $asignature['inasistencia_p'.$period],
							'nota'	=> $note,
							'recovery' => $this->resolveRecovery(
								$id_student, $id_group, $asignature['id_asignatura'], $period
							),
							'indicators'	=> $this->resolveParametersValue(
								$asignature, $period
							),
							'valoration' => $valoration,
							'performances'	=>	array(
								'asignature'	=>	$this->resolveEParametersForPerformanceByAsignature(
										$asignature['id_asignatura'],
										$valoration,
										$period
								),
								'indicators'	=>	$this->resolveEParametersForPerformance(
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
	private function resolveEParametersForPerformanceByAsignature(
		$asignature=array(),
		$valoration,
		$period
	)
	{
		$response = array();

		foreach($this->eParameters as $key => $parameter):

			array_push(
				$response,
				array(
					'id_parameter'	=>	$parameter['id_parametro'],
					'parameter'	=>	$parameter['parametro'],
					'prefix'	=>	$parameter['prefix'],
					'performances'	=>	$this->resolvePerformanceByAsignature(
						$asignature, $valoration, $period, $parameter['prefix']
					)
				)
			);
		endforeach;


		return $response;
	}


	/**
	*
	*
	*/
	public function resolvePerformanceByAsignature(
		$id_asignature, 
		$valoration, 
		$period,
		$prefix
	)
	{

		$response = array();
		foreach($this->performances as $key => $performance):

			if($this->id_group == $performance['id_grupo'] && $id_asignature == $performance['id_asignatura'] && strstr($performance['posicion'], $prefix) && $performance['periodo'] == $period):

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
	private function resolveEParametersForPerformance($asignature =array(), $period)
	{
		$response = array();

		foreach($this->eParameters as $key => $parameter):

			array_push(
				$response,
				array(
					'id_parameter'	=>	$parameter['id_parametro'],
					'parameter'	=>	$parameter['parametro'],
					'prefix'	=>	$parameter['prefix'],
					'performances'	=>	$this->resolvePerformanceByIndicators(
						$asignature, $period, $parameter['prefix']
					)
				)
			);
		endforeach;


		return $response;
	}

	/**
	*
	*
	*/
	public function resolvePerformanceByIndicators($asignature=array(), $period, $prefix)
	{

		$response = array();
		$per_note = 0;

		foreach($this->performances as $key => $performance):

			if($asignature['id_asignatura'] == $performance['id_asignatura'] && $performance['periodo'] == $period && strstr($performance['posicion'], $prefix)):

				// if( (strstr($performance['posicion'], $prefix)) || () ):
					$per_note = isset($asignature[$performance['posicion'].'_'.$period]) ? $asignature[$performance['posicion'].'_'.$period] : 0;
					$valoration = $this->getValoration($per_note);

					array_push($response, 

						array(
							'codigo'	=>	$performance['codigo'],
							'note'	=>	$per_note,
							'valoration'	=>	$valoration,
							'position'	=>	$performance['posicion'],
							'observation'	=>	utf8_encode(
								(isset($performance[strtolower($valoration)])) ? $performance[strtolower($valoration)] : ''
							)
						)
					);
				// endif;
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

		elseif($this->request['db'] == 'agoranet_iesr' || $this->request['db'] == 'agoranet_confamar'):
			
			$resp2 = $this->_evaluation->getRecovery2(
				$id_student,
				$id_group,
				$id_asignature,
				$period
			);

			if($resp2['state']):

				$response['recovery_note'] = $this->roundNumber( 
					$resp2['data'][0]['nota'], 2
				);

				$response['old_note'] = $this->roundNumber(
					$resp2['data'][0]['nota_evaluacion'], 2
				);

			endif;
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
		
		if(strlen($number) == $size):
			return $number;

		elseif(strlen($number) > ($size+1)):
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
		$resp = $this->_gObservation->findByStudent(
			$id_student, $id_group, $period
		);

		$response = array();
		if($resp['state']):
			$contentArray = explode('<p>', $resp['data'][0]['observaciones']);

			foreach($contentArray as $p):
				array_push($response, $p);
			endforeach;
		endif;

		return $response;
	}


	/**
	*
	*
	*/
	private function resolveParametersValue($asignature=array(), $period)
	{
		$response = array();

		if(isset($this->request['tableDetail'])):

			foreach($this->eParameters as $key => $parameter):

				$percentage = (isset($asignature[
								'pcent_'.$parameter['prefix'].'_'.$period	]
								)) ? $asignature[
									'pcent_'.$parameter['prefix'].'_'.$period
								] : 0 ;
				array_push(
					$response, 
					array(
						'id_parameter'	=>	$parameter['id_parametro'],
						'parameter'	=>	$parameter['parametro'],
						'prefix'	=>	$parameter['prefix'],
						'weight'	=>	$parameter['peso'],
						'indicators'	=> $this->resolveIndicatorsValue(
							$parameter, $asignature, $period
						),
						'percentage'	=>	$percentage,
					)	
				);
			endforeach;
			
		endif;

		return $response;
	}

	/**
	*
	*
	*/
	private function resolveIndicatorsValue(
		$parameter=array(), 
		$asignature=array(),
		$period
	)
	{
		$response = array();

		foreach($parameter['indicadores'] as $key => $indicator):

			$value = 0;
			$field = '';
			$percentage = (isset($indicator['percentage'])) ? $indicator['percentage'] : 0;

			if($parameter['prefix'] == 'aeep'):
				$value = $asignature[
					$parameter['prefix'].'_'.$period
				];

				$field = $parameter['prefix'].'_'.$period;

			else:
				$value = $asignature[ 
					$parameter['prefix'].($key+1).'_'.$period
				];

				$field = $parameter['prefix'].($key+1).'_'.$period;
			
			endif;
			array_push(
				$response, 
				array(
					'id'	=>	$indicator['id'],
					'field'	=> $field,
					'indicator'	=>	$indicator['indicator'],
					'abbreviation'	=>	$indicator['abbreviation'],
					'value'	=>	$value,
					'percentage'	=> $percentage

				)
			);

		endforeach;

		return $response;
	}

	/**
	*
	*
	*
	*/
	private function indicatorsHasPercentage($indicators=array())
	{

		foreach($indicators as $key => $indicator):

			if(isset($indicator['percentage']) && $indicator['percentage'] > 0):
				return true;
			endif;

		endforeach;

		return false;
	}
}	
?>