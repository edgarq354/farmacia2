<?php
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers:  Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, X-Auth-Token");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Allow: GET, POST, OPTIONS, PUT, DELETE");
header('content-type: application/json; charset=utf-8');


require('clsfarmacia.php');



if ($_SERVER['REQUEST_METHOD'] == 'POST') {

switch ($_GET['opcion']) {
		case 'lista_farmacia':
			   lista_farmacia();
			break;
		case 'insertar_farmacia':
			   insertar_farmacia();
			break;
		case 'modificar_farmacia':
			   modificar_farmacia();
			break;	
		case 'eliminar_farmacia':
			   eliminar_farmacia();
			break;		
		case 'mostrar_farmacia_por_id':
			   mostrar_farmacia_por_id();
			break;
		case 'lista_farmacia_turno':
			   lista_farmacia_turno();
			break;	

		default:	
		print json_encode(array('suceso' => '2' ,'mensaje'=>'Actualice la aplicación' ));		
			break;

	}
}else
{
		print json_encode(array('suceso' => '2' ,'mensaje'=>'No esta enviando datos Post' ));
}	

function lista_farmacia()
{
	$dato=json_decode(file_get_contents("php://input"),true);
	$id_usuario=$dato['id_usuario'];
	$texto=$dato['texto'];
	$latitud=$dato['latitud']; 
	$longitud=$dato['latitud']; 

	//mensaje predeterminado
	 $m['suceso']="0";
	 $m['mensaje']="No tenemos una farmacia cerca de su zona.";
	 // fin del mensaje

	$retorno=Farmacia::lista_farmacia($id_usuario,$texto,$latitud,$longitud);

	if($retorno==-1)
	{
     $m['suceso']="0";
	 $m['mensaje']="No tenemos una farmacia cerca de su zona.";
	 $m['lista']=array($retorno);
	}else{
     $m['suceso']="1";
	 $m['mensaje']="solicitud completada.";
	 $m['lista']=$retorno;
	}
	 
 	print json_encode($m);

}




	?>