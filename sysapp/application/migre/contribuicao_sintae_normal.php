<?php
    include_once('inc/sessao.php');
    include_once('inc/conexao.php');
    include_once('inc/class.TemplatePower.inc.php');
	
	header( 'location:'.base_url().'index.php/planos/contribuicao_instituidor_mensal/index/8/10');
	exit;		

    $tpl = new TemplatePower('tpl/tpl_contribuicao_sintae_normal.html');

    $tpl->prepare();

    $PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
    include_once('inc/skin.php');

    $tpl->assign('usuario', $N);
    $tpl->assign('divsao', $D);

    $tpl->assign('mes_inicial', date('m'));
    $tpl->assign('ano_inicial', date('Y'));

    if( $D!='GF' && $D!='GI' )
    {
        header( 'Location: acesso_restrito.php?IMG=' );
    }
    
    $tpl->printToscreen();
?>