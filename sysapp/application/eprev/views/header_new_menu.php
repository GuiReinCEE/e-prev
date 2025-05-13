<?php
header('Content-Type: text/html; charset=ISO-8859-1');
//echo $this->session->userdata( 'cd_menu' );
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
 <HEAD>

  <TITLE> ePrev (with CI) </TITLE>
  <META NAME="Generator" CONTENT="EditPlus">
  <META NAME="Author" CONTENT="">
  <META NAME="Keywords" CONTENT="">
  <META NAME="Description" CONTENT="">
  <link rel="shortcut icon" href="<?php echo $this->config->item('base_url'); ?>favicon.ico" type="image/x-icon" >
  
  <link href="<?= $this->config->item('base_url'); ?>skins/skin001/css/abas.css" rel="stylesheet" type="text/css">
  <link href="<?= $this->config->item('base_url'); ?>skins/skin001/css/default.css" rel="stylesheet" type="text/css">

  <script type='text/javascript' src='<?= $this->config->item('base_url'); ?>js/default.js'></script>
  <script type='text/javascript' src='<?= $this->config->item('base_url'); ?>skins/skin001/sort_table/sortabletable.js'></script>
  <link type='text/css' rel='StyleSheet' href='<?= $this->config->item('base_url'); ?>skins/skin001/sort_table/sortabletable.css'>

	<script src="<?= $this->config->item('base_url'); ?>js/jquery-1.2.6.min.js" type="text/javascript"></script>
	<script src="<?= $this->config->item('base_url'); ?>js/jquery-plugins/jquery.maskedinput-1.2.js" type="text/javascript"></script>
	<script src="<?= $this->config->item('base_url'); ?>js/jquery-plugins/jquery-numeric-pack.js" type="text/javascript"></script>

	<!-- calendar stylesheet -->
	<link rel="stylesheet" type="text/css" media="all" href="<?php echo base_url() ?>js/jscalendar/calendar-win2k-cold-1.css" title="win2k-cold-1" />

	<!-- main calendar program -->
	<script type="text/javascript" src="<?php echo base_url() ?>js/jscalendar/calendar.js"></script>

	<!-- language for the calendar -->
	<script type="text/javascript" src="<?php echo base_url() ?>js/jscalendar/lang/calendar-br.js"></script>

	<!-- the following script defines the Calendar.setup helper function, which makes
	 adding a calendar a matter of 1 or 2 lines of code. -->
	<script type="text/javascript" src="<?php echo base_url() ?>js/jscalendar/calendar-setup.js"></script>

  <!-- MENU -->
  <link rel="stylesheet" href="<?= base_url() ?>skins/skin001/css/verde.css" type="text/css" media="all">
  <script type='text/javascript' src='<?= $this->config->item('base_url'); ?>js/menu.js'></script>
  <!-- // MENU -->

  <script>
  	function realizar_login()
  	{
  		var f = document.forms[0];
  		f.action = document.getElementById('base_url').value + 'login/entrar';
  		f.submit();
  	}
  	function buscar()
  	{
  		var f = document.forms[0];
  		f.action = document.getElementById('base_url').value + 'geral/buscar';
  		f.submit();
  	}
  	function enterBuscar(e)
  	{
  		if(e.keyCode==13)
  		{
  			buscar();
	  		return false;
  		}
  		else
  		{
  			return true;
  		}
  	}
  	
  	/*$(document).ready( function(){ 
		$("#loading").ajaxStart( function(){ $("#loading").show(); } ).ajaxStop( function(){ $("#loading").hide(); } ); 
	});*/ 

  </script>
  
  <style>
  
	.links2
	{
		color:#000000;
		font-family:Verdana,Arial,Helvetica,sans-serif;
		font-size:10px;
		font-style:normal;
		font-weight:bold;
		line-height:20px;
		text-decoration:none;
	}
  
  </style>

 </HEAD>

<BODY leftmargin="0" topmargin="0" bgcolor="" onload="getMenu('<?= $this->session->userdata('cd_menu') ?>', '')">

<div id="general"> <!-- GENERAL -->

<? echo form_open(''); ?>

	<div id="header">
		<div id="header_logo">
			<img src="<?= base_url() ?>skins/skin001/img/logo_eprev.png" border="0">
		</div>
		<div id="header_opcao">
			<div id="header_opcao_menu">
				<table>
				<tr>
					<td>
						<? if($this->session->userdata('usuario')!='') : ?>
							<b><?= $this->session->userdata('usuario'); ?></b> [ <?php echo anchor('login/sair', 'SAIR'); ?> ] |
						<? endif; ?>
					</td>
					<td>
						<div id="clicar_aqui">Nossos Sites <img src="<?= base_url() ?>skins/skin001/img/mais.gif" border="0"></div>
					</td>
					<td>|</td>
					<td>
						<div id="favoritos">Favoritos <img src="<?= base_url() ?>skins/skin001/img/mais.gif" border="0"></div>
					</td>
					<td>|</td>
					<td>
						<div id="mais_usados">Mais usados <img src="<?= base_url() ?>skins/skin001/img/mais.gif" border="0"></div>
					</td>
				</tr>
				</table>
			</div>
			<BR>
			<BR>
			<BR>
			<div id="header_opcao_busca">
				<input type="text" 
					id="header_opcao_campo" 
					name="keyword"
					onkeypress="return enterBuscar(event);"
				/>
				<input type="button"
					id="header_opcao_botao"
					value="Buscar"
					onclick="buscar();"
				/>
			</div>
		</div>
	</div>

	<div id="menu" style="padding-bottom:30px; clear:both;"></div>

	<input id="root" name="root" type="hidden" value="<?= $this->config->item('base_url'); ?>" />
	<input id="base_url" name="base_url" type="hidden" value="<?= $this->config->item('base_url'); ?>index.php/" />
	<input type="hidden" name="current_page" id="current_page" value="0" />

<div id="conteudo" style="clear:both;">  <!-- DIV CONTEUDO -->
<br>