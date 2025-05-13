<?php
include_once('inc/sessao.php');
include_once('inc/conexao.php');

header('location:'.base_url().'index.php/ecrm/atualiza_intranet/index/'.$_REQUEST['div']);
?>