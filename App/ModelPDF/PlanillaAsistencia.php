<?php

namespace App\ModelPDF;

use Lib\merge\FPDF as FPDF;
/**
* 
*/
class PlanillaAsistencia extends FPDF
{
	
	public $tipo = 'Planilla de asistencia';
	public $infoGroupAndAsig = array();
	public $institution = array();

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
	    $this->Cell(90, 6, '', 0,0);
	    // Título
	    $this->Cell(120, 6, $this->institution['nombre_inst'], 0, 0, 'C');
	    // Movernos a la derecha
	    $this->Cell(0, 6, '', 0,0);
	    // Salto de línea
	    $this->Ln(6);

	    // SEGUNDA LINEA
	    $this->SetFont('Arial','B',9);
	    $this->Cell(90, 4, '', 0,0);
	    // Título
	    $this->Cell(120,4, strtoupper($this->infoGroupAndAsig['sede']), 0, 0, 'C');
	    // Movernos a la derecha
	    $this->Cell(0, 4, '', 0,0);
	    // Salto de línea
	    $this->Ln(4);

	    // TERCERA LINEA
	    $this->Cell(90, 4, '', 0,0);
	    // Título
	    $this->Cell(120, 4, strtoupper($this->tipo), 0, 0, 'C');
	    // Movernos a la derecha
	    $this->Cell(0, 4, '', 0,0);
	    // Salto de línea
	    $this->Ln(4);

	    // CUARTA LINEA
	    $this->SetFont('Arial','',8);
	    // Movernos a la derecha
	    $this->Cell(25, 4, '', 0,0);
	    $this->Cell(65, 4, 'GRUPO: '.$this->infoGroupAndAsig['nombre_grupo'], 0, 0, 'L');
	    // Título
	    $this->Cell(120,4, ' DIRECTOR DE GRUPO: '.$this->infoGroupAndAsig['director_grupo'], 0, 0, 'C');
	    // Movernos a la derecha
	    $this->Cell(0, 4, 'FECHA: ____________________', 0,0);
	    // Salto de línea
	    $this->Ln(4);

	    // QUINTA LINEA
	    // Movernos a la derecha
	    $this->Cell(25, 4, '', 0,0);
	    $this->Cell(65, 4, 'ASIGNATURA: '.$this->infoGroupAndAsig['asignatura'], 0, 0, 'L');
	    // Título
	    $this->Cell(120,4, 'DOCENTE: '.$this->infoGroupAndAsig['docente'], 0, 0, 'C');
	    // Movernos a la derecha
	    $this->Cell(0, 4, utf8_decode('AÑO LECTIVO ').date('Y'), 0,0);
	    // Salto de línea
	    $this->Ln(4);
	    $this->crearEncabezado();
	}

	// Pie de página
	function Footer(){
	    // Posición: a 1,5 cm del final
	    $this->SetY(-15);
	    // Arial italic 8
	    $this->SetFont('Arial','I',8);
	    // Número de página
	    $this->Cell(0,10,utf8_decode('página ').$this->PageNo().'/{nb}',0,0,'C');
	}

	public function crearEncabezado(){

		// Salto de linea
		$this->Ln(4);
		// 
		$this->SetFillColor(135, 206, 235);
		// 
		$this->SetFont('Arial','B',9);

		$this->Cell(89, 4, 'APELLIDOS Y NOMBRES DE ESTUDIANTE', 1,0, 'C', true);
		$this->Cell(8, 4, 'NOV', 1, 0, 'C', true);
		$this->Cell(8, 4, 'EST', 1, 0, 'C', true);
		for ($i=0; $i <31 ; $i++)
			if($i < 9)
				$this->Cell(6, 4, '0'.($i+1), 1, 0, 'C', true);
			else
				$this->Cell(6, 4, ($i+1), 1, 0, 'C', true);

		$this->Ln(4);
	}

	public function showData($lista){

		$this->SetFont('Arial','',8);

		foreach ($lista as $clave => $valor) {
			if($valor['estudiante'] != NULL){

				if($clave < 9)
				{
					$this->Cell(89, 4, '0'.($clave+1).' '.($valor['estudiante']), 1,0);	
				}
				else
				{
					$this->Cell(89, 4, ($clave+1).' '.($valor['estudiante']), 1,0);
				}
				
				$this->Cell(8, 4, '', 1, 0, 'C');
				$this->Cell(8, 4, $valor['estatus'], 1, 0, 'C');

				for ($i=0; $i < 31; $i++) { 
					$this->Cell(6, 4, '', 1, 0, 'C');
				}
				$this->Ln(4);
			}
		}
	} 
}
?>