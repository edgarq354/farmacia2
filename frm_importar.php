<?php

require_once('Basededatos.php');




   class Importar extends Database
{
  public function Importar()
  {
    parent::Database();
  }

  public static function importar_conductor()
  {
  	$consulta="SELECT * FROM tab_conductores";
   	


   	$query = parent::getInstance()->getDb()->prepare($consulta);
   	
        $query->execute(array(8000)); 
         $tokens = array(); 

$total=0;
$registrado=0;

        while($row=$query->fetch(PDO::FETCH_OBJ)) {

        	$sw_registrado=false;


        	$sw_existe=self::existe($row->cond_licencia);
        	if($sw_existe==false)
        	{
        		$fecha_nac=str_replace('/', '-', $row->cond_fechanac);
        			$consulta="INSERT into conductor (ci,nombre,paterno,materno,expedido,fecha_nacimiento,sexo,celular,usuario,contrasenia,direccion,categoria_licencia,tipo,id_empresa,tipo_de_pago) values(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
				try{
	  				$comando=parent::getInstance()->getDb()->prepare($consulta);
					$comando->execute(array($row->cond_licencia,$row->cond_nombre,$row->cond_paterno,$row->cond_materno,$row->cond_expedido,$fecha_nac,'M',$row->cond_celular,$row->cond_licencia,$row->cond_licencia,$row->cond_domicilio,$row->cond_categoria,$row->cond_tipo,'6','SEMANA'));

						$sw_registrado=true;

		  		}catch(PDOException $e)
		  		{
		  			$sw_registrado=false;
            echo $e;
		  		}

        	}

        	if($sw_registrado==true)
        	{
        		$registrado+=1;
        		echo "<font>REGISTRO [CI:".$row->cond_licencia."] [Nombre:".$row->cond_nombre."]</font><br>";
        	}
        	else if($sw_existe==true)
        	{
        		echo "<font color='BLUE'>TA EXISTE EN EL SISTEMA [CI:".$row->cond_licencia."] [Nombre:".$row->cond_nombre."]</font><br>";
        	}else
        	{
        		echo "<font color='RED'>NO SE REGISTRO [CI:".$row->cond_licencia."] [Nombre:".$row->cond_nombre."]</font><br>";
        	}
 			$total+=1;

 				

    }
    $resta=$total-$registrado;
 			echo "<font size='15px'>TOTAL:".$total."<br>REGISTRADO:".$registrado."<br>NO SE REGISTRO:".$resta."</font>";

  }

    public static  function existe($buscar)
   { $resultado=false;
   	try{
   	$query =parent::getInstance()->getDb()->prepare("SELECT ci from conductor where ci=? limit 1");
        $query->execute(array($buscar)); 
 		$row=$query->fetch(PDO::FETCH_ASSOC);
			if($row)
			{
				if($row['ci']!='')
				{
					$resultado=true;
				}
				else
				{
					$resultado=false;
				}
			}
			else
			{
				$resultado=false;
			}
		}
		catch(PDOException $e)
		{	
        $resultado=false;
    }
    return $resultado;

   }


 public static function importar_vehiculo()
  {
    $consulta="SELECT * FROM tab_moviles";
    


    $query = parent::getInstance()->getDb()->prepare($consulta);
    
        $query->execute(array(8000)); 
         $tokens = array(); 

$total=0;
$registrado=0;

        while($row=$query->fetch(PDO::FETCH_OBJ)) {

          $sw_registrado=false;


          $sw_existe=self::existe_vehiculo($row->mov_placa);
          if($sw_existe==false)
          {           
              $consulta="INSERT into vehiculo (placa,marca,clase,color,tipo,id_empresa) values(?,?,?,?,?,?)";
        try{
            $comando=parent::getInstance()->getDb()->prepare($consulta);
          $comando->execute(array($row->mov_placa,$row->mov_marca,$row->mov_clase,$row->mov_color,$row->mov_tipo,'6'));

            $sw_registrado=true;

          }catch(PDOException $e)
          {
            $sw_registrado=false;
            echo $e;
          }

          }

          if($sw_registrado==true)
          {
            $registrado+=1;
            echo "<font>REGISTRO [PLACA:".$row->mov_placa."] [marca:".$row->mov_marca."]</font><br>";
          }
          else if($sw_existe==true)
          {
            echo "<font color='BLUE'>YA EXISTE EN EL SISTEMA [PLACA:".$row->mov_placa."] [marca:".$row->mov_marca."]</font><br>";
          }else
          {
            echo "<font color='RED'>NO SE REGISTRO [PLACA:".$row->mov_placa."] [marca:".$row->mov_marca."]</font><br>";
          }
      $total+=1;

        

    }
    $resta=$total-$registrado;
      echo "<font size='15px'>TOTAL:".$total."<br>REGISTRADO:".$registrado."<br>NO SE REGISTRO:".$resta."</font>";

  }

    public static  function existe_vehiculo($buscar)
   { $resultado=false;
    try{
    $query =parent::getInstance()->getDb()->prepare("SELECT placa from vehiculo where placa=? limit 1");
        $query->execute(array($buscar)); 
    $row=$query->fetch(PDO::FETCH_ASSOC);
      if($row)
      {
        if($row['placa']!='')
        {
          $resultado=true;
        }
        else
        {
          $resultado=false;
        }
      }
      else
      {
        $resultado=false;
      }
    }
    catch(PDOException $e)
    { 
        $resultado=false;
    }
    return $resultado;

   }

    public static function importar_asignacion()
  {
    $consulta="SELECT c.cond_licencia as 'ci',m.mov_placa as 'placa',u.uni_id as 'codigo' FROM tab_conductores c,tab_moviles m,tab_unidades u where c.cond_id=u.cond_id and m.mov_id=u.mov_id";
    


    $query = parent::getInstance()->getDb()->prepare($consulta);
    
        $query->execute(); 
         $tokens = array(); 

$total=0;
$registrado=0;

        while($row=$query->fetch(PDO::FETCH_OBJ)) {

          $sw_registrado=false;


          $sw_existe=self::existe_asignacion($row->placa,$row->ci);
          if($sw_existe==false)
          {           
              $consulta="INSERT into asignacion (id_vehiculo,id_conductor,codigo_empresa,id_empresa) values(?,?,?,?)";
        try{
            $comando=parent::getInstance()->getDb()->prepare($consulta);
          $comando->execute(array($row->placa,$row->ci,$row->codigo,'6'));

            $sw_registrado=true;

          }catch(PDOException $e)
          {
            $sw_registrado=false;
            echo $e;
          }

          }

          if($sw_registrado==true)
          {
            $registrado+=1;
            echo "<font>REGISTRO [PLACA:".$row->placa."] [CI:".$row->ci."]</font><br>";
          }
          else if($sw_existe==true)
          {
            echo "<font color='BLUE'>YA EXISTE EN EL SISTEMA [PLACA:".$row->placa."] [CI:".$row->ci."]</font><br>";
          }else
          {
            echo "<font color='RED'>NO SE REGISTRO [PLACA:".$row->placa."] [CI:".$row->ci."]</font><br>";
          }
      $total+=1;

        

    }
    $resta=$total-$registrado;
      echo "<font size='15px'>TOTAL:".$total."<br>REGISTRADO:".$registrado."<br>NO SE REGISTRO:".$resta."</font>";

  }

    public static  function existe_asignacion($placa,$ci)
   { $resultado=false;
    try{
    $query =parent::getInstance()->getDb()->prepare("SELECT id_vehiculo,id_conductor from asignacion where id_vehiculo=? and id_conductor=? limit 1");
        $query->execute(array($placa,$ci)); 
    $row=$query->fetch(PDO::FETCH_ASSOC);
      if($row)
      {
        if($row['id_vehiculo']!='' && $row['id_conductor']!='')
        {
          $resultado=true;
        }
        else
        {
          $resultado=false;
        }
      }
      else
      {
        $resultado=false;
      }
    }
    catch(PDOException $e)
    { 
        $resultado=false;
    }
    return $resultado;

   }



public static function importar_pago_frecuencia()
   {  
    $total=0;
    $registrado=0;
      $consulta="SELECT * from pagofrecuencia where pfrec_observaciones='PERMISO' group by usuario,uni_id ORDER BY `pagofrecuencia`.`uni_id` ASC";
    
    $query = parent::getInstance()->getDb()->prepare($consulta);
    $query->execute(); 

        while($row=$query->fetch(PDO::FETCH_OBJ)) {
          $id_usuario=self::get_id_usuario($row->usuario);
          $id_conductor=self::get_id_conductor($row->uni_id);

              $consulta="INSERT into pago_frecuencia (id_conductor,id_login,permiso) values(?,?,1)";
          try{
            $comando=parent::getInstance()->getDb()->prepare($consulta);
            $comando->execute(array($id_conductor,$id_usuario));
            $lastId = parent::getInstance()->getDb()->lastInsertId();
              self::get_detalle_frecuencia($row->uni_id,$lastId,$id_conductor,$row->usuario);
              $registrado +=1;


            }catch(PDOException $e)
            {
              echo "<font  color='RED'>ERROR AL REGISTRAR  CODIGO:".$row->uni_id." CI:".$id_conductor."</font> ID:".$row->Id." USUARIO:".$row->usuario."<BR>".$e."<br>";
            }

            $total +=1;
         }

          $resta=$total-$registrado;
      echo "<font size='15px' color='PURPPLE'>TOTAL:".$total."<br>REGISTRADO:".$registrado."<br>NO SE REGISTRO:".$resta."</font><br>";

      self::actualizar_monto_pago_frecuencia();

   }


   public static function actualizar_monto_pago_frecuencia()
   {
      $consulta="SELECT id from pago_frecuencia ";
    
    $query = parent::getInstance()->getDb()->prepare($consulta);
    $query->execute(array($codigo,$usuario)); 

        while($row=$query->fetch(PDO::FETCH_OBJ)) {
              self::actualizar_pago($row->id);
         }
     
   }


   public static function actualizar_pago($id_pago_frecuencia)
   {

      $consulta="SELECT sum(costo_por_frecuencia)as 'monto' from detalle_frecuencia where id_pago_frecuencia=? limit 1";
    
    $query = parent::getInstance()->getDb()->prepare($consulta);
    $query->execute(array($id_pago_frecuencia)); 
    $row=$query->fetch(PDO::FETCH_OBJ);

         $actualizar="UPDATE pago_frecuencia SET monto_total=?, monto_frecuencia=? where id=?";
         $update=parent::getInstance()->getDb()->prepare($actualizar);
         $update->execute(array($row->monto,$row->monto,$id_pago_frecuencia)); 
   }



   public static function get_detalle_frecuencia($codigo,$id_pago_frecuencia,$id_conductor,$usuario)
   {  
      $total=0;
      $registrado=0;
      $consulta="SELECT * from pagofrecuencia where pfrec_observaciones='PAGO REALIZADO' and uni_id=? and usuario=?";
    
    $query = parent::getInstance()->getDb()->prepare($consulta);
    $query->execute(array($codigo,$usuario)); 

        while($row=$query->fetch(PDO::FETCH_OBJ)) {

          $sw_registrado=self::frecuencia_pagado($id_conductor,$row->pfrec_efectivo);
          $id_frecuencia=self::get_id_frecuencia($row->frec_id);
          if($sw_registrado==false && $id_frecuencia!=-1)
          {
               $consulta="INSERT into detalle_frecuencia (id_pago_frecuencia,id_frecuencia,costo_por_frecuencia) values(?,?,?)";
              try{
                  $comando=parent::getInstance()->getDb()->prepare($consulta);
                   $comando->execute(array($id_pago_frecuencia,$id_frecuencia,$row->pfrec_efectivo));

                  $sw_registrado=true;
                  $registrado +=1;
                }catch(PDOException $e)
                {
                   echo " numero:".$id_frecuencia.". ci:".$id_conductor." exception:".$e."<br>";
                  $sw_registrado=false;
                }
          }
          else
          {
            echo "<font  color='RED'>SW_REGISTRADO:".$sw_registrado." ID_FRECUENCIA:".$id_frecuencia."  NUMERO:".$row->frec_id."</font> <br>";
          }
          $total +=1;
         }
      $resta=$total-$registrado;
      echo "<font  color='BLUE'>NRO:".$id_pago_frecuencia." TOTAL:".$total."<br>REGISTRADO:".$registrado."<br>NO SE REGISTRO:".$resta."</font><br><br>";
   
   }

   public static  function get_id_usuario($nombre)
   {
    $resultado=-1;
    try{
    $query =parent::getInstance()->getDb()->prepare("SELECT id from admins where id_empresa=6 and usuario=? limit 1");
        $query->execute(array($nombre)); 
    $row=$query->fetch(PDO::FETCH_ASSOC);
      if($row)
      {
        if($row['id'])
        {
          $resultado=$row['id'];
        }
        else
        {
          $resultado=-1;
        }
      }
      else
      {
        $resultado=-1;
      }
    }
    catch(PDOException $e)
    { 
        $resultado=-1;
    }
    return $resultado;

   }
    public static  function get_id_conductor($codigo_empresa)
   {
    $resultado=-1;
    try{
    $query =parent::getInstance()->getDb()->prepare("SELECT id_conductor from asignacion where id_empresa=6 and codigo_empresa=? limit 1");
        $query->execute(array($codigo_empresa)); 
    $row=$query->fetch(PDO::FETCH_ASSOC);
      if($row)
      {
        if($row['id_conductor'])
        {
          $resultado=$row['id_conductor'];
        }
        else
        {
          $resultado=-1;
        }
      }
      else
      {
        $resultado=-1;
      }
    }
    catch(PDOException $e)
    { 
        $resultado=-1;
    }
    return $resultado;

   }


   public static  function get_id_frecuencia($numero_semana)
   {
    $resultado=-1;
    try{
    $query =parent::getInstance()->getDb()->prepare("SELECT id from frecuencia where tipo='SEMANA' and numero=? limit 1");
        $query->execute(array($numero_semana)); 
    $row=$query->fetch(PDO::FETCH_ASSOC);
      if($row)
      {
        if($row['id'])
        {
          $resultado=$row['id'];
        }
        else
        {
          $resultado=-1;
        }
      }
      else
      {
        $resultado=-1;
      }
    }
    catch(PDOException $e)
    { 
        $resultado=-1;
    }
    return $resultado;

   }

   public static  function frecuencia_pagado($ci,$numero_frecuencia)
   { $resultado=false;
    try{
    $query =parent::getInstance()->getDb()->prepare("SELECT df.id_frecuencia,f.numero as 'numero',pf.id_conductor as 'ci' from pago_frecuencia pf, detalle_frecuencia df,frecuencia f where pf.id=df.id_pago_frecuencia and f.id=df.id_frecuencia and f.tipo='SEMANA' and pf.id_conductor=? and f.numero=? limit 1");
        $query->execute(array($ci,$numero_frecuencia)); 
    $row=$query->fetch(PDO::FETCH_ASSOC);
      if($row)
      {
        if($row['ci']==$ci && $row['numero']==$numero_frecuencia)
        {
          $resultado=true;
        }
        else
        {
          $resultado=false;
        }
      }
      else
      {
        $resultado=false;
      }
    }
    catch(PDOException $e)
    { 
        $resultado=false;
    }
    return $resultado;

   }
}






//IMPORTAR CONDUCTOR
switch ($_GET['opcion']) {
  case 'pago_frecuencia':
    Importar::importar_pago_frecuencia();
    break;
   case 'actualizar':
    Importar::actualizar_monto_pago_frecuencia();
    break;
  default:
    # code...
    break;
}

//importar::actualizar_monto_pago_frecuencia();

?>