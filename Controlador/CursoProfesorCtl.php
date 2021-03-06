<?php

class CursoProfesorCtl{
	private $modelo;

	public function ejecutar(){

		require_once("Modelo/CursoProfesorMdl.php");
		$this -> modelo = new CursoProfesorMdl();
		
		switch ($_GET['act']){
			case 'mostrar_pagina':
				if( !empty($_POST) ){
					$_SESSION['clave_curso'] = $_POST['clave_curso'];
					$_SESSION['nombre_curso'] = $_POST['nombre_curso'];
					header( "Location: index.php?ctl=curso_profesor&act=mostrar_pagina" );
				}
				$nombre = explode( " ", $_SESSION['nombre_usuario'] );
				$curso = $_SESSION['nombre_curso'];
				$vista = file_get_contents( "Vista/CursoProfesor.html" );
				$vista = str_replace( "&lt;Nombre&gt;", $nombre[0], $vista );
				$vista = str_replace( "&lt;Nombre de curso&gt;", $curso, $vista );
				echo $vista;
				break;

			case "listar_rubros":
				$rubros_array = $this -> modelo -> obtenerRubros( $_SESSION['clave_curso'] );
				echo json_encode( $rubros_array );
				break;

			case "eliminar_rubros":
				$id_rubros = $_POST['id_rubros'];
				$this -> eliminarRubros( $id_rubros );
				break;

			default:
				$msj_error = "Acción inválida";
				$vista = file_get_contents( "Vista/Error.html" );
				$vista = str_replace( "{ERROR}", $msj_error, $vista );
				echo $vista;
				break;
		}
	}

	public function eliminarRubros( $id_rubros ){
        $rubros_length = count( $id_rubros );
        for( $i = 0 ; $i < $rubros_length ; ++$i )
            $this -> modelo -> eliminarRubro( $id_rubros[$i] );
    }
}