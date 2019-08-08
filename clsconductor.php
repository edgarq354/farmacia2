<?php
require('Basededatos.php');
require_once 'Firebase.php';
require_once 'Push.php';
class conductor extends Database
 {
  public function conductor()
  {
    parent::Database();
  }


    public static function  notificacion_conductor()
    {
        try{
       $push = new Push('Sistema',"Servicio abierto de aplicación",null,"taxi","","","0","0","25");
       // obteniendo el empuje del objeto push
       $mPushNotification = $push->getPush(); 
       
       // obtener el token del objeto de base de datos

       $devicetoken = self::get_token_conductor();     

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

    public static   function get_token_conductor()
   { $query = parent::getInstance()->getDb()->prepare("SELECT token,TIMESTAMPDIFF(MINUTE,fecha_ultimo,now()) as tiempo from conductor where estado=1 and login=1 and TIMESTAMPDIFF(MINUTE,fecha_ultimo,now())>=1");
        $query->execute(); 
         $tokens = array(); 
        while($row=$query->fetch(PDO::FETCH_OBJ)) {
      array_push($tokens, $row->token);
    }
        return $tokens; 
   }



  public static function actualizar_token($id_conductor,$token)
   {
    try{
    $consulta="UPDATE conductor SET token=? where ci=?";
    $comando=parent::getInstance()->getDb()->prepare($consulta);
    $comando->execute(array($token,$id_conductor));
  return true;
    } catch (PDOException $e) {
     return false;
    }
   }

public static function actualizar_login($id_conductor,$login,$imei)
   {
    try{
    $consulta="UPDATE conductor SET login=?,estado=?,imei=? where ci=?";
    $comando=parent::getInstance()->getDb()->prepare($consulta);
    $comando->execute(array($login,$login,$imei,$id_conductor));
  return true;
    } catch (PDOException $e) {
     return false;
    }
   }

public static function actualizar_datos_conductor($ci,$email,$imei,$nro_ham,$nro_transito,$nro_factura_luz,$nro_antecedente_transito,$nro_antecedente_felcc)
   {
    try{
  



    $consulta="UPDATE conductor SET correo=?,imei=? where ci=?";
    $comando=parent::getInstance()->getDb()->prepare($consulta);
    $comando->execute(array($email,$imei,$ci));

    self::actualizar_nro_ci($ci,$ci);
    self::actualizar_nro_licencia($ci,$ci);
    self::actualizar_nro_ham($ci,$nro_ham);
    self::actualizar_nro_transito($ci,$nro_transito);
    self::actualizar_nro_antecedente_transito($ci,$nro_antecedente_transito);
    self::actualizar_nro_antecedente_felcc($ci,$nro_antecedente_felcc);
    self::actualizar_nro_factura_luz($ci,$nro_factura_luz);


  return true;
    } catch (PDOException $e) {
     return false;
    }
   }

public static function actualizar_datos_vehiculo($placa,$modelo,$cilindrada,$tipo,$nro_chasis,$nro_poliza,$nro_rua,$nro_soat,$nro_puerta,$nro_asientos,$nro_inspeccion_tecnica,$color)

   {
    try{
    $consulta="UPDATE vehiculo SET modelo=?,cilindrada=?, tipo=?, nro_chasis=?,nro_poliza=?,nro_rua=?,nro_soat=?,nro_puertas=?,nro_asientos=?,nro_inspeccion_tecnica=?,color=? where placa=?";
    $comando=parent::getInstance()->getDb()->prepare($consulta);
    $comando->execute(array($modelo,$cilindrada,$tipo,$nro_chasis,$nro_poliza,$nro_rua,$nro_soat,$nro_puerta,$nro_asientos,$nro_inspeccion_tecnica,$color,$placa));

    $uno=self::actualizar_nro_rua($placa,$nro_rua);
    $dos=self::actualizar_nro_soat($placa,$nro_soat);
    $tres=self::actualizar_nro_inspeccion_tecnica($placa,$nro_inspeccion_tecnica);

  if($uno==true && $dos==true && $tres==true)
  {
    return true;
  }
  else return false;

    } catch (PDOException $e) {
     return false;
    }
   }

   public static function actualizar_nro_rua($id_vehiculo,$nro)
  {//ACTUALIZAR DATOS DEL VEHICULO
    $cantidad=0;
        $id_fotocopia=0;
    
    try{
    $consulta="SELECT max(f.id) as 'cantidad',f.id from fotocopia f, vehiculo v WHERE  f.id=v.id_fotocopia_rua and v.placa=? ";
    $comando=parent::getInstance()->getDb()->prepare($consulta);
    $comando->execute(array($id_taxi));
      $row=$comando->fetch(PDO::FETCH_ASSOC);
      
            if($row)
            {
              $cantidad=$row["cantidad"];
              $id_fotocopia=$row["id"];
            }
            
    } catch (PDOException $e) {
            $cantidad=0;
            $id_fotocopia=0;
    }
         $cantidad+=1;

  try{

                   if($cantidad==1){
                    try{
                    $consulta="INSERT into fotocopia (nombre,numero) values('RUA',?) ";
                    $comando=parent::getInstance()->getDb()->prepare($consulta);
                    $comando->execute(array($nro));
                    $id_fotocopia = parent::getInstance()->getDb()->lastInsertId();
                    }catch(Exception $e){
                    }
                    }
                    else{
                      try{
                      $consulta="UPDATE fotocopia set numero=? where id=?";
                      $comando=parent::getInstance()->getDb()->prepare($consulta);
                      $comando->execute(array($nro,$id_fotocopia));
                       }catch(Exception $e){
                      }
                    }
                   
            try{
            $consulta="UPDATE vehiculo set id_fotocopia_rua=? where placa=?";
            $comando=parent::getInstance()->getDb()->prepare($consulta);
            $comando->execute(array($id_fotocopia,$id_taxi));
             }catch(Exception $e){
            }
      return true;
    } catch (PDOException $e) {
      return false;
    }
  } 

     public static function actualizar_nro_soat($id_vehiculo,$nro)
  {//ACTUALIZAR DATOS DEL VEHICULO
    $cantidad=0;
        $id_fotocopia=0;
    
    try{
    $consulta="SELECT max(f.id) as 'cantidad',f.id from fotocopia f, vehiculo v WHERE  f.id=v.id_fotocopia_soat and v.placa=? ";
    $comando=parent::getInstance()->getDb()->prepare($consulta);
    $comando->execute(array($id_taxi));
      $row=$comando->fetch(PDO::FETCH_ASSOC);
      
            if($row)
            {
              $cantidad=$row["cantidad"];
              $id_fotocopia=$row["id"];
            }
            
    } catch (PDOException $e) {
            $cantidad=0;
            $id_fotocopia=0;
    }
         $cantidad+=1;

  try{

                   if($cantidad==1){
                    try{
                    $consulta="INSERT into fotocopia (nombre,numero) values('SOAT',?) ";
                    $comando=parent::getInstance()->getDb()->prepare($consulta);
                    $comando->execute(array($nro));
                    $id_fotocopia = parent::getInstance()->getDb()->lastInsertId();
                    }catch(Exception $e){
                    }
                    }
                    else{
                      try{
                      $consulta="UPDATE fotocopia set numero=? where id=?";
                      $comando=parent::getInstance()->getDb()->prepare($consulta);
                      $comando->execute(array($nro,$id_fotocopia));
                       }catch(Exception $e){
                      }
                    }
                   
            try{
            $consulta="UPDATE vehiculo set id_fotocopia_rua=? where placa=?";
            $comando=parent::getInstance()->getDb()->prepare($consulta);
            $comando->execute(array($id_fotocopia,$id_taxi));
             }catch(Exception $e){
            }
      return true;
    } catch (PDOException $e) {
      return false;
    }
  } 
   
     public static function actualizar_nro_inspeccion_tecnica($id_vehiculo,$nro)
  {//ACTUALIZAR DATOS DEL VEHICULO
    $cantidad=0;
        $id_fotocopia=0;
    
    try{
    $consulta="SELECT max(f.id) as 'cantidad',f.id from fotocopia f, vehiculo v WHERE  f.id=v.id_foto_inspeccion_tecnica and v.placa=? ";
    $comando=parent::getInstance()->getDb()->prepare($consulta);
    $comando->execute(array($id_taxi));
      $row=$comando->fetch(PDO::FETCH_ASSOC);
      
            if($row)
            {
              $cantidad=$row["cantidad"];
              $id_fotocopia=$row["id"];
            }
            
    } catch (PDOException $e) {
            $cantidad=0;
            $id_fotocopia=0;
    }
         $cantidad+=1;

  try{

                   if($cantidad==1){
                    try{
                    $consulta="INSERT into fotocopia (nombre,numero) values('INSPECCION TECNICA',?) ";
                    $comando=parent::getInstance()->getDb()->prepare($consulta);
                    $comando->execute(array($nro));
                    $id_fotocopia = parent::getInstance()->getDb()->lastInsertId();
                    }catch(Exception $e){
                    }
                    }
                    else{
                      try{
                      $consulta="UPDATE fotocopia set numero=? where id=?";
                      $comando=parent::getInstance()->getDb()->prepare($consulta);
                      $comando->execute(array($nro,$id_fotocopia));
                       }catch(Exception $e){
                      }
                    }
                   
            try{
            $consulta="UPDATE vehiculo set id_foto_inspeccion_tecnica=? where placa=?";
            $comando=parent::getInstance()->getDb()->prepare($consulta);
            $comando->execute(array($id_fotocopia,$id_taxi));
             }catch(Exception $e){
            }
      return true;
    } catch (PDOException $e) {
      return false;
    }
  } 

    public static function actualizar_nro_ci($id_taxi,$nro)
  {
    $cantidad=0;
        $id_fotocopia=0;
    
    try{
    $consulta="SELECT max(f.id) as 'cantidad',f.id from fotocopia f, conductor c WHERE  f.id=c.id_fotocopia_ci and c.ci=? ";
    $comando=parent::getInstance()->getDb()->prepare($consulta);
    $comando->execute(array($id_taxi));
      $row=$comando->fetch(PDO::FETCH_ASSOC);
      
            if($row)
            {
              $cantidad=$row["cantidad"];
              $id_fotocopia=$row["id"];
            }
            
    } catch (PDOException $e) {
            $cantidad=0;
            $id_fotocopia=0;
    }
         $cantidad+=1;

  try{

                   if($cantidad==1){
                    try{
                    $consulta="INSERT into fotocopia (nombre,numero) values('LICENCIA DE CONDUCIR',?) ";
                    $comando=parent::getInstance()->getDb()->prepare($consulta);
                    $comando->execute(array($nro));
                    $id_fotocopia = parent::getInstance()->getDb()->lastInsertId();
                    }catch(Exception $e){
                    }
                    }
                    else{
                      try{
                      $consulta="UPDATE fotocopia set numero=? where id=?";
                      $comando=parent::getInstance()->getDb()->prepare($consulta);
                      $comando->execute(array($nro,$id_fotocopia));
                       }catch(Exception $e){
                      }
                    }
                   
            try{
            $consulta="UPDATE conductor set id_fotocopia_ci=? where ci=?";
            $comando=parent::getInstance()->getDb()->prepare($consulta);
            $comando->execute(array($id_fotocopia,$id_taxi));
             }catch(Exception $e){
            }
      return true;
    } catch (PDOException $e) {
      return false;
    }
  } 

  public static function actualizar_nro_licencia($id_taxi,$nro)
  {
    $cantidad=0;
        $id_fotocopia=0;
    
    try{
    $consulta="SELECT max(f.id) as 'cantidad',f.id from fotocopia f, conductor c WHERE  f.id=c.id_fotocopia_licencia and c.ci=? ";
    $comando=parent::getInstance()->getDb()->prepare($consulta);
    $comando->execute(array($id_taxi));
      $row=$comando->fetch(PDO::FETCH_ASSOC);
      
            if($row)
            {
              $cantidad=$row["cantidad"];
              $id_fotocopia=$row["id"];
            }
            
    } catch (PDOException $e) {
            $cantidad=0;
            $id_fotocopia=0;
    }
         $cantidad+=1;

  try{

                   if($cantidad==1){
                    try{
                    $consulta="INSERT into fotocopia (nombre,numero) values('CI',?) ";
                    $comando=parent::getInstance()->getDb()->prepare($consulta);
                    $comando->execute(array($nro));
                    $id_fotocopia = parent::getInstance()->getDb()->lastInsertId();
                    }catch(Exception $e){
                    }
                    }
                    else{
                      try{
                      $consulta="UPDATE fotocopia set numero=? where id=?";
                      $comando=parent::getInstance()->getDb()->prepare($consulta);
                      $comando->execute(array($nro,$id_fotocopia));
                       }catch(Exception $e){
                      }
                    }
                   
            try{
            $consulta="UPDATE conductor set id_fotocopia_licencia=? where ci=?";
            $comando=parent::getInstance()->getDb()->prepare($consulta);
            $comando->execute(array($id_fotocopia,$id_taxi));
             }catch(Exception $e){
            }
      return true;
    } catch (PDOException $e) {
      return false;
    }
  } 

  public static function actualizar_nro_ham($id_taxi,$nro)
  {
    $cantidad=0;
        $id_fotocopia=0;
    
    try{
    $consulta="SELECT max(f.id) as 'cantidad',f.id from fotocopia f, conductor c WHERE  f.id=c.id_fotocopia_tarjeta_de_identificacion_de_ham and c.ci=? ";
    $comando=parent::getInstance()->getDb()->prepare($consulta);
    $comando->execute(array($id_taxi));
      $row=$comando->fetch(PDO::FETCH_ASSOC);
      
            if($row)
            {
              $cantidad=$row["cantidad"];
              $id_fotocopia=$row["id"];
            }
            
    } catch (PDOException $e) {
            $cantidad=0;
            $id_fotocopia=0;
    }
         $cantidad+=1;

  try{

                   if($cantidad==1){
                    try{
                    $consulta="INSERT into fotocopia (nombre,numero) values('TARJETA DE IDENTIFICACION DE HAM',?) ";
                    $comando=parent::getInstance()->getDb()->prepare($consulta);
                    $comando->execute(array($nro));
                    $id_fotocopia = parent::getInstance()->getDb()->lastInsertId();
                    }catch(Exception $e){
                    }
                    }
                    else{
                      try{
                      $consulta="UPDATE fotocopia set numero=? where id=?";
                      $comando=parent::getInstance()->getDb()->prepare($consulta);
                      $comando->execute(array($nro,$id_fotocopia));
                       }catch(Exception $e){
                      }
                    }
                   
            try{
            $consulta="UPDATE conductor set id_fotocopia_tarjeta_de_identificacion_de_ham=? where ci=?";
            $comando=parent::getInstance()->getDb()->prepare($consulta);
            $comando->execute(array($id_fotocopia,$id_taxi));
             }catch(Exception $e){
            }
      return true;
    } catch (PDOException $e) {
      return false;
    }
  } 

public static function actualizar_nro_transito($id_taxi,$nro)
  {
    $cantidad=0;
        $id_fotocopia=0;
    
    try{
    $consulta="SELECT max(f.id) as 'cantidad',f.id from fotocopia f, conductor c WHERE  f.id=c.id_fotocopia_tarjeta_de_identificacion_de_transito and c.ci=? ";
    $comando=parent::getInstance()->getDb()->prepare($consulta);
    $comando->execute(array($id_taxi));
      $row=$comando->fetch(PDO::FETCH_ASSOC);
      
            if($row)
            {
              $cantidad=$row["cantidad"];
              $id_fotocopia=$row["id"];
            }
            
    } catch (PDOException $e) {
            $cantidad=0;
            $id_fotocopia=0;
    }
         $cantidad+=1;

  try{

                   if($cantidad==1){
                    try{
                    $consulta="INSERT into fotocopia (nombre,numero) values('TARJETA DE IDENTIFICACION DE TRANSITO',?) ";
                    $comando=parent::getInstance()->getDb()->prepare($consulta);
                    $comando->execute(array($nro));
                    $id_fotocopia = parent::getInstance()->getDb()->lastInsertId();
                    }catch(Exception $e){
                    }
                    }
                    else{
                      try{
                      $consulta="UPDATE fotocopia set numero=? where id=?";
                      $comando=parent::getInstance()->getDb()->prepare($consulta);
                      $comando->execute(array($nro,$id_fotocopia));
                       }catch(Exception $e){
                      }
                    }
                   
            try{
            $consulta="UPDATE conductor set id_fotocopia_tarjeta_de_identificacion_de_transito=? where ci=?";
            $comando=parent::getInstance()->getDb()->prepare($consulta);
            $comando->execute(array($id_fotocopia,$id_taxi));
             }catch(Exception $e){
            }
      return true;
    } catch (PDOException $e) {
      return false;
    }
  } 

  public static function actualizar_nro_factura_luz($id_taxi,$nro)
  {
    $cantidad=0;
        $id_fotocopia=0;
    
    try{
    $consulta="SELECT max(f.id) as 'cantidad',f.id from fotocopia f, conductor c WHERE  f.id=c.id_fotocopia_factura_de_agua_luz and c.ci=? ";
    $comando=parent::getInstance()->getDb()->prepare($consulta);
    $comando->execute(array($id_taxi));
      $row=$comando->fetch(PDO::FETCH_ASSOC);
      
            if($row)
            {
              $cantidad=$row["cantidad"];
              $id_fotocopia=$row["id"];
            }
            
    } catch (PDOException $e) {
            $cantidad=0;
            $id_fotocopia=0;
    }
         $cantidad+=1;

  try{

                   if($cantidad==1){
                    try{
                    $consulta="INSERT into fotocopia (nombre,numero) values('FACTURA DE AGUA O LUZ',?) ";
                    $comando=parent::getInstance()->getDb()->prepare($consulta);
                    $comando->execute(array($nro));
                    $id_fotocopia = parent::getInstance()->getDb()->lastInsertId();
                    }catch(Exception $e){
                    }
                    }
                    else{
                      try{
                      $consulta="UPDATE fotocopia set numero=? where id=?";
                      $comando=parent::getInstance()->getDb()->prepare($consulta);
                      $comando->execute(array($nro,$id_fotocopia));
                       }catch(Exception $e){
                      }
                    }
                   
            try{
            $consulta="UPDATE conductor set id_fotocopia_factura_de_agua_luz=? where ci=?";
            $comando=parent::getInstance()->getDb()->prepare($consulta);
            $comando->execute(array($id_fotocopia,$id_taxi));
             }catch(Exception $e){
            }
      return true;
    } catch (PDOException $e) {
      return false;
    }
  } 

    public static function actualizar_nro_antecedente_transito($id_taxi,$nro)
  {
    $cantidad=0;
        $id_fotocopia=0;
    
    try{
    $consulta="SELECT max(f.id) as 'cantidad',f.id from fotocopia f, conductor c WHERE  f.id=c.id_fotocopia_certificado_de_antecedentes_del_transito and c.ci=? ";
    $comando=parent::getInstance()->getDb()->prepare($consulta);
    $comando->execute(array($id_taxi));
      $row=$comando->fetch(PDO::FETCH_ASSOC);
      
            if($row)
            {
              $cantidad=$row["cantidad"];
              $id_fotocopia=$row["id"];
            }
            
    } catch (PDOException $e) {
            $cantidad=0;
            $id_fotocopia=0;
    }
         $cantidad+=1;

  try{

                   if($cantidad==1){
                    try{
                    $consulta="INSERT into fotocopia (nombre,numero) values('CERTIFICADO DE ANTECEDENTES DEL TRANSITO',?) ";
                    $comando=parent::getInstance()->getDb()->prepare($consulta);
                    $comando->execute(array($nro));
                    $id_fotocopia = parent::getInstance()->getDb()->lastInsertId();
                    }catch(Exception $e){
                    }
                    }
                    else{
                      try{
                      $consulta="UPDATE fotocopia set numero=? where id=?";
                      $comando=parent::getInstance()->getDb()->prepare($consulta);
                      $comando->execute(array($nro,$id_fotocopia));
                       }catch(Exception $e){
                      }
                    }
                   
            try{
            $consulta="UPDATE conductor set id_fotocopia_certificado_de_antecedentes_del_transito=? where ci=?";
            $comando=parent::getInstance()->getDb()->prepare($consulta);
            $comando->execute(array($id_fotocopia,$id_taxi));
             }catch(Exception $e){
            }
      return array($cantidad, $id_fotocopia);
    } catch (PDOException $e) {
      return false;
    }
  }

      public static function actualizar_nro_antecedente_felcc($id_taxi,$nro)
  {
    $cantidad=0;
        $id_fotocopia=0;
    
    try{
    $consulta="SELECT max(f.id) as 'cantidad',f.id from fotocopia f, conductor c WHERE  f.id=c.id_fotocopia_certificado_de_antecedentes_del_felcc and c.ci=? ";
    $comando=parent::getInstance()->getDb()->prepare($consulta);
    $comando->execute(array($id_taxi));
      $row=$comando->fetch(PDO::FETCH_ASSOC);
      
            if($row)
            {
              $cantidad=$row["cantidad"];
              $id_fotocopia=$row["id"];
            }
            
    } catch (PDOException $e) {
            $cantidad=0;
            $id_fotocopia=0;
    }
         $cantidad+=1;

  try{

                   if($cantidad==1){
                    try{
                    $consulta="INSERT into fotocopia (nombre,numero) values('CERTIFICADO DE ANTECEDENTE DEL FELCC',?) ";
                    $comando=parent::getInstance()->getDb()->prepare($consulta);
                    $comando->execute(array($nro));
                    $id_fotocopia = parent::getInstance()->getDb()->lastInsertId();
                    }catch(Exception $e){
                    }
                    }
                    else{
                      try{
                      $consulta="UPDATE fotocopia set numero=? where id=?";
                      $comando=parent::getInstance()->getDb()->prepare($consulta);
                      $comando->execute(array($nro,$id_fotocopia));
                       }catch(Exception $e){
                      }
                    }
                   
            try{
            $consulta="UPDATE conductor set id_fotocopia_certificado_de_antecedentes_del_felcc=? where ci=?";
            $comando=parent::getInstance()->getDb()->prepare($consulta);
            $comando->execute(array($id_fotocopia,$id_taxi));
             }catch(Exception $e){
            }
      return true;
    } catch (PDOException $e) {
      return false;
    }
  }



public static function get_cargar_codigo($id_usuario,$codigo)
   {
    /*
    0: error al registrar.
  1: carga de codigo correctamente.
  2: codigo con fecha caducada.
  3: codigo ya usado anteriormente.
  4: codigo ya usado anteriormente.
  5: codigo incorrecto.
  6: codigo de compartir ya agregado anteriormente.

    */
    $resultado="0";


      if($codigo>=10001)
      {


        $row="";
          try{
          $consulta="SELECT *,date(now()) as 'hoy' from codigo where  id=?";
          $comando=parent::getInstance()->getDb()->prepare($consulta);
          $comando->execute(array($codigo));
          $row=$comando->fetch(PDO::FETCH_ASSOC);
          } catch (PDOException $e) {
            
          }
         if($row)
          {
              $fecha_inicio = strtotime($row['fecha_inicio']);
                $fecha_fin = strtotime($row['fecha_fin']);
                $fecha = strtotime($row['hoy']);
                $monto=$row['monto'];

               

            if(($fecha >= $fecha_inicio) && ($fecha <= $fecha_fin)) {
              $sw=false;
              try{
                $comando=parent::getInstance()->getDb()->prepare("INSERT into conductor_codigo  (id_conductor,id_codigo) values(?,?) ");
              $comando->execute(array($id_usuario,$codigo));
              $sw=true;
            } catch (PDOException $eee) {
              $sw=false;
               
            }
            if($sw==true)
            {

            try{
               $consulta = "UPDATE conductor set credito=credito+? where ci=?";
             $comando = parent::getInstance()->getDb()->prepare($consulta);
             $comando->execute(array($monto,$id_usuario));
             $resultado="1";
            } catch (PDOException $ee) {
            
              $resultado="3";
            }

            }else
            {
              $resultado="4";

            }

             


          }else{
              $resultado="2";
          }


          }
          else{
             $resultado="5";
          }
          
      }

    return $resultado;
   }

    public static  function iniciar_sesion($usuario,$contrasenia,$token,$imei)
   {$resultado=-1;
    
  /*
  resultados:
    -1=error al iniciar sesion.
    1=cuenta iniciada en otro celular.
    2=cuenta inactiva.
    3=conductor que aun no esta asignado.
    4=contraseña incorrecta.

  */

    if(self::existe_conductor($usuario,$contrasenia)){
       $resultado=3;
          if(self::existe_asignacion_por_cuenta($usuario,$contrasenia)){

              if(self::existe_asignacion_iniciado($usuario,$contrasenia)){
                  $resultado=1;
              }else{
                  try{
                        $consulta="SELECT c.*,v.* from conductor c,vehiculo v 
                        where  c.id_vehiculo=v.placa and c.login=0 and c.usuario=? 
                        and c.contrasenia=? LIMIT 1";
                        $comando=parent::getInstance()->getDb()->prepare($consulta);
                        $comando->execute(array($usuario,$contrasenia));
                        $row=$comando->fetch(PDO::FETCH_ASSOC);
                       
                        if($row)
                        {
                          $resultado=$row;
                          self::actualizar_login($row['ci'],'1',$imei);
                          self::actualizar_token($row['ci'],$token);
                        }
                      } catch (PDOException $e) {
                        $resultado=2;
                      }
              }
          }
      } else 
      {
        $resultado=4;
      }
      return $resultado;
   }


public function existe_conductor($usuario,$contrasenia){
   try{
    $consulta="SELECT nombre from conductor where usuario=? and contrasenia=?";
    $comando=parent::getInstance()->getDb()->prepare($consulta);
    $comando->execute(array($usuario,$contrasenia));
    $row = $comando->fetch(PDO::FETCH_ASSOC);
    if($row)
        return true;
      else
        return false;
  }catch(PDOException $e)
  {
    return false;
  } 
}
public function existe_asignacion_por_cuenta($usuario,$contrasenia){
   try{
    $consulta="SELECT c.ci as 'id_conductor' from conductor c,vehiculo v where v.placa=c.id_vehiculo and  c.usuario=? 
    and c.contrasenia=?";
    $comando=parent::getInstance()->getDb()->prepare($consulta);
    $comando->execute(array($usuario,$contrasenia));
    $row = $comando->fetch(PDO::FETCH_ASSOC);
    if($row)
        return true;
      else
        return false;
  }catch(PDOException $e)
  {
    return false;
  } 
}
public function existe_asignacion_iniciado($usuario,$contrasenia){
   try{
    $consulta="SELECT c.ci as 'id_conductor' from vehiculo v,conductor c where  v.placa=c.id_vehiculo and c.login=1 and c.usuario=? and c.contrasenia=?";
    $comando=parent::getInstance()->getDb()->prepare($consulta);
    $comando->execute(array($usuario,$contrasenia));
    $row = $comando->fetch(PDO::FETCH_ASSOC);
    if($row)
        return true;
      else
        return false;
  }catch(PDOException $e)
  {
    return false;
  } 
}

  
 

  public static function get_conductor_en_rango($latitud,$longitud,$diametro)
  {
    try{
    $consulta="SELECT *,distancia_entre_dos_puntos(latitud,longitud,?,?) as 'distancia' from conductor where distancia_entre_dos_puntos(latitud,longitud,?,?)<= ? and estado='1' and login='1'";
    $comando=parent::getInstance()->getDb()->prepare($consulta);
    $comando->execute(array($latitud,$longitud,$latitud,$longitud,$diametro));
    $row = $comando->fetchAll();
    if($row)

        return $row;
      else
        return -1;
  }catch(PDOException $e)
  {
    return -1;
  } 
  }

  
  public static function get_conductor_libre($ci)
  {
    try{
    $consulta="SELECT c.* from conductor c where c.id_vehiculo='0' and ci like '%?%' and estado<>6";
    $comando=parent::getInstance()->getDb()->prepare($consulta);
    $comando->execute(array($ci));
    $row = $comando->fetchAll();
    if($row)

        return $row;
      else
        return -1;
    }catch(PDOException $e)
    {
      return -1;
    } 
  }


public static function get_credito($id_conductor)
  {
    try{
    $consulta="SELECT credito from conductor where ci=?";
    $comando=parent::getInstance()->getDb()->prepare($consulta);
    $comando->execute(array($id_conductor));
    $row=$comando->fetch(PDO::FETCH_ASSOC);
    if($row)
        return $row['credito'];
      else
        return -1;
  }catch(PDOException $e)
  {
    return -1;
  } 
  }
  public static function get_calificacion_conductor($id_conductor)
  {
    try{
    $consulta="SELECT calificacion from conductor where ci=?";
    $comando=parent::getInstance()->getDb()->prepare($consulta);
    $comando->execute(array($id_conductor));
    $row=$comando->fetch(PDO::FETCH_ASSOC);
    if($row)
        return $row['calificacion'];
      else
        return -1;
  }catch(PDOException $e)
  {
    return -1;
  } 
  }

  public static function get_calificacion_vehiculo($id_conductor)
  {
    try{
    $consulta="SELECT v.calificacion from vehiculo v,conductor c where  
     v.placa=c.id_vehiculo and c.estado=1 and c.ci=? limit 1";
    $comando=parent::getInstance()->getDb()->prepare($consulta);
    $comando->execute(array($id_conductor));
    $row=$comando->fetch(PDO::FETCH_ASSOC);
    if($row)
        return $row['calificacion'];
      else
        return -1;
  }catch(PDOException $e)
  {
    return -1;
  } 
  }

  public static function verificar_conductor_qr($id_conductor)
  {
    try{
      $codigo=base64_decode($id_conductor);
      $dato_conductor=str_replace("taxivalle", "", $codigo);

    $consulta="SELECT ci from conductor where ci=? ";
    $comando=parent::getInstance()->getDb()->prepare($consulta);
    $comando->execute(array($dato_conductor));
    $row=$comando->fetch(PDO::FETCH_ASSOC);
    if($row['ci']==$dato_conductor)
        return true;
      else
        return false;
  }catch(PDOException $e)
  {
    return false;
  } 
  }

  public static function verificar_vehiculo_qr($id_vehiculo)
  {
    try{
       $codigo=base64_decode($id_vehiculo);
      $dato_vehiculo=str_replace("taxivalle", "", $codigo);

    $consulta="SELECT placa from vehiculo where placa=?";
    $comando=parent::getInstance()->getDb()->prepare($consulta);
    $comando->execute(array($dato_vehiculo));
    $row=$comando->fetch(PDO::FETCH_ASSOC);
    if($row['placa']==$dato_vehiculo)
        return true;
      else
        return false;
  }catch(PDOException $e)
  {
    return false;
  } 
  }


/*Pre registro*/



public static function guardar_conductor_pre_registro(
    $ci,
    $nombre,
    $paterno,
    $materno,
    $expedido,
    $categoria,
    $direccion,
    $celular,
    $genero,
    $correo,
    $estado,
    $token
  )
   {

    if($estado=="1"){

        try{
          $consulta="UPDATE conductor set usuario=?,contrasenia=?,nombre=?,paterno=?,materno=?,sexo=?,celular=?,correo=?,direccion=?,categoria_licencia=?,expedido=?,estado_asignacion=6,token=?,pre_registro=1 where ci= ? ";
          $comando=parent::getInstance()->getDb()->prepare($consulta);
          $comando->execute(array($ci,$ci,$nombre,$paterno,$materno,$genero,$celular,$correo,$direccion,$categoria,$expedido,$token,$ci));
          return  $ci;
      }catch(PDOException $e)
      {
        echo $e;
         return  $ci;
      }
    }else{

       try{
      $consulta="INSERT INTO conductor (ci,usuario,contrasenia,expedido,nombre,paterno,materno,sexo,celular,correo,direccion,categoria_licencia,tipo,id_empresa,estado_asignacion,token,pre_registro) values(?,?,?,?,?,?,?,?,?,?,?,?,?,?,6,?,?)";
      $comando=parent::getInstance()->getDb()->prepare($consulta);
      $comando->execute(array($ci,$ci,$ci,$expedido,$nombre,$paterno,$materno,$genero,$celular,$correo,$direccion,$categoria,'ASALARIADO','6',$token,'1'));
     
      return $ci;
      } catch (PDOException $e) {
        echo $e;
       return "-1";
      } 

    }
   }

   public static function guardar_vehiculo_pre_registro(
    $ci,
    $placa,
    $marca,
    $tipo,
    $clase,
    $modelo,
    $color,
    $ci_pro,
    $nombre_pro,
    $paterno_pro,
    $materno_pro,
    $expedido_pro,
    $radicatoria,
    $movil,
    $moto,
    $estado
  )
   {

 
    //inicio de registro del propietario si no existe aun en la base de datos
    $sw_pro=self::verificar_ci_duenio($ci_pro);

    if($sw_pro==true)
    {
        try{
          $consulta="UPDATE duenio set nombre=?,paterno=?,materno=?,expedido=? where ci= ? ";
          $comando=parent::getInstance()->getDb()->prepare($consulta);
          $comando->execute(array($nombre_pro,$paterno_pro,$materno_pro,$expedido_pro,$ci_pro));
        }catch(PDOException $e)
        {    
          echo $e;
        }
    }else
    {
      try{
          $consulta="INSERT INTO duenio (ci,nombre,paterno,materno,expedido,titulo) values(?,?,?,?,?,?) ";
          $comando=parent::getInstance()->getDb()->prepare($consulta);
          $comando->execute(array($ci_pro,$nombre_pro,$paterno_pro,$materno_pro,$expedido_pro,'PROPIETARIO DEL VEHICULO'));
        }catch(PDOException $e)
        {  
          echo $e;
          }
    }


    //registro del vehiculo

    if($estado=="1"){

        try{
          $consulta="UPDATE vehiculo set marca=?,tipo=?,clase=?,modelo=?, color=?,id_propietario=?,radicatoria=?,movil=?,moto=? where placa= ? ";
          $comando=parent::getInstance()->getDb()->prepare($consulta);
          $comando->execute(array($marca,$tipo,$clase,$modelo,$color,$ci_pro,$radicatoria,$movil,$moto,$placa));

          $consulta="UPDATE conductor set id_vehiculo=? where ci= ? ";
          $comando=parent::getInstance()->getDb()->prepare($consulta);
          $comando->execute(array( $placa,$ci));

          return  $placa;
      }catch(PDOException $e)
      {
        
         return  $placa;
      }
    }else{

       try{
      $consulta="INSERT INTO vehiculo (placa,marca,tipo,clase,modelo,color,id_propietario,radicatoria,estado,movil,id_empresa,moto) values(?,?,?,?,?,?,?,?,?,?,?,?)";
      $comando=parent::getInstance()->getDb()->prepare($consulta);
      $comando->execute(array($placa,$marca,$tipo,$clase,$modelo,$color,$ci_pro,$radicatoria,'7',$movil,'6',$moto));

      $consulta="UPDATE conductor set id_vehiculo=? where ci= ? ";
          $comando=parent::getInstance()->getDb()->prepare($consulta);
          $comando->execute(array( $placa,$ci));


     
      return $placa;
      } catch (PDOException $e) {
         
       return "-1";
      } 

    }
   }

   

  public static function verificar_ci_conductor($id_conductor)
  {
    try{
 
    $consulta="SELECT * from conductor where ci=? ";
    $comando=parent::getInstance()->getDb()->prepare($consulta);
    $comando->execute(array($id_conductor));
    $row=$comando->fetch(PDO::FETCH_ASSOC);
    if($row['ci']==$id_conductor)
        return $row;
      else
        return "-1";
  }catch(PDOException $e)
  {
    return "-1";
  } 
  }



  public static function verificar_carnet_ci($id_conductor,$id_foto)
  {
      $resultado="";
    try{
    $consulta="SELECT  fo.direccion from foto fo,fotocopia f, conductor c WHERE fo.id_fotocopia=f.id and f.id=c.id_fotocopia_ci and fo.id=? and c.ci=?  ";
    $comando=parent::getInstance()->getDb()->prepare($consulta);
    $comando->execute(array($id_foto,$id_conductor));
      $row=$comando->fetch(PDO::FETCH_ASSOC);

      if($row)
      {
        $resultado=$row['direccion'];
      }
      
    } catch (PDOException $e) {
 
    }

    return $resultado;
  }




  public static function verificar_licencia_ci($id_conductor,$id_foto)
  {
      $resultado="";
    try{
    $consulta="SELECT  fo.direccion from foto fo,fotocopia f, conductor c WHERE fo.id_fotocopia=f.id and f.id=c.id_fotocopia_licencia and fo.id=? and c.ci=?  ";
    $comando=parent::getInstance()->getDb()->prepare($consulta);
    $comando->execute(array($id_foto,$id_conductor));
      $row=$comando->fetch(PDO::FETCH_ASSOC);

      if($row)
      {
        $resultado=$row['direccion'];
      }
      
    } catch (PDOException $e) {
 
    }

    return $resultado;
  }

  public static function verificar_inspeccion_tecnica_placa($placa,$id_foto)
  {
    $resultado="";
    try{
    $consulta="SELECT  fo.direccion from foto fo,fotocopia f, vehiculo v WHERE fo.id_fotocopia=f.id and f.id=v.id_foto_inspeccion_tecnica and fo.id=? and v.placa= ?  ";
    $comando=parent::getInstance()->getDb()->prepare($consulta);
    $comando->execute(array($id_foto,$placa));
      $row=$comando->fetch(PDO::FETCH_ASSOC);

      if($row)
      {
        $resultado=$row['direccion'];
      }
      
    } catch (PDOException $e) {
 
    }

    return $resultado;
  }





   public static function verificar_soat_placa($placa,$id_foto)
  {
     $resultado="";
    try{
    $consulta="SELECT  fo.direccion from foto fo,fotocopia f, vehiculo v WHERE fo.id_fotocopia=f.id and f.id=v.id_fotocopia_soat and fo.id=? and v.placa= ?  ";
    $comando=parent::getInstance()->getDb()->prepare($consulta);
    $comando->execute(array($id_foto,$placa));
      $row=$comando->fetch(PDO::FETCH_ASSOC);

      if($row)
      {
        $resultado=$row['direccion'];
      }
      
    } catch (PDOException $e) {
 
    }

    return $resultado;
  }


   public static function verificar_ruat_placa($placa,$id_foto)
  {
      $resultado="";
    try{
    $consulta="SELECT  fo.direccion from foto fo,fotocopia f, vehiculo v WHERE fo.id_fotocopia=f.id and f.id=v.id_fotocopia_rua and fo.id=? and v.placa= ?  ";
    $comando=parent::getInstance()->getDb()->prepare($consulta);
    $comando->execute(array($id_foto,$placa));
      $row=$comando->fetch(PDO::FETCH_ASSOC);

      if($row)
      {
        $resultado=$row['direccion'];
      }
      
    } catch (PDOException $e) {
 
    }

    return $resultado;
  }






    public static function verificar_placa_vehiculo($placa)
  {
      try{
        
        

      $consulta="SELECT v.*,d.ci,d.nombre,d.paterno,d.materno,d.expedido from vehiculo v, duenio d  where v.placa=? AND d.ci=v.id_propietario ";
      $comando=parent::getInstance()->getDb()->prepare($consulta);
      $comando->execute(array($placa));
      $row=$comando->fetch(PDO::FETCH_ASSOC);
      if($row['placa']==$placa)
          return $row;
        else
          return "-1";
    }catch(PDOException $e)
    {
      return "-1";
    } 
  }

 public static function verificar_ci_duenio($ci)
  {
      try{

          $consulta="SELECT * from duenio where ci=? ";
          $comando=parent::getInstance()->getDb()->prepare($consulta);
          $comando->execute(array($ci));
          $row=$comando->fetch(PDO::FETCH_ASSOC);
 
          if($row['ci']==$ci)
              return true; 
            else
              return false;
        }catch(PDOException $e)
        {
          
          return false;
        } 
  }

/*Fin de pre registro*/

    public static  function set_estado($estado,$id_conductor)
  {
    try{
      $consulta="UPDATE conductor set estado=? where ci= ? ";
      $comando=parent::getInstance()->getDb()->prepare($consulta);
      $comando->execute(array($estado,$id_conductor));
      return true;
    }catch(PDOException $e)
    {
      return false;
    }
  }
   public static  function cerrar_sesion($id_conductor,$placa)
  {
    try{
      $consulta="UPDATE conductor set estado=0,login=0 where ci= ? ";
      $comando=parent::getInstance()->getDb()->prepare($consulta);
      $comando->execute(array($id_conductor));
      return true;
    }catch(PDOException $e)
    {
      return false;
    }
  }
    public static function set_ubicacion_punto($latitud,$longitud,$id_conductor,$placa,$rotacion)
  {
    try{
    $consulta="UPDATE conductor set latitud_asignacion= ? , longitud_asignacion= ?,rotacion=?,fecha_ultimo=now()  where ci= ?";
    $comando=parent::getInstance()->getDb()->prepare($consulta);
    $comando->execute(array($latitud,$longitud,$rotacion,$id_conductor));
    return true;
    }catch(PDOException $e)
    {
      return false;
    }

  }

      public static function set_ubicacion_punto_carrera($latitud,$longitud,$id_conductor,$placa,$id_carrera,$numero,$id_pedido,$distancia,$rotacion)
  {
    if(self::existe_ruta_por_id_carrera_numero($id_pedido,$id_carrera,$numero)==false){
     
    try{
    $consulta="UPDATE conductor a set a.latitud_asignacion= ? , a.longitud_asignacion= ?,a.rotacion=?,a.fecha_ultimo=now()  where a.ci= ? ";
    $comando=parent::getInstance()->getDb()->prepare($consulta);
    $comando->execute(array($latitud,$longitud,$rotacion,$id_conductor));
    $sw=self::set_puntos($latitud,$longitud,$id_pedido,$id_carrera,$numero,$distancia,$rotacion);
    return true;
    }catch(PDOException $e)
    {
      return false;
    }
    }else 
    {
      return false;
    }

  }

   public static function set_puntos($latitud,$longitud,$id_pedido,$id_carrera,$numero,$distancia,$rotacion)
  {
  try{
    $consulta="INSERT ruta (latitud,longitud,id_pedido,id_carrera,numero,distancia,rotacion)values(?,?,?,?,?,?,?)";
    $comando=parent::getInstance()->getDb()->prepare($consulta);
    $comando->execute(array($latitud,$longitud,$id_pedido,$id_carrera,$numero,$distancia,$rotacion));
    
    return true;
    }catch(PDOException $e)
    {
      return false;
    }
  }


  public static   function obtener_ubicacion_por_id_pedido($id_pedido)
  {
    $consulta="SELECT c.latitud_asignacion as 'latitud', c.longitud_asignacion as 'longitud',p.id as 'id_pedido',c.rotacion ,p.estado,p.clase_vehiculo from pedido p,conductor c where p.id_conductor=c.ci and p.id=?";
    $resultado="-1";
    try{
       $comando=parent::getInstance()->getDb()->prepare($consulta);
      $comando->execute(array($id_pedido));
      $row=$comando->fetch(PDO::FETCH_ASSOC);
      $resultado=$row;
    }catch(PDOException $e)
    {
      $resultado="-1";
    }
    return $resultado;
  }

 public static function get_imagen($ci)
  {

    try{
      $consulta="SELECT direccion_imagen from conductor where ci=".$ci;
      $comando=parent::getInstance()->getDb()->prepare($consulta);
      $comando->execute();
      $row=$comando->fetch(PDO::FETCH_ASSOC);
  
 
      $src_imagen_perfil="";
       if(file_exists("../taxivalle/public/".$row['direccion_imagen']))
      {
        $src_imagen_perfil=file_get_contents("../taxivalle/public/".$row['direccion_imagen']);
        return $src_imagen_perfil;
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

public static function get_logo_empresa($id_empresa)
  {

    try{
      $consulta="SELECT fo.direccion from empresa e,fotocopia f,foto fo  where  fo.id_fotocopia=f.id and e.id_imagen_corporativa=f.id and e.id=".$id_empresa;
      $comando=parent::getInstance()->getDb()->prepare($consulta);
      $comando->execute();
      $row=$comando->fetch(PDO::FETCH_ASSOC);
  
      $src_imagen_perfil="";
       if(file_exists("../taxivalle/storage".$row['direccion']))
      {
        $src_imagen_perfil=file_get_contents("../taxivalle/storage".$row['direccion']);
        return $src_imagen_perfil;
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

public static function get_imagen_usuario($id_usuario)
  {
    try{
      $consulta="SELECT direccion_imagen from usuario where id=".$id_usuario;
      $comando=parent::getInstance()->getDb()->prepare($consulta);
      $comando->execute();
      $row=$comando->fetch(PDO::FETCH_ASSOC);
  
      $src_imagen_perfil="";
      if($row){
           if(file_exists("./".$row['direccion_imagen']))
          {
            $src_imagen_perfil=file_get_contents("./".$row['direccion_imagen']);
            return $src_imagen_perfil;
          }
          else
          {
            return -1;
          }
        }else{

           return -1;
          
        }
    }catch(PDOException $e)
    {
      return -1;
    }
  }

    public static  function actualizar_contrasenia($ci,$antigua,$nueva)
   {
    try{
    $consulta="UPDATE conductor SET contrasenia=? where ci=? and contrasenia=?";
    $comando=parent::getInstance()->getDb()->prepare($consulta);
    $comando->execute(array($nueva,$ci,$antigua));
     return true;
    } catch (PDOException $e) {
     return false;
    }
   }
   public static function get_estado_contrasenia($ci,$contrasenia)
  {
    try{
      $consulta="SELECT ci from conductor where ci=? and contrasenia=?";
      $comando=parent::getInstance()->getDb()->prepare($consulta);
      $comando->execute(array($ci,$contrasenia));
      $row=$comando->fetch(PDO::FETCH_ASSOC);
      if($row)
      {
        return true;
      }else
      {
        return false;
      }    
    }catch(PDOException $e)
    {
      return false;
    }
  }

  public static function pedido_en_curso_por_id($id_pedido)
  {
    $consulta="SELECT p.*, concat(nombre,' ',apellido)as 'nombre_usuario',u.celular from pedido p,usuario u where p.id_usuario=u.id and p.tipo_reserva=0 and p.id=?";
      try{
        $comando=parent::getInstance()->getDb()->prepare($consulta);
        $comando->execute(array($id_pedido));
        $row=$comando->fetch(PDO::FETCH_ASSOC);
        if($row)
          return $row;
         else
          return -1;
      }catch(PDOException $e)
      {
           return -1;
      }
  }

  public static function get_estado($ci)
  {
    try{
      $consulta="SELECT estado  from conductor where ci=? limit 1";
      $comando=parent::getInstance()->getDb()->prepare($consulta);
      $comando->execute(array($ci));
      $row=$comando->fetch(PDO::FETCH_ASSOC);
      if($row)
      {
        return $row['estado'];
      }else
      {
        return -1;
      }    
    }catch(PDOException $e)
    {
       
      return -1;
    }
  }

  public static function get_placa($ci)
  {
    try{
      $consulta="SELECT id_vehiculo from conductor where ci=? limit 1";
      $comando=parent::getInstance()->getDb()->prepare($consulta);
      $comando->execute(array($ci));
      $row=$comando->fetch(PDO::FETCH_ASSOC);
      if($row)
      {
        return $row['id_vehiculo'];
      }else
      {
        return -1;
      }    
    }catch(PDOException $e)
    {
      return -1;
    }
  }

   public static function get_panico_bloqueo_por_ci($ci)
  {
    try{
      $consulta="SELECT panico,bloqueo from conductor where ci=? limit 1";
      $comando=parent::getInstance()->getDb()->prepare($consulta);
      $comando->execute(array($ci));
      $row=$comando->fetch(PDO::FETCH_ASSOC);
      if($row)
      {
        return $row;
      }else
      {
        return -1;
      }    
    }catch(PDOException $e)
    {
      return -1;
    }
  }

  public static function get_estado_asignacion_por_ci($ci)
  {
    try{
      $consulta="SELECT estado_asignacion as 'estado' from conductor  where ci=? limit 1";
      $comando=parent::getInstance()->getDb()->prepare($consulta);
      $comando->execute(array($ci));
      $row=$comando->fetch(PDO::FETCH_ASSOC);
      if($row)
      {
        return $row['estado'];
      }else
      {
        return -1;
      }    
    }catch(PDOException $e)
    {
      return -1;
    }
  }

    public static function get_estado_asignacion_por_placa($placa)
  {
    try{
      $consulta="SELECT estado_asignacion as 'estado' from conductor  where id_vehiculo=?  limit 1";
      $comando=parent::getInstance()->getDb()->prepare($consulta);
      $comando->execute(array($placa));
      $row=$comando->fetch(PDO::FETCH_ASSOC);
      if($row)
      {
        return $row['estado'];
      }else
      {
        return -1;
      }    
    }catch(PDOException $e)
    {
      return -1;
    }
  }

  public static function get_estado_placa($placa)
  {
    try{
      $consulta="SELECT estado from vehiculo where placa=? limit 1";
      $comando=parent::getInstance()->getDb()->prepare($consulta);
      $comando->execute(array($placa));
      $row=$comando->fetch(PDO::FETCH_ASSOC);
      if($row)
      {
        return $row['estado'];
      }else
      {
        return -1;
      }    
    }catch(PDOException $e)
    {
      return -1;
    }
  }


  public static function insertar_imagen_perfil($ci,$imagen)
  {

      $nuevo_nombre="Imagen_PERFIL-".$ci.".jpg";
        $rutadelaimagen="Imagen_Conductor/".$nuevo_nombre;
        $direccion_imagen_png="../taxivalle2/storage/".$rutadelaimagen;
    
      $resultado=false;
      if (is_uploaded_file($imagen)) 
      { 
          if(move_uploaded_file($imagen,$direccion_imagen_png)){
               try{
                  $consulta="UPDATE conductor set direccion_imagen=? where ci=?";
                  $comando=parent::getInstance()->getDb()->prepare($consulta);
                  $comando->execute(array($rutadelaimagen,$ci));
                    $resultado=true;
                  } catch (PDOException $e) {
                  }
         }

      } 
      return $resultado;
  }



public static function insertar_imagen_rua($placa,$imagen)
  {
    $cantidad=0;
    $id_fotocopia=0;
    $numero="";

     try{
    $consulta="SELECT nro_rua from vehiculo v WHERE  v.placa=? ";
    $comando=parent::getInstance()->getDb()->prepare($consulta);
    $comando->execute(array($placa));
    $row=$comando->fetch(PDO::FETCH_ASSOC);
      
            if($row)
            {
              $numero=$row["nro_rua"];
            }      
    } catch (PDOException $e) {
    }
    
    try{
    $consulta="SELECT max(fo.id) as 'cantidad',fo.id_fotocopia from foto fo,fotocopia f, vehiculo v WHERE fo.id_fotocopia=f.id and f.id=v.id_fotocopia_rua and v.placa=? ";
    $comando=parent::getInstance()->getDb()->prepare($consulta);
    $comando->execute(array($placa));
      $row=$comando->fetch(PDO::FETCH_ASSOC);
      
            if($row)
            {
              $cantidad=$row["cantidad"];
              $id_fotocopia=$row["id_fotocopia"];
            }      
    } catch (PDOException $e) {
        $cantidad=0;
            $id_fotocopia=0;
    }
         $cantidad+=1;

        $nuevo_nombre="Imagen_RUA-".$placa."-".$cantidad.".png";
        $rutadelaimagen="Imagen_Vehiculo/".$nuevo_nombre;

    $direccion_imagen_png="../taxivalle/storage/".$rutadelaimagen;
    
          //MOVER LA IMAGEN AL WEBSERVI
  move_uploaded_file($imagen,$direccion_imagen_png);
    
  try{

                   if($cantidad==1){
                    try{
                    $consulta="INSERT into fotocopia (nombre,numero) values('RUA',?) ";
            $comando=parent::getInstance()->getDb()->prepare($consulta);
            $comando->execute(array($numero));
            $id_fotocopia = parent::getInstance()->getDb()->lastInsertId();
            }catch(Exception $e){
            }
                    }
                    else{
                      try{
                      $consulta="UPDATE fotocopia set numero=? where id=?";
            $comando=parent::getInstance()->getDb()->prepare($consulta);
            $comando->execute(array($numero,$id_fotocopia));
             }catch(Exception $e){
            }
                    }
                    try{
                        $consulta="INSERT into foto (id_fotocopia,id,direccion) values(?,?,?) ";
            $comando=parent::getInstance()->getDb()->prepare($consulta);
            $comando->execute(array($id_fotocopia,$cantidad,$rutadelaimagen));
             }catch(Exception $e){
            }
            try{
            $consulta="UPDATE vehiculo set id_fotocopia_rua=? where placa=?";
            $comando=parent::getInstance()->getDb()->prepare($consulta);
            $comando->execute(array($id_fotocopia,$placa));
             }catch(Exception $e){
            }
      return array($cantidad, $id_fotocopia);
    } catch (PDOException $e) {
      return false;
    }
  } 

public static function insertar_imagen_ci_rua($placa,$imagen)
  {
    $cantidad=0;
    $id_fotocopia=0;
    $numero="";

     try{
    $consulta="SELECT nro_documento_propiedad from vehiculo v WHERE  v.placa=? ";
    $comando=parent::getInstance()->getDb()->prepare($consulta);
    $comando->execute(array($placa));
    $row=$comando->fetch(PDO::FETCH_ASSOC);
      
            if($row)
            {
              $numero=$row["nro_documento_propiedad"];
            }      
    } catch (PDOException $e) {
    }
    
    try{
    $consulta="SELECT max(fo.id) as 'cantidad',fo.id_fotocopia from foto fo,fotocopia f, vehiculo v WHERE fo.id_fotocopia=f.id and f.id=v.id_fotocopia_ci_titular_del_rua and v.placa=? ";
    $comando=parent::getInstance()->getDb()->prepare($consulta);
    $comando->execute(array($placa));
      $row=$comando->fetch(PDO::FETCH_ASSOC);
      
            if($row)
            {
              $cantidad=$row["cantidad"];
              $id_fotocopia=$row["id_fotocopia"];
            }      
    } catch (PDOException $e) {
        $cantidad=0;
            $id_fotocopia=0;
    }
         $cantidad+=1;

        $nuevo_nombre="Imagen_CI-RUA-".$placa."-".$cantidad.".png";
        $rutadelaimagen="Imagen_Vehiculo/".$nuevo_nombre;

    $direccion_imagen_png="../taxivalle/storage/".$rutadelaimagen;
    
          //MOVER LA IMAGEN AL WEBSERVI
  move_uploaded_file($imagen,$direccion_imagen_png);

  try{

                   if($cantidad==1){
                    try{
                    $consulta="INSERT into fotocopia (nombre,numero) values('CI TITULAR DEL RUA',?) ";
            $comando=parent::getInstance()->getDb()->prepare($consulta);
            $comando->execute(array($numero));
            $id_fotocopia = parent::getInstance()->getDb()->lastInsertId();
            }catch(Exception $e){
            }
                    }
                    else{
                      try{
                      $consulta="UPDATE fotocopia set numero=? where id=?";
            $comando=parent::getInstance()->getDb()->prepare($consulta);
            $comando->execute(array($numero,$id_fotocopia));
             }catch(Exception $e){
            }
                    }
                    try{
                        $consulta="INSERT into foto (id_fotocopia,id,direccion) values(?,?,?) ";
            $comando=parent::getInstance()->getDb()->prepare($consulta);
            $comando->execute(array($id_fotocopia,$cantidad,$rutadelaimagen));
             }catch(Exception $e){
            }
            try{
            $consulta="UPDATE vehiculo set id_fotocopia_ci_titular_del_rua=? where placa=?";
            $comando=parent::getInstance()->getDb()->prepare($consulta);
            $comando->execute(array($id_fotocopia,$placa));
             }catch(Exception $e){
            }
      return array($cantidad, $id_fotocopia);
    } catch (PDOException $e) {
      return false;
    }
  } 


public static function insertar_imagen_ruat($placa,$imagen)
  {
    $cantidad=0;
    $id_fotocopia=0;
    $numero="";
    $resultado=false;
 
     try{
    $consulta="SELECT nro_documento_propiedad from vehiculo v WHERE  v.placa=? ";
    $comando=parent::getInstance()->getDb()->prepare($consulta);
    $comando->execute(array($placa));
    $row=$comando->fetch(PDO::FETCH_ASSOC);
      
            if($row)
            {
              $numero=$row["nro_documento_propiedad"];
            }      
    } catch (PDOException $e) {
    }
    
    try{
    $consulta="SELECT max(fo.id) as 'cantidad',fo.id_fotocopia from foto fo,fotocopia f, vehiculo v WHERE fo.id_fotocopia=f.id and f.id=v.id_fotocopia_rua and v.placa=? ";
    $comando=parent::getInstance()->getDb()->prepare($consulta);
    $comando->execute(array($placa));
      $row=$comando->fetch(PDO::FETCH_ASSOC);
      
            if($row)
            {
              $cantidad=$row["cantidad"];
              $id_fotocopia=$row["id_fotocopia"];
            }      
    } catch (PDOException $e) {
        $cantidad=0;
            $id_fotocopia=0;
    }
         $cantidad+=1;

        $nuevo_nombre="Imagen_RUA-".$placa."-".$cantidad.".png";
        $rutadelaimagen="Imagen_Vehiculo/".$nuevo_nombre;

    $direccion_imagen_png="../taxivalle/storage/".$rutadelaimagen;
    
          //MOVER LA IMAGEN AL WEBSERVI
  move_uploaded_file($imagen,$direccion_imagen_png);

  try{

                   if($cantidad==1){
                    try{
                    $consulta="INSERT into fotocopia (nombre,numero) values('RUA',?) ";
            $comando=parent::getInstance()->getDb()->prepare($consulta);
            $comando->execute(array($numero));
            $id_fotocopia = parent::getInstance()->getDb()->lastInsertId();
            }catch(Exception $e){
            }
                    }
                    else{
                      try{
                      $consulta="UPDATE fotocopia set numero=? where id=?";
            $comando=parent::getInstance()->getDb()->prepare($consulta);
            $comando->execute(array($numero,$id_fotocopia));
             }catch(Exception $e){
            }
                    }
                    try{
                        $consulta="INSERT into foto (id_fotocopia,id,direccion) values(?,?,?) ";
                        $comando=parent::getInstance()->getDb()->prepare($consulta);
                        $comando->execute(array($id_fotocopia,1,$rutadelaimagen));
                        $resultado=true;
                   }catch(Exception $e){
                     $consulta="UPDATE foto set direccion=? where id=? and id_fotocopia=?";
                     $comando=parent::getInstance()->getDb()->prepare($consulta);
                     $comando->execute(array($rutadelaimagen,1,$id_fotocopia));
                     $resultado=true;
                  }
            try{
            $consulta="UPDATE vehiculo set id_fotocopia_rua=? where placa=?";
            $comando=parent::getInstance()->getDb()->prepare($consulta);
            $comando->execute(array($id_fotocopia,$placa));
             }catch(Exception $e){
            }
      return array($cantidad, $id_fotocopia);
    } catch (PDOException $e) {
      $resultado=false;
    }
    return $resultado;
  } 


public static function insertar_imagen_doc_propiedad($placa,$imagen)
  {
    $cantidad=0;
    $id_fotocopia=0;
    $numero="";

     try{
    $consulta="SELECT nro_documento_propiedad from vehiculo v WHERE  v.placa=? ";
    $comando=parent::getInstance()->getDb()->prepare($consulta);
    $comando->execute(array($placa));
    $row=$comando->fetch(PDO::FETCH_ASSOC);
      
            if($row)
            {
              $numero=$row["nro_documento_propiedad"];
            }      
    } catch (PDOException $e) {
    }
    
    try{
    $consulta="SELECT max(fo.id) as 'cantidad',fo.id_fotocopia from foto fo,fotocopia f, vehiculo v WHERE fo.id_fotocopia=f.id and f.id=v.id_fotocopia_documento_de_propiedad and v.placa=? ";
    $comando=parent::getInstance()->getDb()->prepare($consulta);
    $comando->execute(array($placa));
      $row=$comando->fetch(PDO::FETCH_ASSOC);
      
            if($row)
            {
              $cantidad=$row["cantidad"];
              $id_fotocopia=$row["id_fotocopia"];
            }      
    } catch (PDOException $e) {
        $cantidad=0;
            $id_fotocopia=0;
    }
         $cantidad+=1;

        $nuevo_nombre="Imagen_DOCUMENTO-PROPIEDAD-".$placa."-".$cantidad.".png";
        $rutadelaimagen="Imagen_Vehiculo/".$nuevo_nombre;

    $direccion_imagen_png="../taxivalle/storage/".$rutadelaimagen;
    
         //MOVER LA IMAGEN AL WEBSERVI
  move_uploaded_file($imagen,$direccion_imagen_png);

  try{

                   if($cantidad==1){
                    try{
                    $consulta="INSERT into fotocopia (nombre,numero) values('DOCUMENTO DE PROPIEDAD',?) ";
            $comando=parent::getInstance()->getDb()->prepare($consulta);
            $comando->execute(array($numero));
            $id_fotocopia = parent::getInstance()->getDb()->lastInsertId();
            }catch(Exception $e){
            }
                    }
                    else{
                      try{
                      $consulta="UPDATE fotocopia set numero=? where id=?";
            $comando=parent::getInstance()->getDb()->prepare($consulta);
            $comando->execute(array($numero,$id_fotocopia));
             }catch(Exception $e){
            }
                    }
                    try{
                        $consulta="INSERT into foto (id_fotocopia,id,direccion) values(?,?,?) ";
            $comando=parent::getInstance()->getDb()->prepare($consulta);
            $comando->execute(array($id_fotocopia,$cantidad,$rutadelaimagen));
             }catch(Exception $e){
            }
            try{
            $consulta="UPDATE vehiculo set id_fotocopia_documento_de_propiedad=? where placa=?";
            $comando=parent::getInstance()->getDb()->prepare($consulta);
            $comando->execute(array($id_fotocopia,$placa));
             }catch(Exception $e){
            }
      return array($cantidad, $id_fotocopia);
    } catch (PDOException $e) {
      return false;
    }
  } 

  public static function insertar_imagen_soat($placa,$imagen)
  {
    $cantidad=0;
    $id_fotocopia=0;
    $numero="";

     try{
    $consulta="SELECT nro_soat from vehiculo v WHERE  v.placa=? ";
    $comando=parent::getInstance()->getDb()->prepare($consulta);
    $comando->execute(array($placa));
    $row=$comando->fetch(PDO::FETCH_ASSOC);
      
            if($row)
            {
              $numero=$row["nro_soat"];
            }      
    } catch (PDOException $e) {
    }
    
    try{
    $consulta="SELECT max(fo.id) as 'cantidad',fo.id_fotocopia from foto fo,fotocopia f, vehiculo v WHERE fo.id_fotocopia=f.id and f.id=v.id_fotocopia_soat and v.placa=? ";
    $comando=parent::getInstance()->getDb()->prepare($consulta);
    $comando->execute(array($placa));
      $row=$comando->fetch(PDO::FETCH_ASSOC);
      
            if($row)
            {
              $cantidad=$row["cantidad"];
              $id_fotocopia=$row["id_fotocopia"];
            }      
    } catch (PDOException $e) {
        $cantidad=0;
            $id_fotocopia=0;
    }
         $cantidad+=1;

        $nuevo_nombre="Imagen_SOAT-".$placa."-".$cantidad.".png";
        $rutadelaimagen="Imagen_Vehiculo/".$nuevo_nombre;

    $direccion_imagen_png="../taxivalle/storage/".$rutadelaimagen;
    
          //MOVER LA IMAGEN AL WEBSERVI
  move_uploaded_file($imagen,$direccion_imagen_png);
    
  try{

                   if($cantidad==1){
                    try{
                    $consulta="INSERT into fotocopia (nombre,numero) values('SOAT',?) ";
            $comando=parent::getInstance()->getDb()->prepare($consulta);
            $comando->execute(array($numero));
            $id_fotocopia = parent::getInstance()->getDb()->lastInsertId();
            }catch(Exception $e){
            }
                    }
                    else{
                      try{
                      $consulta="UPDATE fotocopia set numero=? where id=?";
            $comando=parent::getInstance()->getDb()->prepare($consulta);
            $comando->execute(array($numero,$id_fotocopia));
             }catch(Exception $e){
            }
                    }
                    try{
                        $consulta="INSERT into foto (id_fotocopia,id,direccion) values(?,?,?) ";
            $comando=parent::getInstance()->getDb()->prepare($consulta);
            $comando->execute(array($id_fotocopia,$cantidad,$rutadelaimagen));
             }catch(Exception $e){
            }
            try{
            $consulta="UPDATE vehiculo set id_fotocopia_soat=? where placa=?";
            $comando=parent::getInstance()->getDb()->prepare($consulta);
            $comando->execute(array($id_fotocopia,$placa));
             }catch(Exception $e){
            }
      return array($cantidad, $id_fotocopia);
    } catch (PDOException $e) {
      return false;
    }
  }

   public static function insertar_imagen_inspeccion_tecnica($placa,$imagen)
  {
    $cantidad=0;
    $id_fotocopia=0;
    $numero="";

     try{
    $consulta="SELECT nro_inspeccion_tecnica from vehiculo v WHERE  v.placa=? ";
    $comando=parent::getInstance()->getDb()->prepare($consulta);
    $comando->execute(array($placa));
    $row=$comando->fetch(PDO::FETCH_ASSOC);
      
            if($row)
            {
              $numero=$row["nro_inspeccion_tecnica"];
            }      
    } catch (PDOException $e) {
    }
    
    try{
    $consulta="SELECT max(fo.id) as 'cantidad',fo.id_fotocopia from foto fo,fotocopia f, vehiculo v WHERE fo.id_fotocopia=f.id and f.id=v.id_foto_inspeccion_tecnica and v.placa=? ";
    $comando=parent::getInstance()->getDb()->prepare($consulta);
    $comando->execute(array($placa));
      $row=$comando->fetch(PDO::FETCH_ASSOC);
      
            if($row)
            {
              $cantidad=$row["cantidad"];
              $id_fotocopia=$row["id_fotocopia"];
            }      
    } catch (PDOException $e) {
        $cantidad=0;
            $id_fotocopia=0;
    }
         $cantidad+=1;

        $nuevo_nombre="Imagen_INSPECCION-TECNICA-".$placa."-".$cantidad.".png";
        $rutadelaimagen="Imagen_Vehiculo/".$nuevo_nombre;

    $direccion_imagen_png="../taxivalle/storage/".$rutadelaimagen;
    
        $resultado=false;
      
      //MOVER LA IMAGEN AL WEBSERVI
  move_uploaded_file($imagen,$direccion_imagen_png);
               
        
    
  try{

                   if($cantidad==1){
                    try{
                    $consulta="INSERT into fotocopia (nombre,numero) values('INSPECCION TECNICA',?) ";
            $comando=parent::getInstance()->getDb()->prepare($consulta);
            $comando->execute(array($numero));
            $id_fotocopia = parent::getInstance()->getDb()->lastInsertId();
            }catch(Exception $e){
            }
                    }
                    else{
                      try{
                      $consulta="UPDATE fotocopia set numero=? where id=?";
            $comando=parent::getInstance()->getDb()->prepare($consulta);
            $comando->execute(array($numero,$id_fotocopia));
             }catch(Exception $e){
            }
                    }
                    try{
                        $consulta="INSERT into foto (id_fotocopia,id,direccion) values(?,?,?) ";
            $comando=parent::getInstance()->getDb()->prepare($consulta);
            $comando->execute(array($id_fotocopia,$cantidad,$rutadelaimagen));
             }catch(Exception $e){
            }
            try{
            $consulta="UPDATE vehiculo set id_foto_inspeccion_tecnica=? where placa=?";
            $comando=parent::getInstance()->getDb()->prepare($consulta);
            $comando->execute(array($id_fotocopia,$placa));
             }catch(Exception $e){
            }
      return array($cantidad, $id_fotocopia);
    } catch (PDOException $e) {
      return false;
    }
  }  

///insertar imagenes del vehiculo


  public static function insertar_imagen_perfil_conductor($placa,$imagen)
  {

        $nuevo_nombre="PERFIL-".$placa.".png";
        $rutadelaimagen="Imagen_Conductor/".$nuevo_nombre;
        $direccion_imagen_png="../taxivalle/public/".$rutadelaimagen;
    
      $resultado=false;
      if (is_uploaded_file($imagen)) 
      { 
          if(move_uploaded_file($imagen,$direccion_imagen_png)) {
               try{
                  $consulta="UPDATE conductor set direccion_imagen=? where ci=?";
                  $comando=parent::getInstance()->getDb()->prepare($consulta);
                  $comando->execute(array($rutadelaimagen,$placa));
                    $resultado=true;
                  } catch (PDOException $e) {
                    echo $e;
                  }
         }

      } 
      return $resultado;
  }
  public static function insertar_imagen_delante($placa,$imagen)
  {

        $nuevo_nombre="FotoVehiculoAdelante-".$placa.".png";
        $rutadelaimagen="Imagen_Vehiculo/".$nuevo_nombre;
        $direccion_imagen_png="../taxivalle/storage/".$rutadelaimagen;
    
      $resultado=false;
      if (is_uploaded_file($imagen)) 
      { 
          if(move_uploaded_file($imagen,$direccion_imagen_png)){
               try{
                  $consulta="UPDATE vehiculo set direccion_imagen_adelante=? where placa=?";
                  $comando=parent::getInstance()->getDb()->prepare($consulta);
                  $comando->execute(array($rutadelaimagen,$placa));
                    $resultado=true;
                  } catch (PDOException $e) {
                  }
         }

      } 
      return $resultado;
  }
  public static function insertar_imagen_detras($placa,$imagen)
  {
        $nuevo_nombre="FotoVehiculoAtras-".$placa.".png";
        $rutadelaimagen="Imagen_Vehiculo/".$nuevo_nombre;
        $direccion_imagen_png="../taxivalle/storage/".$rutadelaimagen;

     $resultado=false;
      if (is_uploaded_file($imagen)) 
      { 
          if(move_uploaded_file($imagen,$direccion_imagen_png)){
               try{
                    $consulta="UPDATE vehiculo set direccion_imagen_atras=? where placa=?";
                    $comando=parent::getInstance()->getDb()->prepare($consulta);
                     $comando->execute(array($rutadelaimagen,$placa));
                    $resultado=true;
                  } catch (PDOException $e) {
                  }
         }

      } 
      return $resultado;
  }  
  public static function insertar_imagen_lateral_izquierdo($placa,$imagen)
  {
        $nuevo_nombre="FotoVehiculoInteriorAtras-".$placa.".png";
        $rutadelaimagen="Imagen_Vehiculo/".$nuevo_nombre;
        $direccion_imagen_png="../taxivalle/storage/".$rutadelaimagen;
    
    $resultado=false;
      if (is_uploaded_file($imagen)) 
      { 
          if(move_uploaded_file($imagen,$direccion_imagen_png)){
               try{
                    $consulta="UPDATE vehiculo set direccion_imagen_interior_atras=? where placa=?";
                    $comando=parent::getInstance()->getDb()->prepare($consulta);
                    $comando->execute(array($rutadelaimagen,$placa));
                    $resultado=true;
                  } catch (PDOException $e) {
                  }
         }

      } 
      return $resultado;
  }  
  public static function insertar_imagen_lateral_derecho($placa,$imagen)
  {
        $nuevo_nombre="FotoVehiculoInteriorAdelante-".$placa.".png";
        $rutadelaimagen="Imagen_Vehiculo/".$nuevo_nombre;
        $direccion_imagen_png="../taxivalle/storage/".$rutadelaimagen;
    $resultado=false;
      if (is_uploaded_file($imagen)) 
      { 
          if(move_uploaded_file($imagen,$direccion_imagen_png)){
               try{
                    $consulta="UPDATE vehiculo set direccion_imagen_interior_adelante=? where placa=?";
                    $comando=parent::getInstance()->getDb()->prepare($consulta);
                    $comando->execute(array($rutadelaimagen,$placa));
                    $resultado=true;
                  } catch (PDOException $e) {
                  }
         }

      } 
      return $resultado;
  }  



///INSERTAR IMAGENES DEL CONDUCTOR,.. CI . LICENCIA DE CONDUCIR
  public static function insertar_imagen_cedula($id_taxi,$imagen)
  {
    $cantidad=0;
        $id_fotocopia=0;
    
    try{
    $consulta="SELECT max(fo.id) as 'cantidad',fo.id_fotocopia from foto fo,fotocopia f, conductor c WHERE fo.id_fotocopia=f.id and f.id=c.id_fotocopia_ci and c.ci=? ";
    $comando=parent::getInstance()->getDb()->prepare($consulta);
    $comando->execute(array($id_taxi));
      $row=$comando->fetch(PDO::FETCH_ASSOC);
      
            if($row)
            {
              $cantidad=$row["cantidad"];
              $id_fotocopia=$row["id_fotocopia"];
            }
            
    } catch (PDOException $e) {
        $cantidad=0;
            $id_fotocopia=0;
    }
         $cantidad+=1;

        $nuevo_nombre="Imagen_CI-".$id_taxi."-".$cantidad.".png";
        $rutadelaimagen="Imagen_Conductor/".$nuevo_nombre;

    $direccion_imagen_png="../taxivalle/storage/".$rutadelaimagen;
    
        //MOVER LA IMAGEN AL WEBSERVI
  move_uploaded_file($imagen,$direccion_imagen_png);
    
  try{

                   if($cantidad==1){
                    try{
                    $consulta="INSERT into fotocopia (nombre,numero) values('CI',?) ";
            $comando=parent::getInstance()->getDb()->prepare($consulta);
            $comando->execute(array($id_taxi));
            $id_fotocopia = parent::getInstance()->getDb()->lastInsertId();
            }catch(Exception $e){
            }
                    }
                    else{
                      try{
                      $consulta="UPDATE fotocopia set numero=? where id=?";
            $comando=parent::getInstance()->getDb()->prepare($consulta);
            $comando->execute(array($id_taxi,$id_fotocopia));
             }catch(Exception $e){
            }
                    }
                    try{
                        $consulta="INSERT into foto (id_fotocopia,id,direccion) values(?,?,?) ";
            $comando=parent::getInstance()->getDb()->prepare($consulta);
            $comando->execute(array($id_fotocopia,$cantidad,$rutadelaimagen));
             }catch(Exception $e){
            }
            try{
            $consulta="UPDATE conductor set id_fotocopia_ci=? where ci=?";
            $comando=parent::getInstance()->getDb()->prepare($consulta);
            $comando->execute(array($id_fotocopia,$id_taxi));
             }catch(Exception $e){
            }
      return array($cantidad, $id_fotocopia);
    } catch (PDOException $e) {
      return false;
    }
  } 

   public static function insertar_imagen_licencia($id_taxi,$imagen)
  {
    $cantidad=0;
        $id_fotocopia=0;
    
    try{
    $consulta="SELECT max(fo.id) as 'cantidad',fo.id_fotocopia from foto fo,fotocopia f, conductor c WHERE fo.id_fotocopia=f.id and f.id=c.id_fotocopia_licencia and c.ci=? ";
    $comando=parent::getInstance()->getDb()->prepare($consulta);
    $comando->execute(array($id_taxi));
      $row=$comando->fetch(PDO::FETCH_ASSOC);
      
            if($row)
            {
              $cantidad=$row["cantidad"];
              $id_fotocopia=$row["id_fotocopia"];
            }
            
    } catch (PDOException $e) {
        $cantidad=0;
            $id_fotocopia=0;
    }
         $cantidad+=1;

        $nuevo_nombre="Imagen_LICENCIA-".$id_taxi."-".$cantidad.".png";
        $rutadelaimagen="Imagen_Conductor/".$nuevo_nombre;

    $direccion_imagen_png="../taxivalle/storage/".$rutadelaimagen;
    
          //MOVER LA IMAGEN AL WEBSERVI
  move_uploaded_file($imagen,$direccion_imagen_png);
    
  try{

                   if($cantidad==1){
                    try{
                    $consulta="INSERT into fotocopia (nombre,numero) values('LICENCIA DE CONDUCIR',?) ";
            $comando=parent::getInstance()->getDb()->prepare($consulta);
            $comando->execute(array($id_taxi));
            $id_fotocopia = parent::getInstance()->getDb()->lastInsertId();
            }catch(Exception $e){
            }
                    }
                    else{
                      try{
                      $consulta="UPDATE fotocopia set numero=? where id=?";
            $comando=parent::getInstance()->getDb()->prepare($consulta);
            $comando->execute(array($id_taxi,$id_fotocopia));
             }catch(Exception $e){
            }
                    }
                    try{
                        $consulta="INSERT into foto (id_fotocopia,id,direccion) values(?,?,?) ";
            $comando=parent::getInstance()->getDb()->prepare($consulta);
            $comando->execute(array($id_fotocopia,$cantidad,$rutadelaimagen));
             }catch(Exception $e){
            }
            try{
            $consulta="UPDATE conductor set id_fotocopia_licencia=? where ci=?";
            $comando=parent::getInstance()->getDb()->prepare($consulta);
            $comando->execute(array($id_fotocopia,$id_taxi));
             }catch(Exception $e){
            }
      return array($cantidad, $id_fotocopia);
    } catch (PDOException $e) {
      return false;
    }
  } 

public static function insertar_imagen_identificacion_ham($id_taxi,$imagen)
  {

     $cantidad=0;
        $id_fotocopia=0;
    
    try{
    $consulta="SELECT max(fo.id) as 'cantidad',fo.id_fotocopia from foto fo,fotocopia f, conductor c WHERE fo.id_fotocopia=f.id and f.id=c.id_fotocopia_tarjeta_de_identificacion_de_ham and c.ci=? ";
    $comando=parent::getInstance()->getDb()->prepare($consulta);
    $comando->execute(array($id_taxi));
      $row=$comando->fetch(PDO::FETCH_ASSOC);
      
            if($row)
            {
              $cantidad=$row["cantidad"];
              $id_fotocopia=$row["id_fotocopia"];
            }
            
    } catch (PDOException $e) {
        $cantidad=0;
            $id_fotocopia=0;
    }
         $cantidad+=1;

        $nuevo_nombre="Imagen_HAM-".$id_taxi."-".$cantidad.".png";
        $rutadelaimagen="Imagen_Conductor/".$nuevo_nombre;

    $direccion_imagen_png="../taxivalle/storage/".$rutadelaimagen;
    
          //MOVER LA IMAGEN AL WEBSERVI
  move_uploaded_file($imagen,$direccion_imagen_png);
    
  try{

                   if($cantidad==1){
                    try{
                    $consulta="INSERT into fotocopia (nombre,numero) values('TARJETA DE IDENTIFICACION DE HAM','') ";
            $comando=parent::getInstance()->getDb()->prepare($consulta);
            $comando->execute();
            $id_fotocopia = parent::getInstance()->getDb()->lastInsertId();
            }catch(Exception $e){
            }
                    }
                   
                    try{
                        $consulta="INSERT into foto (id_fotocopia,id,direccion) values(?,?,?) ";
            $comando=parent::getInstance()->getDb()->prepare($consulta);
            $comando->execute(array($id_fotocopia,$cantidad,$rutadelaimagen));
             }catch(Exception $e){
            }
            try{
            $consulta="UPDATE conductor set id_fotocopia_tarjeta_de_identificacion_de_ham=? where ci=?";
            $comando=parent::getInstance()->getDb()->prepare($consulta);
            $comando->execute(array($id_fotocopia,$id_taxi));
             }catch(Exception $e){
            }
      return array($cantidad, $id_fotocopia);
    } catch (PDOException $e) {
      return false;
    }
  } 

public static function insertar_imagen_identificacion_transito($id_taxi,$imagen)
  {

     $cantidad=0;
        $id_fotocopia=0;
    
    try{
    $consulta="SELECT max(fo.id) as 'cantidad',fo.id_fotocopia from foto fo,fotocopia f, conductor c WHERE fo.id_fotocopia=f.id and f.id=c.id_fotocopia_tarjeta_de_identificacion_de_transito and c.ci=? ";
    $comando=parent::getInstance()->getDb()->prepare($consulta);
    $comando->execute(array($id_taxi));
      $row=$comando->fetch(PDO::FETCH_ASSOC);
      
            if($row)
            {
              $cantidad=$row["cantidad"];
              $id_fotocopia=$row["id_fotocopia"];
            }
            
    } catch (PDOException $e) {
        $cantidad=0;
            $id_fotocopia=0;
    }
         $cantidad+=1;

        $nuevo_nombre="Imagen_TRANSITO-".$id_taxi."-".$cantidad.".png";
        $rutadelaimagen="Imagen_Conductor/".$nuevo_nombre;

    $direccion_imagen_png="../taxivalle/storage/".$rutadelaimagen;
    
          //MOVER LA IMAGEN AL WEBSERVI
  move_uploaded_file($imagen,$direccion_imagen_png);
    
  try{

                   if($cantidad==1){
                    try{
                    $consulta="INSERT into fotocopia (nombre,numero) values('TARJETA DE IDENTIFICACION DE TRANSITO','') ";
            $comando=parent::getInstance()->getDb()->prepare($consulta);
            $comando->execute();
            $id_fotocopia = parent::getInstance()->getDb()->lastInsertId();
            }catch(Exception $e){
            }
                    }
                   
                    try{
                        $consulta="INSERT into foto (id_fotocopia,id,direccion) values(?,?,?) ";
            $comando=parent::getInstance()->getDb()->prepare($consulta);
            $comando->execute(array($id_fotocopia,$cantidad,$rutadelaimagen));
             }catch(Exception $e){
            }
            try{
            $consulta="UPDATE conductor set id_fotocopia_tarjeta_de_identificacion_de_transito=? where ci=?";
            $comando=parent::getInstance()->getDb()->prepare($consulta);
            $comando->execute(array($id_fotocopia,$id_taxi));
             }catch(Exception $e){
            }
      return array($cantidad, $id_fotocopia);
    } catch (PDOException $e) {
      return false;
    }
  } 

public static function insertar_imagen_factura_luz($id_taxi,$imagen)
  {

     $cantidad=0;
        $id_fotocopia=0;
    
    try{
    $consulta="SELECT max(fo.id) as 'cantidad',fo.id_fotocopia from foto fo,fotocopia f, conductor c WHERE fo.id_fotocopia=f.id and f.id=c.id_fotocopia_factura_de_agua_luz and c.ci=? ";
    $comando=parent::getInstance()->getDb()->prepare($consulta);
    $comando->execute(array($id_taxi));
      $row=$comando->fetch(PDO::FETCH_ASSOC);
      
            if($row)
            {
              $cantidad=$row["cantidad"];
              $id_fotocopia=$row["id_fotocopia"];
            }
            
    } catch (PDOException $e) {
        $cantidad=0;
            $id_fotocopia=0;
    }
         $cantidad+=1;

        $nuevo_nombre="Imagen_FACTURA-".$id_taxi."-".$cantidad.".png";
        $rutadelaimagen="Imagen_Conductor/".$nuevo_nombre;

    $direccion_imagen_png="../taxivalle/storage/".$rutadelaimagen;
    
          //MOVER LA IMAGEN AL WEBSERVI
  move_uploaded_file($imagen,$direccion_imagen_png);
    
  try{

                   if($cantidad==1){
                    try{
                    $consulta="INSERT into fotocopia (nombre,numero) values('FACTURA DE AGUA O LUZ','') ";
            $comando=parent::getInstance()->getDb()->prepare($consulta);
            $comando->execute();
            $id_fotocopia = parent::getInstance()->getDb()->lastInsertId();
            }catch(Exception $e){
            }
                    }
                   
                    try{
                        $consulta="INSERT into foto (id_fotocopia,id,direccion) values(?,?,?) ";
            $comando=parent::getInstance()->getDb()->prepare($consulta);
            $comando->execute(array($id_fotocopia,$cantidad,$rutadelaimagen));
             }catch(Exception $e){
            }
            try{
            $consulta="UPDATE conductor set id_fotocopia_factura_de_agua_luz=? where ci=?";
            $comando=parent::getInstance()->getDb()->prepare($consulta);
            $comando->execute(array($id_fotocopia,$id_taxi));
             }catch(Exception $e){
            }
      return array($cantidad, $id_fotocopia);
    } catch (PDOException $e) {
      return false;
    }
  } 

public static function insertar_imagen_antecedente_transito($id_taxi,$imagen)
  {

     $cantidad=0;
        $id_fotocopia=0;
    
    try{
    $consulta="SELECT max(fo.id) as 'cantidad',fo.id_fotocopia from foto fo,fotocopia f, conductor c WHERE fo.id_fotocopia=f.id and f.id=c.id_fotocopia_certificado_de_antecedentes_del_transito and c.ci=? ";
    $comando=parent::getInstance()->getDb()->prepare($consulta);
    $comando->execute(array($id_taxi));
      $row=$comando->fetch(PDO::FETCH_ASSOC);
      
            if($row)
            {
              $cantidad=$row["cantidad"];
              $id_fotocopia=$row["id_fotocopia"];
            }
            
    } catch (PDOException $e) {
        $cantidad=0;
            $id_fotocopia=0;
    }
         $cantidad+=1;

        $nuevo_nombre="Imagen_ANTECEDENTES-TRANSITO-".$id_taxi."-".$cantidad.".png";
        $rutadelaimagen="Imagen_Conductor/".$nuevo_nombre;

    $direccion_imagen_png="../taxivalle/storage/".$rutadelaimagen;
    
          //MOVER LA IMAGEN AL WEBSERVI
  move_uploaded_file($imagen,$direccion_imagen_png);
    
  try{

                   if($cantidad==1){
                    try{
                    $consulta="INSERT into fotocopia (nombre,numero) values('CERTIFICADO DE ANTECEDENTES DEL TRANSITO','') ";
            $comando=parent::getInstance()->getDb()->prepare($consulta);
            $comando->execute();
            $id_fotocopia = parent::getInstance()->getDb()->lastInsertId();
            }catch(Exception $e){
            }
                    }
                   
                    try{
                        $consulta="INSERT into foto (id_fotocopia,id,direccion) values(?,?,?) ";
            $comando=parent::getInstance()->getDb()->prepare($consulta);
            $comando->execute(array($id_fotocopia,$cantidad,$rutadelaimagen));
             }catch(Exception $e){
            }
            try{
            $consulta="UPDATE conductor set id_fotocopia_certificado_de_antecedentes_del_transito=? where ci=?";
            $comando=parent::getInstance()->getDb()->prepare($consulta);
            $comando->execute(array($id_fotocopia,$id_taxi));
             }catch(Exception $e){
            }
      return array($cantidad, $id_fotocopia);
    } catch (PDOException $e) {
      return false;
    }
  }


public static function insertar_imagen_antecedente_felcc($id_taxi,$imagen)
  {

     $cantidad=0;
        $id_fotocopia=0;
    
    try{
    $consulta="SELECT max(fo.id) as 'cantidad',fo.id_fotocopia from foto fo,fotocopia f, conductor c WHERE fo.id_fotocopia=f.id and f.id=c.id_fotocopia_certificado_de_antecedentes_del_felcc and c.ci=? ";
    $comando=parent::getInstance()->getDb()->prepare($consulta);
    $comando->execute(array($id_taxi));
      $row=$comando->fetch(PDO::FETCH_ASSOC);
      
            if($row)
            {
              $cantidad=$row["cantidad"];
              $id_fotocopia=$row["id_fotocopia"];
            }
            
    } catch (PDOException $e) {
        $cantidad=0;
            $id_fotocopia=0;
    }
         $cantidad+=1;

        $nuevo_nombre="Imagen_CERTIFICADO-ANTECEDENTES-FELCC-".$id_taxi."-".$cantidad.".png";
        $rutadelaimagen="Imagen_Conductor/".$nuevo_nombre;

    $direccion_imagen_png="../taxivalle/storage/".$rutadelaimagen;
    
          //MOVER LA IMAGEN AL WEBSERVI
  move_uploaded_file($imagen,$direccion_imagen_png);
    
  try{

                   if($cantidad==1){
                    try{
                    $consulta="INSERT into fotocopia (nombre,numero) values('CERTIFICADO DE ANTECEDENTE DEL FELCC','') ";
            $comando=parent::getInstance()->getDb()->prepare($consulta);
            $comando->execute();
            $id_fotocopia = parent::getInstance()->getDb()->lastInsertId();
            }catch(Exception $e){
            }
                    }
                   
                    try{
                        $consulta="INSERT into foto (id_fotocopia,id,direccion) values(?,?,?) ";
            $comando=parent::getInstance()->getDb()->prepare($consulta);
            $comando->execute(array($id_fotocopia,$cantidad,$rutadelaimagen));
             }catch(Exception $e){
            }
            try{
            $consulta="UPDATE conductor set id_fotocopia_certificado_de_antecedentes_del_felcc=? where ci=?";
            $comando=parent::getInstance()->getDb()->prepare($consulta);
            $comando->execute(array($id_fotocopia,$id_taxi));
             }catch(Exception $e){
            }
      return array($cantidad, $id_fotocopia);
    } catch (PDOException $e) {
      return false;
    }
  }
  // FIN DE LA INSERCION DE LA UMAGENES DEL CONDUCTOR,.


 public static function get_vehiculo_por_placa($placa)
  {
    $resultado=false;
     try{ 
    $consulta="SELECT placa,modelo,cilindrada,tipo,nro_chasis,nro_poliza,nro_rua,nro_soat,nro_puertas,nro_asientos,nro_inspeccion_tecnica,color,direccion_imagen_adelante,direccion_imagen_atras,direccion_imagen_interior_adelante,direccion_imagen_interior_atras from vehiculo v WHERE  v.placa=? ";
    $comando=parent::getInstance()->getDb()->prepare($consulta);
    $comando->execute(array($placa));
    $row=$comando->fetch(PDO::FETCH_ASSOC);
      
            if($row)
            {
              $resultado=$row;
            }   
            else{
              $resultado=false;
            }   
    } catch (PDOException $e) {
      $resultado=false;
     }
    return $resultado;
    }

    public static function get_conductor_panico_por_ci($ci)
  {
    $resultado=false;
     try{ 
    $consulta="SELECT c.ci,c.nombre,c.paterno,c.materno,c.celular
    from conductor c  WHERE    c.ci=? limit 1 ";
    $comando=parent::getInstance()->getDb()->prepare($consulta);
    $comando->execute(array($ci));
    $row=$comando->fetch(PDO::FETCH_ASSOC);
      
            if($row)
            {
              $resultado=$row;
            }   
            else{
              $resultado=false;
            }   
    } catch (PDOException $e) {
      $resultado=false;
     }
    return $resultado;
    }
       
public static function get_conductor_por_ci($ci)
  {
    $resultado=false;
     try{ 
    $consulta="SELECT correo from conductor where ci=?";
    $comando=parent::getInstance()->getDb()->prepare($consulta);
    $comando->execute(array($ci));
    $row=$comando->fetch(PDO::FETCH_ASSOC);
    $correo=$row['correo'];   

    $consulta="SELECT f.numero as 'nro_ham' from conductor c,fotocopia f where c.ci=? and c.id_fotocopia_tarjeta_de_identificacion_de_ham=f.id ";
    $comando=parent::getInstance()->getDb()->prepare($consulta);
    $comando->execute(array($ci));
    $nro_ham=$comando->fetch(PDO::FETCH_ASSOC)['nro_ham'];

    $consulta="SELECT f.numero as 'nro_transito' from conductor c,fotocopia f where c.ci=? and c.id_fotocopia_tarjeta_de_identificacion_de_transito=f.id ";
    $comando=parent::getInstance()->getDb()->prepare($consulta);
    $comando->execute(array($ci));
    $nro_transito=$comando->fetch(PDO::FETCH_ASSOC)['nro_transito'];

    $consulta="SELECT f.numero as 'nro_factura_luz' from conductor c,fotocopia f where c.ci=? and c.id_fotocopia_factura_de_agua_luz=f.id ";
    $comando=parent::getInstance()->getDb()->prepare($consulta);
    $comando->execute(array($ci));
    $nro_factura_luz=$comando->fetch(PDO::FETCH_ASSOC)['nro_factura_luz'];

    $consulta="SELECT f.numero as 'nro_antecedente_transito' from conductor c,fotocopia f where c.ci=? and c.id_fotocopia_certificado_de_antecedentes_del_transito=f.id ";
    $comando=parent::getInstance()->getDb()->prepare($consulta);
    $comando->execute(array($ci));
    $nro_antecedente_transito=$comando->fetch(PDO::FETCH_ASSOC)['nro_antecedente_transito'];

    $consulta="SELECT f.numero as 'nro_antecedente_felcc' from conductor c,fotocopia f where c.ci=? and c.id_fotocopia_certificado_de_antecedentes_del_felcc=f.id ";
    $comando=parent::getInstance()->getDb()->prepare($consulta);
    $comando->execute(array($ci));
    $nro_antecedente_felcc=$comando->fetch(PDO::FETCH_ASSOC)['nro_antecedente_felcc'];

    return array($ci, $correo,$nro_ham,$nro_transito,$nro_factura_luz,$nro_factura_luz,$nro_antecedente_transito,$nro_antecedente_felcc);            
    } catch (PDOException $e) {
      $resultado=false;
     }
    return $resultado;
    }

public static function existe_ruta_por_id_carrera_numero($id_pedido,$id_carrera,$numero)
  {
    $resultado=false;
     try{ 
    $consulta="SELECT numero from ruta WHERE  id_pedido=? and id_carrera=? and numero=? limit 1 ";
    $comando=parent::getInstance()->getDb()->prepare($consulta);
    $comando->execute(array($id_pedido,$id_carrera,$numero));
    $row=$comando->fetch(PDO::FETCH_ASSOC);
      
            if($row)
            {
              $resultado=true;
            }   
            else{
              $resultado=false;
            }   
    } catch (PDOException $e) {
      $resultado=false;
     }
    return $resultado;
    }


 public static function existe_ruta_prueba_por_id_carrera_numero($id_carrera,$numero)
  {
    $resultado=false;
     try{ 
    $consulta="SELECT numero from ruta_prueba WHERE  id_carrera=? and numero=? limit 1 ";
    $comando=parent::getInstance()->getDb()->prepare($consulta);
    $comando->execute(array($id_carrera,$numero));
    $row=$comando->fetch(PDO::FETCH_ASSOC);
      
            if($row)
            {
              $resultado=true;
            }   
            else{
              $resultado=false;
            }   
    } catch (PDOException $e) {
      $resultado=false;
     }
    return $resultado;
    }


       


//FUNCIONES PARA OBTENER LA TARIFA DE PRUEBA
public static function set_ubicacion_punto_prueba($latitud,$longitud,$id_carrera,$numero,$distancia,$rotacion)
  {
  if(self::existe_ruta_prueba_por_id_carrera_numero($id_carrera,$numero)==false){
     try{
    $consulta="INSERT ruta_prueba (latitud,longitud,id_carrera,numero,distancia,rotacion)values(?,?,?,?,?,?)";
    $comando=parent::getInstance()->getDb()->prepare($consulta);
    $comando->execute(array($latitud,$longitud,$id_carrera,$numero,$distancia,$rotacion));
    return true;
    }catch(PDOException $e)
    {
      return false;
    }
  }else
  {return false;}
}

public function guardar_imagen_png($dato,$file)
  {
    if($dato!="")
    {

    // Imagen png codificada en base64.
    $v_imagen_Base64 = "data:image/png;base64,".$dato;

    // Eliminamos data:image/png; y base64, de la cadena que tenemos
    // Hay otras formas de hacerlo
    list(, $v_imagen_Base64) = explode(';', $v_imagen_Base64);
    list(, $v_imagen_Base64) = explode(',', $v_imagen_Base64);

    // Decodificamos $Base64Img codificada en base64.
    $v_imagen_Base64 = base64_decode($v_imagen_Base64);
    
    // Escribimos la información obtenida en un archivo para que se cree la imagen correctamente
    $file=str_replace(".txt", ".png", $file);
    file_put_contents($file, $v_imagen_Base64);
    }
  }

  
  public static function get_taxi_en_rango($latitud,$longitud,$diametro)
   {  
      $consulta="SELECT distancia_entre_dos_puntos('".$latitud."','".$longitud."',c.latitud_asignacion,c.longitud_asignacion) as distancia ,
      c.rotacion,c.latitud_asignacion as 'latitud',c.longitud_asignacion as 'longitud',v.moto,c.ci as 'ci',now() as 'fecha' from conductor c,vehiculo v 
      where c.id_vehiculo=v.placa and c.estado=1 and c.login=1 
      and distancia_entre_dos_puntos('".$latitud."','".$longitud."',c.latitud_asignacion,c.longitud_asignacion)<= ?  
      and c.ci not in (select id_conductor from pedido where estado<=1 and tipo_reserva=0 and id_conductor is not null)  
      order by distancia asc ";

     $query = parent::getInstance()->getDb()->prepare($consulta);
    
    $query->execute(array($diametro)); 
    $row = $query->fetchAll();
    if($row)
    {
      return $row;
    }else 
     {return false;} 
   }



  public static function get_conductor_en_rango_por_tiempo($latitud,$longitud,$diametro)
  {
    try{
  //  $consulta="SELECT *,distancia_entre_dos_puntos(latitud,longitud,?,?) as 'distancia',now() as 'fecha', TIMESTAMPDIFF(MINUTE,fecha_ultimo,now()) as 'minuto' from conductor where distancia_entre_dos_puntos(latitud,longitud,?,?)<= ? and estado='1' and TIMESTAMPDIFF(MINUTE,fecha_ultimo,now())<=10 and login='1'";

     $consulta="SELECT distancia_entre_dos_puntos('".$latitud."','".$longitud."',c.latitud_asignacion,c.longitud_asignacion) as distancia ,
      c.rotacion,c.latitud_asignacion as 'latitud',c.longitud_asignacion as 'longitud',v.moto,now() as 'fecha',c.fecha_ultimo, TIMESTAMPDIFF(MINUTE,fecha_ultimo,now()) as 'minuto',c.ci as 'ci'  from conductor c,vehiculo v,ajuste a 
      where c.id_vehiculo=v.placa and c.estado=1 and c.login=1 
      and distancia_entre_dos_puntos('".$latitud."','".$longitud."',c.latitud_asignacion,c.longitud_asignacion)<= ?  
      and c.ci not in (select id_conductor from pedido where estado<=1 and tipo_reserva=0 and id_conductor is not null) and TIMESTAMPDIFF(MINUTE,c.fecha_ultimo,now())<=a.tiempo_visualizacion_conductor and a.anio=".date('Y')." 
      order by distancia asc ";
    $comando=parent::getInstance()->getDb()->prepare($consulta);
    $comando->execute(array($diametro));
    $row = $comando->fetchAll();
    if($row)

        return $row;
      else
        return -1;
  }catch(PDOException $e)
  {
    echo $e;
    return -1;
  } 
  }

   public static function get_marca_vehiculo($marca)
  {
    
     try{ 
      $consulta="SELECT marca from vehiculo where marca like '%".$marca."%' GROUP BY marca order by marca asc";
       
      $query = parent::getInstance()->getDb()->prepare($consulta);
        $query->execute(); 
         $tokens = array(); 

        while($row=$query->fetch(PDO::FETCH_OBJ)) {
         array_push($tokens, $row->marca);
        }
   

      return $tokens;
       
      } catch (PDOException $e) {
        return false;
       }
    }

  public static function get_pedido_actual($id_conductor)
  {
   try{

    $consulta="SELECT  p.*,concat(nombre,' ',apellido)as 'nombre_usuario',u.celular
     from pedido p, usuario u  where p.id_usuario=u.id and p.estado <2 and   p.id_conductor=? and tipo_reserva=0  order by p.id desc limit 1 ";
    $comando=parent::getInstance()->getDb()->prepare($consulta);
     $comando->execute(array($id_conductor));
     $row=$comando->fetch(PDO::FETCH_OBJ);
     if($row){
      return $row;
     }else{
      return -1;
     }

     
    } catch (PDOException $e) {
        return -1;
      }
    }

  public static function get_id_pedido_actual($id_conductor)
  {

 try{
      $consulta="SELECT  p.*,concat(nombre,' ',apellido)as 'nombre_usuario',u.celular
     from pedido p, usuario u  where p.id_usuario=u.id and p.estado <2 and   p.id_conductor=? and tipo_reserva=0  order by p.id desc limit 1 ";
      $comando=parent::getInstance()->getDb()->prepare($consulta);
      $comando->execute(array($id_conductor));
      $row=$comando->fetch(PDO::FETCH_ASSOC);
      if($row)
      {
        return $row['id'];
      }else
      {
        return -1;
      }    
    }catch(PDOException $e)
    {
       
      return -1;
    }


    }
  
   public static function get_carrera_ultimo_por_id($id_pedido)
  {
   try{
    $consulta="SELECT  * from carrera  where  id_pedido=? order by id desc limit 1 ";
    $comando=parent::getInstance()->getDb()->prepare($consulta);
     $comando->execute(array($id_pedido));
     $row=$comando->fetch(PDO::FETCH_OBJ);
     if($row){
      return $row;
     }else{
      return -1;
     }

     
    } catch (PDOException $e) {
        return -1;
      }
    }
  
     public static function get_carrera_ruta_ultimo_por_id($id_pedido)
  {


 try{
      $consulta="SELECT numero from ruta  where  id_pedido=?  order by id_pedido,id_carrera,numero desc limit 1 ";
      $comando=parent::getInstance()->getDb()->prepare($consulta);
      $comando->execute(array($id_pedido));
      $row=$comando->fetch(PDO::FETCH_ASSOC);
      if($row)
      {
        return $row['numero'];
      }else
      {
        return 1;
      }    
    }catch(PDOException $e)
    {
       
      return 1;
    }
  }

  

  }
?>