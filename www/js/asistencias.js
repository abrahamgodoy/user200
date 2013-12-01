function cargarAlumnos() {
	$.ajax({
		url: 'index.php?ctl=asistencias&act=carga_alumnos',
		dataType: 'json',
		success: function ( json ) {
			var cuerpo = document.getElementById( 'cuerpo_tabla' );
			var plantilla = document.getElementById( 'template_tr' );
			var acumulador = 0;
			var contador = 0;
			for( i in json ) {
				var fila = plantilla.cloneNode();
				fila.removeAttribute( 'style' );
				fila.setAttribute( 'id', json[i].codigo );
				var tds = fila.getElementsByTagName( 'td' );
				tds[1].appendChild( document.createTextNode( json[i].nombre ) );
				tds[2].appendChild( document.createTextNode( json[i].promedio_asist + "%" ) );
				cuerpo.appendChild( fila );
				acumulador += Number( json[i].promedio_asist );
				++contador;
			}
			if( contador > 0 ) {
				acumulador /= Number( contador );
				document.getElementById( 'promedio_gen' ).appendChild( document.createTextNode( acumulador + "%" ) );
			}
		},
		error: function () {
        	console.log( "no funciono carga de alumnos" );
      	}
	});
}

function sqlToDatePickerFormat( fecha ) {
	var fecha_array = fecha.split( "-" );
	fecha_array.reverse();
	return fecha_array.join( "/" );
}

function datePickerToSqlFormat( fecha ) {
	var fecha_array = fecha.split( "/" );
	fecha_array.reverse();
	return fecha_array.join( "-" );
}

function limpiarTabla( encabezado, filas ) {
	var columnas = encabezado.getElementsByTagName( 'th' );
	for( var it = columnas.length - 2; it > 1; --it ) {
		encabezado.removeChild( columnas[it] );
		for( var j = 1; j < filas.length; ++j ){
			filas[j].removeChild( filas[j].lastChild.previousSibling.previousSibling );
		}
	}
	document.getElementById( 'tabulador').setAttribute( 'colspan', 2 );
}

function desplegarFechas( fecha_input ) {
	var error = document.getElementById( 'fecha_error' );
	if( error != null ) 
		error.parentNode.removeChild( error );

	var fecha_sql = datePickerToSqlFormat( fecha_input.value );
	$.ajax({
		type: 'POST',
		data: {fecha:fecha_sql},
		url: 'index.php?ctl=asistencias&act=obtener_fechas',
		dataType: 'json',
		success: function ( json ) {
			var encabezado = document.getElementById( 'encabezado_tabla' );
			var filas = document.getElementById( 'cuerpo_tabla' ).getElementsByTagName( 'tr' );
			limpiarTabla( encabezado, filas );

			var alumnos_arr = new Array();
			for( var it = 1; it < filas.length; ++it )
				alumnos_arr.push( filas[it].id );

			var plantilla_fecha = document.getElementById( 'template_chbx' );
			var plantilla_asis = document.getElementById( 'template_asistencia' );
			var plantilla_falta = document.getElementById( 'template_falta' );
			var tab_footer = document.getElementById( 'tabulador' );

			$.ajax({
				type: 'POST',
				data: {fechas: json, alumnos: alumnos_arr},
				url: 'index.php?ctl=asistencias&act=obtener_asistencias',
				dataType: 'json',
				success: function( json2 ) {
					for( i in json ) {
						var th = document.createElement( 'th' );
						th.setAttribute( 'id', json[i] );
						var temp_chbx = plantilla_fecha.cloneNode();
						temp_chbx.removeAttribute( 'id' );
						temp_chbx.removeAttribute( 'style' );
						th.appendChild( temp_chbx );
						th.appendChild( document.createTextNode( sqlToDatePickerFormat( json[i] ) ) );
						encabezado_tabla.insertBefore( th, encabezado_tabla.lastChild.previousSibling );
						var numero = Number( 3 ) + Number( i );
						tab_footer.setAttribute( 'colspan', numero  ); 
						for( var it = 1; it < filas.length; ++it ) {
							var td = document.createElement( 'td' );
							for( j in json2 ) {
								if( Array.isArray( json2[j] ) ) {
									for( k  in json2[j] ) {
										if( json2[j][k].alumno == filas[it].id &&
											json2[j][k].fecha == json[i] ){
											if( json2[j][k].asistencia != 0 ) {
												var asist = plantilla_asis.cloneNode();
												asist.removeAttribute( 'id' );
												asist.removeAttribute( 'style' );
												td.appendChild( asist );
											}
											else {
												var falta = plantilla_falta.cloneNode();
												falta.removeAttribute( 'id' );
												falta.removeAttribute( 'style' );
												td.appendChild( falta );
											}
										}
									}
								}
							}
							filas[it].insertBefore( td, filas[it].lastChild.previousSibling );
						}
					}
				}
			});
		},
		error: function() {			
			var div = document.createElement( 'div' );
			div.setAttribute( 'class', 'error' );
			div.setAttribute( 'id', 'fecha_error' );
			div.appendChild( document.createTextNode( "Fecha invalida" ) );
			fecha_input.parentNode.appendChild( div );
		}
	});
}

function validarFecha() {
	var fecha_input = document.getElementById( 'asistencias_desde' );
	
	var error = document.getElementById( 'fecha_error' );
	if( error != null ) 
		error.parentNode.removeChild( error );
	
	if( fecha_input.value == "" ) {
		var div = document.createElement( 'div' );
		div.setAttribute( 'class', 'error' );
		div.setAttribute( 'id', 'fecha_error' );
		div.appendChild( document.createTextNode( "Introduce una fecha" ) );
		fecha_input.parentNode.appendChild( div );
		return false;
	}
	return true;
}

function marcarAsistencia() {
	if( validarFecha() ) {
		var encabezado = document.getElementById( 'encabezado_tabla' ).getElementsByTagName( 'th' );
		var filas = document.getElementById( 'cuerpo_tabla' ).getElementsByTagName( 'tr' );
		var plantilla_asis = document.getElementById( 'template_asistencia' );
		
		for( var i = 2; i < encabezado.length - 1; ++i ){
			if( encabezado[i].firstChild.checked ) {
				for( var j = 1; j < filas.length; ++j ){
					var tds = filas[j].getElementsByTagName( 'td' );
					if( tds[0].firstChild.checked  ) {
						if( tds[i].firstChild == null  ) {
							$.ajax({
								type: 'POST',
								data: {fecha: encabezado[i].id, alumno: filas[j].id, fila: j},
								url: 'index.php?ctl=asistencias&act=marcar_asistencia',
								dataType: 'json',
								success: function( json ) {
									var temp_tds = filas[json[0]].getElementsByTagName( 'td' );
									temp_tds[temp_tds.length - 1].innerText = json[1] + "%"; 
									console.log( filas[json[0]] );
									console.log( json[1] );
								},
								error: function() {
									console.log( "que pedo D: " );
								}
							});
							var asist = plantilla_asis.cloneNode();
							asist.removeAttribute( 'id' );
							asist.removeAttribute( 'style' );
							tds[i].appendChild( asist );
						}
						else {
							if( tds[i].firstChild.getAttribute( 'class' ) == 'icon-remove' ) {
								$.ajax({
									type: 'POST',
									data: {fecha: encabezado[i].id, alumno: filas[j].id, fila: j},
									url: 'index.php?ctl=asistencias&act=marcar_asistencia',
									dataType: 'json',
									success: function( json ) {
										var temp_tds = filas[json[0]].getElementsByTagName( 'td' );
										temp_tds[temp_tds.length - 1].innerText = json[1] + "%";
									}
								});
								tds[i].removeChild( tds[i].firstChild );
								var asist = plantilla_asis.cloneNode();
								asist.removeAttribute( 'id' );
								asist.removeAttribute( 'style' );
								tds[i].appendChild( asist );
							}
						}
					}
				} 
			}
		}
	}
}

function marcarFalta() {
	if( validarFecha() ) {
		var encabezado = document.getElementById( 'encabezado_tabla' ).getElementsByTagName( 'th' );
		var filas = document.getElementById( 'cuerpo_tabla' ).getElementsByTagName( 'tr' );
		var plantilla_falta = document.getElementById( 'template_falta' );
		
		for( var i = 2; i < encabezado.length - 1; ++i ){
			if( encabezado[i].firstChild.checked ) {
				for( var j = 1; j < filas.length; ++j ){
					var tds = filas[j].getElementsByTagName( 'td' );
					if( tds[0].firstChild.checked ) {
						$.ajax({
							type: 'POST',
							data: {fecha: encabezado[i].id, alumno: filas[j].id},
							url: 'index.php?ctl=asistencias&act=marcar_falta',
							success: function() {
								console.log( "si se pudo ^.^" );
							}
						});
						if( tds[i].firstChild )
							tds[i].removeChild( tds[i].firstChild );
						var falta = plantilla_falta.cloneNode();
						falta.removeAttribute( 'id' );
						falta.removeAttribute( 'style' );
						tds[i].appendChild( falta );
					}
				} 
			}
		}
	}}