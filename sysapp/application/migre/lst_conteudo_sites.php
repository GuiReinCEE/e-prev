<?php
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');

	header( 'location:'.base_url().'index.php/ecrm/conteudo_site/index/'.$_REQUEST['cs'].'/'.$_REQUEST['ed'] );
?>