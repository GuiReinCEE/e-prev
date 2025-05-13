<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');

    header( 'location:'.base_url().'index.php/ecrm/atendimento_lista/programa');

	$tpl = new TemplatePower('tpl/tpl_atend_pessoal_programa.html');
	$tpl->prepare();
	$tpl->assign('n', $n);
// --------------------------------------------------------- inicialização do skin das telas:
	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
// ---------------------------------------------------------
	$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);
	
	$_REQUEST['dt_inicial'] = (trim($_REQUEST['dt_inicial']) == '' ? date('01/m/Y') : $_REQUEST['dt_inicial']);
	$tpl->assign('dt_inicial', $_REQUEST['dt_inicial']);
	
	$_REQUEST['dt_final']   = (trim($_REQUEST['dt_final'])   == '' ? date('d/m/Y') : $_REQUEST['dt_final']);
	$tpl->assign('dt_final', $_REQUEST['dt_final']);	
	
	
	#### TIPO DE ATENDIMENTO ###
	switch ($_REQUEST['tipo_atendimento']) 
	{
		case 'C': $tpl->assign('sel_consulta', ' selected');
				  break;	
		case 'P': $tpl->assign('sel_pessoal', ' selected');
				  break;	
		case 'T': $tpl->assign('sel_telefonico', ' selected');
				  break;	
		case 'E': $tpl->assign('sel_email', ' selected');
				  break;	
		default: $tpl->assign('sel_todos', ' selected');
	}	
	
	
	#### BUSCA ATENDIMENTOS ####
	$qr_sql = " 
				SELECT lt.descricao AS tp_programa,
				       COUNT(*) AS qt_programa,
					   TO_CHAR(AVG(hr_tempo),'HH24:MI:SS') AS qt_tempo
				  FROM projetos.atendimento_tela_capturada atc	
				  JOIN projetos.atendimento a
			            ON a.cd_atendimento = atc.cd_atendimento
				  JOIN projetos.telas_programas tp
			            ON tp.cd_tela = atc.cd_tela
				  JOIN public.listas lt
				    ON lt.codigo = tp.cd_programa_fceee
				   AND lt.categoria = 'PRFC'			  
				 WHERE CAST(atc.dt_acesso AS DATE) BETWEEN TO_DATE('".$_REQUEST['dt_inicial']."','DD/MM/YYYY')  AND TO_DATE('".$_REQUEST['dt_final']."','DD/MM/YYYY')
			       AND (a.dt_hora_fim_atendimento - a.dt_hora_inicio_atendimento) > '00:00:10'::INTERVAL
				   ".(intval($_REQUEST['cd_atendente']) > 0 ? " AND a.id_atendente = ".intval($_REQUEST['cd_atendente']) : "")."
				   ".(trim($_REQUEST['tipo_atendimento']) != '' ? " AND a.indic_ativo = '".$_REQUEST['tipo_atendimento']."'" : "")."
				 GROUP BY tp_programa
				 ORDER BY qt_programa DESC				 
		      ";
	$ob_resul = pg_query($db, $qr_sql);
	$nr_maior_atendimento = 0;
	$nr_conta = 0;
	while ($ar_reg = pg_fetch_array($ob_resul)) 
	{
		$tpl->newBlock('default');
		if (($nr_conta % 2) != 0) 
		{
			$tpl->assign('cor_fundo', $v_cor_fundo1);
		}
		else 
		{
			$tpl->assign('cor_fundo', $v_cor_fundo2);
		}		
		#$tpl->assign('tp_atendimento', $ar_reg['ds_tipo_atendimento']);
		$tpl->assign('tp_programa', $ar_reg['tp_programa']);
		$tpl->assign('numero_acessos_default', $ar_reg['qt_programa']);
		$tpl->assign('qt_tempo', $ar_reg['qt_tempo']);
		
		if($nr_conta == 0)
		{
			$nr_maior_atendimento = $ar_reg['qt_programa']; 
			$tpl->assign('numero_acessos_default_10', '100%');
		}
		else
		{
			$nr_tam = ($ar_reg['qt_programa'] * 100)/$nr_maior_atendimento;
			$tpl->assign('numero_acessos_default_10', $nr_tam.'%');
		}		
		
		$nr_conta++;
	}	
	
	
	#### COMBO ATENDENTES ####
	$qr_sql = "	
				SELECT uc.codigo,
				       uc.guerra
				  FROM projetos.usuarios_controledi uc
				 WHERE uc.divisao = 'GAP'
				   AND uc.tipo <> 'X'
				 ORDER BY uc.guerra
			  ";
	$ob_resul = pg_query($db, $qr_sql);
	$tpl->newBlock('bk_atendente');
	$tpl->assign('cd_atendente', '');	
	$tpl->assign('ds_atendente', 'Todos');	
	$tpl->assign('fl_atendente', (intval($_REQUEST['cd_atendente']) == 0 ? 'selected' : ''));	
	while ($ar_reg = pg_fetch_array($ob_resul)) 
	{
		$tpl->newBlock('bk_atendente');
		$tpl->assign('cd_atendente', $ar_reg['codigo']);	
		$tpl->assign('ds_atendente', $ar_reg['guerra']);	
		$tpl->assign('fl_atendente', (intval($_REQUEST['cd_atendente']) == $ar_reg['codigo'] ? 'selected' : ''));	
	}
	
	@pg_close($db);
	$tpl->printToScreen();



	function convdata_br_iso($dt) {
      // Pressupõe que a data esteja no formato DD/MM/AAAA
      // A melhor forma de gravar datas no PostgreSQL é utilizando 
      // uma string no formato DDDD-MM-AA. Esta função justamente 
      // adequa a data a este formato
      $d = substr($dt, 0, 2);
      $m = substr($dt, 3, 2);
      $a = substr($dt, 6, 4);
      return $a.'-'.$m.'-'.$d;
   }
?>