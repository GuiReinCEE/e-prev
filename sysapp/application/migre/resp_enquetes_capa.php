<?php
	if($_REQUEST['c'] == 65)
	{
		include_once('inc/sessao_enquetes.php');
	}
	else if(($_REQUEST['c'] == 30) or ($_REQUEST['c'] == 79) or ($_REQUEST['c'] == 102) or ($_REQUEST['c'] == 111))
	{
	}
	else
	{
		include_once('inc/sessao.php');
	}
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');
// ----------------------------------------------- se for tipo formulário, atualizar o último respondente
	$sql = "select controle_respostas, ultimo_respondente, tipo_layout from projetos.enquetes where cd_enquete =  ".$_REQUEST['c'];
	$rs = pg_exec($db, $sql);
	$reg = pg_fetch_array($rs);
	$controle_respostas = $reg['controle_respostas'];
	if ($reg['controle_respostas'] == 'F') 
	{
		$v_ultimo = ($reg['ultimo_respondente'] + 1);
		$sql = " 
				UPDATE projetos.enquetes 
				   SET ultimo_respondente = ".$v_ultimo." 
				 WHERE cd_enquete = ".$_REQUEST['c']." 
			   ";
		pg_query($db, $sql);		

		$_SESSION['ENQ_CHAVE'] = $v_ultimo;
	}
	else if($reg['controle_respostas'] == 'R') 
	{
		
		session_unset("PESQUISA_RE");
		session_start("PESQUISA_RE");
		
		$_SESSION['ENQ_CD_EMPRESA']            = rand(0,99);
		$_SESSION['ENQ_CD_REGISTRO_EMPREGADO'] = rand(0,999999);
		$_SESSION['ENQ_SEQ_DEPENDENCIA']       = rand(0,99);	
		
		/*
		if($_SESSION['ENQ_CD_REGISTRO_EMPREGADO'] == "")
		{
			echo '<META HTTP-EQUIV="Refresh" CONTENT="0;URL=enquete_login_re.php?c='.$_REQUEST['c'].'">';
		}
		*/
	}
	#ECHO print_r($_SESSION);exit;
	
	
	if ($reg['tipo_layout'] == 3) {
		$tpl = new TemplatePower('tpl/tpl_resp_enquetes_capa_'.$_REQUEST['c'].'.html');
		$tpl->prepare();
	} elseif ($reg['tipo_layout'] == 2) {
		$tpl = new TemplatePower('tpl/tpl_resp_enquetes_capa_fceee.html');
		$tpl->prepare();
	} elseif ($reg['tipo_layout'] == 4) {
		$tpl = new TemplatePower('tpl/tpl_resp_enquetes_capa_atendimento.html');
		$tpl->prepare();
	} else {
		$tpl = new TemplatePower('tpl/tpl_resp_enquetes_capa.html');
		$tpl->prepare();
		$tpl->assign('n', $n);
		$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
		include_once('inc/skin.php');
		$tpl->assign('usuario', $_SESSION['N']);
		$tpl->assign('divsao', $_SESSION['D']);
	}
//-----------------------------------------------   
	if ($_REQUEST['c'] == 10) { $_REQUEST['c'] = 13; }
// -----------------------------------------------
	/*
	$sql =        " select 	dt_inicio, 
	                        dt_fim,  
							to_char(dt_inicio, 'DD/MM/YYYY HH24:MI') as dt_inicio_ed, 
							to_char(dt_fim, 'DD/MM/YYYY HH24:MI') as dt_fim_ed, 
							current_timestamp as data_hoje, 
							cd_responsavel ";
	$sql = $sql . " from 	projetos.enquetes  ";
	$sql = $sql . " where 	cd_enquete = $c ";
	$rs = pg_exec($db, $sql);
	$reg = pg_fetch_array($rs);
	if ((($reg['dt_inicio'] < $reg['data_hoje']) and ($reg['dt_fim'] > $reg['data_hoje'])) or ($reg['cd_responsavel'] == $Z)) 
	{
	*/
	
	$sql = "
			SELECT dt_inicio,
                   dt_fim,  
				   to_char(dt_inicio, 'DD/MM/YYYY HH24:MI') as dt_inicio_ed, 
				   to_char(dt_fim, 'DD/MM/YYYY HH24:MI') as dt_fim_ed, 
				   current_timestamp as data_hoje, 
				   cd_responsavel,
				   CASE WHEN CURRENT_TIMESTAMP >= dt_inicio
					    THEN 'S'
					    ELSE 'N'
				   END AS fl_inicio,
				   CASE WHEN CURRENT_TIMESTAMP >= dt_fim
					    THEN 'S'
					    ELSE 'N'
				   END AS fl_fim
	          FROM projetos.enquetes  
			 WHERE cd_enquete = ".$_REQUEST['c']." 			  
	       ";
	$rs = pg_query($db, $sql);
	$reg = pg_fetch_array($rs);	
	if ((($reg['fl_inicio'] == 'S') and ($reg['fl_fim'] == 'N')) or ($reg['cd_responsavel'] == $_SESSION['Z'])) 
	{	
//----------------------------------------------- Verifica se usuário já preencheu a enquete
		if ($controle_respostas == 'P') 
		{
			$sql =        " select 	count(*) as num_regs ";
			$sql = $sql . " from 	projetos.enquetes_participantes ep, projetos.enquetes e  ";
			$sql = $sql . " where 	ep.cd_enquete = ".$_REQUEST['c']." and cd_empresa = $EMP 
			and ep.cd_registro_empregado = $RE and ep.seq_dependencia = $SEQ and ep.cd_enquete = e.cd_enquete and e.controle_respostas = 'P' ";
//			echo $sql;
		} 
		else if ($controle_respostas == 'R') 
		{
			$sql = " 
					SELECT COUNT(*) AS num_regs 
					  FROM projetos.enquetes_participantes ep, 
					       projetos.enquetes e  
					 WHERE ep.cd_enquete = ".$_REQUEST['c']." 
					   AND cd_empresa               = ".$_SESSION['ENQ_CD_EMPRESA']." 
			           AND ep.cd_registro_empregado = ".$_SESSION['ENQ_CD_REGISTRO_EMPREGADO']." 
					   AND ep.seq_dependencia       = ".$_SESSION['ENQ_SEQ_DEPENDENCIA']." 
					   AND ep.cd_enquete            = e.cd_enquete 
					   AND e.controle_respostas     = 'R' 
				   ";
		} 		
		else if ($controle_respostas == 'I') 
		{
			$sql = " 
					SELECT COUNT(*) as num_regs
					  FROM projetos.enquete_resultados e
					 WHERE e.cd_enquete = ".$_REQUEST['c']." 
					   AND e.ip = '".$_SERVER['REMOTE_ADDR']."'
				   ";
			if(($_REQUEST['c'] == 30) or ($_REQUEST['c'] == 79) or ($_REQUEST['c']== 111))
			{
				$sql = " 
						SELECT COUNT(*) as num_regs
						  FROM projetos.enquete_resultados e
						 WHERE e.cd_enquete = ".$_REQUEST['c']." 
						   AND e.ip = '".$_SERVER['REMOTE_ADDR']."'
						   AND CURRENT_DATE < DATE_TRUNC('month',e.dt_resposta) + '1 month'::interval
					   ";			
			}
		}
		else {
			$sql =        " select 	count(*) as num_regs ";
			$sql = $sql . " from 	projetos.usuarios_enquetes ue, projetos.enquetes e  ";
			$sql = $sql . " where 	ue.cd_enquete = ".intval($_REQUEST['c'] )." 
			and cd_usuario = ".intval($_SESSION['Z'] )."
			and ue.cd_enquete = e.cd_enquete 
			and e.controle_respostas = 'U' ";
		}

		$rs = pg_exec($db, $sql);

		$reg=pg_fetch_array($rs);
		if ($reg['num_regs'] > 0) 
		{
			$tpl->newBlock('mensagem');
			$tpl->assign('mensagem', 'Você já respondeu a esta pesquisa!');
		}
		else {
//----------------------------------------------- Informações do agrupamento
			$tpl->newBlock('cadastro');
			$tpl->assign('cor_fundo1', $v_cor_fundo1);
			$tpl->assign('cor_fundo2', $v_cor_fundo2);
			$tpl->assign('eq', $_REQUEST['c']);
	
			$sql =        " select 	cd_enquete, titulo, to_char(dt_inicio, 'DD/MM/YYYY HH24:MI') as dt_inicio, to_char(dt_fim, 'DD/MM/YYYY HH24:MI') as dt_fim, to_char(dt_fim, 'YYYYMMDD') as dt_fim2, cd_site, cd_responsavel, texto_abertura ";
			$sql = $sql . " from 	projetos.enquetes  ";
			$sql = $sql . " where 	cd_enquete =  ".$_REQUEST['c'];
			$rs = pg_exec($db, $sql);
			$reg=pg_fetch_array($rs);
			$tpl->assign('codigo', $reg['cd_enquete']);
			$tpl->assign('titulo', $reg['titulo']);
			$tpl->assign('dt_inicio', $reg['dt_inicio']);
			$tpl->assign('dt_fim', $reg['dt_fim']);
			$tpl->assign('descricao', nl2br($reg['texto_abertura']));
			$v_site = $reg['cd_site'];
			$v_responsavel = $reg['cd_responsavel'];
		}
//--------------------------------------------------------------------
	} 
	elseif ($reg['fl_inicio'] == 'N') 
	{
		$tpl->newBlock('mensagem');
		$tpl->assign('mensagem', 'Pesquisa ainda não iniciou!');
		$tpl->newBlock('periodo');
		$tpl->assign('periodo', 'Pesquisa aberta entre ' . $reg['dt_inicio_ed'] . ' e ' . $reg['dt_fim_ed'] .'.');
	} 
	else 
	{
		$tpl->newBlock('mensagem');
		$tpl->assign('mensagem', 'Pesquisa encerrada!');
		$tpl->newBlock('periodo');
		$tpl->assign('periodo', 'Pesquisa aberta entre ' . $reg['dt_inicio_ed'] . ' e ' . $reg['dt_fim_ed'] .'.');
		
	}
//--------------------------------------------------------------------
	pg_close($db);
	$tpl->printToScreen();	
?>