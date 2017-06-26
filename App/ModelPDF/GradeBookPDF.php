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
	public $maxPeriod = 0;
	public $areas = array();
	public $gradeBook = array();
	public $valoration = array();
	public $infoStudent = array();
	public $institution = array();
	public $infoGroupAndAsig = array();

	private $_h_c = 4;


	function Header(){
		$pic = 'data:image/png;base64,'.base64_encode($this->institution["logo_byte"]);
		$info = getimagesize($pic);

	    // Logo
	    $this->Image($pic, 6, 4, 20, 20, 'png');
		    
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
	    $this->Ln(4);

	    // CUARTA LINEA
	    $this->SetFont('Arial','',8);
	    // Movernos a la derecha
	    $this->Cell(25, 4, '', 0,0);
	    $this->Cell(25, 4, 'GRUPO: '.$this->infoGroupAndAsig['nombre_grupo'], 0, 0, 'L');
	    
	    // Título
	    $this->Cell(0,4, ' DIRECTOR DE GRUPO: '.$this->infoGroupAndAsig['director_grupo'], 0, 0, 'R');
	    // Movernos a la derecha
	    // $this->Cell(0, 4, 'MES_____________________', 0,0);
	    // Salto de línea
	    $this->Ln(4);

	    // QUINTA LINEA
	    // Movernos a la derecha
	    $this->SetFont('Arial','B',8);
	    $this->Cell(25, 4, '', 0,0);
	    $this->Cell(115, 4, 'ESTUDIANTE: '.$this->infoStudent['estudiante'].' '.$this->infoStudent['idstudents'], 0, 0, 'L');
	    // Título
	    // $this->Cell(120,4, 'DOCENTE: ', 0, 0, 'C');
	    // Movernos a la derecha
	    $this->SetFont('Arial','',8);
	    $this->Cell(0, 4, utf8_decode('AÑO LECTIVO ').date('Y'), 0,0, 'L');
	    // Salto de línea
	    $this->Ln(8);

	    $this->subHeader();
	}

	private function subHeader(){
		$this->Cell(150, $this->_h_c, 'INFORME DE EVALUACION PERIODO 1', 1,0, 'L');
		$this->Cell(10, $this->_h_c, 'IHS', 1,0, 'C');
		$this->Cell(20, $this->_h_c, 'VAL', 1,0, 'C');
		$this->Cell(0, $this->_h_c, utf8_decode('DESEMPEÑO'), 1,0, 'C');

		$this->Ln(4);
	}

	// 
	public function createGradeBook(){
		// 
		$this->AddPage();	
		$this->createDetailBook();

		// $this->AddPage();
		$this->createTableDetail();
	}

	private function createDetailBook()
	{
		$performance = new Performance(DB);

		foreach ($this->areas as $key => $value) {

			$this->SetFont('Arial','B',8);
			
			$this->Cell(0, $this->_h_c, utf8_decode($value), 1,0, 'L');
			$this->Ln($this->_h_c);

			foreach ($this->gradeBook as $key => $value2) {
				
				if(utf8_decode($value) == $value2['area'])
				{
					$nota = round($value2['periodo1'],1);

					$desempeno = $performance->getPerformanceToPeriod(
						$this->infoGroupAndAsig['id_grado'],
						$value2['id_asignatura'],
						1 //Periodos
					)['data'];
					$valoracion = '';
					
					$this->SetFont('Arial','B',8);
					$this->Cell(150, $this->_h_c, $value2['asignatura'], 'L',0, 'L');
					$this->Cell(10, $this->_h_c, $value2['ihs'], 0,0, 'C');
					$this->Cell(20, $this->_h_c, $nota, 0,0, 'C');

					foreach ($this->valoration as $key2 => $value3) {
						if(
							$nota >= $value3['minimo'] &&
							$nota <= $value3['maximo']
						)
						{
							$valoracion = $value3['valoracion'];
							$this->Cell(0, $this->_h_c, $valoracion, 'R',0, 'C');
						}
						else if($nota == NULL || $nota == 0)
						{
							$valoracion = 'bajo';
						}
					}

					$this->Ln($this->_h_c);

					foreach ($desempeno as $keyD => $valueD) {
						$this->SetFont('Arial','',8);
						if(strtolower($valoracion) == 'superior')
							if(($keyD+1) == count($desempeno))
								$this->determineHeihtCell($valueD['superior'], 'LR');
							else
								$this->determineHeihtCell($valueD['superior'], 'LR');
						else
							if($valoracion != '')
								if(($keyD+1) == count($desempeno))
									$this->determineHeihtCell($valueD[strtolower($valoracion)], 'LRB');
								else
									$this->determineHeihtCell($valueD[strtolower($valoracion)], 'LR');
					}
				}
			}
		}
	}

	private function determineHeihtCell($data, $border)
	{	
		if(strlen($data) > 117)
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

		$this->Cell(80, $this->_h_c, 'AREAS / ASIGNATURAS', 1,0, 'C');
		$this->Cell(8, $this->_h_c, 'IHS', 1,0, 'C');
		$this->Cell(8, $this->_h_c, 'P1', 'LTB',0, 'C');
		$this->Cell(8, $this->_h_c, '25%', 'TRB', 0,'C');

		$this->Ln($this->_h_c);

		$this->Cell(80, $this->_h_c, '', 1,0, 'C');
		$this->Cell(8, $this->_h_c, '', 1,0, 'C');
		$this->Cell(8, $this->_h_c, 'Val', 1,0, 'C');
		$this->Cell(8, $this->_h_c, 'Sup', 1, 0,'C');		

		$this->Ln($this->_h_c);
		// Imprimios las areas
		foreach ($this->areas as $key => $value) {

			$this->SetFont('Arial','B',8);

			if(strlen($value) > 35)
			{
				$this->MultiCell(80, $this->_h_c, utf8_decode($value), 1, 'C');
			}
			else
			{
				$this->Cell(80, $this->_h_c, utf8_decode($value), 1,0, 'C');
				$this->Cell(24, $this->_h_c, '', 1,0, 'C');
				$this->Ln($this->_h_c);
			}

			// Asignaturas
			foreach ($this->gradeBook as $key => $value2) {
				
				if(utf8_decode($value) == $value2['area'])
				{
					$nota = round($value2['periodo1'],1);

					// 
					$this->SetFont('Arial','',8);
					$this->Cell(80, $this->_h_c, $value2['asignatura'], 1,0, 'C');
					$this->Cell(8, $this->_h_c, $value2['ihs'], 1,0, 'C');
					$this->Cell(8, $this->_h_c, $nota, 1,0, 'C');
					$this->Cell(8, $this->_h_c, '', 1,0, 'C');

					$this->Ln($this->_h_c);
				}
			}
		}
	}
	// Pie de página
	function Footer(){
	    // Posición: a 1,5 cm del final
	    $this->SetY(-13);
	    // Arial italic 8
	    $this->SetFont('Arial','I',8);
	    // Número de página
	    $this->Cell(0,10,utf8_decode('página ').$this->PageNo(),0,0,'C');
	}
}
?>