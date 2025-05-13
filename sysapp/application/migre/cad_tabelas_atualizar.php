<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');
	$tpl = new TemplatePower('tpl/tpl_cad_tabelas_atualizar.html');
        
        header( 'location:'.base_url().'index.php/servico/tabelas_atualizar/cadastro/'.$_REQUEST['c']);
//-----------------------------------------------   
	$tpl->prepare();
	$tpl->assign('n', $n);
	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);
	
	$tpl->assign('tabela_titulo', $_REQUEST['c']);
//-----------------------------------------------
	$tpl->newBlock('cadastro');
	$tpl->assign('cor_fundo1', $v_cor_fundo1);
	$tpl->assign('cor_fundo2', $v_cor_fundo2);
	if (isset($c))	
	{
		$sql = " 
				SELECT TO_CHAR(dt_inicio, 'DD/MM/YYYY HH24:MI:SS') AS dt_inicio,
					   TO_CHAR(dt_final, 'DD/MM/YYYY HH24:MI:SS') AS dt_final,
					   TO_CHAR(dt_final - dt_inicio,'HH24:MI:SS') AS hr_tempo,
					   tabela, 
					   comando_inicial, 
					   comando_final,
					   comando, 
					   contagem, 
					   periodicidade, 
					   postgres, 
					   oracle, 
					   truncar,
					   access_callcenter, 
					   campo_controle_incremental, 
					   incrementar, 
					   condicao 
			      FROM projetos.tabelas_atualizar 
				 WHERE UPPER(tabela) = UPPER('".$_REQUEST['c']."') 
				";	
		$rs = pg_exec($db, $sql);
		$reg=pg_fetch_array($rs);
		$tpl->assign('codigo', $reg['tabela']);
		$tpl->assign('condicao', $reg['condicao']);
		$tpl->assign('comando_inicial', $reg['comando_inicial']);
		$tpl->assign('comando_final', $reg['comando_final']);
		$tpl->assign('tabela', $reg['tabela']);
		$tpl->assign('dt_inicio', $reg['dt_inicio']);
		$tpl->assign('dt_final', $reg['dt_final']);
		$tpl->assign('hr_tempo', $reg['hr_tempo']);
		$tpl->assign('comando', $reg['comando']);
		$tpl->assign('contagem', $reg['contagem']);
		$tpl->assign('periodicidade', $reg['periodicidade']);
		$tpl->assign('postgres', $reg['postgres']);
		$tpl->assign('oracle', $reg['oracle']);
		$tpl->assign('access_callcenter', $reg['access_callcenter']);
		$tpl->assign('truncar', $reg['truncar']);
		$tpl->assign('incrementar', $reg['incrementar']);
		$tpl->assign('campo_controle', $reg['campo_controle_incremental']);
	}
//-----------------------------------------------
	pg_close($db);
	$tpl->printToScreen();	
?>