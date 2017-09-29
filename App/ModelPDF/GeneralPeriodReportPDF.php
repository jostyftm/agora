<?php
namespace App\ModelPDF;

use Lib\merge\FPDF as FPDF;

/**
* 
*/
class GeneralPeriodReportPDF extends FPDF
{
	
	public $tipo = 'INFORME GENERAL DE PERIODO';

	public $infoStudent = array();
	public $institution = array();
	public $infoGroupAndAsig = array();
	public $content = array();
	public $date = '';
	public $period = 1;
	public $options = array();

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
	    $this->Cell(80, 4, 'GRUPO: '.$this->infoGroupAndAsig['nombre_grupo'], 0, 0, 'L');
	    
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
	    $this->Cell(80, 4, $this->infoStudent, 0, 0, 'L');
	    // Título
	    // $this->Cell(120,4, 'DOCENTE: ', 0, 0, 'C');
	    // Movernos a la derecha
	    $this->Cell(0, 4, 'FECHA: '.($this->date == '') ? date("Y-m-d") : $this->date, 0,0, 'L');
	    // Salto de línea
	    $this->Ln(8);

	    $this->subHeader();
	}

	// SubHeader
	private function subHeader(){
		$this->Cell(0, $this->_h_c, utf8_decode('INFORME GENERAL DEL PERIODO '.$this->period.' - AÑO LECTIVO ').date('Y'), 1,0, 'L'); 
		$this->Ln($this->_h_c);
	}

	public function createReportGeneralPeriod(){

		// Config

		// 
		$this->AddPage();
		// 
		$this->createReport();

		// 
		$this->showTeacherFirm();

		// 
		if(isset($this->options['doubleFace'])):
			$this->showDoubleFace();
		endif;
	}

	public function createReport(){
		$this->SetFont('Arial','',9);

		$border = 'LR';
		foreach($this->content as $key => $p):

			if(count($this->content) == ($key+1) )
				$border = 'LRB';

			$this->determineCell($this->hideTilde($p), $border);

		endforeach;
	}

	/**
	*
	*
	*/
	private function determineCell($data, $border)
	{	
		$this->SetFont('Arial','',8);

		if(strlen($data) > 100)
			$this->MultiCell(0, $this->_h_c, strip_tags($data), $border, 'L');
		else
		{
			$this->Cell(0, $this->_h_c, strip_tags($data), $border,0, 'L');
			$this->Ln(4);
		}
	}

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

		if(strstr($content, '&aacute;')){
			$content = str_replace('&aacute;', 'á', $content);
			$decoded = true;
		}

		if(strstr($content, '&iacute;')){
			$content = str_replace('&iacute;', 'í', $content);
			$decoded = true;
		}

		if(strstr($content, '&oacute;')){
			$content = str_replace('&oacute;', 'ó', $content);
			$decoded = true;
		}

		if(strstr($content, '&uacute;')){
			$content = str_replace('&uacute;', 'ú', $content);
			$decoded = true;
		}

		if(strstr($content, '&ntilde;')){
			$content = str_replace('&ntilde;', 'ñ', $content);
			$decoded = true;
		}

		if(strstr($content, '&nbsp;')){
			$content = str_replace('&nbsp;', ' ', $content);
			$decoded = true;
		}
		
		return ($decoded) ? utf8_decode($content) : $content;
	}

	// 
	private function showTeacherFirm()
	{	
		$this->Ln(10);


		$this->SetFont('Arial','B',8);
		// DIRECTOR DE GRUPO
	    $this->Cell(0,4,
    	$this->infoGroupAndAsig['doc_primer_nomb']." ".
    	$this->infoGroupAndAsig['doc_segundo_nomb']." ".
    	$this->infoGroupAndAsig['doc_primer_ape']." ".
    	$this->infoGroupAndAsig['doc_segundo_ape'], 0, 0, 'L');

	    $this->Ln();

	    $this->SetFont('Arial','',8);
	    $this->Cell(0,4,"DIRECTOR DE GRUPO", 0,0);
	}


	/**
	*
	*
	*/
	private function showDoubleFace()
	{
		if($this->PageNo()% 2 != 0 && $this->PageNo() >= 1):
			$this->AddPage();
		endif;
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