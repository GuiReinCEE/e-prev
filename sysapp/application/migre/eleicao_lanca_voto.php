<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');

	$tpl = new TemplatePower('tpl/tpl_eleicao_lanca_voto.html');
	$tpl->prepare();
	
	#$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	#include_once('inc/skin.php');

	$ANO_ELEICAO = 2010;
	
	$tpl->assignGlobal('ANO_ELEICAO', $ANO_ELEICAO);
	
	$tpl->assign('n', $n);
	$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);

	include_once('eleicao_permissao.php');
	
	#### VERIFICA ELEICAO ####
	$qr_sql = " 
				SELECT situacao,
                       num_votos
				  FROM eleicoes.eleicao
				 WHERE ano_eleicao = ".$ANO_ELEICAO."
				   AND cd_eleicao  = 1
			  ";
	$ob_resul = pg_query($db, $qr_sql);
	$fl_formulario = "";
	$form_cad      = "style='display:none;'"; 
	$fl_disabled   = "";
	if(pg_num_rows($ob_resul) > 0) 
	{	
		$ar_reg = pg_fetch_array($ob_resul);
		if ((trim($ar_reg['situacao']) <> 'A') or ($ar_reg['num_votos'] == 0))
		{
			if((trim($ar_reg['situacao']) == 'F') and ($ar_reg['num_votos'] > 0))
			{
				$fl_formulario = "";
				$form_cad      = "style='display:none;'";
				$fl_disabled   = "disabled";
			}
			else
			{
				$fl_formulario = "style='display:none;'";
				$form_cad      = "";
			}
		}	
	}
	else
	{
		echo "
				<script>
					alert('Não exite eleição para este ano.');
					document.location.href = 'workspace.php';
				</script>
			 ";
		exit;
	}	

	$tpl->assign('form_cad', $form_cad);
	$tpl->assign('fl_formulario', $fl_formulario);
	$tpl->assign('fl_disabled', $fl_disabled);
	
	if($fl_formulario == "")
	{

		#### TOTAL DA ELEICAO #####
		$qr_sql = "
					SELECT COALESCE(e.num_votos,0) AS qt_total_recebido,
					       COALESCE(e.votos_apurados,0) AS qt_total_valido,
					       COALESCE(e.invalidados, 0) AS qt_total_invalido,
					       COALESCE(e.votos_apurados,0) + COALESCE(e.invalidados, 0) AS qt_total_apurado
					  FROM eleicoes.eleicao e
					 WHERE e.ano_eleicao = ".$ANO_ELEICAO."
					   AND e.cd_eleicao = 1 
		          ";
		$ob_resul = pg_query($db, $qr_sql);
		$ar_reg   = pg_fetch_array($ob_resul);
		$tpl->assign('qt_total_recebido', $ar_reg['qt_total_recebido']);
		$tpl->assign('qt_total_apurado', $ar_reg['qt_total_apurado']);
		$tpl->assign('qt_total_valido', $ar_reg['qt_total_valido']);
		$tpl->assign('qt_total_invalido', $ar_reg['qt_total_invalido']);
		
		
		/*
		#### TOTAIS POR CARGO #####
		$qr_sql = "
					SELECT cce.cd_cargo,
					       SUM(aev.num_votos) AS qt_total
					  FROM eleicoes.eleicao e
					  JOIN eleicoes.candidatos_eleicoes ce
					    ON ce.ano_eleicao = e.ano_eleicao
					   AND ce.cd_eleicao  = e.cd_eleicao
					  JOIN eleicoes.cargos_eleicoes cce
					    ON cce.cd_cargo = ce.cd_cargo
					  JOIN eleicoes.apuracao_eleicoes aev
					    ON aev.ano_eleicao           = ce.ano_eleicao
					   AND aev.cd_eleicao            = ce.cd_eleicao
					   AND aev.cd_empresa            = ce.cd_empresa
					   AND aev.cd_registro_empregado = ce.cd_registro_empregado
					   AND aev.seq_dependencia       = ce.seq_dependencia
					 WHERE e.ano_eleicao = ".$ANO_ELEICAO."
					   AND e.cd_eleicao  = 1
					   AND cce.tp_cargo  = 'T'
					 GROUP BY cce.cd_cargo
		          ";
		$ob_resul = pg_query($db, $qr_sql);
		while ($ar_reg=pg_fetch_array($ob_resul)) 
		{
			if($ar_reg['cd_cargo'] == 10)
			{
				$tpl->assign('qt_total_deliberativo', (intval($ar_reg['qt_total']) > 0 ? $ar_reg['qt_total']/2 : intval($ar_reg['qt_total'])));
			}
			
			if($ar_reg['cd_cargo'] == 20)
			{
				$tpl->assign('qt_total_fiscal', $ar_reg['qt_total']);
			}
			
			if($ar_reg['cd_cargo'] == 30)
			{
				$tpl->assign('qt_total_diretor', $ar_reg['qt_total']);
			}
		}	
		*/

		#### LISTA CANDIDATOS CONSELHO DELIBERATIVO ####
		$qr_sql = " 
					SELECT TRIM(TO_CHAR(ce.cd_empresa,'00')) AS cd_empresa,
						   TRIM(TO_CHAR(ce.cd_registro_empregado,'000000')) AS cd_registro_empregado,
						   TRIM(TO_CHAR(ce.seq_dependencia,'00')) AS seq_dependencia,
					       ce.nome AS ds_candidato,
					       cce.nome AS cargo,
					       ae.num_votos AS qt_total_candidato
					  FROM eleicoes.eleicao e
					  JOIN eleicoes.candidatos_eleicoes ce
					    ON ce.ano_eleicao = e.ano_eleicao
					   AND ce.cd_eleicao  = e.cd_eleicao
					  JOIN eleicoes.cargos_eleicoes cce
					    ON cce.cd_cargo = ce.cd_cargo
					  JOIN eleicoes.apuracao_eleicoes ae
					    ON ae.ano_eleicao           = ce.ano_eleicao
					   AND ae.cd_eleicao            = ce.cd_eleicao
					   AND ae.cd_empresa            = ce.cd_empresa
					   AND ae.cd_registro_empregado = ce.cd_registro_empregado
					   AND ae.seq_dependencia       = ce.seq_dependencia
					 WHERE e.ano_eleicao = ".$ANO_ELEICAO."
					   AND e.cd_eleicao  = 1
					   AND cce.tp_cargo  = 'T'
					   AND cce.cd_cargo  = 10
					 ORDER BY ce.posicao
		           ";
		$ob_resul = pg_query($db, $qr_sql);
		$cor = "#DAE9F7";
		$cor_atual = $cor;
		while ($ar_reg=pg_fetch_array($ob_resul)) 
		{
			$tpl->newBlock('lista_deliberativo');
			
			if($cor == $cor_atual)
			{
				$cor_atual = "#FFFFFF";
			}
			else
			{
				$cor_atual = $cor;
			}
			
			$tpl->assign('bg_color', $cor_atual);
			
			$tpl->assign('ds_candidato', $ar_reg['ds_candidato']);
			$tpl->assign('cd_empresa', $ar_reg['cd_empresa']);
			$tpl->assign('cd_registro_empregado', $ar_reg['cd_registro_empregado']);
			$tpl->assign('seq_dependencia', $ar_reg['seq_dependencia']);
			$tpl->assign('qt_total_candidato', $ar_reg['qt_total_candidato']);
		}	

		
		#### LISTA CANDIDATOS CONSELHO FISCAL ####
		$qr_sql = " 
					SELECT TRIM(TO_CHAR(ce.cd_empresa,'00')) AS cd_empresa,
						   TRIM(TO_CHAR(ce.cd_registro_empregado,'000000')) AS cd_registro_empregado,
						   TRIM(TO_CHAR(ce.seq_dependencia,'00')) AS seq_dependencia,
					       ce.nome AS ds_candidato,
					       cce.nome AS cargo,
					       ae.num_votos AS qt_total_candidato
					  FROM eleicoes.eleicao e
					  JOIN eleicoes.candidatos_eleicoes ce
					    ON ce.ano_eleicao = e.ano_eleicao
					   AND ce.cd_eleicao  = e.cd_eleicao
					  JOIN eleicoes.cargos_eleicoes cce
					    ON cce.cd_cargo = ce.cd_cargo
					  JOIN eleicoes.apuracao_eleicoes ae
					    ON ae.ano_eleicao           = ce.ano_eleicao
					   AND ae.cd_eleicao            = ce.cd_eleicao
					   AND ae.cd_empresa            = ce.cd_empresa
					   AND ae.cd_registro_empregado = ce.cd_registro_empregado
					   AND ae.seq_dependencia       = ce.seq_dependencia
					 WHERE e.ano_eleicao = ".$ANO_ELEICAO."
					   AND e.cd_eleicao  = 1
					   AND cce.tp_cargo  = 'T'
					   AND cce.cd_cargo  = 20
					 ORDER BY ce.posicao
		           ";
		$ob_resul = pg_query($db, $qr_sql);
		$cor = "#DAE9F7";
		$cor_atual = $cor;		
		while ($ar_reg=pg_fetch_array($ob_resul)) 
		{
			$tpl->newBlock('lista_fiscal');
			
			if($cor == $cor_atual)
			{
				$cor_atual = "#FFFFFF";
			}
			else
			{
				$cor_atual = $cor;
			}			
			
			$tpl->assign('bg_color', $cor_atual);
			
			$tpl->assign('ds_candidato', $ar_reg['ds_candidato']);
			$tpl->assign('cd_empresa', $ar_reg['cd_empresa']);
			$tpl->assign('cd_registro_empregado', $ar_reg['cd_registro_empregado']);
			$tpl->assign('seq_dependencia', $ar_reg['seq_dependencia']);		
			$tpl->assign('qt_total_candidato', $ar_reg['qt_total_candidato']);
		}	
		
		#### LISTA CANDIDATOS DIRETOR ####
		$qr_sql = " 
					SELECT TRIM(TO_CHAR(ce.cd_empresa,'00')) AS cd_empresa,
						   TRIM(TO_CHAR(ce.cd_registro_empregado,'000000')) AS cd_registro_empregado,
						   TRIM(TO_CHAR(ce.seq_dependencia,'00')) AS seq_dependencia,
					       ce.nome AS ds_candidato,
					       cce.nome AS cargo,
					       ae.num_votos AS qt_total_candidato
					  FROM eleicoes.eleicao e
					  JOIN eleicoes.candidatos_eleicoes ce
					    ON ce.ano_eleicao = e.ano_eleicao
					   AND ce.cd_eleicao  = e.cd_eleicao
					  JOIN eleicoes.cargos_eleicoes cce
					    ON cce.cd_cargo = ce.cd_cargo
					  JOIN eleicoes.apuracao_eleicoes ae
					    ON ae.ano_eleicao           = ce.ano_eleicao
					   AND ae.cd_eleicao            = ce.cd_eleicao
					   AND ae.cd_empresa            = ce.cd_empresa
					   AND ae.cd_registro_empregado = ce.cd_registro_empregado
					   AND ae.seq_dependencia       = ce.seq_dependencia
					 WHERE e.ano_eleicao = ".$ANO_ELEICAO."
					   AND e.cd_eleicao  = 1
					   AND cce.tp_cargo  = 'T'
					   AND cce.cd_cargo  = 30
					 ORDER BY ce.posicao
		           ";
		$ob_resul = pg_query($db, $qr_sql);
		$cor = "#DAE9F7";
		$cor_atual = $cor;		
		while ($ar_reg=pg_fetch_array($ob_resul)) 
		{
			$tpl->newBlock('lista_diretor');
			
			if($cor == $cor_atual)
			{
				$cor_atual = "#FFFFFF";
			}
			else
			{
				$cor_atual = $cor;
			}			
			
			$tpl->assign('bg_color', $cor_atual);		

			$tpl->assign('ds_candidato', $ar_reg['ds_candidato']);
			$tpl->assign('cd_empresa', $ar_reg['cd_empresa']);
			$tpl->assign('cd_registro_empregado', $ar_reg['cd_registro_empregado']);
			$tpl->assign('seq_dependencia', $ar_reg['seq_dependencia']);		
			$tpl->assign('qt_total_candidato', $ar_reg['qt_total_candidato']);
		}	
	}
	
	$tpl->printToScreen();
	pg_close($db);
?>