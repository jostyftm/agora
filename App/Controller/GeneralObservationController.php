<?php
namespace App\Controller;

use App\Config\View as View;
use App\Model\PeriodModel as Period;
use App\Model\TeacherModel as Teacher;
use App\Model\GeneralObservationModel as GeneralObservation;
/**
* 
*/
class GeneralObservationController
{	
	private $_generalObservation;

	function __construct()
	{
		$this->_generalObservation = new GeneralObservation(DB);
	}

	/*
	 *
	 * @param
	 * @return
	 *
	*/
	public function indexAction($role)
	{

		if($role == 'teacher'):

			$teacher = new Teacher(DB);
			$gObservation = $teacher->getGeneralObservations(TC)['data'];

			$view = new View(
				'teacher/partials/evaluation/observations',
				'home',
				[
					'tittle_panel'	=>	'Observaciones Generales',
					'observations'	=>	$gObservation,
					'history'		=>	array(
						'current'	=> '/generalObservation/index/teacher'
					)
				]
			);

			$view->execute();

		elseif($role == 'institution'):
			echo "institution";
		else:
			echo "404 no se puede mostrar el contenido";
		endif;
	}

	/*
	 *
	 * @param $role
	 * @return
	 *
	*/
	public function createAction()
	{
		// Validamos la session
		if(true)
		{
			// Validamos la peticion GET
			if(isset($_GET['request']) && $_GET['request']== 'crud')
			{

				if($_GET['rol'] == 'teacher')
				{
					$teacher = new Teacher(DB);
					$period = new Period(DB);

					$myGroups = $teacher->getGroupByDirector(TC)['data'];
					$periods = $period->getPeriods()['data'];

					$view = new View(
						'teacher/partials/evaluation/observations',
						'create',
						[
							'tittle_panel'	=>	'Agregar Observaciones Generales',
							'myGroups'		=>	$myGroups,
							'periods'		=>	$periods,
							'back'			=>	$_GET['options']['back']
						]
					);

					$view->execute();
				}
				
			}else{
				echo "404 no se puede mostrar esta pagina";
			}
		}
		else
		{

		}
	}

	/*
	 *
	 * @param role
	 * @return
	 *
	*/
	public function storeAction()
	{	
	
		$response = array();
		foreach($_POST['students'] as $key => $id):
					
			$data = array(
				'id_student'		=> $id,
				'id_group'			=> $_POST['group'],
				'id_period'			=> $_POST['period'],
				'id_group_director'	=>	TC,
				'observations'		=> $_POST['observation']
			);

			array_push($response, $this->_generalObservation->save($data));
		endforeach;

		echo json_encode($response);
	}


	/*
	 *
	 * @param
	 * @return
	 *
	*/
	public function showAction($id_observation)
	{
		
		// Validamos la sesion
		if(true):
		
			// Validamos la peticion GET
			if(isset($_GET['request']) && $_GET['request']== 'crud'):
			
				$response = $this->_generalObservation->find($id_observation)['data'][0];

				if(isset($_GET['rol']) && $_GET['rol'] == 'teacher'):
					$view = new View(
						'teacher/partials/evaluation/observations',
						'show',
						[
							'tittle_panel'	=>	'Ver Observaciones Generales',
							'observation'		=>	$response,
							'back'			=>	$_GET['options']['back']
						]
					);

					$view->execute();

				elseif(isset($_GET['rol']) && $_GET['rol'] == 'institution'):
					echo "institution";
				else:
					echo "404 no se puede mostrar el contenido";
				endif;
			endif;
		endif;

	}


	/*
	 *
	 * @param
	 * @return
	 *
	*/
	public function editAction($id_observation)
	{
		// Validamos la Sesion
		if(true):

			// Validamos la peticion
			if(isset($_GET['request']) && $_GET['request'] == 'crud'):

				$response = $this->_generalObservation->find($id_observation)['data'][0];

				// Validamos el tipo de usuario
				if($_GET['rol'] == 'teacher'):

					$view = new View(
						'teacher/partials/evaluation/observations',
						'edit',
						[
							'tittle_panel'	=>	'Editar Observaciones Generales',
							'observation'		=>	$response,
							'back'			=>	$_GET['options']['back']
						]
					);

					$view->execute();

				elseif($_GET['rol'] == 'institution'):
					echo "institution";
				else:
					echo "404 no se puede mostrar el contenido";
				endif;
			endif;
		endif;
	}

	/*
	 *
	 * @param
	 * @return
	 *
	*/
	public function updateAction()
	{
		
		$data = array(
			'id_observation'	=>	$_POST['id_observation'],
			'observations'		=>	$_POST['observation']
		);

		echo json_encode($this->_generalObservation->update($data));
	}

	/*
	 *
	 * @param
	 * @return
	 *
	*/
	public function deleteAction()
	{
		sleep(2);
		if($this->_generalObservation->delete($_POST['id_observation'])['state']):
			$teacher = new Teacher(DB);
			$gObservation = $teacher->getGeneralObservations(TC)['data'];;

			$view = new View(
				'teacher/partials/evaluation/observations',
				'home',
				[
					'tittle_panel'	=>	'Observaciones Generales',
					'observations'	=>	$gObservation,
					'history'		=>	array(
						'current'	=> '/generalObservation/index/teacher'
					)
				]
			);

			$view->execute();
		endif;
	}
}

?>