<?php

require_once('Basededatos.php');
include_once 'Push.php';
include_once 'Firebase.php';

class Compartir extends Database
{
  public function Compartir()
  {
    parent::Database();
  }

   function enviar_panico($id_usuario,$id_pedido)
  { 
    try{
        $actualizar="UPDATE pedido SET panico=1 where id_usuario=? and id=?";
         $update=parent::getInstance()->getDb()->prepare($actualizar);
         $update->execute(array($id_usuario,$id_pedido));

         $sw_panico=self::enviar_notificacion_panico_usuario_en_pedido($id_usuario,"Panico");

      return $sw_panico;
    } catch (PDOException $e) {
        return false;
    }   
  }
 
    function enviar_panico_conductor($id_conductor)
    { 
      try{
         $actualizar="UPDATE conductor SET panico=1 where ci=?";
         $update=parent::getInstance()->getDb()->prepare($actualizar);
         $update->execute(array($id_conductor));

         self::enviar_notificacion_panico_conductor($id_conductor,"Panico");


        return true;
      } catch (PDOException $e) {
          return false;
      }   
    }

    public static function enviar_notificacion_panico_conductor($id_conductor,$nombre)
    {

      try{
       $push = new Push('Conductor pide auxilio','Conductores con panico.',null,"taxi","",$nombre,0,0,"16");
       // obteniendo el empuje del objeto push
        $push->setIndicacion("");
         $mPushNotification = $push->getPush(); 
         
         // obtener el token del objeto de base de datos

         $devicetoken = self::get_token_conductores($id_conductor);    

        // creación de objeto de clase firebase
         $firebase = new Firebase(); 
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
        
        }
        catch (Exception $e){
         return false;
        }
    }

      public static function enviar_notificacion_panico_usuario_en_pedido($id_usuario,$nombre)
    {

      try{
       $push = new Push('Tu amigo pide auxilio','Usuario con panico.',null,"usuario","",$nombre,0,0,"16");
       // obteniendo el empuje del objeto push
        $push->setIndicacion("");
         $mPushNotification = $push->getPush(); 
         
         // obtener el token del objeto de base de datos

         $devicetoken = self::get_token_usuario_compartido($id_usuario);    

        // creación de objeto de clase firebase
         $firebase = new Firebase(); 
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
        
        }
        catch (Exception $e){
         return false;
        }
    }

       public static function enviar_notificacion_recorrido_compartido($id_usuario,$id_pedido)
    {
        $consulta="SELECT nombre,apellido from usuario where id=?";
        $comando=parent::getInstance()->getDb()->prepare($consulta);
        $comando->execute(array($id_usuario));
        $row=$comando->fetch(PDO::FETCH_ASSOC);

         
        try{
         $push = new Push('Compartio su Recorrido',$nombre,null,"usuario","","",0,0,"17");
       // obteniendo el empuje del objeto push
         $mPushNotification = $push->getPush(); 
         
          // obtener el token del objeto de base de datos

         $devicetoken = self::get_token_usuario_compartido_recorrido($id_pedido);    

        // creación de objeto de clase firebase
        $firebase = new Firebase(); 
         // envío de notificación push y visualización de resultados
         $sw_notificacion=$firebase->send($devicetoken, $mPushNotification);
         $sw_notificacion=true;
         if($sw_notificacion===false)
         {
          return false;
         }
         else
         {
         return true; 
         }
        
        }
        catch (Exception $e){
         return false;
        }
        
    }

    public static function get_token_conductores($id_conductor)
   {  
      $consulta="SELECT c.token from conductor c where c.ci<>?";
      $query = parent::getInstance()->getDb()->prepare($consulta);
        $query->execute(array($id_conductor)); 
         $tokens = array(); 

        while($row=$query->fetch(PDO::FETCH_OBJ)) {
           array_push($tokens, $row->token);
          }
   
        return $tokens; 
   }
 
   public static function get_token_usuario_compartido($id_usuario)
   {  
      $consulta="SELECT u.token from usuario u,usuario_panico up where u.id=up.id_usuario_panico and up.id_usuario=?";
      $query = parent::getInstance()->getDb()->prepare($consulta);
        $query->execute(array($id_usuario)); 
         $tokens = array(); 

        while($row=$query->fetch(PDO::FETCH_OBJ)) {
           array_push($tokens, $row->token);
          }
   
        return $tokens; 
   }

    public static function get_token_usuario_compartido_recorrido($id_pedido)
   {
      $tokens = array(); 

      $consulta="SELECT u.token from usuario u,pedido_compartido pc where  u.id=pc.id_usuario_compartido and pc.id_pedido=? ";
      $query = parent::getInstance()->getDb()->prepare($consulta);
        $query->execute(array($id_pedido)); 
        

        while($row=$query->fetch(PDO::FETCH_OBJ)) {
           array_push($tokens, $row->token);
          }
   
        return $tokens; 
   }
 

 function compartir_pedido($id_usuario,$id_pedido,$js_usuarios)
  { $total=0;
    $registrado=0;
     $array = json_decode($js_usuarios);
    try{
      if(is_array($array) || is_object($array)){
      foreach($array as $obj)
      {  $total=$total+1;
        $id_usuario_compartir=$obj->id_usuario;
          try{
        $consulta="INSERT INTO pedido_compartido(id_pedido,id_usuario_compartido) values(?,?)";
        $comando=parent::getInstance()->getDb()->prepare($consulta);
         $comando->execute(array($id_pedido,$id_usuario_compartir));
          $registrado=$registrado+1;
        } catch (PDOException $e) {
           return false;
        }
      }
     }
    }catch(Exception $e)
    {
     return false;
    }

    if($registrado>0)
    { 
      try{
       $actualizar="UPDATE pedido SET compartido=1 where id_pedido=? and id_usuario=?";
       $update=parent::getInstance()->getDb()->prepare($actualizar);
       $update->execute(array($id_pedido,$id_usuario));
       } catch (PDOException $e) {
         
      }
      
      return true;
    }
    else
      {return false;
      }
   
  }
  function insertar_compartir_carreras($id_usuario,$id_usuario_compartir)
  {
   try{
    $consulta="INSERT INTO compartir_carrera (id_usuario,id_usuario_compartir) values(?,?)";
    $comando=parent::getInstance()->getDb()->prepare($consulta);
     $comando->execute(array($id_usuario,$id_usuario_compartir));
      return true;
    } catch (PDOException $e) {
        return false;
    }
  }
  function insertar_compartir_panico($id_usuario,$id_usuario_panico)
  {
   try{
    $consulta="INSERT INTO usuario_panico (id_usuario,id_usuario_panico) values(?,?)";
    $comando=parent::getInstance()->getDb()->prepare($consulta);
     $comando->execute(array($id_usuario,$id_usuario_panico));
      return true;
    } catch (PDOException $e) {
        return false;
    }
  }
  function lista_de_usuarios_compartir($id_usuario)
  {$consulta="SELECT u.id,u.nombre,u.apellido,u.celular,u.correo,cc.estado as 'estado' from usuario u,compartir_carrera cc  where cc.id_usuario_compartir=u.id and cc.estado=1 and cc.id_usuario=?";
  $resultado=-1;
    try{
          $comando=parent::getInstance()->getDb()->prepare($consulta);
          $comando->execute(array($id_usuario));
          $row=$comando->fetchAll();  
          if($row)
          {
            $resultado=$row;
          }
      else
      {
        $resultado=-1;
      }
    }catch(PDOException $e)
    {
        $resultado=-1;
    }
    return $resultado;
  } 
   function lista_de_usuarios_compartir_panico($id_usuario)
  {$consulta="SELECT u.id,u.nombre,u.apellido,u.celular,u.correo from usuario u,usuario_panico cc where cc.id_usuario_panico=u.id and cc.id_usuario=?";
  $resultado=-1;
    try{
          $comando=parent::getInstance()->getDb()->prepare($consulta);
          $comando->execute(array($id_usuario));
          $row=$comando->fetchAll();  
          if($row)
          {
            $resultado=$row;
          }
      else
      {
        $resultado=-1;
      }
    }catch(PDOException $e)
    {
        $resultado=-1;
    }
    return $resultado;
  } 

  function lista_de_sub_usuarios_por_id_usuario($id_usuario)
  {$consulta="SELECT u.id as 'id_usuario',concat(u.nombre,' ',u.apellido)as 'nombre',p.direccion, p.estado from usuario u, pedido p,pedido_compartido pc where p.id=pc.id_pedido and p.id_usuario=u.id and pc.id_usuario_compartido=? order by p.estado asc";
  $resultado=-1;
    try{
          $comando=parent::getInstance()->getDb()->prepare($consulta);
          $comando->execute(array($id_usuario));
          $row=$comando->fetchAll();  
          if($row)
          {
            $resultado=$row;
          }
      else
      {
        $resultado=-1;
      }
    }catch(PDOException $e)
    {
        $resultado=-1;
    }
    return $resultado;
  } 

  function lista_de_carreras_compartidas_por_id_usuario($id_usuario,$id_usuario_compartido)
  {$consulta="SELECT con.id_empresa,u.id as 'id_usuario',c.fecha_inicio 'fecha_inicio',p.estado as 'estado_pedido', c.estado as 'estado_carrera',p.id as 'id_pedido',c.id as 'id_carrera',c.id_conductor,concat(con.nombre,' ',con.paterno,' ',con.materno) as 'conductor',ve.marca,ve.color,e.razon_social,con.celular as 'celular_conductor',u.nombre as 'nombre_usuario', u.apellido as 'apellido_usuario',u.celular as 'celular_usuario',ve.placa,c.ruta as 'url' from empresa e, usuario u, pedido p,pedido_compartido pc,carrera c,conductor con,vehiculo ve where e.id=con.id_empresa and ve.placa=c.id_vehiculo and con.ci=c.id_conductor and p.id=pc.id_pedido and p.id_usuario=u.id and c.id_pedido=p.id and u.id=? AND pc.id_usuario_compartido=? order by p.estado,c.estado asc";
  $resultado=-1;
    try{
          $comando=parent::getInstance()->getDb()->prepare($consulta);
          $comando->execute(array($id_usuario,$id_usuario_compartido));
          $row=$comando->fetchAll();  
          if($row)
          {
            $resultado=$row;
          }
      else
      {
        $resultado=-1;
      }
    }catch(PDOException $e)
    {
        $resultado=-1;
    }
    return $resultado;
  } 

   function lista_conductor_panico($id_conductor)
  {$consulta="SELECT c.ci as 'id', concat(c.nombre,' ',c.paterno,' ',c.materno)as 'nombre',a.latitud,a.longitud,e.razon_social as 'empresa',a.codigo_empresa,a.id_empresa from conductor c, asignacion a, empresa e where c.ci=a.id_conductor and a.id_empresa=e.id and c.id_empresa=e.id and c.panico=1";
  $resultado=-1;
    try{
          $comando=parent::getInstance()->getDb()->prepare($consulta);
          $comando->execute();
          $row=$comando->fetchAll();  
          if($row)
          {
            $resultado=$row;
          }
      else
      {
        $resultado=-1;
      }
    }catch(PDOException $e)
    {
        $resultado=-1;
    }
    return $resultado;
  } 

   function lista_usuario_panico_por_id_usuario($id_usuario)
  {$consulta="SELECT u.id as 'id_usuario',c.ci as 'id_conductor', concat(u.nombre,' ',u.apellido) as usuario ,u.celular as 'celular_pasajero' ,a.latitud,a.longitud,concat(c.nombre,' ',c.paterno,' ',c.materno) as 'conductor',c.celular as 'celular_conductor',e.razon_social,m.marca,m.placa,m.color,p.id as 'id_pedido' from usuario u, usuario_panico up, pedido p,asignacion a,conductor c,empresa e,vehiculo v where  c.ci=a.id_conductor and v.placa=a.id_vehiculo and e.id=c.id_empresa and  u.id=up.id_usuario and up.id_usuario_panico=? and p.id_usuario=u.id and p.id_conductor=a.id_conductor and p.id_vehiculo=a.id_vehiculo and p.panico=1 and p.estado=1";
  $resultado=-1;
    try{
          $comando=parent::getInstance()->getDb()->prepare($consulta);
          $comando->execute(array($id_usuario));
          $row=$comando->fetchAll();  
          if($row)
          {
            $resultado=$row;
          }
      else
      {
        $resultado=-1;
      }
    }catch(PDOException $e)
    {
        $resultado=-1;
    }
    return $resultado;
  } 

//OBTENER  LA LISTA DE LOS USUARIO CON PANICO SOLO LOS DATOS DE (ID_PEDIDO,LATITUD,LONGITUD)
function lista_usuario_panico_por_id_usuario_rec($id_usuario)
  {$consulta="SELECT a.latitud,a.longitud,p.id as 'id_pedido' from usuario u, usuario_panico up, pedido p,asignacion a where u.id=up.id_usuario and up.id_usuario_panico=? and p.id_usuario=u.id and p.id_conductor=a.id_conductor and p.id_vehiculo=a.id_vehiculo and p.panico=1 and p.estado=1";
  $resultado=-1;
    try{
          $comando=parent::getInstance()->getDb()->prepare($consulta);
          $comando->execute(array($id_usuario));
          $row=$comando->fetchAll();  
          if($row)
          {
            $resultado=$row;
          }
      else
      {
        $resultado=-1;
      }
    }catch(PDOException $e)
    {
        $resultado=-1;
    }
    return $resultado;
  } 



   function buscar_usuario($id_usuario,$celular)
  {$consulta="SELECT u.id,u.nombre,u.apellido,u.celular,u.correo from usuario u  where u.estado='1' and u.nombre like '%".$celular."%'  or u.celular like '%".$celular."%' or u.correo like '%".$celular."%' limit 10";
  $resultado=-1;
    try{
          $comando=parent::getInstance()->getDb()->prepare($consulta);
          $comando->execute();
          $row=$comando->fetchAll();  
          if($row)
          {
            $resultado=$row;
          }
      else
      {
        $resultado=-1;
      }
    }catch(PDOException $e)
    {
        $resultado=-1;
    }
    return $resultado;
  } 

  

function eliminar_compartir_carrera($id_usuario,$id_usuario_compartir)
  {$consulta="DELETE from compartir_carrera where id_usuario=? and id_usuario_compartir=?";

    try{
          $comando=parent::getInstance()->getDb()->prepare($consulta);
      $comando->execute(array($id_usuario,$id_usuario_compartir));

      return true;
    }catch(PDOException $e)
    {
      return false;
    }
  }

}




?>