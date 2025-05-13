<?
   include_once('inc/sessao.php');
   include_once('inc/conexao.php');
   include_once('inc/class.TemplatePower.inc.php');
	$tpl = new TemplatePower('tpl/tpl_lst_etapas_projeto.html');
	$tpl->prepare();
	$tpl->assign('n', $n);
	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);	
	$tpl->assign('cd_projeto', $c);
//---------------------------------------------------------------------------
	$sql =        " select 	cd_projeto, cd_etapa, nome_etapa, ";
	$sql = $sql . "         situacao_etapa, l.descricao as desc_situacao, ";
	$sql = $sql . "         to_char(dt_etapa, 'DD/MM/YYYY') as dt_etapa ";
	$sql = $sql . " from   	projetos.etapas_projeto, listas l ";
	$sql = $sql . " where  	cd_projeto = $c and situacao_etapa = l.codigo and l.categoria = 'SIEP' ";
	$sql = $sql . " order 	by dt_etapa, etapa_anterior, cd_etapa ";
//---------------------------------------------------------------------------
//echo $sql ;
	$rs=pg_exec($db, $sql);
	$cont = 0;
	while ($reg=pg_fetch_array($rs)) {
		$tpl->newBlock('etapa_projeto');
		$cont = $cont + 1;
		if (($cont % 2) <> 0) {
			$tpl->assign('cor_fundo', $v_cor_fundo1);
		}
		else {
			$tpl->assign('cor_fundo', $v_cor_fundo2);
		}
		$tpl->assign('cd_projeto',$reg['cd_projeto']);
		$tpl->assign('cd_etapa', $reg['cd_etapa']);
		$tpl->assign('nome_etapa', $reg['nome_etapa']);		
		$tpl->assign('dt_etapa', $reg['dt_etapa']);
		$tpl->assign('situacao_etapa', $reg['desc_situacao']);

		if ($reg['situacao_etapa'] == 'C') {
			$tpl->assign('imagem', '<img src="img/img_bola_verde.jpg">');
		}
		elseif ($reg['situacao_etapa'] == 'P') {
			$tpl->assign('imagem', '<img src="img/img_bola_amarela.jpg">');
		}
		elseif ($reg['situacao_etapa'] == 'A') {
			$tpl->assign('imagem', '<img src="img/img_bola_azul.jpg">');
		}
		elseif ($reg['situacao_etapa'] == 'T') {
			$tpl->assign('imagem', '<img src="img/img_bola_vermelha.jpg">');
		}
	}
	pg_close($db);
	$tpl->printToScreen();	
?>