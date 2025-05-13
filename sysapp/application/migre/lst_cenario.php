<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');

	header( 'location:'.base_url().'index.php/ecrm/informativo_cenario_legal_conteudo/index/'.$ed );

	include_once('inc/class.TemplatePower.inc.php');
	$tpl = new TemplatePower('tpl/tpl_lst_cenario.html');
	$tpl->prepare();
	$tpl->assign('n', $n);

   $PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
	$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);

   	if (($D <> 'GC') and ($D <> 'GI'))
	{
   		header('location: acesso_restrito.php?IMG=banner_cenario');
	}

	$tpl->newBlock('lista');
	// -------------------------------------------------------------------- Verifica dados da edição:
	$sql =        " select  tit_capa ";
	$sql = $sql . " from   	projetos.edicao_cenario  ";
	$sql = $sql . " where 	cd_edicao = $ed ";
	// echo $sql ;
	$rs=pg_exec($db, $sql);
	$reg=pg_fetch_array($rs);
	$tpl->newBlock('edicao');
	$tpl->assign('cd_edicao',$ed);
	$tpl->assign('tit_capa',$reg['tit_capa']);

	$sql =        " SELECT cd_cenario, ";
	$sql = $sql . "                 titulo, ";
	$sql = $sql . "                 to_char(dt_inclusao, 'DD/MM/YYYY') as data_cad, ";
	$sql = $sql . "                 to_char(dt_exclusao, 'DD/MM/YYYY') as data_exc ";
	$sql = $sql . " FROM   	projetos.cenario  ";
	$sql = $sql . " WHERE 	cd_edicao = $ed ";	
	$sql = $sql . " ORDER BY cd_cenario DESC ";
	// echo $sql ;
	$rs=pg_exec($db, $sql);
	$cont = 0;
	while ($reg=pg_fetch_array($rs))
	{
		$tpl->newBlock('projetos');
		$cont = $cont + 1;
		$tpl->assign('cd_edicao',$ed);
		$tpl->assign('cd_cenario',$reg['cd_cenario']);
		$tpl->assign('cenario', $reg['titulo']);
		$tpl->assign('dt_cadastro', $reg['data_cad']);
		$tpl->assign('dt_exclusao', $reg['data_exc']);
	}
	pg_close($db);
	$tpl->printToScreen();	
?>