<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');
	
	$tpl = new TemplatePower('tpl/tpl_eleicao_lotes_voto.html');
	$tpl->prepare();
	
	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');

	$tpl->assign('n', $n);
	$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);

	include_once('eleicao_permissao.php');
	
	
	#### VERIFICA ELEICAO ####
	$qr_sql = " 
				SELECT situacao,
                       num_votos
				  FROM eleicoes.eleicao
				 WHERE ano_eleicao = 2010
				   AND cd_eleicao  = 1
			  ";
	$ob_resul = pg_query($db, $qr_sql);
	$fl_disabled   = "";
	if(pg_num_rows($ob_resul) > 0) 
	{	
		$ar_reg = pg_fetch_array($ob_resul);
		if ((trim($ar_reg['situacao']) <> 'A') or ($ar_reg['num_votos'] == 0))
		{
			if((trim($ar_reg['situacao']) == 'F') and ($ar_reg['num_votos'] > 0))
			{
				$fl_disabled   = "disabled";
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
	
	
	#### LISTA CANDIDATOS CONSELHO DELIBERATIVO ####
	$qr_sql = " 
				SELECT lae.cd_lote,
				       ce.cd_cargo,
					   cce.nome AS ds_cargo,
				       ce.nome AS ds_candidato,
				       lae.num_votos AS qt_total_candidato,
					   TO_CHAR(lae.dt_hora_exclusao,'DD/MM/YYYY HH24:MI') AS dt_cancela,
					   uc.nome AS ds_usuario,
					   ce.posicao
				  FROM eleicoes.eleicao e
				  JOIN eleicoes.candidatos_eleicoes ce
				    ON ce.ano_eleicao = e.ano_eleicao
				   AND ce.cd_eleicao  = e.cd_eleicao
				  JOIN eleicoes.cargos_eleicoes cce
				    ON cce.cd_cargo = ce.cd_cargo
				  JOIN eleicoes.lotes_apuracao_eleicoes lae
				    ON lae.ano_eleicao           = ce.ano_eleicao
				   AND lae.cd_eleicao            = ce.cd_eleicao
				   AND lae.cd_empresa            = ce.cd_empresa
				   AND lae.cd_registro_empregado = ce.cd_registro_empregado
				   AND lae.seq_dependencia       = ce.seq_dependencia
				  LEFT JOIN projetos.usuarios_controledi uc
				    ON uc.codigo = lae.usu_exclusao
				 WHERE e.ano_eleicao = 2010
				   AND e.cd_eleicao  = 1
				   AND cce.tp_cargo  = 'T'

				UNION		  
						  
				SELECT lae.cd_lote,
				       0 AS cd_cargo,
				       'Kit Inválido' AS ds_cargo,
				       '' AS ds_candidato,
				       lae.num_votos AS qt_total_candidato,
				       TO_CHAR(lae.dt_hora_exclusao,'DD/MM/YYYY HH24:MI') AS dt_cancela,
				       uc.nome AS ds_usuario,
					   0 AS posicao 
				  FROM eleicoes.eleicao e
				  JOIN eleicoes.lotes_apuracao_eleicoes lae
				    ON lae.ano_eleicao           = e.ano_eleicao
				   AND lae.cd_eleicao            = e.cd_eleicao
				  LEFT JOIN projetos.usuarios_controledi uc
				    ON uc.codigo = lae.usu_exclusao
				 WHERE e.ano_eleicao = 2010
				   AND e.cd_eleicao  = 1
				   AND lae.cd_empresa = 99
				   AND lae.cd_registro_empregado = 999999
				   AND lae.seq_dependencia = 99

				 ORDER BY cd_lote DESC,
				          cd_cargo
				   
			   ";
	$ob_resul = pg_query($db, $qr_sql);
	$cd_lote_atual  = 0;
	$cd_cargo_atual = 0;
	$qt_total       = 0;
	$qt_total_cargo = 0;
	$cor = "#DAE9F7";
	$cor_atual = $cor;		
	while ($ar_reg=pg_fetch_array($ob_resul)) 
	{
		if($cd_lote_atual != $ar_reg['cd_lote'])
		{
			$tpl->newBlock('lote');
			$tpl->assign('cd_lote', $ar_reg['cd_lote']);
			$cd_lote_atual = $ar_reg['cd_lote'];
			$tpl->assign('fl_disabled', $fl_disabled);
			
			if($ar_reg['dt_cancela'] != "")
			{
				$tpl->assign('fl_exibe_botao', 'display:none;');
				$tpl->assign('dt_cancela', '<span style="color:red; font-weight: bold;">Lote cancelado em '.$ar_reg['dt_cancela'].' por '.$ar_reg['ds_usuario'].'.</span>');
			}
			
			$qt_total      = 0;
		}
		
		
		$tpl->newBlock('lista_lote');	

		if($cor == $cor_atual)
		{
			$cor_atual = "#FFFFFF";
		}
		else
		{
			$cor_atual = $cor;
		}			
		
		$tpl->assign('bg_color', $cor_atual);		
		
		$tpl->assign('ds_cargo', $ar_reg['ds_cargo']);
		$tpl->assign('ds_candidato', $ar_reg['ds_candidato']);
		$tpl->assign('qt_total_candidato', $ar_reg['qt_total_candidato']);
		$qt_total += $ar_reg['qt_total_candidato'];
		$qt_total_cargo += $ar_reg['qt_total_candidato'];
		

		

	}	
	//$tpl->newBlock('lista_lote_total');
	//$tpl->assign('qt_total', $qt_total);

			

	
	
	$tpl->printToScreen();
	pg_close($db);
?>