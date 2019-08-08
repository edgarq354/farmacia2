<?php

class Push //Almacena las Notificaciones
{
    //notificacion  titulo
    private $title;
  
    //notification message 
    private $message;
    
    //notification image url 
    private $image;
    // tipo_cliente ...1 moto,   2 usuario.
    private $cliente;
    private $id_pedido;
    private $nombre;
    private $latitud;
    private $longitud;
    private $tipo;
    private $pedido;
    private $fecha;
    private $hora;
    private $indicacion;
    private $monto_total;
    private $distancia;
    private $clase_vehiculo;
    private $tipo_pedido_empresa;

    //adicional
    private $latitud_final;
    private $longitud_final;
    private $direccion_final;
    private $detalle;
    //
    private $tipo_pedido;
    private $id_usuario;
    private $id_conductor;
    private $estado;
    private $id_chat;
    private $yo;
 
    //initializing values in this constructor
    public function Push($title, $message, $image,$cliente,$id_pedido,$nombre,$latitud,$longitud,$tipo) {
         $this->title = $title;
         $this->message =$message ; 
         $this->image = $image; 
         $this->cliente=$cliente;
         $this->id_pedido=$id_pedido;
         $this->nombre=$nombre;
         $this->latitud=$latitud;
         $this->longitud=$longitud;
         $this->tipo=$tipo;
         $this->pedido="";
         date_default_timezone_set("America/La_Paz") ;
         $this->fecha =date("d-m-Y",time());
         $this->hora=date("H:i:s",time());
         $indicacion="";
         $this->monto_total="";
         $this->distancia="";
         $this->clase_vehiculo="1";
         $this->tipo_pedido_empresa="0";
         //adicional
         $this->latitud_final=0;
         $this->longitud_final=0;
         $this->direccion_final="";
         $this->detalle="";
         //adicional
         $this->tipo_pedido="0";
         $this->estado="0";
         $this->id_usuario="0";
         $this->id_conductor="0";
         $this->id_chat="0";
         $this->yo="0";


    }

    public function setEstado($estado) {
       $this->estado=$estado;
    }

    public function setId_usuario($id_usuario) {
       $this->id_usuario=$id_usuario;
    }

    public function setId_conductor($id_conductor) {
       $this->id_conductor=$id_conductor;
    }

    public function set_pedido($pedido) {
         $this->pedido=$pedido;
    }

    public function setId_chat($value) {
         $this->id_chat=$value;
    }

   
    public function setYo($value) {
         $this->yo=$value;
    }

    public function setTipo_pedido($value) {
         $this->tipo_pedido=$value;
    }
    

 //fin adicional

     public function setLatitud_final($value)
    {
        $this->latitud_final=$value;
    }

    public function setLongitud_final($value)
    {
        $this->longitud_final=$value;
    }

    public function setDireccion_final($value)
    {
        $this->direccion_final=$value;
    }

     public function setDetalle($value)
    {
        $this->detalle=$value;
    }
//adicional


    public function setDistancia($distancia) {
         
         $this->distancia=$distancia;
    }
        public function setIndicacion($indicacion) {
         $this->indicacion=$indicacion;
    }
    
    public function setClase_vehiculo($clase_vehiculo) {
         $this->clase_vehiculo=$clase_vehiculo;
    }
    public function setTipo_pedido_empresa($tipo_pedido_empresa) {
         $this->tipo_pedido_empresa=$tipo_pedido_empresa;
    }
    public function setMonto_total($monto_total) {
         $this->monto_total=$monto_total;
    }
    // obtener la notificación push
    public  function getPush() {
        $res = array();
        $res['data']['title'] = $this->title;
        $res['data']['message'] = $this->message;
        $res['data']['image'] = $this->image;
        $res['data']['cliente'] = $this->cliente;
        $res['data']['id_pedido'] = $this->id_pedido;
         $res['data']['nombre'] = $this->nombre;
          $res['data']['latitud'] = $this->latitud;
           $res['data']['longitud'] = $this->longitud;
           $res['data']['tipo'] = $this->tipo;
           $res['data']['pedido'] = $this->pedido;
           $res['data']['fecha'] = $this->fecha;
           $res['data']['hora'] = $this->hora;
           $res['data']['indicacion'] = $this->indicacion;
           $res['data']['monto_total'] = $this->monto_total;
           $res['data']['distancia'] = $this->distancia;
           $res['data']['clase_vehiculo'] = $this->clase_vehiculo;
           $res['data']['tipo_pedido_empresa'] = $this->tipo_pedido_empresa;
           //adicinal
           $res['data']['latitud_final'] = $this->latitud_final;
           $res['data']['longitud_final'] = $this->longitud_final;
           $res['data']['direccion_final'] = $this->direccion_final;
           $res['data']['detalle'] = $this->detalle;
           //
           $res['data']['tipo_pedido'] = $this->tipo_pedido;
           $res['data']['id_usuario'] = $this->id_usuario;
           $res['data']['id_conductor'] = $this->id_conductor;
           $res['data']['estado'] = $this->estado;
           $res['data']['id_chat'] = $this->id_chat;
           $res['data']['yo'] = $this->yo;
        return $res; 
    }
}
/*
La clace inicializa las variables necesarias 
para empujar en el constructor, y nos devuelve una matriz con los datos necesarios en el método getPush ().

*/

?>

