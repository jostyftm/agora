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
	public $periods = array();
	public $areas = array();
	public $calAreas = array();
	public $gradeBook = array();
	public $valoration = array();
	public $infoStudent = array();
	public $institution = array();
	public $performancesData = array();
	public $infoGroupAndAsig = array();

	public $Impescala = false;
	public $ImpDobleCara = false;
	public $AreasDisable = false;

	private $_h_c = 4;


	function Header(){
		$pic = 'data:image/png;base64,'.base64_encode($this->institution["logo_byte"]);
		$info = getimagesize($pic);

	    // Logo
	    $this->Image($pic, 12, 14, 15, 15, 'png');
		    
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
	    
	    // Título
	    $this->Cell(0,4, 'DIR. DE GRUPO: '.$this->infoGroupAndAsig['director_grupo'], 0, 0, 'L');
	    // Movernos a la derecha
	    // $this->Cell(0, 4, 'MES_____________________', 0,0);
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
		$this->AddPage();	
		$this->createDetailBook();
		// 
		$this->Cell(0, $this->_h_c, '', 'T',0, 'C');

		// Creamos la tabla con las notas
		$this->createTableDetail();

		// LastConfgi
		$this->LastConfig();

		// 
		$this->createGeneralObservation();

	}

	private function createDetailBook()
	{
		$performance = new Performance(DB);

		foreach ($this->areas as $key => $value) {

			$nArea = '';

			foreach ($this->calAreas as $keyCal => $valueCal) {
				if(utf8_encode($valueCal['Area']) == $value)
				{	
					$nArea = round($valueCal['Valoracion'], 1);
				}
			}

			if($nArea > 0){
				$this->SetFillColor(230,230,230);
				$this->SetFont('Arial','B',8);
				$this->Cell(150, $this->_h_c, utf8_decode($value), 'TBL',0, 'L', true);
				

				if(strlen($nArea) > 1)
				{
					$this->Cell(17, $this->_h_c, $nArea, 'TB',0, 'C', true);
				}else{
					$this->Cell(17, $this->_h_c, $nArea.'.0', 'TB',0, 'C', true);
				}

				foreach ($this->valoration as $key2 => $value3) 
				{			
					if(
						$nArea >= $value3['minimo'] &&
						$nArea <= $value3['maximo']
					)
					{
						$valoracionA = $value3['valoracion'];
					}
					else if($nArea == NULL || $nArea == 0)
					{
						$valoracionA = 'Bajo';
					}
				}
						
				$this->Cell(0, $this->_h_c, $valoracionA, 'TBR', 0, 'C', true);

				$this->Ln($this->_h_c);

				foreach ($this->gradeBook as $key => $value2) {
				
					if(utf8_decode($value) == $value2['area'])
					{
						$nota = round($value2['periodo1'],1);
						$valoracion = '';
						
						$this->SetFont('Arial','B',8);
						$this->Cell(140, $this->_h_c, $value2['asignatura'], 'L',0, 'L');
						$this->Cell(10, $this->_h_c, $value2['ihs'], 0,0, 'C');

						if(strlen($nota) > 1)
							$this->Cell(17, $this->_h_c, $nota, 0,0, 'C');
						else
							$this->Cell(17, $this->_h_c, $nota.'.0', 0,0, 'C');

						foreach ($this->valoration as $key2 => $value3) {
							
							if(
								$nota >= $value3['minimo'] &&
								$nota <= $value3['maximo']
							)
							{
								$valoracion = $value3['valoracion'];
							}
							else if($nota == NULL || $nota == 0)
							{
								$valoracion = 'Bajo';
							}
						}

						$this->Cell(0, $this->_h_c, $valoracion, 'R', 0, 'C');

						$this->Ln($this->_h_c);

						foreach ($this->performancesData as $keyD => $valueD) 
						{
							if(
								$this->infoGroupAndAsig['id_grado'] == $valueD['id_grado'] &&
								$value2['id_asignatura'] == $valueD['id_asignatura'] && $valueD['periodos'] == 1
							)
							{
								$this->SetFont('Arial','',8);
								if(strtolower($valoracion) == 'superior')
									$this->determineHeihtCell($valueD['superior'], 'LR');
								else
									if($valoracion != '')
										$this->determineHeihtCell($valueD[strtolower($valoracion)], 'LR');
							}
						}
					}
				}
			}
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

		// $this->Cell(10, $this->_h_c, '', 0, 0, '');
		$this->Cell( (102 + (22 * count($this->periods)) ), $this->_h_c, utf8_decode('VALORACIONES ACUMULADAS DURANTE EL AÑO LECTIVO'), 1, 0, 'C');

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

		$this->Cell(6, $this->_h_c, 'Fa', 'TRB', 0,'C');

		$this->Ln($this->_h_c);

		// $this->Cell(10, $this->_h_c, '', 0, 0, '');
		$this->Cell(90, $this->_h_c, '', 1,0, 'C');
		$this->Cell(6, $this->_h_c, '', 1,0, 'C');
		$this->Cell(6, $this->_h_c, '', 1,0, 'C');
		$this->Cell(8, $this->_h_c, 'Val', 1,0, 'C');
		$this->Cell(8, $this->_h_c, 'Sup', 1, 0,'C');		

		// Estatico Pendiente
		foreach ($this->periods as $key => $value) 
		{
			if($value['peso'] != 0 && $value['periodos'] != 1)
			{
				$this->Cell(6, $this->_h_c, '', 'BLR', 0,'C');
				$this->Cell(8, $this->_h_c, 'Val', 1,0, 'C');
				$this->Cell(8, $this->_h_c, 'Sup', 1, 0,'C');
			}
		}

		$this->Cell(6, $this->_h_c, '', 'TRB', 0,'C');

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
				}else{
					$this->Cell(8, $this->_h_c, $nArea.'.0', 'TBR',0, 'C', true);
				}

				$this->Ln($this->_h_c);

				// Asignaturas
				foreach ($this->gradeBook as $keyG => $valueG) 
				{
					
					if(utf8_decode($value) == $valueG['area'])
					{
						$nota = round($valueG['periodo1'],1);
						$faltas = ($valueG['inasistencia_p1'] > 0) ? $valueG['inasistencia_p1'] : '';
						// 
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
						$this->Cell(6, $this->_h_c, '', 'TRB', 0,'C');

						// Estatico
						// $cont = 0;
						foreach ($this->periods as $keyP => $valueP) 
						{
							if($valueP['peso'] !=0 && $valueP['periodos'] != 1)
							{	
								$this->Cell(8, $this->_h_c, '', 1,0, 'C');
								$this->Cell(8, $this->_h_c, '', 1,0, 'C');
								$this->Cell(6, $this->_h_c, '', 1,0, 'C');
							}
						}

						$this->Ln($this->_h_c);
					}
				}
			}
		}

		// // $this->Cell(10, $this->_h_c, '', 0, 0, '');
		// $this->Cell(90, $this->_h_c, 'PROMEDIO GENERAL:', 1,0,'');
		// $this->Cell(6, $this->_h_c, '', 'BT',0, 'C');
		// $this->Cell(6, $this->_h_c, '', 'BT', 0,'C');
		// foreach ($this->periods as $keyP => $valueP) 
		// {
		// 	// if($valueP['peso'] !=0 && $valueP['periodos'] != 1)
		// 	// {	
		// 		$this->Cell(8, $this->_h_c, '', 1,0, 'C');
		// 		$this->Cell(8, $this->_h_c, '', 'BT',0, 'C');
		// 		if( ($keyP+1) == count($this->periods))
		// 			$this->Cell(6, $this->_h_c, '','BTR',0, 'C');
		// 		else
		// 			$this->Cell(6, $this->_h_c, '', 'BT',0, 'C');
		// 	// }
		// }


		// $this->Ln($this->_h_c);
		// // $this->Cell(10, $this->_h_c, '', 0, 0, '');
		// $this->Cell(90, $this->_h_c, 'PUESTO EN EL GRUPO:',1,0,'');
		// $this->Cell(6, $this->_h_c, '', 'BT',0, 'C');
		// $this->Cell(6, $this->_h_c, '', 'BT', 0,'C');
		// foreach ($this->periods as $keyP => $valueP) 
		// {
		// 	// if($valueP['peso'] !=0 && $valueP['periodos'] != 1)
		// 	// {	
		// 		$this->Cell(8, $this->_h_c, '', 1,0, 'C');
		// 		$this->Cell(8, $this->_h_c, '', 'BT',0, 'C');
		// 		if( ($keyP+1) == count($this->periods))
		// 			$this->Cell(6, $this->_h_c, '','BTR',0, 'C');
		// 		else
		// 			$this->Cell(6, $this->_h_c, '', 'BT',0, 'C');
		// 	// }
		// }

		// $this->Ln($this->_h_c);
		// $this->Cell(0, $this->_h_c, '', 0, 0, 'C');
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
		$this->Cell(0,$this->_h_c, $this->infoGroupAndAsig['director_grupo'], 0, 0);

		
		$this->Ln($this->_h_c);
		$this->SetFont('Arial','',8);
		$this->Cell(0, $this->_h_c, 'DIRECTOR DE GRUPO', 0,0);
		// if(
		// 	$this->infoGroupAndAsig['id_grado'] >= 1 && 
		// 	$this->infoGroupAndAsig['id_grado'] <= 9
		// )
		// {
		// 	$this->Cell(0, $this->_h_c, 'Faltas de asistencia durante el periodo: ', $border,0, 'L');
		// }
		// else
		// {
		// 	$this->Cell(0, $this->_h_c, '  ', $border,0, 'L');
		// }
	}

	private function LastConfig()
	{
		if($this->ImpDobleCara)
		{
			if($this->PageNo()% 2 != 0)
				$this->AddPage();
		}

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