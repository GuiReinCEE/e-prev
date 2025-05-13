<?php
header('Content-Type: text/html; charset=ISO-8859-1');
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
  <script type='text/javascript' src='<?= $this->config->item('base_url'); ?>skins/skin001/sort_table/sortabletable.js'></script>
  <link type='text/css' rel='StyleSheet' href='<?= $this->config->item('base_url'); ?>skins/skin001/sort_table/sortabletable.css'>

	<script type="text/javascript" src="<?php echo base_url() ?>js/mootools_1.11.js"></script>
	<script type="text/javascript" src="<?php echo base_url() ?>js/imask.js"></script>
	<script type="text/javascript" src="<?php echo base_url() ?>js/fvalidator.js"></script>

	<script type='text/javascript' src='<?= $this->config->item('base_url'); ?>js/prototype_1_6_0.js'></script>
	<script type='text/javascript' src='<?= $this->config->item('base_url'); ?>js/effects.js'></script>

	<!-- calendar stylesheet -->
	<link rel="stylesheet" type="text/css" media="all" href="<?php echo base_url() ?>js/jscalendar/calendar-win2k-cold-1.css" title="win2k-cold-1" />
	
	<!-- main calendar program -->
	<script type="text/javascript" src="<?php echo base_url() ?>js/jscalendar/calendar.js"></script>
	
	<!-- language for the calendar -->
	<script type="text/javascript" src="<?php echo base_url() ?>js/jscalendar/lang/calendar-br.js"></script>
	
	<!-- the following script defines the Calendar.setup helper function, which makes
	 adding a calendar a matter of 1 or 2 lines of code. -->
	<script type="text/javascript" src="<?php echo base_url() ?>js/jscalendar/calendar-setup.js"></script>
	
	<script type="text/javascript">
		//<![CDATA[
		var Page = {
			initialize: function() {

				new iMask({
					onFocus: function(obj) {
						obj.setStyles({"background-color":"#ff8", border:"1px solid #880"});
					},
		
					onBlur: function(obj) {
						obj.setStyles({"background-color":"#fff", border:"1px solid #ccc"});
					},
		
					onValid: function(event, obj) {
						obj.setStyles({"background-color":"#8f8", border:"1px solid #080"});
					},
		
					onInvalid: function(event, obj) {
						if(!event.shift) {
							obj.setStyles({"background-color":"#f88", border:"1px solid #800"});
						}
					}
				});
			}
		};
		
		window.onDomReady(Page.initialize);
		//]]>
	</script>

  <!-- MENU -->
  <link rel="stylesheet" href="<?= base_url() ?>skins/skin001/css/verde.css" type="text/css" media="all">
  <script type='text/javascript' src='<?= $this->config->item('base_url'); ?>js/menu.js'></script>
  <!-- // MENU -->

  <script>
  	function realizar_login()
  	{
  		var f = document.forms[0];
  		f.action = $F('base_url') + 'login/entrar';
  		f.submit();
  	}
  	function buscar()
  	{
  		var f = document.forms[0];
  		f.action = $F('base_url') + 'geral/buscar';
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
  </script>

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
				
				<? if($this->session->userdata('usuario')!='') : ?>
					<b><?= $this->session->userdata('usuario'); ?></b> [ <?php echo anchor('login/sair', 'SAIR'); ?> ] |
				<? endif; ?>
				
				<a href="#">Nossos sites <img src="<?= base_url() ?>skins/skin001/img/mais.gif" border="0"></a> |
				<a href="#">Favoritos <img src="<?= base_url() ?>skins/skin001/img/mais.gif" border="0"></a> |
				<a href="#">Mais usadas <img src="<?= base_url() ?>skins/skin001/img/mais.gif" border="0"></a>
			</div>
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