<?php

namespace App\ModelPDF;

use Lib\merge\FPDF as FPDF;
/**
* 
*/
class EvaluationSheetPDF extends FPDF
{	
	public $novelties = array();

	private $_prefixHeader = "_header";
	private $_prefixSubHeader = '_subHeader';

	private $_prefixShowData = '_showData';
	public $subHeader = array();

	public $tipo = 'Planilla de evaluacion';
	public $evaluation_parameters = array();
	public $low_valoration = array();
	public $min_basic = array();
	public $max_valoration = array();
	public $maxPeriod = 1;
	public $periods = array();
	public $current_period = 1;
	public $infoGroupAndAsig = array();
	public $institution = array();

	private $_with_C_S = 72; //Ancho de la celda del nombre del estudiante
	private $_with_C_N_E = 8; //Ancho de la celda novedad (NOV) y estatus (EST)
	private $_with_period = 6.5;
	private $_with_C_H = 42; //Ancho de la celda donde estan los header (Desempeños)
	private $_with_A_E = 9.5; //Ancho para la celda de AE Cambio
	private $_width_VG_VRA = 8.5;
	private $_width_mark = 267;
	private $fontSizeHeader = 9;

	private $_with_title = 120;
	private $_with_DR = 120;
	private $_with_gr = 75;
	private $_with_cellSpace = 25;

	function Header(){
		
		$this->_configMargin();

		if($this->institution['logo_byte'] != NULL)
		{
			$pic = 'data:image/png;base64,'.base64_encode($this->institution["logo_byte"]);
			$info = getimagesize($pic);

		    // Logo
		    $this->Image($pic, 12, 14, 15, 15, 'png');
		}
		    
	    // Marca de agua

	    //Marco
	    $this->Cell($this->_width_mark, 24, '', 1,0);
	    $this->Ln(0);

	    // PRIMERA LINEA
	    $this->SetFont('Arial','B',12);
	    // Movernos a la dereca
	    $this->Cell(90, 6, '', 0,0);
	    
	    
	    // Título
	    $this->Cell($this->_with_title, 6, ($this->institution['nombre_inst']), 0, 0, 'C');
	    

	    // Movernos a la derecha
	    $this->Cell(0, 6, '', 0,0);
	    // Salto de línea
	    $this->Ln(6);

	    // SEGUNDA LINEA
	    $this->SetFont('Arial','B',$this->fontSizeHeader);
	    $this->Cell(90, 4, '', 0,0);
	    // Título
	    $this->Cell($this->_with_title,4, strtoupper(($this->infoGroupAndAsig['sede'])), 0, 0, 'C');
	    // Movernos a la derecha
	    $this->Cell(0, 4, '', 0,0);
	    // Salto de línea
	    $this->Ln(4);

	    // TERCERA LINEA
	    $this->Cell(90, 4, '', 0,0);
	    // Título
	    $this->Cell($this->_with_title, 4, strtoupper($this->tipo), 0, 0, 'C');
	    // Movernos a la derecha
	    $this->Cell(0, 4, '', 0,0);
	    // Salto de línea
	    $this->Ln(4);

	    // CUARTA LINEA
	    $this->SetFont('Arial','',$this->fontSizeHeader);
	    // Movernos a la derecha
	    $this->Cell($this->_with_cellSpace, 4, '', 0,0);
	    $this->Cell($this->_with_gr, 4, 'GRUPO: '.$this->infoGroupAndAsig['nombre_grupo'], 0, 0, 'L');
	    // Título
	    $this->Cell($this->_with_DR,4, 'DIRECTOR DE GRUPO: '.
					$this->infoGroupAndAsig['dir_primer_nomb']." ".
	    			$this->infoGroupAndAsig['dir_segundo_nomb']." ".
	    			$this->infoGroupAndAsig['dir_primer_ape']." ".
	    			$this->infoGroupAndAsig['dir_segundo_ape'], 0, 0, 'L');
	    // Movernos a la derecha
	    $this->Cell(0, 4, 'FECHA: '.date('d-m-Y'), 0,0,'L');
	    // Salto de línea
	    $this->Ln(4);

	    // QUINTA LINEA
	    // Movernos a la derecha
	    $this->Cell($this->_with_cellSpace, 4, '', 0,0);
	   	$this->Cell($this->_with_gr, 4, substr('ASIGNATURA: '.($this->infoGroupAndAsig['asignatura']), 0,35), 0,0);
	    
	    // Título
	    $this->Cell($this->_with_DR,4, 'DOCENTE: '.
					$this->infoGroupAndAsig['doc_primer_nomb']." ".
	    			$this->infoGroupAndAsig['doc_segundo_nomb']." ".
	    			$this->infoGroupAndAsig['doc_primer_ape']." ".
	    			$this->infoGroupAndAsig['doc_segundo_ape'], 0, 0, 'L');
		
	    // Movernos a la derecha
	    $this->Cell(0, 4, utf8_decode('AÑO LECTIVO ').date('Y'), 0,0,'L');
	    // Salto de línea
	    $this->Ln(8);

	    
	    $this->_header();
	}

	private function _configMargin()
	{

		if($this->DefOrientation == 'P')
	    {	
	    	$this->_width_mark = 190;
	    	$this->_with_title = 10;
	    	$this->_with_gr = 54;
	    	$this->_with_doc = 65;
	    	$this->_with_C_H = 48;
	    	$this->fontSizeHeader = 7;
	    	$this->_with_DR = 90;
			$this->_with_cellSpace = 17;
			$this->_width_VG_VRA = 7.5;

			if($this->maxPeriod == 4)
				$this->_with_C_S = 78;
			else if($this->maxPeriod == 3 && count($this->evaluation_parameters) == 1)
				$this->_with_C_S = 84;
	    }else{

	    	// Preguntamos si tiene 3 periodos
	    	if($this->maxPeriod == 3)
	    	{
	    		// Para los que tienen 3 desempeño y existen AEE
	    		if(count($this->evaluation_parameters) == 3 && $this->fieldExists("AEE")){
	    			$this->_with_C_H = 55;
		    		$this->_with_C_S = 88;
	    		}

	    	}
	    }	
	}
	private function _header()
	{
		// 
		$this->SetFillColor(135, 206, 235);
		// 
		$this->SetFont('Arial','B',8);

		// 
		$this->Cell( ( ($this->_with_C_N_E * 2 ) + ($this->_with_period * $this->maxPeriod) + $this->_with_C_S) , 4, '', 1,0, 'C', true);

		// Recorremos los parametros de evaluacion
		foreach ($this->evaluation_parameters as $key => $value) {

            if($value['parametro'] == 'AEE')
            {
            $this->Cell($this->_with_A_E, 8, (ucwords($value['parametro'])), 1,0, 'C', true);
            }
            else{

            	$this->Cell($this->_with_C_H, 4, (ucwords($value['parametro'])), 1,0, 'C', true);
            }
        }

        $this->Cell($this->_width_VG_VRA, 8, 'VG', 1,0, 'C', true);
        $this->Cell($this->_width_VG_VRA, 8, 'VRA', 1,0, 'C', true);
		$this->Cell($this->_width_VG_VRA, 8, 'Val', 1,0, 'C', true); //Cambio alto 8

		$this->Ln(4);

		$this->_subHeader();
	}

	private function _subHeader(){

		$this->Cell($this->_with_C_S, 4, 'APELLIDOS Y NOMBRES DE ESTUDIANTES', 1,0, 'C', true);
		$this->Cell($this->_with_C_N_E, 4, 'NOV', 1, 0, 'C', true);
		$this->Cell(8, 4, 'EST', 1, 0, 'C', true);

		for ($i=0; $i < $this->maxPeriod; $i++) { 
			$this->Cell($this->_with_period, 4, 'P'.($i+1), 1, 0, 'C', true);			
		}

		foreach ($this->evaluation_parameters as $key => $value) {
			
			if(count($value['indicadores']) == 0)
			{	
				if($value['parametro'] != 'AEE')
            	{
					for ($i=0; $i < 5; $i++) { 
						$this->Cell( $this->_with_C_H / 5 , 4, '', 1,0, 'C', true);
					}
				}
			}
			foreach ($value['indicadores'] as $keyInd => $valueInd) {

				if($value['parametro'] != 'AEE')
            	{
            		$this->Cell( $this->_with_C_H / count($value['indicadores']) , 4, substr($valueInd['abreviacion'], 0,3), 1,0, 'C', true); //Cambio $this->_with_C_H / 5	
            	}
			}
		}

		$this->Ln(4);
	}

	// 
	public function showData($data=array()){

		// 
		$this->SetFont('Arial','',8);

		// Recorremos cada estududiante
		foreach ($data as $key => $value) {
			// Preguntamos si el estudiante es diferente de nulo
			if($value['alu_primer_nom'] != NULL){

				// Se imprime el nombre
				if($key < 9)
				{
					$this->Cell($this->_with_C_S, 4, '0'.($key+1).' '.
						substr(
							(
								$value['alu_primer_ape'].' '.
								$value['alu_segundo_ape'].' '.
								$value['alu_primer_nom'].' '.
								$value['alu_segundo_nom']
							)	
						, 0,37)
					, 1,0, 'L', false);
				}
				else
				{
					$this->Cell($this->_with_C_S, 4, ($key+1).' '.
						substr(
							(
								$value['alu_primer_ape'].' '.
								$value['alu_segundo_ape'].' '.
								$value['alu_primer_nom'].' '.
								$value['alu_segundo_nom']
							)	
						, 0,37)
					, 1,0, 'L', false);
				}
				
				// Se imprime la celda de la novedad
				$this->showNovelty($value['id_estudiante']);
				
				// Mostramos el estado
				$this->showState($value['estatus']);

				// Recorremos los periodos del estudiante
				$this->showPeriodNote($value);

				// Mostrar indicadores y parametros de evaluación
				$this->showIndicatorsCell();
				
				// Muestra la valoración acumulada(VG)
				$this->showVG($value);

				// Muestra la valoración minima de aprobación(VRA)
				$this->showVRA($value);

				// Campo para la valoración final
				$this->Cell($this->_width_VG_VRA,4,'',1,0);

				$this->Ln(4);
			}
		}

		// Mostrar Significado de abreviaciones
		$this->showAbbreviations();	
	}

	private function showPeriodNote($data = array())
	{
		foreach($this->periods as $key => $period) {

			$nota = round($data['periodo'.$period['periodos']],1);
			// Preguntamos si la nota es iguala 0
			if($nota == NULL || $nota == 0 ||  $period['periodos'] > $this->current_period)
				$this->Cell($this->_with_period, 4, '', 1, 0, 'C', false);					 
			else
				if(strlen($nota) == 1)
					$this->Cell($this->_with_period, 4, $nota.'.0', 1, 0, 'C', false);
				else
					$this->Cell($this->_with_period, 4, $nota, 1, 0, 'C', false);

		}
	}

	private function showIndicatorsCell()
	{
		foreach ($this->evaluation_parameters as $key => $value) {
			
			if(count($value['indicadores']) == 0)
			{	
				if($value['parametro'] != 'AEE')
    			{
					for ($i=0; $i < 5; $i++) { 
						$this->Cell( $this->_with_C_H / 5 , 4, '', 1,0, 'C', false);
					}
				}else{
					$this->Cell( $this->_with_A_E , 4, '', 1,0, 'C', false);
				}
			}
			foreach ($value['indicadores'] as $keyInd => $valueInd) {
				if($value['parametro'] != 'AEE')
            	{
            		$this->Cell( $this->_with_C_H / count($value['indicadores']) , 4, '', 1,0, 'C', false);	
            	}else{
					$this->Cell( $this->_with_A_E , 4, '', 1,0, 'C', false);
				}
				
			}
		}
	}

	private function showVG($data = array())
	{	
		
		$vg = round($this->getVG($data), 1);

		$this->Cell($this->_width_VG_VRA, 4, $vg, 1,0, 'C');
	}


	private function showVRA($data = array())
	{
		$vra = $this->getVRA($data);		

		$this->Cell($this->_width_VG_VRA, 4, $vra, 1,0, 'C');
	}

	// 
	private function showNovelty($id_student)
	{
		$novelty = '';

		foreach($this->novelties as $key => $value):
			if($value['idstudents'] == $id_student):
				$novelty = $value['abrev']; 				
			endif;
		endforeach;

		$this->Cell(8, 4, $novelty, 1, 0, 'C');
	}

	// 
	private function showState($state)
	{
		if($state != 'C')
			$this->Cell(8, 4, $state, 1, 0, 'C');
		else
			$this->Cell(8, 4, '', 1, 0, 'C');
	}

	private function fieldExists($field){

		foreach ($this->evaluation_parameters as $key => $parameter) {
			if($parameter['parametro'] == $field)
				return true;
		}

		return false;

	}

	private function getVG($data=array())
	{
		$vg = 0;
		foreach($this->periods as $key => $period):

			if($period['periodos'] <= $this->current_period):

				$nota = round($data['periodo'.($key+1)],1);
				$vg += $nota * ($period['peso']/100);

			endif;

		endforeach;

		return $vg;
	}

	private function getVRA($data=array())
	{
		$vra = '';
		$empty_period = false;
		// Periodos a evaluar
		$P_E = count($this->periods) - ($this->current_period - 1);

		// Minimo Báscico - Valoración
		$min_basic = $this->min_basic['minimo'];

		foreach($this->periods as $key => $period):
			
			if($this->current_period == 1 && $period['periodos'] == 1 ):

				$vra = ($min_basic / $P_E) / ($period['peso'] / 100);

				break;

			elseif( $this->current_period == $period['periodos']):

				// 
				$previous_period_note = $data['periodo'.($this->current_period-1)];

				// 
				$vg_period_previous = round($this->getVG($data),1);

				// 
				$period_tobe_evaluated = (count($this->periods) - ($this->current_period - 1) );

				if( $previous_period_note > 0 && $previous_period_note != '' ):
					$vra = ( ($min_basic - $vg_period_previous) / $period_tobe_evaluated) / ($period['peso'] / 100);
				endif;

			endif;

		endforeach;

		if($vra > 0 || $vra != ''):
			return round($vra,1);
		else:
			return $vra;
		endif;	
	}

	private function showAbbreviations()
	{
		$this->SetFont('Arial','B',9);
		$this->Cell(6,8,'VG:',0,0);
		$this->SetFont('Arial','',9);
		$this->Cell(33,8,'Valoracion Acumulada',0,0);

		$this->Cell(3,8,'/',0,0);

		$this->SetFont('Arial','B',9);
		$this->Cell(8,8,'VRA:',0,0);
		$this->SetFont('Arial','',9);
		$this->Cell(55,8,utf8_decode('Valoracion Requerida de Aprobación'),0,0);

	}

	public function getMargins(){
		return array(
			'rMargin'	=>	$this->rMargin,
			'tMargin'	=>	$this->tMargin,
			'lMargin'	=>	$this->lMargin
		);
	}

	// Pie de página
	function Footer(){

		if($this->DefOrientation == 'P')
		    // Posición: a 1 cm del final
		    $this->SetY(-10);
		else
			$this->SetY(-15);
	    // Arial italic 8
	    $this->SetFont('Arial','I',8);
	    // Número de página
	    $this->Cell(0,10,utf8_decode('Ágora - Página ').$this->PageNo(),0,0,'C');
	}
}
?>