<?php
include 'inc/sessao.php';
include 'inc/conexao.php';
include 'inc/ePrev.Enums.php';

include 'oo/start.php';
using(array('projetos.contribuicao_controle'));

$tipo = $_REQUEST['tipo']; // deve ser "mensal" ou "primeiro"
$ano = $_REQUEST['ano'];
$mes = $_REQUEST['mes'];

$lista = contribuicao_controle::select_1( $tipo, 8, $ano, $mes );
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
 <HEAD>
  <TITLE>(SINPRO) SINPRORS Previdência Lista de Cobranças Geradas </TITLE>
  <META NAME="Generator" CONTENT="EditPlus">
  <META NAME="Author" CONTENT="">
  <META NAME="Keywords" CONTENT="">
  <META NAME="Description" CONTENT="">
  	<!-- SORT TABLE -->
	<script type='text/javascript' src='inc/sort_table/sortabletable.js'></script>
	<link type='text/css' rel='StyleSheet' href='inc/sort_table/sortabletable.css'>
	<!-- SORT TABLE -->
	
	<script>
		function configure_table()
		{
			var ob_resul = new SortableTable(document.getElementById("table-1"),["CaseInsensitiveString", "CaseInsensitiveString", "CaseInsensitiveString", "CaseInsensitiveString", "CaseInsensitiveString"]);
			ob_resul.onsort = function ()
			{
				var rows = ob_resul.tBody.rows;
				var l = rows.length;
				for (var i = 0; i < l; i++)
				{
					removeClassName( rows[i], i % 2 ? "sort-par" : "sort-impar" );
					addClassName( rows[i], i % 2 ? "sort-impar" : "sort-par" );
				}
			};
			ob_resul.sort(0, false);				
		}
	</script>
 </HEAD>

 <BODY onload="configure_table();">
 
	<table>
	<tr>
		<td><img src="img/logo_eprev.png" /></td>
		<td style="font-family:arial;padding-left:20px;"><b>LISTA DE <?php echo $_REQUEST['tipo'];?> DO PLANO SINPRORS para competência - <?php echo $mes . '/' . $ano; ?> (SINPRO)</b></td>
	</tr>
	</table>
	<br />
	<b>Total de registros:</b> <?php echo sizeof($lista); ?>
	<table class="sort-table" id="table-1" align="center" width="100%" cellspacing="2" cellpadding="2">
    	<thead>
		<tr>
			<td><b>RE</b></td>
			<td><b>Nome</b></td>
			<td><b>Email</b></td>
			<td><b>Forma</b></td>
		</tr>
    	</thead>
		<tbody>
		<? foreach( $lista as $item ) : ?>
		<tr onmouseover="sortSetClassOver(this);" onmouseout="sortSetClassOut(this);">
			<td align="center"><?php echo $item['cd_empresa'] . '/' . $item['cd_registro_empregado'] . '/' . $item['seq_dependencia'] ?></td>
			<td align="left"><?php echo $item['nome']; ?></td>
			<td align="left"><?php echo $item['email']; ?></td>
			<td align="left"><?php echo $item['ds_contribuicao_controle_tipo']; ?></td>
		</tr>
		<? endforeach; ?>
	</tbody>
	</table>
	
	<br />
	<!-- <center><a href="javascript:window.close();" style="font-family:arial;padding-left:20px;color:black;"><b>FECHAR</b></a></center> -->

 </BODY>
</HTML>