<?php
	
	namespace Config;

	use Libs\merge\FPDF 	as FPDF;
	use Model\InstitucionModel as Institucion;
	
	class PlanillaSuperacionPDF extends FPDF{
		
		public $sede = '';
		public $tipo = '';
		public $director_grupo = '';
		public $grupo = '';
		public $jornada = '';
		public $asignatura = '';
		public $docente = '';
		public $periodos = array();
		public $institucion = array();

		private $ACNE;  // Ancho Celda Nombre Estudiante
		private $ACFED; // Ancho Celda Firma Estudiante Docente
		private $ALTC = 5;

		public function setPeriodo( $p=array() ){

			$this->periodos = $p;

			if(count($this->periodos) == 4){
				$this->ACFED = 33.5;
				$this->ACNE = 77;

			}else if(count($this->periodos) == 3){
				$this->ACFED = 50;
				$this->ACNE = 78;
			}else if(count($this->periodos) == 2){
				$this->ACFED = 65;
				$this->ACNE = 82;
			}else{
				$this->ACFED = 73;
				$this->ACNE = 100;
			}
		}

		function Header(){
			$pic = 'data:image/png;base64,'.base64_encode($this->institucion["logo_byte"]);
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
		    $this->Cell(90, 6, '', 0,0);
		    // Título
		    $this->Cell(120, 6, $this->institucion['nombre_inst'], 0, 0, 'C');
		    // Movernos a la derecha
		    $this->Cell(0, 6, '', 0,0);
		    // Salto de línea
		    $this->Ln(6);

		    // SEGUNDA LINEA
		    $this->SetFont('Arial','B',9);
		    $this->Cell(90, 4, '', 0,0);
		    // Título
		    $this->Cell(120,4, strtoupper($this->sede), 0, 0, 'C');
		    // Movernos a la derecha
		    $this->Cell(0, 4, '', 0,0);
		    // Salto de línea
		    $this->Ln(4);

		    // TERCERA LINEA
		    $this->Cell(90, 4, '', 0,0);
		    // Título
		    $this->Cell(120, 4, utf8_decode(strtoupper($this->tipo)), 0, 0, 'C');
		    // Movernos a la derecha
		    $this->Cell(0, 4, '', 0,0);
		    // Salto de línea
		    $this->Ln(4);

		    // CUARTA LINEA
		    $this->SetFont('Arial','',8);
		    // Movernos a la derecha
		    $this->Cell(25, 4, '', 0,0);
		    $this->Cell(65, 4, 'GRUPO: '.$this->grupo, 0, 0, 'L');
		    // Título
		    $this->Cell(120,4, ' DIRECTOR DE GRUPO: '.$this->director_grupo, 0, 0, 'C');
		    // Movernos a la derecha
		    $this->Cell(0, 4, 'MES_____________________', 0,0);
		    // Salto de línea
		    $this->Ln(4);

		    // QUINTA LINEA
		    // Movernos a la derecha
		    $this->Cell(25, 4, '', 0,0);
		    $this->Cell(65, 4, 'ASIGNATURA: '.$this->asignatura, 0, 0, 'L');
		    // Título
		    $this->Cell(120,4, 'DOCENTE: '.$this->docente, 0, 0, 'C');
		    // Movernos a la derecha
		    $this->Cell(0, 4, utf8_decode('AÑO LECTIVO ').date('Y'), 0,0);
		    // Salto de línea
		    $this->Ln(4);

		    $this->crearEncabezado();
		}

		public function crearEncabezado(){

			// Salto de linea
			$this->Ln(4);
			// 
			$this->SetFillColor(135, 206, 235);
			// 
			$this->SetFont('Arial','B',9);

			$this->Cell($this->ACNE, 4, 'APELLIDOS Y NOMBRES DE ESTUDIANTE', 1,0, 'C', true);
			
			foreach ($this->periodos as $clave => $valor) {
				$this->Cell(20, 4, 'PERIODO '.split('_', $valor)[1], 1, 0, 'C', true);
				$this->Cell(14, 4, 'VALOR ', 1, 0, 'C', true);	
			}
			$this->Cell(11, 4, 'SUP ', 1, 0, 'C', true);	
			$this->Cell($this->ACFED, 4, 'FIRMA ESTUDIANTE ', 1, 0, 'C', true);	
			$this->Cell($this->ACFED, 4, 'FIRMA DOCENTE ', 1, 0, 'C', true);	

			// for ($i=0; $i <31 ; $i++)
			// 	if($i < 9)
			// 		$this->Cell(6, 4, '0'.($i+1), 1, 0, 'C', true);
			// 	else
			// 		$this->Cell(6, 4, ($i+1), 1, 0, 'C', true);

			$this->Ln(4);
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

		public function mostrarDatos($lista){

			$this->SetFont('Arial','', 9);

			foreach ($lista as $clave => $valor) {
				$this->Cell($this->ACNE, $this->ALTC, ($clave+1).' '.$valor['estudiante'], 1,0);

				foreach ($this->periodos as $periodo) {
					if($valor[$periodo] != NULL)
						$this->Cell(20, $this->ALTC, $valor[$periodo], 1, 0, 'C');
							// echo "<td align='center'>".$valor[$periodo]."</td>";
					else
						$this->Cell(20, $this->ALTC, '0.0', 1, 0, 'C');						
						// echo "<td align='center'>0.0</td>";
					$this->Cell(14, $this->ALTC, '', 1, 0, 'C');
				}

				$this->Cell(11, $this->ALTC, '', 1, 0, 'C');
				$this->Cell($this->ACFED, $this->ALTC, '', 1, 0, 'C');
				$this->Cell($this->ACFED, $this->ALTC, '', 1, 0, 'C');
				$this->Ln($this->ALTC);
			}
		}
	}
?>