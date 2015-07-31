<?php 

include ("conf.php");
if (!$basedatos) {
	echo "<br><CENTER><span class=\"tex_menu\">
				                               Problemas de conexion con la base de datos.
				                               </CENTER>";
	exit;
}

	echo "<select class='form-control' id='select_curso' name='select_curso'>";
	echo "<option value='' selected>[ Seleccione ]</option>";
	$sql = "SELECT DISTINCT nombre FROM curso ORDER by nombre ASC";
	$rs = mysql_query($sql) or die("La consulta fall&oacute;: ".mysql_error());
	while ($row_rs = @mysql_fetch_array($rs)){
		$curso = $row_rs[0];
					    				//$idCurso = $row_rs[1];
			echo "<option value='".$curso."'>".$curso."</option>";
			}
			echo "</select>";
?>