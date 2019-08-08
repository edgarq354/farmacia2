<?php

require_once('Basededatos.php');
include_once 'Push.php';
include_once 'Firebase.php';

class Carrera extends Database
{
  public function Carrera()
  {
    parent::Database();
  }
 
 function get_pedido_corporativo_por_id_pedido($id_pedido)
  {$consulta="SELECT p.id_empresa_cliente,p.tipo_pedido_empresa from pedido p,empresa e where p.id_empresa_cliente=e.id and p.id=?";

    try{
          $comando=parent::getInstance()->getDb()->prepare($consulta);
      $comando->execute(array($id_pedido));
      $row=$comando->fetch(PDO::FETCH_ASSOC);
      if($row)
      {
        return $row;
      }
      else
      {
        return 0;
      }
    }catch(PDOException $e)
    {
      return 0;
    }
  } 

  function get_pedido_todo_por_id($id_pedido)
  {$consulta="SELECT * from pedido where id=?";

    try{
          $comando=parent::getInstance()->getDb()->prepare($consulta);
      $comando->execute(array($id_pedido));
      $row=$comando->fetch(PDO::FETCH_ASSOC);
      if($row)
      {
        return $row;
      }
      else
      {
        return 0;
      }
    }catch(PDOException $e)
    {
      return 0;
    }
  } 


   function get_pedido_por_id_pedido($id_pedido)
  {$consulta="SELECT p.id_empresa_cliente,p.tipo_pedido_empresa from pedido p  where  p.id=?";

    try{
          $comando=parent::getInstance()->getDb()->prepare($consulta);
      $comando->execute(array($id_pedido));
      $row=$comando->fetch(PDO::FETCH_ASSOC);
      if($row)
      {
        return $row['id_empresa_cliente'];
      }
      else
      {
        return 0;
      }
    }catch(PDOException $e)
    {
      return 0;
    }
  } 

  function comenzar_carrera($id_pedido,$latitud,$longitud,$altura,$ci,$placa,$id_usuario,$direccion)
  {
   try{
		$consulta="INSERT INTO carrera (id,id_pedido,latitud_inicio,longitud_inicio,altura_inicio,id_conductor,id_vehiculo,id_usuario,direccion_inicio) values(?,?,?,?,?,?,?,?,?)";
		$comando=parent::getInstance()->getDb()->prepare($consulta);

		 $comando->execute(array(1,$id_pedido,$latitud,$longitud,$altura,$ci,$placa,$id_usuario,$direccion));

     //actualizar estado en modo de carrera.
     $consulta="UPDATE pedido set estado=1 where id= ? and estado<2";
     $comando=parent::getInstance()->getDb()->prepare($consulta);
     $comando->execute(array($id_pedido));

    if(self::verificar_estado_del_pedido($id_pedido)==3)
    {
      self::eliminar_carrera(1);
      //-2 a sido cancelado el pedido
      return -2;
    }   
    else
    {

      // fin de carrera
          return 1;
     }
		} catch (PDOException $e) {
         $row=self::get_carrera_en_curso($id_pedido);
        if($row['id']==1){
          return 1;
        }else{
          return -1;
        }
      }
		}

   function delivery_enviado_en_camino($id_pedido)
   {
    //Delivery recogido de la tienda y esta en envio a la direcion del domicilio.
    $pedido=self::get_pedido_todo_por_id($id_pedido);
    $clase_vehiculo=$pedido['clase_vehiculo'];
    if($clase_vehiculo=="5")
    {
      $consulta="UPDATE pedido SET estado_pedido=13, tipo_pedido=0 where id=".$id_pedido;
      try{
        $comando=parent::getInstance()->getDb()->prepare($consulta);
        $comando->execute();
        return true;        
      }catch(PDOException $e)
      {
           return false;
      }
    }
   }

    function delivery_entregado($id_pedido)
   {
    //Delivery entregado al cliente
     $pedido=self::get_pedido_todo_por_id($id_pedido);
        $clase_vehiculo=$pedido['clase_vehiculo'];
        if($clase_vehiculo=="5")
        {
          $consulta="UPDATE pedido SET estado_pedido=15, tipo_pedido=0 where id=".$id_pedido;
          try{
            $comando=parent::getInstance()->getDb()->prepare($consulta);
            $comando->execute();
            return true;        
          }catch(PDOException $e)
          {
               return false;
          }
        }
   }

  

  function nueva_carrera($id_pedido,$latitud,$longitud,$altura,$ci,$placa,$id_usuario,$monto,$distancia,$direccion)
  {
      //FINALIZAR Y COMENZAR UNA NUEVA CARRERA.
   try{
    $consulta="INSERT INTO carrera (id,id_pedido,latitud_inicio,longitud_inicio,altura_inicio,id_conductor,id_vehiculo,id_usuario,direccion_inicio) values(?,?,?,?,?,?,?,?,?)";
    
    $id_carrera=(int)self::get_cantidad_carrera($id_pedido);

    $pedido_corporativo=self::get_pedido_por_id_pedido($id_pedido);
    if($pedido_corporativo==0){
      //CARRERA NO CORPORATIVA FINALIZADA.
      //PEDIDO_CORPORATIVO
      //0: NO ES UN PEDIDO PEDIDO CORPÒRATIVO
      //DIREFERNTE: PEDIDO CON SOLICITUD CORPORATIVA.
      $final_carrera=self::terminar_carrera($id_carrera,$id_pedido,$latitud,$longitud,$altura,$ci,$placa,$id_usuario,$monto,$distancia,$direccion);
    }else{
      //FINALIZACION DE UNA SOLICITUD CORPORATIVA.
      /*
      linea de codigo reservado para la tarifa modificada en base a la empresa y el vehiculo.
      */
 
      $id_empresa_cliente=$pedido_corporativo['id_empresa_cliente'];
      $tipo_pedido_empresa=$pedido_corporativo['tipo_pedido_empresa'];

       $empresa=self::get_facturacion_empresa($id_empresa_cliente);
       $con_factura=$empresa['con_factura'];
       $iva=$empresa['iva'];
       $it=$empresa['it'];
       $paga_iva=$empresa['paga_iva'];
       $paga_it=$empresa['paga_it'];
       $quien_paga_iva=$empresa['quien_paga_iva'];
       $quien_paga_it=$empresa['quien_paga_it'];



    //monto por una carrera
       $monto_conductor=$monto; 
       $monto_empresa=$monto;

      //monto total de un pedido.
       $aux_monto_total2=$monto;

       if($con_factura==1){
          if($paga_iva){
            if($quien_paga_iva==1){
                //paga la empresa
              $monto_empresa+=(($aux_monto_total2*$iva)/100);
               
            }else{
              //paga el conduyctor
              $monto_conductor+=(($aux_monto_total2*$iva)/100);
            }
          }
          if($paga_it==1){
            if($quien_paga_it==1){
              //paga la empresa.
              $monto_empresa+=(($aux_monto_total2*$it)/100);
            }else{
              //paga el conductor.
              $monto_conductor+=(($aux_monto_total2*$it)/100);
            }
          }
       } 

      $final_carrera=self::terminar_carrera_corporativo($id_carrera,$id_pedido,$latitud,$longitud,$altura,$ci,$placa,$id_usuario,$monto,$monto_conductor,$monto_empresa,$id_empresa_cliente,$tipo_pedido_empresa,$distancia,$direccion);
    }



    
if($final_carrera==true){
    $comando=parent::getInstance()->getDb()->prepare($consulta);
    $id_carrera=$id_carrera+1;
    $comando->execute(array($id_carrera,$id_pedido,$latitud,$longitud,$altura,$ci,$placa,$id_usuario,$direccion));
   
   
          return $id_carrera;
        }
        else
        {
          return -1;
        }
 
    } catch (PDOException $e) {
       
       return -1;
    }
  }

 function get_cantidad_carrera($id_pedido)
  {$consulta="SELECT count(*)as cantidad from carrera where id_pedido=?";

    try{
          $comando=parent::getInstance()->getDb()->prepare($consulta);
      $comando->execute(array($id_pedido));
      $row=$comando->fetch(PDO::FETCH_ASSOC);
      if($row)
      {
        return $row['cantidad'];
      }
      else
      {
        return 0;
      }
    }catch(PDOException $e)
    {
      return 0;
    }
  } 
function verificar_estado_del_pedido($id_pedido)
  {$consulta="SELECT estado from pedido where id=?";

    try{
          $comando=parent::getInstance()->getDb()->prepare($consulta);
      $comando->execute(array($id_pedido));
      $row=$comando->fetch(PDO::FETCH_ASSOC);
      if($row)
      {
        return $row['estado'];
      }
      else
      {
        return -1;
      }
    }catch(PDOException $e)
    {
      return -1;
    }
  }

 function eliminar_carrera($id_carrera)
  {$consulta="DELETE from carrera where id=?";

    try{
          $comando=parent::getInstance()->getDb()->prepare($consulta);
      $comando->execute(array($id_carrera));

      return true;
    }catch(PDOException $e)
    {
      return false;
    }
  }


 function terminar_carrera($id_carrera,$id_pedido,$latitud,$longitud,$altura,$ci,$placa,$id_usuario,$monto,$distancia,$direccion)
 //carrera normal
  {$ruta=self::get_ruta_por_id_carrera($id_carrera,$id_pedido,$latitud,$longitud);

    $distancia=self::get_distancia_por_carrera($id_carrera,$id_pedido);
    
    $consulta="UPDATE carrera  set latitud_fin=? ,longitud_fin=?,altura_fin=?,distancia=? ,monto=?,fecha_fin=now(),ruta= ?,estado=2,distancia=? ,direccion_fin=?
    where id_pedido=? and id_usuario=? and id_vehiculo=? and id_conductor=? and id=?";

    try{
          $comando=parent::getInstance()->getDb()->prepare($consulta);
      $comando->execute(array($latitud,$longitud,$altura,$distancia,$monto,$ruta,$distancia,$direccion,$id_pedido,$id_usuario,$placa,$ci,$id_carrera));


     //obtener token para enviar la finnalizacion de la carrera..
      $token=self::get_token_id_pedido($id_pedido);
      self::enviar_notificacion_de_fin_de_carrera($token,$id_pedido,$id_carrera);
      return true;
    }catch(PDOException $e)
    {
     
      return false;
    }
  }


 function terminar_carrera_corporativo($id_carrera,$id_pedido,$latitud,$longitud,$altura,$ci,$placa,$id_usuario,$monto,$monto_conductor,$monto_empresa,$id_empresa_cliente,$tipo_pedido_empresa,$distancia,$direccion)
 // carrera corporativa
  {$ruta=self::get_ruta_por_id_carrera($id_carrera,$id_pedido,$latitud,$longitud);

    $distancia=self::get_distancia_por_carrera($id_carrera,$id_pedido);
    $costo=5;



    $consulta="UPDATE carrera  set latitud_fin=? ,longitud_fin=?,altura_fin=?,distancia=? ,monto=?,fecha_fin=now(),ruta= ?,estado=2,
    monto_conductor=?,monto_empresa=?,tipo_carrera_empresa=?,id_empresa_cliente=? , distancia=?,direccion_fin=?
    where id_pedido=? and id_usuario=? and id_vehiculo=? and id_conductor=? and id=?";

    try{
          $comando=parent::getInstance()->getDb()->prepare($consulta);
      $comando->execute(array($latitud,$longitud,$altura,$distancia,$monto,$ruta,
        $monto_conductor,$monto_empresa,$tipo_pedido_empresa,$id_empresa_cliente,$distancia,$direccion,$id_pedido,$id_usuario,$placa,$ci,$id_carrera));


     //obtener token para enviar la finnalizacion de la carrera..
      $token=self::get_token_id_pedido($id_pedido);
      self::enviar_notificacion_de_fin_de_carrera($token,$id_pedido,$id_carrera);
      return true;
    }catch(PDOException $e)
    {
     
      return false;
    }
  }


   function finalizar_pedido($id_carrera,$id_pedido,$latitud,$longitud,$altura,$ci,$placa,$id_usuario,$monto_total_2,$distancia_carrera,$direccion,$comentario)
  {//$ruta=self::get_ruta_por_id_carrera($id_carrera,$id_pedido,$latitud,$longitud);
$ruta="";
   // $distancia=self::get_distancia_por_carrera($id_carrera,$id_pedido);
 


//SUMAR EL TOTAL DE LAS CARRERAS.//TARIFA NORMAL
$monto_total=self::get_monto_por_id_pedido($id_pedido)+$monto_total_2;
$estado_billetera=1;
 $detalle="";
 $monto_efectivo;

    try{

   $pedido_corporativo=self::get_pedido_por_id_pedido($id_pedido);
    if($pedido_corporativo==0){

      //CARRERA NO CORPORATIVA FINALIZADA.
      //PEDIDO_CORPORATIVO
      //0: NO ES UN PEDIDO PEDIDO CORPÒRATIVO
      //DIREFERNTE: PEDIDO CON SOLICITUD CORPORATIVA.

    $monto_billetera=self::get_monto_billetera($id_pedido);
    $monto_billetera_usado=0;
    $monto_billetera_agregar_conductor=0;

if($estado_billetera==1){
   if($monto_total<$monto_billetera)
      {
        $monto_billetera_agregar_conductor=$monto_total;
       $monto_billetera_usado=$monto_total;
       $monto_efectivo=0;
      }else{
        $monto_billetera_agregar_conductor=$monto_total-$monto_billetera;
         $monto_billetera_usado=$monto_billetera; 
          $monto_efectivo=$monto_total-$monto_billetera;
      }

            $detalle="El total del servicio es ".$monto_total." Bs menos el monto de Billetera es ".$monto_efectivo." Bs";
} 

       $consulta="UPDATE carrera c, pedido p, conductor cond,usuario u  set u.cantidad_solicitud=u.cantidad_solicitud+1,
           c.latitud_fin=? ,
           c.longitud_fin=?,
           c.distancia=? ,
           c.monto=?,
           c.fecha_fin=now(),
            c.estado=2,
            c.altura_fin=?,
            p.estado=2,
            p.fecha_finalizado=now(),
            p.monto_total=?,
            p.monto_billetera=?,
            p.monto_billetera_conductor=cond.credito,
            cond.credito=cond.credito+?, 
            c.direccion_fin=?,
            p.descripcion_finalizo_conductor=?,
            cond.estado=1,
            cond.credito=cond.credito-1,
            p.estado_pedido=1
            where 
            cond.ci= ? 
            and c.id_pedido=? 
            and c.id_usuario=? 
            and c.id_vehiculo=? 
            and c.id_conductor=? 
            and c.id=? 
            and p.id=?  
            and p.id_usuario=u.id";

          $comando=parent::getInstance()->getDb()->prepare($consulta);
          $comando->execute(array(
            $latitud,
            $longitud,
            $distancia_carrera,
            $monto_total_2,
            $altura,
            $monto_total,
            $monto_billetera_usado,
            $monto_billetera_usado,
            $direccion,
            $comentario,
            $ci,
            $id_pedido,
            $id_usuario,
            $placa,
            $ci,
            $id_carrera,
            $id_pedido));

          $sw_agregado=self::restar_credito_pasajero($monto_billetera_usado,$id_usuario);

    }else{
      //FINALIZACION DE UNA SOLICITUD CORPORATIVA.
      /*
      linea de codigo reservado para la tarifa modificada en base a la empresa y el vehiculo.
      */
      
      $id_empresa_cliente=$pedido_corporativo['id_empresa_cliente'];
      $tipo_pedido_empresa=$pedido_corporativo['tipo_pedido_empresa'];
       $empresa=self::get_facturacion_empresa($id_empresa_cliente);
       $monto=$empresa['monto'];
       $con_factura=$empresa['con_factura'];
       $iva=$empresa['iva'];
       $it=$empresa['it'];
       $paga_iva=$empresa['paga_iva'];
       $paga_it=$empresa['paga_it'];
       $quien_paga_iva=$empresa['quien_paga_iva'];
       $quien_paga_it=$empresa['quien_paga_it'];



    //monto por una carrera
       $monto_conductor=$monto_total_2; 
      $monto_empresa=$monto_total_2;

      //monto total de un pedido.
      $monto_conductor_total=$monto_total; 
      $monto_empresa_total=$monto_total;

      $aux_monto_total=$monto_total;
      $aux_monto_total2=$monto_total_2;

       if($con_factura==1){
          if($paga_iva){
            if($quien_paga_iva==1){
                //paga la empresa
              $monto_empresa_total+=(($aux_monto_total*$iva)/100);
              $monto_empresa+=(($aux_monto_total2*$iva)/100);
               
            }else{
              //paga el conduyctor
              $monto_conductor_total+=(($aux_monto_total*$iva)/100);
              $monto_conductor+=(($aux_monto_total2*$iva)/100);
            }
          }
          if($paga_it==1){
            if($quien_paga_it==1){
              //paga la empresa.
              $monto_empresa_total+=(($aux_monto_total*$it)/100);
              $monto_empresa+=(($aux_monto_total2*$it)/100);
            }else{
              //paga el conductor.
               $monto_conductor_total=(($aux_monto_total*$it)/100);
              $monto_conductor=(($aux_monto_total2*$it)/100);
            }
          }
       } 


         $consulta="UPDATE carrera c, pedido p, conductor cond,usuario u set u.cantidad_solicitud=u.cantidad_solicitud+1 , c.latitud_fin=? ,c.longitud_fin=?,c.distancia=? ,c.monto=?,c.fecha_fin=now(),
            c.ruta= ?,c.estado=2,c.altura_fin=?,p.estado=2,p.fecha_finalizado=now(),p.monto_total=?,c.direccion_fin=? ,p.monto_conductor=?,p.monto_empresa=?,cond.estado=1,
            c.monto_conductor=?,c.monto_empresa=?,c.id_empresa_cliente=?,c.tipo_carrera_empresa=?,p.descripcion_finalizo_conductor=? ,cond.credito=cond.credito-1
            where cond.ci= ? and c.id_pedido=? and c.id_usuario=? and c.id_vehiculo=? and c.id_conductor=? and c.id=? and p.id=? and p.id_usuario=u.id";

          $comando=parent::getInstance()->getDb()->prepare($consulta);
          $comando->execute(array($latitud,$longitud,$distancia_carrera,$monto_total_2,$ruta,$altura,$monto_total,$direccion,$monto_conductor_total,$monto_empresa_total,
            $monto_conductor,$monto_empresa,$id_empresa_cliente,$tipo_pedido_empresa,$comentario,$ci,$id_pedido,
            $id_usuario,$placa,$ci,$id_carrera,$id_pedido));

    }
         

     //obtener token para enviar la finnalizacion de la carrera..
      $token=self::get_token_id_pedido($id_pedido);
      self::enviar_notificacion_terminar_pedido($token,$id_pedido,$id_carrera,$monto_total,$distancia,$detalle);
      return true;
    }catch(PDOException $e)
    {
     echo $e;
      return false;
    }
  }

   

 
//obtener el valor con los parametros de porcentaje(%) y el valor.

public function valor_por_porcentaje_monto($porcentaje,$monto_bruto)
{$monto=($monto_bruto*$porcentaje)/100;
return $monto;
}
public static function sumar_credito($cantidad,$id_moto)
{
$consulta="UPDATE pedido SET credito=credito+".$cantidad." where id=".$id_moto;
      try{
        $comando=parent::getInstance()->getDb()->prepare($consulta);
        $comando->execute();
        return true;        
      }catch(PDOException $e)
      {
           return false;
      }
}

public static function restar_credito_pasajero($cantidad,$id_usuario)
{
$consulta="UPDATE usuario SET billetera=billetera-".$cantidad." where id=".$id_usuario;
      try{
        $comando=parent::getInstance()->getDb()->prepare($consulta);
        $comando->execute();
        return true;        
      }catch(PDOException $e)
      {
           return false;
      }
}

public function get_monto_tarifa($metro,$minuto,$clase_vehiculo)
{
     $tarifa=self::get_tarifa($distancia);
      $basica=$tarifa['basica'];
      $distancia=$tarifa['distancia'];
      $monto_distancia=$tarifa['monto_distancia'];
      $tiempo=$tarifa['tiempo'];
      $monto_tiempo=$tarifa['monto_tiempo'];
      $movil=$tarifa['movil'];
      $lujo=$tarifa['lujo'];
      $aire=$tarifa['aire'];
      $maletero=$tarifa['maletero'];
      $pedido=$tarifa['pedido'];
      $reserva=$tarifa['reserva'];
      $moto=$tarifa['moto'];

      $monto_aumentar=0;
      switch ($clase_vehiculo) {
        case 1: $monto_aumentar=$movil; break;
        case 2: $monto_aumentar=$lujo; break;
        case 3: $monto_aumentar=$aire; break;
        case 4: $monto_aumentar=$maletero; break;
        case 5: $monto_aumentar=$pedido; break;
        case 6: $monto_aumentar=$reserva; break;
        case 7: $monto_aumentar=$moto; break;
        
        default:
          break;
      }

     $resultado=$basica;

           
              $mov_metro=($metro/$distancia)*$monto_distancia;
               $mov_tiempo=($minuto/$tiempo)*$monto_tiempo;
               $resultado+=round(($mov_tiempo+$mov_metro),2);
         
          if($resultado<$basica)
          {
            $resultado=$basica;
          }

          $resultado+=$monto_aumentar;
       $resultado=round($resultado,0);
      return $resultado;
}



public function get_monto_tarifa_2($metro,$minuto,$clase_vehiculo)
{
     $tarifa=self::get_tarifa(0);
      $basica=$tarifa['basica'];
      $distancia=$tarifa['distancia'];
      $tarifa_minima=$tarifa['tarifa_minima'];
      $monto_distancia=$tarifa['monto_distancia'];
      $tiempo=$tarifa['tiempo'];
      $monto_tiempo=$tarifa['monto_tiempo'];
      $movil=$tarifa['movil'];
      $lujo=$tarifa['lujo'];
      $aire=$tarifa['aire'];
      $maletero=$tarifa['maletero'];
      $pedido=$tarifa['pedido'];
      $reserva=$tarifa['reservar'];
      $hora_inicio=$tarifa['hora_inicio'];
      $hora_fin=$tarifa['hora_fin'];
      $monto_hora_nocturna=$tarifa['monto_hora_nocturna'];
      $basica_moto=$tarifa['basica_moto'];
      $distancia_moto=$tarifa['distancia_moto'];
      $monto_distancia_moto=$tarifa['monto_distancia_moto'];
      $tiempo_moto=$tarifa['tiempo_moto'];
      $monto_tiempo_moto=$tarifa['monto_tiempo_moto'];
      $pedido_moto=$tarifa['pedido_moto'];
      $hora_inicio_moto=$tarifa['hora_inicio_moto'];
      $hora_fin_moto=$tarifa['hora_fin_moto']; 
      $monto_hora_nocturna_moto=$tarifa['monto_hora_nocturna_moto']; 
      $hora=date("H");

      $monto_aumentar=0;
      switch ($clase_vehiculo) {
        case 1: $monto_aumentar=$movil; break;
        case 2: $monto_aumentar=$lujo; break;
        case 3: $monto_aumentar=$aire; break;
        case 4: $monto_aumentar=$maletero; break;
        case 5: $monto_aumentar=$pedido; break;
        case 6: $monto_aumentar=$reserva; break;
        case 7: $monto_aumentar=0;  break;
        case 8: $monto_aumentar=$pedido_moto; break;
         case 11: $monto_aumentar=$maletero; break;
        
        default:
        $monto_aumentar=$movil; 
          break;
      }

      if($clase_vehiculo==2){
        //.......TARIFARIO PARA --- LA PAZ ---
        //..... ..... VEHICULOS DE LUJOS........

      //tarifa basica del movil
      $resultado=$basica;

        if($hora>=$hora_inicio && $hora<$hora_fin ){
          //HORARIO DIURNO
        }else{
          //HORARIO NOCTURNO
          $monto_aumentar+=$monto_hora_nocturna;
        }


        //CALCULAR LA TARIFA NORMAL DEL VEHICULO 
         
              $mov_metro=($metro/$distancia)*$monto_distancia;
               $mov_tiempo=($minuto/$tiempo)*$monto_tiempo;
               $resultado+=round(($mov_tiempo+$mov_metro),2);
          
          if($resultado<$tarifa_minima)
          {
            $resultado=$tarifa_minima;
          }

          $resultado+=$monto_aumentar;

      }else if($clase_vehiculo>=7 && $clase_vehiculo<=8){
        //MOTO
        $resultado=$basica_moto;

        if($hora>=$hora_inicio_moto && $hora<=$hora_fin_moto ){
          //HORARIO DIURNO
        }else{
          //HORARIO NOCTURNO
          $monto_aumentar+=$monto_hora_nocturna_moto;
        }



        //CALCULAR LA TARIFA NORMAL DEL VEHICULO 
         
              $mov_metro=($metro/$distancia_moto)*$monto_distancia_moto;
               $mov_tiempo=($minuto/$tiempo)*$monto_tiempo_moto;
               $resultado+=round(($mov_tiempo+$mov_metro),2);
          
          if($resultado<$basica_moto)
          {
            $resultado=$basica_moto;
          }

          $resultado+=$monto_aumentar;
      }else if($clase_vehiculo>=1 && $clase_vehiculo<=11){

        //......TARIFA PARA VALLEGRANDE......
        $resultado=5;
        //VEHICULO
        if($hora>=$hora_inicio && $hora<$hora_fin ){
          //HORARIO DIURNO
        }else{
          //HORARIO NOCTURNO
          $monto_aumentar+=5;
        }


        //CALCULAR LA TARIFA NORMAL DEL VEHICULO 
           

          $resultado+=$monto_aumentar;

      }  

     

          $resultado=round($resultado,0);
       
      return $resultado;
}
//cambiar el estado de la tabla de pedido.. estado=2
public static function terminar_todo_pedido($id_pedido)
  {
      $monto_total=self::get_monto_total_por_id_pedido($id_pedido);
      $monto_empresa=self::get_monto_empresa_por_id_pedido($id_pedido);
      $monto_motista=self::get_monto_motista_por_id_pedido($id_pedido);

  
    $consulta="UPDATE pedido SET estado='2',fecha_llegado=now(),monto_total=?,monto_empresa=?,monto_empresa_aux=?,monto_motista=?,monto_motista_aux=? where id=?";
      try{
        $comando=parent::getInstance()->getDb()->prepare($consulta);
        $comando->execute(array($monto_total,$monto_empresa,$monto_empresa,$monto_motista,$monto_motista,$id_pedido));
        //enviamos notificacion para finalizar el pedido
        self::notificacion_terminar_todo_pedido($id_pedido,$monto_empresa);


        return true;
        
      }catch(PDOException $e)
      {
           return false;
      }
  }

  public static function get_credito_por_id_moto($id_moto)
  {$credito=0;
      try{
          $consulta="SELECT  credito from moto where id=?";
           $comando=parent::getInstance()->getDb()->prepare($consulta);
          $comando->execute(array( $id_moto));
          $row=$comando->fetch(PDO::FETCH_ASSOC);
            if($row)
            {
              $credito=$row['credito'];
            }
      }catch(PDOException $e)
      {

      }
    return $credito;
  }

// enviar notificacion de finalizacion de pedido
public static function notificacion_terminar_todo_pedido($id_pedido,$monto_total)
   { //esta funcion registra el id de la moto en el pedido que acaba de aceptar.......y si el pedio ya a sido registrado entonces devuelve que no se puede registrar...
    $res=false;
        
    try{
      $token=self::get_token_id_pedido($id_pedido);
            
       $push = new Push('Pedido','Carreras finalizadas.',null,"usuario",$id_pedido,"","","","5");
     
      if($monto_total!="" && $monto_total!="NULL" && $monto_total!="null")
      {
       $push->set_monto_total($monto_total);
      }
       // obteniendo el empuje del objeto push
       $mPushNotification = $push->getPush(); 
       
       // obtener el token del objeto de base de datos

     

      // creación de objeto de clase firebase
       $firebase = new Firebase(); 
       
       // envío de notificación push y visualización de resultados
        $firebase->send($token, $mPushNotification);
      return true;
      }
    catch (Exception $e){
  return false;
    }
   }
  //notificaciones......
      public static function enviar_notificacion_de_finalizacion_de_carrera($token,$id_pedido)
    {try{
       $push = new Push('Pedido','Carrera terminada.',null,"usuario",$id_pedido,"","","","1");
       // obteniendo el empuje del objeto push
       
       $mPushNotification = $push->getPush(); 
       // obtener el token del objeto de base de datos

       $devicetoken = $token;    

      // creación de objeto de clase firebase
       $firebase = new Firebase(); 
       
       // envío de notificación push y visualización de resultados
        $firebase->send($devicetoken, $mPushNotification);
      return true;
      }
    catch (Exception $e){
  return false;
    }
    }
 //notificaciones......
      public static function enviar_notificacion_de_fin_de_carrera($token,$id_pedido,$cantidad)
    {try{
      if($cantidad==0)
      {
       $push = new Push('Pedido','Se termino su carrera.',null,"usuario",$id_pedido,"","","","12");
     }
     else if($cantidad>10)
     {
       $push = new Push('Pedido','Se inicio su carrera numero '.$cantidad.'.',null,"usuario",$id_pedido,"","","","12");
     }
     else if($cantidad>0 && $cantidad<=10 )
     {$letra=self::get_numero_a_letra($cantidad);
       $push = new Push('Pedido',$letra.' destino concluido.',null,"usuario",$id_pedido,"","","","12");
     }
       // obteniendo el empuje del objeto push
       
       $mPushNotification = $push->getPush(); 
       // obtener el token del objeto de base de datos

       $devicetoken = $token;    

      // creación de objeto de clase firebase
       $firebase = new Firebase(); 
       
       // envío de notificación push y visualización de resultados
        $firebase->send($devicetoken, $mPushNotification);
      return true;
      }
    catch (Exception $e){
  return false;
    }
    }
        public static function enviar_notificacion_iniciar_carrera($token,$id_pedido)
    {try{
      
     
       $push = new Push('Pedido','¿Abordo el movil?',null,"usuario",$id_pedido,"","0","0","4");
       
       // obteniendo el empuje del objeto push
       
       $mPushNotification = $push->getPush(); 
       // obtener el token del objeto de base de datos

       $devicetoken = $token;    

      // creación de objeto de clase firebase
       $firebase = new Firebase(); 
       
       // 
         // envío de notificación push y visualización de resultados
       $sw_notificacion=$firebase->send($devicetoken, $mPushNotification);
       if($sw_notificacion===false)
       {
        return false;
       }
       else
       {
       return true; 
       }
      return true;
      }
    catch (Exception $e){
  return false;
    }
    }

        public static function enviar_notificacion_usuario($token,$id_pedido,$mensaje,$titulo)
    {try{
      
     
       $push = new Push($titulo,$mensaje,null,"usuario",$id_pedido,"","0","0","112");
       
       // obteniendo el empuje del objeto push
       
       $mPushNotification = $push->getPush(); 
       // obtener el token del objeto de base de datos

       $devicetoken = $token;    

      // creación de objeto de clase firebase
       $firebase = new Firebase(); 
       
       // 
         // envío de notificación push y visualización de resultados
       $sw_notificacion=$firebase->send($devicetoken, $mPushNotification);
       if($sw_notificacion===false)
       {
        return false;
       }
       else
       {
       return true; 
       }
      return true;
      }
    catch (Exception $e){
  return false;
    }
    }

       public static function enviar_notificacion_terminar_pedido($token,$id_pedido,$cantidad,$monto_total,$distancia,$detalle)
    {try{
      
     
       $push = new Push('Pedido','Pedido finalizado.Monto total:'.$monto_total,null,"usuario",$id_pedido,"","","","10");
       $push->setMonto_total($monto_total);
       $push->setDistancia($distancia);
       $push->setDetalle($detalle);
       // obteniendo el empuje del objeto push
       
       $mPushNotification = $push->getPush(); 
       // obtener el token del objeto de base de datos

       $devicetoken = $token;    

      // creación de objeto de clase firebase
       $firebase = new Firebase(); 
       
       // envío de notificación push y visualización de resultados
        $firebase->send($devicetoken, $mPushNotification);
      return true;
      }
    catch (Exception $e){
  return false;
    }
    }

  function set_tarifa($id_tarifa,$id_pedido,$id_usuario,$id_moto,$id)
  {
  	$consulta="UPDATE carrera  set id_tarifa=? where id_pedido=? and id_usuario=? and id_moto=? and id=?";
  	try{
  		    $comando=parent::getInstance()->getDb()->prepare($consulta);
			$comando->execute(array($id_tarifa,$id_pedido,$id_usuario,$id_moto,$id));
			return true;
  	}catch(PDOException $e)
  	{
  		return false;
  	}
  }

function get_monto_por_id_pedido($id_pedido)
  {$resultado=0;
    $consulta ="SELECT sum(monto)as 'monto' from carrera where id_pedido=?";
    try{
      $comando=parent::getInstance()->getDb()->prepare($consulta);
      $comando->execute(array($id_pedido));
      $row=$comando->fetch(PDO::FETCH_ASSOC);
      if($row)
      {
        $resultado=$row['monto'];
      }
     
      
    }
    catch(PDOException $e)
    {
      $resultado=0;
    }
    return $resultado;
  }

 function get_monto_billetera($id_pedido)
  {$resultado=0;
    $consulta ="SELECT u.billetera from pedido p,usuario u where p.id_usuario=u.id and p.id=?";
    try{
      $comando=parent::getInstance()->getDb()->prepare($consulta);
      $comando->execute(array($id_pedido));
      $row=$comando->fetch(PDO::FETCH_ASSOC);
      if($row)
      {
        $resultado=$row['billetera'];
      }
    }
    catch(PDOException $e)
    {
      $resultado=0;
    }
    return $resultado;
  }

  function get_carrera_en_curso($id_pedido)
  {$resultado=-1;
    $consulta ="SELECT id,id_pedido,id_usuario  from carrera where  id_pedido= ?  order by id desc limit 1";
    try{
      $comando=parent::getInstance()->getDb()->prepare($consulta);
      $comando->execute(array($id_pedido));
      $row=$comando->fetch(PDO::FETCH_ASSOC);
      if($row)
      {
        $resultado=$row;
      }
      
    }
    catch(PDOException $e)
    {
      $resultado=-1;
    }
    return $resultado;

  }
  function get_direccion_por_id($id_direccion)
  {
    $consulta="SELECT * from direccion where id=?";
  $resultado=-1;
    try{
      $comando=parent::getInstance()->getDb()->prepare($consulta);
      $comando->execute(array($id_direccion));
      $row=$comando->fetch(PDO::FETCH_ASSOC);
      if($row)
        { $resultado= $row;}

    }
    catch(PDOException $e)
    {
       $resultado=-1;
    }
    return $resultado;
  }

  function get_carrera_por_id($id_pedido,$id_carrera,$ci,$placa)
  {
    $consulta="SELECT latitud_inicio,longitud_inicio,altura_inicio,fecha_inicio, 
    concat(Round(TIMESTAMPDIFF(MINUTE,fecha_inicio,now())/60),':',TIMESTAMPDIFF(MINUTE,fecha_inicio,now())%60) as 'tiempo',now() 
    from carrera where id_pedido=? and id=? and id_conductor=? and id_vehiculo=? ";
  $resultado=-1;
    try{
      $comando=parent::getInstance()->getDb()->prepare($consulta);
      $comando->execute(array($id_pedido,$id_carrera,$ci,$placa));
      $row=$comando->fetch(PDO::FETCH_ASSOC);
      if($row)
        { $resultado= $row;}

    }
    catch(PDOException $e)
    {
      
       $resultado=-1;
    }
    return $resultado;
  }

   function get_clase_vehicu_por_id_pedido($id_pedido)
  {
    $consulta="SELECT clase_vehiculo from pedido where id=? ";
  $resultado=1;
    try{
      $comando=parent::getInstance()->getDb()->prepare($consulta);
      $comando->execute(array($id_pedido ));
      $row=$comando->fetch(PDO::FETCH_ASSOC);
      if($row)
        { $resultado= $row['clase_vehiculo'];
        }
    }
    catch(PDOException $e)
    {
       $resultado=1;
    }
    return $resultado;
  }

    function get_monto_aumentar_por_id_pedido($id_pedido)
  {
    $consulta="SELECT monto_aumentar from pedido where id=? ";
  $resultado=1;
    try{
      $comando=parent::getInstance()->getDb()->prepare($consulta);
      $comando->execute(array($id_pedido ));
      $row=$comando->fetch(PDO::FETCH_ASSOC);
      if($row)
        { $resultado= $row['monto_aumentar'];
        }
    }
    catch(PDOException $e)
    {
       $resultado=1;
    }
    return $resultado;
  }

     function get_cantidad_solicitud_usuario($id_pedido)
  {
    $consulta="SELECT u.cantidad_solicitud from usuario u, pedido p where u.id=p.id_usuario and p.id=? ";
  $resultado=0;
    try{
      $comando=parent::getInstance()->getDb()->prepare($consulta);
      $comando->execute(array($id_pedido ));
      $row=$comando->fetch(PDO::FETCH_ASSOC);
      if($row)
        { $resultado= $row['cantidad_solicitud'];
        }
    }
    catch(PDOException $e)
    {
       $resultado=0;
    }
    return $resultado;
  }


  function get_distancia_por_carrera($id_pedido,$id_carrera)
  {
     $consulta="SELECT sum(distancia)as 'distancia'  from ruta where id_pedido=? and id_carrera=?";
  $resultado=0;
    try{
      $comando=parent::getInstance()->getDb()->prepare($consulta);
      $comando->execute(array($id_pedido,$id_carrera));
      $row=$comando->fetch(PDO::FETCH_ASSOC);
      if($row)
        { $resultado= $row['distancia'];
            if($resultado==NULL)
            {
                $resultado=0;
            }
        }

    }
    catch(PDOException $e)
    {
       $resultado=0;
    }
    return $resultado;
  }

    function get_distancia_metros_por_carrera($id_pedido,$id_carrera)
  {
     $consulta="SELECT latitud,longitud  from ruta where id_pedido=? and id_carrera=? order by numero desc limit 1";
      $resultado=0;
    try{
      $comando=parent::getInstance()->getDb()->prepare($consulta);
      $comando->execute(array($id_pedido,$id_carrera));
      $row=$comando->fetch(PDO::FETCH_ASSOC);

      $consulta_2="SELECT latitud,longitud  from carrera where id_pedido=? and id_carrera=?   limit 1";
      $carrera_condulta=parent::getInstance()->getDb()->prepare($consulta_2);
      $carrera_condulta->execute(array($id_pedido,$id_carrera));
      $carrera=$carrera_condulta->fetch(PDO::FETCH_ASSOC);
      
      if($carrera)
        { 
          
          if($row){
            $latitud_inicio= $row['latitud'];
            $longitud_inicio= $row['longitud'];
            $latitud_fin=$carrera['latitud'];
            $longitud_fin=$carrera['longitud'];

             $resultado=self::calcular_distancia_con_google($latitud_inicio,$longitud_inicio,$latitud_fin,$longitud_fin);
          }else{
              $resultado=0;
          }
             
        }else{
          $resultado=0;
        }

    }
    catch(PDOException $e)
    {
       $resultado=0;
    }
    return $resultado;
  }

  function get_distancia_metros_por_carrera_2($id_pedido,$id_carrera)
  {
     $consulta_c="SELECT latitud_inicio,longitud_inicio,latitud_fin,longitud_fin,fecha_inicio,fecha_fin FROM carrera where id=? and id_pedido=?  ";
      $query_c = parent::getInstance()->getDb()->prepare($consulta_c);
        $query_c->execute(array($id_carrera,$id_pedido)); 
        $primera=$query_c->fetch(PDO::FETCH_ASSOC); 

      $consulta="SELECT latitud,longitud,rotacion FROM ruta where id_carrera=? and id_pedido=?  and  fecha >= date('".$primera['fecha_inicio']."')  order by numero asc";
      $query = parent::getInstance()->getDb()->prepare($consulta);
        $query->execute(array($id_carrera,$id_pedido)); 
       $ruta="";
       
       $sw_punto=false;

     

      $lat5=$primera['latitud_inicio'] ;
      $lon5=$primera['longitud_inicio'] ;

      $plan=$lat5;
      $plon=$lon5;
 
     //agregado
        $latitud=0;
        $longitud=0;

     $auxiliar=""; 
     $aux_rot=0;
     $rotacion=0;
     $sw_rotacion=0;
     $distancia=0;

        while($row=$query->fetch(PDO::FETCH_OBJ)) {

           $latitud = $row->latitud;
           $longitud = $row->longitud;
           $rotacion = $row->rotacion;

          $lat5=$row->latitud;
          $lon5=$row->longitud;

           $dato=$latitud.",".$longitud; 
           $sw_rotacion=0;
           if($aux_rot+1>$rotacion&& $aux_rot-1<$rotacion)
            {
              $sw_rotacion=1;
            }

            
             if($auxiliar!=$dato && $sw_rotacion==0) {
                 $auxiliar=$dato;
                 $aux_rot=$rotacion;
                 $distancia=$distancia+self::calcular_distancia($plan, $plon, $lat5, $lon5);

                  $plan=$lat5;
                  $plon=$lon5;
             }
    }


 
     $lat5=$latitud;
     $lon5=$longitud;
    
     $plan=$lat5;
     $plon=$lon5;
      
     if($latitud!=$plan && $longitud!=$plon){
        $distancia=$distancia+self::calcular_distancia($plan, $plon, $lat5, $lon5);
     } 
     
      $distancia=round($distancia,0);

 return $distancia;

  }

   function get_tiempo_por_carrera($id_pedido,$id_carrera)
  {
     $consulta="SELECT HOUR(SEC_TO_TIME(TIMESTAMPDIFF(MINUTE, fecha_inicio, now())*60)) hora, 
     MINUTE(SEC_TO_TIME(TIMESTAMPDIFF(MINUTE, fecha_inicio, now())*60)) minuto,
     TIMESTAMPDIFF(MINUTE, fecha_inicio, now()),fecha_inicio,now() as 'minuto_total' FROM carrera WHERE id=? and id_pedido=?";
     $tiempo="00:00:00";
    try{
      $comando=parent::getInstance()->getDb()->prepare($consulta);
      $comando->execute(array($id_carrera,$id_pedido));
      $row=$comando->fetch(PDO::FETCH_ASSOC);

          if($row)
            { 
              $hora=$row['hora'];
              $minuto=$row['minuto'];
              $tiempo=$hora.":".$minuto.":00";  
            }
          }
          catch(PDOException $e)
          {
            $tiempo="00:00:00";
          }

    return $tiempo;
  }

     function get_minuto_por_carrera($id_pedido,$id_carrera)
  {
     $consulta="SELECT TIMESTAMPDIFF(MINUTE, fecha_inicio, now()) as 'minuto_total' FROM carrera WHERE id=? and id_pedido=?";
     $tiempo=0;
    try{
      $comando=parent::getInstance()->getDb()->prepare($consulta);
      $comando->execute(array($id_carrera,$id_pedido));
      $row=$comando->fetch(PDO::FETCH_ASSOC);

          if($row)
            { 
              $tiempo=$row['minuto_total']; 
            }
          }
          catch(PDOException $e)
          {
            $tiempo=0;
          }

    return $tiempo;
  }
  
  
  function existe_direccion($latitud,$longitud,$id_usuario)
  {
     $consulta="SELECT * from direccion where latitud=? and longitud=? and id_usuario=?";
  $resultado=-1;
    try{
      $comando=parent::getInstance()->getDb()->prepare($consulta);
      $comando->execute(array($latitud,$longitud,$id_usuario));
      $row=$comando->fetch(PDO::FETCH_ASSOC);
      if($row)
        { $resultado= $row;}

    }
    catch(PDOException $e)
    {
       $resultado=-1;
    }
    return $resultado;
  }
  function insertar_direccion($latitud,$longitud,$detalle,$id_usuario)
  { 
     $direccion=self::existe_direccion($latitud,$longitud,$id_usuario);

     if($direccion=="-1")
     {
       $consulta="INSERT INTO direccion (nombre,detalle,latitud,longitud,id_usuario) values('','".$detalle."','".$latitud."','".$longitud."','".$id_usuario."')";
       
        try{
              $comando=parent::getInstance()->getDb()->prepare($consulta);
          $comando->execute(array($detalle,$latitud,$longitud,$id_usuario));
           $lastId = parent::getInstance()->getDb()->lastInsertId();
  
          return $lastId;
        }catch(PDOException $e)
        {
          return -1;
        }
     }
     else
     {
      return $direccion['id'];
     }
  }

  function lista_de_carrera_por_usuario($id_usuario,$id_pedido)
  {
    $consulta="SELECT c.id,da.detalle as 'detalle_inicio',da.latitud as 'latitud_inicio',da.longitud as 'longitud_inicio',db.detalle as 'detalle_fin',db.latitud as 'latitud_fin',db.longitud as 'longitud_fin',c.distancia,c.opciones,c.fecha_inicio,c.fecha_fin,c.id_pedido,c.id_usuario,c.id_moto,c.monto ,c.ruta as 'ruta' from carrera c, direccion da,direccion db where  c.direccion_inicio=da.id and c.direccion_fin=db.id and c.id_usuario=? and c.id_pedido=?";
    $resultado=-1;
        try{
          $comando=parent::getInstance()->getDb()->prepare($consulta);
          $comando->execute(array($id_usuario,$id_pedido));
          $row=$comando->fetchAll();  
          if($row)
          {
            $resultado=$row;
          }
          
        }catch(PDOException $e)
        {
          $resultado= -1;
        }
        return $resultado;
  }


   function get_ruta_por_id_carrera($id_carrera,$id_pedido,$latitud,$longitud)
  {
     $consulta_c="SELECT latitud_inicio,longitud_fin,latitud_fin,longitud_fin FROM carrera where id=? and id_pedido=?  ";
      $query_c = parent::getInstance()->getDb()->prepare($consulta_c);
        $query_c->execute(array($id_carrera,$id_pedido)); 

      $consulta="SELECT latitud,longitud,rotacion FROM ruta where id_carrera=? and id_pedido=? order by numero asc";
      $query = parent::getInstance()->getDb()->prepare($consulta);
        $query->execute(array($id_carrera,$id_pedido)); 
       $ruta="";
       $inicio="markers=color:red|label:I";
       $fin="";
       $punto="";
       $latitud=0;
       $longitud=0;
       $dato="";
       $auxiliar="";
       $recorrido="path=color:0xff0000ff|weight:5|enc:";
       $sw_punto=false;

      $primera=$query_c->fetch(PDO::FETCH_ASSOC); 
           $inicio = $inicio."|".$primera['latitud_inicio'].",".$primera['longitud_inicio'];

      $lat5=floor($primera['latitud'] * 1e5)-0;
      $lon5=floor($primera['longitud'] * 1e5)-0;

      $plan=$lat5;
      $plon=$lon5;

      $recorrido = $recorrido.self::ascii_encode($lat5).self::ascii_encode($lon5);
     
     $auxiliar=""; 
     $aux_rotacion=""; 

        while($row=$query->fetch(PDO::FETCH_OBJ)) {

           $latitud = $row->latitud;
           $longitud = $row->longitud;

            $lat5=floor($row->latitud * 1e5);
            $lon5=floor($row->longitud * 1e5);

           $dato=$latitud.",".$longitud; 
             if($auxiliar!=$dato && $aux_rotacion!=$row->rotacion) {
                 $auxiliar=$dato;
                 $aux_rotacion=$row->rotacion;
                 $recorrido .=self::ascii_encode($lat5 - $plan);
                 $recorrido .=self::ascii_encode($lon5 - $plon);
                // Store the current coordinates
                  $plan=$lat5;
                  $plon=$lon5;
                 
             }
      
    }

     $lat5=floor($latitud * 1e5)-0;
     $lon5=floor($longitud * 1e5)-0;
 
                 $recorrido .=self::ascii_encode($lat5 - $plan);
                 $recorrido .=self::ascii_encode($lon5 - $plon);
                // Store the current coordinates
                  $plan=$lat5;
                  $plon=$lon5;


   $fin = "|".$latitud.",".$longitud;
     $fin = "markers=color:blue|label:F".$fin;
    $ruta="https://maps.googleapis.com/maps/api/staticmap?size=600x400&scale=2&maptype=roadmap&".$inicio."&".$fin."&".$recorrido;
  return $ruta;
  }

     function get_ruta_por_id_carrera_2($id_carrera,$id_pedido)
  {
     $consulta_c="SELECT latitud_inicio,longitud_inicio,latitud_fin,longitud_fin FROM carrera where id=? and id_pedido=?  ";
      $query_c = parent::getInstance()->getDb()->prepare($consulta_c);
        $query_c->execute(array($id_carrera,$id_pedido)); 

      $consulta="SELECT latitud,longitud,rotacion FROM ruta where id_carrera=? and id_pedido=? order by numero asc  ";
      $query = parent::getInstance()->getDb()->prepare($consulta);
        $query->execute(array($id_carrera,$id_pedido)); 
       $ruta="";
       $inicio="markers=color:red|label:I";
       $fin="";
       $punto="";
       $latitud=0;
       $longitud=0;
       $dato="";
       $auxiliar="";
       $recorrido="path=color:0x000000|weight:5|enc:";
       $sw_punto=false;

      $primera=$query_c->fetch(PDO::FETCH_ASSOC);
      $inicio = $inicio."|".$primera['latitud_inicio'].",".$primera['longitud_inicio'];

      $lat5=floor($primera['latitud_inicio'] * 1e5)-0;
      $lon5=floor($primera['longitud_inicio'] * 1e5)-0;

      $plan=$lat5;
      $plon=$lon5;

      $recorrido = $recorrido.self::ascii_encode($lat5).self::ascii_encode($lon5);
       $aux_rotacion=""; 
            

        while($row=$query->fetch(PDO::FETCH_OBJ)) {
           $latitud = $row->latitud;
           $longitud = $row->longitud;

            $lat5=floor($row->latitud * 1e5);
            $lon5=floor($row->longitud * 1e5);

           $dato=$latitud.",".$longitud;
             if($auxiliar!=$dato && $aux_rotacion!=$row->rotacion) {
                 $auxiliar=$dato;
                 $aux_rotacion=$row->rotacion;
                 $recorrido .=self::ascii_encode($lat5 - $plan);
                 $recorrido .=self::ascii_encode($lon5 - $plon);
                // Store the current coordinates
                  $plan=$lat5;
                  $plon=$lon5;
                 
             }
    }


            $lat5=floor($primera['latitud_fin'] * 1e5)-0;
            $lon5=floor($primera['longitud_fin'] * 1e5)-0;
 
                 $recorrido .=self::ascii_encode($lat5 - $plan);
                 $recorrido .=self::ascii_encode($lon5 - $plon);
                // Store the current coordinates
                  $plan=$lat5;
                  $plon=$lon5;


   $fin = "|".$primera['latitud_fin'].",".$primera['longitud_fin'];
     $fin = "markers=color:blue|label:F".$fin;
    $ruta="https://maps.googleapis.com/maps/api/staticmap?size=600x400&scale=2&maptype=roadmap&".$inicio."&".$fin."&".$recorrido;
  return $ruta;
  }

   function lista_de_carrera_por_moto($id_moto,$id_pedido)
  {
    $consulta="SELECT c.id,da.detalle as 'detalle_inicio',da.latitud as 'latitud_inicio',da.longitud as 'longitud_inicio',db.detalle as 'detalle_fin',db.latitud as 'latitud_fin',db.longitud as 'longitud_fin',c.distancia,c.opciones,c.fecha_inicio,c.fecha_fin,c.id_pedido,c.id_usuario,c.id_moto,c.monto, c.ruta as 'ruta' from carrera c, direccion da,direccion db where c.direccion_inicio=da.id and c.direccion_fin=db.id and c.id_moto=? and c.id_pedido=?";
    $resultado=-1;
        try{
          $comando=parent::getInstance()->getDb()->prepare($consulta);
          $comando->execute(array($id_moto,$id_pedido));
          $row=$comando->fetchAll();  
          if($row)
          {
            $resultado=$row;
          }
          
        }catch(PDOException $e)
        {
          $resultado= -1;
        }
        return $resultado;
  }
  function insertar_casa($detalle,$latitud,$longitud,$id_usuario)
  {$resultado=false;
    $id=self::insertar_direccion($latitud,$longitud,$detalle,$id_usuario);
    if($id)
    {
        try{
        $consulta="UPDATE from usuario set id_casa=? where id=?";
        $comando=parent::getInstance()->getDb()->prepare($consulta);
        $comando->execute(array($id,$id_usuario));
        $resultado=true;
      }catch(PDOException $e)
      {
        $resultado=false;
      }
    }
    return $resultado;
  }
  function insertar_oficina($detalle,$latitud,$longitud,$id_usuario)
  {$resultado=false;
    $id=self::insertar_direccion($latitud,$longitud,$detalle,$id_usuario);
    if($id)
    {
        try{
        $consulta="UPDATE from usuario set id_oficina=? where id=?";
        $comando=parent::getInstance()->getDb()->prepare($consulta);
        $comando->execute(array($id,$id_usuario));
        $resultado=true;
      }catch(PDOException $e)
      {
        $resultado=false;
      }
    }
    return $resultado;
  }
  function insertar_trabajo($detalle,$latitud,$longitud,$id_usuario)
  {$resultado=false;
    $id=self::insertar_direccion($latitud,$longitud,$detalle,$id_usuario);
    if($id)
    {
        try{
        $consulta="UPDATE from usuario set id_trabajo=? where id=?";
        $comando=parent::getInstance()->getDb()->prepare($consulta);
        $comando->execute(array($id,$id_usuario));
        $resultado=true;
      }catch(PDOException $e)
      {
        $resultado=false;
      }
    }
    return $resultado;
  }

//obtiene los puntos de la direccion final de cada carrera  q pertenece a un solo pedido......
  function  lista_de_carreras_por_id_pedido($id_pedido)
  {
    $consulta="select d.* from direccion d,carrera c where d.id=c.direccion_fin and c.id_pedido=?";
    $resultado=-1;
        try{
          $comando=parent::getInstance()->getDb()->prepare($consulta);
          $comando->execute(array($id_pedido));
          $row=$comando->fetchAll();  
          if($row)
          {
            $resultado=$row;
          }
          
        }catch(PDOException $e)
        {
          $resultado= -1;
        }
        return $resultado;
  }

  function existe_carrera_por_id_pedido($id_pedido)
  {
    $consulta="select * from carrera where id_pedido=?";
    $resultado=false;
        try{
          $comando=parent::getInstance()->getDb()->prepare($consulta);
          $comando->execute(array($id_pedido));
          $row=$comando->fetch(PDO::FETCH_ASSOC);  
          if($row)
          {
            $resultado=true;
          }
          
        }catch(PDOException $e)
        {
          $resultado= false;
        }
        return $resultado;

  }

function rutas_por_id_usuario($id_usuario)
  {
    $consulta="select r.* from pedido p, ruta r where p.id=r.id_pedido and p.id_usuario=? order by numero asc;";
    $resultado=false;
        try{
          $comando=parent::getInstance()->getDb()->prepare($consulta);
          $comando->execute(array($id_usuario));
          $row=$comando->fetchAll();  
          if($row)
          {
            $resultado=$row;
          }
          
        }catch(PDOException $e)
        {
          $resultado= false;
        }
        return $resultado;

  }
function rutas_por_id_moto($id_moto)
  {
    $consulta="select r.* from pedido p, ruta r where p.id=r.id_pedido and p.id_moto=? order by numero asc";
    $resultado=false;
        try{
          $comando=parent::getInstance()->getDb()->prepare($consulta);
          $comando->execute(array($id_moto));
          $row=$comando->fetchAll();  
          if($row)
          {
            $resultado=$row;
          }
          
        }catch(PDOException $e)
        {
          $resultado= false;
        }
        return $resultado;

  }
  ///funcion para obtener el token del usuario en base a un pedido

    public static function get_token_id_pedido($id_pedido)
   { //obtenemos el token del usuario.
    $query = parent::getInstance()->getDb()->prepare("SELECT u.token from usuario u, pedido p where u.id=p.id_usuario and p.id=?");
        $query->execute(array($id_pedido)); 
         $tokens = array(); 
        while($row=$query->fetch(PDO::FETCH_OBJ)) {
      array_push($tokens, $row->token);
    }
        return $tokens; 
   }

   public static function get_numero_a_letra($numero)
   {$letra=$numero;
    switch ($numero) {
      case 1:
        $letra="primera";
        break;
      case 2:
        $letra="segunda";
        break;
      case 3:
        $letra="tercera";
        break;
      case 4:
        $letra="cuarta";
        break;
      case 5:
        $letra="quinta";
        break;      
      case 6:
        $letra="sexta";
        break;   
      case 7:
        $letra="septima";
        break;
      case 8:
        $letra="octava";
        break;
      case 9:
        $letra="novena";
        break;
      case 10:
        $letra="decima";
        break;

    }
    return $letra;
   }

   public static function get_ultima_tarifa()
  {
    try{
      $consulta="SELECT * from tarifa order by id desc limit 1";
      $comando=parent::getInstance()->getDb()->prepare($consulta);
      $comando->execute();
      $row=$comando->fetch(PDO::FETCH_ASSOC);
      return $row;
    }catch(PDOException $e)
    {
      return -1;
    }
  }

  function get_monto_total_por_id_pedido($id_pedido)
  {$resultado=-1;
    $consulta ="select sum(c.monto)as 'monto_total' from carrera c,pedido p where p.id=c.id_pedido and p.id=?";
    try{
      $comando=parent::getInstance()->getDb()->prepare($consulta);
      $comando->execute(array($id_pedido));
      $row=$comando->fetch(PDO::FETCH_ASSOC);
      if($row)
      {
        $resultado=$row['monto_total'];
      }
      
    }
    catch(PDOException $e)
    {
      $resultado=-1;
    }
    return $resultado;

  }
   function get_monto_motista_por_id_pedido($id_pedido)
  {$resultado=-1;
    $consulta ="select sum(c.monto_motista)as 'monto_motista' from carrera c,pedido p where p.id=c.id_pedido and p.id=?";
    try{
      $comando=parent::getInstance()->getDb()->prepare($consulta);
      $comando->execute(array($id_pedido));
      $row=$comando->fetch(PDO::FETCH_ASSOC);
      if($row)
      {
        $resultado=$row['monto_motista'];
      }
      
    }
    catch(PDOException $e)
    {
      $resultado=-1;
    }
    return $resultado;

  }
  function get_monto_empresa_por_id_pedido($id_pedido)
  {$resultado=-1;
    $consulta ="select sum(c.monto_empresa)as 'monto_empresa' from carrera c,pedido p where p.id=c.id_pedido and p.id=?";
    try{
      $comando=parent::getInstance()->getDb()->prepare($consulta);
      $comando->execute(array($id_pedido));
      $row=$comando->fetch(PDO::FETCH_ASSOC);
      if($row)
      {
        $resultado=$row['monto_empresa'];
      }
      
    }
    catch(PDOException $e)
    {
      $resultado=-1;
    }
    return $resultado;

  }

function get_facturacion_empresa( $id_empresa_cliente )
  {$consulta="SELECT monto,con_factura,iva,it,paga_iva,paga_it,quien_paga_iva,quien_paga_it from empresa where id=?";

    try{
          $comando=parent::getInstance()->getDb()->prepare($consulta);
      $comando->execute(array($id_empresa_cliente) );
      $row=$comando->fetch(PDO::FETCH_ASSOC);
      if($row)
      {
        return $row;
      }
      else
      {
        return -1;
      }
    }catch(PDOException $e)
    {
      return -1;
    }
  }

function get_tarifa( $cantidad)
  {$consulta="SELECT * from tarifa   limit 1";

    try{
          $comando=parent::getInstance()->getDb()->prepare($consulta);
      $comando->execute( );
      $row=$comando->fetch(PDO::FETCH_ASSOC);
      if($row)
      {
        return $row;
      }
      else
      {
        return -1;
      }
    }catch(PDOException $e)
    {
      return -1;
    }
  }

  function get_distancia($id_carrera,$latitud,$longitud)
{
$comando= parent::getInstance()->getDb()->prepare("SELECT distancia_entre_dos_puntos(?,?,d.latitud,d.longitud) as distancia FROM carrera c,direccion d where  c.id=? and c.direccion_inicio=d.id");
         $comando->execute(array($latitud, $longitud,$id_carrera)); 
       $row=$comando->fetch(PDO::FETCH_ASSOC);
      if($row)
      {
          $resultado=$row['distancia'];
          if($resultado==NULL)
          {
              $resultado=0;
          }
        return $resultado;
          
      }
      else
      {
        return 0;
      }
}

 function lista_de_carrera_por_pedido_conductor($id_conductor,$id_vehiculo,$id_pedido)
  {
    $consulta="SELECT c.id,c.latitud_inicio,c.longitud_inicio ,c.latitud_fin ,c.longitud_fin ,c.distancia,c.tiempo,c.fecha_inicio,c.fecha_fin,c.id_pedido,c.id_usuario,c.monto,c.direccion_inicio,c.direccion_fin, c.ruta as 'ruta' from carrera c where c.id_conductor=? and c.id_vehiculo=? and c.id_pedido=?";
    $resultado=-1;
        try{
          $comando=parent::getInstance()->getDb()->prepare($consulta);
          $comando->execute(array($id_conductor,$id_vehiculo,$id_pedido));
          $row=$comando->fetchAll();  
          if($row)
          {
            $resultado=$row;
          }
          
        }catch(PDOException $e)
        {
          $resultado= -1;
        }
        return $resultado;
  }

 function lista_de_carrera_por_pedido_usuario($id_usuario,$id_pedido)
  {
    $consulta="SELECT c.id,c.latitud_inicio,c.longitud_inicio ,c.latitud_fin ,c.longitud_fin ,c.distancia,c.tiempo,c.fecha_inicio,c.fecha_fin,c.id_pedido,c.id_conductor,c.monto,c.direccion_inicio,c.direccion_fin, c.ruta as 'ruta' from carrera c where c.id_usuario=? and c.id_pedido=?";
    $resultado=-1;
        try{
          $comando=parent::getInstance()->getDb()->prepare($consulta);
          $comando->execute(array($id_usuario,$id_pedido));
          $row=$comando->fetchAll();  
          if($row)
          {
            $resultado=$row;
          }
          
        }catch(PDOException $e)
        {
          $resultado= -1;
        }
        return $resultado;
  }









  //PRUEBA DE TARIFA.
   function iniciar_carrera_prueba($latitud,$longitud,$altura,$ci)
  {
   try{
    $consulta="INSERT INTO prueba (latitud_inicio,longitud_inicio,altura_inicio,id_conductor) values(?,?,?,?)";
    $comando=parent::getInstance()->getDb()->prepare($consulta);

     $comando->execute(array($latitud,$longitud,$altura,$ci));
      $lastId = parent::getInstance()->getDb()->lastInsertId();
     //actualizar estado en modo de carrera.

      //INICIO CARRERA CASUAL.. estado 5
     $consulta="UPDATE conductor set estado=5 where ci= ?";
     $comando=parent::getInstance()->getDb()->prepare($consulta);
     $comando->execute(array($ci));
      return $lastId;


    } catch (PDOException $e) {
        
       return -1;
    }
  }

   function finalizar_carrera_prueba($id_carrera,$latitud,$longitud,$altura,$altura_fin,$ci,$monto_total,$distancia,$tiempo)
  {
    $ruta=self::get_ruta_por_id_carrera_prueba($id_carrera);

    $distancia=self::get_distancia_por_carrera_prueba($id_carrera);

    $consulta="UPDATE prueba set latitud_fin=? ,longitud_fin=?,distancia=? ,monto=?,altura=?,fecha_fin=now(),ruta= ?,estado=2,altura_fin=?,tiempo=? 
    where id=? and id_conductor=?";

    try{
          $comando=parent::getInstance()->getDb()->prepare($consulta);
      $comando->execute(array($latitud,$longitud,$distancia,$monto_total,$altura,$ruta,$altura_fin,$tiempo,$id_carrera,$ci));

       $consulta="UPDATE conductor set estado=1 where ci= ?";
     $comando=parent::getInstance()->getDb()->prepare($consulta);
     $comando->execute(array($ci));

      return true;
    }catch(PDOException $e)
    {
     
      return false;
    }
  }



    function get_ruta_por_id_carrera_prueba($id_carrera)
  {

   $consulta="SELECT latitud,longitud FROM ruta_prueba where id_carrera=? order by numero asc";
    $query = parent::getInstance()->getDb()->prepare($consulta);
        $query->execute(array($id_carrera)); 
       $ruta="";
       $inicio="markers=color:red|label:I";
       $fin="";
       $punto="";
       $latitud=0;
       $longitud=0;
       $dato="";
       $auxiliar="";
       $recorrido="path=color:0xff0000ff|weight:5|enc:";
       $sw_punto=false;

$primera=$query->fetch(PDO::FETCH_ASSOC);

if($primera){

$inicio = $inicio."|".$primera['latitud'].",".$primera['longitud'];

$lat5=floor($primera['latitud'] * 1e5)-0;
$lon5=floor($primera['longitud'] * 1e5)-0;

$plan=$lat5;
$plon=$lon5;

$recorrido = $recorrido.self::ascii_encode($lat5).self::ascii_encode($lon5);



        while($row=$query->fetch(PDO::FETCH_OBJ)) {
           $latitud = $row->latitud;
           $longitud = $row->longitud;

            $lat5=floor($row->latitud * 1e5);
            $lon5=floor($row->longitud * 1e5);

           $dato=$latitud.",".$longitud;
             if($auxiliar!=$dato) {
                 $auxiliar=$dato;
                 $recorrido .=self::ascii_encode($lat5 - $plan);
                 $recorrido .=self::ascii_encode($lon5 - $plon);
                // Store the current coordinates
                  $plan=$lat5;
                  $plon=$lon5;
                 $fin = "|".$latitud.",".$longitud;
             }
      
    }
     $fin = "markers=color:blue|label:F".$fin;
    $ruta="https://maps.googleapis.com/maps/api/staticmap?size=600x400&scale=2&maptype=roadmap&".$inicio."&".$fin."&".$recorrido;
  }else 
  {
    $ruta="https://maps.googleapis.com/maps/api/staticmap?size=600x400&scale=2&maptype=roadmap";
  }
  return $ruta;
  }

  function get_distancia_por_carrera_prueba($id_carrera)
  {
     $consulta="SELECT sum(distancia)as 'distancia' from ruta_prueba where id_carrera=?";
  $resultado=0;
    try{
      $comando=parent::getInstance()->getDb()->prepare($consulta);
      $comando->execute(array($id_carrera));
      $row=$comando->fetch(PDO::FETCH_ASSOC);
      if($row)
        {$resultado=$row['distancia'];
          if($resultado==NULL)
          {
              $resultado=0;
          }
        }

    }
    catch(PDOException $e)
    {
       $resultado=0;
    }
    return $resultado;
  }
  function get_carrera_prueba_por_id($id_carrera,$ci)
  {
    $consulta="SELECT latitud_inicio,longitud_inicio,altura_inicio,fecha_inicio, concat(Round(TIMESTAMPDIFF(MINUTE,fecha_inicio,now())/60),':',TIMESTAMPDIFF(MINUTE,fecha_inicio,now())%60) as 'tiempo',now() from prueba where  id=? and id_conductor=?";
  $resultado=-1;
    try{
      $comando=parent::getInstance()->getDb()->prepare($consulta);
      $comando->execute(array($id_carrera,$ci));
      $row=$comando->fetch(PDO::FETCH_ASSOC);
      if($row)
        { $resultado= $row;}

    }
    catch(PDOException $e)
    {
       $resultado=-1;
    }
    return $resultado;
  }
    
     function lista_de_carrera_casual_conductor($id_conductor)
  {
    $consulta="SELECT c.id,c.latitud_inicio,c.longitud_inicio ,c.latitud_fin ,c.longitud_fin ,c.distancia,c.tiempo,c.fecha_inicio,c.fecha_fin,c.monto, c.ruta as 'ruta' from prueba c where c.id_conductor=?";
    $resultado=-1;
        try{
          $comando=parent::getInstance()->getDb()->prepare($consulta);
          $comando->execute(array($id_conductor));
          $row=$comando->fetchAll();  
          if($row)
          {
            $resultado=$row;
          }
          
        }catch(PDOException $e)
        {
          $resultado= -1;
        }
        return $resultado;
  }



function get_ruta($id_carrera,$id_pedido){
  $enc = "";

  $consulta="SELECT numero,latitud,longitud FROM ruta where id_carrera=? and id_pedido =? order by numero asc";
    $query = parent::getInstance()->getDb()->prepare($consulta);
        $query->execute(array($id_carrera,$id_pedido)); 
       $ruta="";
       $inicio="markers=color:red|label:I";
       $fin="";
       $punto="";
       $latitud=0;
       $longitud=0;
       $dato="";
       $auxiliar="";
       $recorrido="path=color:0xff0000ff|weight:5|enc:";
       $sw_punto=false;
       $aux="";

$primera=$query->fetch(PDO::FETCH_ASSOC);
$inicio = $inicio."|".$primera['latitud'].",".$primera['longitud'];

$lat5=floor($primera['latitud'] * 1e5)-0;
$lon5=floor($primera['longitud'] * 1e5)-0;

$plan=$lat5;
$plon=$lon5;

$recorrido = $recorrido.self::ascii_encode($lat5).self::ascii_encode($lon5);
$aux=$primera['numero']."=".$primera['latitud'].",".$primera['longitud']."<br>";
      

        while($row=$query->fetch(PDO::FETCH_OBJ)) {
           $latitud = $row->latitud;
           $longitud = $row->longitud;


            $lat5=floor($row->latitud * 1e5);
            $lon5=floor($row->longitud * 1e5);

           $dato=$latitud.",".$longitud;
            $aux.=$row->numero."=".$latitud.",".$longitud."<br>"; 
             if($auxiliar!=$dato) {
                 $auxiliar=$dato;
                 

                 $recorrido .=self::ascii_encode($lat5 - $plan);
                 $recorrido .=self::ascii_encode($lon5 - $plon);
    // Store the current coordinates
                  $plan=$lat5;
                  $plon=$lon5;

                 $fin = "|".$latitud.",".$longitud;
                 $enc .=$dato;

             }
      
    }
     $fin = "markers=color:blue|label:F".$fin;
    $ruta="https://maps.googleapis.com/maps/api/staticmap?size=600x400&scale=2&maptype=roadmap&".$inicio."&".$fin."&".$recorrido."&format=jpg";

  
echo "<br> Lista<br>".$aux."<br>";
  return $ruta;
}
 
function ascii_encode($numb) {
        
        $numb = $numb << 1;
        if ($numb < 0) {
                $numb = ~$numb;
        }

        return self::ascii_encode_helper($numb);
}

function ascii_encode_helper($numb) {
        $string = "";
        $count = 0;

        while ($numb >= 0x20) {
                //echo $numb . "<br>";
                $count++;
                $string .= (pack("C",(0x20 | ($numb & 0x1f)) + 63));
                $numb = $numb >> 5;
        }
        //echo $numb . "<br>";
        $string .= pack("C", $numb+63);
        //echo $string . "<br>";;
        return str_replace("\\","\\\\",$string);
}

    function calcular_distancia_con_google($latitud,$longitud,$latitud_fin,$longitud_fin){
        $url = "https://maps.googleapis.com/maps/api/directions/json?origin=".$latitud.",".$longitud."&destination=".$latitud_fin.",".$longitud_fin."&mode=driving&key=AIzaSyDJOgdNEw_LhTMyn7YvAgsRLSamN3ckCeQ";
    // create curl resource
    $ch = curl_init();
    // set url
    curl_setopt($ch, CURLOPT_URL, $url);


    //return the transfer as a string
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    // $output contains the output string
    $output = curl_exec($ch);

    // close curl resource to free up system resources
    curl_close($ch);

    //$arr = json_decode($output, TRUE);

    //Decode the JSON data we received.
    $json = json_decode(trim($output ), true);
     
    //Automatically select the first route that Google gave us.
    $route = $json['routes'][0];
     
    //Loop through the "legs" in our route and add up the distances.
    //print json_encode($json);

    $totalDistance = 0.0;

    foreach($route['legs'] as $leg){
        $totalDistance = $totalDistance + $leg['distance']['value'];
    }
    //Print out the result.

    //fin de la distancia entre dos puntos METROS
    return  $totalDistance;
  
    }


 function calcular_minuto_con_google($latitud,$longitud,$latitud_fin,$longitud_fin){
        $url = "https://maps.googleapis.com/maps/api/directions/json?origin=".$latitud.",".$longitud."&destination=".$latitud_fin.",".$longitud_fin."&mode=driving&key=AIzaSyDJOgdNEw_LhTMyn7YvAgsRLSamN3ckCeQ";
    // create curl resource
    $ch = curl_init();
    // set url
    curl_setopt($ch, CURLOPT_URL, $url);


    //return the transfer as a string
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    // $output contains the output string
    $output = curl_exec($ch);

    // close curl resource to free up system resources
    curl_close($ch);

    //$arr = json_decode($output, TRUE);

    //Decode the JSON data we received.
    $json = json_decode(trim($output ), true);
     
    //Automatically select the first route that Google gave us.
    $route = $json['routes'][0];
     
    //Loop through the "legs" in our route and add up the distances.
    //print json_encode($json);

    $tiempo_2=0;

    foreach($route['legs'] as $leg){
        $tiempo_2=$leg['duration']['value'];
    }

    //Print out the result.
    // tiempo entre dos pùntos MINUTOS
    $tiempo_2=$tiempo_2/60;
    return $tiempo_2;
    }


 function calcular_distancia_minuto_con_google($latitud,$longitud,$latitud_fin,$longitud_fin){
  try{
     $url = "https://maps.googleapis.com/maps/api/directions/json?origin=".$latitud.",".$longitud."&destination=".$latitud_fin.",".$longitud_fin."&mode=driving&key=AIzaSyDJOgdNEw_LhTMyn7YvAgsRLSamN3ckCeQ";
    // create curl resource
    $ch = curl_init();
    // set url
    curl_setopt($ch, CURLOPT_URL, $url);


    //return the transfer as a string
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    // $output contains the output string
    $output = curl_exec($ch);

    // close curl resource to free up system resources
    curl_close($ch);

    //$arr = json_decode($output, TRUE);

    //Decode the JSON data we received.
    $json = json_decode(trim($output ), true);
     
    //Automatically select the first route that Google gave us.
    $route = $json['routes'][0];
     
    //Loop through the "legs" in our route and add up the distances.
    //print json_encode($json);

    $tiempo_2=0;
     $totalDistance = 0.0;

    foreach($route['legs'] as $leg){
        $tiempo_2=$leg['duration']['value'];
         $totalDistance = $totalDistance + $leg['distance']['value'];
    }

    //Print out the result.
    // tiempo entre dos pùntos MINUTOS
    $tiempo_2=$tiempo_2/60;


     
    //Loop through the "legs" in our route and add up the distances.
    //print json_encode($json);
 
  
      return array("distancia"=>$totalDistance,"tiempo"=>$tiempo_2);
    }catch(Exception $e){
      return array("distancia"=>0,"tiempo"=>0);
    }

  }

  function calcular_distancia($point1_lat, $point1_long, $point2_lat, $point2_long, $unit = 'mt', $decimals = 2) {
  // Cálculo de la distancia en grados
  $degrees = rad2deg(acos((sin(deg2rad($point1_lat))*sin(deg2rad($point2_lat))) + (cos(deg2rad($point1_lat))*cos(deg2rad($point2_lat))*cos(deg2rad($point1_long-$point2_long)))));
 
  // Conversión de la distancia en grados a la unidad escogida (kilómetros, millas o millas naúticas)
  switch($unit) {
     case 'mt':
      $distance = $degrees * 111.13384; // 1 grado = 111.13384 km, basándose en el diametro promedio de la Tierra (12.735 km)
      $distance = $distance *1000; // 1 grado = 111.13384 km, basándose en el diametro promedio de la Tierra (12.735 km)
      break;
    case 'km':
      $distance = $degrees * 111.13384; // 1 grado = 111.13384 km, basándose en el diametro promedio de la Tierra (12.735 km)
      break;
    case 'mi':
      $distance = $degrees * 69.05482; // 1 grado = 69.05482 millas, basándose en el diametro promedio de la Tierra (7.913,1 millas)
      break;
    case 'nmi':
      $distance =  $degrees * 59.97662; // 1 grado = 59.97662 millas naúticas, basándose en el diametro promedio de la Tierra (6,876.3 millas naúticas)
  }
  return round($distance, $decimals);
}

 function get_ruta_por_id($id_pedido,$id_carrera)
  {
    $consulta="SELECT * from ruta where id_carrera=? and id_pedido=? order by numero asc";
    $resultado=-1;
        try{
          $comando=parent::getInstance()->getDb()->prepare($consulta);
          $comando->execute(array($id_carrera,$id_pedido));
          $row=$comando->fetchAll();  
          if($row)
          {
            $resultado=$row;
          }
          
        }catch(PDOException $e)
        {
          $resultado= -1;
        }
        return $resultado;
  }

  function get_carrera_por_id_carrera($id_pedido,$id_carrera)
  {
    $consulta="SELECT * from carrera where id=? and id_pedido=?";
    $resultado=-1;
        try{
          $comando=parent::getInstance()->getDb()->prepare($consulta);
          $comando->execute(array($id_carrera,$id_pedido));
          $row=$comando->fetch(PDO::FETCH_ASSOC);  
          if($row)
          {
            $resultado=$row;
          }
          
        }catch(PDOException $e)
        {
          $resultado= -1;
        }
        return $resultado;
  }


}






?>