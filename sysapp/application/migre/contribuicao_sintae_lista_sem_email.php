<?php
include 'inc/sessao.php';
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
 <HEAD>
  <TITLE> SINPRORS Previdência Lista de Cobranças Geradas (SINTAE)</TITLE>
  <META NAME="Generator" CONTENT="EditPlus">
  <META NAME="Author" CONTENT="">
  <META NAME="Keywords" CONTENT="">
  <META NAME="Description" CONTENT="">
  	<!-- SORT TABLE -->
	<script type='text/javascript' src='inc/sort_table/sortabletable.js'></script>
	<link type='text/css' rel='StyleSheet' href='inc/sort_table/sortabletable.css'>
	<!-- SORT TABLE -->

	<script>
	function copiar()
	{
		document.getElementById('content').innerHTML = window.opener.document.getElementById('participantes_sem_email').innerHTML;
	}
	</script>

 </HEAD>

 <BODY onload="copiar();">
 
	<div id="content"></div>

 </BODY>
</HTML>