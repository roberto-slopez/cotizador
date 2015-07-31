<?php 

?>

<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Cotizador en línea</title>
	<link href="css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
	<p>
	<div class="row">
  		<div class="col-md-2"></div>

  			<div class="col-md-8">
  			<img src="img/logo.png" alt="" class="img-thumbnail">
  				<div class="page-header">
  					<h1>Cotizador en línea</h1>
				</div>
				<div class="alert alert-warning" role="alert">
  					<a href="#" class="alert-link">Los valores diferentes a pesos se liquidan a la tasa CB del día. Sujetos a cambios sin previo aviso.</a>
				</div>

				<form action="include/print.php" method="post">
				<div class="row">
					<!-- Primer segmento -->
					<div class="col-md-4">
							<h4>Curso</h4>
						  <?php 
							include ('include/consultas.php');
							?>
						  <h4>País</h4>
						  <select class="form-control" name="select_pais" id="select_pais" disabled>
						    <option value="" disabled selected>Seleccione</option>
						    <option>2</option>
						    <option>3</option>
						    <option>4</option>
						    <option>5</option>
						  </select>
						  <h4>Ciudad</h4>
						  <select class="form-control" id="select_ciudad">
						    <option>1</option>
						    <option>2</option>
						    <option>3</option>
						    <option>4</option>
						    <option>5</option>
						  </select>
						  <h4>Centro</h4>
						  <select class="form-control" id="select_centro">
						    <option>1</option>
						    <option>2</option>
						    <option>3</option>
						    <option>4</option>
						    <option>5</option>
						  </select>
						  <h4>Semanas</h4>
						  <select class="form-control" id="select_semanas">
						    <option>1</option>
						    <option>2</option>
						    <option>3</option>
						    <option>4</option>
						    <option>5</option>
						  </select>
						  <h4>Lecciones por semana</h4>
						  <select class="form-control" id="select_lecciones">
						    <option>1</option>
						    <option>2</option>
						    <option>3</option>
						    <option>4</option>
						    <option>5</option>
						  </select>
						  <h4>Jornada de lecciones</h4>
						  <select class="form-control" id="select_jornada">
						    <option>1</option>
						    <option>2</option>
						    <option>3</option>
						    <option>4</option>
						    <option>5</option>
						  </select>
					</div>
					<!-- Segundo segmento -->
					<div class="col-md-4" id="paso_2">
						<h4>Alojamiento</h4>
						<select class="form-control" id="select_alojamiento">
						  <option>1</option>
						  <option>2</option>
						  <option>3</option>
						  <option>4</option>
						  <option>5</option>
						</select>
						<h4>Semanas de alojamiento</h4>
						  <select class="form-control" id="select_semanas_alojamiento">
						    <option>1</option>
						    <option>2</option>
						    <option>3</option>
						    <option>4</option>
						    <option>5</option>
						  </select>
						  <h4>Tipo de alojamiento</h4>
						  <select class="form-control" id="select_tipo_alojamiento">
						    <option>1</option>
						    <option>2</option>
						    <option>3</option>
						    <option>4</option>
						    <option>5</option>
						  </select>
						  <h4>Tipo de habitación</h4>
						  <select class="form-control" id="select_tipo_habitacion">
						    <option>1</option>
						    <option>2</option>
						    <option>3</option>
						    <option>4</option>
						    <option>5</option>
						  </select>
						  <h4>Alimentación</h4>
						  <select class="form-control" id="select_alimentacion">
						    <option>1</option>
						    <option>2</option>
						    <option>3</option>
						    <option>4</option>
						    <option>5</option>
						  </select>
						  <h4>Traslado</h4>
						  <select class="form-control" id="select_traslado">
						    <option>1</option>
						    <option>2</option>
						    <option>3</option>
						    <option>4</option>
						    <option>5</option>
						  </select>
						  <h4>Seguro</h4>
						  <select class="form-control" id="select_seguro">
						    <option>1</option>
						    <option>2</option>
						    <option>3</option>
						    <option>4</option>
						    <option>5</option>
						  </select>
					</div>
					<!-- Tercer segmento -->
					<div class="col-md-4" id="paso_3">
						<h4>Moneda</h4>
						<select class="form-control" id="select_moneda" hidden>
						  <option>COP</option>
						  <option>USD</option>
						  <option>EURO</option>
						 </select>
						<p></p>
						<ul class="list-group" id="list_valores">
  							<li class="list-group-item">
    						<span class="badge">$0</span>
    						CURSO
  							</li>
  							<li class="list-group-item">
    						<span class="badge">$0</span>
    						REGISTRO
  							</li>
  							<li class="list-group-item">
    						<span class="badge">$0</span>
    						MATERIALES
  							</li>
  							<li class="list-group-item">
    						<span class="badge">$0</span>
    						ESTADIA
  							</li>
  							<li class="list-group-item">
    						<span class="badge">$0</span>
    						TRASLADO
  							</li>
  							<li class="list-group-item">
    						<span class="badge">$0</span>
    						FINANCIEROS
  							</li>
  							<li class="list-group-item">
    						<span class="badge">$0</span>
    						ASISTENCIA
  							</li>
  							<li class="list-group-item">
    						<span class="badge">$0</span>
    						VISA
  							</li>
  							<li class="list-group-item">
    						<span class="badge">$0</span>
    						TOTAL
  							</li>
						</ul>
						<p></p>
						<button type="submit" class="btn btn-success" id="boton_imprimir">
							<span class="glyphicon glyphicon-print" aria-hidden="true"></span> IMPRIMIR
						</button>
					</div>
					
				</div>
				</form>

  			</div>

  		<div class="col-md-2"></div>
	</div>

	
	<footer>
		<p></p>
		<p></p>
	</footer>
	
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<script src="js/cotizador.js"></script>
</body>
</html>