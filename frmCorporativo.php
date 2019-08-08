<?php
require 'clscorporativo.php';

switch ($_GET['opcion']) {
	case 'verificar_administrador_empresa':
		verificar_administrador_empresa();
		break;
	case 'verificar_administrador_lugar':
		verificar_administrador_lugar();
		break;	
	case 'lista_de_usuarios_por_id_empresa':
		lista_de_usuarios_por_id_empresa();
		break;
	case 'lista_de_usuarios_sin_empresa':
		lista_de_usuarios_sin_empresa();
		break;
	case 'agregar_usuario_empresa':
		agregar_usuario_empresa();		
		break;
	case 'eliminar_usuario_empresa':
		eliminar_usuario_empresa();		
		break;	
	default:
		
		break;
}
function verificar_administrador_empresa()
{
	$dato=json_decode(file_get_contents("php://input"),true);
	$p=Corporativo::verificar_administrador_empresa($dato['id_usuario']);
	if($p!=-1)
	{
		$monto_deuda=Corporativo::monto_deuda_empresa($p['id']);

		$resultado["suceso"]="1";
		$resultado["mensaje"]="Correcto";
		$resultado["id_empresa"]=$p['id'];
		$resultado["nit"]=$p['nit'];
		$resultado["razon_social"]=$p['razon_social'];
		$resultado["direccion"]=$p['direccion'];
		$resultado["monto_deuda"]=$monto_deuda;
		print json_encode($resultado); 	
	 }
	else
	{
			print json_encode(array('suceso' => '2','mensaje' => 'No tiene acceso a esta opción por que no es administrador de ninguna Empresa.'));
	}
}

function verificar_administrador_lugar()
{
	$dato=json_decode(file_get_contents("php://input"),true);
	$p=Corporativo::verificar_administrador_lugar($dato['id_usuario']);
	if($p!=-1)
	{
		 

		$resultado["suceso"]="1";
		$resultado["mensaje"]="Correcto";
		$resultado["id_empresa"]=$p['id'];
		$resultado["nit"]=$p['nit'];
		$resultado["razon_social"]=$p['nombre'];
		$resultado["direccion"]=$p['direccion'];
		$resultado["direccion_logo"]=$p['direccion_logo'];
		print json_encode($resultado); 	
	 }
	else
	{
			print json_encode(array('suceso' => '2','mensaje' => 'No tiene acceso a esta opción por que no es administrador de ninguna Empresa.'));
	}
}
function lista_de_usuarios_por_id_empresa()
{
	$dato=json_decode(file_get_contents("php://input"),true);
	$p=Corporativo::lista_de_usuarios_por_id_empresa($dato['id_empresa']);

	if($p!='-1')
	{
		$resultado["suceso"]="1";
		$resultado["mensaje"]="Correcto";
		$resultado["lista_usuario"]=$p;
		print json_encode($resultado); 	
	 }
	else
	{
			print json_encode(array('suceso' => '2','mensaje' => 'Lista vacia'));
	}
}

function lista_de_usuarios_sin_empresa()
{$dato=json_decode(file_get_contents("php://input"),true);
	$p=Corporativo::lista_de_usuarios_sin_empresa($dato['celular']);

	if($p!=-1)
	{
		$resultado["suceso"]="1";
		$resultado["mensaje"]="Correcto";
		$resultado["lista_usuario"]=$p;
		print json_encode($resultado); 	
	 }
	else
	{
			print json_encode(array('suceso' => '2','mensaje' => 'Lista vacia'));
	}
}

function agregar_usuario_empresa()
{
	$dato=json_decode(file_get_contents("php://input"),true);
	$p=Corporativo::agregar_usuario_empresa($dato['id_usuario'],$dato['id_administrador'],$dato['id_empresa']);
	if($p=1)
	{
		$resultado["suceso"]="1";
		$resultado["mensaje"]="Usuario agregado correctamente.";
		print json_encode($resultado); 	
	 }
	else
	{
			print json_encode(array('suceso' => '2','mensaje' => 'No se realizo el registro.'));
	}
}
function eliminar_usuario_empresa()
{
	$dato=json_decode(file_get_contents("php://input"),true);
	$p=Corporativo::eliminar_usuario_empresa($dato['id_usuario'],$dato['id_administrador'],$dato['id_empresa']);
	if($p==1)
	{
		$resultado["suceso"]="1";
		$resultado["mensaje"]="Usuario eliminado correctamente.";
		print json_encode($resultado); 	
	 }
	else
	{
			print json_encode(array('suceso' => '2','mensaje' => 'No se realizo la eliminación.'));
	}
}



?>