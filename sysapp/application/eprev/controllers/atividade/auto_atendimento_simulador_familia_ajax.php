<?php
	include_once('inc/conexao.php');
	
	if($_POST)
	{
		if($_POST['ds_funcao'] == "calculaBeneficio")
		{
			calculaBeneficio(
			$_POST['cd_empresa'], $_POST['cd_registro_empregado'], $_POST['seq_dependencia'], $_POST['dt_ingresso'],
			$_POST['dt_simulacao'],$_POST['dt_nascimento'],$_POST['nr_idade_apos'],$_POST['vl_contribuicao'],$_POST['vl_rentabilidade'],
			$_POST['nr_prazo'], $_POST['fl_contribuicao_empregador'], $_POST['vl_contribuicao_empregador'], $_POST['qt_contribuicao_empregador'],$_POST['vl_aporte'], $_POST['fl_adiantamento'], $_POST['vl_adiantamento']
			);
		}
		
		if($_POST['ds_funcao'] == "buscaMesesApos")
		{
			buscaMesesApos($_POST['dt_nascimento'],$_POST['dt_simulacao'],$_POST['dt_ingresso'],$_POST['nr_idade_apos']);
		}		
	}

	function buscaMesesApos($dt_nascimento, $dt_simulacao, $dt_ingresso, $nr_idade_apos)
	{
		$dt_aposentadoria  = calculaDataAposentadoria($dt_nascimento, $dt_ingresso, $nr_idade_apos);
		
		echo intval(calculaMeses($dt_simulacao, $dt_aposentadoria));
	}
	
	function calculaMeses($dt_ini, $dt_fim)
	{
		global $db;
		$qr_sql = "
					SELECT (EXTRACT('years' FROM TO_DATE('".$dt_fim."','DD/MM/YYYY')) - EXTRACT('years' FROM TO_DATE('".$dt_ini."','DD/MM/YYYY'))) * 12 + (EXTRACT('months' FROM TO_DATE('".$dt_fim."','DD/MM/YYYY')) - EXTRACT('months' FROM TO_DATE('".$dt_ini."','DD/MM/YYYY'))) AS qt_meses
	              ";
		$ob_resul = pg_query($db, $qr_sql);
		$ar_reg   = pg_fetch_array($ob_resul);
		return $ar_reg['qt_meses'];	
	}
	
	function calculaDataAposentadoria($dt_nascimento,$dt_ingresso_plano,$nr_idade_apos)
	{
		global $db;
		
		$qr_sql = "
					SELECT TO_CHAR(dt_aposentadoria,'DD/MM/YYYY') AS dt_aposentadoria,
                           dt_referencia					
					  FROM simulador.familia_calcula_datas(TO_DATE('".$dt_nascimento."','DD/MM/YYYY'), TO_DATE('".$dt_ingresso_plano."','DD/MM/YYYY'), ".$nr_idade_apos.")
		          ";
		/*
		$qr_sql = "
					SELECT TO_CHAR(CASE WHEN ((CASE WHEN (TO_DATE('".$dt_nascimento."','DD/MM/YYYY')+ '50 years'::interval) > (TO_DATE('".$dt_ingresso_plano."','DD/MM/YYYY') + '5 year'::interval)
											THEN (TO_DATE('".$dt_nascimento."','DD/MM/YYYY') + '50 years'::interval)
											ELSE (TO_DATE('".$dt_ingresso_plano."','DD/MM/YYYY') + '5 years'::interval) 	
										END)::DATE) < ((TO_DATE('".$dt_nascimento."','DD/MM/YYYY') + '".$nr_idade_apos." years'::interval)::DATE) 					
							   THEN (TO_DATE('".$dt_nascimento."','DD/MM/YYYY') + '".$nr_idade_apos." years'::interval)::DATE
							   ELSE ((CASE WHEN (TO_DATE('".$dt_nascimento."','DD/MM/YYYY')+ '50 years'::interval) > (TO_DATE('".$dt_ingresso_plano."','DD/MM/YYYY') + '5 year'::interval)
											 THEN (TO_DATE('".$dt_nascimento."','DD/MM/YYYY')+ '50 years'::interval) 					               
											 ELSE (TO_DATE('".$dt_ingresso_plano."','DD/MM/YYYY') + '5 years'::interval) 					    
										  END)::DATE) 						
								END,'DD/MM/YYYY') AS dt_aposentadoria 		
		          ";
		*/
		$ob_resul = pg_query($db, $qr_sql);
		$ar_data  = pg_fetch_array($ob_resul);	
		return $ar_data['dt_aposentadoria'];
	}	
		
	function calculaBeneficio($cd_empresa, $cd_registro_empregado, $seq_dependencia, $dt_ingresso, $dt_simulacao,$dt_nascimento,$nr_idade_apos,$vl_contribuicao,$vl_rentabilidade, $nr_prazo, $fl_contribuicao_empregador, $vl_contribuicao_empregador, $qt_contribuicao_empregador,$vl_aporte_inicial, $fl_adiantamento, $vl_adiantamento)
	{
		$debug = FALSE;

		$ar_ip = array(
			'10.63.4.150',
			'10.63.4.102',
			'10.63.4.87'
		);
		
		if(in_array($_SERVER['REMOTE_ADDR'], $ar_ip))
		{
			$debug = TRUE;
		}			
		
		echo ($debug ?  "<BR>CONTRIBUICAO => ".$vl_contribuicao : "");
		echo ($debug ?  "<BR>IDADE APOS => ".$nr_idade_apos : "");
		echo ($debug ?  "<BR>RENTABILIDADE => ".$vl_rentabilidade : "");
		echo ($debug ?  "<BR>PRAZO => ".$nr_prazo : "");

		$qt_meses       = calculaMeses($dt_nascimento, $dt_simulacao);
		echo ($debug ?  "<BR>QT MESES IDADE => ".$qt_meses : "");
		$nr_idade_atual = (int) ($qt_meses / 12);		
		$nr_idade_atual = ($qt_meses / 12);		
		
		echo ($debug ?  "<BR>IDADE ATUAL => ".$nr_idade_atual : "");
		
		$dt_aposentadoria = calculaDataAposentadoria($dt_nascimento,$dt_ingresso,$nr_idade_apos);
		
		echo ($debug ?  "<BR>DT APOS => ".$dt_aposentadoria : "");
		
		$qt_meses_apos = calculaMeses($dt_simulacao, $dt_aposentadoria);
		
		if(intval($qt_meses_apos) < 0)
		{
			$qt_meses_apos = 0;
		}

		echo ($debug ?  "<BR>QT MESES APOS => ".$qt_meses_apos : "");

		
		#### CIP ####
		$vl_cip = calculaCIP($dt_nascimento, $dt_simulacao,$qt_meses_apos,$vl_contribuicao,$vl_rentabilidade);
		
		$fl_BPD = checkBPD($cd_empresa, $cd_registro_empregado, $seq_dependencia);
		echo ($debug ?  "<br>É BPD => ".$fl_BPD : "");
		
		if($fl_BPD == "S")
		{
			#### BPD NÃO TEM PROJECAO ####
			$vl_cip = 0;
		}	

		echo ($debug ?  "<BR>CIP PROJETADA => ".$vl_cip : "");
		
		#### BUSCA CIP ATUAL ####
		$qt_cota = calculaCIPAtual($cd_empresa, $cd_registro_empregado, $seq_dependencia, $dt_ingresso);
		$vl_cota = getValorCota();
		$vl_cip_atual = $qt_cota * getValorCota();
		$vl_saldo_atual = $vl_cip_atual;
		echo ($debug ?  "<BR>QT COTAS => ".$qt_cota : "");
		echo ($debug ?  "<BR>VL COTAS => ".$vl_cota : "");
		echo ($debug ?  "<BR>CIP ATUAL => ".$vl_cip_atual : "");		
				
		$vl_cip_atual = atualizaCIPAtual($qt_meses_apos,$vl_cip_atual,$vl_rentabilidade);
		echo ($debug ?  "<BR>CIP ATUAL CORRIGIDA => ".$vl_cip_atual : "");
		
		echo ($debug ?  "<BR>CONTRIB EMPREGADOR => ".$fl_contribuicao_empregador : "");
		#### CONTRIBUICAO EMPREGADOR ####
		$vl_saldo_empregador = 0;
		if($fl_contribuicao_empregador == "S")
		{
			echo ($debug ?  "<BR>CONTRIBUICAO EMPREGADOR => ".$vl_contribuicao_empregador : "");
			$vl_saldo_empregador = calculaContribuicaoEmpregador($vl_contribuicao_empregador, $qt_contribuicao_empregador, $vl_rentabilidade);
			echo ($debug ?  "<BR>SALDO EMPREGADOR => ".$vl_saldo_empregador : "");
			
			if($qt_contribuicao_empregador < $qt_meses_apos)
			{
				$vl_saldo_empregador = atualizaContribuicaoEmpregador(($qt_meses_apos - $qt_contribuicao_empregador),$vl_saldo_empregador,$vl_rentabilidade);
				echo ($debug ?  "<BR>SALDO EMPREGADOR ATUAL => ".$vl_saldo_empregador : "");
			}
		}	

		echo ($debug ? "<BR>VL APORTE => ".$vl_aporte_inicial : "");
		$vl_cip_aporte = 0;
		if(floatval($vl_aporte_inicial) > 0)
		{
			$vl_cip_aporte = atualizaCIPAtual($qt_meses_apos,$vl_aporte_inicial,$vl_rentabilidade);
		}	
		
		#### SOMAS AS CIP's #####
		$vl_cip = ($vl_cip + $vl_cip_atual + $vl_saldo_empregador + $vl_cip_aporte);	
		echo ($debug ?  "<BR>CIP TOTAL => ".$vl_cip : "");

		#### ADIANTAMENTO DE 20% ####
		echo ($debug ?  "<BR>ADIANTAMENTO => ".$fl_adiantamento : "");
		if(trim(strtoupper($fl_adiantamento)) == "S")
		{
			echo ($debug ?  "<BR>PERCENTUAL DE ADIANTAMENTO => ".$vl_adiantamento : "");

			$vl_adiantamento_calc = ($vl_cip/100) * $vl_adiantamento;
			$vl_cip = $vl_cip - $vl_adiantamento_calc;
			
			echo ($debug ?  "<BR>ADIANTAMENTO ".$vl_adiantamento."% => ".$vl_adiantamento_calc : "");
			echo ($debug ?  "<BR>CIP TOTAL (-) ADIANTAMENTO => ".$vl_cip : "");
		}	
		
		$vl_cip_acumulada = round(($vl_cip),2);
		$vl_contribuicao_inicial = round((($vl_cip / ($nr_prazo * 13))),2);		
		
		#### VERIFICA PRAZO ####
		$nr_prazo_maximo = calculaPrazo($vl_cip);
		echo ($debug ?  "<BR>PRAZO MAXIMO => ".$nr_prazo_maximo : "");	

		if (($nr_prazo_maximo == 0) or ($nr_prazo > $nr_prazo_maximo))
		{
			echo "
			<div class='simulador_erro'>
						<table  align='center' border='0'>";

		if(trim(strtoupper($fl_adiantamento)) == "S")
		{
			echo "
					<td align='center'>
						<div class='saldo_acum'>
							<div class='saldo_acum_titulo'>Adiantamento<BR>".$vl_adiantamento."%</div>
							<div class='saldo_acum_valor'><span style='font-size: 60%'>R$</span> ".number_format(round(($vl_adiantamento_calc),2),2,',','.')."</div>
						</div>			
					</td>
			";
		}

			echo "
					<div class='simulador_erro'>
						<table  align='center' border='0'>
							<tr>
								<td>
									<div class='saldo_acum'>
										<div class='saldo_acum_titulo'>Saldo Acumulado</div>
										<div class='saldo_acum_valor'>R$ ".number_format($vl_cip_acumulada,2,',','.')."</div>
									</div>					
								</td>
							</tr>
						</table>
						<BR>
						Este valor é insuficiente para gerar benefícios de aposentadoria no prazo estipulado na simulação. 
						<BR>
						Seu benefício ficou inferior a uma Unidade Referencial. 
						<BR>
						Altere os parâmetros da simulação para que seu benefício fique igual ou superior ao valor da Unidade Referencial de R$ <b>".number_format(getURFAMILIA(),2,',','.')."</b>.
					</div>
					<BR>
					<BR>
				 ";
			exit;
		}

		echo "
			<BR>
			<table border='0' width='100%' align='center'> 
				";

		if(trim(strtoupper($fl_adiantamento)) == "S")
		{
			echo "<tr>
					<td align='center' colspan='3'>
						<div class='saldo_acum'>
							<div class='saldo_acum_titulo'>Adiantamento<BR>".$vl_adiantamento."%</div>
							<div class='saldo_acum_valor'><span style='font-size: 60%'>R$</span> ".number_format(round(($vl_adiantamento_calc),2),2,',','.')."</div>
						</div>			
					</td>
					</tr>
			";
		}

		echo "
				<tr>
					<td align='center' colspan='3'>
						<div class='saldo_acum'>
							<div class='saldo_acum_titulo'>Contribuição</div>
							<div class='saldo_acum_valor'>R$ ".number_format($vl_contribuicao,2,',','.')."</div>
						</div>			
					</td>	
				</tr>
				<tr>
					<td align='center'>
						<div class='saldo_acum'>
							<div class='saldo_acum_titulo'>Saldo Acum Atual</div>
							<div class='saldo_acum_valor'>R$ ".number_format($vl_saldo_atual,2,',','.')."</div>
						</div>			
					</td>					
					<td align='center'>
						<div class='saldo_acum'>
							<div class='saldo_acum_titulo'>Saldo Acum Projetado</div>
							<div class='saldo_acum_valor'>R$ ".number_format($vl_cip_acumulada,2,',','.')."</div>
						</div>			
					</td>
				</tr>
				<tr>
					<td align='center' colspan='3'>
						<div class='ben_inicial'>
							<div class='ben_inicial_titulo'>Benefício Inicial</div>
							<div class='ben_inicial_valor'>R$ ".number_format($vl_contribuicao_inicial,2,',','.')."</div>
						</div>
					</td>
				</tr>
				
				
				
				<tr>
					<td colspan='3'>
					<BR>
					<fieldset style='width:98%; border: 0px;'>
						<legend>
							Evolução <a id='obEvolucaoIcone' herf='#' onclick='exibeEvolucao();' title='Exibir a evolução' style='cursor:pointer; text-decoration: none;'>[+]</a>
						</legend>	
						<table border='0' width='98%' align='center' cellspacing='1' cellpadding='1' id='obEvolucao' style='display:none;'>  
							<tr>
								<td valign='top'>".evoluiBeneficio($vl_cip_acumulada,$vl_contribuicao_inicial,$vl_rentabilidade,$nr_prazo)."</td>
							</tr>
							<tr>
								<td	colspan='3'>
									* Saldo no ínicio do ano.
								</td>
							</tr>
						</table>
					</fieldset>
					<BR>					
					</td>
				</tr>				
			</table>
			<BR>
		     ";	
	}
		
	function calculaCIP($dt_nascimento, $dt_simulacao,$qt_mes_apos,$vl_contribuicao,$vl_rentabilidade)
	{

		$nr_parametro_1 = $qt_mes_apos;	
		$nr_parametro_2 = (pow((1.000000000 + $vl_rentabilidade), $nr_parametro_1)) - 1;
		$nr_parametro_3 = $nr_parametro_2 / $vl_rentabilidade;

		$vl_cip = $nr_parametro_3 * $vl_contribuicao;

		return $vl_cip;
	}	
	
	function calculaCIPAtual($cd_empresa, $cd_registro_empregado, $seq_dependencia, $dt_ingresso)
	{	
		global $db;
		
		/*
		$qr_sql = "
					SELECT (COALESCE(qt_cip,0) + COALESCE(qt_port,0)) AS qt_cota
					  FROM (SELECT fnc_saldo_cd_contas::NUMERIC AS qt_cip 
					          FROM oracle.fnc_saldo_cd_contas(9, ".$cd_empresa.", ".$cd_registro_empregado.", ".$seq_dependencia.", '".$dt_ingresso."', 11)) a,
					       (SELECT fnc_saldo_cd_contas::NUMERIC AS qt_port 
					          FROM oracle.fnc_saldo_cd_contas(9, ".$cd_empresa.", ".$cd_registro_empregado.", ".$seq_dependencia.", '".$dt_ingresso."', 1401)) c					
	              ";
		*/		  
				  
		$qr_sql = "
					SELECT familia_cip_quota AS qt_cota
					  FROM simulador.familia_cip_quota(".$cd_empresa.", ".$cd_registro_empregado.", ".$seq_dependencia.", TO_DATE('".$dt_ingresso."','DD/MM/YYYY'));
		          ";						  
		$ob_resul = pg_query($db, $qr_sql);
		$ar_reg   = pg_fetch_array($ob_resul);
		return $ar_reg['qt_cota'];				
	}
	
	function atualizaCIPAtual($qt_meses,$vl_cip,$vl_rentabilidade)
	{
		$vl_cip = (pow((1.000000000 + $vl_rentabilidade), $qt_meses)) * $vl_cip;
		return $vl_cip;
	}

	function calculaContribuicaoEmpregador($vl_contribuicao,$qt_meses, $vl_rentabilidade)
	{
		$vl_cip = (pow((1.000000000 + $vl_rentabilidade), $qt_meses)) - 1;
		$vl_cip = $vl_cip / $vl_rentabilidade;
		$vl_cip = $vl_cip * $vl_contribuicao;
		
		return $vl_cip;
	}	
	
	function atualizaContribuicaoEmpregador($qt_meses,$vl_cip,$vl_rentabilidade)
	{
		$vl_cip = (pow((1.000000000 + $vl_rentabilidade), $qt_meses)) * $vl_cip;
		return $vl_cip;
	}	
	
	function calculaPrazo($vl_cip)
	{
		$nr_prazo = (int) (($vl_cip  / getURFAMILIA()) / 13); 
		if($nr_prazo < 5)
		{
			$nr_prazo = 0;
		}

		return $nr_prazo;
	}
	
	function evoluiBeneficio($vl_saldo,$vl_renda,$vl_rentabilidade,$nr_prazo)
	{
		$vl_saldo_atual = $vl_saldo;
		$vl_renda_atual = $vl_renda;
		$nr_prazo_atual = $nr_prazo;
	
		$ds_retorno = '
						<BR>
						<div style="padding:5px;">
						<table class="sort-table" id="table-1" align="center" width="90%" cellspacing="2" cellpadding="2">
							<thead>
							<tr height="25">
								<th>
									Ano
								</th>
								<th>
									Saldo*
								</th>	
								<th>
									Benefício
								</th>
							</tr>
							</thead>
							<tbody>	
							<tr onmouseover="sortSetClassOver(this);" onmouseout="sortSetClassOut(this);"> 
								<td align="center" style="padding:5px;">
									1º
								</td>
								<td align="right" style="padding:5px;">
									'.number_format(round($vl_saldo_atual,2),2,',','.').'
								</td>	
								<td align="right" style="padding:5px;">
									'.number_format(round($vl_renda_atual,2),2,',','.').'
								</td>
							</tr>							
		              ';
		
		$nr_conta = 1;
		while($nr_conta < $nr_prazo)
		{
			if(($nr_conta % 2) != 0)
			{
				$bg_color = 'sort-par';
			}
			else
			{
				$bg_color = 'sort-impar';		
			}			
			
			$vl_A = $vl_saldo_atual * (pow((1 + $vl_rentabilidade),12));
			$vl_B = ($vl_renda_atual * (13/12)) * (((pow((1 + $vl_rentabilidade),12)) - 1) / $vl_rentabilidade);

			
			$vl_saldo_atual = $vl_A - $vl_B;	
			$vl_renda_atual = $vl_saldo_atual / (($nr_prazo_atual - 1) * 13);
			$ds_retorno.= '
							<tr onmouseover="sortSetClassOver(this);" onmouseout="sortSetClassOut(this);" class="'.$bg_color.'"> 
								<td align="center" style="padding:5px;">
									'.($nr_conta + 1).'º
								</td>
								<td align="right" style="padding:5px;">
									'.number_format(round($vl_saldo_atual,2),2,',','.').'
								</td>
								<td align="right" style="padding:5px;">
									'.number_format(round($vl_renda_atual,2),2,',','.').'
								</td>
							</tr>							
			              ';
			
			$nr_prazo_atual--;			
			$nr_conta++;
		}
		
		$ds_retorno.= "
						</tbody>	
						</table>
						</div>
		              ";
		
		return $ds_retorno;
	}	
	
	function getValorCota()
	{
		global $db;
		$qr_sql = "
					SELECT vlr_indice 
					  FROM public.indices  
					 WHERE cd_indexador = 119
					 ORDER BY dt_indice DESC
					 LIMIT 1						
	              ";
		$ob_resul = pg_query($db, $qr_sql);
		$ar_reg   = pg_fetch_array($ob_resul);
		return $ar_reg['vlr_indice'];		
	}	
	
	function getURFAMILIA()
	{
		global $db;
		$qr_sql = "
					SELECT vlr_taxa
					  FROM public.taxas
					 WHERE cd_indexador = 81
					 ORDER BY dt_taxa DESC
					 LIMIT 1						
	              ";
		$ob_resul = pg_query($db, $qr_sql);
		$ar_reg   = pg_fetch_array($ob_resul);
		return $ar_reg['vlr_taxa'];		
	}

	function checkBPD($cd_empresa, $cd_registro_empregado, $seq_dependencia)
	{
		global $db;
		
		$qr_sql = "
			        SELECT CASE WHEN COUNT(*) > 0 
					            THEN 'S'
								ELSE 'N'
					       END AS fl_bpd
					  FROM public.afastados a
					 WHERE a.cd_empresa            = ".$cd_empresa."
					   AND a.cd_registro_empregado = ".$cd_registro_empregado."
					   AND a.seq_dependencia       = ".$seq_dependencia."
					   AND a.tipo_afastamento      = 72 -- BPD
					   AND ((a.dt_final_afastamento IS NULL) OR (DATE_TRUNC('month', a.dt_final_afastamento) >= DATE_TRUNC('month',CURRENT_DATE)))				 
	              ";
		$ob_resul = pg_query($db, $qr_sql);
		$ar_reg   = pg_fetch_array($ob_resul);
		return $ar_reg['fl_bpd'];		
	}	
?>