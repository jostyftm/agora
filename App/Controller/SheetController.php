<?php
namespace App\Controller;

use App\Model\SheetModel as Sheet;
use App\Model\PeriodModel as Period;
use App\Model\TeacherModel as Teacher;
use App\Model\PerformanceModel as Performance;
use App\Model\InstitutionModel as Institution;
use App\Model\EvaluationPeriodModel as Evaluation;
/**
* 
*/
class SheetController
{
	private $_sheet;
	private $_teacher;
	private $_evaluation;
	private $_institution;
	private $_performance;
	
	/**
	*
	*
	*/
	function __construct()
	{
		
	}

	/**
	* @author
	* @param
	* @return
	*/
	public function attendanceAction($db)
	{
		// Preguntamos si el array POST NO esta vacio
		if(!empty($_POST) && isset($_POST['teacher'])):

			$teacher = new Teacher($db);	
			$sheet = new Sheet($db);
			
			$path = './'.time().$db.'-planillaAsistencia/';

			$sheet->setPath($path);


			if(!file_exists($path))
				mkdir($path);

			foreach($_POST['teacher'] as $key => $id_teacher):

				$groups = $teacher->getAsignaturesAndGroups($id_teacher)['data'];	

				foreach($groups as $key => $group):

						$sheet->studentAttendanceSheet($group['id_asignatura'], $group['id_grupo'], 'studentAttendance');
				endforeach;
		
			endforeach;

			$sheet->merge('l');
		else:

			echo "Vacio";
		endif;
	}

	/**
	*
	*
	*/
	public function evaluationAction($db)
	{
		if(!empty($_POST) && isset($_POST['teacher'])):

			$sheet = new Sheet($db);
			$period = new Period($db);
			$teacher = new Teacher($db);	
			$performance = new Performance($db);

			// Creamos el directorio
			$path = './'.time().$db.'-planillaEvaluacion/';

			if(!file_exists($path))
			{	
				mkdir($path);
			}

			// Obtenemos la cantidad de periodos
			$periods = count($period->all()['data']);

			// OBtenemos los parametros de evaluacion
			$Resp_eP = $performance->getEvaluationParameters()['data'];
			// 
			$evaluation_parameters = array();
			
			// Recorremos cada parametro de evaluacion y creamos un nuevo array
			foreach ($Resp_eP as $key => $value) 
			{
				array_push($evaluation_parameters, 
					array(
						'id_parametro' => $value['id_parametro_evaluacion'],
						'parametro' => $value['parametro'],
						'indicadores' => $performance->getPerformanceIndicators($value['id_parametro_evaluacion'])['data']
					)
				);
			}

			// Cargamos las opciones para el pdf
			$options = array(
				// 'infoIns'			=> $this->_institution->getInfo()['data'][0],
				'e_parameters'		=>	$evaluation_parameters,
				'orientation'		=>	$_POST['orientation'],
				'papper'			=>	$_POST['papper']
			);

			// Asignamos el directorio
			$sheet->setPath($path);
			// Asignamos las opciones
			$sheet->setOptions($options);

			// 
			foreach($_POST['teacher'] as $key => $id_teacher):

				// 
				$groups = $teacher->getAsignaturesAndGroups($id_teacher)['data'];

				// Recorremos los grupos y las asignaturas recibidos por POST
				foreach ($groups as $key => $group):

					$sheet->evaluactionSheet($periods, $group['id_asignatura'], $group['id_grupo']);
				endforeach;

			endforeach;

			// 
			$sheet->merge($_POST['orientation']);
		else:

		endif;
	}


	// /**
	// * @author
	// * @param
	// * @return
	// */
	// public function attendanceAction()
	// {
	// 	// Preguntamos si el array POST NO esta vacio
	// 	if(!empty($_POST) && isset($_POST['groups'])):

	// 		$path = './'.time().'/';

	// 		$this->_sheet->setPath($path);

	// 		if(!file_exists($path))
	// 			mkdir($path);

	// 		foreach($_POST['groups'] as $key => $group):
				
	// 			$id_asignature = split('-', $group)[0];
	// 			$id_group = split('-', $group)[1];

	// 			$this->_sheet->studentAttendanceSheet($id_asignature, $id_group, 'studentAttendance');
	// 		endforeach;

	// 		// 
	// 		$this->_sheet->merge('l');
	// 	else:

	// 		echo "Vacio";
	// 	endif;
	// }

	/**
	*
	*
	*/
	// public function evaluationAction()
	// {
	// 	if(!empty($_POST) && isset($_POST['groups'])):

	// 		// Creamos el directorio
	// 		$path = './'.time().'/';

	// 		if(!file_exists($path))
	// 		{	
	// 			mkdir($path);
	// 		}

	// 		// OBtenemos los parametros de evaluacion
	// 		$Resp_eP = $this->_performance->getEvaluationParameters()['data'];
	// 		// 
	// 		$evaluation_parameters = array();
			
	// 		// Recorremos cada parametro de evaluacion y creamos un nuevo array
	// 		foreach ($Resp_eP as $key => $value) 
	// 		{
	// 			array_push($evaluation_parameters, 
	// 				array(
	// 					'id_parametro' => $value['id_parametro_evaluacion'],
	// 					'parametro' => $value['parametro'],
	// 					'indicadores' => $this->_performance->getPerformanceIndicators($value['id_parametro_evaluacion'])['data']
	// 				)
	// 			);
	// 		}

	// 		// Cargamos las opciones para el pdf
	// 		$options = array(
	// 			'infoIns'			=> $this->_institution->getInfo()['data'][0],
	// 			'e_parameters'		=>	$evaluation_parameters,
	// 			'orientation'		=>	$_POST['orientation'],
	// 			'papper'			=>	$_POST['papper']
	// 		);

	// 		// Asignamos el directorio
	// 		$this->_sheet->setPath($path);
	// 		// Asignamos las opciones
	// 		$this->_sheet->setOptions($options);

	// 		// Recorremos los grupos y las asignaturas recibidos por POST
	// 		foreach ($_POST['groups'] as $key => $group) {
				
	// 			$id_asignature = split('-', $group)[0];
	// 			$id_group = split('-', $group)[1];

	// 			$this->_sheet->evaluactionSheet($_POST['period'], $id_asignature, $id_group);
	// 		}

	// 		// 
	// 		$this->_sheet->merge($_POST['orientation']);
	// 	else:

	// 	endif;
	// }
}
?>