<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	
	header( 'location: '.base_url().'index.php/ecrm/multimidia/video_cadastro/'.intval($_REQUEST['c']));
	EXIT;	
	
	include_once('inc/class.TemplatePower.inc.php');
	$tpl = new TemplatePower('tpl/tpl_cad_filmes.html');
//-----------------------------------------------   
	$tpl->prepare();
	$tpl->assign('n', $n);
	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);
//-----------------------------------------------
	if (($D <> 'GRI') and ($Z <> 191) and ($Z <> 170)) {
   		header('location: acesso_restrito.php?IMG=banner_filmes');
	}
	$tpl->newBlock('cadastro');
	if (isset($c))	{
		$sql =        " select 	cd_video, ";
		$sql = $sql . "        	titulo, ds_local, diretorio, arquivo, ";
		$sql = $sql . "        	to_char(dt_evento, 'DD/MM/YYYY') as dt_evento, ";
		$sql = $sql . "        	to_char(dt_atualizacao, 'DD/MM/YYYY') as dt_atualizacao ";
		$sql = $sql . " from   	acs.videos  ";
		$sql = $sql . " where 	cd_video = $c";
		$rs = pg_exec($db, $sql);
		$reg=pg_fetch_array($rs);
		$tpl->assign('codigo', $reg['cd_video']);
		$tpl->assign('titulo', $reg['titulo']);
		$tpl->assign('local', $reg['ds_local']);
		$v_diretorio = str_replace("file://Srvimagem/MULTIMIDIA/","",$reg['diretorio']);
		$v_diretorio = str_replace("/","",$v_diretorio);
		$tpl->assign('diretorio', $v_diretorio);
		$tpl->assign('arquivo', $reg['arquivo']);
		$tpl->assign('dt_evento', $reg['dt_evento']);			
	}
//-----------------------------------------------
	pg_close($db);
	$tpl->printToScreen();	
?>