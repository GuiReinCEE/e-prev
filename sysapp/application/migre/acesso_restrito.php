<?php
include_once('inc/sessao.php');
include_once('inc/conexao.php');
include_once('inc/class.TemplatePower.inc.php');

$tpl = new TemplatePower('tpl/tpl_acesso_restrito.html');
$tpl->prepare();
$tpl->assign('n', $n);

// --------------------------------------------------------- inicialização do skin das telas:
$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
include_once('inc/skin.php');
// ---------------------------------------------------------

$tpl->assign('usuario', $N);
$tpl->assign('divsao', $D);
$tpl->assign('imagem', $IMG);
$tpl->newBlock('cadastro');
$tpl->printToScreen();
?>