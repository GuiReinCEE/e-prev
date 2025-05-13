<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');
	
	header('location:'.base_url().'index.php/ecrm/cadastro_plano_certificado');
	
// -------------------------------------------------------------------
	$tpl = new TemplatePower('tpl/tpl_lst_plano.html');
	$tpl->prepare();
	$tpl->assign('n', $n);
// -------------------------------------------------------------------   
	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
	$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);
	$tpl->assign('p',$p);

	#### OS Nº 17608 ####
	if (($_SESSION['D'] != 'GI') AND ($_SESSION['U'] != "mpozzebon") AND ($_SESSION['U'] != "vdornelles"))
	{
   		header('location: acesso_restrito.php?IMG=banner_exec_tarefa');
	} 	
	
	
// -------------------------------------------------------------------
	$sql =        " select cd_plano, nome_certificado, to_char(dt_inicio,'dd/mm/yyyy') as dt_inicio, to_char(dt_final,'dd/mm/yyyy') as dt_final, versao_certificado ";
	$sql = $sql . " from   planos_certificados where cd_plano = $p order by versao_certificado desc";
// -------------------------------------------------------------------
	$rs=pg_exec($db, $sql);
	$cont = 0;
	while ($reg=pg_fetch_array($rs)) {
		$tpl->newBlock('plano');
		$cont = $cont + 1;
		if ($L == 'P') {
			$L = 'I';
			$tpl->assign('cor_fundo',$v_cor_fundo1);
		} else {
			$L = 'P';
			$tpl->assign('cor_fundo',$v_cor_fundo2);
		}
		$tpl->assign('cd_plano',$reg['cd_plano']);
		$tpl->assign('cd_versao',$reg['versao_certificado']);
		$tpl->assign('descricao',$reg['nome_certificado']);
		$tpl->assign('dt_inicio',$reg['dt_inicio']);
		$tpl->assign('dt_final',$reg['dt_final']);
	}
// -------------------------------------------------------------------
	pg_close($db);
	$tpl->printToScreen();	
?>