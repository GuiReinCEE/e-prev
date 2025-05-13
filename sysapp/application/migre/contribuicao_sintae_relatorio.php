<?
    include_once('inc/sessao.php');
    include_once('inc/conexao.php');
    include_once('inc/class.TemplatePower.inc.php');

    $tpl = new TemplatePower('tpl/tpl_contribuicao_sintae_relatorio.html');

    $tpl->prepare();

    $PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
    include_once('inc/skin.php');

    $tpl->assign('usuario', $N);
    $tpl->assign('divsao', $D);

    $tpl->assign('mes_inicial', date('m'));
    $tpl->assign('ano_inicial', date('Y'));
    
    // Abrir direto em aba informada por querystring
    $tpl->assign( 'load_by_url', $_REQUEST['aba'] );

    if( $D!='GF' && $D!='GI' && $D!='GAP' )
    {
        header( 'Location: acesso_restrito.php?IMG=' );
    }

    $tpl->printToscreen();
?>