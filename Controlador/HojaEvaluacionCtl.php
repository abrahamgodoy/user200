<?php

class HojaEvaluacionCtl{
	private $modelo;

	public function ejecutar(){

		require_once( 'Modelo/HojaEvaluacionMdl.php' );
		$this -> modelo = new HojaEvaluacionMdl();

		switch( $_GET['act'] ){
			case 'agregar_hoja':
				$this -> agregarHoja( 5 );
				break;

			case 'mostrar_pagina':
				require_once( "Vista/HojaEvaluacion.html" );
				break;

			case 'cargar_hoja':
				$this -> cargarHoja();
				break;

			case 'guardar_hoja':
				break;

			case 'modificar_hoja':
				break;
		}
	}

	function agregarHoja( $num_columnas ){
		$id_alumnos = $this -> modelo -> obtenerAlumnos( '12345' );
		$id_alumnos_length = count( $id_alumnos );
		
		for( $i = 1 ; $i <= $num_columnas ; $i++ ){
			$id_columna = $this -> modelo -> agregarColumna( "Columna"."$i", 2 );
			for( $j = 0 ; $j < $id_alumnos_length ; $j++ )
				$this -> modelo -> agregarCelda( 0, $id_columna, $id_alumnos[$j]['Alumno_codigo'], '12345' );
		}

		$id_columna = $this -> modelo -> agregarColumna( "Promedio", 2 );
		for( $j = 0 ; $j < $id_alumnos_length ; $j++ )
			$this -> modelo -> agregarCelda( 0, $id_columna, $id_alumnos[$j]['Alumno_codigo'], '12345' );
	}

	function cargarHoja(){
		$id_alumnos = $this -> modelo -> obtenerAlumnos( '12345' );
		$id_columnas = $this -> modelo -> obtenerColumnas( 2 );

		$id_celdas_totales = array();
		for( $i = 0 ; $i < count( $id_columnas ) ; $i++ ){
			$id_celdas = $this -> modelo -> obtenerCeldas( $id_columnas[$i]['idColumna'] );
			$id_celdas_totales = array_merge( $id_celdas_totales, $id_celdas );
		}
	}


}