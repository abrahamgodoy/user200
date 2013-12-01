<?php

class AltaAlumnoCtl {
	private $modelo;

	private function generarPass() {
		$fecha = getdate();
		$fecha_string = implode( "", $fecha );
		$nuevo_pass = substr( str_shuffle( $fecha_string ), 0, 8 );
		return $nuevo_pass;
	}

	private function altaSimple() {
		$nombre = trim( $_POST["nombre"] );
		$codigo = trim( $_POST["codigo"] );
		$carrera = $_POST["ingenierias"];
		$correo = trim( $_POST["mail"] );
		$pass = $this -> generarPass();
		$celular = $_POST["celular"];
		$git = trim( $_POST["cuenta_git"] );
		$pagina = trim( $_POST["pagina_web"] );

		$alumno = $this -> modelo -> buscarAlmuno( $codigo  );
		if(  $alumno === false ) {
			$resultado = $this -> modelo -> alta( $codigo , $nombre, $carrera, $correo, sha1( $pass ) );
			if( $resultado !== FALSE ) {
				if( $celular !== "" ) {
					$this -> modelo -> agregarCelular( $codigo, $celular );
				}
				if( $git !== "" ) {
					$this -> modelo -> agregarGit( $codigo, $git );
				}
				if( $pagina !== "" ) {
					$this -> modelo -> agregarPagina( $codigo, $pagina );
				}

				$this -> modelo -> ligarCurso( $codigo, $_SESSION['clave_curso'] );

				require_once( "SmartMail.php" );
				$mail = new SmartMail();
				$mail -> enviarPassword( $nombre, $pass, $correo );

				header( "Location: index.php?ctl=lista_alumnos&act=lista" );
			}
			else {
				require_once( "Vista/Error.html" );
			}
		}
		else {
			$ligas_curso = $this -> modelo -> buscarCursoLigado( $codigo );
			$contador = 0;
			$encontrado = false;
			foreach( $ligas_curso as $fila ) {
                if( $fila['activo'] ) 
                	$contador++; 
                if( $fila['Curso_clave_curso'] == $_SESSION['clave_curso'] )
                	$encontrado = true;  
            }
			if( $contador == 0 ) {
				$this -> modelo -> actualizarDatos( $codigo, $nombre, $carrera, $correo, sha1( $pass ), $celular, $git, $pagina );
				
				require_once( "SmartMail.php" );
				$mail = new SmartMail();
				$mail -> enviarPassword( $nombre, $pass, $correo );
				if( $encontrado )
					$this -> modelo -> activarLigaCurso( $codigo, $_SESSION['clave_curso'] );
				else 
					$this -> modelo -> ligarCurso( $codigo, $_SESSION['clave_curso'] );
				
			}
			else {
				if( $encontrado )
					$this -> modelo -> activarLigaCurso( $codigo, $_SESSION['clave_curso'] );
				else 
					$this -> modelo -> ligarCurso( $codigo, $_SESSION['clave_curso'] );
			}
			header( "Location: index.php?ctl=lista_alumnos&act=lista" );
		}
	}

	public function ejecutar() {
		require_once( "Modelo/AltaAlumnoMdl.php" );
		$this -> modelo = new AltaAlumnoMdl();

		switch ( $_GET['act']) {
			case 'mostrar_pagina':
				require_once( "Vista/AltaAlumno.html" );
				break;
			case 'alta_simple':
				$this -> altaSimple();
				break;
			case 'alta_archivo':
				header( "Location: index.php?ctl=lista_alumnos&act=lista" );
				break;
			default:
				$msj_error = "Acción invalida";
				$vista = file_get_contents( "Vista/Error.html" );
				$vista = str_replace( "{ERROR}", $msj_error, $vista );
				echo $vista;
				break;
		}
	}
}