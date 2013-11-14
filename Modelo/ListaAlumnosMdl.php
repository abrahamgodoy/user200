<?php

class ListaAlumnosMdl {
	private $bd;

	public function __construct() {
		require_once( "BaseDeDatos.php" );
		$this -> bd = BaseDeDatos::obtenerInstancia();
	}	

	public function obtenerAlumnos() {
		$consulta = "SELECT * FROM alumno";
		$alumnos_array = $this -> bd -> consultaGeneral( $consulta );

		return $alumnos_array;
	}

	public function eliminarAlumno( $codigo ) {
		$consulta = "UPDATE alumno SET activo = FALSE WHERE codigo = $codigo";
		return $this -> bd -> consultaEspecifica( $consulta );
	}
}