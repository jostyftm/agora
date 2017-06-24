<?php
	
	namespace Config;

	use Libs\merge\FPDF 	as FPDF;
	use Model\InstitucionModel as Institucion;
	
	class PDF extends FPDF{
		
		public $sede = '';
		public $tipo = '';
		public $directorG = '';
		public $grupo = '';
		public $jornada = '';
		public $asignatura = '';
		public $docenteAsignatura = '';
		public $institucion = array();

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
		    $this->Cell(120, 4, strtoupper($this->tipo), 0, 0, 'C');
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
		    $this->Cell(120,4, ' DIRECTOR DE GRUPO: '.$this->directorG, 0, 0, 'C');
		    // Movernos a la derecha
		    $this->Cell(0, 4, 'MES_____________________', 0,0);
		    // Salto de línea
		    $this->Ln(4);

		    // QUINTA LINEA
		    // Movernos a la derecha
		    $this->Cell(25, 4, '', 0,0);
		    $this->Cell(65, 4, 'ASIGNATURA: '.$this->asignatura, 0, 0, 'L');
		    // Título
		    $this->Cell(120,4, 'DOCENTE: '.$this->docenteAsignatura, 0, 0, 'C');
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

		public function mostrarDatos($lista){

			$this->SetFont('Arial','',8);

			foreach ($lista as $clave => $valor) {
				$this->Cell(89, 4, ($clave+1).' '.$valor['primer_apellido'].' '.$valor['segundo_apellido'].' '.$valor['primer_nombre'].' '.$valor['segundo_nombre'], 1,0);
				$this->Cell(8, 4, '', 1, 0, 'C');
				$this->Cell(8, 4, $valor['estatus'], 1, 0, 'C');

				for ($i=0; $i < 31; $i++) { 
					$this->Cell(6, 4, '', 1, 0, 'C');
				}
				$this->Ln(4);
			}
		}
	}
?>