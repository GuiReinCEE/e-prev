<?
    include_once('inc/sessao.php');
    include_once('inc/conexao.php');
    include_once('inc/class.TemplatePower.inc.php');

    $tpl = new TemplatePower('tpl/tpl_avaliacao.html');

    $tpl->prepare();

    $PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
    include_once('inc/skin.php');

    $tpl->assign('usuario', $N);
    $tpl->assign('divsao', $D);
    $tpl->assign('ano_atual', date('Y'));
    
    $tipo_promocao_hidden = "";
	if($_REQUEST["cd_capa"]!="")
	{
    	$result = pg_query("SELECT * FROM projetos.avaliacao_capa WHERE cd_avaliacao_capa = " . intval($_REQUEST["cd_capa"]));
    	if($row = pg_fetch_array($result))
    	{
    		$tipo_promocao_hidden = $row['tipo_promocao'];
    	}
	}
    $tpl->assign('tipo_promocao_hidden', $tipo_promocao_hidden);

    if (isset($_REQUEST["tipo"]))
    {
        $tpl->assign('load_by_url_tipo', $_REQUEST["tipo"]);
	}
    if (isset($_REQUEST["cd_capa"]))
    {
        $tpl->assign('load_by_url_cd_capa', $_REQUEST["cd_capa"]);
	}

    $tpl->printToscreen();
?>