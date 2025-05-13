<?php
	$_APP_ID  = "e7a9e3f647dd33941430647118aaf2b7";
	
	require_once('inc/sessao_auto_atendimento.php');
	include_once('inc/class.SocketAbstraction2.inc.php');
	include_once('inc/class.TemplatePower.inc.php');
	include_once('inc/class.BrowserDetect.inc.php');
	include_once('funcoes.inc.php');
	include_once('inc/conexao.php');
	require_once('inc/config.inc.php');
	
	$_SESSAO_EMP_ANT = (MD5(uniqid(rand(),true)));
	$ano_base_ir = (date('Y') - 1);
	
	
	#### BUSCA INFO EMPRESTIMO ANDAMENTO ####
	$cmd = "prc_ext_cons_emp_call;".$_SESSION['EMP'].";".$_SESSION['RE'].";".$_SESSION['SEQ']; 
	$ob_skt = new Socket();
	$ob_skt->SetRemoteHost(SKT_IP);
	$ob_skt->SetRemotePort(SKT_PORTA);
	$ob_skt->SetBufferLength(131072);
	$ob_skt->SetConnectTimeOut(1);
	$ar_historio = Array();
	if ($ob_skt->Connect()) 
	{	
		$retorno = $ob_skt->Ask($cmd);
		if (!$ob_skt->Error()) 
		{
			#echo $retorno;
			
			$ar_tmp = explode(";",$retorno);
			
				$ar_historio['vl_saldo_devedor']  = number_format(str_replace(",","",$ar_tmp[3]),2,",",".");
				$ar_historio['vl_prest_atrasada'] = number_format(str_replace(",","",$ar_tmp[8]),2,",",".");
				$ar_historio['qt_prest']          = $ar_tmp[4];
				$ar_historio['qt_prest_paga']     = $ar_tmp[6];
				$ar_historio['qt_prest_atrasada'] = $ar_tmp[7];
				/*
					1 = 1;
					2 = 2,550.39;
					3 = 661.00;
					4 = 36;
					5 = 86.38;
					6 = 24;
					7 = 3;
					8 = 367.12;
					9 = P;

					1 Saida = Saida & CStr(nvl(CM("id_tem"), "")) & ";"
					2 Saida = Saida & Format(CDbl(swap_pv(nvl(CM("vlr_emprestimo"), 0))), "###,###,##0.00") & ";"
					3 Saida = Saida & Format(CDbl(swap_pv(nvl(CM("saldo_devedor"), 0))), "###,###,##0.00") & ";"
					4 Saida = Saida & nvl(CM("nro_parcelas"), "0") & ";"
					5 Saida = Saida & Format(CDbl(swap_pv(nvl(CM("vlr_prest"), "0"))), "###,###,##0.00") & ";"
					6 Saida = Saida & CStr(nvl(CM("nro_prest_pagas"), 0)) & ";"
					7 Saida = Saida & CStr(nvl(CM("nro_prest_atraso"), 0)) & ";"
					8 Saida = Saida & Format(CDbl(swap_pv(nvl(CM("total_atraso"), 0))), "###,###,##0.00") & ";"
					9 Saida = Saida & nvl(CM("po_pre_pos_fixado"), "P") & ";"
				*/
			
		}
	}
	
	
	#### QUANTIDADE DE PARCELAS PARA LIQUIDAÇÃO ####
	$qt_parcela = 0;
	if(intval($ar_historio['qt_prest_atrasada']) > 0)
	{
		$qt_parcela = intval($ar_historio['qt_prest_atrasada']);
	}
	else
	{
		$qt_parcela = intval($ar_historio['qt_prest']) - intval($ar_historio['qt_prest_paga']);
	}
	
	$qt_pagamento_vencer = '<select name="qtPagamentoVencer" id="qtPagamentoVencer" style="width: 100px;">';
	$nr_i = 1;

	### OS: 58179 ###
	if($qt_parcela > 3)
	{
		$qt_parcela = 3;
	}

	while($nr_i <= $qt_parcela)
	{
		$qt_pagamento_vencer.= '<option value="'.$nr_i.'">'.$nr_i.'</option>';
		$nr_i++;
	}
	$qt_pagamento_vencer.= '</select>';
	
	$qt_pagamento_atrasada = '<select name="qtPagamentoAtrasada" id="qtPagamentoAtrasada" style="width: 100px;">';
	$nr_i = 1;
	while($nr_i <= $qt_parcela)
	{
		$qt_pagamento_atrasada.= '<option value="'.$nr_i.'">'.$nr_i.'</option>';
		$nr_i++;
	}
	$qt_pagamento_atrasada.= '</select>';
	
	#### DATA VENCIMENTO LIQUIDAÇÃO ####
	$qr_sql = "
				SELECT TO_CHAR(date_trunc('day', dd)::DATE,'DD/MM/YYYY') AS dt_vencimento
                  FROM generate_series (CURRENT_DATE, CURRENT_DATE + '10 day'::INTERVAL, '1 day'::interval) dd
                 WHERE date_trunc('month', dd)::DATE < (date_trunc('month', CURRENT_DATE) + '1 month'::interval)::date;	
	          ";
	$ob_resul = pg_query($db,$qr_sql);	
	
	$dtPagamentoAtrasada = '<select name="dtPagamentoAtrasada" id="dtPagamentoAtrasada" style="width: 100px;">';
	$dtPagamentoVencer   = '<select name="dtPagamentoVencer" id="dtPagamentoVencer" style="width: 100px;">';
	$dtPagamentoTotal   = '<select name="dtPagamentoTotal" id="dtPagamentoTotal" style="width: 100px;">';
	while ($ar_reg = pg_fetch_array($ob_resul)) 
	{
		$dtPagamentoAtrasada.= '<option value="'.$ar_reg['dt_vencimento'].'">'.$ar_reg['dt_vencimento'].'</option>';
		$dtPagamentoVencer.= '<option value="'.$ar_reg['dt_vencimento'].'">'.$ar_reg['dt_vencimento'].'</option>';
		$dtPagamentoTotal.= '<option value="'.$ar_reg['dt_vencimento'].'">'.$ar_reg['dt_vencimento'].'</option>';
	}
	$dtPagamentoAtrasada.= '</select>';
	$dtPagamentoVencer.= '</select>';
	$dtPagamentoTotal.= '</select>';
	
	
	$tpl = new TemplatePower('tpl/tpl_auto_atendimento.html');
	$tpl->prepare();
	include_once('auto_atendimento_monta_sessao.php');

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
					   'INF_EMPRESTIMO'
					 )
		      ";
	@pg_query($db,$qr_sql); 	
	
	//<table width="100%" align="center"  cellspacing="2" cellpadding="2" border="0" style="font-size: 10pt; font-family: Verdana, Tahoma, Helvetica, sans-serif, arial;">
	
	
	#### 
	//http://app.eletroceee.com.br/srvautoatendimento/index.php/get_emprestimo_andamento 
	
	if(preg_match('/10.63.4.150/',$_SERVER['REMOTE_ADDR']))
	{
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, APP_SRV."/srvautoatendimento/index.php/get_emprestimo_andamento");
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS,"id_app=".$_APP_ID."&re_cripto=".$_SESSION['RE_CRIPTO']);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$_RETORNO = curl_exec($ch);
		curl_close ($ch);

		#print_r($_RETORNO); echo "<HR>";
		
		$FL_RETORNO = TRUE;
		$_RETORNO = json_decode($_RETORNO, TRUE);
		if (!(json_last_error() === JSON_ERROR_NONE))
		{
			switch (json_last_error()) 
			{
				case JSON_ERROR_NONE:
					$FL_RETORNO = TRUE;
				break;
					default:
					$FL_RETORNO = FALSE;
				break;
			}
		}
		#echo "X".$FL_RETORNO; echo "<HR>";
		#echo "<PRE>"; print_r($_RETORNO);  echo "<HR>";
		#exit;

		$ar_saldo_devedor_anterior = array();
		$ar_saldo_devedor_atual    = array();
		if($FL_RETORNO)
		{
			#echo $_RETORNO['error']['status'];echo "<HR>";
			if(intval($_RETORNO['error']['status']) == 0)
			{
				$ar_saldo_devedor_anterior = $_RETORNO['result']['andamento']['saldo_devedor_anterior'];
				$ar_saldo_devedor_atual    = $_RETORNO['result']['andamento']['saldo_devedor_atual'];
			}
		}	
		
		$tb_irpf = '
					<br>						
					<table width="580" class="sort-table" align="center" cellspacing="2" cellpadding="2">
						<thead>
							<tr>
								<td colspan="2" style="background: #4E7A4E; color:white;">
									<b>IRPF</b>
								</td>
							</tr>
						</thead>	
						<tbody>	
							<tr>
								<td>
									<b>CNPJ:</b>
								</td>
								<td>
									90.884.412/0001-24
								</td>	
							</tr>					
							<tr>
								<td colspan="2">					
				   ';		
		
		if(intval($ar_saldo_devedor_anterior) > 0)
		{
			$tb_irpf.= '
							<table border="0" width="100%" class="sort-table" align="center" cellspacing="2" cellpadding="2">
								<tbody>	

									<tr>
										<td width="25%">
											<b>Ano Base para IR:</b>
										</td>
										<td align="left">
											<b>'.$ar_saldo_devedor_anterior[0]['ano_base'].'</b>
										</td>	
									</tr>
									<tr>
										<td colspan="2">
											<table width="100%" class="sort-table" align="center" cellspacing="2" cellpadding="2">
												<thead>
													<tr>
														<td>
															Contrato
														</td>
														<td>
															Tipo
														</td>													
														<td>
															Saldo em '.$ar_saldo_devedor_anterior[0]['dt_base'].'
														</td>
														<td>
															IR
														</td>
													</tr>
												</thead>
												<tbody>	
						';
			$nr_total_irpf = 0;
			foreach($ar_saldo_devedor_anterior as $ar_reg)
			{
				$nr_total_irpf+=$ar_reg['saldo'];
				$tb_irpf.= '
											<tr>
												<td align="center">
													'.trim($ar_reg['cd_contrato']).'
												</td>
												<td align="center">
													'.utf8_decode(trim($ar_reg['tipo'])).'
												</td>								
												<td align="right">
													'.$ar_reg['saldo'].'
												</td>
												<td align="center">
													<a href="auto_atendimento_emprestimo_ir.php?ano_base_ir='.$ar_reg['ano_base'].'&cd_contrato='.$ar_reg['cd_contrato'].'" target="_blank">[download]</a>	
												</td>
											</tr>			
						   ';
			}	
			$tb_irpf.= '
												</tbody>
					   ';
			
			/* REVISAR #################***************************
			if (intval($ar_saldo_devedor_anterior) > 1)
			{
				$tb_irpf.= '		
												<tfoot>
													<tr class="sort-par">
														<td colspan="2" align="center">
															<b>Total</b>
														</td>
														<td align="right">
															<b>'.number_format(trim($nr_total_irpf),2,",",".").'</b>
														</td>	
													</tr>											
												</tfoot>
							';											
			}
			*/
			$tb_irpf.= '		
											</table>
										</td>								
									</tr>								
								</tbody>								
							</table>	
							<BR>
					   ';		
		}	
		
	
		if(intval($ar_saldo_devedor_atual) > 0)
		{
			$tb_irpf.= '
						   <table width="100%" class="sort-table" align="center" cellspacing="2" cellpadding="2">
								<tbody>	
									<tr>
										<td width="25%">
											<b>Ano Base para IR:</b>
										</td>
										<td align="left">
											<b>'.$ar_saldo_devedor_atual[0]['ano_base'].'</b>
										</td>	
									</tr>
									<tr>
										<td colspan="2">
											<table width="100%" class="sort-table" align="center" cellspacing="2" cellpadding="2">
												<thead>
													<tr>
														<td>
															Contrato
														</td>
														<td>
															Tipo
														</td>													
														<td>
															Saldo em '.$ar_saldo_devedor_atual[0]['dt_base'].'
														</td>
														<td>
															IR
														</td>
													</tr>
												</thead>
												<tbody>	
						';
			$nr_total_irpf = 0;
			foreach($ar_saldo_devedor_atual as $ar_reg)
			{
				$nr_total_irpf+=$ar_reg['saldo'];
				$tb_irpf.= '
											<tr>
												<td align="center">
													'.trim($ar_reg['cd_contrato']).'
												</td>
												<td align="center">
													'.utf8_decode(trim($ar_reg['tipo'])).'
												</td>								
												<td align="right">
													'.$ar_reg['saldo'].'
												</td>
												<td align="center">
													<a href="auto_atendimento_emprestimo_ir.php?ano_base_ir='.$ar_reg['ano_base'].'&cd_contrato='.$ar_reg['cd_contrato'].'" target="_blank">[download]</a>	
												</td>	
											</tr>			
						   ';
			}	
			$tb_irpf.= '
												</tbody>
					   ';
			/*REVISA ##################*****************************	   
			if (intval(pg_num_rows($ob_resul)) > 1)
			{
				$tb_irpf.= '		
												<tfoot>
													<tr class="sort-par">
														<td colspan="2" align="center">
															<b>Total</b>
														</td>
														<td align="right">
															<b>'.number_format(trim($nr_total_irpf),2,",",".").'</b>
														</td>	
													</tr>											
												</tfoot>
							';											
			}
			*/
			$tb_irpf.= '		
											</table>
										</td>								
									</tr>								
								</tbody>								
							</table>	
					   ';		
		}
		$tb_irpf.= '		
									</td>								
								</tr>								
							</tbody>								
						</table>	
						<BR>
				   ';		
	
	}
	else
	{

	
		#### SALDO DEVEDOR IRPF ANO ANTERIOR AO ANO BASE ####
		$qr_sql = "
					SELECT e.cd_contrato,
						   oracle.pck_emprestimos_2_fnc_saldo_devedor_atual(e.cd_contrato::INTEGER, '31/12/".($ano_base_ir - 1)."') AS saldo,
						   t.descricao AS tipo,
						   TO_CHAR(e.dt_deposito,'DD/MM/YYYY') AS dt_deposito,
						   TO_CHAR(e.dt_encerramento,'DD/MM/YYYY') AS dt_encerramento
					  FROM emprestimos e, 
						   emprestimos_patrocinadoras ep, 
						   tipos_emprestimos t
					 WHERE e.seq_emprestimo_patroc  = ep.seq_emprestimo_patroc
					   AND e.cd_plano               = ep.cd_plano
					   AND e.cd_empresa             = ep.cd_empresa
					   AND ep.cd_tipo               = t.cd_tipo
						   AND e.cd_empresa             = ".$_SESSION['EMP']."
						   AND e.cd_registro_empregado  = ".$_SESSION['RE']."
						   AND e.seq_dependencia        = ".$_SESSION['SEQ']."
					   AND e.id_situacao_emprestimo NOT IN (6,7,9,10)	
					   AND TO_DATE('31/12/".($ano_base_ir - 1)."','DD/MM/YYYY') BETWEEN e.dt_deposito AND COALESCE(e.dt_encerramento,TO_DATE('31/12/".($ano_base_ir - 1)."','DD/MM/YYYY'))
					 ORDER BY cd_contrato DESC		
				  ";
		#echo "<PRE> $qr_sql </PRE>"; exit;
		$ob_resul = pg_query($db,$qr_sql);
		$tb_irpf = '
					<br>						
					<table width="580" class="sort-table" align="center" cellspacing="2" cellpadding="2">
						<thead>
							<tr>
								<td colspan="2" style="background: #4E7A4E; color:white;">
									<b>IRPF</b>
								</td>
							</tr>
						</thead>	
						<tbody>	
							<tr>
								<td>
									<b>CNPJ:</b>
								</td>
								<td>
									90.884.412/0001-24
								</td>	
							</tr>					
							<tr>
								<td colspan="2">					
				   ';
		if(intval(pg_num_rows($ob_resul)) > 0)
		{
			$tb_irpf.= '
							<table border="0" width="100%" class="sort-table" align="center" cellspacing="2" cellpadding="2">
								<tbody>	

									<tr>
										<td width="25%">
											<b>Ano Base para IR:</b>
										</td>
										<td align="left">
											<b>'.($ano_base_ir - 1).'</b>
										</td>	
									</tr>
									<tr>
										<td colspan="2">
											<table width="100%" class="sort-table" align="center" cellspacing="2" cellpadding="2">
												<thead>
													<tr>
														<td>
															Contrato
														</td>
														<td>
															Tipo
														</td>													
														<td>
															Saldo em 31/12/'.($ano_base_ir - 1).'
														</td>
														<td>
															IR
														</td>
													</tr>
												</thead>
												<tbody>	
						';
			$nr_total_irpf = 0;
			while ($ar_reg = pg_fetch_array($ob_resul)) 
			{
				$nr_total_irpf+=$ar_reg['saldo'];
				$tb_irpf.= '
											<tr>
												<td align="center">
													'.trim($ar_reg['cd_contrato']).'
												</td>
												<td align="center">
													'.trim($ar_reg['tipo']).'
												</td>								
												<td align="right">
													'.number_format(floatval($ar_reg['saldo']),2,",",".").'
												</td>
												<td align="center">
													<a href="auto_atendimento_emprestimo_ir.php?ano_base_ir='.($ano_base_ir - 1).'&cd_contrato='.$ar_reg['cd_contrato'].'" target="_blank">[download]</a>	
												</td>
											</tr>			
						   ';
			}	
			$tb_irpf.= '
												</tbody>
					   ';
					   
			if (intval(pg_num_rows($ob_resul)) > 1)
			{
				$tb_irpf.= '		
												<tfoot>
													<tr class="sort-par">
														<td colspan="2" align="center">
															<b>Total</b>
														</td>
														<td align="right">
															<b>'.number_format(trim($nr_total_irpf),2,",",".").'</b>
														</td>	
													</tr>											
												</tfoot>
							';											
			}
			$tb_irpf.= '		
											</table>
										</td>								
									</tr>								
								</tbody>								
							</table>	
							<BR>
					   ';		
		}	
		
		#### SALDO DEVEDOR IRPF ANO BASE ####
		$qr_sql = "
					SELECT e.cd_contrato,
						   oracle.pck_emprestimos_2_fnc_saldo_devedor_atual(e.cd_contrato::INTEGER, '31/12/".$ano_base_ir."') AS saldo,
						   t.descricao AS tipo,
						   TO_CHAR(e.dt_deposito,'DD/MM/YYYY') AS dt_deposito,
						   TO_CHAR(e.dt_encerramento,'DD/MM/YYYY') AS dt_encerramento
					  FROM emprestimos e, 
						   emprestimos_patrocinadoras ep, 
						   tipos_emprestimos t
					 WHERE e.seq_emprestimo_patroc  = ep.seq_emprestimo_patroc
					   AND e.cd_plano               = ep.cd_plano
					   AND e.cd_empresa             = ep.cd_empresa
					   AND ep.cd_tipo               = t.cd_tipo
						   AND e.cd_empresa             = ".$_SESSION['EMP']."
						   AND e.cd_registro_empregado  = ".$_SESSION['RE']."
						   AND e.seq_dependencia        = ".$_SESSION['SEQ']."
					   AND e.id_situacao_emprestimo NOT IN (6,7,9,10)	
					   AND TO_DATE('31/12/".$ano_base_ir."','DD/MM/YYYY') BETWEEN e.dt_deposito AND COALESCE(e.dt_encerramento,TO_DATE('31/12/".$ano_base_ir."','DD/MM/YYYY'))
					 ORDER BY cd_contrato DESC		
				  ";
		#echo "<!-- <PRE> $qr_sql </PRE>-->";
		$ob_resul = pg_query($db,$qr_sql);
		
		if(intval(pg_num_rows($ob_resul)) > 0)
		{
			$tb_irpf.= '
						   <table width="100%" class="sort-table" align="center" cellspacing="2" cellpadding="2">
								<tbody>	
									<tr>
										<td width="25%">
											<b>Ano Base para IR:</b>
										</td>
										<td align="left">
											<b>'.$ano_base_ir.'</b>
										</td>	
									</tr>
									<tr>
										<td colspan="2">
											<table width="100%" class="sort-table" align="center" cellspacing="2" cellpadding="2">
												<thead>
													<tr>
														<td>
															Contrato
														</td>
														<td>
															Tipo
														</td>													
														<td>
															Saldo em 31/12/'.$ano_base_ir.'
														</td>
														<td>
															IR
														</td>
													</tr>
												</thead>
												<tbody>	
						';
			$nr_total_irpf = 0;
			while ($ar_reg = pg_fetch_array($ob_resul)) 
			{
				$nr_total_irpf+=$ar_reg['saldo'];
				$tb_irpf.= '
											<tr>
												<td align="center">
													'.trim($ar_reg['cd_contrato']).'
												</td>
												<td align="center">
													'.trim($ar_reg['tipo']).'
												</td>								
												<td align="right">
													'.number_format(floatval($ar_reg['saldo']),2,",",".").'
												</td>
												<td align="center">
													<a href="auto_atendimento_emprestimo_ir.php?ano_base_ir='.$ano_base_ir.'&cd_contrato='.$ar_reg['cd_contrato'].'" target="_blank">[download]</a>	
												</td>	
											</tr>			
						   ';
			}	
			$tb_irpf.= '
												</tbody>
					   ';
					   
			if (intval(pg_num_rows($ob_resul)) > 1)
			{
				$tb_irpf.= '		
												<tfoot>
													<tr class="sort-par">
														<td colspan="2" align="center">
															<b>Total</b>
														</td>
														<td align="right">
															<b>'.number_format(trim($nr_total_irpf),2,",",".").'</b>
														</td>	
													</tr>											
												</tfoot>
							';											
			}
			$tb_irpf.= '		
											</table>
										</td>								
									</tr>								
								</tbody>								
							</table>	
					   ';		
		}
		$tb_irpf.= '		
									</td>								
								</tr>								
							</tbody>								
						</table>	
						<BR>
				   ';	
	}
	
	$demonstrativo = '
                        <table width="580" class="sort-table" align="center" cellspacing="2" cellpadding="2">
							<thead>
								<tr>
									<td colspan="2" style="background: #4E7A4E; color:white;">
										<b>EMPRÉSTIMO EM ANDAMENTO</b>
									</td>
								</tr>
							</thead>
							<tbody>	
								<tr>
									<td width="25%">
										<b>Contrato:</b>
									</td>
									<td style="color: blue;">
										<b>{CONTRATO}</b>
									</td>	
								</tr>
								<tr>
									<td colspan="2" align="center">
									<a style="font-size: 10pt;" href="contrato_emprestimo.php?cd_contrato={CONTRATO}&amp;fl_cad=S&amp;fl_fin=S&amp;fl_ass=S&amp;tp_imp=3&amp;fl_dem=S" target="_blank">Clique aqui para visualizar o demonstrativo do empréstimo</a>
									<input type="hidden" id="cd_contrato_emprestimo" name="cd_contrato_emprestimo" value="{CONTRATO}">
									</td>									
								</tr>						
							</tbody>								
						</table>	
	
						'.$tb_irpf.'
					
                        <table width="580" class="sort-table" align="center" cellspacing="2" cellpadding="2">
							<thead>
								<tr>
									<td colspan="4" style="background: #4E7A4E; color:white;">
										<b>SITUAÇÃO DO EMPRÉSTIMO</b>
									</td>
								</tr>
							</thead>
							<tbody>	
								<tr>
									<td style="width: 120px;">
										<b>Saldo Devedor Atual:</b>
									</td>
									<td style="width: 120px;">
										R$ '.($ar_historio['vl_saldo_devedor']).'
									</td>	
									<td style="width: 150px;">
										<b>Prestações Atrasadas:</b>
									</td>
									<td style="width: 120px;">
										R$ '.($ar_historio['vl_prest_atrasada']).'
									</td>								
								</tr>
								<tr>
									<td>
										<b>Nr Parcelas Pagas:</b>
									</td>	
									<td>										
										'.($ar_historio['qt_prest_paga']).' de '.($ar_historio['qt_prest']).'
									</td>	
									<td>
										<b>Nr Parcelas Atrasadas:</b>
									</td>	
									<td>										
										'.($ar_historio['qt_prest_atrasada']).'
									</td>								
								</tr>	
								<tr>
									<td colspan="4" align="center">
										<BR>
										<input type="button" value="Simular Postergar Parcelas" name="Submit" onclick="postergarEmprestimo();" class="botao" style="width: 190px;">
										
										'.(
												intval($ar_historio['qt_prest_atrasada']) > 0 
												? 
													'<input type="button" value="Simular Pagar Parcelas" name="Submit" onclick="pagamentoEmprestimoParcelasAtrasada();" class="botao" style="width: 180px;">' 
												: 
													'<input type="button" value="Simular Pagar Parcelas" name="Submit" onclick="pagamentoEmprestimoParcelasVencer();" class="botao" style="width: 180px;">' 
										).'
										<input type="button" value="Simular Pagamento Total" name="Submit" onclick="pagamentoEmprestimoTotal();" class="botao" style="width: 180px;">
										
										
									</td>								
								</tr>								
							</tbody>								
						</table>	
						<br>
	                 ';
				 
	$tabela_pre = "
				<script type='text/javascript' src='inc/sort_table/sortabletable.js'></script>
				<link type='text/css' rel='StyleSheet' href='inc/sort_table/sortabletable.css'>";

	$tabela_pre .= "
                        <table width='580' class='sort-table' align='center' cellspacing='2' cellpadding='2'>
							<thead>
								<tr>
									<td style='background: #4E7A4E; color:white;'>
										<b>SITUAÇÃO DAS PARCELAS DO EMPRÉSTIMO</b>
									</td>
								</tr>
							</thead>
							<tbody>	
								<tr>	
									<td align='center'>
									
									".(
										(intval($ar_historio['qt_prest_atrasada']) > 0)
										?
										"Caso os valores pendentes já tenham sido pagos, os mesmos deverão ser desconsiderados."
										:
										""
									)."
									
				<table class='sort-table' id='table-1' align='center'  cellspacing='2' cellpadding='2'>
					<thead>
					<tr> 
						<td>
							Parcela
						</td>
						<td>
							Ano/Mês
								
						</td>
						<td>
							Situação
						</td>
						<td>
							Pgto Parcial
						</td>							
						<td>
							Valor
						</td>					
					</tr>
					</thead>
					<tbody>		
	          ";
			  
	$tabela_pos = "
                        <table width='580' class='sort-table' align='center' cellspacing='2' cellpadding='2'>
							<thead>
								<tr>
									<td style='background: #4E7A4E; color:white;'>
										<b>PARCELAS DO EMPRÉSTIMO</b>
									</td>
								</tr>
							</thead>
							<tbody>	
								<tr>	
									<td>
	
				<script type='text/javascript' src='inc/sort_table/sortabletable.js'></script>
				<link type='text/css' rel='StyleSheet' href='inc/sort_table/sortabletable.css'>	
				<div style='text-align:center; font-family:arial; font-size: 11pt;'>
					(*) A prestação será ajustada pela variação do INPC-IBGE divulgada no mês anterior ao vencimento.
				</div>				
				<br>
				<table class='sort-table' id='table-1' align='center'  cellspacing='2' cellpadding='2'>
					<thead>
					<tr> 
						<td>
							Parcela
						</td>
						<td>
							Ano/Mês
								
						</td>
						<td>
							Situação
						</td>
						<td>
							Pgto Parcial
						</td>							
						<td>
							Valor(*)
						</td>					
					</tr>
					</thead>
					<tbody>		
	          ";			  
			  
	$tr_modelo = "
					<tr onmouseover='sortSetClassOver(this);' onmouseout='sortSetClassOut(this);'> 
						<td align='center'>
							{NUM_PARCELA}
						</td>
						<td align='center'>
							{ANO}/{MES}
						</td>
						<td align='center' style='color:{SITUACAO_COR};'>
							<b>{SITUACAO}</b>
						</td>
						<td align='right'>
							{VALOR_PARCIAL}
						</td>							
						<td align='right'>
							{VALOR}
						</td>							
					</tr>	
	               ";
	$tr_linhas = "";	
	$tabela_fim = '
					</tbody>	
				</table>
									</td>
								</tr>						
							</tbody>								
						</table>				
				<BR>
				<script>
					var ob_resul = new SortableTable(document.getElementById("table-1"),["CaseInsensitiveString","CaseInsensitiveString", "CaseInsensitiveString","NumberFloat","NumberFloat"]);
						ob_resul.onsort = function () {
							var rows = ob_resul.tBody.rows;
							var l = rows.length;
							for (var i = 0; i < l; i++) {
								removeClassName(rows[i], i % 2 ? "sort-par" : "sort-impar");
								addClassName(rows[i], i % 2 ? "sort-impar" : "sort-par");
							}
						};
					ob_resul.sort(0, false);	
				</script>		
				
				<script src="inc/date_pack.js" type="text/javascript"></script>
				
				<script>
					var ds_url = "auto_atendimento_emprestimo_anterior_ajax.php";
					
					// -- PAGAMENTO A ATRASADA -- //
					function pagamentoEmprestimoParcelasAtrasada()
					{
						$("#pagamentoEmprestimoAtrasadaContrato1").html(document.getElementById("cd_contrato_emprestimo").value);
						
						$("#pagamentoEmprestimoAtrasada").modal({
							focus:false,
							containerCss:{
								width:600,
								height:400
								},
							onClose: function (dialog) {
								$.modal.close();
							}					
						});	
					}
					
					function pagamentoEmprestimoAtrasadaSimular()
					{
						$("#pagamentoEmprestimoAtrasadaSimulador").show();
						$("#pagamentoEmprestimoAtrasadaResultado").hide();
					}					
					
					function pagamentoEmprestimoAtrasadaSimularExecutar()
					{
						if($.trim($("#dtPagamentoAtrasada").val()) == "")
						{
							alert("Informe a Data de Pagamento.");
							$("#dtPagamentoAtrasada").focus();
						}
						else if($("#qtPagamentoAtrasada").val() < 1)
						{
							alert("Informe a Quantidade de Parcelas.");
							$("#qtPagamentoAtrasada").focus();
						}						
						else
						{
							$.post(ds_url,
							{
								ds_funcao    : "calculaLiquidacaoAtrasada",
								cd_contrato  : document.getElementById("cd_contrato_emprestimo").value,
								dt_pagamento : document.getElementById("dtPagamentoAtrasada").value,
								qt_parcela   : document.getElementById("qtPagamentoAtrasada").value
							},
							function(data)
							{
								var obj = data;
								
								if(typeof obj === "object")
								{
									if(obj.fl_erro == "S")
									{
										alert("ERRO "+obj.cd_erro+":\\n"+obj.retorno);
									}
									else
									{
										$("#pagamentoEmprestimoAtrasadaContrato2").html(document.getElementById("cd_contrato_emprestimo").value);
										$("#pagamentoEmprestimoAtrasadaQtPagamento").html(document.getElementById("qtPagamentoAtrasada").value);
										$("#pagamentoEmprestimoAtrasadaDtPagamento").html(document.getElementById("dtPagamentoAtrasada").value);
										$("#pagamentoEmprestimoAtrasadaValor").html(obj.ar_reg.vl_valor);
										$("#pagamentoEmprestimoAtrasadaSimulador").hide();
										$("#pagamentoEmprestimoAtrasadaResultado").show();	

										$("#formPgAtrasadaContrato").val(document.getElementById("cd_contrato_emprestimo").value);				
										$("#formPgAtrasadaDtPagamento").val(document.getElementById("dtPagamentoAtrasada").value);				
										$("#formPgAtrasadaQtPagamento").val(document.getElementById("qtPagamentoAtrasada").value);				
										$("#formPgAtrasadaValor").val(obj.ar_reg.vl_valor_calc);				
									}
								}
							},
							"json");
						}
					}
					
					function pagamentoEmprestimoAtrasadaSimularBoleto()
					{
						$("#pagamentoEmprestimoAtrasadaBoletoForm").submit();
					}					
					
					
					// -- PAGAMENTO A VENCER -- //
					function pagamentoEmprestimoParcelasVencer()
					{
						$("#pagamentoEmprestimoVencerContrato1").html(document.getElementById("cd_contrato_emprestimo").value);
						
						$("#pagamentoEmprestimoVencer").modal({
							focus:false,
							containerCss:{
								width:600,
								height:400
								},
							onClose: function (dialog) {
								$.modal.close();
							}					
						});	
					}
					
					function pagamentoEmprestimoVencerSimular()
					{
						$("#pagamentoEmprestimoVencerSimulador").show();
						$("#pagamentoEmprestimoVencerResultado").hide();
					}					
					
					function pagamentoEmprestimoVencerSimularExecutar()
					{
						if($.trim($("#dtPagamentoVencer").val()) == "")
						{
							alert("Informe a Data de Pagamento.");
							$("#dtPagamentoVencer").focus();
						}
						else if($("#qtPagamentoVencer").val() < 1)
						{
							alert("Informe a Quantidade de Parcelas.");
							$("#qtPagamentoVencer").focus();
						}						
						else
						{
							$.post(ds_url,
							{
								ds_funcao    : "calculaLiquidacaoVencer",
								cd_contrato  : document.getElementById("cd_contrato_emprestimo").value,
								dt_pagamento : document.getElementById("dtPagamentoVencer").value,
								qt_parcela   : document.getElementById("qtPagamentoVencer").value
							},
							function(data)
							{
								var obj = data;
								
								if(typeof obj === "object")
								{
									if(obj.fl_erro == "S")
									{
										alert("ERRO "+obj.cd_erro+":\\n"+obj.retorno);
									}
									else
									{
										$("#pagamentoEmprestimoVencerContrato2").html(document.getElementById("cd_contrato_emprestimo").value);
										$("#pagamentoEmprestimoVencerQtPagamento").html(document.getElementById("qtPagamentoVencer").value);
										$("#pagamentoEmprestimoVencerDtPagamento").html(document.getElementById("dtPagamentoVencer").value);
										$("#pagamentoEmprestimoVencerValor").html(obj.ar_reg.vl_valor);
										$("#pagamentoEmprestimoVencerValorAbatimento").html(obj.ar_reg.vl_valor_abatimento);
										$("#pagamentoEmprestimoVencerSimulador").hide();
										$("#pagamentoEmprestimoVencerResultado").show();	

										$("#formPgVencerContrato").val(document.getElementById("cd_contrato_emprestimo").value);				
										$("#formPgVencerDtPagamento").val(document.getElementById("dtPagamentoVencer").value);				
										$("#formPgVencerQtPagamento").val(document.getElementById("qtPagamentoVencer").value);				
										$("#formPgVencerValor").val(obj.ar_reg.vl_valor_calc);				
									}
								}
							},
							"json");
						}
					}
					
					function pagamentoEmprestimoVencerSimularBoleto()
					{
						$("#pagamentoEmprestimoVencerBoletoForm").submit();
					}					
					
					// -- PAGAMENTO TOTAL -- //
					function pagamentoEmprestimoTotal()
					{
						$("#pagamentoEmprestimoTotalContrato1").html(document.getElementById("cd_contrato_emprestimo").value);
						
						$("#pagamentoEmprestimoTotal").modal({
							focus:false,
							containerCss:{
								width:600,
								height:400
								},
							onClose: function (dialog) {
								$.modal.close();
							}					
						});			
					}
					
					function pagamentoEmprestimoTotalSimular()
					{
						$("#pagamentoEmprestimoTotalSimulador").show();
						$("#pagamentoEmprestimoTotalResultado").hide();
					}	

					function pagamentoEmprestimoTotalSimularExecutar()
					{
						if($.trim($("#dtPagamentoTotal").val()) != "")
						{	
							$.post(ds_url,
							{
								ds_funcao    : "calculaLiquidacaoTotal",
								cd_contrato  : document.getElementById("cd_contrato_emprestimo").value,
								dt_pagamento : document.getElementById("dtPagamentoTotal").value
							},
							function(data)
							{
								var obj = data;
								
								if(typeof obj === "object")
								{
									if(obj.fl_erro == "S")
									{
										alert("ERRO "+obj.cd_erro+":\\n"+obj.retorno);
									}
									else
									{
										$("#pagamentoEmprestimoTotalContrato2").html(document.getElementById("cd_contrato_emprestimo").value);
										$("#pagamentoEmprestimoTotalDtPagamento").html(document.getElementById("dtPagamentoTotal").value);
										$("#pagamentoEmprestimoTotalValor").html(obj.ar_reg.vl_valor);
										$("#pagamentoEmprestimoTotalValorAbatimento").html(obj.ar_reg.vl_valor_abatimento);
										$("#pagamentoEmprestimoTotalSimulador").hide();
										$("#pagamentoEmprestimoTotalResultado").show();	

										$("#formPgTotalContrato").val(document.getElementById("cd_contrato_emprestimo").value);				
										$("#formPgTotalDtPagamento").val(document.getElementById("dtPagamentoTotal").value);				
										$("#formPgTotalValor").val(obj.ar_reg.vl_valor_calc);				
									}
								}
							},
							"json");
						}
						else
						{
							alert("Informe a Data de Pagamento.");
							$("#dtPagamentoTotal").focus();
						}
					}
					
					function pagamentoEmprestimoTotalSimularBoleto()
					{
						$("#pagamentoEmprestimoTotalBoletoForm").submit();
					}
					
					
					// -- POSTERGAR -- //
					function postergarEmprestimo()
					{
						$("#postergarEmprestimoContrato").html(document.getElementById("cd_contrato_emprestimo").value);
						$("#formPostergarContrato").val(0);
						
						if($.trim(document.getElementById("cd_contrato_emprestimo").value) != "")
						{	
							$.post(ds_url,
							{
								ds_funcao    : "calculaPostegar",
								cd_contrato  : document.getElementById("cd_contrato_emprestimo").value
							},
							function(data)
							{
								var obj = data;
								
								if(typeof obj === "object")
								{
									if(obj.fl_erro == "S")
									{
										alert("ERRO "+obj.cd_erro+":\\n"+obj.retorno);
									}
									else
									{
										$("#formPostergarContrato").val(document.getElementById("cd_contrato_emprestimo").value);	
										$("#postergarEmprestimoValor").html(obj.vl_parcela);	
										$("#postergarEmprestimoData").html(obj.dt_ultima);	
										
										$("#postergarEmprestimo").modal({
											focus:false,
											containerCss:{
												width:850,
												height:630
												},
											onClose: function (dialog) {
												$.modal.close();
											}					
										});														
													
									}
								}
							},"json");
						}						
					}

					function postergarEmprestimoConcede()
					{
						var confirmacao = "Esta operação será irreversível e a parcela do próximo mês irá para o final do contrato, alterando o valor das próximas parcelas.\\n\\n"+
				            "Clique [Ok] para Confirmar\\n\\n"+
				            "Clique [Cancelar] para Cancelar\\n\\n";

				        if(confirm(confirmacao))
				        {
							if($.trim(document.getElementById("cd_contrato_emprestimo").value) != "")
							{	
								document.getElementById("assinatura-base64").value = document.getElementById("assinatura").toDataURL("image/jpeg");

								$.post(ds_url,
								{
									ds_funcao          : "concedePostegar",
									cd_contrato        : document.getElementById("cd_contrato_emprestimo").value,
									assinatura_base64  : document.getElementById("assinatura-base64").value
								},
								function(data)
								{
									var obj = data;
									
									if(typeof obj === "object")
									{
										if(obj.fl_erro == "S")
										{
											alert("ERRO "+obj.cd_erro+":\\n"+obj.retorno);
										}
										else
										{
											alert(obj.retorno);
											location.reload();
										}
									}
								},"json");
							}		
						}				
					}					
					
					jQuery(function($){
					   
					});					
				</script>	
				
				<script src="inc/mascara.js"></script>
				<link type="text/css" href="inc/janela/basic.css" rel="stylesheet" media="screen" />
				<!-- IE 6 "fixes" -->
				<!--[if lt IE 7]>
				<link type="text/css" href="inc/janela/basic_ie.css" rel="stylesheet" media="screen" />
				<![endif]-->			

				<!-- PAGAMENTO  ATRASADA -->
				<form id="pagamentoEmprestimoAtrasadaBoletoForm" action="auto_atendimento_emprestimo_anterior_boleto_atrasada.php" method="post" target="_blank">
					<input type="hidden" id="formPgAtrasadaContrato"    name="formPgAtrasadaContrato"    value="0">
					<input type="hidden" id="formPgAtrasadaDtPagamento" name="formPgAtrasadaDtPagamento" value="">
					<input type="hidden" id="formPgAtrasadaQtPagamento" name="formPgAtrasadaQtPagamento" value="0">
					<input type="hidden" id="formPgAtrasadaValor"       name="formPgAtrasadaValor"       value="0">
				</form>

				<style>
					#pagamentoEmprestimoAtrasadaTitulo {
						font-family: Calibri, Arial;
						font-size: 18pt;
						font-weight: bold;
					}
					
					#pagamentoEmprestimoAtrasadaTexto {
						font-family: Calibri, Arial;
						font-size: 12pt;
						font-weight: normal;
						text-align:justify;
					}
					
					.pagamentoEmprestimoAtrasadaTextoOBS {
						font-family: Calibri, Arial;
						font-size: 10pt;
						font-weight: normal;
						text-align:justify;
					}					
				</style>				
				<div id="pagamentoEmprestimoAtrasada" class="basic-modal-content" style="display:none;">
					<div id="pagamentoEmprestimoVencerTitulo">
						Simulação de Pagamento de Parcelas Atrasadas
					</div>
					<BR>
					<div id="pagamentoEmprestimoAtrasadaTexto">
						O VALOR TOTAL DAS PARCELAS ATRASADAS para pagamento é calculado considerando a data de pagamento informada e o quantidade de parcelas.
					</div>					
					<BR>

					<table id="pagamentoEmprestimoAtrasadaSimulador" width="500" class="sort-table" align="center" cellspacing="2" cellpadding="2">
						<thead>
							<tr>
								<td colspan="4" style="background: #4E7A4E; color:white;">
									<b>INFORME O DADOS PARA SIMULAR</b>
								</td>
							</tr>
						</thead>
						<tbody>	
							<tr>
								<td style="font-weight:bold; 180px;">
									<b>Contrato:</b>
								</td>
								<td id="pagamentoEmprestimoAtrasadaContrato1">
								</td>	
							</tr>						
							<tr>
								<td style="font-weight:bold; font-size: 120%; width: 180px;">
									<b>Quantidade de Parcelas:</b>
								</td>
								<td>
									'.$qt_pagamento_atrasada.'
								</td>	
							</tr>
							<tr>
								<td style="font-weight:bold; font-size: 120%; width: 180px;">
									<b>Data de Pagamento:</b>
								</td>
								<td>
									'.$dtPagamentoAtrasada.'
									<span style="font-size: 90%">*vencimento máximo em 10 dias</span>
								</td>	
							</tr>							
							<tr>
								<td colspan="4" align="center">
									<BR>
									<input type="button" value="Simular" onclick="pagamentoEmprestimoAtrasadaSimularExecutar();" class="botao" style="width: 120px;">
								</td>								
							</tr>								
						</tbody>								
					</table>
					<BR>
					<div id="pagamentoEmprestimoAtrasadaResultado" style="display: none;">	
						<table width="500" class="sort-table" align="center" cellspacing="2" cellpadding="2">
						<thead>
							<tr>
								<td colspan="4" style="background: #4E7A4E; color:white;">
									<b>RESULTADO</b>
								</td>
							</tr>
						</thead>						
							<tbody>	
								<tr>
									<td style="font-weight:bold; 180px;">
										<b>Contrato:</b>
									</td>
									<td id="pagamentoEmprestimoAtrasadaContrato2">
									</td>	
								</tr>
								<tr>
									<td style="font-weight:bold; width: 180px;">
										Quantidade de Parcelas:
									</td>
									<td id="pagamentoEmprestimoAtrasadaQtPagamento" style=" font-weight:bold;">
										
									</td>	
								</tr>								
								<tr>
									<td style="font-weight:bold; width: 180px;">
										Data de Pagamento:
									</td>
									<td id="pagamentoEmprestimoAtrasadaDtPagamento" style=" font-weight:bold;">
										
									</td>	
								</tr>							
								<tr>
									<td style="font-weight:bold; font-size: 130%; width: 180px;">
										Valor Total (R$):
									</td>
									<td id="pagamentoEmprestimoAtrasadaValor" style="font-weight:bold; font-size: 150%; color: blue;">
									</td>	
								</tr>
								<tr>
									<td colspan="4" align="center">
										<BR>
										<input type="button" value="Imprimir Boleto p/ Pagar" onclick="pagamentoEmprestimoAtrasadaSimularBoleto();" class="botao" style="width: 200px;">
									</td>								
								</tr>								
							</tbody>								
						</table>
					</div>
					<BR>
					<div class="pagamentoEmprestimoAtrasadaTextoOBS">
						<b>Observação:</b><BR>
						- Caso ocorra duplicidade de pagamentos a devolução será realizada até o dia 10 do mês subsequente<BR>
						- O prazo para liquidação é de até três dias úteis após o pagamento do boleto<BR>
						- Será reenviada nova cobrança para a Patrocinadora referente ao débito em aberto, nos casos em que não ocorrer o desconto em folha ou o pagamento do boleto sem encargos até o dia 10 do mês.
					</div>
					
				</div>	
				
				
				<!-- PAGAMENTO A VENCER -->
				<form id="pagamentoEmprestimoVencerBoletoForm" action="auto_atendimento_emprestimo_anterior_boleto_vencer.php" method="post" target="_blank">
					<input type="hidden" id="formPgVencerContrato"    name="formPgVencerContrato"    value="0">
					<input type="hidden" id="formPgVencerDtPagamento" name="formPgVencerDtPagamento" value="">
					<input type="hidden" id="formPgVencerQtPagamento" name="formPgVencerQtPagamento" value="0">
					<input type="hidden" id="formPgVencerValor"       name="formPgVencerValor"       value="0">
				</form>

				<style>
					#pagamentoEmprestimoVencerTitulo {
						font-family: Calibri, Arial;
						font-size: 18pt;
						font-weight: bold;
					}
					
					#pagamentoEmprestimoVencerTexto {
						font-family: Calibri, Arial;
						font-size: 12pt;
						font-weight: normal;
						text-align:justify;
					}
					
					.pagamentoEmprestimoVencerTextoOBS {
						font-family: Calibri, Arial;
						font-size: 10pt;
						font-weight: normal;
						text-align:justify;
					}					
				</style>				
				<div id="pagamentoEmprestimoVencer" class="basic-modal-content" style="display:none;">
					<div id="pagamentoEmprestimoVencerTitulo">
						Simulação de Pagamento de Parcelas
					</div>
					<BR>
					<div id="pagamentoEmprestimoVencerTexto">
						O VALOR TOTAL DAS PARCELAS para pagamento é calculado considerando a data de pagamento informada e o quantidade de parcelas.
					</div>					
					<BR>

					<table id="pagamentoEmprestimoVencerSimulador" width="500" class="sort-table" align="center" cellspacing="2" cellpadding="2">
						<thead>
							<tr>
								<td colspan="4" style="background: #4E7A4E; color:white;">
									<b>INFORME O DADOS PARA SIMULAR</b>
								</td>
							</tr>
						</thead>
						<tbody>	
							<tr>
								<td style="font-weight:bold; 180px;">
									<b>Contrato:</b>
								</td>
								<td id="pagamentoEmprestimoVencerContrato1">
								</td>	
							</tr>						
							<tr>
								<td style="font-weight:bold; font-size: 120%; width: 180px;">
									<b>Quantidade de Parcelas:</b>
								</td>
								<td>
									'.$qt_pagamento_vencer.'
								</td>	
							</tr>
							<tr>
								<td style="font-weight:bold; font-size: 120%; width: 180px;">
									<b>Data de Pagamento:</b>
								</td>
								<td>
									'.$dtPagamentoVencer.'
									<span style="font-size: 90%">*vencimento máximo em 10 dias</span>
								</td>	
							</tr>							
							<tr>
								<td colspan="4" align="center">
									<BR>
									<input type="button" value="Simular" onclick="pagamentoEmprestimoVencerSimularExecutar();" class="botao" style="width: 120px;">
								</td>								
							</tr>								
						</tbody>								
					</table>
					<BR>
					<div id="pagamentoEmprestimoVencerResultado" style="display: none;">	
						<table width="500" class="sort-table" align="center" cellspacing="2" cellpadding="2">
						<thead>
							<tr>
								<td colspan="4" style="background: #4E7A4E; color:white;">
									<b>RESULTADO</b>
								</td>
							</tr>
						</thead>						
							<tbody>	
								<tr>
									<td style="font-weight:bold; 180px;">
										<b>Contrato:</b>
									</td>
									<td id="pagamentoEmprestimoVencerContrato2">
									</td>	
								</tr>
								<tr>
									<td style="font-weight:bold; width: 180px;">
										Quantidade de Parcelas:
									</td>
									<td id="pagamentoEmprestimoVencerQtPagamento" style=" font-weight:bold;">
										
									</td>	
								</tr>								
								<tr>
									<td style="font-weight:bold; width: 180px;">
										Data de Pagamento:
									</td>
									<td id="pagamentoEmprestimoVencerDtPagamento" style=" font-weight:bold;">
										
									</td>	
								</tr>	
								<tr>
									<td style="font-weight:bold; width: 180px;">
										Valor do Abatimento (R$):
									</td>
									<td id="pagamentoEmprestimoVencerValorAbatimento" style="font-weight:bold; color: green;">
									</td>	
								</tr>								
								<tr>
									<td style="font-weight:bold; font-size: 130%; width: 180px;">
										Valor Total (R$):
									</td>
									<td id="pagamentoEmprestimoVencerValor" style="font-weight:bold; font-size: 150%; color: blue;">
									</td>	
								</tr>
								<tr>
									<td colspan="4" align="center">
										<BR>
										<input type="button" value="Imprimir Boleto p/ Pagar" onclick="pagamentoEmprestimoVencerSimularBoleto();" class="botao" style="width: 200px;">
										<input type="button" value="Nova Simulação" onclick="pagamentoEmprestimoVencerSimular();" class="botao_disabled" style="width: 200px;">
									</td>								
								</tr>								
							</tbody>								
						</table>
					</div>
					<BR>
					<div class="pagamentoEmprestimoVencerTextoOBS">
						<b>Observação:</b><BR>
						- Caso ocorra duplicidade de pagamentos a devolução será realizada até o dia 10 do mês subsequente<BR>
						- O prazo para liquidação é de até três dias úteis após o pagamento do boleto<BR>
						- Será reenviada nova cobrança para a Patrocinadora referente ao débito em aberto, nos casos em que não ocorrer o desconto em folha ou o pagamento do boleto sem encargos até o dia 10 do mês.
					</div>
					
				</div>				
				
				
				
				<!-- PAGAMENTO TOTAL -->
				<form id="pagamentoEmprestimoTotalBoletoForm" action="auto_atendimento_emprestimo_anterior_boleto_total.php" method="post" target="_blank">
					<input type="hidden" id="formPgTotalContrato"    name="formPgTotalContrato"    value="0">
					<input type="hidden" id="formPgTotalDtPagamento" name="formPgTotalDtPagamento" value="">
					<input type="hidden" id="formPgTotalValor"       name="formPgTotalValor"       value="0">
				</form>
			
				<style>
					#pagamentoEmprestimoTotalTitulo {
						font-family: Calibri, Arial;
						font-size: 18pt;
						font-weight: bold;
					}
					
					#pagamentoEmprestimoTotalTexto {
						font-family: Calibri, Arial;
						font-size: 12pt;
						font-weight: normal;
						text-align:justify;
					}
					
					.pagamentoEmprestimoTotalTextoOBS {
						font-family: Calibri, Arial;
						font-size: 10pt;
						font-weight: normal;
						text-align:justify;
					}					
				</style>
				<div id="pagamentoEmprestimoTotal" class="basic-modal-content" style="display:none;">
					<div id="pagamentoEmprestimoTotalTitulo">
						Simulação do Pagamento Total
					</div>
					<BR>
					<div id="pagamentoEmprestimoTotalTexto">
						O VALOR TOTAL para pagamento é calculado considerando a data de pagamento informada.
					</div>					
					<BR>

					<table id="pagamentoEmprestimoTotalSimulador" width="500" class="sort-table" align="center" cellspacing="2" cellpadding="2">
						<thead>
							<tr>
								<td colspan="4" style="background: #4E7A4E; color:white;">
									<b>INFORME O DADOS PARA SIMULAR</b>
								</td>
							</tr>
						</thead>
						<tbody>	
							<tr>
								<td style="font-weight:bold; 180px;">
									<b>Contrato:</b>
								</td>
								<td id="pagamentoEmprestimoTotalContrato1">
								</td>	
							</tr>						
							<tr>
								<td style="font-weight:bold; font-size: 120%; width: 180px;">
									<b>Data de Pagamento:</b>
								</td>
								<td>
									'.$dtPagamentoTotal.'
									<span style="font-size: 90%">*vencimento máximo em 10 dias</span>
								</td>	
							</tr>
							<tr>
								<td colspan="4" align="center">
									<BR>
									<input type="button" value="Simular" onclick="pagamentoEmprestimoTotalSimularExecutar();" class="botao" style="width: 120px;">
								</td>								
							</tr>								
						</tbody>								
					</table>
					<BR>
					<div id="pagamentoEmprestimoTotalResultado" style="display: none;">	
						<table width="500" class="sort-table" align="center" cellspacing="2" cellpadding="2">
						<thead>
							<tr>
								<td colspan="4" style="background: #4E7A4E; color:white;">
									<b>RESULTADO</b>
								</td>
							</tr>
						</thead>						
							<tbody>	
								<tr>
									<td style="font-weight:bold; 180px;">
										<b>Contrato:</b>
									</td>
									<td id="pagamentoEmprestimoTotalContrato2">
									</td>	
								</tr>							
								<tr>
									<td style="font-weight:bold; width: 180px;">
										Data de Pagamento:
									</td>
									<td id="pagamentoEmprestimoTotalDtPagamento" style=" font-weight:bold;">
										
									</td>	
								</tr>	
								<tr>
									<td style="font-weight:bold; width: 180px;">
										Valor do Abatimento (R$):
									</td>
									<td id="pagamentoEmprestimoTotalValorAbatimento" style="font-weight:bold; color: green;">
									</td>	
								</tr>								
								<tr>
									<td style="font-weight:bold; font-size: 130%; width: 180px;">
										Valor Total (R$):
									</td>
									<td id="pagamentoEmprestimoTotalValor" style="font-weight:bold; font-size: 150%; color: blue;">
									</td>	
								</tr>
								<tr>
									<td colspan="4" align="center">
										<BR>
										<input type="button" value="Imprimir Boleto p/ Pagar" onclick="pagamentoEmprestimoTotalSimularBoleto();" class="botao" style="width: 200px;">
										<input type="button" value="Nova Simulação" onclick="pagamentoEmprestimoTotalSimular();" class="botao_disabled" style="width: 200px;">
									</td>								
								</tr>								
							</tbody>								
						</table>
					</div>
					<BR>
					<div class="pagamentoEmprestimoTotalTextoOBS">
						<b>Observação:</b><BR>
						- Caso ocorra duplicidade de pagamentos a devolução será realizada até o dia 10 do mês subsequente<BR>
						- O prazo para liquidação é de até três dias úteis após o pagamento do boleto<BR>
						- Será reenviada nova cobrança para a Patrocinadora referente ao débito em aberto, nos casos em que não ocorrer o desconto em folha ou o pagamento do boleto sem encargos até o dia 10 do mês.
					</div>
					
				</div>		




				<!-- POSTERGACAO DE PARCELAS -->
				<form id="postergarEmprestimoForm" action="auto_atendimento_emprestimo_anterior_postergar.php" method="post" target="_blank">
					<input type="hidden" id="formPostergarContrato"  name="formPostergarContrato" value="0">
				</form>
			
				<style>
					#postergarEmprestimoTitulo {
						font-family: Calibri, Arial;
						font-size: 18pt;
						font-weight: bold;
					}
					
					#postergarEmprestimoTexto {
						font-family: Calibri, Arial;
						font-size: 12pt;
						font-weight: normal;
						text-align:justify;
					}
					
					.postergarEmprestimoTextoOBS {
						font-family: Calibri, Arial;
						font-size: 10pt;
						font-weight: normal;
						text-align:justify;
					}					
				</style>
				<div id="postergarEmprestimo" class="basic-modal-content" style="display:none;">
					<div id="postergarEmprestimoTitulo">
						Simulação de Postergação
					</div>

					<BR>
					<div id="postergarEmprestimoResultado">	
						<table width="800" class="sort-table" align="center" cellspacing="2" cellpadding="2">
						<thead>
							<tr>
								<td colspan="4" style="background: #4E7A4E; color:white;">
									<b>INFORMAÇÕES</b>
								</td>
							</tr>
						</thead>						
							<tbody>	
								<tr>
									<td style="font-weight:bold; 180px;">
										<b>Contrato:</b>
									</td>
									<td id="postergarEmprestimoContrato">
									</td>	
								</tr>
								<tr>
									<td style="font-weight:bold; 180px;">
										Nova data da última parcela:
									</td>
									<td id="postergarEmprestimoData">
									</td>	
								</tr>								
								<tr>
									<td style="font-weight:bold; font-size: 130%; width: 200px;">
										Novo Valor da Parcela (R$):
									</td>
									<td id="postergarEmprestimoValor" style="font-weight:bold; font-size: 150%; color: blue;">
									</td>	
								</tr>
								<tr >
									<td colspan=2>&nbsp;</td>
								</tr>			
								
								<tr>
									<td colspan=2><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Desenhe sua assinatura (*):</font></td>
								</tr>

								<tr>
									<td colspan=2>
								
										<style>
											.sigPad {
											  margin: 0;
											  padding: 0;
											  width: 720px;
											}

											.sigWrapper {
											  clear: both;
											  width: 720px;
											  height: 200px;

											  border: 1px solid #ccc;
											}

											.signed .sigWrapper {
											  border: 0;
											}

											.pad {
											  position: relative;
											}

											.current .pad {
											  /**
											   * For cross browser compatibility, this should be an absolute URL
											   * In IE the cursor is relative to the HTML document
											   * In all other browsers the cursor is relative to the CSS file
											   *
											   * http://www.useragentman.com/blog/2011/12/21/cross-browser-css-cursor-images-in-depth/
											   */
											  cursor: url("inc/signature-pad/assets/pen.cur"), crosshair;
											  /**
											   * IE will ignore this line because of the hotspot position
											   * Unfortunately we need this twice, because some browsers ignore the hotspot inside the .cur
											   */
											  cursor: url("inc/signature-pad/assets/pen.cur") 16 16, crosshair;

											  -ms-touch-action: none;
											  -webkit-user-select: none;
											  -moz-user-select: none;
											  -ms-user-select: none;
											  -o-user-select: none;
											  user-select: none;
											}
											
											p.error_assinar {
											  display: block;
											  margin: 0.5em 0;
											  padding: 0.4em;

											  background-color: #f33;

											  color: #fff;
											  font-weight: bold;
											  font-family: Verdana, Arial, Helvetica, sans-serif;
											}	

											p.assinar_error {
												margin-top: 2px;
												margin-bottom: 2px;
												width: 705px;
												vertical-align: top;
												background-color: #FF8F49;
												background-image: -moz-linear-gradient(center top , #FFB587, #FF954F);
												border: 1px solid #FF6A00;
												color: #FFFFFF !important;
												-moz-user-select: none;
												border-radius: 2px 2px 2px 2px;
												cursor: pointer;
												display: inline-block;
												
												height: 29px;
												line-height: 29px;
												min-width: 54px;
												padding: 0 8px;
												text-align: center;
												text-decoration: none !important;
												font-family: Verdana, Arial, Helvetica, sans-serif;
												font-weight: bold;
												font-size: 10pt;
											}						
										</style>

										<!--[if lt IE 9]><script src="inc/signature-pad/assets/flashcanvas.js"></script><![endif]-->


										<div method="post" action="" class="sigPad">
											<div class="sig sigWrapper">
											  <div class="typed"></div>
											  <canvas class="pad" width="720" height="200" id="assinatura"></canvas>
											  <input type="hidden" name="assinatura-output" class="output" id="assinatura-output">
											  <input type="hidden" name="assinatura-base64" id="assinatura-base64">
											</div>
											<span class="clearButton"><a href="#clear" class="button button-round">Limpar</a></span>
										</div>

										  
										<script src="inc/signature-pad/jquery.signaturepad.js"></script>
										<script>

											$(document).ready(function() {
	
												$(".sigPad").signaturePad({
													variableStrokeWidth:true,
													drawOnly: true,
													errorClass: "assinar_error",
													errorMessageDraw : "É necessário que você desenhe sua assinatura",
													lineWidth: 0	
												});	  

											});
										</script>	
										<script src="inc/signature-pad/assets/json2.min.js"></script>
									
									</td>
								</tr>

								<tr>
									<td colspan="4" align="center">
										<BR>
										<input type="button" value="Postergar Agora" onclick="postergarEmprestimoConcede();" class="botao" style="width: 200px;">
									</td>								
								</tr>								
							</tbody>								
						</table>
						<h3>
							Esta operação será irreversível e a parcela do próximo mês irá para o final do contrato, alterando o valor das próximas parcelas.<br/><br/>

							"O objetivo desta regra é possibilitar ao participante a carência no pagamento da parcela do mês seguinte, postergando-a para o final do contrato e recalculando o valor pré-fixado das próximas parcelas e saldo devedor".<br/>

							"A postergação permitirá que até duas (02) parcelas do contrato, no período de 12 parcelas, com validade para a competência do período subsequente à solicitação, possam ser realocadas para o período final do contrato, ou seja, será postergada a parcela do mês seguinte".<br/>

							"Poderá o participante postergar mais de uma parcela. Porém, não será possível a postergação de duas parcelas em uma única solicitação".<br/>
						</h3>
					</div>
				</div>
				
                  ';
				  
	
	#### BUSCA EMPRESTIMO ANTERIOR ####
	$qr_sql = "
				SELECT * 
				  FROM oracle.emprestimo_andamento_parcelas(".$_SESSION['EMP'].",".$_SESSION['RE'].",".$_SESSION['SEQ'].")
	          ";

	$ob_resul = pg_query($db,$qr_sql);
	$nr_total_reg_parcelas = pg_num_rows($ob_resul);
	while ($ar_reg = pg_fetch_array($ob_resul)) 
	{
		$tr_parcela = $tr_modelo;
		$tr_parcela = str_replace("{NUM_PARCELA}",$ar_reg['nr_parcela'],$tr_parcela);
		$tr_parcela = str_replace("{MES}",$ar_reg['mes'],$tr_parcela);
		$tr_parcela = str_replace("{ANO}",$ar_reg['ano'],$tr_parcela);
		$tr_parcela = str_replace("{SITUACAO}",$ar_reg['situacao'],$tr_parcela);
		$tr_parcela = str_replace("{SITUACAO_COR}",$ar_reg['situacao_cor'],$tr_parcela);
		$tr_parcela = str_replace("{VALOR_PARCIAL}",number_format($ar_reg['vlr_pago_parcial'],2,",","."),$tr_parcela);
		$tr_parcela = str_replace("{VALOR}",number_format($ar_reg['vlr_prest_fech'],2,",","."),$tr_parcela);
		$tr_linhas.= $tr_parcela;
	}

	#DEMONSTRATIVO DO EMPRESTIMO
	$qr_contrato = "
					SELECT cd_contrato,
					       forma_calculo
				      FROM oracle.emprestimo_andamento_contrato(".$_SESSION['EMP'].",".$_SESSION['RE'].",".$_SESSION['SEQ'].")
				   ";	
	$ob_dados = pg_query($db,$qr_contrato);
	$ar_reg = pg_fetch_array($ob_dados);
	$CONTRATO = $ar_reg['cd_contrato'];
	$demonstrativo = str_replace("{CONTRATO}",$CONTRATO,$demonstrativo);
	if(intval($CONTRATO) > 0)
	{
		if($ar_reg['forma_calculo'] == "O") #POS-FIXADO
		{
			$tabela = $tabela_pos;
		}
		else #PREFIXADO
		{
			$tabela = $tabela_pre;
		}	
	
		if($nr_total_reg_parcelas > 0)
		{
			$conteudo = str_replace("{tabela}",($demonstrativo.$tabela.$tr_linhas.$tabela_fim),$conteudo);
		}
		else
		{
			$conteudo = str_replace("{tabela}",$demonstrativo,$conteudo);
		}
	}
	else
	{
		$sem_emp = "
						".$tb_irpf."
						<br><br><br>
						<center>
							<h1 style='font-family: Calibri, Arial; font-size: 18pt;'>
								EM MANUTENÇÃO
								<!--Não há empréstimo em andamento.<BR><BR>Você pode simular empréstimo.-->
							</h1>
						</center>
						<br><br><br>
					";				   
		$conteudo = str_replace("{tabela}",$sem_emp,$conteudo);
	}
	
	$tpl->assign('conteudo',$conteudo);
	$tpl->printToScreen();
?>