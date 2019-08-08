<?php
require 'clsguia_turistica.php';

switch ($_GET['opcion']) {
	case 'lista_de_categoria':
		lista_de_categoria();
		break;
	case 'lista_de_lugar':
		lista_de_lugar();
		break;
	case 'lista_de_producto':
		lista_de_producto();
		break;
	case 'lista_de_producto_todo':
		lista_de_producto_todo();
		break;	
	case 'lista_de_categoria_delivery':
		lista_de_categoria_delivery();
		break;
	case 'lista_de_tipo_producto_delivery':
		lista_de_tipo_producto_delivery();
		break;
	case 'lista_de_lugar_delivery':
		lista_de_lugar_delivery();
		break;
	case 'lista_de_producto_delivery':
		lista_de_producto_delivery();
		break;
	case 'actualizar_producto':
		actualizar_producto();
		break;	
	case 'actualizar_producto_estado':
		actualizar_producto_estado();
		break;	
	case 'lista_delivery_pendiente':
		lista_delivery_pendiente();
		break;	
	case 'get_lugar_por_id_pedido':
		get_lugar_por_id_pedido();		
		break;
	default:
		print json_encode(array('suceso' => '2','mensaje' => 'Intruso detectado.'));
		break;
}
 
 function get_lugar_por_id_pedido()
{
	$dato=json_decode(file_get_contents("php://input"),true);

	$p=Guia_turistica::get_lugar_por_id_pedido($dato['id_pedido']);
	if($p!=-1)
	{
		 

		$resultado["suceso"]="1";
		$resultado["mensaje"]="Correcto";
		$resultado["id"]=$p['id'];
		$resultado["nit"]=$p['nit'];
		$resultado["razon_social"]=$p['nombre'];
		$resultado["direccion"]=$p['direccion'];
		$resultado["telefono"]=$p['telefono'];
		$resultado["celular"]=$p['whatsapp'];
		$resultado["direccion_logo"]=$p['direccion_logo'];
		print json_encode($resultado); 	
	 }
	else
	{
			print json_encode(array('suceso' => '2','mensaje' => 'No tiene acceso a esta opción por que no es administrador de ninguna Empresa.'));
	}
		
}

function lista_de_categoria()
{
	$dato=json_decode(file_get_contents("php://input"),true);
	$p=Guia_turistica::lista_de_categoria();

	if($p!='-1')
	{
		$resultado["suceso"]="1";
		$resultado["mensaje"]="Correcto";
		$resultado["lista"]=$p;
		print json_encode($resultado); 	
	 }
	else
	{
			print json_encode(array('suceso' => '2','mensaje' => 'Lista vacia'));
	}
}

function lista_de_lugar()
{
	$dato=json_decode(file_get_contents("php://input"),true);
	$p=Guia_turistica::lista_de_lugar($dato['id_categoria'],$dato['latitud'],$dato['longitud']);

	if($p!='-1')
	{
		$resultado["suceso"]="1";
		$resultado["mensaje"]="Correcto";
		$resultado["lista"]=$p;
		print json_encode($resultado); 	
	 }
	else
	{
			print json_encode(array('suceso' => '2','mensaje' => 'Lista vacia'));
	}
}

function lista_de_producto()
{
	$dato=json_decode(file_get_contents("php://input"),true);
	$p=Guia_turistica::lista_de_producto($dato['id_lugar'] );

	if($p!='-1')
	{
		$resultado["suceso"]="1";
		$resultado["mensaje"]="Correcto";
		$resultado["lista"]=$p;
		print json_encode($resultado); 	
	 }
	else
	{
			print json_encode(array('suceso' => '2','mensaje' => 'Lista vacia'));
	}
}

function lista_de_producto_todo()
{
	$dato=json_decode(file_get_contents("php://input"),true);
	$p=Guia_turistica::lista_de_producto_todo($dato['id_lugar'] );

	if($p!='-1')
	{
		$resultado["suceso"]="1";
		$resultado["mensaje"]="Correcto";
		$resultado["lista"]=$p;
		print json_encode($resultado); 	
	 }
	else
	{
			print json_encode(array('suceso' => '2','mensaje' => 'Lista vacia'));
	}
}


function lista_de_categoria_delivery()
{
	$dato=json_decode(file_get_contents("php://input"),true);
	$p=Guia_turistica::lista_de_categoria_delivery();

	if($p!='-1')
	{
		$resultado["suceso"]="1";
		$resultado["mensaje"]="Correcto";
		$resultado["lista"]=$p;
		print json_encode($resultado); 	
	 }
	else
	{
			print json_encode(array('suceso' => '2','mensaje' => 'Lista vacia'));
	}
}

function lista_de_tipo_producto_delivery()
{
	$dato=json_decode(file_get_contents("php://input"),true);
	$p=Guia_turistica::lista_de_tipo_producto_delivery($dato['id_lugar']);

	if($p!='-1')
	{
		$resultado["suceso"]="1";
		$resultado["mensaje"]="Correcto";
		$resultado["lista"]=$p;
		print json_encode($resultado); 	
	 }
	else
	{
			print json_encode(array('suceso' => '2','mensaje' => 'Lista vacia'));
	}
}

function lista_de_lugar_delivery()
{
	$dato=json_decode(file_get_contents("php://input"),true);
	$p=Guia_turistica::lista_de_lugar_delivery($dato['id_categoria'],$dato['latitud'],$dato['longitud']);

	if($p!='-1')
	{
		$resultado["suceso"]="1";
		$resultado["mensaje"]="Correcto";
		$resultado["lista"]=$p;
		print json_encode($resultado); 	
	 }
	else
	{
			print json_encode(array('suceso' => '2','mensaje' => 'Lista vacia'));
	}
}

function lista_de_producto_delivery()
{
	$dato=json_decode(file_get_contents("php://input"),true);
	$p=Guia_turistica::lista_de_producto_delivery($dato['id_lugar'] );

	if($p!='-1')
	{
		$resultado["suceso"]="1";
		$resultado["mensaje"]="Correcto";
		$resultado["lista"]=$p;
		print json_encode($resultado); 	
	 }
	else
	{
			print json_encode(array('suceso' => '2','mensaje' => 'Lista vacia'));
	}
}

function actualizar_producto()
{
	$dato=json_decode(file_get_contents("php://input"),true);
	$p=Guia_turistica::actualizar_producto($dato['id_producto'],$dato['precio']);

	if($p==true)
	{
		$resultado["suceso"]="1";
		$resultado["mensaje"]="Producto actualizado correctamente";
		print json_encode($resultado); 	
	 }
	else
	{
			print json_encode(array('suceso' => '2','mensaje' => 'Vuelva a intentarlo'));
	}
}

function actualizar_producto_estado()
{
	$dato=json_decode(file_get_contents("php://input"),true);
	$p=Guia_turistica::actualizar_producto_estado($dato['id_producto'],$dato['estado']);

	if($p==true)
	{
		$resultado["suceso"]="1";
		$resultado["mensaje"]="Cambio de estado correctamente";
		print json_encode($resultado); 	
	 }
	else
	{
			print json_encode(array('suceso' => '2','mensaje' => 'Vuelva a intentarlo'));
	}
}

function lista_delivery_pendiente()
{
	$dato=json_decode(file_get_contents("php://input"),true);
	$p=Guia_turistica::lista_delivery_pendiente($dato['id_lugar'] );

	if($p!='-1')
	{
		$resultado["suceso"]="1";
		$resultado["mensaje"]="Correcto";
		$resultado["lista"]=$p;
		print json_encode($resultado); 	
	 }
	else
	{
			print json_encode(array('suceso' => '2','mensaje' => 'Lista vacia'));
	}
}

	


?>