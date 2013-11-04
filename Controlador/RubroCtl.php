<?php

class RubroCtl{

	private $modelo;

	public function ejecutar(){

		require_once("Modelo/RubroMdl.php");
		$this -> modelo = new RubroMdl();

		switch ($_GET['act']){

			case "agregar_rubro":

				if(empty($_POST))
					require_once("Vista/RubroEvaluacion.html");
				else
					$this -> nuevoRubro();
				break;

			default:
				require_once("Vista/Error.html");
		}
	}


	function nuevoRubro(){

		var_dump($_POST);

		$nombre_rubro = $_POST["nombre_rubro"];
		$valor_rubro = $_POST["valor_rubro"];
		$tiene_hoja = $POST["tiene_hoja"];

		$resultado = 0;
		if( $tiene_hoja === "tiene_hoja" ){
			$columnas = $_POST['columnas_rubro'];
			$resultado = $this -> modelo -> agregarRubro( $nombre_rubro, $valor_rubro, $tiene_hoja, $columnas );
		}
		else
			$resultado = $this -> modelo -> agregarRubro( $nombre_rubro, $valor_rubro, 0, $columnas );

		

		if($resultado!==FALSE)
			require_once("Vista/RubroEvaluacion.html");
		else
			require_once("Vista/Error.html");
	}
}