<?
   include_once('inc/sessao.php');
   include_once('inc/conexao.php');
   include_once('inc/class.TemplatePower.inc.php');

   header( 'location:'.base_url().'index.php/atendimento_lista/atendimento/'.$_REQUEST['at']);

   $tpl = new TemplatePower('tpl/tpl_lst_atendimento.html');
   $tpl->prepare();
   $tpl->assign('n', $n);
   
   	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
	$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);

   	if (($D <> 'GAP') and ($D <> 'GI')) {
   		header('location: acesso_restrito.php?IMG=banner_atend_pessoal');
	}

// ------------------------------------------------------------------------------- Detalhamento deste atendimento:
	if (isset($_REQUEST['at']))	
	{
		$sql = "
				SELECT a.cd_atendimento, 
				       a.cd_plano, 
					   a.cd_empresa, 
					   a.cd_registro_empregado, 
					   a.seq_dependencia, 
					   a.obs, 
					   COALESCE(p.nome,a.nome) AS nome,
					   u.guerra, 
					   a.resp_encaminhamento, 
					   r.guerra AS ds_resp_encaminhamento,
					   TO_CHAR(a.dt_hora_inicio_atendimento, 'DD/MM/YYYY HH24:MI:SS') AS dt_atendimento, 
					   TO_CHAR(a.dt_hora_fim_atendimento, 'DD/MM/YYYY HH24:MI:SS') AS dt_fim_atendimento, 
					   TO_CHAR(a.dt_hora_fim_atendimento - a.dt_hora_inicio_atendimento,'HH24:MI:SS') AS hr_atendimento, 
					   TO_CHAR(a.dt_encaminhamento, 'DD/MM/YYYY HH24:MI:SS') AS dt_encaminhamento, 
					   CASE WHEN dt_encaminhamento IS NOT NULL 
					        THEN 'Encaminhado'
							ELSE 'Aberto'
					   END AS situacao,
					   a.id_atendente, 
					   CASE WHEN (a.tipo_atendimento_indicado = 'E')
							THEN 'Empréstimo'
							ELSE a.tipo_atendimento_indicado
					   END AS tipo_atendimento_indicado
				  FROM projetos.atendimento a
				  JOIN projetos.usuarios_controledi u 
				    ON u.codigo = a.id_atendente 
				  LEFT JOIN projetos.usuarios_controledi r
				    ON u.codigo = a.resp_encaminhamento
				  LEFT JOIN participantes p
			        ON p.cd_empresa            = a.cd_empresa
			       AND p.cd_registro_empregado = a.cd_registro_empregado
			       AND p.seq_dependencia       = a.seq_dependencia				  
				 WHERE a.cd_atendimento = ".$_REQUEST['at'];
		$rs = pg_query($db, $sql);
		$reg=pg_fetch_array($rs);
		$tpl->assign('atendimento', $reg['cd_atendimento']);
		$tpl->assign('atendente', $reg['guerra']);
		$tpl->assign('obs', $reg['obs']);
		$v_obs = $reg['obs'];
		$tpl->assign('dt_atendimento', $reg['dt_atendimento']);
		$tpl->assign('emp', $reg['cd_empresa']);
		$tpl->assign('re', $reg['cd_registro_empregado']);
		$tpl->assign('seq', $reg['seq_dependencia']);
		$tpl->assign('nome_participante', $reg['nome']);
		$tpl->assign('situacao', $reg['situacao']);
		$tpl->assign('dt_encaminhamento', $reg['dt_encaminhamento']);
		$tpl->assign('prog_identificado', $reg['tipo_atendimento_indicado']);
		$tpl->assign('duracao_atendimento', $reg['hr_atendimento']);
		$tpl->assign('inicio_atendimento', $reg['dt_atendimento']);
		$tpl->assign('fim_atendimento', $reg['dt_fim_atendimento']);
		$tpl->assign('suporte', $reg['ds_resp_encaminhamento']);
		

// ------------------------------------------------------------------------------- Encaminhamentos
		$sql2 = "select 	texto_encaminhamento 
				from   	projetos.atendimento_encaminhamento 
				where   cd_atendimento = ". $_REQUEST['at'];
		$rs2 = pg_exec($db, $sql2);
	   	while ($reg2=pg_fetch_array($rs2)) {
			$tpl->newBlock('anotacao');
			$tpl->assign('cor_fundo', $v_cor_fundo3);
			$tpl->assign('cd_atendimento', $_REQUEST['at']);
			$tpl->assign('tipo_anotacao', 'Encaminhamento Suporte');
			$tpl->assign('anotacao', $reg2['texto_encaminhamento']);
		}
// ------------------------------------------------------------------------------- Reclamações
		$sql2 = "select texto_reclamacao
				from   	projetos.atendimento_reclamacao 
				where   cd_atendimento = ". $_REQUEST['at'];
		$rs2 = pg_exec($db, $sql2);		
	   	while ($reg2=pg_fetch_array($rs2)) {
			$tpl->newBlock('anotacao');
			$tpl->assign('cor_fundo', $v_cor_fundo4);
			$tpl->assign('cd_atendimento', $_REQUEST['at']);
			$tpl->assign('tipo_anotacao', 'Reclamação/Sugestão');
			$tpl->assign('anotacao', $reg2['texto_reclamacao']);
		}
		
		#### RECLAMAÇÃO NOVO 04/2010 ####
		$qr_sql = "
					SELECT r.numero,
					       r.ano,
						   r.tipo,
						   TO_CHAR(r.numero,'FM0000') || '/' || TO_CHAR(r.ano,'FM0000') || '/' || r.tipo AS cd_reclamacao,
						   r.descricao,
						   TO_CHAR(ran.dt_inclusao,'DD/MM/YYYY HH24:MI') AS dt_retorno,
						   ra.descricao AS ds_acao,
						   uca.nome AS ds_usuario_responsavel
				      FROM projetos.reclamacao r
					  LEFT JOIN projetos.reclamacao_andamento ra
					    ON ra.numero                  = r.numero
					   AND ra.ano                     = r.ano
					   AND ra.tipo                    = r.tipo
					   AND ra.tp_reclamacao_andamento = 'A'
					  LEFT JOIN projetos.reclamacao_andamento ran
					    ON ran.numero                  = r.numero
					   AND ran.ano                     = r.ano
					   AND ran.tipo                    = r.tipo
					   AND ran.tp_reclamacao_andamento = 'R' --RETORNO	
					  LEFT JOIN projetos.reclamacao_atendimento rat
					    ON rat.numero = r.numero
					   AND rat.ano    = r.ano
					   AND rat.tipo   = r.tipo
					  LEFT JOIN projetos.usuarios_controledi uca
					    ON uca.codigo = rat.cd_usuario_responsavel
				     WHERE r.cd_atendimento = ".$_REQUEST['at']."
					   AND r.dt_exclusao    IS NULL
				  ";
		$ob_resul = pg_query($db, $qr_sql);		
	   	while ($ar_reg = pg_fetch_array($ob_resul)) 
		{
			$reclamacao = "
							Número: <a href='../cieprev/index.php/ecrm/reclamacao/cadastro/".$ar_reg['numero']."/".$ar_reg['ano']."/".$ar_reg['tipo']."' style='font-weight:bold'>".$ar_reg['cd_reclamacao']."</a>
							<BR>
							<BR>
							<b>Descrição:</b> <i>".$ar_reg['descricao']."</i>
						    <BR>
						    <BR>
							<b>Responsável:</b> <i>".$ar_reg['ds_usuario_responsavel']."</i>							
						    <BR>
						    <BR>
							<b>Ação:</b> <i>".$ar_reg['ds_acao']."</i>
							<BR>
							<BR>
							<b>Retorno:</b> <i>".$ar_reg['dt_retorno']."</i>
							<BR>
							<BR>							
			              ";
			$tpl->newBlock('anotacao');
			$tpl->assign('cor_fundo', $v_cor_fundo4);
			$tpl->assign('cd_atendimento', $_REQUEST['at']);
			$tpl->assign('tipo_anotacao', 'Reclamação/Sugestão');
			$tpl->assign('anotacao', $reclamacao);
		}		

		
// ------------------------------------------------------------------------------- Retorno
		$sql2 = "select texto_retorno
				from   	projetos.atendimento_retorno
				where   cd_atendimento = ". $_REQUEST['at'];
		$rs2 = pg_exec($db, $sql2);		
	   	while ($reg2=pg_fetch_array($rs2)) {
			$tpl->newBlock('anotacao');
			$tpl->assign('cor_fundo', $v_cor_fundo4);
			$tpl->assign('cd_atendimento', $_REQUEST['at']);
			$tpl->assign('tipo_anotacao', 'Retorno');
			$tpl->assign('anotacao', $reg2['texto_retorno']);
		}
		
// ------------------------------------------------------------------------------- Observações
		$sql2 = "select texto_observacao
				from   	projetos.atendimento_observacao 
				where   cd_atendimento = ". $_REQUEST['at'];
		$rs2 = pg_exec($db, $sql2);		
	   	while ($reg2=pg_fetch_array($rs2)) 
		{
			$tpl->newBlock('anotacao');
			$tpl->assign('cor_fundo', $v_cor_fundo3);
			$tpl->assign('cd_atendimento', $_REQUEST['at']);
			$tpl->assign('tipo_anotacao', 'Observação');
			$tpl->assign('anotacao', $reg2['texto_observacao']);
		}
// -------------------------------------------------------------------------------


	}
	
	
	
	#### BUSCA TELAS DO ATENDIMENTO ####
	$qr_sql = "
				SELECT tp.nome_tela AS tela,
					   TO_CHAR(atc.dt_acesso,'HH24:MI') AS hr_hora,
					   lt.descricao AS tp_tela
				  FROM projetos.atendimento_tela_capturada atc	
				  LEFT JOIN projetos.telas_programas tp
					ON tp.cd_tela = atc.cd_tela
				  LEFT JOIN public.listas lt
					ON lt.codigo = tp.cd_programa_fceee
				   AND lt.categoria = 'PRFC'			  
				 WHERE atc.cd_atendimento = ".$_REQUEST['at']."
				 ORDER BY atc.dt_acesso ASC
			  ";
	$ob_resul = pg_query($db, $qr_sql);
	$nr_conta = 0;
	while ($ar_reg = pg_fetch_array($ob_resul)) 
	{
		if(($nr_conta % 2) != 0)
		{
			$bg_color = $v_cor_fundo1;
		}
		else
		{
			$bg_color = $v_cor_fundo2;		
		}
		
		$tpl->newBlock('tela_capturada');
		$tpl->assign('cor_fundo', $bg_color);
		$tpl->assign('tela',      $ar_reg['tela']);
		$tpl->assign('hora',      $ar_reg['hr_hora']);
		$tpl->assign('programa',  $ar_reg['tp_tela']);
	}	
// -------------------------------------------------------------------------------
	pg_close($db);
	$tpl->printToScreen();	
?>