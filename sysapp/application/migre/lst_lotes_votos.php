<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');
	$tpl = new TemplatePower('tpl/tpl_lst_lotes_votos.html');
//--------------------------------------------------------------
	$tpl->prepare();
	$tpl->assign('n', $n);
	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);
//--------------------------------------------------------------	
	if ($ano == '') {
		$ano = date('Y');
	}
	if ($cd_eleicao == '') {
		$cd_eleicao = '1';
	}
	$tpl->newBlock('lista');
	if ($canc == 'S') {
		$tpl->newBlock('cab_cancelados');
		$sql =        " select 	cd_lote, to_char(dt_hora_lancamento, 'dd/mm/yy hh24:mi:ss') as dt_hora_lancamento, u1.guerra as usu_lan, sum(num_votos) as num_votos, ";
		$sql = $sql . "		 	to_char(dt_hora_exclusao, 'dd/mm/yy hh24:mi:ss') as dt_hora_exclusao, u2.guerra as usu_can ";
		$sql = $sql . " from   	eleicoes.lotes_apuracao_eleicoes, projetos.usuarios_controledi u1, projetos.usuarios_controledi u2 ";
		$sql = $sql . " where	ano_eleicao = $ano ";
		$sql = $sql . " and 	cd_eleicao = $cd_eleicao "; //and length(trim(ce.logradouro)) > 32";		
		$sql = $sql . " and 	usu_lancamento = u1.codigo ";
		$sql = $sql . " and 	usu_exclusao = u2.codigo ";
		$sql = $sql . " and 	dt_hora_exclusao is not null ";
		$sql = $sql . " group 	by cd_lote, dt_hora_lancamento, dt_hora_exclusao, u1.guerra, u2.guerra ";
		$sql = $sql . " order 	by cd_lote, dt_hora_lancamento, dt_hora_exclusao, u1.guerra, u2.guerra "; 
	}
	else {
		$tpl->newBlock('cab_valido');
		$sql =        " select 	cd_lote, to_char(dt_hora_lancamento, 'dd/mm/yy hh24:mi:ss') as dt_hora_lancamento, guerra, sum(num_votos) as num_votos ";
		$sql = $sql . " from   	eleicoes.lotes_apuracao_eleicoes, projetos.usuarios_controledi ";
		$sql = $sql . " where	ano_eleicao = $ano ";
		$sql = $sql . " and 	cd_eleicao = $cd_eleicao "; //and length(trim(ce.logradouro)) > 32";		
		$sql = $sql . " and 	usu_lancamento = codigo ";
		$sql = $sql . " and 	dt_hora_exclusao is null ";
		$sql = $sql . " group 	by cd_lote, dt_hora_lancamento, guerra ";
		$sql = $sql . " order 	by cd_lote, dt_hora_lancamento, guerra "; 
	}	
	$rs=pg_exec($db, $sql);
	$cont = 0;
	while ($reg=pg_fetch_array($rs)) {
		if ($canc == 'S') {
			$tpl->newBlock('lote_cancelado');
			$cont = $cont + 1;
			if (($cont % 2) <> 0) {
				$tpl->assign('cor_fundo', $v_cor_fundo1);
			}
			else {
				$tpl->assign('cor_fundo', $v_cor_fundo2);
			}
			$tpl->assign('cd_lote', $reg['cd_lote']);
			$tpl->assign('dt_hora_lancamento', $reg['dt_hora_lancamento']);
			$tpl->assign('usuario', $reg['usu_lan']);
			$tpl->assign('dt_hora_cancelamento', $reg['dt_hora_exclusao']);
			$tpl->assign('usu_can', $reg['usu_can']);
			$tpl->assign('votos', $reg['num_votos']);
			$tpl->assign('cedulas', ($reg['num_votos'] / 4));
		}
		else {
			$tpl->newBlock('lote_valido');
			$cont = $cont + 1;
			if (($cont % 2) <> 0) {
				$tpl->assign('cor_fundo', $v_cor_fundo1);
			}
			else {
				$tpl->assign('cor_fundo', $v_cor_fundo2);
			}
			$tpl->assign('cd_lote', $reg['cd_lote']);
			$tpl->assign('dt_hora_lancamento', $reg['dt_hora_lancamento']);
			$tpl->assign('usuario', $reg['guerra']);
			$tpl->assign('votos', $reg['num_votos']);
			$tpl->assign('cedulas', ($reg['num_votos'] / 4));
		}
	}
//--------------------------------------------------------------
	pg_close($db);
	$tpl->printToScreen();	
?>