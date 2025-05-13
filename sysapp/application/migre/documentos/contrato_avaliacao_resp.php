<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');
	
	$tpl = new TemplatePower('tpl/tpl_contrato_avaliacao_resp.html');
	$tpl->prepare();
	
	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');

	$tpl->assign('n', $n);
	$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);
	
	$_REQUEST['cd_contrato_avaliacao'] = 4;
	//$_SESSION['Z'] = 22;
	$_SESSION['Z'] = 47;
	
	
	#### VERIFICA USUARIO É AVALIADOR ####
	$qr_sql = "
				SELECT COUNT(*) AS fl_usuario
				  FROM projetos.contrato_avaliacao_item cai
				 WHERE cai.cd_contrato_avaliacao = ".$_REQUEST['cd_contrato_avaliacao']."
				   AND cai.dt_exclusao           IS NULL
				   AND cai.cd_usuario_avaliador  = ".$_SESSION['Z']."
	          ";
	$ob_resul = pg_query($db,$qr_sql);
	$ar_reg   = pg_fetch_array($ob_resul);	
	if($ar_reg['fl_usuario'] < 1)
	{
		echo "USUÁRIO NÃO É AVALIADOR";
		exit;
	}
	
	#### VERIFICA DATA LIMITE PARA RESPONDER ####
	$qr_sql = "
				SELECT COUNT(*) AS fl_limite
				  FROM projetos.contrato_avaliacao ca
				 WHERE ca.dt_limite_avaliacao   > CURRENT_TIMESTAMP
				   AND ca.dt_exclusao           IS NULL
				   AND ca.cd_contrato_avaliacao = ".$_REQUEST['cd_contrato_avaliacao']."
				   AND 0 < (SELECT COUNT(*) 
								FROM projetos.contrato_avaliacao_item cai
							   WHERE cai.cd_contrato_avaliacao =  ca.cd_contrato_avaliacao
							     AND cai.dt_exclusao           IS NULL
								 AND cai.cd_usuario_avaliador  = ".$_SESSION['Z'].")
	          ";
	$ob_resul = pg_query($db,$qr_sql);
	$ar_reg   = pg_fetch_array($ob_resul);	
	if($ar_reg['fl_limite'] < 1)
	{
		echo "DATA LIMITE PARA PREENCHIMENTO ENCERROU";
		exit;
	}	
	
	
	#### BUSCA DADOS DO CONTRATO AVALIADO ####
	$qr_sql = "
				SELECT c.ds_empresa,
					   c.ds_servico,
					   TO_CHAR(ca.dt_inicio_avaliacao,'MM/YYYY') AS dt_ini,
					   TO_CHAR(ca.dt_fim_avaliacao,'MM/YYYY') AS dt_fim
				  FROM projetos.contrato_avaliacao ca
				  JOIN projetos.contrato c
					ON c.cd_contrato = ca.cd_contrato
				 WHERE ca.dt_limite_avaliacao   > CURRENT_TIMESTAMP
				   AND ca.dt_exclusao           IS NULL
				   AND ca.cd_contrato_avaliacao = ".$_REQUEST['cd_contrato_avaliacao']."
				   AND 0 < (SELECT COUNT(*) 
								FROM projetos.contrato_avaliacao_item cai
							   WHERE cai.cd_contrato_avaliacao =  ca.cd_contrato_avaliacao
							     AND cai.dt_exclusao           IS NULL
								 AND cai.cd_usuario_avaliador  = ".$_SESSION['Z'].")
	          ";
	$ob_resul = pg_query($db,$qr_sql);
	
	if(pg_num_rows($ob_resul) > 0)
	{
		$ar_reg   = pg_fetch_array($ob_resul);
		$tpl->newBlock('contrato');
		$tpl->assign('ds_empresa', $ar_reg['ds_empresa']);
		$tpl->assign('ds_servico', $ar_reg['ds_servico']);
		$tpl->assign('dt_ini', $ar_reg['dt_ini']);
		$tpl->assign('dt_fim', $ar_reg['dt_fim']);
		
		#### GRUPO DE PERGUNTAS ####
		$qr_sql = "
					SELECT cai.cd_contrato_avaliacao_item,
					       cai.cd_divisao,
						   cai.cd_usuario_avaliador,
						   cfg.cd_contrato_formulario_grupo,
						   cfg.ds_contrato_formulario_grupo
					  FROM projetos. cai
					  JOIN projetos.contrato_avaliacao ca
						ON ca.cd_contrato_avaliacao = cai.cd_contrato_avaliacao
					  JOIN projetos.contrato_formulario cf
						ON cf.cd_contrato_formulario = ca.cd_contrato_formulario
					   AND cf.dt_exclusao IS NULL
					  JOIN projetos.contrato_formulario_grupo cfg
						ON cfg.cd_contrato_formulario       = ca.cd_contrato_formulario
					   AND cfg.cd_contrato_formulario_grupo = cai.cd_contrato_formulario_grupo
					 WHERE ca.dt_limite_avaliacao   > CURRENT_TIMESTAMP
					   AND ca.dt_exclusao           IS NULL
					   AND ca.cd_contrato_avaliacao = ".$_REQUEST['cd_contrato_avaliacao']."
					   AND cai.cd_usuario_avaliador = ".$_SESSION['Z']."
					 ORDER BY cfg.nr_ordem ASC
				  ";
		$ob_grupo = pg_query($db,$qr_sql);
		$nr_grupo = 1;
		while($ar_grupo = pg_fetch_array($ob_grupo))
		{
			$tpl->newBlock('grupo');
			$tpl->assign('nr_grupo', $nr_grupo);		
			$tpl->assign('ds_grupo', $ar_grupo['ds_contrato_formulario_grupo']);		
			
			#### PERGUNTAS ####
			$qr_sql = "
						SELECT cd_contrato_formulario_pergunta,
						       ds_contrato_formulario_pergunta
						  FROM projetos.contrato_formulario_pergunta
						 WHERE dt_exclusao                  IS NULL
						   AND cd_contrato_formulario_grupo = ".$ar_grupo['cd_contrato_formulario_grupo']."
						 ORDER BY nr_ordem ASC
			          ";
			$ob_pergunta = pg_query($db,$qr_sql);
			$nr_pergunta = 1;
			while($ar_pergunta = pg_fetch_array($ob_pergunta))
			{
				$tpl->newBlock('pergunta');
				$tpl->assign('nr_pergunta', $nr_pergunta);		
				$tpl->assign('ds_pergunta', $ar_pergunta['ds_contrato_formulario_pergunta']);	
				$nr_classe = ($nr_classe == 1 ? 2 : 1);
				$tpl->assign('nr_classe', $nr_classe);		
				
				#### RESPOSTAS ####
				$qr_sql = "
							SELECT cd_contrato_formulario_resposta, 
							       cd_contrato_formulario_pergunta, 
								   cd_resposta, 
								   ds_resposta
							  FROM projetos.contrato_formulario_resposta
							 WHERE dt_exclusao                  IS NULL
							   AND cd_contrato_formulario_pergunta = ".$ar_pergunta['cd_contrato_formulario_pergunta']."
							 ORDER BY nr_ordem ASC							  
						  ";
				$ob_resposta = pg_query($db,$qr_sql);
				$nr_resposta = 1;
				while($ar_resposta = pg_fetch_array($ob_resposta))
				{
					$tpl->newBlock('resposta');
					$tpl->assign('nr_resposta', $nr_resposta);		
					$tpl->assign('ds_resposta', $ar_resposta['ds_resposta']);	
					$tpl->assign('cd_contrato_formulario_pergunta', $ar_resposta['cd_contrato_formulario_pergunta']);	
					$tpl->assign('cd_contrato_formulario_resposta', $ar_resposta['cd_contrato_formulario_resposta']);
					$tpl->assign('cd_contrato_avaliacao_item', $ar_resposta['cd_contrato_avaliacao_item']);

					
					$nr_resposta++;
				}				
				$nr_pergunta++;
			}
			$nr_grupo++;
		}
		
	}
	else
	{
		echo "AVALIAÇÃO DE CONTRATO INDISPONÍVEL";
		exit;
	}
	
	
	
	
	
	$tpl->printToScreen();
	pg_close($db);
?>