<?php
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	
	header( 'location:'.base_url().'index.php/ecrm/atendimento_lista/index/'.(trim($_REQUEST['RE_GA']) == '' ? '' : $_REQUEST['RE_GA']) );
?>