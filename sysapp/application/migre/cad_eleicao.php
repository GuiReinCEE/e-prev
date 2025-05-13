<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');
	$tpl = new TemplatePower('tpl/tpl_cad_eleicoes.html');
//-----------------------------------------------   
	$tpl->prepare();
	$tpl->assign('n', $n);
	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
	$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);
//--------------------------------
	if ($msg <> '') {
		$tpl->assign('msg', $msg);
	}
	$sql =        " select 	indic_07 ";
	$sql = $sql . " from   	projetos.usuarios_controledi ";
	$sql = $sql . " where 	codigo = $Z ";
	$rs = pg_exec($db, $sql);
	if ($reg=pg_fetch_array($rs)) 
	{
		if (($reg['indic_07'] == ' ') or ($reg['indic_07'] == '')) {
			header("location: acesso_restrito.php?IMG=banner_administracao_eleicoes"); 
		}
	} 
//-----------------------------------------------
	$tpl->newBlock('cadastro');
	$tpl->assign('cor_fundo1', $v_cor_fundo1);
	$tpl->assign('cor_fundo2', $v_cor_fundo2);
	if (isset($c))	{
		$sql =        " select 	cd_eleicao, ano_eleicao, nome, situacao, num_votos, votos_apurados, modalidade, ";
		$sql = $sql . "			to_char(dt_hr_abertura, 'DD/MM/YYYY HH:MI') as dt_hr_abertura, ";
		$sql = $sql . "        	to_char(dt_hr_fechamento, 'DD/MM/YYYY HH:MI') as dt_hr_fechamento ";
		$sql = $sql . " from eleicoes.eleicao where cd_eleicao = $c and ano_eleicao = $ano" ;
		$rs = pg_exec($db, $sql);
		$reg=pg_fetch_array($rs);
		$tpl->assign('codigo', $reg['cd_eleicao']);
		$tpl->assign('ano', $reg['ano_eleicao']);
		$tpl->assign('nome', $reg['nome']);
		$v_tlei = $reg['modalidade'];
//		echo $v_tlei;
		if ($reg['situacao'] == 'A') {		
			$tpl->assign('chk_aberta', 'checked');
		}
		else {
			$tpl->assign('chk_fechada', 'checked');
		}
		$tpl->assign('num_votos', $reg['num_votos']);
		$tpl->assign('num_apurados', $reg['votos_apurados']);
		$tpl->assign('dt_hr_abertura', $reg['dt_hr_abertura']);
		$tpl->assign('dt_hr_fechamento', $reg['dt_hr_fechamento']);
	}
//------------------------------------------------------------------------------------------- Combo Público Participante
	$sql = "SELECT * FROM listas WHERE categoria='TLEI' ORDER BY descricao";
	$rs = pg_exec($db, $sql);
	while ($reg = pg_fetch_array($rs)) {
		$tpl->newBlock('cbo_tlei');
		$tpl->assign('cd_tlei', $reg['codigo']);
		$tpl->assign('nome_tlei', $reg['descricao']);
		$tpl->assign('chk_tlei', ($reg['codigo'] == $v_tlei ? ' selected' : ''));
	}
//-----------------------------------------------
	pg_close($db);
	$tpl->printToScreen();	
?>