<?php
	include_once('inc/sessao_auto_atendimento.php');
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');
	ini_set('auto_detect_line_endings', true);
	include_once('inc/class.SocketAbstraction.inc.php');
	include_once('inc/ePrev.Service.Projetos.php');
	include( 'oo/start.php' );
	using( array( 'public.bloqueto' ) );


	$tpl = new TemplatePower('tpl/tpl_auto_atendimento.html');
	$tpl->prepare();
	include_once('auto_atendimento_monta_sessao.php');
	
	$tabela_patrocinadora = '';

	$tabela_instituidor = '
							<BR>
							{TEXTO_CONTRIBUICAO}
							<BR>
							<div class="link_contrib">
								Contribuições disponíveis para pagamento:
								<BR><BR>
							</div>
							<table width="100%" cellspacing="1" cellpadding="0" border="0">
						  ';

	#### LOG ####
	$qr_sql = "
				INSERT INTO public.log_acessos_usuario 
					 (
					   sid,
					   hora,
					   pagina
					 ) 
				VALUES
					 (
					   ".$_SESSION['SID'].",
					   CURRENT_TIMESTAMP,
					   'DEBITOS'
					 )
		      ";
	@pg_query($db,$qr_sql); 

    
	$meses = array("Janeiro","Fevereiro","Março","Abril","Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro");

	
	$qr_sql = "
				SELECT COUNT(*) AS fl_ativo
				  FROM public.participantes
				 WHERE cd_empresa            = ".$_SESSION['EMP']."
				   AND cd_registro_empregado = ".$_SESSION['RE']."
				   AND seq_dependencia       = ".$_SESSION['SEQ']."
			       AND tipo_folha            IN (2,3,4,5,9,10,14,15,20,30,35,40,45,50,55,60,65,70,75,80)
		      ";
	$ob_resul = pg_query($db, $qr_sql);
	$ar_reg = pg_fetch_array($ob_resul);	
	
	if(intval($ar_reg['fl_ativo']) > 0)
	{
		$conteudo = '
					<br><br><br>
					<center>
						<h1 style="font-family: Calibri, Arial; font-size: 15pt;">
							Área somente para ATIVOS.
						</h1>
					</center>
					<br><br><br>
					';	
	}
	elseif($_SESSION['PLANO'] == 7) #### SENGE ####
    {
		$texto = "
			<div class='link_contrib' style='width: 100%; text-align: justify;'>
				Faça aqui a sua contribuição mensal e aportes. 
			</div>
			";		
		$tabela_instituidor = str_replace("{TEXTO_CONTRIBUICAO}",$texto,$tabela_instituidor);		
		
		#### VERIFICA SE HÁ BLOQUETO ABERTO (SEM PAGAMENTO) ####
		$qr_sql = "
					SELECT b.ano_competencia,
						   b.mes_competencia,
						   funcoes.cripto_re(b.cd_empresa,b.cd_registro_empregado,b.seq_dependencia) AS re,
						   funcoes.cripto_mes_ano(b.mes_competencia, b.ano_competencia) AS comp
					  FROM public.bloqueto b
					  JOIN public.participantes p
						ON p.cd_empresa            = b.cd_empresa
					   AND p.cd_registro_empregado = b.cd_registro_empregado
					   AND p.seq_dependencia       = b.seq_dependencia					  
					 WHERE b.cd_empresa            = ".$_SESSION['EMP']."
					   AND b.cd_registro_empregado = ".$_SESSION['RE']."
					   AND b.seq_dependencia       = ".$_SESSION['SEQ']."
					   AND b.status        IS NULL
					   AND b.data_retorno  IS NULL
					   AND (b.dt_vencimento >= CURRENT_DATE OR b.dt_limite_sem_encargos >= CURRENT_DATE)
					   AND b.dt_lancamento >= DATE_TRUNC('month', (CURRENT_DATE - '1 month'::interval))
					   AND b.dt_lancamento = (SELECT MAX(b2.dt_lancamento) 
									            FROM bloqueto b2 
								               WHERE b2.cd_registro_empregado = b.cd_registro_empregado 
									             AND b2.cd_empresa            = b.cd_empresa 
									             AND b2.seq_dependencia       = b.seq_dependencia)	
				     GROUP BY b.ano_competencia,
							  b.mes_competencia,
							  re, 
							  comp
					 
					 ORDER BY b.ano_competencia,
							  b.mes_competencia	
		          ";
		$ob_resul = pg_query($db,$qr_sql);
		
		#echo "<PRE>$qr_sql</PRE>"; exit;
		
		if(pg_num_rows($ob_resul) > 0)
		{
			#### MENSAL E ATRASADOS ####
			while ($ar_bloqueto = pg_fetch_array($ob_resul)) 
			{
				$linha_inst = '
								<tr>
									<td class="link_contrib">
										- {mes_extenso} de {ano}
									</td>
								</tr>
							  ';
				$linha_inst = str_replace('{RE}', $ar_bloqueto['re'], $linha_inst);
				$linha_inst = str_replace('{COMP}', $ar_bloqueto['comp'], $linha_inst);
				$linha_inst = str_replace('{ano}', $ar_bloqueto['ano_competencia'], $linha_inst);
				$linha_inst = str_replace('{mes_extenso}', $meses[$ar_bloqueto['mes_competencia']-1], $linha_inst);				
				$tabela_instituidor.= $linha_inst;
				
				$item_RE   = $ar_bloqueto['re'];
				$item_COMP = $ar_bloqueto['comp'];
			}
			
			$linha_inst = '
							<tr>
								<td>
									<BR>
									<a class="link_contrib" href="senge_pagamento.php?re='.$item_RE.'&comp='.$item_COMP.'" target="_blank">Clique aqui para efetuar o pagamento</a>
								</td>
							</tr>
						  ';	
			$tabela_instituidor.= $linha_inst;
		}
		else
		{
			#### ADICIONAL ####
			$qr_sql = "
						SELECT funcoes.cripto_re(".$_SESSION['EMP'].",".$_SESSION['RE'].",".$_SESSION['SEQ'].") AS re,
							   funcoes.cripto_mes_ano(99, 9999) AS comp
					  ";
			$ob_resul = pg_query($db,$qr_sql);		
			$ar_reg = pg_fetch_array($ob_resul);
			
			$linha_inst = '
							<tr>
								<td>
									<a class="link_contrib" style="color: blue;" href="senge_pagamento.php?re={RE}&comp={COMP}" target="_blank">- Contribuição Voluntária</a>
								</td>
							</tr>
						  ';
			$linha_inst = str_replace('{RE}', $ar_reg['re'], $linha_inst);
			$linha_inst = str_replace('{COMP}', $ar_reg['comp'], $linha_inst);
			$tabela_instituidor.= $linha_inst;			
		}
		$tabela_instituidor.="
							</table>
							<BR>
							";        
    } #SENGE
    elseif($_SESSION['PLANO'] == 8) #### SINPRORS ####
    {
		$texto = "
			<div class='link_contrib' style='width: 100%; text-align: justify;'>
				Faça aqui a sua contribuição mensal e aportes.
			</div>
			";		
		$tabela_instituidor = str_replace("{TEXTO_CONTRIBUICAO}",$texto,$tabela_instituidor);		
		
		#### VERIFICA SE HÁ BLOQUETO ABERTO (SEM PAGAMENTO) ####
		$qr_sql = "
					SELECT b.ano_competencia,
						   b.mes_competencia,
						   funcoes.cripto_re(b.cd_empresa,b.cd_registro_empregado,b.seq_dependencia) AS re,
						   funcoes.cripto_mes_ano(b.mes_competencia, b.ano_competencia) AS comp
					  FROM public.bloqueto b
					  JOIN public.participantes p
						ON p.cd_empresa            = b.cd_empresa
					   AND p.cd_registro_empregado = b.cd_registro_empregado
					   AND p.seq_dependencia       = b.seq_dependencia					  
					 WHERE b.cd_empresa            = ".$_SESSION['EMP']."
					   AND b.cd_registro_empregado = ".$_SESSION['RE']."
					   AND b.seq_dependencia       = ".$_SESSION['SEQ']."
					   AND b.status        IS NULL
					   AND b.data_retorno  IS NULL
					   AND (b.dt_vencimento >= CURRENT_DATE OR b.dt_limite_sem_encargos >= CURRENT_DATE)
					   AND b.dt_lancamento >= DATE_TRUNC('month', (CURRENT_DATE - '1 month'::interval))
					   AND CAST(b.dt_emissao AS DATE) <> CURRENT_DATE
					   AND b.dt_lancamento = (SELECT MAX(b2.dt_lancamento) 
									            FROM bloqueto b2 
								               WHERE b2.cd_registro_empregado = b.cd_registro_empregado 
									             AND b2.cd_empresa            = b.cd_empresa 
									             AND b2.seq_dependencia       = b.seq_dependencia)	
				     GROUP BY b.ano_competencia,
							  b.mes_competencia,
							  re, 
							  comp
					 
					 ORDER BY b.ano_competencia,
							  b.mes_competencia	
		          ";
		$ob_resul = pg_query($db,$qr_sql);
		
		#echo "<PRE>$qr_sql</PRE>"; exit;
		
		if(pg_num_rows($ob_resul) > 0)
		{
			#### MENSAL E ATRASADOS ####
			while ($ar_bloqueto = pg_fetch_array($ob_resul)) 
			{
				$linha_inst = '
								<tr>
									<td class="link_contrib">
										- {mes_extenso} de {ano}
									</td>
								</tr>
							  ';
				$linha_inst = str_replace('{RE}', $ar_bloqueto['re'], $linha_inst);
				$linha_inst = str_replace('{COMP}', $ar_bloqueto['comp'], $linha_inst);
				$linha_inst = str_replace('{ano}', $ar_bloqueto['ano_competencia'], $linha_inst);
				$linha_inst = str_replace('{mes_extenso}', $meses[$ar_bloqueto['mes_competencia']-1], $linha_inst);				
				$tabela_instituidor.= $linha_inst;
				
				$item_RE   = $ar_bloqueto['re'];
				$item_COMP = $ar_bloqueto['comp'];
			}
			
			$linha_inst = '
							<tr>
								<td>
									<BR>
									<a class="link_contrib" href="sinprors_pagamento.php?re='.$item_RE.'&comp='.$item_COMP.'" target="_blank">Clique aqui para efetuar o pagamento</a>
								</td>
							</tr>
						  ';	
			$tabela_instituidor.= $linha_inst;							  
			
		}
		else
		{
			#### ADICIONAL ####
			$qr_sql = "
						SELECT funcoes.cripto_re(".$_SESSION['EMP'].",".$_SESSION['RE'].",".$_SESSION['SEQ'].") AS re,
							   funcoes.cripto_mes_ano(99, 9999) AS comp
					  ";
			$ob_resul = pg_query($db,$qr_sql);		
			$ar_reg = pg_fetch_array($ob_resul);
			
			$linha_inst = '
							<tr>
								<td>
									<a class="link_contrib" style="color: blue;" href="sinprors_pagamento.php?re={RE}&comp={COMP}" target="_blank">- Contribuição Voluntária</a>
								</td>
							</tr>
						  ';
			$linha_inst = str_replace('{RE}', $ar_reg['re'], $linha_inst);
			$linha_inst = str_replace('{COMP}', $ar_reg['comp'], $linha_inst);
			$tabela_instituidor.= $linha_inst;			
		}
		$tabela_instituidor.="
							</table>
							<BR>
							<div style='text-align: justify;'>
								<a class='link_contrib' style='font-size: 95%' href='documentos/SINPRO_contribuicao_adicional.pdf' target='_blank'><img src='img/sinprors_pagamanento_info.gif' border='0'> Como fazer contribuições adicionais. Clique aqui.</a>
							</div>
							";        
    } # FIM SINPRORS
    elseif(intval($_SESSION['PLANO']) == 9) #### FAMILIA ####
	{
		$texto = "
			<div style='font-family: calibri, arial; font-size: 12pt; width: 100%; text-align: justify;'>
				Faça aqui a sua contribuição mensal e aportes.
			</div>
			";		
		$tabela_instituidor = str_replace("{TEXTO_CONTRIBUICAO}",$texto,$tabela_instituidor);		
		
		/*
		#### VERIFICA SE HÁ BLOQUETO ABERTO (SEM PAGAMENTO) ####
		$qr_sql = "
					SELECT b.ano_competencia,
						   b.mes_competencia,
						   funcoes.cripto_re(b.cd_empresa,b.cd_registro_empregado,b.seq_dependencia) AS re,
						   funcoes.cripto_mes_ano(b.mes_competencia, b.ano_competencia) AS comp,
						   b.id_suspensao_presumida,
						   (CASE WHEN b.id_suspensao_presumida = 'A' THEN 'Do Mês'
						         WHEN b.id_suspensao_presumida = 'S' THEN 'Suspensão Presumida'
						         WHEN b.id_suspensao_presumida = 'N' THEN 'Atrasada'
								 ELSE 'Contribuição'
						   END) AS ds_boleto
					  FROM public.bloqueto b
					  JOIN public.participantes p
						ON p.cd_empresa            = b.cd_empresa
					   AND p.cd_registro_empregado = b.cd_registro_empregado
					   AND p.seq_dependencia       = b.seq_dependencia					  
					 WHERE b.cd_empresa            = ".$_SESSION['EMP']."
					   AND b.cd_registro_empregado = ".$_SESSION['RE']."
					   AND b.seq_dependencia       = ".$_SESSION['SEQ']."
					   AND b.status        IS NULL
					   AND b.data_retorno  IS NULL
					   AND (b.dt_vencimento >= CURRENT_DATE OR b.dt_limite_sem_encargos >= CURRENT_DATE)
					   AND b.dt_lancamento >= DATE_TRUNC('month', (CURRENT_DATE - '1 month'::interval))
					   AND b.dt_lancamento = (SELECT MAX(b2.dt_lancamento) 
									            FROM bloqueto b2 
								               WHERE b2.cd_registro_empregado = b.cd_registro_empregado 
									             AND b2.cd_empresa            = b.cd_empresa 
									             AND b2.seq_dependencia       = b.seq_dependencia)	
				     GROUP BY b.ano_competencia,
							  b.mes_competencia,
							  re, 
							  comp,
							  b.id_suspensao_presumida,
							  ds_boleto
					 
					 ORDER BY b.ano_competencia DESC,
							  b.mes_competencia	DESC
		          ";
		$ob_resul = pg_query($db,$qr_sql);
		*/	
		
		$qr_sql = "
					SELECT ano_competencia, 
						   mes_competencia,
						   ds_boleto,
						   nr_competencia,
						   id_suspensao_presumida,
						   funcoes.cripto_mes_ano(mes_competencia,ano_competencia) AS comp,
                           (SELECT funcoes.cripto_re(".$_SESSION['EMP'].",".$_SESSION['RE'].",".$_SESSION['SEQ'].")) AS re
					  FROM boleto.boleto_instituidor((SELECT funcoes.cripto_re(".$_SESSION['EMP'].",".$_SESSION['RE'].",".$_SESSION['SEQ'].")))
					 WHERE COALESCE(id_suspensao_presumida,'') NOT IN ('S')
					 ORDER BY nr_ordem, ano_competencia DESC, mes_competencia DESC
				  ";
		$ob_resul = pg_query($db,$qr_sql);		  
		#echo "<PRE>$qr_sql</PRE>"; exit;		
		
		#echo "<PRE>$qr_sql</PRE>";
		
		if(pg_num_rows($ob_resul) > 0)
		{
			#### MENSAL E ATRASADOS ####
			while ($ar_bloqueto = pg_fetch_array($ob_resul)) 
			{
				if($ar_bloqueto['id_suspensao_presumida'] == "A")
				{
					$linha_inst = '
									<tr>
										<td>
											<a class="link_contrib" style="color: blue;" href="familia_pagamento_valor.php?re={RE}&comp={COMP}"  target="_blank">- {mes_extenso} de {ano}</a>
											<BR>
										</td>
									</tr>
								  ';
					$linha_inst = str_replace('{RE}', $ar_bloqueto['re'], $linha_inst);
					$linha_inst = str_replace('{COMP}', $ar_bloqueto['comp'], $linha_inst);
					$linha_inst = str_replace('{ano}', $ar_bloqueto['ano_competencia'], $linha_inst);
					$linha_inst = str_replace('{mes_extenso}', $ar_bloqueto['ds_boleto']." de ". $meses[$ar_bloqueto['mes_competencia']-1], $linha_inst);				
					$tabela_instituidor.= $linha_inst;
				}
				else if($ar_bloqueto['id_suspensao_presumida'] == "APORTE")
				{
					$linha_inst = '
									<tr>
										<td>
											<BR>
											<a class="link_contrib" style="color: blue;" href="familia_pagamento_valor.php?re={RE}&comp={COMP}"  target="_blank">- {mes_extenso}</a>
										</td>
									</tr>
								  ';
					$linha_inst = str_replace('{RE}', $ar_bloqueto['re'], $linha_inst);
					$linha_inst = str_replace('{COMP}', $ar_bloqueto['comp'], $linha_inst);
					$linha_inst = str_replace('{ano}', $ar_bloqueto['ano_competencia'], $linha_inst);
					$linha_inst = str_replace('{mes_extenso}', $ar_bloqueto['ds_boleto']." (". $ar_bloqueto['nr_competencia'].")", $linha_inst);				
					$tabela_instituidor.= $linha_inst;
				}				
				else
				{
				
					$linha_inst = '
									<tr>
										<td>
											<a class="link_contrib" href="familia_pagamento_valor.php?re={RE}&comp={COMP}"  target="_blank">- {mes_extenso} de {ano}</a>
										</td>
									</tr>
								  ';
					$linha_inst = str_replace('{RE}', $ar_bloqueto['re'], $linha_inst);
					$linha_inst = str_replace('{COMP}', $ar_bloqueto['comp'], $linha_inst);
					$linha_inst = str_replace('{ano}', $ar_bloqueto['ano_competencia'], $linha_inst);
					$linha_inst = str_replace('{mes_extenso}', $ar_bloqueto['ds_boleto']." de ". $meses[$ar_bloqueto['mes_competencia']-1], $linha_inst);				
					$tabela_instituidor.= $linha_inst;
				}
			}

			/*
			#### ADICIONAL ####
			$qr_sql = "
						SELECT funcoes.cripto_re(".$_SESSION['EMP'].",".$_SESSION['RE'].",".$_SESSION['SEQ'].") AS re,
							   funcoes.cripto_mes_ano(99, 9999) AS comp
					  ";
			$ob_resul = pg_query($db,$qr_sql);		
			$ar_reg = pg_fetch_array($ob_resul);
			
			$linha_inst = '
							<tr>
								<td>
									<br>
									<a class="link_contrib" style="color: blue;" href="familia_pagamento.php?re={RE}&comp={COMP}" target="_blank">- Aporte (Contribuição Voluntária)</a>
								</td>
							</tr>
						  ';
			$linha_inst = str_replace('{RE}', $ar_reg['re'], $linha_inst);
			$linha_inst = str_replace('{COMP}', $ar_reg['comp'], $linha_inst);
			$tabela_instituidor.= $linha_inst;	
			*/
		}
		$tabela_instituidor.="</table>";
	} # FIM FAMILIA
	else 
	{
		$msg ="";
		$tabela_instituidor = "";
		$tabela_patrocinadora = "
					<script type='text/javascript' src='inc/sort_table/sortabletable.js'></script>
					<link type='text/css' rel='StyleSheet' href='inc/sort_table/sortabletable.css'>	
					<div class='link_contrib' style='width: 100%; text-align: justify;'>
						Caso os valores pendentes no site já tenham sido pagos, os mesmos deverão ser desconsiderados<br/>
						Para pagamento e demais esclarecimentos, ligue 0800 51 2596 (de fixo) ou 51 3027 1221 (de celular)
					</div>
					<table class='sort-table' id='table-1' align='center' width='100%' cellspacing='2' cellpadding='2'>
						<thead>
						<tr> 
							<td>Descrição</td>					
							<td>Ano/Mês</td>					
							<td>Inicial</td>					
							<td>Encargo</td>					
							<td>Multa</td>					
							<td>Pago</td>					
							<td>Total</td>					
						</tr>
						</thead>
						<tbody>	
				  ";
		$tabela_patrocinadora_linha  = "
						<tr onmouseover='sortSetClassOver(this);' onmouseout='sortSetClassOut(this);'> 
							<td style='white-space: nowrap;'>
								{DESCRICAO}
							</td>					
							<td style='text-align:center;'>
								{ANO_COMPETENCIA}/{MES_COMPETENCIA}
							</td>	
							<td style='text-align:right;'>
								{VLR_INICIAL}
							</td>	
							<td style='text-align:right;'>
								{VLR_ENCARGO}
							</td>							
							<td style='text-align:right;'>
								{VLR_MULTA}
							</td>							
							<td style='text-align:right;'>
								{VLR_PAGO}
							</td>							
							<td style='text-align:right;'>
								{VLR_TOTAL}
							</td>														
						</tr>	
				  ";
		$tabela_patrocinadora_fim = '
						</tbody>	
						<tbody>
							<tr onmouseover="sortSetClassOver(this);" onmouseout="sortSetClassOut(this);">
								<td align="center"><b>Total:</b></td>
								<td></td>								
								<td style="text-align:right;">
									<b>{TOT_INICIAL}</b>
								</td>
								<td></td>
								<td></td>
								<td style="text-align:right;">
									{TOT_PAGO}
								</td>
								<td style="text-align:right;">
									{TOT_TOTAL}
								</td>								
							</tr>
						</tbody>
						
					</table>
		
					<script>
						var ob_resul = new SortableTable(document.getElementById("table-1"),["CaseInsensitiveString", "CaseInsensitiveString","Number","Number","Number","Number","Number"]);
							ob_resul.onsort = function () {
								var rows = ob_resul.tBody.rows;
								var l = rows.length;
								for (var i = 0; i < l; i++) {
									removeClassName(rows[i], i % 2 ? "sort-par" : "sort-impar");
									addClassName(rows[i], i % 2 ? "sort-impar" : "sort-par");
								}
							};
							ob_resul.sort(1, true);				
					</script>		
					  ';		
		
		
		$sql = " 
				 SELECT d.codigo_lancamento, 
				        c.descricao, 
				        d.ano_competencia, 
				        d.mes_competencia, 
				        d.vlr_inicial, 
				        d.vlr_encargo, 
				        d.vlr_multa, 
				        d.vlr_pago, 
				        d.vlr_saldo, 
				        current_date as dt_corrente, 
				        d.dt_lancamento, 
				        d.seq_lancamento, 
				        d.dt_pagamento 
				   FROM public.debitos d, 
				        public.codigos_cobrancas c 
				  WHERE d.cd_empresa            = ".$_SESSION['EMP']."
				    AND d.cd_registro_empregado = ".$_SESSION['RE']."
				    AND d.seq_dependencia       = ".$_SESSION['SEQ']."
					AND d.dt_lancamento         = (SELECT MAX(dt_lancamento) 
													 FROM debitos d2
													WHERE d2.cd_empresa            = d.cd_empresa 
													  AND d2.cd_registro_empregado = d.cd_registro_empregado 
													  AND d2.seq_dependencia       = d.seq_dependencia) 
				    AND d.vlr_pago              < d.vlr_inicial 
                    AND c.id_tipo               != 'P'
                    AND c.id_acerto             <> 2 -- DEVOLUCAO
				    AND c.codigo_lancamento     = d.codigo_lancamento 
				    AND NOT EXISTS (SELECT 1
				                      from cobrancas c1
				                     where c1.cd_empresa            = d.cd_empresa 
				                       and c1.cd_registro_empregado = d.cd_registro_empregado 
				                       and c1.seq_dependencia       = d.seq_dependencia 
				                       and c1.codigo_lancamento     = d.codigo_lancamento 
				                       and c1.mes_competencia       = d.mes_competencia 
				                       and c1.ano_competencia       = d.ano_competencia 
				                       and c1.seq_lancamento        = d.seq_lancamento 
				                       and c1.dt_lancamento         = d.dt_lancamento 
				                       and c1.sit_registro          = 'E' ) 
				  ORDER BY d.ano_competencia DESC, 
				           d.mes_competencia DESC
		       ";
		$rs = pg_exec($db, $sql);
		$linha_debito = "";
		if (pg_numrows($rs) > 0) 
		{
			$tot_saldo       = 0;
			$tot_vlr_inicial = 0;
			$tot_vlr_pago    = 0;
			while ($regOs = pg_fetch_array($rs))  
			{
				$linha_debito_tmp = $tabela_patrocinadora_linha;
				$linha_debito.= str_replace('{DESCRICAO}',       $regOs['descricao'], $linha_debito_tmp);
				$linha_debito = str_replace('{ANO_COMPETENCIA}', $regOs['ano_competencia'], $linha_debito);
				$linha_debito = str_replace('{MES_COMPETENCIA}', $regOs['mes_competencia'], $linha_debito);
				$linha_debito = str_replace('{VLR_INICIAL}',     number_format($regOs['vlr_inicial'],2,',','.'), $linha_debito);
				$linha_debito = str_replace('{VLR_ENCARGO}',     number_format($regOs['vlr_encargo'],2,',','.'), $linha_debito);
				$linha_debito = str_replace('{VLR_MULTA}',       number_format($regOs['vlr_multa'],2,',','.'), $linha_debito);
				$linha_debito = str_replace('{VLR_PAGO}',        number_format($regOs['vlr_pago'],2,',','.'), $linha_debito);
				$linha_debito = str_replace('{VLR_TOTAL}',       number_format($regOs['vlr_saldo'],2,',','.'), $linha_debito);	

				//Somatórios
				$tot_vlr_inicial += $regOs['vlr_inicial'];
				$tot_vlr_pago    += $regOs['vlr_pago'];
				$tot_vlr_saldo   += $regOs['vlr_saldo'];
			} 
			
			$tabela_patrocinadora_fim = str_replace('{TOT_INICIAL}', number_format($tot_vlr_inicial,2,',','.'), $tabela_patrocinadora_fim);
			$tabela_patrocinadora_fim = str_replace('{TOT_PAGO}',    number_format($tot_vlr_pago,2,',','.'), $tabela_patrocinadora_fim);
			$tabela_patrocinadora_fim = str_replace('{TOT_TOTAL}',   number_format($tot_vlr_saldo,2,',','.'), $tabela_patrocinadora_fim);
			$tabela_patrocinadora.= $linha_debito.$tabela_patrocinadora_fim;
		} 
		else 
		{
			$msg = "
					<br><br><br>
					<center>
						<h1 style='font-family: Calibri, Arial; font-size: 15pt;'>
							Não há registro.
						</h1>
					</center>
					<br><br><br>
				   ";			
			$tabela_patrocinadora = "";
			$tabela_instituidor = "";
			
		}
	}
	$conteudo = str_replace('{msg}', $msg, $conteudo);
	$conteudo = str_replace('{tabela_patrocinadora}', $tabela_patrocinadora, $conteudo);
	$conteudo = str_replace('{tabela_instituidor}', $tabela_instituidor, $conteudo);

//--------------------------------------------------------------------------------------------------
	$tpl->assign('conteudo',$conteudo);
	$tpl->printToScreen();
	pg_close($db);
//--------------------------------------------------------------------------------------------------
?>