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
	    $this->Cell(90, 4, 'ESTUDIANTE: '.$this->gradeBook['student']['name'], 0, 0, 'L');

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
			$this->showTableValoration();

			// 
			$this->showScore();

			// MOSTRAMOS EL PUESTO OCUPADO
			$this->showPosition();
		endif;

		if($this->gradeBook['config'][0]['tableDetail']):
			// MOSTRAMOS EL CUADRO DETALLADO

		endif;

		// MOSTRAR ESCALA VALORATIVA
		if($this->gradeBook['config'][0]['valorationScale']):
			$this->showValueScale();
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

			// PREGUNTAMOS SI LAS AREAS NO SE DESACTIVAN
			if(!$this->gradeBook['config'][0]['areasDisabled']):

				$this->Cell(150, $this->_h_c, utf8_decode($area['name']), 'TBL',0, 'L', true);
				$this->Cell(17, $this->_h_c, $area['nota'], 'TB',0, 'C', true);
				$this->Cell(0, $this->_h_c, $area['valoration'], 'TBR', 0, 'C', true);
			
			else:

				$this->Cell(0, $this->_h_c, utf8_decode($area['name']), 1,0, 'L', true);

			endif;

			$this->Ln();

			// RECORREMOS LAS ASIGNATURAS
			$this->showAsignature($area['asignatures']);

			
		endforeach;
	}

	/**
	*
	*
	*/
	private function showAsignature($asignatires = array())
	{	
		// 
		$this->SetFont('Arial','B',8);
		foreach($asignatires as $keyAsignature => $asignature):

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
		endforeach;
	}

	/**
	*
	*
	*/
	private function showValoration($asignature)
	{
		$pahtImage = "http://agora.dev/Public/img/";
		$height = 0;

		// if($ihs == 0)
		// 	$ihs = '';
		
		// if($this->perdioFace):
		// 	$height = 11;
		// 	$val = '';
		// else:
		// 	$height = $this->_h_c;
		// endif;

		$this->Cell(140, $this->_h_c, utf8_decode($asignature['name']), 'LT',0, 'L');
		$this->Cell(10, $this->_h_c,
			($asignature['ihs'] == 0) ? '' : $asignature['ihs'], 
		'T',0, 'C');
		$this->Cell(17, $this->_h_c, $asignature['nota'], 'T',0, 'C');

		// if($this->perdioFace):
		// 	$this->Image($pahtImage.strtolower($valoration).'.jpg', 185, $this->GetY()+1, 9, 9, 'JPG');
		// 	$this->Cell(0, $height, '', 'R', 0, 'C');
		// else:
			$this->Cell(0, $this->_h_c, strtoupper($asignature['valoration']), 'RT', 0, 'C');
		// endif;

		$this->Ln();
	}

	/**
	*
	*
	*/
	private function showPerformance($performances = array())
	{
		if($this->gradeBook['config'][0]['showPerformance'] == 'indicators'):

			foreach($performances['indicators'] as $indicatorsKey => $performance):
			
				$this->determineCell(
					utf8_decode($performance['observation']
					), 
				'LR');

			endforeach;
		else:
			foreach($performances['asignature'] as $indicatorsKey => $performance):
				
				$this->determineCell(
					utf8_decode($performance['observation']
					), 
				'LR');
			
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
		$this->Cell(0, $this->_h_c,'DOCENTE: '. $teacher, 'LR',0,'R');

		$this->Ln($this->_h_c);
	}
	/**
	*
	*
	*/
	private function determineCell($data, $border)
	{	
		$this->SetFont('Arial','',8);

		if(strlen($data) > 100)
			$this->MultiCell(0, $this->_h_c, '   * '.strip_tags($data), $border, 'L');
		else
		{
			$this->Cell(0, $this->_h_c, '   * '.strip_tags($data), $border,0, 'L');
			$this->Ln(4);
		}
	}


	/**
	*
	*
	*/
	private function showTableValoration()
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
	private function showTableDetail()
	{

	}
	
	/**
	*
	*
	*/
	private function showAreaTableDetail($period=array())
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

			// MOSTRAMOS LA VALORACION DE CADA PERIODO
				$this->showPeriodValorationByArea($area);

			$this->Ln();

			// MOSTRAMOS LAS ASIGNATURAS
			$this->showAsignatureTableDetail($area['asignatures']);

			
		endforeach;
	}

	/**
	*
	*
	*/
	private function showAsignatureTableDetail($asignatures=array())
	{
		foreach($asignatures as $keyAsignature => $asignature):
		$this->SetFont('Arial','',8);

			$this->Cell(90, $this->_h_c, 
				utf8_decode(
					substr($asignature['name'], 0, 51)
				),
			'TBL',0, 'L');

			$this->Cell(6, $this->_h_c, 
				($asignature['ihs']== 0) ? '' : $asignature['ihs']
			, 1,0, 'C');

			// MOSTRAMOS LA VALORACION DE LOS PERIODOS
			$this->showPeriodValorationByAsignature($asignature);

			$this->Ln();
		endforeach;
	}

	/**
	*
	*
	*/
	private function showPeriodValorationByArea($area=array())
	{

		foreach($this->gradeBook['periods'] as $periodKey => $period):

			$note = '';
			if(!empty($period['areas'])):
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
			$inasistencia = '';

			if(!empty($period['areas'])):
				foreach($period['areas'] as $areaKey => $areaa):

					foreach($areaa['asignatures'] as $asignaturee):
						if($asignature['id_asignatura'] == $asignaturee['id_asignatura']):

							if($asignaturee['recovery']['recovery_note'] > 0):
								$note = $asignaturee['recovery']['old_note'];
								$recovery_note = $asignaturee['recovery']['recovery_note'];	
							else:
								$note = $asignaturee['nota'];
							
							endif;
							
							$noAttendace = $asignaturee['inasistencia'];
						endif;
					endforeach;

				endforeach;

			endif;
			
			$this->Cell(6, $this->_h_c, $inasistencia, 1,0, 'C');

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

		// MOSTRAMOS LOS PUESTOS DE CADA PERIODO
		$this->showPeriodPositions();

		$this->Ln();
	}

	/**
	*
	*
	*/
	private function showPeriodPositions()
	{

		foreach($this->gradeBook['periods'] as $periodKey => $period):

			$this->Cell(6, $this->_h_c, '', 1,0, 'C');

			$this->Cell(8, $this->_h_c, $period['position'], 1,0, 'C');
						
			$this->Cell(8, $this->_h_c, '', 1, 0,'C');

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

		// MOSTRAMOS LOS PUESTOS DE CADA PERIODO
		$this->showPeriodScores();

		$this->Ln();
	}

	/**
	*
	*
	*/
	private function showPeriodScores()
	{

		foreach($this->gradeBook['periods'] as $periodKey => $period):

			$this->Cell(6, $this->_h_c, '', 1,0, 'C');

			$this->Cell(8, $this->_h_c, $period['pgg'], 1,0, 'C');
						
			$this->Cell(8, $this->_h_c, '', 1, 0,'C');

		endforeach;
	}

	private function showValueScale()
	{
		$this->Ln($this->_h_c * 2);

		$this->SetFont('Arial','B',8);
		
		$this->Cell(0, $this->_h_c, utf8_decode('ESCALA DE VALORACIÓN:'), 0, 0, '');
		$this->Ln($this->_h_c);

		$this->SetFont('Arial','',8);
		foreach ($this->gradeBook['valorations'] as $key => $valoration):
			
			$this->Cell(0, $this->_h_c, utf8_decode('DESEMPEÑO '.$valoration['val'].': '.$valoration['minimo'].' A '.$valoration['maximo']), 0,0, '');
			$this->Ln($this->_h_c);
		endforeach;
	}

	private function DoubleFace()
	{
		if($this->PageNo()% 2 != 0 && $this->PageNo() >= 1):
			$this->AddPage();
		endif;
	}

	private function showObservationsByAsignature($asignature = array())
	{	
		

		if(isset($asignature['observations'][0]['observation']) && $asignature['observations'][0]['observation']!= NULL):
			$this->SetFont('Arial','B',8);
			
			$this->Cell(0, $this->_h_c, 'Observaciones', 'LR', 0, 'L');
			
			$this->Ln();
			
			$this->determineCell($asignature['observations'][0]['observation'],
			'LR');

			$this->Ln();
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
			$this->Cell(5, $this->_h_c, '', 0, 0, '');
			$this->Cell(190, $this->_h_c, '', 'B',0, 'L');
			
			$this->Ln($this->_h_c * 1.5);
			$this->Cell(5, $this->_h_c, '', 0, 0, '');
			$this->Cell(190, $this->_h_c, '', 'B',0, 'L');

			$this->Ln($this->_h_c * 1.5);
			$this->Cell(5, $this->_h_c, '', 0, 0, '');
			$this->Cell(190, $this->_h_c, '', 'B',0, 'L');
		else:

			foreach($observations as $observation):
				$this->Ln();
				$this->determineCell($observation['observaciones'], 0);

			endforeach;

		endif;
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