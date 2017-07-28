<?php

namespace App\ModelPDF;

use Lib\merge\FPDF as FPDF;
use App\Model\PerformanceModel as Performance;
/**
* 
*/
class GradeBookPDF extends FPDF
{
	
	public $tipo = 'INFORME DESCRIPTIVO Y VALORATIVO';
	public $model = '';
	public $date = '';
	public $maxPeriod = 0;
	public $areas = array();
	public $periods = array();
	public $calAreas = array();
	public $gradeBook = array();
	public $positions = array();
	public $valoration = array();
	public $infoStudent = array();
	public $institution = array();
	public $performancesData = array();
	public $infoGroupAndAsig = array();
	public $performanceIndicators = array();

	private $_prefixValoration = 'valoracion';

	public $Impescala = false;
	public $perdioFace = false;
	public $ImpDobleCara = false;
	public $AreasDisable = false;
	public $DesemDisable = false;
	public $DoceDisabled = false;
	public $CombinedEvaluation = false;

	private $_h_c = 4;


	function Header(){
		if($this->institution['logo_byte'] != NULL)
		{
			$pic = 'data:image/png;base64,'.base64_encode($this->institution["logo_byte"]);
			$info = getimagesize($pic);

		    // Logo
		    $this->Image($pic, 12, 14, 15, 15, 'png');
		}
		    
	    // Marca de agua

	    //Marco
	    $this->Cell(0, 24, '', 1,0);
	    $this->Ln(0);

	    // PRIMERA LINEA
	    $this->SetFont('Arial','B',12);
	    // Movernos a la dereca
	    // $this->Cell(50, 6, '', 1,0);
	    // Título
	    $this->Cell(0, 6, $this->institution['nombre_inst'], 0, 0, 'C');
	    // Movernos a la derecha
	    // $this->Cell(0, 6, '', 1,0);
	    // Salto de línea
	    $this->Ln(6);

	    // SEGUNDA LINEA
	    $this->SetFont('Arial','B',9);
	    // Título
	    $this->Cell(0,4, 'SEDE: '.strtoupper($this->infoGroupAndAsig['sede']), 0, 0, 'C');
	    // Movernos a la derecha
	    // Salto de línea
	    $this->Ln(4);

	    // TERCERA LINEA
	    // Título
	    $this->Cell(0, 4, strtoupper($this->tipo), 0, 0, 'C');
	    // Movernos a la derecha
	    // Salto de línea
	    $this->Ln(5);

	    // CUARTA LINEA
	    // Movernos a la derecha
	    $this->SetFont('Arial','B',8);
	    $this->Cell(17, 4, '', 0,0);
	    $this->Cell(90, 4, 'GRUPO: '.$this->infoGroupAndAsig['nombre_grupo'], 0, 0, 'L');
	    
	    // DIRECTOR DE GRUPO
	     $this->Cell(0,4, 'DIR. DE GRUPO: '.
	    	$this->infoGroupAndAsig['doc_primer_nomb']." ".
	    	$this->infoGroupAndAsig['doc_segundo_nomb']." ".
	    	$this->infoGroupAndAsig['doc_primer_ape']." ".
	    	$this->infoGroupAndAsig['doc_segundo_ape'], 0, 0, 'L');

	    // Salto de línea
	    $this->Ln(4);

	    // QUINTA LINEA
	    // Movernos a la derecha
	    $this->Cell(17, 4, '', 0,0);
	    $this->Cell(90, 4, 'ESTUDIANTE: '.
	    	$this->infoStudent['primer_ape_alu'].' '.
	    	$this->infoStudent['segundo_ape_alu'].' '.
	    	$this->infoStudent['primer_nom_alu'].' '.
	    	$this->infoStudent['segundo_nom_alu'], 0, 0, 'L');
	    // Título
	    // $this->Cell(120,4, 'DOCENTE: ', 0, 0, 'C');
	    // Movernos a la derecha
	    $this->Cell(0, 4, 'FECHA: '.$this->date, 0,0, 'L');
	    // Salto de línea
	    $this->Ln(8);

	    $this->subHeader();
	}

	private function subHeader(){
		$this->Cell(140, $this->_h_c, $this->tipo.utf8_decode(' PERIODO 1 - AÑO LECTIVO ').date('Y'), 1,0, 'L'); 
		$this->Cell(10, $this->_h_c, 'IHS', 1,0, 'C');
		$this->Cell(17, $this->_h_c, 'VAL', 1,0, 'C');
		$this->Cell(0, $this->_h_c, utf8_decode('DESEMPEÑO'), 1,0, 'C');

		$this->Ln($this->_h_c);
	}

	// 
	public function createGradeBook(){
		
		// 
		$this->config();
		// 
		$this->AddPage();	
		// 
		$this->createDetailBook();
		// 
		$this->Cell(0, $this->_h_c, '', 'T',0, 'C');

		if($this->CombinedEvaluation):
			// Creamos la tabla con las notas
			$this->createTableDetail();
		endif;

		// LastConfig
		$this->LastConfig();

		// 
		$this->createGeneralObservation();
	}

	private function config()
	{
		if($this->infoGroupAndAsig['id_grado'] == 4 && $this->institution['cod_dane'] == 176109003183)
			$this->_prefixValoration = 'valoracion_trans';
	}

	private function createDetailBook()
	{

		// Recorremos las areas
		foreach ($this->areas as $key => $value) 
		{

			$nArea = '';
			$valoracionA = 0;
			foreach ($this->calAreas as $keyCal => $valueCal) {
				if(utf8_encode($valueCal['Area']) == $value)
				{	
					$nArea = round($valueCal['Valoracion'], 1);
				}
			}

			if($nArea > 0)
			{
				$this->SetFillColor(230,230,230);
				$this->SetFont('Arial','B',8);
				
				if(!$this->AreasDisable){
					$this->Cell(150, $this->_h_c, utf8_decode($value), 'TBL',0, 'L', true);
					if(strlen($nArea) > 1)
					{
						$this->Cell(17, $this->_h_c, $nArea, 'TB',0, 'C', true);
					}else{
						$this->Cell(17, $this->_h_c, $nArea.'.0', 'TB',0, 'C', true);
					}

					// Funcion para obtener la valoracion
					$valoracionA = $this->getPrefixValoration($nArea);
							
					$this->Cell(0, $this->_h_c, strtoupper($valoracionA), 'TBR', 0, 'C', true);
				}else{
					$this->Cell(0, $this->_h_c, utf8_decode($value), 1,0, 'L', true);
				}

				$this->Ln($this->_h_c);

				// Recorremos las asignaturas
				foreach ($this->gradeBook as $key => $value2) 
				{
				
					if(utf8_decode($value) == $value2['area'])
					{
						$nota = round($value2['eval_1_per'],1);
						$valoracion = '';
						$prefixValoracion = '';
						
						// Verificamos si la nota esta en 0
						if($nota > 0): 
							$this->SetFont('Arial','B',8);
							// $this->Cell(140, $this->_h_c, $value2['asignatura'], 'L',0, 'L');
							// $this->Cell(10, $this->_h_c, $value2['ihs'], 0,0, 'C');
							$asignature = $value2['asignatura'];
							$ihs = $value2['ihs'];

							if(strlen($nota) > 1)
								$nota = $nota;
								// $this->Cell(17, $this->_h_c, $nota, 0,0, 'C');
							else
								$nota = $nota.'.0';

							// 
							// $this->Cell(17, $this->_h_c, $nota, 0,0, 'C');

							// Funcion para obtener la valoracion
							$prefixValoracion = $this->getPrefixValoration($nota);
							// Valoracion para mostrar los desempeños
							$valoracion = $this->getValoration($nota);
							
							// Mostramos la valoracion
							$this->showValoration($asignature, $ihs, $nota, $prefixValoracion);

							// Desempeño por los indicadores
							$this->SetFont('Arial','',8);
							if($this->DesemDisable)
								$this->showPerformancesByIndicators($value2);
							else
								$this->showPerformancesByAsignature($value2, $valoracion);

							// Preguntamos si la opcion para mostrar al docente est habilitada
							if($this->DoceDisabled)
								$this->showTeacher($value2);

						endif;
					}
				}
			}
		}
	}

	private function showValoration($asignature='', $ihs='', $val='', $valoration){

		$pahtImage = "http://agora.dev/Public/img/";
		$height = 0;

		if($ihs == 0)
			$ihs = '';
		
		if($this->perdioFace):
			$height = 11;
			$val = '';
		else:
			$height = $this->_h_c;
		endif;

		$this->Cell(140, $height, $asignature, 'L',0, 'L');
		$this->Cell(10, $height, $ihs, 0,0, 'C');
		$this->Cell(17, $height, $val, 0,0, 'C');

		if($this->perdioFace):
			$this->Image($pahtImage.strtolower($valoration).'.jpg', 185, $this->GetY()+1, 9, 9, 'JPG');
			$this->Cell(0, $height, '', 'R', 0, 'C');
		else:
			$this->Cell(0, $this->_h_c, strtoupper($valoration), 'R', 0, 'C');
		endif;

		$this->Ln($height);
	}

	private function getPrefixValoration($note)
	{
		$valoration = '';
		foreach ($this->valoration as $key2 => $value3) 
		{
								
			if($note >= $value3['minimo'] && $note <= $value3['maximo'])
				$valoration = $value3[$this->_prefixValoration];
			
			else if($note == NULL || $note == 0)
					$valoration = 'Bajo';
		}

		return $valoration;
	}

	private function getValoration($note)
	{
		$valoration = '';
		foreach ($this->valoration as $key2 => $value3) 
		{
								
			if($note >= $value3['minimo'] && $note <= $value3['maximo'])
				$valoration = $value3['valoracion'];
			
			else if($note == NULL || $note == 0)
					$valoration = 'Bajo';
		}

		return $valoration;
	}

	private function showTeacher($data=array())
	{	
		$this->SetFont('Arial','B',8);
		$this->Cell(0, $this->_h_c,'DOCENTE: '. 
			$data['doc_primer_nomb']." ".
			$data['doc_segundo_nomb']." ".
			$data['doc_primer_ape']." ".
			$data['doc_segundo_ape'], 'LR',0,'R');

		$this->Ln($this->_h_c);
	}
	private function showPerformancesByAsignature($data_asignature=array(), $valoration)
	{
		foreach ($this->performancesData as $keyD => $valueD) 
		{
			if(
				$this->infoGroupAndAsig['id_grupo'] == $valueD['id_grupo'] &&
				$data_asignature['id_asignatura'] == $valueD['id_asignatura'] && $valueD['periodos'] == 1
			):
			
				if(strtolower($valoration) == 'superior')
					$this->determineHeihtCell($valueD['superior'], 'LR');
				else
					if($valoration != '')
						$this->determineHeihtCell($valueD[strtolower($valoration)], 'LR');
			endif;
		}
	}
	private function showPerformancesByIndicators($data_asignature=array())
	{
		foreach ($this->performancesData as $keyP => $valueP) {
						
			$notaDesem = 0;
			$valoracion = '';

			if($data_asignature['id_asignatura'] == $valueP['id_asignatura']):
				$notaDesem = $data_asignature[$valueP['posicion']];

				foreach ($this->valoration as $keyV => $valueV) {
								
					if(
						$notaDesem >= $valueV['minimo'] &&
						$notaDesem <= $valueV['maximo']	
					):
						$valoracion = $valueV['valoracion'];
					endif;
				}

									
				if(strtolower($valoracion) == 'superior')
					$this->determineHeihtCell($valueP['superior'], 'LR');
				else
					if($valoracion != '')
						$this->determineHeihtCell($valueP[strtolower($valoracion)], 'LR');
			endif;
		}
	}

	private function determineHeihtCell($data, $border)
	{	
		if(strlen($data) > 100)
			$this->MultiCell(0, $this->_h_c, '   * '.strip_tags($data), $border, 'L');
		else
		{
			$this->Cell(0, $this->_h_c, '   * '.strip_tags($data), $border,0, 'L');
			$this->Ln(4);
		}
	}

	private function createTableDetail()
	{
		$this->Ln($this->_h_c);

		$this->SetFont('Arial','B',8);

		$this->Cell( (96 + (22 * count($this->periods)) ), $this->_h_c, utf8_decode('VALORACIONES ACUMULADAS DURANTE EL AÑO LECTIVO'), 1, 0, 'C');

		$this->Ln($this->_h_c);

		// $this->Cell(10, $this->_h_c, '', 0, 0, '');
		$this->Cell(90, $this->_h_c, 'AREAS / ASIGNATURAS', 1,0, 'C');
		$this->Cell(6, $this->_h_c, 'IHS', 1,0, 'C');

		foreach ($this->periods as $key => $value) 
		{	
			if($value['peso'] != 0)
			{
				$this->Cell(6, $this->_h_c, 'Fa', 1,0, 'C');
				$this->Cell(8, $this->_h_c, 'P'.($key+1), 'LTB',0, 'C');
				$this->Cell(8, $this->_h_c, $value['peso'].'%', 'TRB', 0,'C');
			}
		}

		$this->Ln($this->_h_c);

		// 
		$this->Cell(90, $this->_h_c, '', 1,0, 'C');
		$this->Cell(6, $this->_h_c, 'ihs', 1,0, 'C');
		
		foreach ($this->periods as $key => $value) 
		{	
			if($value['peso'] != 0)
			{
				$this->Cell(6, $this->_h_c, '', 1,0, 'C');
				$this->Cell(8, $this->_h_c, 'Val', 1,0, 'C');
				$this->Cell(8, $this->_h_c, 'Sup', 1, 0,'C');
			}
		}
		

		// Salto
		$this->Ln($this->_h_c);
		
		// Imprimios las areas
		foreach ($this->areas as $key => $value) 
		{

			$nArea = '';

			foreach ($this->calAreas as $keyCal => $valueCal) {
				if(utf8_encode($valueCal['Area']) == $value)
				{	
						
					$nArea = round($valueCal['Valoracion'], 1);
				}
			}

			if($nArea > 0)
			{
				$this->SetFont('Arial','B',8);
				$this->SetFillColor(230,230,230);
				$this->Cell( 90, $this->_h_c, substr(utf8_decode($value), 0, 49), 1,0, 'L', true);

				$this->Cell(6, $this->_h_c, '', 1,0, 'C', true);
				$this->Cell(6, $this->_h_c, '', 1,0, 'C', true);

				if(strlen($nArea) > 1)
				{
					$this->Cell(8, $this->_h_c, $nArea, 'TBR',0, 'C', true);
					$this->Cell(8, $this->_h_c, '', 'TBR',0, 'C', true);
				}else{
					$this->Cell(8, $this->_h_c, $nArea.'.0', 'TBR',0, 'C', true);
					$this->Cell(8, $this->_h_c, '', 'TBR',0, 'C', true);
				}

				$this->Ln($this->_h_c);

				// Asignaturas
				foreach ($this->gradeBook as $keyG => $valueG) 
				{
					
					if(utf8_decode($value) == $valueG['area'])
					{
						$nota = round($valueG['eval_1_per'],1);
						$faltas = ($valueG['inasistencia_p1'] > 0) ? $valueG['inasistencia_p1'] : '';
						// Cambio
						if($nota > 0):
							$this->SetFont('Arial','',8);
							// $this->Cell(10, $this->_h_c, '', 0, 0, '');
							$this->Cell(90, $this->_h_c, substr($valueG['asignatura'], 0, 50), 1,0, 'L');
							$this->Cell(6, $this->_h_c, $valueG['ihs'], 1,0, 'C');

							$this->Cell(6, $this->_h_c, $faltas, 1,0, 'C');

							if(strlen($nota) > 1)
								$this->Cell(8, $this->_h_c, $nota, 1,0, 'C');
							else
								$this->Cell(8, $this->_h_c, $nota.'.0', 1,0, 'C');

							$this->Cell(8, $this->_h_c, '', 1,0, 'C');
							// $this->Cell(6, $this->_h_c, '', 'TRB', 0,'C');

							// Estatico
							// $cont = 0;
							foreach ($this->periods as $keyP => $valueP) 
							{
								if($valueP['peso'] !=0 && $valueP['periodos'] != 1)
								{	
									$this->Cell(6, $this->_h_c, '', 1,0, 'C');
									$this->Cell(8, $this->_h_c, '', 1,0, 'C');
									$this->Cell(8, $this->_h_c, '', 1,0, 'C');
								}
							}

							$this->Ln($this->_h_c);
						endif;
					}
				}
			}
		}

		$this->Cell(90, $this->_h_c, utf8_decode('PESTO EN EL GRUPO'), 1, 0, 'C');
		$this->Cell(6, $this->_h_c, '', 1,0, 'C');
		
		
		foreach($this->positions as $position):
			foreach ($this->periods as $key => $value):
		
				if($value['peso'] != 0):
					if($this->infoStudent['idstudents'] == $position['id_student'] && $value['periodos'] == $position['period']):

						$this->Cell(6, $this->_h_c, '', 1,0, 'C');
						$this->Cell(8, $this->_h_c, $position['position'], 1,0, 'C');
						$this->Cell(8, $this->_h_c, '', 1, 0,'C');
					endif;
				endif;

				endforeach;
			
		endforeach;
	}

	private function createGeneralObservation()
	{

		$this->Ln($this->_h_c * 2);

		$this->SetFont('Arial','B',8);
		$this->Cell(0, $this->_h_c, 'OBSERVACIONES GENERALES:', 0,0, 'L');

		$this->SetFont('Arial','',8);
		$this->Ln($this->_h_c * 1.5);
		$this->Cell(5, $this->_h_c, '', 0, 0, '');
		$this->Cell(190, $this->_h_c, '', 'B',0, 'L');
		
		$this->Ln($this->_h_c * 1.5);
		$this->Cell(5, $this->_h_c, '', 0, 0, '');
		$this->Cell(190, $this->_h_c, '', 'B',0, 'L');

		$this->Ln($this->_h_c * 1.5);
		$this->Cell(5, $this->_h_c, '', 0, 0, '');
		$this->Cell(190, $this->_h_c, '', 'B',0, 'L');

		
		$this->Ln($this->_h_c * 4);
		$this->SetFont('Arial','B',8);
		$this->Cell(0,$this->_h_c, 
			$this->infoGroupAndAsig['doc_primer_nomb']." ".
	    	$this->infoGroupAndAsig['doc_segundo_nomb']." ".
	    	$this->infoGroupAndAsig['doc_primer_ape']." ".
	    	$this->infoGroupAndAsig['doc_segundo_ape'], 0, 0);

		
		$this->Ln($this->_h_c);
		$this->SetFont('Arial','',8);
		$this->Cell(0, $this->_h_c, 'DIRECTOR DE GRUPO', 0,0);

		if($this->ImpDobleCara)
		{	
			if($this->PageNo()% 2 != 0 && $this->PageNo() > 2)
				$this->AddPage();
		}
	}

	private function LastConfig()
	{
		
		if($this->Impescala)
		{	
			$this->Ln($this->_h_c * 2);

			$this->SetFont('Arial','B',8);
			
			$this->Cell(0, $this->_h_c, utf8_decode('ESCALA DE VALORACIÓN:'), 0, 0, '');
			$this->Ln($this->_h_c);

			$this->SetFont('Arial','',8);
			foreach ($this->valoration as $key => $value) {
				$this->Cell(0, $this->_h_c, utf8_decode('DESEMPEÑO '.$value['val'].': '.$value['minimo'].' A '.$value['maximo']), 0,0, '');
				$this->Ln($this->_h_c);
			}
		}
	}

	// Pie de página
	function Footer(){
	    // Posición: a 1,5 cm del final
	    $this->SetY(-15);
	    // Arial italic 8
	    $this->SetFont('Arial','I',8);
	    // Número de página
	    $this->Cell(0,$this->_h_c,utf8_decode('Ágora - Página ').$this->PageNo(),0,0,'C');
	}
}
?>