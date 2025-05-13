<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');
	$tpl = new TemplatePower('tpl/tpl_cad_encaminhamento_aberto.html');
	$tpl->prepare();
	$tpl->assign('n', $n);
	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
	$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);

	
	$tpl->newBlock('cadastro');
	$tpl->assignGlobal('cor_fundo1', $v_cor_fundo1);
	$tpl->assignGlobal('cor_fundo2', $v_cor_fundo2); 
	
	
	#### BUSCA ATENDIMENTO ####
	$qr_sql = "
				SELECT a.cd_atendimento,
				       CASE WHEN (a.indic_ativo = 'T') THEN 'Telefônico'
				            WHEN (a.indic_ativo = 'P') THEN 'Pessoal'
							WHEN (a.indic_ativo = 'C') THEN 'Consulta'
							WHEN (a.indic_ativo = 'E') THEN 'E-mail'
				            ELSE 'Não Informado'
				       END AS tp_atendimento,				
				       TO_CHAR(a.dt_hora_inicio_atendimento,'DD/MM/YYYY HH24:MI') AS dt_atendimento,
				       a.cd_empresa,
				       a.cd_registro_empregado,
				       a.seq_dependencia,
				       p.nome AS nome_participante,
				       a.obs,
				       uc.guerra AS atendente  
				  FROM projetos.atendimento a 
				  JOIN projetos.usuarios_controledi uc
				    ON uc.codigo = a.id_atendente
				  LEFT JOIN public.participantes p
				    ON p.cd_empresa            = a.cd_empresa
				   AND p.cd_registro_empregado = a.cd_registro_empregado
				   AND p.seq_dependencia       = a.seq_dependencia
				 WHERE a.cd_atendimento = ".$_REQUEST['at'];
	$ob_resul = pg_query($db, $qr_sql);
	$ar_reg   = pg_fetch_array($ob_resul);
	$tpl->assign('cd_atendimento',    $ar_reg['cd_atendimento']); 
	$tpl->assign('atendente',         $ar_reg['atendente']); 
	$tpl->assign('emp',               $ar_reg['cd_empresa']); 
	$tpl->assign('re',                $ar_reg['cd_registro_empregado']); 
	$tpl->assign('seq',               $ar_reg['seq_dependencia']); 
	$tpl->assign('nome_participante', $ar_reg['nome_participante']);
	$tpl->assign('dt_atendimento',    $ar_reg['dt_atendimento']);
	$tpl->assign('tp_atendimento',    utf8_decode($ar_reg['tp_atendimento']));
	if(trim($ar_reg['obs']) != "")
	{
		$tpl->newBlock('observacoes');
		$tpl->assign('obs',    str_replace(chr(10), '<br>', $ar_reg['obs']));
	}
	
	#### BUSCA OBSERVACOES DO ATENDIMENTO ####
	$qr_sql = "
				SELECT ao.texto_observacao 
				  FROM projetos.atendimento_observacao ao
				 WHERE ao.cd_atendimento = ".$_REQUEST['at'];
	$ob_resul = pg_query($db, $qr_sql);
	while($ar_reg = pg_fetch_array($ob_resul))
	{
		if(trim($ar_reg['texto_observacao']) != "")
		{
			$tpl->newBlock('observacoes');
			$tpl->assign('obs',    str_replace(chr(10), '<br>', $ar_reg['texto_observacao']));
		}
	}
	
	#### BUSCA ENCAMINHAMENTOS DO ATENDIMENTO ####
	$qr_sql = "
				SELECT ae.cd_encaminhamento,
                       ae.cd_atendimento,
				       CASE WHEN ae.dt_cancelado IS NOT NULL
                            THEN 'Cancelado'
                            WHEN ae.dt_retorno_encaminhamento IS NOT NULL
				            THEN 'Encaminhado'
				            ELSE 'Aberto'
				       END AS fl_atendimento,
                       uc.guerra AS solicitante,
                       TO_CHAR(ae.dt_encaminhamento,'DD/MM/YYYY HH24:MI') AS dt_solicitacao,
					   uc1.guerra AS atendente,
					   TO_CHAR(ae.dt_retorno_encaminhamento,'DD/MM/YYYY HH24:MI') AS dt_encaminhamento,
					   TO_CHAR(ae.dt_cancelado,'DD/MM/YYYY HH24:MI') AS dt_cancelado,
					   ae.texto_encaminhamento
				  FROM projetos.atendimento_encaminhamento ae
				  JOIN projetos.usuarios_controledi uc
				    ON uc.codigo = ae.id_atendente				  
				  LEFT JOIN projetos.usuarios_controledi uc1
				    ON uc1.codigo = ae.id_atendente_retorno						
				 WHERE ae.cd_atendimento = ".$_REQUEST['at']."
				 ORDER BY ae.dt_encaminhamento ASC
			  ";
	$ob_resul = pg_query($db, $qr_sql);
	while($ar_reg = pg_fetch_array($ob_resul))
	{
		$tpl->newBlock('encaminhamentos');
		$tpl->assign('cd_encaminhamento',  $ar_reg['cd_encaminhamento']);
		$tpl->assign('cd_atendimento',     $ar_reg['cd_atendimento']);
		$tpl->assign('situacao',           $ar_reg['fl_atendimento']);
		$tpl->assign('solicitante',        $ar_reg['solicitante']);
		$tpl->assign('dt_solicitacao',     $ar_reg['dt_solicitacao']);
		$tpl->assign('atendente',          $ar_reg['atendente']);
		$tpl->assign('dt_encaminhamento',  $ar_reg['dt_encaminhamento']);		
		$tpl->assign('dt_cancelado',       $ar_reg['dt_cancelado']);		
		$tpl->assign('texto_encaminhamento',  str_replace(chr(10), '<br>',$ar_reg['texto_encaminhamento']));	
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
	
/*	
// -------------------------------------------------------------------------------
	if (isset($at))	{
		$sql = "select 	cd_atendimento, a.cd_plano, a.cd_empresa, a.cd_registro_empregado, guerra, obs, 
						a.seq_dependencia, resp_encaminhamento, 
						to_char(dt_hora_inicio_atendimento, 'dd/mm/yyyy hh24:mi') as dt_atendimento, 
						to_char(dt_encaminhamento, 'dd/mm/yyyy hh24:mi') as dt_encaminhamento, 
						id_atendente, obs, tipo_atendimento_indicado
				from   	projetos.atendimento a, projetos.usuarios_controledi u 
				where	a.id_atendente = u.codigo and cd_atendimento = " . $at;
		$rs = pg_exec($db, $sql);
		$reg=pg_fetch_array($rs);
		$tpl->assign('atendimento', $reg['cd_atendimento']);
		$tpl->assign('atendente', $reg['guerra']);
//		$tpl->assign('obs', $reg['obs']);
		$tpl->assign('dt_atendimento', $reg['dt_atendimento']);
		$tpl->assign('emp', $reg['cd_empresa']);
		$tpl->assign('re', $reg['cd_registro_empregado']);
		$tpl->assign('seq', $reg['seq_dependencia']);
		if ($reg['resp_encaminhamento'] != '') {
			$sql2 = "select guerra from projetos.usuarios_controledi where codigo = ".$reg['resp_encaminhamento']." ";
			$rs2 = pg_exec($db, $sql2);
			$reg2 = pg_fetch_array($rs2);
			$tpl->assign('suporte', $reg2['guerra']);
		}
		if ($reg['dt_encaminhamento'] == '') { 
			$tpl->assign('situacao', 'Aberto'); 
		} else {
			$tpl->assign('situacao', 'Encaminhado'); 
		}
		$tpl->assign('dt_encaminhamento', $reg['dt_encaminhamento']);
// -------------------------------------------------------------------------------
		$sql2 = "select 	texto_encaminhamento 
				from   	projetos.atendimento_encaminhamento 
				where   cd_atendimento = ". $at;
		$rs2 = pg_exec($db, $sql2);
		$reg2=pg_fetch_array($rs2);
		$tpl->assign('obs', $reg2['texto_encaminhamento']);
// -------------------------------------------------------------------------------
		if ($reg['cd_registro_empregado'] != '') {
			$v_re = $reg['cd_registro_empregado'];
			$sql2 = "select	nome from participantes where cd_empresa = ".$reg['cd_empresa']. 
					" and cd_registro_empregado = " . $reg['cd_registro_empregado'] . " and seq_dependencia = " . $reg['seq_dependencia'];
			$rs2 = pg_exec($db, $sql2);
			$reg2 = pg_fetch_array($rs2);
			$tpl->assign('nome_participante', $reg2['nome']);
// -------------------------------------------------------------------------------
			$sql = "select 	prog, 
					to_char(dt_acesso, 'hh24:mi') as dt_acesso_ed, dt_acesso
					from   	projetos.log_acessos_programas
					where	atendimento = " . $at . " and cd_registro_empregado = ".$v_re." order by dt_acesso desc";
			$rs = pg_exec($db, $sql);
			while ($reg=pg_fetch_array($rs)) { 
				if (substr_count($reg['prog'], 'Fundação CEEE') != 0) {
					$v_prog = str_replace('Fundação CEEE - ', '', $reg['prog']);
					$v_pos = strpos($v_prog, ' - ');
					$v_prog = substr($v_prog, 0, $v_pos);
					$sql2 = "select cd_programa_fceee, dt_cadastro, descricao from projetos.telas_programas where nome_tela = '".$v_prog."' ";
					$rs2 = pg_exec($db, $sql2);
					$reg2 = pg_fetch_array($rs2);
					$tpl->newBlock('tela_acessada');
					if ($l == 'P') { 
						$l = 'I';
						$tpl->assign('cor_fundo', $v_cor_fundo3);
					} else {
						$tpl->assign('cor_fundo', $v_cor_fundo4);
						$l = 'P';
					}
					$tpl->assign('hora', $reg['dt_acesso_ed']);
					$tpl->assign('tela', $v_prog);
					$tpl->assign('descricao', $reg2['descricao']);
					$tpl->assign('programa', $reg2['cd_programa_fceee']);
				}
			}
		}
	} elseif (isset($sp))	{
		$sql = "select 	cd_solicitacao, a.cd_empresa, a.cd_registro_empregado, guerra, convert(descricao, 'UTF8', 'LATIN1') as descricao, 
						a.seq_dependencia, cd_resp_encaminhamento, 
						to_char(dt_abertura, 'dd/mm/yyyy hh24:mi') as dt_atendimento, 
						to_char(dt_encaminhamento, 'dd/mm/yyyy hh24:mi') as dt_encaminhamento, 
						cd_resp_encaminhamento, tipo_atendimento_indicado
				from   	projetos.solicitacoes_participantes a, projetos.usuarios_controledi u 
				where	a.cd_resp_abertura = u.codigo and cd_solicitacao = " . $sp;
		$rs = pg_exec($db, $sql);
		$reg=pg_fetch_array($rs);
		$tpl->assign('cd_solicitacao', $reg['cd_solicitacao']);
		$tpl->assign('atendente', $reg['guerra']);
		$tpl->assign('obs', $reg['descricao']);
		$tpl->assign('dt_atendimento', $reg['dt_atendimento']);
		$tpl->assign('emp', $reg['cd_empresa']);
		$tpl->assign('re', $reg['cd_registro_empregado']);
		$tpl->assign('seq', $reg['seq_dependencia']);
		if ($reg['cd_resp_encaminhamento'] != '') {
			$sql2 = "select guerra from projetos.usuarios_controledi where codigo = ".$reg['cd_resp_encaminhamento']." ";
			$rs2 = pg_exec($db, $sql2);
			$reg2 = pg_fetch_array($rs2);
			$tpl->assign('suporte', $reg2['guerra']);
		}
		if ($reg['dt_encaminhamento'] == '') { 
			$tpl->assign('situacao', 'Aberto'); 
		} else {
			$tpl->assign('situacao', 'Encaminhado'); 
		}
		$tpl->assign('dt_encaminhamento', $reg['dt_encaminhamento']);
// -------------------------------------------------------------------------------
		if ($reg['cd_registro_empregado'] != '') {
			$v_re = $reg['cd_registro_empregado'];
			$sql2 = "select	nome from participantes where cd_empresa = ".$reg['cd_empresa']. 
					" and cd_registro_empregado = " . $reg['cd_registro_empregado'] . " and seq_dependencia = " . $reg['seq_dependencia'];
			$rs2 = pg_exec($db, $sql2);
			$reg2 = pg_fetch_array($rs2);
			$tpl->assign('nome_participante', $reg2['nome']);
// -------------------------------------------------------------------------------
		}
	}
//-----------------------------------------------
*/
	pg_close($db);
	$tpl->printToScreen();	
?>