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
	<link rel="shortcut icon" href="<?php echo base_url(); ?>favicon.ico" type="image/x-icon" >

	<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>extjs/resources/css/ext-all.css" />
 	<script type="text/javascript" src="<?php echo base_url(); ?>extjs/adapter/jquery/jquery.js"></script>
 	<script type="text/javascript" src="<?php echo base_url(); ?>extjs/adapter/jquery/ext-jquery-adapter.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>extjs/ext-all.js"></script>
    
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>extjs/lib/grid/grid-examples.css" />
	
	<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>extjs/lib/menu/menus.css" />
	
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>extjs/lib/form/forms.css"/>
	<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>extjs/lib/form/combos.css" />

	<link href="<?= $this->config->item('base_url'); ?>skins/skin001/css/abas_verde.css" rel="stylesheet" type="text/css">
	<link href="<?= $this->config->item('base_url'); ?>skins/skin001/css/default.css" rel="stylesheet" type="text/css">

	<script type='text/javascript' src='<?= $this->config->item('base_url'); ?>js/default.js'></script>
	<script type='text/javascript' src='<?= $this->config->item('base_url'); ?>skins/skin001/sort_table/sortabletable.js'></script>
	<link type='text/css' rel='StyleSheet' href='<?= $this->config->item('base_url'); ?>skins/skin001/sort_table/sortabletable.css'>

	<script src="<?= $this->config->item('base_url'); ?>js/jquery-plugins/jquery.maskedinput-1.2.js" type="text/javascript"></script>
	<script src="<?= $this->config->item('base_url'); ?>js/jquery-plugins/jquery-numeric-pack.js" type="text/javascript"></script>

	<!-- calendar stylesheet -->
	<link rel="stylesheet" type="text/css" media="all" href="<?php echo base_url() ?>js/jscalendar/calendar-win2k-cold-1.css" title="win2k-cold-1" />
	<script type="text/javascript" src="<?php echo base_url() ?>js/jscalendar/calendar.js"></script>
	<script type="text/javascript" src="<?php echo base_url() ?>js/jscalendar/lang/calendar-br.js"></script>
	<script type="text/javascript" src="<?php echo base_url() ?>js/jscalendar/calendar-setup.js"></script>

	<!-- style exceptions for IE 6 -->
	<!--[if IE 6]>
	<style type="text/css">
		.fg-menu-ipod .fg-menu li { width: 95%; }
		.fg-menu-ipod .ui-widget-content { border:0; }
	</style>
	<![endif]-->	

    <script type="text/javascript">    
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
    </script>

	<script>
	Ext.onReady(function() {

		Ext.onReady(function() {

			// Criamos o menu
			var atividade = new Ext.menu.Menu({ id: 'atividade', items: <?php echo menu_extjs_start(8); ?> });
			var cadastro = new Ext.menu.Menu({ id: 'cadastro', items: <?php echo menu_extjs_start(40); ?> });
			var ecrm = new Ext.menu.Menu({ id: 'ecrm', items: <?php echo menu_extjs_start(4); ?> });
			var gestao = new Ext.menu.Menu({ id: 'gestao', items: <?php echo menu_extjs_start(29); ?> });
			var intranet = new Ext.menu.Menu({ id: 'intranet', items: <?php echo menu_extjs_start(281); ?> });
			var planos = new Ext.menu.Menu({ id: 'planos', items: <?php echo menu_extjs_start(16); ?> });
			var servicos = new Ext.menu.Menu({ id: 'servicos', items: <?php echo menu_extjs_start(31); ?> });

			var toolBar = new Ext.Toolbar({
				id:'toolBar'
				, renderTo: 'MenuDiv'
				, items:[
					new Ext.Button({text:'Atividades',menu:atividade})
					, new Ext.Button({text:'Cadastros',menu:cadastro})
					, new Ext.Button({text:'e-CRM',menu:ecrm})
					, new Ext.Button({text:'Gestão',menu:gestao})
					, new Ext.Button({text:'Intranet',menu:intranet})
					, new Ext.Button({text:'Planos',menu:planos})
					, new Ext.Button({text:'Serviços',menu:servicos})
				]
			});

			var nossosite = new Ext.menu.Menu({ 
				id:'nossosite'
				, items: [
					{text: 'Fundação CEEE', handler:function(){window.open('https://www.fundacaoceee.com.br');}}
					, {text: 'CEEEPrev', handler:function(){window.open('http://www.ceeeprev.com.br');}}
					, {text: 'CRMPrev', handler:function(){window.open('http://www.crmprev.com.br');}}
					, {text: 'SENGE Previdência', handler:function(){window.open('http://www.sengeprevidencia.com.br');}}
					, {text: 'SINPRORS Previdência', handler:function(){window.open('http://www.sinprorsprevidencia.com.br');}}
				]
			});
			var ferramenta = new Ext.menu.Menu({ 
				id:'ferramenta'
				, items: [
					{text: 'Anotações', handler:function(){window.open('http://www.e-prev.com.br/controle_projetos/cad_anotacoes.php');}}
					, {text: 'Consultas', handler:function(){window.open('http://www.e-prev.com.br/controle_projetos/consulta0.php');}}
					, {text: 'Pesquisas', handler:function(){window.open('http://www.e-prev.com.br/controle_projetos/intranet_pesquisa.php');}}
					, {text: 'Relatórios', handler:function(){window.open('http://www.e-prev.com.br/controle_projetos/relatorio0.php');}}
					, {text: 'Skins', handler:function(){window.open('http://www.e-prev.com.br/controle_projetos/at_skin.php?r=1&h=');}}
				]
			});
			var favorito = new Ext.menu.Menu({ 
				id:'favorito'
				, items: [
					{text: 'Workspace', handler:function(){window.open('http://www.e-prev.com.br/controle_projetos/workspace.php');}}
				]
			});
			var acessorio = new Ext.menu.Menu({ 
				id:'acessorio'
				, items: [
					{text: 'Calculadora', handler:function(){window.open('http://www.e-prev.com.br/controle_projetos/calc.htm');}}
					, {text: 'Calendário', handler:function(){window.open('http://www.e-prev.com.br/controle_projetos/cal_agenda.php?r=1&h=C');}}
				]
			});

			var toolBar2 = new Ext.Toolbar({
				id:'toolBar2'
				, renderTo:'AtalhosDiv'
				, items:[
					new Ext.Button({text:'Nossos Sites', menu:nossosite})
					, new Ext.Button({text:'Ferramentas', menu:ferramenta})
					, new Ext.Button({text:'Favoritos', menu:favorito})
					, new Ext.Button({text:'Acessórios', menu:acessorio})
				]
			});

		});

	});
    </script>

    <style>
    .x-toolbar {
		background: none;
		border-color:#A9BFD3;
		border-style:none;
		border-width:0 0 1px;
		display:block;
		padding:2px;
		position:relative;
	}
    </style>

  <style>
	.topo-primeiro{
		margin-top:5px;margin-left:100px;heigth:45px; border-width: 1px; border-style: none;
		font-family:Arial, Verdana;
		font-size:10px;
		font-weight: bold;
		white-space: nowrap;
	}

	.topo-segundo{
		margin-top:11px;
		margin-left:10px;
		heigth:50px; 
		border-width: 1px; 
		border-style: none;
		font-family:Arial;
		font-size:30px;
		color:white;
		font-weight: normal;
		white-space: nowrap;
	}

	.topo-terceiro{
		margin-top:10px;
		margin-left:40px;
		heigth:50px; 
		border-width: 1px; 
		border-style: none;
		font-family:Arial, Verdana;
		font-size:10px;
		font-weight: bold;
		white-space: nowrap;
	}

	.links2
	{
		color:#000000;
		font-family:Arial, Verdana;
		font-size:10px;
		font-style:normal;
		font-weight:bold;
		line-height:20px;
		text-decoration:none;
	}

	.menu-fly{ border: 1px none #327E04; font-size:10px; font-weight: bold; }
	.ui-corner-all { -moz-border-radius: 4px; -webkit-border-radius: 4px; font-family:Arial, Verdana; font-size:11px; }

  </style>
 </HEAD>
 <BODY topmargin="0" leftmargin="0">

 <table cellpadding="0" cellspacing="0" width="100%" border="0">
  <tr>
	  <td width="112" valign="top"><img src="<?php echo base_url() ?>skins/skin001/menuant/logo.jpg" /></td>
	  <td background="<?php echo base_url() ?>skins/skin001/menuant/logobackground.jpg" valign="top">

		<div class="topo-primeiro"><div id="AtalhosDiv" style="display:;"></div></div>

		<table cellpadding="0" cellspacing="0" width="100%" border="0">
		<tr>
		<td>
			<div id="page_title" class="topo-segundo"><?php if( isset($topo_titulo) ) echo($topo_titulo); else echo "e-prev"; ?></div>
		</td>
		<td valign="bottom">
			<a style="color:white;font-size:12px;" href="<?php echo base_url_eprev(); ?>cad_recurso_workspace.php"><?php echo $this->session->userdata('usuario') . ' - ' . $this->session->userdata('divisao') ?> - </a><a style="color:white;font-size:12px;" href="<?php echo base_url().index_page(); ?>/login/sair">sair</a>
		</td>
		</tr>
		</table>

		<div class="topo-terceiro" title="Clique para abrir o menu">

			<div id="MenuDiv"></div>

		</div>

	  </td>
  </tr>
  </table>

	<div id="nossosite_content" style="display:none;">
	<ul>
		<li><a href="http://www.fundacaoceee.com.br">Fundação CEEE</a></li>
		<li><a href="http://www.ceeeprev.com.br">CEEEPrev</a></li>
		<li><a href="http://www.crmprev.com.br">CRMPrev</a></li>
		<li><a href="http://www.sengeprevidencia.com.br">SENGE Previdência</a></li>
		<li><a href="http://www.sinprorsprevidencia.com.br">SINPRORS Previdência</a></li>
	</ul>
	</div>

	<div id="ferramenta_content" style="display:none;">
	<ul>
		<li><a href="http://www.e-prev.com.br/controle_projetos/cad_anotacoes.php">Anotações</a></li>
		<li><a href="http://www.e-prev.com.br/controle_projetos/consulta0.php">Consultas</a></li>
		<li><a href="http://www.e-prev.com.br/controle_projetos/intranet_pesquisa.php">Pesquisas</a></li>
		<li><a href="http://www.e-prev.com.br/controle_projetos/relatorio0.php">Relatórios</a></li>
		<li><a href="http://www.e-prev.com.br/controle_projetos/at_skin.php?r=1&h=">Skins</a></li>
	</ul>
	</div>

	<div id="favorito_content" style="display:none;">
	<ul>
		<li><a href="http://www.e-prev.com.br/controle_projetos/workspace.php">Workspace</a></li>
	</ul>
	</div>

	<div id="acessorio_content" style="display:none;">
	<ul>
		<li><a href="http://www.e-prev.com.br/controle_projetos/calc.htm">Calculadora</a></li>
		<li><a href="http://www.e-prev.com.br/controle_projetos/cal_agenda.php?r=1&h=C">Calendário</a></li>
	</ul>
	</div>

	<input id="root" name="root" type="hidden" value="<?= $this->config->item('base_url'); ?>" />
  	<input id="base_url" name="base_url" type="hidden" value="<?= $this->config->item('base_url'); ?>index.php/" />
	<input type="hidden" name="current_page" id="current_page" value="0" />

<div id="conteudo">  <!-- DIV CONTEUDO -->
<br>