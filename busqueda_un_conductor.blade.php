@extends ('layouts.inicio')
@section ('contenido')
@include('alerts.success')
@include('alerts.request')
@include('alerts.errors')
    <style>
      #map {
        height: 100%;
      }
      html, body {
        height: 100%;
        margin: 0;
        padding: 0;
      }
      .controls {
        margin-top: 10px;
        border: 1px solid transparent;
        border-radius: 2px 0 0 2px;
        box-sizing: border-box;
        -moz-box-sizing: border-box;
        height: 32px;
        outline: none;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
      }

      #pac-input {
        background-color: #fff;
        font-family: Roboto;
        font-size: 15px;
        font-weight: 300;
        margin-left: 12px;
        padding: 0 11px 0 13px;
        text-overflow: ellipsis;
        width: 300px;
      }

      #pac-input:focus {
        border-color: #4d90fe;
      }

      .pac-container {
        font-family: Roboto;
      }

      #type-selector {
        color: #fff;
        background-color: #4d90fe;
        padding: 5px 11px 0px 11px;
      }

      #type-selector label {
        font-family: Roboto;
        font-size: 13px;
        font-weight: 300;
      }
      #target {
        width: 345px;
      }
    </style>


<div class="row">	
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

		<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
		    <select name="buscar_un_conductor" class="form-control selectpicker" id="buscar_un_conductor" data-live-search="true" >
		     <option value="">BUSCAR CONDUCTOR...                                   </option>
		        @foreach($conductor_vehiculo as $bcv)
		        <option value="{{$bcv->ci}}">{{$bcv->nombre}} {{$bcv->paterno}} {{$bcv->materno}} - {{$bcv->codigo_empresa}} - {{$bcv->id_vehiculo}} - {{$bcv->celular}}</option>
		        @endforeach
		    </select>
		</div>
	



		<div class="col-lg-2 col-md-2 col-sm-2 col-xs-12" hidden="">
			<div class="pull-right" id="btn_not" hidden="">		
				<button type="button" class="btn btn-primary" onclick="Mostrar_Textos()"><i class="fa fa-check-circle" aria-hidden="true"></i> ENVIAR NOTIFICAION</button>	
			</div>		
		</div>
	</div>

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

	<div class="col-lg-10 col-md-10 col-sm-10 col-xs-12">
		<div id="mapa" style="width: 100%; height: 500px;"></div>
	</div>

	<div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">

		<table class="table table-striped table-bordered table-condensed table-hover" style="background: white;">
				<thead>
					<th ><center>Significado</center></th>
				</thead>
				<tbody>
               		<tr>
					<td align="left" data-toggle="tooltip" data-placement="top" title="Conductores Activos"><img src="images/activo.png" width="30px" height="40px"> Activo </td>
					</tr>
					<tr>
					<td align="left" data-toggle="tooltip" data-placement="top" title="Conductores que estan cubriendo una solicitud de pedido"><img src="images/ic_pedido.png" width="30px" height="40px"> En pedido </td>
					</tr>
					<tr>
					<td align="left" data-toggle="tooltip" data-placement="top" title="Conductores con estado desabilitado"><img src="images/inactivo.png" width="30px" height="40px"> Ocupado</td>
					</tr>
					<tr>
					<td align="left" data-toggle="tooltip" data-placement="top" title="Conductores bloqueados"><img src="images/ic_bloqueado.png" width="30px" height="40px"> Bloqueado </td>
					</tr>
					<tr>
					<td align="left" data-toggle="tooltip" data-placement="top" title="Conductores suspendido por (Deudas o Otros )"><img src="images/ic_suspendido.png" width="30px" height="40px"> Suspendido </td>
					</tr>
					</tbody>
				</tbody>
			</table>
		</div>

	</div>


	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">		
{{Form::open(array('url' => 'notificacion_conductores'))}}  
		<div id="notificacion" hidden="">
			<input type="hidden" name="titulo_msn" id="titulo_msn" class="form-control" value="TAXICORP"> 
			<textarea id="detalle_msn" name="detalle_msn" class="form-control" rows="5" placeholder="Ingrese el mensaje..."></textarea><br>
			<div class="pull-right">
				{!!Form::submit('ENVIAR',['class'=>'btn-sm btn-primary','id'=>'btn_notificacion','onclick'=>'btn_esconder()'])!!}
				
				<input type="button" value="CANCELAR" onclick="Esconder_Textos()" class="btn-sm btn-danger">								
			</div>		
		</div>
		<br>
		<br>
			
		<input type="button" id="btn_sel_todos_adm" hidden="" value="SELECCIONAR TODOS" onclick="Seleccionar_Todos()" class="btn-sm btn-warning">	
		<input type="button" id="btn_desel_todos_adm" hidden="" style="background: #F7BE81" value="DESELECCIONAR TODOS" onclick="Deseleccionar_Todos()" class="btn-sm btn-warning">	
  			
		<table class="table table-striped table-bordered table-condensed table-hover" style="background: white">
			<thead>
					<th><center></center></th>
					<th><center>CI</center></th>
					<th><center>CONDUCTOR</center></th>
					<th><center>PLACA</center></th>
					<th><center>MARCA</center></th>
					<th><center>CLASE</center></th>
					<th><center>CELULAR</center></th>
					<?php if (Auth::user()->privilegio != 0): ?>
					<th><center>EMPRESA</center></th>						
					<?php endif ?>
			</thead>

			<tbody align="center" id="body_conductor">

			</tbody>
		</table>
{!!Form::close()!!}
	</div>










</div>

<input type="hidden" name="privilegio" id="privilegio" value="{{Auth::user()->privilegio}}">


{!!Html::script('js/buscar_vehiculo_un_conductor.js')!!}
<script async defer
  src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBe-ZgLp9ix2Ejixdqtf6bHbFDTWq9k2IU">
</script>

@endsection
