<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');
// ----------------------------------------------------------   
	$tpl = new TemplatePower('tpl/tpl_adm_listner.html');
	$tpl->prepare();
	$tpl->assign('n', $n);
	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
	$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);
// ----------------------------------------------------------
	$tpl->newBlock('cadastro');
	$n = 'U';
	$tpl->assign('insere', $n);
// ----------------------------------------------------------
	$sql =        " select 	ip, porta, to_char(dt_hr_atividade, 'dd/mm/yyyy hh24:mi') as dt_hora_atividade, ";
	$sql = $sql . " 		banco, to_char(ultima_resposta, 'dd/mm/yyyy hh24:mi') as ultima_resposta, situacao, tempo_resp ";
	$sql = $sql . " from   	projetos.adm_listner order by ip, porta";
	$rs = pg_exec($db, $sql);
	while ($reg=pg_fetch_array($rs)) {
		$tpl->newBlock('equipamento');
		if ($c == 2) {
			$c = 1;
			$tpl->assign('cor_fundo', $v_cor_fundo1);
		}
		else {
			$c = 2;
			$tpl->assign('cor_fundo', $v_cor_fundo2);
		}
		if ($reg['situacao'] == 'I') {
			$tpl->assign('cor_atividade', '#F0E0C7');
		}elseif ($reg['situacao'] == 'E') {
			$tpl->assign('cor_atividade', '#BAC7A1');
		}elseif ($reg['situacao'] == 'A') {
			$tpl->assign('cor_atividade', '#CFE8D9');
		}
		else {
			$tpl->assign('cor_atividade', '#FFFFD6');	 
		}
		$tpl->assign('ip', $reg['ip']);	 
		$tpl->assign('porta', $reg['porta']);
		if ($reg['porta'] == '3625') {
			$tpl->assign('processo', 'Call Center');
		} elseif ($reg['porta'] == '9731') {
			$tpl->assign('processo', 'Auto-atendimento WEB');
		} elseif ($reg['porta'] == '4444') {
			$tpl->assign('processo', 'Desenvolvimento');
		} elseif ($reg['porta'] == '3355') {
			$tpl->assign('processo', 'Sistema de empréstimos');
		}
		$tpl->assign('dt_hr_atividade', $reg['dt_hora_atividade']);
		$tpl->assign('banco', $reg['banco']);
		$tpl->assign('ultima_resposta', $reg['ultima_resposta']);
		if ($reg['situacao'] == 'A') {
			$tpl->assign('situacao', 'Ativo');
		} elseif ($reg['situacao'] == 'I') { 
			$tpl->assign('situacao', 'Inativo');
		} else {
			$tpl->assign('situacao', $reg['situacao']);
		}
		if ($reg['tempo_resp'] != '') {
			if ($reg['tempo_resp'] == 1) {
				$tpl->assign('tempo_resp', $reg['tempo_resp'].' segundo');
			} else {
				$tpl->assign('tempo_resp', $reg['tempo_resp'].' segundos');
			}
		}
	}
// ----------------------------------------------------------
	pg_close($db);
	$tpl->printToScreen();	
?>