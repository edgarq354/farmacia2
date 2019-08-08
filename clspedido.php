<?php
require_once 'Basededatos.php';
require_once 'Firebase.php';
require_once 'Push.php';

class Pedido extends Database
{
   public function Pedido()
	{	
	  parent::Database();	
	}	


public static function confirmar_pedido_delivery_usuario($id_pedido,$id_usuario,$id_lugar,$carrito,$nit,$nombre,$latitud_final,$longitud_final,$direccion,$referencia)
{
	$resultado=false;
	$array = json_decode($carrito);
	$monto_pedido=0;
	$c=0;
	$total=0;
$lugar=self::get_lugar_por_id($id_lugar);
$latitud=$lugar['latitud'];
$longitud=$lugar['longitud'];

	foreach ($array as $value) {
		$total+=1;
		$_id_producto=$value->id_producto;
		$_id_pedido=$value->id_pedido;
		$_cantidad=$value->cantidad;
		$_monto_unidad=$value->monto_unidad;
		$_monto_total=$value->monto_total;
		$sw_carrito=self::guardar_carrito($_id_producto,$_id_pedido,$_cantidad,$_monto_unidad,$_monto_total);
		if($sw_carrito==true)
		{
			$monto_pedido+=$_monto_total;
			$c+=1;
		}else
		{
			$sw_carrito_p=self::actualizar_carrito($_id_producto,$_id_pedido,$_cantidad,$_monto_unidad,$_monto_total);
			if ($sw_carrito_p==true) {
				$monto_pedido+=$_monto_total;
				$c+=1;
			}
		} 
	}
	 
if($c>0)
{
	$consulta="UPDATE pedido set fecha_pedido=now(),monto_pedido=?,estado_pedido=10,id_lugar=?,nit_cliente=?, nombre_cliente=?,latitud_final=?,longitud_final=?,latitud=?,longitud=?,direccion=?,direccion_inicio=? where id=?";
			try{
  			$comando=parent::getInstance()->getDb()->prepare($consulta);
				$comando->execute(array($monto_pedido,$id_lugar,$nit,$nombre,$latitud_final,$longitud_final,$latitud,$longitud,$referencia,$direccion,$id_pedido));
				  $resultado=true;
 
	  		}catch(PDOException $e)
	  		{

	  		 $resultado=false;
	  		}
}
if($resultado==true)
{
	self::enviar_notificacion_hay_un_pedido($id_lugar,"Nuevo Delivery por ".$nombre);
}

	
 return $resultado;
}
 
 public static function guardar_carrito($id_producto,$id_pedido,$cantidad,$monto_unidad,$monto_total)
 {
 	$resultado=false;

	$consulta="INSERT into carrito (id_producto,id_pedido,cantidad,monto_unidad,monto_total) values(?,?,?,?,?)";
			try{
  			$comando=parent::getInstance()->getDb()->prepare($consulta);
				$comando->execute(array($id_producto,$id_pedido,$cantidad,$monto_unidad,$monto_total));
				$id_pedido = parent::getInstance()->getDb()->lastInsertId();
				  $resultado=true;
 
	  		}catch(PDOException $e)
	  		{
	  		 $resultado=false;
	  		}
	return $resultado;

 }

  public static function actualizar_carrito($id_producto,$id_pedido,$cantidad,$monto_unidad,$monto_total)
 {
 	$resultado=false;

	$consulta="UPDATE carrito set cantidad=?, monto_unidad=?,monto_total=? where id_producto=? and id_pedido=?";
			try{
  			$comando=parent::getInstance()->getDb()->prepare($consulta);
				$comando->execute(array($cantidad,$monto_unidad,$monto_total,$id_producto,$id_pedido));
				$id_pedido = parent::getInstance()->getDb()->lastInsertId();
				  $resultado=true;
 
	  		}catch(PDOException $e)
	  		{
	  		 $resultado=false;
	  		}
	return $resultado;

 }

	public static   function registrar_delivery($id_usuario)
   { $query = parent::getInstance()->getDb()->prepare("SELECT * from pedido where tipo_pedido=1 and estado_pedido<1 and id_usuario=?");
        $query->execute(array($id_usuario)); 
     $cantidad=0;
        while($row=$query->fetch(PDO::FETCH_OBJ)) {
 			 $cantidad++;
    }
    $id_pedido=0;

    if($cantidad==0)
    {
    	$consulta="INSERT into pedido (id_usuario,tipo_pedido,tipo_pedido_auxiliar,clase_vehiculo) values(?,1,1,5)";
			try{
  			$comando=parent::getInstance()->getDb()->prepare($consulta);
				$comando->execute(array($id_usuario));
				$id_pedido = parent::getInstance()->getDb()->lastInsertId();
				  
 
	  		}catch(PDOException $e)
	  		{
	  			
	  			$id_pedido='-1';
	  		}
    }
        return $id_pedido; 
    
   }

    public static  function get_delivery_lugar_por_id($id_pedido)
   {$resultado=-1;
   	try{
   	$query = parent::getInstance()->getDb()->prepare("SELECT p.*,l.nombre from pedido p,lugar l where l.id=p.id_lugar and p.id=? ");
        $query->execute(array($id_pedido)); 
 		$row=$query->fetch(PDO::FETCH_ASSOC);
			if($row)
			{
				return $row;
			}
		}
		catch(PDOException $e)
		{	
        $resultado =-1; 
   		 }
    return $resultado;
   }
	
		public static  function get_pedido_por_id_pedido($id_pedido)
	{
		$consulta="SELECT concat(c.nombre,' ',c.paterno) as 'nombre_taxi',c.celular,c.ci as 'id_taxi',v.marca,v.placa,c.numero_movil,
		p.id as 'id_pedido',u.id as 'id_usuario',v.color,p.latitud,p.longitud,p.estado,p.abordo,
		c.calificacion as 'calificacion_conductor',v.calificacion as'calificacion_vehiculo',
		e.razon_social as 'empresa',e.id as 'id_empresa',p.clase_vehiculo,p.id_lugar,p.tipo_pedido_auxiliar  from pedido p,conductor c,vehiculo v,usuario u,empresa e 
		where v.placa=p.id_vehiculo and c.ci=p.id_conductor and p.id_usuario=u.id and c.id_empresa=e.id  
		and c.id_vehiculo=v.placa and p.id=?";
		try{
			$comando=parent::getInstance()->getDb()->prepare($consulta);
			$comando->execute(array($id_pedido));
			$row=$comando->fetch(PDO::FETCH_ASSOC);
			if($row)
			{
				return $row;
			}
			return -1;
		}catch(PDOException $e)
		{
			return -1;
		}
	}

		public static  function get_pedido_por_id($id_pedido)
	{
		$consulta="SELECT  p.* from pedido p where  p.id=?";
		try{
			$comando=parent::getInstance()->getDb()->prepare($consulta);
			$comando->execute(array($id_pedido));
			$row=$comando->fetch(PDO::FETCH_ASSOC);
			if($row)
			{
				return $row;
			}
			return -1;
		}catch(PDOException $e)
		{
			return -1;
		}
	}

		public static  function get_lugar_por_id($id_lugar)
	{
		$consulta="SELECT  p.* from lugar p where  p.id=?";
		try{
			$comando=parent::getInstance()->getDb()->prepare($consulta);
			$comando->execute(array($id_lugar));
			$row=$comando->fetch(PDO::FETCH_ASSOC);
			if($row)
			{
				return $row;
			}
			return -1;
		}catch(PDOException $e)
		{
			return -1;
		}
	}

	public static function get_pedido_por_id_usuario($id_usuario)
	{
		$consulta="SELECT * from pedido where estado<=1 and id_vehiculo is not null and tipo_reserva=0 and tipo_pedido=0 and   id_usuario=?  limit 1 ";
		try{
			$comando=parent::getInstance()->getDb()->prepare($consulta);
			$comando->execute(array($id_usuario));
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
 
 public static function get_reservas_por_id_usuario($id_usuario)
	{
		$consulta="SELECT * from pedido where tipo_reserva=1 and id_usuario=? order by id desc";
		try{
			$comando=parent::getInstance()->getDb()->prepare($consulta);
			$comando->execute(array($id_usuario));
			$row=$comando->fetchAll();
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


	public static function get_delivery_por_id_usuario($id_usuario)
	{
		$consulta="SELECT * from pedido where tipo_pedido=1 and id_usuario=? order by id desc";
		try{
			$comando=parent::getInstance()->getDb()->prepare($consulta);
			$comando->execute(array($id_usuario));
			$row=$comando->fetchAll();
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


	 public static function get_delivery($id_usuario)
	{
		$consulta="SELECT p.*  from pedido p  where  p.tipo_pedido=1 and  p.id_usuario=? order by id desc";
		try{
			$comando=parent::getInstance()->getDb()->prepare($consulta);
			$comando->execute(array($id_usuario));
			$row=$comando->fetchAll();
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

		 public static function get_delivery_por_id_administrador_lugar($id_usuario)
	{
		$consulta="SELECT p.*  from pedido p,lugar l  where l.id=p.id_lugar and p.tipo_pedido=1 and l.id_administrador=? order by id desc";
		try{
			$comando=parent::getInstance()->getDb()->prepare($consulta);
			$comando->execute(array($id_usuario));
			$row=$comando->fetchAll();
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

	 public static function get_delivery_pendiente($id_lugar)
	{
		$consulta="SELECT p.*,u.nombre,u.apellido,u.celular  from pedido p,lugar l,usuario u  where l.id=p.id_lugar and p.estado_pedido=10 and u.id=p.id_usuario and p.tipo_pedido=1 and l.id=? order by id desc";
		try{
			$comando=parent::getInstance()->getDb()->prepare($consulta);
			$comando->execute(array($id_lugar));
			$row=$comando->fetchAll();
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
	public static function  get_delivery_en_proceso($id_lugar)
	{
		$consulta="SELECT p.*,u.nombre,u.apellido,u.celular  from pedido p,lugar l,usuario u  where l.id=p.id_lugar and p.estado_pedido=11 and u.id=p.id_usuario and p.tipo_pedido=1 and l.id=? order by id desc";
		try{
			$comando=parent::getInstance()->getDb()->prepare($consulta);
			$comando->execute(array($id_lugar));
			$row=$comando->fetchAll();
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

		public static function  get_delivery_conductor_en_camino($id_lugar)
	{
		$consulta="SELECT p.id,p.id_conductor,p.fecha_pedido,p.latitud,p.longitud,c.nombre,concat(c.paterno,' ',c.materno) as 'apellido',c.celular,v.marca,v.placa,p.direccion,p.estado,p.estado_pedido,p.detalle_cancelo_usuario,p.direccion_inicio as 'detalle',p.monto_total,p.clase_vehiculo,p.calificacion_conductor,p.calificacion_vehiculo,concat(u.nombre,' ',u.apellido) as 'pasajero',l.nombre as 'razon_social',p.monto_pedido from pedido p,conductor c,vehiculo v,usuario u,lugar l where p.id_lugar=l.id and p.id_usuario=u.id and p.id_conductor=c.ci and p.id_vehiculo=v.placa and p.id_lugar=? and p.estado_pedido=12 ORDER by p.id desc limit 20";
		try{
			$comando=parent::getInstance()->getDb()->prepare($consulta);
			$comando->execute(array($id_lugar));
			$row=$comando->fetchAll();
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

		public static function  get_delivery_conductor_en_proceso($id_lugar)
	{
		$consulta="SELECT p.id,p.id_conductor,p.fecha_pedido,p.latitud,p.longitud,c.nombre,concat(c.paterno,' ',c.materno) as 'apellido',c.celular,v.marca,v.placa,p.direccion,p.estado,p.estado_pedido,p.detalle_cancelo_usuario,p.direccion_inicio as 'detalle',p.monto_total,p.clase_vehiculo,p.calificacion_conductor,p.calificacion_vehiculo,concat(u.nombre,' ',u.apellido) as 'pasajero',l.nombre as 'razon_social',p.monto_pedido from pedido p,conductor c,vehiculo v,usuario u,lugar l where p.id_lugar=l.id and p.id_usuario=u.id and p.id_conductor=c.ci and p.id_vehiculo=v.placa and p.id_lugar=? and p.estado_pedido=13 ORDER by p.id desc limit 20";
		try{
			$comando=parent::getInstance()->getDb()->prepare($consulta);
			$comando->execute(array($id_lugar));
			$row=$comando->fetchAll();
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

		public static function get_delivery_completados($id_lugar)
	{
		$consulta="SELECT p.id,p.id_conductor,p.fecha_pedido,p.latitud,p.longitud,c.nombre,concat(c.paterno,' ',c.materno) as 'apellido',c.celular,v.marca,v.placa,p.direccion,p.estado,p.estado_pedido,p.detalle_cancelo_usuario,p.direccion_inicio as 'detalle',p.monto_total,p.clase_vehiculo,p.calificacion_conductor,p.calificacion_vehiculo,concat(u.nombre,' ',u.apellido) as 'pasajero',l.nombre as 'razon_social',p.monto_pedido from pedido p,conductor c,vehiculo v,usuario u,lugar l where p.id_lugar=l.id and p.id_usuario=u.id and p.id_conductor=c.ci and p.id_vehiculo=v.placa and p.id_lugar=? and p.estado_pedido=15 ORDER by p.id desc limit 20";
		try{
				$comando=parent::getInstance()->getDb()->prepare($consulta);
				$comando->execute(array($id_lugar));
				$row=$comando->fetchAll();
				if($row)
					return $row;
				 else
					return -1;
			}catch(PDOException $e)
			{
			     return -1;
			}
	}

	public static function get_todos_reservas($id_conductor)
	{
		$consulta=" (SELECT * from pedido where tipo_reserva_auxiliar=1  and fecha_reserva>now() and estado_reserva=0) union (SELECT * from pedido where tipo_reserva=1 and estado_reserva=1 and id_conductor=?) order by id desc";
		try{
			$comando=parent::getInstance()->getDb()->prepare($consulta);
			$comando->execute(array($id_conductor));
			$row=$comando->fetchAll();
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

		public static function get_todos_delvery($id_conductor)
	{
		$consulta=" (SELECT * from pedido where tipo_pedido_auxiliar=1  and fecha_pedido>now() and estado_pedido=0) union (SELECT * from pedido where tipo_pedido=1 and estado_pedido=1 and id_conductor=?) order by id desc";
		try{
			$comando=parent::getInstance()->getDb()->prepare($consulta);
			$comando->execute(array($id_conductor));
			$row=$comando->fetchAll();
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



public static function get_empresa_por_id($id_empresa)
	{
		$consulta="SELECT razon_social,direccion,whatsapp from empresa where id=? limit 1 ";
		try{
			$comando=parent::getInstance()->getDb()->prepare($consulta);
			$comando->execute(array($id_empresa));
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

public static function get_telefono_empresa_por_id($id_empresa)
	{
		$consulta="SELECT numero from telefono where id_empresa=? ";
		try{
			$comando=parent::getInstance()->getDb()->prepare($consulta);
			$comando->execute(array($id_empresa));
			$row=$comando->fetchAll();
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


	public static function get_direccion_por_latitud_longitud($latitud,$longitud)
	  {
	    $consulta="SELECT * from direccion where latitud= ? and longitud=?";
	    try{
	       $comando=parent::getInstance()->getDb()->prepare($consulta);
	      $comando->execute(array($latitud,$longitud));
	      $row = $comando->fetch(PDO::FETCH_ASSOC);
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


   public  static function set_direccion($detalle,$latitud,$longitud,$id_empresa,$id_usuario)
  {

   try{

    if($id_empresa!="")
    {
      $consulta="INSERT INTO direccion (detalle,latitud,longitud,id_empresa,id_usuario) values(?,?,?,?,?)";
      $comando=parent::getInstance()->getDb()->prepare($consulta);
      $comando->execute(array($detalle,$latitud,$longitud,$id_empresa,$id_usuario));
    }
    else
    {
      $consulta="INSERT INTO direccion (detalle,latitud,longitud,id_usuario) values(?,?,?,?)";
      $comando=parent::getInstance()->getDb()->prepare($consulta);
      $comando->execute(array($detalle,$latitud,$longitud,$id_usuario));

    }
 		return true;
		} catch (PDOException $e) {
		   return false;
		}
  }



	public static function pedido_en_curso($id_conductor,$id_vehiculo)
	{
		$consulta="SELECT p.*, concat(nombre,' ',apellido)as 'nombre_usuario',u.celular from pedido p,usuario u where p.id_usuario=u.id and p.id_conductor=? and p.id_vehiculo=? and p.id_usuario=u.id  and p.estado<=1 limit 1";
			try{
				$comando=parent::getInstance()->getDb()->prepare($consulta);
				$comando->execute(array($id_conductor,$id_vehiculo));
				$row=$comando->fetch(PDO::FETCH_ASSOC);
				if($row)
					return $row;
				 else
					return "-1";
			}catch(PDOException $e)
			{
			     return "-1";
			}
	}

	public static function get_id_carrera($id_pedido,$id_conductor,$id_vehiculo)
	{
		$consulta="SELECT max(id) as id_carrera from carrera where id_pedido=? and id_conductor=? and id_vehiculo=? limit 1";
			try{
				$comando=parent::getInstance()->getDb()->prepare($consulta);
				$comando->execute(array($id_pedido,$id_conductor,$id_vehiculo));
				$row=$comando->fetch(PDO::FETCH_ASSOC);
				if($row)
					return $row['id_carrera'];
				 else
					return "-1";
			}catch(PDOException $e)
			{
				
			     return "-1";
			}
	}
	
	public static function get_estado_pedido($id_pedido)
	{
		$consulta="SELECT estado  from pedido where id=? and tipo_reserva=0 and tipo_pedido=0";
			try{
				$comando=parent::getInstance()->getDb()->prepare($consulta);
				$comando->execute(array($id_pedido));
				$row=$comando->fetch(PDO::FETCH_ASSOC);
				if($row)
					return $row['estado'];
				 else
					return -1;
			}catch(PDOException $e)
			{
			     return -1;
			}
	}



		public static function get_estado_pedido_reserva($id_pedido)
	{
		$consulta="SELECT estado_reserva  from pedido where id=? and tipo_reserva=1";
			try{
				$comando=parent::getInstance()->getDb()->prepare($consulta);
				$comando->execute(array($id_pedido));
				$row=$comando->fetch(PDO::FETCH_ASSOC);
				if($row)
					return $row['estado_reserva'];
				 else
					return -1;
			}catch(PDOException $e)
			{
			     return -1;
			}
	}

		public static function get_estado_pedido_delivery($id_pedido)
	{
		$consulta="SELECT estado_pedido  from pedido where id=? and tipo_pedido=1";
			try{
				$comando=parent::getInstance()->getDb()->prepare($consulta);
				$comando->execute(array($id_pedido));
				$row=$comando->fetch(PDO::FETCH_ASSOC);
				if($row)
					return $row['estado_pedido'];
				 else
					return -1;
			}catch(PDOException $e)
			{
			     return -1;
			}
	}


	public static function get_id_empresa_por_id_usuario($id_usuario)
	{
		$consulta="(SELECT id_empresa  from usuario  where id=? and id_empresa<>0) union (SELECT id as 'id_empresa' from empresa where tipo like '%CLIENTE%' and id_administrador=?)";
			try{
				$comando=parent::getInstance()->getDb()->prepare($consulta);
				$comando->execute(array($id_usuario,$id_usuario));
				$row=$comando->fetch(PDO::FETCH_ASSOC);
				if($row)
					return $row['id_empresa'];
				 else
					return 0;
			}catch(PDOException $e)
			{
			     return 0;
			}
	}


		public static  function pedir_taxi($id_usuario,$latitud,$longitud,$indicacion,$nombre,$numero_casa,$imei,$clase_vehiculo,$tipo_pedido_empresa,$direccion)
	{
  		$resultado=-1;
  	
	  	$id_pedido=self::id_ultimo_pedido($id_usuario);//ultimo pedido sin aceptar
	  	//$id_pedido_en_proceso=self::id_ultimo_pedido_en_proceso($id_usuario);

	  	if($id_pedido=='-1')
	  	{
	  		$pedido=Pedido::pedido_en_camino($id_usuario);
				if($pedido!="-1")
				{
					print json_encode(array('suceso' => '1','mensaje' => 'Ya tiene un pedido en camino.','id_pedido'=>$pedido));
					$sw=true;
				}
			$consulta="INSERT into pedido (id_usuario,latitud,longitud,direccion,estado,numero_casa,imei,clase_vehiculo,tipo_pedido_empresa,direccion_inicio) values(?,?,?,?,0,?,?,?,?,?)";
			try{
  			$comando=parent::getInstance()->getDb()->prepare($consulta);
				$comando->execute(array($id_usuario,$latitud,$longitud,$indicacion,$numero_casa,$imei,$clase_vehiculo,$tipo_pedido_empresa,$direccion));
				$lastId = parent::getInstance()->getDb()->lastInsertId();
				$not=self::enviar_notificacion_pedido_taxi($id_usuario,$latitud,$longitud,$lastId,$nombre,$indicacion,$clase_vehiculo,$tipo_pedido_empresa);
          		$resultado=$lastId;

          		if($not===false)
          		{
					$resultado='-1';
          		}
	  		}catch(PDOException $e)
	  		{
	  			
	  			$resultado='-1';
	  		}
	  	}
	  	else
	  	{
			$consulta="UPDATE pedido set id_usuario=?,latitud=?,longitud=?,direccion=?,direccion_inicio=?,fecha_pedido=now(), estado=0,imei=?,clase_vehiculo=?,tipo_pedido_empresa=?
			 where id=?";
			try{
				
  				$comando=parent::getInstance()->getDb()->prepare($consulta);

				$comando->execute(array($id_usuario,$latitud,$longitud,$indicacion,$direccion,$imei,$clase_vehiculo,$tipo_pedido_empresa,$id_pedido));
				
          		
				$not=self::enviar_notificacion_pedido_taxi($id_usuario,$latitud,$longitud,$id_pedido,$nombre,$indicacion,$clase_vehiculo,$tipo_pedido_empresa);


          		$resultado=$id_pedido;

          		if($not===false)
          		{
					$resultado='-1';
          		}


	  		}catch(PDOException $e)
	  		{

	  			$resultado='-1';
	  		}	
	  	}

	  	return $resultado;
	}

public static  function buscar_conductor_delivery($id_pedido)
	{
  		$resultado=-1;
  	
	  	 $pedido=self::get_delivery_lugar_por_id($id_pedido);
		$id_usuario=$pedido['id_usuario'];
		$latitud=$pedido['latitud'];
		$longitud=$pedido['longitud'];
		$indicacion=$pedido['direccion'];
		$nombre=$pedido['nombre'];
		$numero_casa="";
		$imei=$pedido['imei'];
		$clase_vehiculo=$pedido['clase_vehiculo'];
		$tipo_pedido_empresa=$pedido['tipo_pedido_empresa'];
		$tipo_pedido=$pedido['tipo_pedido'];
		$direccion=$pedido['direccion_inicio'];
			 
			try{
  			 
				$not=self::enviar_notificacion_delivery_taxi($id_usuario,$latitud,$longitud,$id_pedido,$nombre,$indicacion,$clase_vehiculo,$tipo_pedido_empresa,$tipo_pedido);
          		$resultado=1;

          		if($not===false)
          		{
					$resultado='-1';
          		}
	  		}catch(PDOException $e)
	  		{
	  			echo $e;
	  			
	  			$resultado='-1';
	  		}
	  	 
	  	return $resultado;
	}





   public static  function pedir_taxi_corporativo($id_usuario,$latitud,$longitud,$indicacion,$nombre,$numero_casa,$imei,$clase_vehiculo,$tipo_pedido_empresa,$id_empresa,$direccion)
	{

		//SOLICITUD CORPORATIVA CON ID EMPRESA
  		$resultado=-1;
  	
	  	$id_pedido=self::id_ultimo_pedido($id_usuario);//ultimo pedido sin aceptar
	  	//$id_pedido_en_proceso=self::id_ultimo_pedido_en_proceso($id_usuario);

	  	if($id_pedido=='-1')
	  	{
			$consulta="INSERT into pedido (id_usuario,latitud,longitud,direccion,estado,numero_casa,imei,clase_vehiculo,tipo_pedido_empresa,id_empresa_cliente,direccion_inicio) values(?,?,?,?,0,?,?,?,?,?,?)";
			try{
  			$comando=parent::getInstance()->getDb()->prepare($consulta);
				$comando->execute(array($id_usuario,$latitud,$longitud,$indicacion,$numero_casa,$imei,$clase_vehiculo,$tipo_pedido_empresa,$id_empresa,$direccion));
				$lastId = parent::getInstance()->getDb()->lastInsertId();
				$not=self::enviar_notificacion_pedido_taxi($id_usuario,$latitud,$longitud,$lastId,$nombre,$indicacion,$clase_vehiculo,$tipo_pedido_empresa);
          		$resultado=$lastId;

          		if($not===false)
          		{
					$resultado='-1';
          		}
	  		}catch(PDOException $e)
	  		{
	  			
	  			$resultado='-1';
	  		}
	  	}
	  	else
	  	{
			$consulta="UPDATE pedido set id_usuario=?,latitud=?,longitud=?,direccion=?,direccion_inicio=?,fecha_pedido=now(), estado=0,imei=?,clase_vehiculo=?,tipo_pedido_empresa=?
			 where id=?";
			try{
				
  				$comando=parent::getInstance()->getDb()->prepare($consulta);

				$comando->execute(array($id_usuario,$latitud,$longitud,$indicacion,$direccion,$imei,$clase_vehiculo,$tipo_pedido_empresa,$id_pedido));
				
          		
				$not=self::enviar_notificacion_pedido_taxi($id_usuario,$latitud,$longitud,$id_pedido,$nombre,$indicacion,$clase_vehiculo,$tipo_pedido_empresa);


          		$resultado=$id_pedido;

          		if($not===false)
          		{
					$resultado='-1';
          		}


	  		}catch(PDOException $e)
	  		{

	  			$resultado='-1';
	  		}	
	  	}

	  	return $resultado;
	}

  public static  function reservar_movil($id_usuario,$latitud,$longitud,$indicacion,$nombre,$numero_casa,$imei,$clase_vehiculo,$tipo_pedido_empresa,$fecha_reserva)
	{
  		$resultado=-1;
	  	//$id_pedido_en_proceso=self::id_ultimo_pedido_en_proceso($id_usuario);
			$consulta="INSERT into pedido (id_usuario,latitud,longitud,direccion,estado,numero_casa,imei,clase_vehiculo,tipo_pedido_empresa,fecha_reserva,tipo_reserva,tipo_reserva_auxiliar) values(?,?,?,?,0,?,?,?,?,?,1,1)";
			try{
  			$comando=parent::getInstance()->getDb()->prepare($consulta);
				$comando->execute(array($id_usuario,$latitud,$longitud,$indicacion,$numero_casa,$imei,$clase_vehiculo,$tipo_pedido_empresa,$fecha_reserva));
				$lastId = parent::getInstance()->getDb()->lastInsertId();
				$not=self::enviar_notificacion_reservar_movil($id_usuario,$latitud,$longitud,$lastId,$nombre,$indicacion,$clase_vehiculo,$tipo_pedido_empresa,$fecha_reserva);
          		$resultado=$lastId;

          		if($not===false)
          		{
					$resultado='-1';
          		}
	  		}catch(PDOException $e)
	  		{
	  			
	  			$resultado='-1';
	  		}
	  	return $resultado;
	}

	 public static  function reservar_movil_corporativo($id_usuario,$latitud,$longitud,$indicacion,$nombre,$numero_casa,$imei,$clase_vehiculo,$tipo_pedido_empresa,$id_empresa,$fecha_reserva)
	{

		//SOLICITUD CORPORATIVA CON ID EMPRESA
  		$resultado=-1;
  	 
			$consulta="INSERT into pedido (id_usuario,latitud,longitud,direccion,estado,numero_casa,imei,clase_vehiculo,tipo_pedido_empresa,id_empresa_cliente,fecha_reserva,tipo_reserva,tipo_reserva_auxiliar) values(?,?,?,?,0,?,?,?,?,?,?,1,1)";
			try{
  			$comando=parent::getInstance()->getDb()->prepare($consulta);
				$comando->execute(array($id_usuario,$latitud,$longitud,$indicacion,$numero_casa,$imei,$clase_vehiculo,$tipo_pedido_empresa,$id_empresa,$fecha_reserva));
				$lastId = parent::getInstance()->getDb()->lastInsertId();
				$not=self::enviar_notificacion_reservar_movil($id_usuario,$latitud,$longitud,$lastId,$nombre,$indicacion,$clase_vehiculo,$tipo_pedido_empresa,$fecha_reserva);
          		$resultado=$lastId;

          		if($not===false)
          		{
					$resultado='-1';
          		}
	  		}catch(PDOException $e)
	  		{
	  			
	  			$resultado='-1';
	  		}
	   

	  	return $resultado;
	}



		public static function enviar_notificacion_pedido_taxi($id_usuario,$latitud,$longitud,$id_pedido,$nombre,$indicacion,$clase_vehiculo,$tipo_pedido_empresa)
	  {

	  	try{
	  		$p='Pedido de un Movil';
	  		if($clase_vehiculo>=7 && $clase_vehiculo<=8){
	  			$p='Pedido de una Moto';
	  		}
		   $push = new Push('Pedido',$p,null,"taxi",$id_pedido,$nombre,$latitud,$longitud,"2");
	     // obteniendo el empuje del objeto push
			 $push->setIndicacion($indicacion);
			 $push->setClase_vehiculo($clase_vehiculo);
        	 $push->setTipo_pedido_empresa($tipo_pedido_empresa);
			 $mPushNotification = $push->getPush(); 
			 // obtener el token del objeto de base de datos

			 $devicetoken = self::get_token_taxi($latitud,$longitud,300,$clase_vehiculo);		 
			 
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

	public static function enviar_notificacion_delivery_taxi($id_usuario,$latitud,$longitud,$id_pedido,$nombre,$indicacion,$clase_vehiculo,$tipo_pedido_empresa,$tipo_pedido)
	  {

	  	try{
	  		$p='Solicitud para Delivery';
	  		if($clase_vehiculo>=7 && $clase_vehiculo<=8){
	  			$p='Pedido de una Moto';
	  		}
		   $push = new Push('Pedido',$p,null,"taxi",$id_pedido,$nombre,$latitud,$longitud,"2");
	     // obteniendo el empuje del objeto push
			 $push->setIndicacion($indicacion);
			 $push->setClase_vehiculo($clase_vehiculo);
        	 $push->setTipo_pedido_empresa($tipo_pedido_empresa);
        	 $push->setTipo_pedido($tipo_pedido);
			 $mPushNotification = $push->getPush(); 
			 // obtener el token del objeto de base de datos

			 $devicetoken = self::get_token_taxi($latitud,$longitud,5000,$clase_vehiculo);		 
			 
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
			echo $e;
	return false;
		}
    }





    public static function enviar_notificacion_reservar_movil($id_usuario,$latitud,$longitud,$id_pedido,$nombre,$indicacion,$clase_vehiculo,$tipo_pedido_empresa,$fecha_reserva)
	  {

	  	try{
		   $push = new Push('Reserva de movil','Hay un reserva para las '.$fecha_reserva,null,"taxi",$id_pedido,$nombre,$latitud,$longitud,"17");
	     // obteniendo el empuje del objeto push
			 $push->setIndicacion($indicacion);
			 $push->setClase_vehiculo($clase_vehiculo);
        	 $push->setTipo_pedido_empresa($tipo_pedido_empresa);
			 $mPushNotification = $push->getPush(); 
			 // obtener el token del objeto de base de datos

			 $devicetoken = self::get_token_taxi($latitud,$longitud,100000,$clase_vehiculo);		 
			 
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
public static function get_token_taxi($latitud,$longitud,$diametro,$clase_vehiculo)
   {  
   	switch ($clase_vehiculo) {
   		case 1:
   		//CUALQUIER MOVIL
   			$consulta="SELECT token,distancia_entre_dos_puntos(".$latitud.",".$longitud.",c.latitud_asignacion,c.longitud_asignacion) as distancia 
	   		FROM conductor c,vehiculo v where c.id_vehiculo=v.placa and c.estado=1 and c.login=1 and v.movil=1 and v.moto=0
	   		and distancia_entre_dos_puntos(".$latitud.",".$longitud.",c.latitud_asignacion,c.longitud_asignacion)<= ?  
	   		and c.ci not in (select id_conductor from pedido where estado<=1 and id_conductor is not null)  
	   		order by distancia asc ";
   			break;
   		case 2:
   		//MOVILES DE LUJO
   			$consulta="SELECT token,distancia_entre_dos_puntos(".$latitud.",".$longitud.",c.latitud_asignacion,c.longitud_asignacion) as distancia 
	   		FROM conductor c,vehiculo v where c.id_vehiculo=v.placa and c.estado=1 and c.login=1 and v.lujo=1 and moto=0
	   		and distancia_entre_dos_puntos(".$latitud.",".$longitud.",c.latitud_asignacion,c.longitud_asignacion)<= ?  
	   		and c.ci not in (select id_conductor from pedido where estado<=1 and id_conductor is not null)  
	   		order by distancia asc ";
   			break;
   		case 3:
   		//MOVILES CON AIRE ACONDICIONADO
   			$consulta="SELECT token,distancia_entre_dos_puntos(".$latitud.",".$longitud.",c.latitud_asignacion,c.longitud_asignacion) as distancia 
	   		FROM conductor c,vehiculo v where c.id_vehiculo=v.placa and c.estado=1 and c.login=1 and v.aire=1 and moto=0
	   		and distancia_entre_dos_puntos(".$latitud.",".$longitud.",c.latitud_asignacion,c.longitud_asignacion)<= ?  
	   		and c.ci not in (select id_conductor from pedido where estado<=1 and id_conductor is not null)  
	   		order by distancia asc ";
   			break;
   		case 4:
   		//MOVILES CON MALETERO
   			$consulta="SELECT token,distancia_entre_dos_puntos(".$latitud.",".$longitud.",c.latitud_asignacion,c.longitud_asignacion) as distancia 
	   		FROM conductor c,vehiculo v where c.id_vehiculo=v.placa and c.estado=1 and c.login=1 and v.maletero_libre=1 and moto=0
	   		and distancia_entre_dos_puntos(".$latitud.",".$longitud.",c.latitud_asignacion,c.longitud_asignacion)<= ?  
	   		and c.ci not in (select id_conductor from pedido where estado<=1 and id_conductor is not null)  
	   		order by distancia asc ";
   			break;
   		case 5:
   		//CUALQUIER MOVIL 'PEDIDO DE ENCOMIENDA'.
   			$consulta="SELECT token,distancia_entre_dos_puntos(".$latitud.",".$longitud.",c.latitud_asignacion,c.longitud_asignacion) as distancia 
	   		FROM conductor c,vehiculo v where c.id_vehiculo=v.placa and c.estado=1 and c.login=1 and v.movil=1 and moto=0
	   		and distancia_entre_dos_puntos(".$latitud.",".$longitud.",c.latitud_asignacion,c.longitud_asignacion)<= ?  
	   		and c.ci not in (select id_conductor from pedido where estado<=1 and id_conductor is not null)  
	   		order by distancia asc ";
   			break;	
   		case 6:
   		//CUALQUIER MOVIL 'RESERVAR UN MOVIL PARA OTRA FECHA.
   			$consulta="SELECT token,distancia_entre_dos_puntos(".$latitud.",".$longitud.",c.latitud_asignacion,c.longitud_asignacion) as distancia 
	   		FROM conductor c,vehiculo v where c.id_vehiculo=v.placa and c.estado=1 and c.login=1 and v.movil=1 and moto=0
	   		and distancia_entre_dos_puntos(".$latitud.",".$longitud.",c.latitud_asignacion,c.longitud_asignacion)<= ?  
	   		and c.ci not in (select id_conductor from pedido where estado<=1 and id_conductor is not null)  
	   		order by distancia asc ";
   			break;	
   		case 7:
   		//CUALQUIER MOTO
   			$consulta="SELECT token,distancia_entre_dos_puntos(".$latitud.",".$longitud.",c.latitud_asignacion,c.longitud_asignacion) as distancia 
	   		FROM conductor c,vehiculo v where c.id_vehiculo=v.placa and c.estado=1 and c.login=1 and v.movil=1 and moto=1
	   		and distancia_entre_dos_puntos(".$latitud.",".$longitud.",c.latitud_asignacion,c.longitud_asignacion)<= ?  
	   		and c.ci not in (select id_conductor from pedido where estado<=1 and id_conductor is not null)  
	   		order by distancia asc ";
   			break;	
   		 case 8:
   		//CUALQUIER MOTO
   			$consulta="SELECT token,distancia_entre_dos_puntos(".$latitud.",".$longitud.",c.latitud_asignacion,c.longitud_asignacion) as distancia 
	   		FROM conductor c,vehiculo v where c.id_vehiculo=v.placa and c.estado=1 and c.login=1 and v.movil=1 and moto=1
	   		and distancia_entre_dos_puntos(".$latitud.",".$longitud.",c.latitud_asignacion,c.longitud_asignacion)<= ?  
	   		and c.ci not in (select id_conductor from pedido where estado<=1 and id_conductor is not null)  
	   		order by distancia asc ";
   			break;	

   		case 11:
   		//MOVILES CON PARRILLA
   			$consulta="SELECT token,distancia_entre_dos_puntos(".$latitud.",".$longitud.",c.latitud_asignacion,c.longitud_asignacion) as distancia 
	   		FROM conductor c,vehiculo v where c.id_vehiculo=v.placa and c.estado=1 and c.login=1 and v.parrilla=1 and moto=0
	   		and distancia_entre_dos_puntos(".$latitud.",".$longitud.",c.latitud_asignacion,c.longitud_asignacion)<= ?  
	   		and c.ci not in (select id_conductor from pedido where estado<=1 and id_conductor is not null)  
	   		order by distancia asc ";
   			break;	
   		case 15:
   		//MOVILES CAMIONETA
   			$consulta="SELECT token,distancia_entre_dos_puntos(".$latitud.",".$longitud.",c.latitud_asignacion,c.longitud_asignacion) as distancia 
	   		FROM conductor c,vehiculo v where c.id_vehiculo=v.placa and c.estado=1 and c.login=1 and v.camioneta=1 and moto=0
	   		and distancia_entre_dos_puntos(".$latitud.",".$longitud.",c.latitud_asignacion,c.longitud_asignacion)<= ?  
	   		and c.ci not in (select id_conductor from pedido where estado<=1 and id_conductor is not null)  
	   		order by distancia asc ";
   			break;							
   	}

   			
   		$query = parent::getInstance()->getDb()->prepare($consulta);
        $query->execute(array($diametro)); 
         $tokens = array(); 

        while($row=$query->fetch(PDO::FETCH_OBJ)) {
		 array_push($tokens, $row->token);
		    }
   
        return $tokens; 
   }

   	public  function distancia_pedido_conductor($id_pedido,$id_conductor)
	  {
	  	$distancia=0;
	  	$consulta="SELECT distancia_entre_dos_puntos(p.latitud,p.longitud,c.latitud_asignacion,c.longitud_asignacion) as distancia   from pedido p,conductor c WHERE p.id=? and c.ci=? ";
			try{
				$comando=parent::getInstance()->getDb()->prepare($consulta);
				$comando->execute(array($id_pedido,$id_conductor));
				$row=$comando->fetch(PDO::FETCH_ASSOC);
				if($row)
				{
					$sw_reg=self::registrar_punto_inicio($id_pedido,$id_conductor);
					$distancia=$row["distancia"];
				}
			}catch(PDOException $e)
			{
			     $distancia=0;
			}

			return $distancia;
	}


   public function registrar_punto_inicio($id_pedido,$id_conductor)
	{ $resultado=false;
		 
		try{
			$registrar_punto="UPDATE pedido p,conductor c set p.latitud_inicio=c.latitud_asignacion , p.longitud_inicio=c.longitud_asignacion where p.id= ? and  c.ci=? ";
			$comando=parent::getInstance()->getDb()->prepare($registrar_punto);
			$comando->execute(array($id_pedido,$id_conductor));
			$resultado=true;
            }
            catch(PDOException $e)
            {
		$resultado=false;
            }		
            return $resultado;
     }

  public function registrar_punto_cancelacion($id_pedido,$id_conductor)
	{ $resultado=false;
		 
		try{
			$registrar_punto="UPDATE pedido p,conductor c set p.latitud_cancelado=c.latitud_asignacion , p.longitud_cancelado=c.longitud_asignacion where p.id= ? and  c.ci=? ";
			$comando=parent::getInstance()->getDb()->prepare($registrar_punto);
			$comando->execute(array($id_pedido,$id_conductor));
			$resultado=true;
            }
            catch(PDOException $e)
            {
		$resultado=false;
            }		
            return $resultado;
     }

     public function registrar_punto_cancelacion_usuario($id_pedido)
	{ $resultado=false;
		 
		 //registra la ubicacion del conductor cuando el pasajero cancelo la solicitud.
		$asignado=self::id_taxi_del_pedido($id_pedido);
			if($asignado!=-1)
			{
			   $id_conductor=$asignado['id_conductor'];
					try{
						$registrar_punto="UPDATE pedido p,conductor c set p.latitud_cancelado=c.latitud_asignacion , p.longitud_cancelado=c.longitud_asignacion where p.id= ? and  c.ci=? ";
						$comando=parent::getInstance()->getDb()->prepare($registrar_punto);
						$comando->execute(array($id_pedido,$id_conductor));
						$resultado=true;
			            }
			            catch(PDOException $e)
			            {
							$resultado=false;
			            }	
			  
			}
 
			
            return $resultado;
     }


	public static function enviar_notificacion_pedido_taxi_rango($id_pedido,$diametro_inicio,$diametro_fin)
	  {
	  	$consulta="SELECT concat(u.nombre,' ',u.apellido) as 'nombre',p.latitud,p.longitud,p.direccion,p.clase_vehiculo,p.tipo_pedido_empresa  from pedido p,usuario u where u.id=p.id_usuario and p.id=?";
			try{
				$comando=parent::getInstance()->getDb()->prepare($consulta);
				$comando->execute(array($id_pedido));
				$row=$comando->fetch(PDO::FETCH_ASSOC);
				if($row)
					{
						$nombre=$row['nombre'];
						$latitud=$row['latitud'];
						$longitud=$row['longitud'];
						$indicacion=$row['direccion'];
						$clase_vehiculo=$row['clase_vehiculo'];
						$tipo_pedido_empresa=$row['tipo_pedido_empresa'];
						try{
							$p='Pedido de Movil';
							if($clase_vehiculo>=7 && $clase_vehiculo<=8 ){
								$p='Pedido de una Moto';
							}
								   $push = new Push('Pedido',$p,null,"taxi",$id_pedido,$nombre,$latitud,$longitud,"2");
							     // obteniendo el empuje del objeto push
									$push->setIndicacion($indicacion);
									$push->setClase_vehiculo($clase_vehiculo);
									$push->setTipo_pedido_empresa($tipo_pedido_empresa);
									 $mPushNotification = $push->getPush(); 
									 
									 // obtener el token del objeto de base de datos

									 $devicetoken = self::get_token_taxi_rango($latitud,$longitud,$diametro_inicio,$diametro_fin,$clase_vehiculo);		 

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
				 else
				 	return false;
					
			}catch(PDOException $e)
			{
			     return false;
			}

	  	
    }
public static function get_token_taxi_rango($latitud,$longitud,$diametro_inicio,$diametro_fin,$clase_vehiculo)
   {  

   		  	switch ($clase_vehiculo) {
   		case 1:
   		//CUALQUIER MOVIL
   			$consulta="SELECT token,distancia_entre_dos_puntos(".$latitud.",".$longitud.",c.latitud_asignacion,c.longitud_asignacion) as distancia 
	   		FROM conductor c,vehiculo v where c.id_vehiculo=v.placa and c.estado=1 and c.login=1 and v.movil=1
	   		and distancia_entre_dos_puntos(".$latitud.",".$longitud.",c.latitud_asignacion,c.longitud_asignacion) >? 
	   		and distancia_entre_dos_puntos(".$latitud.",".$longitud.",c.latitud_asignacion,c.longitud_asignacion)<= ? 
	   		and c.ci not in (select id_conductor from pedido where estado<=1 and id_conductor is not null) 
	   		order by distancia asc ";
   			break;
   		case 2:
   		//MOVILES DE LUJO
   			$consulta="SELECT token,distancia_entre_dos_puntos(".$latitud.",".$longitud.",c.latitud_asignacion,c.longitud_asignacion) as distancia 
	   		FROM conductor c,vehiculo v where c.id_vehiculo=v.placa and c.estado=1 and c.login=1 and v.lujo=1
	   		and distancia_entre_dos_puntos(".$latitud.",".$longitud.",c.latitud_asignacion,c.longitud_asignacion) >? 
	   		and distancia_entre_dos_puntos(".$latitud.",".$longitud.",c.latitud_asignacion,c.longitud_asignacion)<= ? 
	   		and c.ci not in (select id_conductor from pedido where estado<=1 and id_conductor is not null) 
	   		order by distancia asc ";
   			break;
   		case 3:
   		//MOVILES CON AIRE ACONDICIONADO
   			$consulta="SELECT token,distancia_entre_dos_puntos(".$latitud.",".$longitud.",c.latitud_asignacion,c.longitud_asignacion) as distancia 
	   		FROM conductor c,vehiculo v where c.id_vehiculo=v.placa and c.estado=1 and c.login=1 and v.aire=1
	   		and distancia_entre_dos_puntos(".$latitud.",".$longitud.",c.latitud_asignacion,c.longitud_asignacion) >? 
	   		and distancia_entre_dos_puntos(".$latitud.",".$longitud.",c.latitud_asignacion,c.longitud_asignacion)<= ? 
	   		and c.ci not in (select id_conductor from pedido where estado<=1 and id_conductor is not null) 
	   		order by distancia asc ";
   			break;
   		case 4:
   		//MOVILES CON MALETERO
   			$consulta="SELECT token,distancia_entre_dos_puntos(".$latitud.",".$longitud.",c.latitud_asignacion,c.longitud_asignacion) as distancia 
	   		FROM conductor c,vehiculo v where c.id_vehiculo=v.placa and c.estado=1 and c.login=1 and v.maletero_libre=1
	   		and distancia_entre_dos_puntos(".$latitud.",".$longitud.",c.latitud_asignacion,c.longitud_asignacion) >? 
	   		and distancia_entre_dos_puntos(".$latitud.",".$longitud.",c.latitud_asignacion,c.longitud_asignacion)<= ? 
	   		and c.ci not in (select id_conductor from pedido where estado<=1 and id_conductor is not null) 
	   		order by distancia asc ";
	   		break;
	   	case 5:
   		//CUALQUIER MOVIL 'PEDIDO DE UNA ENCOMIENDA'
   			$consulta="SELECT token,distancia_entre_dos_puntos(".$latitud.",".$longitud.",c.latitud_asignacion,c.longitud_asignacion) as distancia 
	   		FROM conductor c,vehiculo v where c.id_vehiculo=v.placa and c.estado=1 and c.login=1 and v.movil=1
	   		and distancia_entre_dos_puntos(".$latitud.",".$longitud.",c.latitud_asignacion,c.longitud_asignacion) >? 
	   		and distancia_entre_dos_puntos(".$latitud.",".$longitud.",c.latitud_asignacion,c.longitud_asignacion)<= ? 
	   		and c.ci not in (select id_conductor from pedido where estado<=1 and id_conductor is not null) 
	   		order by distancia asc ";
   			break;
   		case 6:
   		//CUALQUIER MOVIL 'RESERVAR UN MOVIL PARA UNA FECHA'
   			$consulta="SELECT token,distancia_entre_dos_puntos(".$latitud.",".$longitud.",c.latitud_asignacion,c.longitud_asignacion) as distancia 
	   		FROM conductor c,vehiculo v where c.id_vehiculo=v.placa and c.estado=1 and c.login=1 and v.movil=1
	   		and distancia_entre_dos_puntos(".$latitud.",".$longitud.",c.latitud_asignacion,c.longitud_asignacion) >? 
	   		and distancia_entre_dos_puntos(".$latitud.",".$longitud.",c.latitud_asignacion,c.longitud_asignacion)<= ? 
	   		and c.ci not in (select id_conductor from pedido where estado<=1 and id_conductor is not null) 
	   		order by distancia asc ";
   			break;	
   		case 7:
   		//CUALQUIER MOTO
   			$consulta="SELECT token,distancia_entre_dos_puntos(".$latitud.",".$longitud.",c.latitud_asignacion,c.longitud_asignacion) as distancia 
	   		FROM conductor c,vehiculo v where c.id_vehiculo=v.placa and c.estado=1 and c.login=1 and v.movil=1 and moto=1
	   		and distancia_entre_dos_puntos(".$latitud.",".$longitud.",c.latitud_asignacion,c.longitud_asignacion) >? 
	   		and distancia_entre_dos_puntos(".$latitud.",".$longitud.",c.latitud_asignacion,c.longitud_asignacion)<= ? 
	   		and c.ci not in (select id_conductor from pedido where estado<=1 and id_conductor is not null) 
	   		order by distancia asc ";
   			break;	
   		 case 8:
   		//CUALQUIER MOTO
   			$consulta="SELECT token,distancia_entre_dos_puntos(".$latitud.",".$longitud.",c.latitud_asignacion,c.longitud_asignacion) as distancia 
	   		FROM conductor c,vehiculo v where c.id_vehiculo=v.placa and c.estado=1 and c.login=1 and v.movil=1 and moto=1
	   		and distancia_entre_dos_puntos(".$latitud.",".$longitud.",c.latitud_asignacion,c.longitud_asignacion) >? 
	   		and distancia_entre_dos_puntos(".$latitud.",".$longitud.",c.latitud_asignacion,c.longitud_asignacion)<= ? 
	   		and c.ci not in (select id_conductor from pedido where estado<=1 and id_conductor is not null) 
	   		order by distancia asc ";
   			break;	 
   		case 11:
   		//MOVILES CON PARRILLA
   			$consulta="SELECT token,distancia_entre_dos_puntos(".$latitud.",".$longitud.",c.latitud_asignacion,c.longitud_asignacion) as distancia 
	   		FROM conductor c,vehiculo v where c.id_vehiculo=v.placa and c.estado=1 and c.login=1 and v.parrilla=1
	   		and distancia_entre_dos_puntos(".$latitud.",".$longitud.",c.latitud_asignacion,c.longitud_asignacion) >? 
	   		and distancia_entre_dos_puntos(".$latitud.",".$longitud.",c.latitud_asignacion,c.longitud_asignacion)<= ? 
	   		and c.ci not in (select id_conductor from pedido where estado<=1 and id_conductor is not null) 
	   		order by distancia asc ";
	   		break;
	   	case 15:
   		//MOVILES CAMIONETA
   			$consulta="SELECT token,distancia_entre_dos_puntos(".$latitud.",".$longitud.",c.latitud_asignacion,c.longitud_asignacion) as distancia 
	   		FROM conductor c,vehiculo v where c.id_vehiculo=v.placa and c.estado=1 and c.login=1 and v.camioneta=1
	   		and distancia_entre_dos_puntos(".$latitud.",".$longitud.",c.latitud_asignacion,c.longitud_asignacion) >? 
	   		and distancia_entre_dos_puntos(".$latitud.",".$longitud.",c.latitud_asignacion,c.longitud_asignacion)<= ? 
	   		and c.ci not in (select id_conductor from pedido where estado<=1 and id_conductor is not null) 
	   		order by distancia asc ";
	   		break;			
   	}
   	
   	$query = parent::getInstance()->getDb()->prepare($consulta);
   	if($diametro_fin==5000)
   	{
	$diametro_fin=$diametro_inicio;
   	}
        $query->execute(array($diametro_inicio,$diametro_fin)); 
         $tokens = array(); 

        while($row=$query->fetch(PDO::FETCH_OBJ)) {
 array_push($tokens, $row->token);
    }
   
        return $tokens; 
   }

   public static function taxi_disponible($latitud,$longitud)
   {  

   	$query = parent::getInstance()->getDb()->prepare("SELECT token,distancia_entre_dos_puntos(?,?,c.latitud_asignacion,c.longitud_asignacion) as distancia 
   		FROM conductor c,vehiculo v where c.id_vehiculo=v.placa and c.estado=1 and c.login=1 
   		and distancia_entre_dos_puntos(?,?,c.latitud_asignacion,c.longitud_asignacion)<= ?  
   		and c.ci not in (select pp.id_conductor from pedido pp where pp.estado<=1 and pp.id_conductor is not null) 
   		order by distancia asc ");
        $query->execute(array($latitud, $longitud,$latitud, $longitud,5000)); 
		$row=$query->fetchAll();
		if($row)
		{
			return true;
		}
		else
			{
				return false;
			}

   }


public static function taxi_disponible_por_clase($latitud,$longitud,$clase_vehiculo)
   {  

	switch ($clase_vehiculo) {
   		case 1:
   		//CUALQUIER MOVIL
   			$consulta="SELECT token,distancia_entre_dos_puntos(".$latitud.",".$longitud.",c.latitud_asignacion,c.longitud_asignacion) as distancia 
	   		FROM conductor c,vehiculo v where c.id_vehiculo=v.placa and c.estado=1 and c.login=1 and v.movil=1 and v.moto=0
	   		and distancia_entre_dos_puntos(".$latitud.",".$longitud.",c.latitud_asignacion,c.longitud_asignacion)<= ?  
	   		and c.ci not in (select id_conductor from pedido where estado<=1 and id_conductor is not null)  
	   		order by distancia asc ";
   			break;
   		case 2:
   		//MOVILES DE LUJO
   			$consulta="SELECT token,distancia_entre_dos_puntos(".$latitud.",".$longitud.",c.latitud_asignacion,c.longitud_asignacion) as distancia 
	   		FROM conductor c,vehiculo v where c.id_vehiculo=v.placa and c.estado=1 and c.login=1 and v.lujo=1 and moto=0
	   		and distancia_entre_dos_puntos(".$latitud.",".$longitud.",c.latitud_asignacion,c.longitud_asignacion)<= ?  
	   		and c.ci not in (select id_conductor from pedido where estado<=1 and id_conductor is not null)  
	   		order by distancia asc ";
   			break;
   		case 3:
   		//MOVILES CON AIRE ACONDICIONADO
   			$consulta="SELECT token,distancia_entre_dos_puntos(".$latitud.",".$longitud.",c.latitud_asignacion,c.longitud_asignacion) as distancia 
	   		FROM conductor c,vehiculo v where c.id_vehiculo=v.placa and c.estado=1 and c.login=1 and v.aire=1 and moto=0
	   		and distancia_entre_dos_puntos(".$latitud.",".$longitud.",c.latitud_asignacion,c.longitud_asignacion)<= ?  
	   		and c.ci not in (select id_conductor from pedido where estado<=1 and id_conductor is not null)  
	   		order by distancia asc ";
   			break;
   		case 4:
   		//MOVILES CON MALETERO
   			$consulta="SELECT token,distancia_entre_dos_puntos(".$latitud.",".$longitud.",c.latitud_asignacion,c.longitud_asignacion) as distancia 
	   		FROM conductor c,vehiculo v where c.id_vehiculo=v.placa and c.estado=1 and c.login=1 and v.maletero_libre=1 and moto=0
	   		and distancia_entre_dos_puntos(".$latitud.",".$longitud.",c.latitud_asignacion,c.longitud_asignacion)<= ?  
	   		and c.ci not in (select id_conductor from pedido where estado<=1 and id_conductor is not null)  
	   		order by distancia asc ";
   			break;
   		case 5:
   		//CUALQUIER MOVIL 'PEDIDO DE ENCOMIENDA'.
   			$consulta="SELECT token,distancia_entre_dos_puntos(".$latitud.",".$longitud.",c.latitud_asignacion,c.longitud_asignacion) as distancia 
	   		FROM conductor c,vehiculo v where c.id_vehiculo=v.placa and c.estado=1 and c.login=1 and v.movil=1 and moto=0
	   		and distancia_entre_dos_puntos(".$latitud.",".$longitud.",c.latitud_asignacion,c.longitud_asignacion)<= ?  
	   		and c.ci not in (select id_conductor from pedido where estado<=1 and id_conductor is not null)  
	   		order by distancia asc ";
   			break;	
   		case 6:
   		//CUALQUIER MOVIL 'RESERVAR UN MOVIL PARA OTRA FECHA.
   			$consulta="SELECT token,distancia_entre_dos_puntos(".$latitud.",".$longitud.",c.latitud_asignacion,c.longitud_asignacion) as distancia 
	   		FROM conductor c,vehiculo v where c.id_vehiculo=v.placa and c.estado=1 and c.login=1 and v.movil=1 and moto=0
	   		and distancia_entre_dos_puntos(".$latitud.",".$longitud.",c.latitud_asignacion,c.longitud_asignacion)<= ?  
	   		and c.ci not in (select id_conductor from pedido where estado<=1 and id_conductor is not null)  
	   		order by distancia asc ";
   			break;	
   		case 7:
   		//CUALQUIER MOTO
   			$consulta="SELECT token,distancia_entre_dos_puntos(".$latitud.",".$longitud.",c.latitud_asignacion,c.longitud_asignacion) as distancia 
	   		FROM conductor c,vehiculo v where c.id_vehiculo=v.placa and c.estado=1 and c.login=1 and v.movil=1 and moto=1
	   		and distancia_entre_dos_puntos(".$latitud.",".$longitud.",c.latitud_asignacion,c.longitud_asignacion)<= ?  
	   		and c.ci not in (select id_conductor from pedido where estado<=1 and id_conductor is not null)  
	   		order by distancia asc ";
   			break;	
   		case 8:
   		//CUALQUIER MOTO
   			$consulta="SELECT token,distancia_entre_dos_puntos(".$latitud.",".$longitud.",c.latitud_asignacion,c.longitud_asignacion) as distancia 
	   		FROM conductor c,vehiculo v where c.id_vehiculo=v.placa and c.estado=1 and c.login=1 and v.movil=1 and moto=1
	   		and distancia_entre_dos_puntos(".$latitud.",".$longitud.",c.latitud_asignacion,c.longitud_asignacion)<= ?  
	   		and c.ci not in (select id_conductor from pedido where estado<=1 and id_conductor is not null)  
	   		order by distancia asc ";
   			break;		
   		case 11:
   		//MOVILES CON PARRILLA
   			$consulta="SELECT token,distancia_entre_dos_puntos(".$latitud.",".$longitud.",c.latitud_asignacion,c.longitud_asignacion) as distancia 
	   		FROM conductor c,vehiculo v where c.id_vehiculo=v.placa and c.estado=1 and c.login=1 and v.parrilla=1 and moto=0
	   		and distancia_entre_dos_puntos(".$latitud.",".$longitud.",c.latitud_asignacion,c.longitud_asignacion)<= ?  
	   		and c.ci not in (select id_conductor from pedido where estado<=1 and id_conductor is not null)  
	   		order by distancia asc ";
   			break;
   		case 15:
   		//MOVILES CAMIONETA
   			$consulta="SELECT token,distancia_entre_dos_puntos(".$latitud.",".$longitud.",c.latitud_asignacion,c.longitud_asignacion) as distancia 
	   		FROM conductor c,vehiculo v where c.id_vehiculo=v.placa and c.estado=1 and c.login=1 and v.camioneta=1 and moto=0
	   		and distancia_entre_dos_puntos(".$latitud.",".$longitud.",c.latitud_asignacion,c.longitud_asignacion)<= ?  
	   		and c.ci not in (select id_conductor from pedido where estado<=1 and id_conductor is not null)  
	   		order by distancia asc ";
   			break;							
   	}



   		$query = parent::getInstance()->getDb()->prepare($consulta);
        $query->execute(array(5000)); 
		$row=$query->fetchAll();
		if($row)
		{
			return true;
		}
		else
			{
				return false;
			}

   }
 	public static  function id_ultimo_pedido($id_usuario)
   {
   	try{
   	$query = parent::getInstance()->getDb()->prepare("SELECT id from pedido where date(fecha_pedido)=date(now()) and tipo_reserva_auxiliar=0 and tipo_pedido_auxiliar=0 and id_usuario=?  and id_conductor is null and id_vehiculo is null and estado=0 limit 1");
        $query->execute(array($id_usuario)); 
 		$row=$query->fetch(PDO::FETCH_ASSOC);
			if($row)
			{
				return $row['id'];
			}
			else
			{
				return -1;
			}
		}
		catch(PDOException $e)
		{	
        return -1; 
    }
   }
   public static  function id_ultimo_pedido_en_proceso($id_usuario)
   {
   	try{
   	$query = parent::getInstance()->getDb()->prepare("SELECT id from pedido where date(fecha_pedido)=date(now()) and id_usuario=? and estado=1 limit 1");
        $query->execute(array($id_usuario)); 
 		$row=$query->fetch(PDO::FETCH_ASSOC);
			if($row)
			{
				return $row['id'];
			}
			else
			{
				return -1;
			}
		}
		catch(PDOException $e)
		{	
        return -1; 
    }
   }
   	public static  function pedido_en_camino($id_usuario)
   {
   	try{
   	$query = parent::getInstance()->getDb()->prepare("SELECT * from pedido where date(fecha_pedido)=date(now()) and id_usuario=? and id_conductor is not null and estado<=1 and tipo_pedido=0 and tipo_reserva=0 limit 1");
        $query->execute(array($id_usuario)); 
 		$row=$query->fetch(PDO::FETCH_ASSOC);
			if($row)
			{
				return $row['id'];
			}
			else
			{
				return "-1";
			}
		}
		catch(PDOException $e)
		{	
			
        return "-1"; 
    }
   }



   public static function get_conductor_numero_movil($id_pedido)
   {
   	$resultado="..";
	$consulta="SELECT c.* from pedido p,conductor c where  p.id=? and p.id_conductor=c.ci ";
      try{
        $comando=parent::getInstance()->getDb()->prepare($consulta);
        $comando->execute(array($id_pedido));
        $row=$comando->fetch(PDO::FETCH_ASSOC);
        if($row)
          $resultado="#:".$row['numero_movil'].", Conductor:".$row['nombre']." ".$row['paterno']." ".$row['materno'].".";
         else
          $resultado=".";
      }catch(PDOException $e)
      {
         	$resultado=". :-(".$e;
      }
    
   	return $resultado;
   }

  	public static function aceptar_pedido($id_pedido,$id_conductor,$id_vehiculo)
   { //esta funcion registra el id de la taxi en el pedido que acaba de aceptar.......y si el pedio ya a sido registrado entonces devuelve que no se puede registrar...
   /*
	estado del taxi:
	0=inactivo
	1=activo
	2=
	3=
	4=en camino a un pedido.
   */
   	$res=false;
$asignado=self::id_taxi_del_pedido($id_pedido);
if($asignado!=-1)
{
   if($asignado['id_vehiculo']!=$id_vehiculo && $asignado['id_conductor']!=$id_conductor){

   	//CONSULTAR LA DISTANCIA ENTRE EL CONDUCTOR Y EL PUNTO DE LA SOLICITUD...
   	$distancia_conductor=self::distancia_pedido_conductor($id_pedido,$id_conductor);
   	$monto_aumentar=0;
   	if($distancia_conductor>1500)
   	{
   		$monto_aumentar=0;
   	}else if($distancia_conductor>800){
   		$monto_aumentar=0;
   	}
   	else if($distancia_conductor>400){
   		$monto_aumentar=0;
   	}

   	//FIN.........EL AUMENTO DEL MONTO DE LA TARIFA AL PEDIDO


    $consulta="UPDATE pedido p,conductor c,vehiculo v set p.id_vehiculo=?,p.id_conductor=?,p.monto_aumentar=? ,p.distancia_conductor=? , p.estado=0,p.fecha_proceso=now()
     where p.id=? and TIMESTAMPDIFF(MINUTE,p.fecha_pedido,now())<=1 and p.estado=0 and c.id_vehiculo=v.placa
    and c.credito>0 and c.estado=1 and c.bloqueo=0 and p.id_vehiculo is null and p.id_conductor is null";
      $aceptado=false;
			try{
  			    $comando=parent::getInstance()->getDb()->prepare($consulta);
				$comando->execute(array($id_vehiculo,$id_conductor,$monto_aumentar,$distancia_conductor,$id_pedido));
				$aceptado=true;
	  		}catch(PDOException $e)
	  		{
	  			$aceptado=false;
	  		}
	  	
	  		//si no hubo ningun problema al actualizar entonces verificamos si el id_taxi esta en el pedido
	  		if($aceptado==true)
	  		{
	  			try{
  			    $ejecutar=parent::getInstance()->getDb()->prepare("UPDATE  conductor set estado=2 where ci=?");
				$ejecutar->execute(array($id_conductor));
		  		}catch(PDOException $e)
		  		{
		  			$aceptado=false;
		  		}
		  		
				
				 $resultado=self::id_taxi_del_pedido($id_pedido);
	  			if($resultado['id_vehiculo']==$id_vehiculo && $resultado['id_conductor']==$id_conductor)
	  			{
	  				$res=true;
	  				$token=self::get_token_id_pedido($id_pedido);
	  				self::enviar_notificacion_aceptar_pedido($token,$id_pedido);
	  			}
	  			else
	  			{
	  			   $ejecutar=parent::getInstance()->getDb()->prepare("UPDATE  conductor set estado=1 where ci=?");
					$ejecutar->execute(array($id_conductor));
	  			}

	  		}
	  	}
	  }

return $res;
   }

   	public static function asignar_delivery_conductor($id_pedido,$id_conductor,$id_vehiculo)
   { //esta funcion registra el id de la taxi en el pedido que acaba de aceptar.......y si el pedio ya a sido registrado entonces devuelve que no se puede registrar...
   /*
	estado del taxi:
	0=inactivo
	1=activo
	2=
	3=
	4=en camino a un pedido.
   */
   	$res=false;
$asignado=self::id_taxi_del_pedido_delivery($id_pedido);

if($asignado!=-1)
{
   if($asignado['id_vehiculo']!=$id_vehiculo && $asignado['id_conductor']!=$id_conductor){

   	//CONSULTAR LA DISTANCIA ENTRE EL CONDUCTOR Y EL PUNTO DE LA SOLICITUD...
   	$distancia_conductor=self::distancia_pedido_conductor($id_pedido,$id_conductor);
   	$monto_aumentar=0;
   	if($distancia_conductor>1500)
   	{
   		$monto_aumentar=2;
   	}else if($distancia_conductor>800){
   		$monto_aumentar=1;
   	}
   	else if($distancia_conductor>400){
   		$monto_aumentar=0;
   	}

   	//FIN.........EL AUMENTO DEL MONTO DE LA TARIFA AL PEDIDO


    $consulta="UPDATE pedido p,conductor c,vehiculo v set p.id_vehiculo=?,p.id_conductor=?,p.monto_aumentar=? ,p.distancia_conductor=?,p.estado_pedido=12, p.estado=0,p.fecha_proceso=now()
     where p.id=?  and c.id_vehiculo=v.placa
    and c.credito>0 and c.estado=1 and c.bloqueo=0 and p.id_vehiculo is null and p.id_conductor is null";
      $aceptado=false;
			try{
  			    $comando=parent::getInstance()->getDb()->prepare($consulta);
				$comando->execute(array($id_vehiculo,$id_conductor,$monto_aumentar,$distancia_conductor,$id_pedido));
				$aceptado=true;
	  		}catch(PDOException $e)
	  		{
	  			 
	  			$aceptado=false;
	  		}
	  	
	  		//si no hubo ningun problema al actualizar entonces verificamos si el id_taxi esta en el pedido
	  		if($aceptado==true)
	  		{
	  			try{
  			    $ejecutar=parent::getInstance()->getDb()->prepare("UPDATE  conductor set estado=2  where ci=?");
				$ejecutar->execute(array($id_conductor));
		  		}catch(PDOException $e)
		  		{
		  			$aceptado=false;
		  		}
		  		
				
				 $resultado=self::id_taxi_del_pedido_delivery($id_pedido);
	  			if($resultado['id_vehiculo']==$id_vehiculo && $resultado['id_conductor']==$id_conductor)
	  			{
	  				$res=true;
	  				$token=self::get_token_id_pedido($id_pedido);
	  			}
	  			else
	  			{
	  			   $ejecutar=parent::getInstance()->getDb()->prepare("UPDATE  conductor set estado=1 where ci=?");
					$ejecutar->execute(array($id_conductor));
	  			}

	  		}
	  	}
	  }

return $res;
   }


     	public static function aceptar_reserva($id_pedido,$id_conductor,$id_vehiculo)
   { //esta funcion registra el id de la taxi en el pedido que acaba de aceptar.......y si el pedio ya a sido registrado entonces devuelve que no se puede registrar...
   /*
	estado del taxi:
	0=inactivo
	1=activo
	2=
	3=
	4=en camino a un pedido.
   */
   	$res=false;
$asignado=self::id_taxi_del_pedido_reserva($id_pedido);
if($asignado!=-1)
{
   if($asignado['id_vehiculo']!=$id_vehiculo && $asignado['id_conductor']!=$id_conductor){
    $consulta="UPDATE pedido p,conductor c,vehiculo v set p.id_vehiculo=?,p.id_conductor=?, p.estado_reserva=1,p.fecha_reserva_aceptado=now() 
     where p.id=? and TIMESTAMPDIFF(MINUTE,now(),p.fecha_reserva)>20 and p.estado_reserva=0 and c.id_vehiculo=v.placa
    and c.credito>0  and c.bloqueo=0 AND p.tipo_reserva=1  and p.id_vehiculo is null and p.id_conductor is null";
      $aceptado=false;
			try{
  			    $comando=parent::getInstance()->getDb()->prepare($consulta);
				$comando->execute(array($id_vehiculo,$id_conductor,$id_pedido));
				$aceptado=true;
	  		}catch(PDOException $e)
	  		{
	  			$aceptado=false;
	  		}
	  	
	  		//si no hubo ningun problema al actualizar entonces verificamos si el id_taxi esta en el pedido
	  		if($aceptado==true)
	  		{
	  			 
		  		
				
				 $resultado=self::id_taxi_del_pedido_reserva($id_pedido);
	  			if($resultado['id_vehiculo']==$id_vehiculo && $resultado['id_conductor']==$id_conductor)
	  			{
	  				$res=true;
	  				$token=self::get_token_id_pedido($id_pedido);
	  				self::enviar_notificacion_aceptar_reserva($token,$id_pedido);
	  			}
	  			

	  		}
	  	}
	  }

return $res;
   }

   	public static function aceptar_delivery($id_usuario,$id_pedido)
   { //esta funcion registra el id de la taxi en el pedido que acaba de aceptar.......y si el pedio ya a sido registrado entonces devuelve que no se puede registrar...
   /*
	estado del delivery:
	0=inactivo
	1=activo
	2=
	3=
	4=en camino a un pedido.
	10=pedido confirmado el envio al LUGAR.
	11=Pedido confirmado por la empresa.
	12=
	13=
	14=Pedido cancelado delivery por el administrador.

   */
 
    $consulta="UPDATE pedido set estado_pedido=11 where id=? and estado_pedido=10";
      $aceptado=false;
			try{
  			    $comando=parent::getInstance()->getDb()->prepare($consulta);
				$comando->execute(array($id_pedido));
				$aceptado=true;	
	  		}catch(PDOException $e)
	  		{
	  			$aceptado=false;
	  		}
	  	
	  		//si no hubo ningun problema al actualizar entonces verificamos si el id_taxi esta en el pedido
	  		if($aceptado==true)
	  		{
	  			  
	  				$token=self::get_token_id_pedido_usuario($id_pedido);
	  				self::notificacion_delivery_aceptado($id_pedido,$token);
	  			}

return $aceptado;
   }

     	public static function iniciar_pedido_reserva($id_pedido,$id_conductor,$id_vehiculo)
   { //esta funcion registra el id de la taxi en el pedido que acaba de aceptar.......y si el pedio ya a sido registrado entonces devuelve que no se puede registrar...
   /*
	estado del taxi:
	0=inactivo
	1=activo
	2=
	3=
	4=en camino a un pedido.
   */
   	$res=false;
$asignado=self::id_taxi_del_pedido_reserva($id_pedido);

if($asignado!=-1)
{
   if($asignado['id_vehiculo']==$id_vehiculo && $asignado['id_conductor']==$id_conductor){
    $consulta="UPDATE pedido p,conductor c,vehiculo v set  p.estado=0,p.fecha_proceso=now(),
     p.tipo_reserva_auxiliar=1,p.tipo_reserva=0 where p.id=?  and p.id_vehiculo=? and p.id_conductor=? and p.estado=0 and c.id_vehiculo=v.placa and c.credito>0 and c.estado=1 and c.bloqueo=0  and p.tipo_reserva=1 and p.estado_reserva=1";
      $aceptado=false;
			try{
  			    $comando=parent::getInstance()->getDb()->prepare($consulta);
				$comando->execute(array($id_pedido,$id_vehiculo,$id_conductor));
				$aceptado=true;
	  		}catch(PDOException $e)
	  		{

	  			$aceptado=false;
	  		}
	  	
	  		//si no hubo ningun problema al actualizar entonces verificamos si el id_taxi esta en el pedido
	  		if($aceptado==true)
	  		{
	  			try{
  			    $ejecutar=parent::getInstance()->getDb()->prepare("UPDATE  conductor set estado=2 where ci=?");
				$ejecutar->execute(array($id_conductor));
		  		}catch(PDOException $e)
		  		{
		  			$aceptado=false;
		  		}
		  		
				
				 $resultado=self::id_taxi_del_pedido($id_pedido);
	  			if($resultado['id_vehiculo']==$id_vehiculo && $resultado['id_conductor']==$id_conductor)
	  			{
	  				$res=true;
	  				$token=self::get_token_id_pedido($id_pedido);
	  				self::enviar_notificacion_aceptar_pedido($token,$id_pedido);
	  			}
	  			else
	  			{
	  			   $ejecutar=parent::getInstance()->getDb()->prepare("UPDATE  conductor set estado=1 where ci=?");
					$ejecutar->execute(array($id_conductor));
	  			}

	  		}
	  	}
	  }

return $res;
   }

  	public static function enviar_notificacion_comenzar_pedido_reservado($token,$id_pedido)
	  {try{
		   $push = new Push('Pedido','Su movil de reserva esta en camino.',null,"usuario",$id_pedido,"","","","");
	     // obteniendo el empuje del objeto push
				$pedido=self::get_pedido_por_id_pedido($id_pedido);
				$push->set_pedido(array($pedido)); 
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

	public static function enviar_notificacion_aceptar_pedido($token,$id_pedido)
	  {try{
		   $push = new Push('Pedido de Taxi-Valle','Su Movil esta en camino.',null,"usuario",$id_pedido,"","","","1");
	     // obteniendo el empuje del objeto push
				$pedido=self::get_pedido_por_id_pedido($id_pedido);
				$push->set_pedido(array($pedido)); 
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
    public static function enviar_notificacion_aceptar_reserva($token,$id_pedido)
	  {try{
		   $push = new Push('Reserva de movil','Su reserva a sido aceptada',null,"usuario",$id_pedido,"","","","17");
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


 	public static   function get_token_id_pedido($id_pedido)
   { $query = parent::getInstance()->getDb()->prepare("SELECT u.token from usuario u, pedido p  where u.id=p.id_usuario and p.id=? and p.estado<2");
        $query->execute(array($id_pedido)); 
         $tokens = array(); 
        while($row=$query->fetch(PDO::FETCH_OBJ)) {
 			array_push($tokens, $row->token);
    }
        return $tokens; 
   }

     public static function get_token_id_pedido_usuario($id_pedido)
   { $query = parent::getInstance()->getDb()->prepare("SELECT m.token from usuario m, pedido p where m.id=p.id_usuario and p.id=?");
        $query->execute(array($id_pedido)); 
         $tokens = array(); 
        while($row=$query->fetch(PDO::FETCH_OBJ)) {
 			array_push($tokens, $row->token);
    }
        return $tokens; 
   }
     public static function get_token_id_pedido_conductor($id_pedido)
   { $query = parent::getInstance()->getDb()->prepare("SELECT c.token from conductor c, pedido p where c.ci=p.id_conductor and p.id=?");
        $query->execute(array($id_pedido)); 
         $tokens = array(); 
        while($row=$query->fetch(PDO::FETCH_OBJ)) {
 			array_push($tokens, $row->token);
    }
        return $tokens; 
   }

     public static function get_token_administrador_id_lugar($id_lugar)
   { $query = parent::getInstance()->getDb()->prepare("SELECT m.token from usuario m, lugar p where p.id_administrador=m.id and p.id=?");
        $query->execute(array($id_lugar)); 
         $tokens = array(); 
        while($row=$query->fetch(PDO::FETCH_OBJ)) {
 			array_push($tokens, $row->token);
    }
        return $tokens; 
   }





 	public static  function id_taxi_del_pedido($id_pedido)
   {$resultado=-1;
   	try{
   	$query = parent::getInstance()->getDb()->prepare("SELECT id_vehiculo,id_conductor from pedido where id=? and tipo_reserva=0 and tipo_pedido=0");
        $query->execute(array($id_pedido)); 
 		$row=$query->fetch(PDO::FETCH_ASSOC);
			if($row)
			{
				return $row;
			}
		}
		catch(PDOException $e)
		{	
        $resultado =-1; 
   		 }
    return $resultado;
   }

 	public static  function id_taxi_del_pedido_reserva($id_pedido)
   {$resultado=-1;
   	try{
   	$query = parent::getInstance()->getDb()->prepare("SELECT id_vehiculo,id_conductor from pedido where id=? and tipo_reserva=1");
        $query->execute(array($id_pedido)); 
 		$row=$query->fetch(PDO::FETCH_ASSOC);
			if($row)
			{
				return $row;
			}
		}
		catch(PDOException $e)
		{	
        $resultado =-1; 
   		 }
    return $resultado;
   }

   	public static  function id_taxi_del_pedido_delivery($id_pedido)
   {$resultado=-1;
   	try{
   	$query = parent::getInstance()->getDb()->prepare("SELECT id_vehiculo,id_conductor from pedido where id=? and tipo_pedido=1");
        $query->execute(array($id_pedido)); 
 		$row=$query->fetch(PDO::FETCH_ASSOC);
			if($row)
			{
				return $row;
			}
		}
		catch(PDOException $e)
		{	
        $resultado =-1; 
   		 }
    return $resultado;
   }

  	public static function pedido_en_curso_por_id($id_pedido)
	{
		$consulta="SELECT p.*, concat(nombre,' ',apellido)as 'nombre_usuario',u.celular from pedido p,usuario u where p.id_usuario=u.id and p.tipo_reserva=0 and p.tipo_pedido=0 and p.id=?";
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

	 	public static function delivery_en_curso_por_id($id_pedido)
	{
		$consulta="SELECT p.*, concat(nombre,' ',apellido)as 'nombre_usuario',u.celular from pedido p,usuario u where p.id_usuario=u.id  and p.id=?";
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

	public static function get_datos_si_esta_disponible($id_conductor,$placa)
	{
		$consulta="SELECT c.estado_asignacion as 'estado_asignacion',c.estado,c.panico,c.bloqueo from conductor c,vehiculo v 
		where  c.id_vehiculo=v.placa and  c.ci=? and c.id_vehiculo=?";
			try{
				$comando=parent::getInstance()->getDb()->prepare($consulta);
				$comando->execute(array($id_conductor,$placa));
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
	public static function tiene_pedido($id_conductor,$placa)
	{
		$consulta="SELECT estado,panico from pedido where estado<=1 and tipo_reserva=0 and tipo_pedido=0 and id_conductor=? and id_vehiculo=?";
			try{
				$comando=parent::getInstance()->getDb()->prepare($consulta);
				$comando->execute(array($id_conductor,$placa));
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

 
 	public static function estoy_cerca($id_pedido)
   { 
  	  	
	  try{
	  	$token=self::get_token_id_pedido($id_pedido);
	  				
		   $push = new Push('Pedido','El Movil esta a 500 mt.',null,"usuario",$id_pedido,"","","","14");
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
   public static function notificacion_llego_el_taxi($id_pedido)
   { 
  	  	
	  try{
	  	$token=self::get_token_id_pedido($id_pedido);
	  				
		   $push = new Push('Pedido en Taxi-Valle','Su Movil esta en su puerta.',null,"usuario",$id_pedido,"","","","15");
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

   public static function notificacion_pedido_finalizado($id_pedido)
   { 
  	  	
	  try{
	  	$token=self::get_token_id_pedido_conductor($id_pedido);
	  	$token_usuario=self::get_token_id_pedido_usuario($id_pedido);
	  				
		   $push = new Push('Pedido','Pedido finalizado correctamente.',null,"taxi",$id_pedido,"","","","7");
		    $push_usuario = new Push('Pedido','Pedido finalizado correctamente.',null,"usuario",$id_pedido,"","","","10");
	     // obteniendo el empuje del objeto push
			 $mPushNotification = $push->getPush(); 
			 
			 // obtener el token del objeto de base de datos

			// creación de objeto de clase firebase
			 $firebase = new Firebase(); 
			 $firebase_usuario = new Firebase(); 
			 
			 // envío de notificación push y visualización de resultados
			 $firebase_usuario->send($token_usuario,  $push_usuario->getPush());
			  $firebase->send($token, $mPushNotification);
			
			return true;
			}
		catch (Exception $e){
	return false;
		}
   }

   public static function notificacion_delivery_aceptado($id_pedido,$token)
   { 
  	  	
	  try{
	   	     $push = new Push('Pedido','Su pedido esta en proceso.',null,"usuario",$id_pedido,"","","","111");
	     
			// creación de objeto de clase firebase
			 $firebase_usuario = new Firebase(); 
			 
			 // envío de notificación push y visualización de resultados
			 $firebase_usuario->send($token,  $push->getPush());
			
			return true;
			}
		catch (Exception $e){
	return false;
		}
   }
    
    public static function cancelar_pedido_usuario($id_pedido,$id_usuario)
	{ $resultado=false;
		#Confirmamos el pedido.  diciendo que el motista llego,
		try{
			$confirmar_pedido="UPDATE pedido p,conductor c set p.estado='4' ,c.estado='1', p.fecha_finalizado = now() where p.id= ? and p.id_usuario=? and p.estado='0' and c.ci=p.id_conductor";
			$comando=parent::getInstance()->getDb()->prepare($confirmar_pedido);
			$comando->execute(array($id_pedido,$id_usuario));
			$resultado=true;

			$sw_u=self::registrar_punto_cancelacion_usuario($id_pedido);
            }
            catch(PDOException $e)
            {
		$resultado=false;
            }		
            return $resultado;
     }

    public static function cancelar_pedido_reserva_usuario($id_pedido,$id_usuario,$detalle)
	{ $resultado=false;
		#Confirmamos el pedido.  diciendo que el motista llego,
		try{
			$confirmar_pedido="UPDATE pedido p set p.estado='4',p.estado_reserva='4' ,  p.fecha_finalizado = now(),p.detalle_cancelo_usuario=? where p.id= ? and p.id_usuario=? and p.estado='0'";
			$comando=parent::getInstance()->getDb()->prepare($confirmar_pedido);
			$comando->execute(array($detalle,$id_pedido,$id_usuario));
			$resultado=true;
            }
            catch(PDOException $e)
            {
		$resultado=false;
            }		
            return $resultado;
     }     

     public static function cancelar_pedido_delivery_usuario($id_pedido,$id_usuario)
	{ $resultado=false;
		#Confirmamos el pedido.  diciendo que el motista llego,
		try{
			$confirmar_pedido="UPDATE pedido p set p.estado='4',p.estado_pedido='4' ,  p.fecha_finalizado = now() where p.id= ? and p.id_usuario=? and p.estado='0'";
			$comando=parent::getInstance()->getDb()->prepare($confirmar_pedido);
			$comando->execute(array($id_pedido,$id_usuario));
			$resultado=true;
            }
            catch(PDOException $e)
            {
		$resultado=false;
            }		
            return $resultado;
     }  

       public static function cancelar_delivery_administrador($id_pedido,$id_usuario)
	{ $resultado=false;
		#Confirmamos el pedido.  diciendo que el motista llego,
		try{
			$confirmar_pedido="UPDATE pedido p set p.estado='14',p.estado_pedido='14' ,  p.fecha_finalizado = now() where p.id= ? and p.id_usuario=? ";
			$comando=parent::getInstance()->getDb()->prepare($confirmar_pedido);
			$comando->execute(array($id_pedido,$id_usuario));
			$resultado=true;
            }
            catch(PDOException $e)
            {
		$resultado=false;
            }		
            return $resultado;
     }     


 public static function cancelar_abordo_carrera($id_pedido,$id_usuario)
	{ $resultado=false;
		#Confirmamos el pedido.  diciendo que el motista llego,
		try{
			$confirmar_pedido="UPDATE pedido p,conductor c set p.estado='4' ,c.estado='1',p.abordo='2', p.fecha_finalizado = now() where p.id= ? and p.id_usuario=? and p.estado='1'";
			$comando=parent::getInstance()->getDb()->prepare($confirmar_pedido);
			$comando->execute(array($id_pedido,$id_usuario));
			$resultado=true;
            }
            catch(PDOException $e)
            {
			$resultado=false;
            }		
            return $resultado;
     }

 public static function aceptar_abordo_carrera($id_pedido,$id_usuario)
	{ $resultado=false;
		#Confirmamos el pedido.  diciendo que el motista llego,
		try{
			$confirmar_pedido="UPDATE pedido p,conductor c set p.abordo='1' where p.id= ? and p.id_usuario=? and p.estado='1'";
			$comando=parent::getInstance()->getDb()->prepare($confirmar_pedido);
			$comando->execute(array($id_pedido,$id_usuario));
			$resultado=true;
            }
            catch(PDOException $e)
            {
			$resultado=false;
            }		
            return $resultado;
     }

    public static function detalle_cancelar_pedido_usuario($id_pedido,$id_usuario,$detalle)
	{ $resultado=false;
		#Confirmamos el pedido.  diciendo que el motista llego,
		try{
			$confirmar_pedido="UPDATE pedido set detalle_cancelo_usuario=? where id_usuario=? and id=?";
			$comando=parent::getInstance()->getDb()->prepare($confirmar_pedido);
			$comando->execute(array($detalle,$id_usuario,$id_pedido));
			$resultado=true;
            }
            catch(PDOException $e)
            {
		$resultado=false;
            }		
            return $resultado;
     }

      public static function cancelar_pedido_conductor($id_pedido,$id_conductor,$placa,$detalle)
	{ $resultado=false;
		#Confirmamos el pedido.  diciendo que el motista llego,
		try{
			$confirmar_pedido="UPDATE pedido p,conductor c set p.estado='5' ,c.estado='1', p.fecha_finalizado = now(),detalle_cancelo_conductor=? where p.id= ? and p.id_conductor=? and p.id_vehiculo=? and c.ci=? and p.estado='0'";
			$comando=parent::getInstance()->getDb()->prepare($confirmar_pedido);
			$comando->execute(array($detalle,$id_pedido,$id_conductor,$placa,$id_conductor));
			$resultado=true;
			$sw_p=self::registrar_punto_cancelacion($id_pedido,$id_conductor);
            }
            catch(PDOException $e)
            {
		$resultado=false;
            }		
            return $resultado;
     }

       public static function cancelar_pedido_reserva_conductor($id_pedido,$id_conductor,$placa,$detalle)
	{ $resultado=false;
		#Confirmamos el pedido.  diciendo que el motista llego,
		try{
			$confirmar_pedido="UPDATE pedido p,conductor c set id_vehiculo=null,id_conductor=null,p.estado='0' , p.fecha_finalizado = now(),detalle_cancelo_conductor=? where p.id= ? and p.id_conductor=? and p.id_vehiculo=? and p.estado='0' and p.estado_reserva=1 and p.tipo_reserva=1";
			$comando=parent::getInstance()->getDb()->prepare($confirmar_pedido);
			$comando->execute(array($detalle,$id_pedido,$id_conductor,$placa));
			$resultado=true;

            }
            catch(PDOException $e)
            {
		$resultado=false;
            }		
            return $resultado;
     }

        public static function cancelar_pedido_delivery_conductor($id_pedido,$id_conductor,$placa,$detalle)
	{ $resultado=false;
		#Confirmamos el pedido.  diciendo que el motista llego,
		try{
			$confirmar_pedido="UPDATE pedido p,conductor c set id_vehiculo=null,id_conductor=null,p.estado='0' , p.fecha_finalizado = now(),detalle_cancelo_conductor=? where p.id= ? and p.id_conductor=? and p.id_vehiculo=? and p.estado='0' and p.estado_pedido=1 and p.tipo_pedido=1";
			$comando=parent::getInstance()->getDb()->prepare($confirmar_pedido);
			$comando->execute(array($detalle,$id_pedido,$id_conductor,$placa));
			$resultado=true;

            }
            catch(PDOException $e)
            {
		$resultado=false;
            }		
            return $resultado;
     }


      public static function detalle_cancelar_pedido_conductor($id_pedido,$id_conductor,$detalle)
	{ $resultado=false;
		#Confirmamos el pedido.  diciendo que el motista llego,
		try{
			$confirmar_pedido="UPDATE pedido set detalle_cancelo_conductor=? where id_conductor=? and id=?";
			$comando=parent::getInstance()->getDb()->prepare($confirmar_pedido);
			$comando->execute(array($detalle,$id_conductor,$id_pedido));
			$resultado=true;
            }
            catch(PDOException $e)
            {
		$resultado=false;
            }		
            return $resultado;
     }

         public static function notificacion_pedido_cancelado_usuario($id_pedido,$detalle)
   { 
  	  	
	  try{
	  	$token=self::get_token_id_pedido_conductor($id_pedido);	 
	  				
	  				//notificacion para el taxista
		   $push = new Push('Pedido','El Pasajero cancelo el pedido.'.$detalle,null,"taxi",$id_pedido,"","","","13");
		   //notificacion al usuario
	     // obteniendo el empuje del objeto push
			 $mPushNotification = $push->getPush(); 
			 
			 // obtener el token del objeto de base de datos

			// creación de objeto de clase firebase
			 $firebase = new Firebase(); 
				 //envia notificaion el taxi(taxista)
			  $firebase->send($token, $mPushNotification);
			
			return true;
			}
		catch (Exception $e){
	return false;
		}
   }
    public static function notificacion_pedido_reserva_cancelado_conductor($id_pedido,$detalle)
   { 
  	  	
	  try{
	  	$token_usuario=self::get_token_id_pedido_usuario($id_pedido);
	  				
		   //notificacion al usuario
		    $push_usuario = new Push('Pedido','El Conductor cancelo la reserva.'.$detalle.'. Su reserva se esta enviando nuevamente.',null,"usuario",$id_pedido,"","","","7");
	    	 // obtener el token del objeto de base de datos

			// creación de objeto de clase firebase
			 $firebase_usuario = new Firebase(); 
			 
			 // envío de notificación push y visualización de resultados
			 //envia notificacion al usuario..
			 $firebase_usuario->send($token_usuario,  $push_usuario->getPush());
			 //envia notificaion el taxi(taxista)
			
			return true;
			}
		catch (Exception $e){
	return false;
		}
   }

       public static function notificacion_pedido_cancelado_conductor($id_pedido,$detalle)
   { 
  	  	
	  try{
	  	$token_usuario=self::get_token_id_pedido_usuario($id_pedido);
	  				
		   //notificacion al usuario
		    $push_usuario = new Push('Pedido','El Conductor cancelo el pedido.'.$detalle.'. Vuelva a solicitar tu movil o llamanos a la central de Radio Movil.',null,"usuario",$id_pedido,"","","","12");
	    	 // obtener el token del objeto de base de datos

			// creación de objeto de clase firebase
			 $firebase_usuario = new Firebase(); 
			 
			 // envío de notificación push y visualización de resultados
			 //envia notificacion al usuario..
			 $firebase_usuario->send($token_usuario,  $push_usuario->getPush());
			 //envia notificaion el taxi(taxista)
			
			return true;
			}
		catch (Exception $e){
	return false;
		}
   }

    public static function cancelar_pedido($id_pedido)
   { 
	   try{
      $consulta="UPDATE pedido set estado= 3 where id= ? and estado=3 and id_conductor is null and id_vehiculo is null";
      $comando=parent::getInstance()->getDb()->prepare($consulta);
      $comando->execute(array($id_pedido));
 		 $pedido=self::get_pedido_por_id_pedido($id_pedido);
 		 if($pedido=="-1")
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

   	public static function monto_total_por_id_pedido($id_pedido)
	{
		$consulta="SELECT monto_total from pedido where id=?";
		try {
			$comando = parent::getInstance()->getDb()->prepare($consulta);
			$comando->execute(array($id_pedido));
			$row=$comando->fetch(PDO::FETCH_ASSOC);
			return $row['monto_total'];
		} catch (PDOException $e) {
			return -1;
		}
	}

	 public static function cargar_puntuacion($id_pedido,$punto_conductor,$punto_vehiculo,$descripcion)
	{ $resultado=false;
		#Confirmamos el pedido.  diciendo que el motista llego,
		try{
			$punto_conductor=(int)$punto_conductor;
			$punto_vehiculo=(int)$punto_vehiculo;
			if($punto_conductor<=0){
				$punto_vehiculo=0;
			}
			if($punto_vehiculo<=0){
				$punto_vehiculo=0;
			}

			$consulta="UPDATE pedido set calificacion_conductor=?,calificacion_vehiculo=?,descripcion_finalizo=? where id= ?";
			$comando=parent::getInstance()->getDb()->prepare($consulta);
			$comando->execute(array($punto_conductor,$punto_vehiculo,$descripcion,$id_pedido));
			$resultado=true;

			$pedido=self::get_dato_pedido_por_id($id_pedido);
			$id_conductor=$pedido['id_conductor'];
			$id_vehiculo=$pedido['id_vehiculo'];
			
			if($pedido!=-1)
			{
						$punto_conductor_2=self::get_calificacion_conductor($id_conductor);
						$punto_vehiculo_2=self::get_calificacion_vehiculo($id_vehiculo);
						
					if($punto_conductor!=-1 && $punto_vehiculo!=-1)
					{
						$punto_conductor=(int)(((int)$punto_conductor+(int)$punto_conductor_2)/2);
						$punto_vehiculo=(int)(((int)$punto_vehiculo+(int)$punto_vehiculo_2)/2);

						self::actualizar_calificacion($id_conductor,$id_vehiculo,$punto_conductor,$punto_vehiculo);
					}
			}

            }
            catch(PDOException $e)
            {
            	
				$resultado=false;
            }		
            return $resultado;
     }
 
 public static function actualizar_calificacion($id_conductor,$id_vehiculo,$punto_conductor,$punto_vehiculo)
   { 
	   try{
	   	if($punto_conductor<=0){
	   		$punto_conductor=1;
	   	}
	   	if($punto_vehiculo<=0){
	   		$punto_vehiculo=1;
	   	}
      $consulta="UPDATE conductor c, vehiculo v set c.calificacion=?, v.calificacion=? where c.ci=? and v.placa=?";
      $comando=parent::getInstance()->getDb()->prepare($consulta);
      $comando->execute(array($punto_conductor,$punto_vehiculo,$id_conductor,$id_vehiculo));
 		return true;
      
       }catch(PDOException $e)
     {
      return false;
     }
   }

       public static function get_dato_pedido_por_id($id)
		{
			$consulta="SELECT id_conductor,id_vehiculo from pedido where id=?";
		try{
				$comando=parent::getInstance()->getDb()->prepare($consulta);
				$comando->execute(array($id));
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
		 public static function get_calificacion_por_ci_vehiculo($id_conductor,$id_vehiculo)
		{
			$consulta="SELECT avg(calificacion_conductor)as 'calificacion_conductor',avg(calificacion_vehiculo) as 'calificacion_vehiculo' from pedido where id_conductor=? or id_vehiculo=?";
		try{
				$comando=parent::getInstance()->getDb()->prepare($consulta);
				$comando->execute(array($id_conductor,$id_vehiculo));
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
			 public static function get_calificacion_conductor($id_conductor)
		{
			$consulta="SELECT calificacion as 'calificacion_conductor' from conductor where  ci=? ";
		try{
				$comando=parent::getInstance()->getDb()->prepare($consulta);
				$comando->execute(array($id_conductor));
				$row=$comando->fetch(PDO::FETCH_ASSOC);
				if($row)
					return $row['calificacion_conductor'];
				 else
					return -1;
			}catch(PDOException $e)
			{
			     return -1;
			}
		}
			 public static function get_calificacion_vehiculo($id_vehiculo)
		{
			$consulta="SELECT calificacion as 'calificacion_vehiculo' from vehiculo where placa =? ";
		try{
				$comando=parent::getInstance()->getDb()->prepare($consulta);
				$comando->execute(array($id_vehiculo));
				$row=$comando->fetch(PDO::FETCH_ASSOC);
				if($row)
					return $row['calificacion_vehiculo'];
				 else
					return -1;
			}catch(PDOException $e)
			{
			     return -1;
			}
		}

     	  public static function lista_pedido_por_ci($ci)
		{
			$consulta="SELECT p.id,p.id_usuario,p.fecha_pedido,p.latitud,p.longitud,m.nombre,m.apellido,m.celular,p.direccion,p.estado,p.detalle_cancelo_usuario,p.direccion_inicio as 'detalle',p.monto_total,p.clase_vehiculo,p.calificacion_conductor,p.calificacion_vehiculo from pedido p,usuario m where p.id_usuario=m.id and p.id_conductor= ? ORDER by p.id desc limit 50";
		try{
				$comando=parent::getInstance()->getDb()->prepare($consulta);
				$comando->execute(array($ci));
				$row=$comando->fetchAll();
				if($row)
					return $row;
				 else
					return -1;
			}catch(PDOException $e)
			{
			     return -1;
			}
		}
		 public static function lista_pedido_por_ci_mes($ci,$mes,$anio)
		{
			$consulta="SELECT p.id,p.id_usuario,p.fecha_pedido,p.latitud,p.longitud,m.nombre,m.apellido,m.celular,p.direccion,p.estado,p.detalle_cancelo_usuario,p.direccion_inicio as 'detalle',p.monto_total,p.clase_vehiculo,p.calificacion_conductor,p.calificacion_vehiculo from pedido p,usuario m where p.id_usuario=m.id and p.id_conductor= ? and MONTH(p.fecha_pedido)=? and YEAR(p.fecha_pedido)=? ORDER by p.id desc limit 30";
		try{
				$comando=parent::getInstance()->getDb()->prepare($consulta);
				$comando->execute(array($ci,$mes,$anio));
				$row=$comando->fetchAll();
				if($row)
					return $row;
				 else
					return -1;
			}catch(PDOException $e)
			{
			     return -1;
			}
		}

		 public static function lista_pedido_por_id_usuario($id_usuario)
		{
			$consulta="SELECT p.id,p.id_conductor,p.fecha_pedido,p.latitud,p.longitud,c.nombre,concat(c.paterno,' ',c.materno) as 'apellido',c.celular,v.marca,v.placa,p.direccion,p.estado,p.detalle_cancelo_usuario,p.direccion_inicio as 'detalle',p.monto_total,p.clase_vehiculo,p.calificacion_conductor,p.calificacion_vehiculo from pedido p,conductor c,vehiculo v where p.id_conductor=c.ci and p.id_vehiculo=v.placa and p.id_usuario=? ORDER by p.id desc limit 100";
		try{
				$comando=parent::getInstance()->getDb()->prepare($consulta);
				$comando->execute(array($id_usuario));
				$row=$comando->fetchAll();
				if($row)
					return $row;
				 else
					return -1;
			}catch(PDOException $e)
			{
			     return -1;
			}
		}




public static function lista_pedido_por_id_usuario_mes($id_usuario,$mes,$anio)
		{
		
			
			$consulta="SELECT p.id,p.id_conductor,p.fecha_pedido,p.latitud,p.longitud,c.nombre,concat(c.paterno,' ',c.materno) as 'apellido',c.celular,v.marca,v.placa,p.direccion,p.estado,p.detalle_cancelo_usuario,p.direccion_inicio as 'detalle',p.monto_total,p.clase_vehiculo,p.calificacion_conductor,p.calificacion_vehiculo from pedido p,conductor c,vehiculo v where p.id_conductor=c.ci and p.id_vehiculo=v.placa and p.id_usuario=?  and MONTH(p.fecha_pedido)=? and YEAR(p.fecha_pedido)=?  ORDER by p.id desc ";
		try{
				$comando=parent::getInstance()->getDb()->prepare($consulta);
				$comando->execute(array($id_usuario,$mes,$anio));
				$row=$comando->fetchAll();
				if($row)
					return $row;
				 else
					return -1;
			}catch(PDOException $e)
			{
			     return -1;
			}
		}



		public static function lista_pedido_por_id_usuario_top50($id_usuario)
		{
			$consulta="SELECT p.id,p.id_conductor,p.fecha_pedido,p.latitud,p.longitud,c.nombre,concat(c.paterno,' ',c.materno) as 'apellido',c.celular,v.marca,v.placa,p.direccion,p.estado,p.detalle_cancelo_usuario,p.direccion_inicio as 'detalle',p.monto_total,p.clase_vehiculo,p.calificacion_conductor,p.calificacion_vehiculo from pedido p,conductor c,vehiculo v where p.id_conductor=c.ci and p.id_vehiculo=v.placa and p.id_usuario=? ORDER by p.id desc limit 50";
		try{
				$comando=parent::getInstance()->getDb()->prepare($consulta);
				$comando->execute(array($id_usuario));
				$row=$comando->fetchAll();
				if($row)
					return $row;
				 else
					return -1;
			}catch(PDOException $e)
			{
			     return -1;
			}
		}


		 public static function lista_delivery_por_id_usuario($id_usuario)
		{
			$consulta="SELECT p.id,p.id_conductor,p.fecha_pedido,p.latitud,p.longitud,c.nombre,concat(c.paterno,' ',c.materno) as 'apellido',c.celular,v.marca,v.placa,p.direccion,p.estado,p.detalle_cancelo_usuario,p.direccion_inicio as 'detalle',p.monto_total,p.clase_vehiculo,p.calificacion_conductor,p.calificacion_vehiculo from pedido p,conductor c,vehiculo v where p.id_conductor=c.ci and p.id_vehiculo=v.placa and p.id_usuario=? ORDER by p.id desc limit 100";
		try{
				$comando=parent::getInstance()->getDb()->prepare($consulta);
				$comando->execute(array($id_usuario));
				$row=$comando->fetchAll();
				if($row)
					return $row;
				 else
					return -1;
			}catch(PDOException $e)
			{
			     return -1;
			}
		}

 public static function lista_pedido_por_id_max_4($id_pedido)
		{
			$consulta="SELECT p.id,p.id_conductor,p.fecha_pedido,p.latitud,p.longitud,c.nombre,concat(c.paterno,' ',c.materno) as 'apellido',c.celular,v.marca,v.placa,p.direccion,p.estado,p.detalle_cancelo_usuario,p.direccion_inicio as 'detalle',p.monto_total,p.clase_vehiculo,p.calificacion_conductor,p.calificacion_vehiculo from pedido p,conductor c,vehiculo v where p.id_conductor=c.ci and p.id_vehiculo=v.placa and p.id<=? ORDER by p.id desc limit 1";
		try{
				$comando=parent::getInstance()->getDb()->prepare($consulta);
				$comando->execute(array($id_pedido));
				$row=$comando->fetchAll();
				if($row)
					return $row;
				 else
					return -1;
			}catch(PDOException $e)
			{
			     return -1;
			}
		}

	 public static function get_pedido_proceso_id_pedido($id)
		{
			$consulta="SELECT p.id,p.id_conductor,p.id_vehiculo,p.id_usuario,p.fecha_pedido,p.latitud,p.longitud,m.nombre,m.apellido,m.celular,m.direccion_imagen,p.direccion,p.direccion_inicio,p.estado,p.estado_pedido,p.detalle_cancelo_usuario,p.direccion_inicio as 'detalle',p.monto_total,p.monto_pedido,p.clase_vehiculo,p.calificacion_conductor,p.calificacion_vehiculo,p.id_lugar,l.nombre as 'razon_social', l.nit,l.direccion as 'direccion_empresa',l.direccion_logo from pedido p,usuario m,lugar l where p.id_usuario=m.id and l.id=p.id_lugar and p.id= ? ORDER by p.id desc limit 1";
		try{
				$comando=parent::getInstance()->getDb()->prepare($consulta);
				$comando->execute(array($id));
				$row=$comando->fetchAll();
				if($row)
					return $row;
				 else
					return -1;
			}catch(PDOException $e)
			{
			     return -1;
			}
		}

	 
		public static function get_carrito_por_id_pedido($id)
		{
			$consulta="SELECT c.*,p.nombre,p.descripcion,p.imagen1 from carrito c,producto p where p.id=c.id_producto and c.id_pedido=?";
		try{
				$comando=parent::getInstance()->getDb()->prepare($consulta);
				$comando->execute(array($id));
				$row=$comando->fetchAll();
				if($row)
					return $row;
				 else
					return -1;
			}catch(PDOException $e)
			{
			     return -1;
			}
		}


		public static function enviar_notificacion_conductor($id_pedido,$id_usuario,$ci,$placa,$detalle)
		{
				try{
		   $push = new Push('Sistema',$detalle,null,"usuario",$id_pedido,"","0","0","7");
	     // obteniendo el empuje del objeto push
			 $mPushNotification = $push->getPush(); 
			 
			 // obtener el token del objeto de base de datos

			 $devicetoken = self::get_token_id_pedido($id_pedido);		 

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
		
		public static function enviar_notificacion_usuario($id_pedido,$id_usuario,$detalle)
		{
				try{
		   $push = new Push('Taxi-Valle',$detalle,null,"taxi",$id_pedido,"","0","0","6");
	     // obteniendo el empuje del objeto push
			 $mPushNotification = $push->getPush(); 
			 
			 // obtener el token del objeto de base de datos

			 $devicetoken = self::get_token_id_pedido_conductor($id_pedido);		 

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

		public static function enviar_notificacion_lugar($id_lugar,$detalle)
		{
				try{
		   $push = new Push('Sistema',$detalle,null,"usuario","0","","0","0","110");
	     // obteniendo el empuje del objeto push
			 $mPushNotification = $push->getPush(); 
			 
			 // obtener el token del objeto de base de datos

			 $devicetoken = self::get_token_administrador_id_lugar($id_lugar);		 

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

	public static function enviar_notificacion_hay_un_pedido($id_lugar,$detalle)
		{
				try{
		   $push = new Push('Sistema',$detalle,null,"usuario","0","","0","0","110");
	     // obteniendo el empuje del objeto push
			 $mPushNotification = $push->getPush(); 
			 
			 // obtener el token del objeto de base de datos

			 $devicetoken = self::get_token_administrador_id_lugar($id_lugar);		 

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
			
		 	 }catch (Exception $e){
		     return false;
			}

		}

 	

	public static function enviar_notificacion_su_pedido_esta_en_camino($id_lugar,$detalle)
		{
				try{
		   $push = new Push('Sistema',$detalle,null,"usuario","0","","0","0","112");
	     // obteniendo el empuje del objeto push
			 $mPushNotification = $push->getPush(); 
			 
			 // obtener el token del objeto de base de datos

			 $devicetoken = self::get_token_administrador_id_lugar($id_lugar);		 

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
			
		 	 }catch (Exception $e){
		     return false;
			}

		}	


		public static function enviar_notificacion_su_pedido_esta_en_proceso($id_lugar,$detalle)
		{
				try{
		   $push = new Push('Sistema',$detalle,null,"usuario","0","","0","0","113");
	     // obteniendo el empuje del objeto push
			 $mPushNotification = $push->getPush(); 
			 
			 // obtener el token del objeto de base de datos

			 $devicetoken = self::get_token_administrador_id_lugar($id_lugar);		 

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
			
		 	 }catch (Exception $e){
		     return false;
			}

		}	

		public static function enviar_notificacion_pedido_finalizado($id_lugar,$detalle)
		{
				try{
		   $push = new Push('Sistema',$detalle,null,"usuario","0","","0","0","115");
	     // obteniendo el empuje del objeto push
			 $mPushNotification = $push->getPush(); 
			 
			 // obtener el token del objeto de base de datos

			 $devicetoken = self::get_token_administrador_id_lugar($id_lugar);		 

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
			
		 	 }catch (Exception $e){
		     return false;
			}

		}	

		
}

?>