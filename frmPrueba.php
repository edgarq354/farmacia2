<?php 
/**
* Loguear al usuario 
*/
 


//if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	
	// Decodificando formato Json
	switch ($_GET['opcion']) {
		case 'insertar_perfil':
			   insertar_perfil();
			break;
		default:
			print json_encode(array('suceso' => '2' ,'mensaje'=>'Actualice la aplicación.' ));
			break;
	}

 
 function insertar_perfil()
{
	$dato=json_decode(file_get_contents("php://input"),true);
	$nombre=$dato['nombre'];
	$apellido=$dato['apellido'];
	$celular=$dato['celular'];
	print json_encode(array('suceso' => '2' ,'mensaje'=>'Error al registrar Usuario.N:'.$nombre.' '.$apellido.' C:'.$celular ));	
		
}
?>
