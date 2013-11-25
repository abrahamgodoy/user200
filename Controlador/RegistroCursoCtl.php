<?php

class RegistroCursoCtl {
	private $modelo;

	private function altaCurso() {
		$curso = $_POST['curso'];
		$seccion = trim( $_POST['seccion'] );
		$nrc = trim( $_POST['nrc'] );
		$ciclo = $_POST['ciclo'];
		$dia = $_POST['dia'];
		$horas_dia = $_POST['horas_dia'];
		$hora_inicio = $_POST['hora_inicio'];
		$asignatura = $_POST['asignatura'];

		$existente = $this -> modelo -> buscarCurso( $nrc );
		var_dump( $existente );

		if( $existente === false ) {
			if( $this -> modelo -> agregarCurso( $nrc, $asignatura, $seccion, $ciclo, "424242", $curso ) ) {
				array_shift( $dia );
				array_shift( $horas_dia );
				array_shift( $hora_inicio );
				for( $i = 0; $i < count( $dia ); ++$i ){
					$array = explode( ":", $hora_inicio[$i] );
					$array[0] = $array[0] + $horas_dia[$i];
					$hora_fin = implode( ":", $array );
					$this -> modelo -> agregarDiaClase( $nrc, $dia[$i], $hora_inicio[$i], $hora_fin );
				}
				header( "Location: index.php?ctl=profesor&act=cursos" );
			}
			else {
				require_once( "Vista/Error.html" );
			}
		}
		else {
			if( $existente['activo'] ) {
				require_once( "Vista/Error.html" );
			}
			else {
				$this -> modelo -> eliminarDiasClase( $nrc );
				$this -> modelo -> actualizarCurso( $nrc, $curso[0], $seccion, $ciclo, "424242", $curso[1] );
				array_shift( $dia );
				array_shift( $horas_dia );
				array_shift( $hora_inicio );
				for( $i = 0; $i < count( $dia ); ++$i ){
					$array = explode( ":", $hora_inicio[$i] );
					$array[0] = $array[0] + $horas_dia[$i];
					$hora_fin = implode( ":", $array );
					$this -> modelo -> agregarDiaClase( $nrc, $dia[$i], $hora_inicio[$i], $hora_fin );
				}
				header( "Location: index.php?ctl=profesor&act=cursos" );
			}
		}
	}

	public function ejecutar() {
		require_once( "Modelo/RegistroCursoMdl.php" );
		$this -> modelo = new RegistroCursoMdl();

		switch ( $_GET['act']) {
			case 'alta':
				if( empty( $_POST ) ) {
					require_once( "Vista/RegistroCurso.html" );
				}
				else {
					$this -> altaCurso ();
				}
				break;
			case 'carga_academias':
				$deptos_array = $this -> modelo -> obtenerAcademias();
				if( $deptos_array )
					echo json_encode( $deptos_array );
				break;
			case 'carga_cursos':
				$depto = $_POST['departamento'];
				$cursos_array = $this -> modelo -> obtenerCursos( $depto );
				if( $cursos_array )
					echo json_encode( $cursos_array );
				break;
			case 'carga_ciclos':
				$ciclos_array = $this -> modelo -> obtenerCiclos();
				if( $ciclos_array )
					echo json_encode( $ciclos_array );
				break;
		}
	}
}