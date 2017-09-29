<?php
namespace App\ModelPDF;
use Lib\merge\FPDF as FPDF;
/**
* 
*/
class GradeBook2PDF extends FPDF
{
	public $institution = array();	
	public $group = array();
	public $gradeBook = array();
	public $performances = array();
	public $valorations = array();

	public $positions = array();

	private $_h_c = 4;

	function header()
	{
		if($this->institution['logo_byte'] != NULL)
		{
			$pic = 'data:image/png;base64,'.base64_encode(
				$this->institution['logo_byte']
			);

			$info = getimagesize($pic);

		    // Logo
		    $this->Image($pic, 12, 14, 15, 15, 'png');
		}

		//Marco
	    $this->Cell(0, 24, '', 1,0);
	    $this->Ln(0);

	    // NOMBRE DE LA INSTITUCIÓN
	    $this->SetFont('Arial','B',12);
	    $this->Cell(0, 6, ($this->institution['nombre_inst']), 0, 0, 'C');
	    $this->Ln(6);

	    $this->SetFont('Arial','B',9);
	    // NOMBRE DE LA SEDE
	    if($this->group['sede'] != NULL):
		    $this->Cell(0,4, 'SEDE: '.strtoupper(($this->group['sede'])), 0, 0, 'C');
		    
	    endif;

	    $this->Ln(4);

	    // TITULO DEL PDF
	    $this->Cell(0, 4, strtoupper($this->gradeBook['tittle']), 0, 0, 'C');
	    $this->Ln();

	    // NOMBRE DEL GRUPO
	    $this->SetFont('Arial','',8);
	    $this->Cell(20, 4, '', 0,0);
	    $this->Cell(90, 4, 'GRUPO: '.$this->group['nombre_grupo'], 0, 0, 'L');

	    // DIRECTOR DE GRUPO
	     $this->Cell(0,4, 'DIR. DE GRUPO: '.
	    	$this->group['doc_primer_nomb']." ".
	    	$this->group['doc_segundo_nomb']." ".
	    	$this->group['doc_primer_ape']." ".
	    	$this->group['doc_segundo_ape'], 0, 0, 'L');
	    $this->Ln();

	    // NOMBRE DEL ESTUDIANTE
	    $this->Cell(20, 4, '', 0,0);
	    $this->Cell(90, 4, 'ESTUDIANTE: '.utf8_decode($this->gradeBook['student']['name']), 0, 0, 'L');

	    // FECHA
	    $this->Cell(0, 4, 'FECHA: '.$this->gradeBook['date'], 0,0, 'L');
	    // Salto de línea
	    $this->Ln(8);

	    $this->subHeader();
	}

	private function subHeader()
	{
		$this->Cell(140, $this->_h_c, $this->gradeBook['tittle'].utf8_decode(' PERIODO '.$this->gradeBook["current_period"].' - AÑO LECTIVO ').date('Y'), 1,0, 'L'); 

		$this->Cell(10, $this->_h_c, 'IHS', 1,0, 'C');
		$this->Cell(17, $this->_h_c, 'VAL', 1,0, 'C');
		$this->Cell(0, $this->_h_c, utf8_decode('DESEMPEÑO'), 1,0, 'C');

		$this->Ln($this->_h_c+4);
	}

	/**
	*
	*
	*/
	public function createGradeBook()
	{
		
		// AÑADIMOS UNA PAGINA EN BLANCO
		$this->addPage();

		// RECOREMOS LOS PERIODOS
		foreach($this->gradeBook['periods'] as $periodKey => $period):

			// PREGUNTAMOS SI EL PERIODO RECORRIDO ES IGUAL AL PERIOD SOLICITADO
			if($period['period'] == $this->gradeBook['current_period']):

				// MOSTRAMOS LAS AREAS
				$this->showAreas($period['areas']);
				$this->Cell(0, $this->_h_c, '', 'T',0, 'L');
			endif;

		endforeach;

		$this->Ln();

		if($this->gradeBook['config'][0]['combinedEvaluation']):
			// MOSTRAMOS EL CUADRO ACUMULATIVO
			$this->showCombineEvaluation();

			// MOSTRAMOS EL PROMEDIO GENERAL
			$this->showScore();

			// MOSTRAMOS EL PUESTO OCUPADO
			$this->showPosition();
		endif;

		// 
		if($this->gradeBook['config'][0]['tableDetail']):

			$this->showTableDetail($period);

		endif;

		// MOSTRAR ESCALA VALORATIVA
		if($this->gradeBook['config'][0]['valorationScale']):
			$this->showValueScale();
		endif;

		// MOSTRAMOS LAS INASISTENCIA EN CASO DE QUE SEA UN CURSO INFERIOR A 6°
		if($this->gradeBook['grade'] < 10):

			$this->showTotalAttendanceByPeriod();

		endif;

		// MOSTRAMOS LAS OBSERVACIONES GENERALES
		$this->showGeneralObservation($this->gradeBook['observation']);

		if($this->gradeBook['config'][0]['doubleFace']	):
			$this->DoubleFace();
		endif;
	}

	private function showAreas($areas = array())
	{
		// RECORREMOS LAS AREAS
		foreach($areas as $areaKey => $area):

			// FONDO PARA LAS CELDAS DE LAS AREAS
			$this->SetFillColor(230,230,230);
			$this->SetFont('Arial','B',8);

			if($area['nota'] != NULL):
				// PREGUNTAMOS SI LAS AREAS NO SE DESACTIVAN
				if(!$this->gradeBook['config'][0]['areasDisabled']):

					$this->Cell(150, $this->_h_c, utf8_decode($area['name']), 'TBL',0, 'L', true);
					$this->Cell(17, $this->_h_c, $area['nota'], 'TB',0, 'C', true);
					$this->Cell(0, $this->_h_c, strtoupper($area['valoration']), 'TBR', 0, 'C', true);
				
				else:

					$this->Cell(0, $this->_h_c, utf8_decode($area['name']), 1,0, 'L', true);

				endif;

				$this->Ln();
				// RECORREMOS LAS ASIGNATURAS
				$this->showAsignature($area['asignatures']);
			endif;

			
		endforeach;
	}

	/**
	*
	*
	*/
	private function showAsignature($asignatires = array())
	{	
		// 
		foreach($asignatires as $keyAsignature => $asignature):

			if($this->determineShowValoration($asignature)):
				
				if($asignature['nota'] > 0):

					// MOSTRAMOS LA VALORACIÓN
					$this->showValoration($asignature);

					// MOSTRAMOS LOS DESEMPEÑOS (LOGROS)
					$this->SetFont('Arial','',8);
					$this->showPerformance($asignature['performances']);

					// MOSTRAMOS LAS OBSERVACIONES
					$this->showObservationsByAsignature($asignature);

					// MOSTRAMOS AL DOCENTE
					if($this->gradeBook['config'][0]['showTeacher']):
						$this->showTeacher($asignature['teacher']);
					endif;

				endif;

				
			endif;
			
		endforeach;
	}


	/**
	*
	*
	*/
	private function determineShowValoration($asignature=array())
	{
		foreach($this->gradeBook['periods'] as $keyPeriod => $period):

			foreach($period['areas'] as $keyAreas => $area):

				foreach($area['asignatures'] as $keyAsignature => $asignaturee):

					if($asignature['id_asignatura'] == $asignaturee['id_asignatura'] && $asignaturee['nota'] > 0):

						return true;

					endif;

				endforeach;

			endforeach;

		endforeach;

		return false;
	}

	/**
	*
	*
	*/
	private function showValoration($asignature)
	{	
		$this->SetFont('Arial','B',8);

		$pahtImage = "http://agora.dev/Public/img/";
		$height = 0;
		$note = 0;
		$valoration = '';

		if($this->gradeBook['config'][0]['NumberValoration']):
			$note = $asignature['nota'];
			$valoration = strtoupper($asignature['valoration']);
		else: 
			$note = '';
			$valoration = '';
		endif;

		$ihs = ($asignature['ihs'] == 0) ? '' : $asignature['ihs'];
		
		if($this->gradeBook['config'][0]['showFaces'] == true):
			$height = 11;
		else:
			$height = $this->_h_c;
		endif;

		$this->Cell(140, $height, utf8_decode($asignature['name']), 'L',0, 'L');
		$this->Cell(10, $height, $ihs, 0,0, 'C');

		$this->Cell(17, $height, $note, 0,0, 'C');

		if($this->gradeBook['config'][0]['showFaces'] == true):

			$this->Image($pahtImage.strtolower($asignature['valoration']).'.jpg', 185, $this->GetY()+1, 9, 9, 'JPG');
			$this->Cell(0, $height, '', 'R', 0, 'C');

		else:

			$this->Cell(0, $this->_h_c, $valoration, 'R', 0, 'C');
		endif;

		$this->Ln($height);
	}

	/**
	*
	*
	*/
	private function showPerformance($performances = array())
	{
		if($this->gradeBook['config'][0]['showPerformance'] == 'indicators'):

			foreach($performances['indicators'] as $parameterKey => $parameter):

				if(!empty($parameter['performances']) && $this->gradeBook['config'][0]['performanceRating']):

					$this->SetFont('Arial','B',8);
					$this->Cell(0, $this->_h_c, utf8_decode($parameter['parameter']), 'LR',0,'L');
					$this->Ln();
				endif;
				

				foreach($parameter['performances'] as $indicatorsKey => $performance):

					$this->determineCell(
						utf8_decode('   * '.strtoupper($performance['observation'])), 
					'LR');

				endforeach;

			endforeach;

		else:

			foreach($performances['asignature'] as $parameterKey => $parameter):

				if(!empty($parameter['performances']) && $this->gradeBook['config'][0]['performanceRating']):

					$this->SetFont('Arial','B',8);
					$this->Cell(0, $this->_h_c, utf8_decode($parameter['parameter']), 'LR',0,'L');
					$this->Ln();
				endif;

				foreach($parameter['performances'] as $indicatorsKey => $performance):
					
					$this->determineCell(
						utf8_decode('   * '.strtoupper($performance['observation'])
						), 
					'LR');
				
				endforeach;

			endforeach;
		endif;
	}

	/**
	*
	*
	*/
	private function showTeacher($teacher)
	{	
		$this->SetFont('Arial','B',8);
		$this->Cell(0, $this->_h_c,'DOCENTE: '. utf8_decode($teacher), 'LR',0,'R');

		$this->Ln($this->_h_c);
	}
	/**
	*
	*
	*/
	private function determineCell($data, $border)
	{	
		$this->SetFont('Arial','',8);

		if(strlen($data) > 100 && strlen($data) > 0)
			$this->MultiCell(0, $this->_h_c, strip_tags($data), $border, 'L');
		else if(strlen($data) > 0)
		{
			$this->Cell(0, $this->_h_c, strip_tags($data), $border,0, 'L');
			$this->Ln(4);
		}
	}


	/**
	*
	*
	*/
	private function showCombineEvaluation()
	{
		$this->SetFont('Arial','B',8);

		$this->Cell( (96 + (22 * count($this->gradeBook['periods'])) ), $this->_h_c, utf8_decode('VALORACIONES ACUMULADAS DURANTE EL AÑO LECTIVO'), 1, 0, 'C');

		$this->Ln();

		$this->Cell(90, $this->_h_c, 'AREAS / ASIGNATURAS', 1,0, 'C');
		$this->Cell(6, $this->_h_c, 'IHS', 1,0, 'C');

		// RECORREMOS LOS PERIODOS
		foreach($this->gradeBook['periods'] as $periodKey => $period):

			$this->Cell(6, $this->_h_c, 'Fa', 1,0, 'C');
			$this->Cell(8, $this->_h_c, 'P'.$period['period'], 'LTB',0, 'C');
			$this->Cell(8, $this->_h_c, $period['percentage'], 'TRB', 0,'C');
		endforeach;

		$this->Ln();
		
		// 
		$this->Cell(90, $this->_h_c, '', 1,0, 'C');
		$this->Cell(6, $this->_h_c, '', 1,0, 'C');

		foreach($this->gradeBook['periods'] as $periodKey => $period):

			$this->Cell(6, $this->_h_c, '', 1,0, 'C');
			$this->Cell(8, $this->_h_c, 'VAL', 1,0, 'C');
			$this->Cell(8, $this->_h_c, 'SUP', 1, 0,'C');
		endforeach;

		$this->Ln();

		foreach($this->gradeBook['periods'] as $periodKey => $period):

			if($this->gradeBook['current_period'] == $period['period']):
				// MOSTRAMOS LAS AREAS 
				$this->showAreaTableDetail($period);
			endif;
		endforeach;
	}

	/**
	*
	*
	*/
	private function showTableDetail($period=array())
	{
		$this->Ln($this->_h_c);

		$this->SetFont('Arial','B',8);

		$this->Cell( (100 + (22 * count($this->gradeBook['periods'])) ), $this->_h_c, utf8_decode('CUADRO DETALLADO DURANTE EL AÑO LECTIVO'), 1, 0, 'C');

		$this->Ln();
		
		$this->Cell(96, $this->_h_c, 'AREAS / ASIGNATURAS', 1,0, 'C');
		// $this->Cell(6, $this->_h_c, 'IHS', 1,0, 'C');

		

		// MOSTRAMOS LOS DESEMPEÑOS
		$this->showPerformanceTableDetail();

		// 
		foreach($this->gradeBook['periods'] as $keyPeriod => $period):

			if($this->gradeBook['current_period'] == $period['period']):

				$this->showAreaTableDetail($period, 'tableDetail');

			endif;
		endforeach;
	}

	/**
	*
	*
	*/
	private function showPerformanceTableDetail()
	{
		foreach($this->gradeBook['eParameters'] as $keyParameters => $parameter):

			$name = explode(' ', $parameter['parametro']);
			$nameFull = substr($name[0], 0,1).". ".substr($name[1], 0,3);
			$this->Cell(11, $this->_h_c, $nameFull, 'LTB',0, 'C');
			$this->Cell(10, $this->_h_c, $parameter['peso']."%", 'TBR',0,'C');

		endforeach;

		// MOSTRAMOS EL CAMPO PARA LA VALORACIÓN FINAL
		$this->Cell(10, $this->_h_c, "VAL", 'TBR',0,'C');

		$this->Ln();
	}

	/**
	*
	*
	*/
	private function showAreaTableDetail($period=array(), $type = 'combinedEvaluation')
	{

		foreach($period['areas'] as $areaKey => $area):
			// FONDO PARA LAS CELDAS DE LAS AREAS
			$this->SetFillColor(230,230,230);
			$this->SetFont('Arial','B',8);

			$this->Cell(96 , $this->_h_c, 
				utf8_decode(
					substr($area['name'], 0, 58)
				),
			1,0, 'L', true);

			if($type == "combinedEvaluation"):
				// MOSTRAMOS LA VALORACION DE CADA PERIODO
				$this->showPeriodValorationByArea($area);

			endif;
			
			$this->Ln();

			// MOSTRAMOS LAS ASIGNATURAS
			$this->showAsignatureTableDetail($area['asignatures'], $type);

			
		endforeach;
	}

	/**
	*
	*
	*/
	private function showAsignatureTableDetail($asignatures=array(), $type='combinedEvaluation')
	{
		foreach($asignatures as $keyAsignature => $asignature):
			$this->SetFont('Arial','',8);

			if($this->determineShowValoration($asignature)):

				if($type == 'combinedEvaluation'):

					$this->Cell(90, $this->_h_c, 
					utf8_decode(substr($asignature['name'], 0, 51)),'TBL',0, 'L');

					$this->Cell(6, $this->_h_c, 
					($asignature['ihs']== 0) ? '' : $asignature['ihs'], 1,0, 'C');
					// MOSTRAMOS LA VALORACION DE LOS PERIODOS
					$this->showPeriodValorationByAsignature($asignature);

				else:
					$this->Cell(96, $this->_h_c, 
					utf8_decode(substr($asignature['name'], 0, 51)),'TBL',0, 'L');

					$this->showValorationPerformanceTableDetail($asignature);
				endif;

				$this->Ln();

			endif;
		endforeach;
	}

	/**
	*
	*
	*/
	private function showValorationPerformanceTableDetail($asignature=array())
	{

		foreach($asignature['indicators'] as $keyIndicators => $performance):

			$noteIndicator = 0;

			foreach($performance['indicators'] as $keySubIndicator => $indicator):

				$noteIndicator +=$indicator['value'];

			endforeach;
			$this->Cell(11, $this->_h_c, $noteIndicator, 'LTB',0, 'C');
			$this->Cell(10, $this->_h_c, $performance['percentage'], 'TBR',0,'C');

		endforeach;

		$this->Cell(10, $this->_h_c, $asignature['nota'], 'TBR',0,'C');
	}

	/**
	*
	*
	*/
	private function showPeriodValorationByArea($area=array())
	{

		foreach($this->gradeBook['periods'] as $periodKey => $period):

			$note = '';
			if(!empty($period['areas']) && $period['period'] <= $this->gradeBook['current_period']):
				foreach($period['areas'] as $areaKey => $areaa):


					if($area['id_area'] == $areaa['id_area'] && !$this->gradeBook['config'][0]['areasDisabled']):

						$note = $areaa['nota'];

					endif;

				endforeach;
			endif;

			$this->Cell(6, $this->_h_c, '', 1,0, 'C', true);

			$this->Cell(8, $this->_h_c, $note, 1,0, 'C', true);
					
			$this->Cell(8, $this->_h_c, '', 1, 0,'C', true);

		endforeach;
	}

	/**
	*
	*
	*/
	private function showPeriodValorationByAsignature($asignature=array())
	{
		foreach($this->gradeBook['periods'] as $periodKey => $period):

			$note = '';
			$noAttendace = '';
			$recovery_note = '';

			if(!empty($period['areas']) && $period['period'] <= $this->gradeBook['current_period']):
				foreach($period['areas'] as $areaKey => $areaa):

					foreach($areaa['asignatures'] as $asignaturee):
						if($asignature['id_asignatura'] == $asignaturee['id_asignatura']):

							if($asignaturee['recovery']['recovery_note'] > 0):
								$note = $asignaturee['recovery']['old_note'];
								$recovery_note = $asignaturee['recovery']['recovery_note'];	
							else:
								$note = ($asignaturee['nota'] > 0) ? $asignaturee['nota'] : ''; 
							
							endif;
							
							if($this->gradeBook['grade'] >= 10):
								$noAttendace = $asignaturee['inasistencia'];
							endif;

						endif;

					endforeach;

				endforeach;

			endif;
			
			$this->Cell(6, $this->_h_c, $noAttendace, 1,0, 'C');

			$this->Cell(8, $this->_h_c, $note, 1,0, 'C');
						
			$this->Cell(8, $this->_h_c, $recovery_note, 1, 0,'C');
		endforeach;
	}


	/**
	*
	*
	*/
	private function showPosition()
	{	
		$this->SetFont('Arial','B',8);

		$this->Cell(90 , $this->_h_c, 
				utf8_decode(
					'PUESTO EN EL GRUPO:'
				),
			1,0, 'R');
		$this->Cell(6 , $this->_h_c, '',	1,0, 'R');

		foreach($this->gradeBook['periods'] as $periodKey => $period):

			// MOSTRAMOS LOS PUESTOS DE CADA PERIODO
			$this->showPeriodPositions($period['period']);
		endforeach;
		$this->Ln();
	}

	/**
	*
	*
	*/
	private function showPeriodPositions($period)
	{

		// 
		foreach($this->positions as $keyPP => $pperiod):
			if(!empty($pperiod) && $period <= $this->gradeBook['current_period']):
				foreach($pperiod as $keyPosition => $position):
					
					if($position['period'] == $period && $position['id_student'] == $this->gradeBook['student']['id']):
						$this->Cell(6, $this->_h_c, '', 1,0, 'C');

						$this->Cell(8, $this->_h_c, $position['position'], 1,0, 'C');
									
						$this->Cell(8, $this->_h_c, '', 1, 0,'C');
					endif;
				endforeach;
			endif;
		endforeach;
	}

	/**
	*
	*
	*/
	private function showScore()
	{	
		$this->SetFont('Arial','B',8);

		$this->Cell(90 , $this->_h_c, 
				utf8_decode(
					'PROMEDIO GENERAL DEL ESTUDIANTE:'
				),
			1,0, 'R');
		$this->Cell(6 , $this->_h_c, '',	1,0, 'R');

		foreach($this->gradeBook['periods'] as $periodKey => $period):
			// MOSTRAMOS LOS PUESTOS DE CADA PERIODO
			$this->showPeriodScores($period['period']);
		endforeach;

		$this->Ln();
	}

	/**
	*
	*
	*/
	private function showPeriodScores($period)
	{

		foreach($this->positions as $keyPP => $pperiod):

			if(!empty($pperiod) && $period <= $this->gradeBook['current_period']):
				foreach($pperiod as $keyPosition => $position):
					
					if($position['period'] == $period && $position['id_student'] == $this->gradeBook['student']['id']):

						$this->Cell(6, $this->_h_c, '', 1,0, 'C');

						$this->Cell(8, $this->_h_c, $position['average'], 1,0, 'C');
									
						$this->Cell(8, $this->_h_c, '', 1, 0,'C');

					endif;

				endforeach;
			endif;
		endforeach;
	}

	/**
	*
	*
	*/
	private function showValueScale()
	{
		$this->Ln($this->_h_c * 2);

		$this->SetFont('Arial','B',8);
		
		$this->Cell(0, $this->_h_c, utf8_decode('ESCALA DE VALORACIÓN:'), 0, 0, '');
		$this->Ln($this->_h_c);

		$this->SetFont('Arial','',8);
		foreach ($this->valorations as $key => $valoration):
			
			$this->Cell(0, $this->_h_c, utf8_decode('DESEMPEÑO '.$valoration['val'].': '.$valoration['minimo'].' A '.$valoration['maximo']), 0,0, '');
			$this->Ln($this->_h_c);
		endforeach;
	}

	/**
	*
	*
	*/
	private function DoubleFace()
	{
		if($this->PageNo()% 2 != 0 && $this->PageNo() >= 1):
			$this->AddPage();
		endif;
	}

	/**
	*
	*
	*/
	private function showObservationsByAsignature($asignature = array())
	{	

		if(isset($asignature['observations'][0]['observation']) && $asignature['observations'][0]['observation']!= NULL):
			$this->SetFont('Arial','B',8);
			
			$this->Cell(0, $this->_h_c, 'OBSERVACIONES', 'LR', 0, 'L');
			
			$this->Ln();
			
			$this->determineCell('   * '.strtoupper($this->hideTilde($asignature['observations'][0]['observation'])),
			'LR');

			// $this->Ln();
		endif;
	}

	/**
	*
	*/
	private function showTotalAttendanceByPeriod()
	{
		
		$this->Ln($this->_h_c);

		$noAttendace = 0;

		foreach($this->gradeBook['periods'] as $key => $period):

			if($this->gradeBook['current_period'] == $period['period']):

				$noAttendace = $period['inasistencia'];

			endif;

		endforeach;

		if($noAttendace > 0):

			$this->SetFont('Arial','',8);

			$this->Cell(53, $this->_h_c, "Faltas de asistencia durante el periodo {$this->gradeBook['current_period']}: ", 0,0);

			$this->SetFont('Arial','B',8);

			$this->Cell(0, $this->_h_c, $noAttendace, 0, 0);

		endif;
	}

	private function showGeneralObservation($observations=array())
	{

		$this->Ln($this->_h_c*2);

		$this->SetFont('Arial','B',8);
		$this->Cell(0, $this->_h_c, 'OBSERVACIONES GENERALES:', 0,0, 'L');

		if(empty($observations)):	
			// MOSTRAMOS LAS LINEAS
			$this->Ln($this->_h_c * 1.5);
			$this->Cell(190, $this->_h_c, '', 'B',0, 'L');
			
			$this->Ln($this->_h_c * 1.5);
			$this->Cell(190, $this->_h_c, '', 'B',0, 'L');

			$this->Ln($this->_h_c * 1.5);
			$this->Cell(190, $this->_h_c, '', 'B',0, 'L');
		else:

			foreach($observations as $observation):
				$this->Ln();
				$this->determineCell($this->hideTilde($observation), 0);

			endforeach;

		endif;

		$this->Ln($this->_h_c * 4);
		// DIRECTOR DE GRUPO
	     $this->Cell(0,4, $this->group['doc_primer_nomb']." ".
	    	$this->group['doc_segundo_nomb']." ".
	    	$this->group['doc_primer_ape']." ".
	    	$this->group['doc_segundo_ape'], 0, 0, 'L');

	    $this->Ln();

	    $this->SetFont('Arial','',8);
	    $this->Cell(0, $this->_h_c, 'DIRECTOR DE GRUPO', 0,0);
	}

	// 
	/**
	*
	*
	*/
	private function hideTilde($text)
	{	
		$content = $text;
		$decoded = false;

		if(strstr($content, '&eacute;')){
			$content = str_replace('&eacute;', 'é', $content);
			$decoded = true;
		}
		
		if(strstr($content, '&Eacute;')){
			$content = str_replace('&Eacute;', 'É', $content);
			$decoded = true;
		}

		if(strstr($content, '&aacute;')){
			$content = str_replace('&aacute;', 'á', $content);
			$decoded = true;
		}
		
		if(strstr($content, '&Aacute;')){
			$content = str_replace('&Aacute;', 'Á', $content);
			$decoded = true;
		}
		
		if(strstr($content, '&iacute;')){
			$content = str_replace('&iacute;', 'í', $content);
			$decoded = true;
		}
		
		if(strstr($content, '&Iacute;')){
			$content = str_replace('&Iacute;', 'Í', $content);
			$decoded = true;
		}

		if(strstr($content, '&oacute;')){
			$content = str_replace('&oacute;', 'ó', $content);
			$decoded = true;
		}
		
		if(strstr($content, '&Oacute;')){
			$content = str_replace('&Oacute;', 'Ó', $content);
			$decoded = true;
		}
		
		if(strstr($content, '&uacute;')){
			$content = str_replace('&uacute;', 'ú', $content);
			$decoded = true;
		}
		
		if(strstr($content, '&Uacute;')){
			$content = str_replace('&Uacute;', 'Ú', $content);
			$decoded = true;
		}
		
		if(strstr($content, '&ntilde;')){
			$content = str_replace('&ntilde;', 'ñ', $content);
			$decoded = true;
		}
		
		if(strstr($content, '&Ntilde;')){
			$content = str_replace('&Ntilde;', 'Ñ', $content);
			$decoded = true;
		}
		
		if(strstr($content, '&ldquo;')){
			$content = str_replace('&ldquo;', '"', $content);
			$decoded = true;
		}
		
		if(strstr($content, '&rdquo;')){
			$content = str_replace('&rdquo;', '"', $content);
			$decoded = true;
		}
		
		if(strstr($content, '&hellip;')){
			$content = str_replace('&hellip;', '...', $content);
			$decoded = true;
		}	
		
		if(strstr($content, '&iquest;')){
			$content = str_replace('&iquest;', '¿', $content);
			$decoded = true;
		}
		
		if(strstr($content, '&nbsp;')){
			$content = str_replace('&nbsp;', ' ', $content);
			$decoded = true;
		}
		
		return ($decoded) ? utf8_decode($content) : $content;
	}

	function footer()
	{
		// Posición: a 1,5 cm del final
	    $this->SetY(-15);
	    // Arial italic 8
	    $this->SetFont('Arial','I',8);
	    // Número de página
	    $this->Cell(0, 4,utf8_decode('Ágora - Página ').$this->PageNo(),0,0,'C');
	}
}
?>