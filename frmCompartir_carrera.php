<?php
require('clscompartir.php');



switch ($_GET['opcion']) {
    
    case 'enviar_panico':
         enviar_panico();
      break;
      case 'enviar_panico_conductor':
         enviar_panico_conductor();
      break;
     case 'compartir_pedido':
         compartir_pedido();
      break;
     case 'insertar_compartir_carreras':
         insertar_compartir_carreras();
      break;
      case 'insertar_compartir_panico':
         insertar_compartir_panico();
      break;
      case 'buscar_usuario':
         buscar_usuario();
      break;
       case 'buscar_usuario_panico':
         buscar_usuario_panico();
      break;
      case 'lista_de_usuarios_compartir':
          lista_de_usuarios_compartir();
      break;
      case 'lista_de_usuarios_compartir_panico':
          lista_de_usuarios_compartir_panico();
      break;
      case 'eliminar_compartir_carrera':
          eliminar_compartir_carrera();
      break;      
      case 'lista_de_sub_usuarios_por_id_usuario':
          lista_de_sub_usuarios_por_id_usuario();
      break;   
      case 'lista_de_carreras_compartidas_por_id_usuario':
          lista_de_carreras_compartidas_por_id_usuario();
      break;   
      case 'lista_conductor_panico':
          lista_conductor_panico();
      break; 
       case 'lista_usuario_panico_por_id_usuario':
          lista_usuario_panico_por_id_usuario();
      break; 
      case 'lista_usuario_panico_por_id_usuario_rec':
      //OBTENEMOS LA NUEVA LISTA DE USUARIO SOLO LA LOCALIZACION SIN NOMBRE..
          lista_usuario_panico_por_id_usuario_rec();
      break; 
        
    default:      
      break;

  }

function enviar_panico()
{
  $dato=json_decode(file_get_contents("php://input"),true);
  $id_usuario=$dato['id_usuario'];
  $id_pedido=$dato['id_pedido'];
  $dato=Compartir::enviar_panico($id_usuario,$id_pedido);
  if($dato==true)
  {
    $campo['suceso']="1";
    $campo['mensaje']="Se ha enviado el panico.";
    print json_encode($campo); 
  }
  else
  {print json_encode(array('suceso' =>'2','mensaje'=>'Ocurrio un problema al enviar el panico' ));
  }
}
function enviar_panico_conductor()
{

$dato=json_decode(file_get_contents("php://input"),true);
$id_conductor=$dato['id_conductor'];
$dato=Compartir::enviar_panico_conductor($id_conductor);
if($dato==true)
{
  $campo['suceso']="1";
  $campo['mensaje']="Se ha enviado el panico.";
  print json_encode($campo); 
}
else
{print json_encode(array('suceso' =>'2','mensaje'=>'Ocurrio un problema al enviar el panico' ));
}
}

function compartir_pedido()
{
$dato=json_decode(file_get_contents("php://input"),true);
$id_usuario=$dato['id_usuario'];
$id_pedido=$dato['id_pedido'];
$js_usuarios=$dato['usuarios'];
$dato=Compartir::compartir_pedido($id_usuario,$id_pedido,$js_usuarios);
 Compartir::enviar_notificacion_recorrido_compartido($id_usuario,$id_pedido);
if($dato==true)
{
  $campo['suceso']="1";
  $campo['mensaje']="Se ha Compartido correctamente.";
  print json_encode($campo); 
}
else
{print json_encode(array('suceso' =>'2','mensaje'=>'Ocurrio un problema al registrar la comparticion de carreras.' ));
}

}

function insertar_compartir_carreras()
{

$dato=json_decode(file_get_contents("php://input"),true);
$id_usuario=$dato['id_usuario'];
$dato=Compartir::insertar_compartir_carreras($id_usuario,$dato['id_usuario_compartir']);
 $buscar=Compartir::lista_de_usuarios_compartir($id_usuario);
if($dato==true && $buscar!='-1')
{
  $campo['suceso']="1";
  $campo['mensaje']="Correcto.";
  $campo['usuario']= $buscar;
  print json_encode($campo); 
}
else
{print json_encode(array('suceso' =>'2','mensaje'=>'El usuario ya esta registrado.' ));
}
}
function insertar_compartir_panico()
{

$dato=json_decode(file_get_contents("php://input"),true);
$id_usuario=$dato['id_usuario'];
$dato=Compartir::insertar_compartir_panico($id_usuario,$dato['id_usuario_compartir']);
 $buscar=Compartir::lista_de_usuarios_compartir_panico($id_usuario);
if($dato==true && $buscar!='-1')
{
  $campo['suceso']="1";
  $campo['mensaje']="Correcto.";
  $campo['usuario']= $buscar;
  print json_encode($campo); 
}
else
{print json_encode(array('suceso' =>'2','mensaje'=>'El número ya esta registrado.' ));
}
}

function buscar_usuario()
{
$dato=json_decode(file_get_contents("php://input"),true);
$id_usuario=$dato['id_usuario'];
$celular=$dato['celular'];
$buscar=Compartir::buscar_usuario($dato['id_usuario'],$dato['celular']);

if($buscar!='-1')
{
 $campo['suceso']="1";
  $campo['mensaje']="Correcto.";
  $campo['usuario']= $buscar;
  print json_encode($campo); 
}
else
{print json_encode(array('suceso' =>'2','mensaje'=>'No se encontro el dato en el servidor.'));
}
}



function eliminar_compartir_carrera()
{
$dato=json_decode(file_get_contents("php://input"),true);
$id_usuario=$dato['id_usuario'];
$id_usuario_compartir=$dato['id_usuario_compartir'];
$buscar=Compartir::eliminar_compartir_carrera($id_usuario,$id_usuario_compartir);
if($buscar!='-1')
{
 $campo['suceso']="1";
  $campo['mensaje']="Se elimino correctamente.";
  $campo['usuario']= $buscar;
  print json_encode($campo); 
}
else
{print json_encode(array('suceso' =>'2','mensaje'=>'Ocurrio un problema al Eliminar la comparticion de carreras.' ));
}
}

function lista_de_usuarios_compartir()
{
$dato=json_decode(file_get_contents("php://input"),true);
$id_usuario=$dato['id_usuario'];
$buscar=Compartir::lista_de_usuarios_compartir($id_usuario);
if($buscar!=-1)
{
 $campo['suceso']="1";
  $campo['mensaje']="correctamente.";
  $campo['usuario']= $buscar;
  print json_encode($campo); 
}
else
{print json_encode(array('suceso' =>'2','mensaje'=>'Ocurrio un problema al buscar la comparticion de carreras.' ));
}
}
function lista_de_usuarios_compartir_panico()
{
$dato=json_decode(file_get_contents("php://input"),true);
$id_usuario=$dato['id_usuario'];
$buscar=Compartir::lista_de_usuarios_compartir_panico($id_usuario);
if($buscar!=-1)
{
 $campo['suceso']="1";
  $campo['mensaje']="correctamente.";
  $campo['usuario']= $buscar;
  print json_encode($campo); 
}
else
{print json_encode(array('suceso' =>'2','mensaje'=>'Ocurrio un problema al buscar la comparticion de carreras.' ));
}
}


    function lista_de_sub_usuarios_por_id_usuario()
     {
      $dato=json_decode(file_get_contents("php://input"),true);
      $id_usuario=$dato['id_usuario'];
      $buscar=Compartir::lista_de_sub_usuarios_por_id_usuario($id_usuario);
      if($buscar!=-1)
      {
       $campo['suceso']="1";
        $campo['mensaje']="correctamente.";
        $campo['usuario']= $buscar;
        print json_encode($campo); 
      }
      else
      {print json_encode(array('suceso' =>'2','mensaje'=>'Lista Vacia.' ));
      }
    }

     function lista_de_carreras_compartidas_por_id_usuario()
    {
      $dato=json_decode(file_get_contents("php://input"),true);
      $id_usuario=$dato['id_usuario'];
      $id_usuario_compartido=$dato['id_usuario_compartido'];
      $buscar=Compartir::lista_de_carreras_compartidas_por_id_usuario($id_usuario,$id_usuario_compartido);
      if($buscar!=-1)
      {
       $campo['suceso']="1";
        $campo['mensaje']="correctamente.";
        $campo['usuario']= $buscar;
        print json_encode($campo); 
      }
      else
      {print json_encode(array('suceso' =>'2','mensaje'=>'Lista Vacia.' ));
      }
    }
    
       function lista_conductor_panico()
    {
      $dato=json_decode(file_get_contents("php://input"),true);
      $id_conductor=$dato['ci'];
      $buscar=Compartir::lista_conductor_panico($id_conductor);
      if($buscar!=-1)
      {
       $campo['suceso']="1";
        $campo['mensaje']="correctamente.";
        $campo['conductor']= $buscar;
        print json_encode($campo); 
      }
      else
      {print json_encode(array('suceso' =>'2','mensaje'=>'Lista Vacia.' ));
      }
    }

         function lista_usuario_panico_por_id_usuario()
    {
      $dato=json_decode(file_get_contents("php://input"),true);
      $id_usuario=$dato['ci'];
      $buscar=Compartir::lista_usuario_panico_por_id_usuario($id_usuario);
      if($buscar!=-1)
      {
       $campo['suceso']="1";
        $campo['mensaje']="correctamente.";
        $campo['usuario']= $buscar;
        print json_encode($campo); 
      }
      else
      {print json_encode(array('suceso' =>'2','mensaje'=>'Lista Vacia.' ));
      }
    }


         function lista_usuario_panico_por_id_usuario_rec()
    {
      $dato=json_decode(file_get_contents("php://input"),true);
      $id_usuario=$dato['ci'];
      $buscar=Compartir::lista_usuario_panico_por_id_usuario_rec($id_usuario);
      if($buscar!=-1)
      {
       $campo['suceso']="1";
        $campo['mensaje']="correctamente.";
        $campo['usuario']= $buscar;
        print json_encode($campo); 
      }
      else
      {print json_encode(array('suceso' =>'2','mensaje'=>'Lista Vacia.' ));
      }
    }
    

?>