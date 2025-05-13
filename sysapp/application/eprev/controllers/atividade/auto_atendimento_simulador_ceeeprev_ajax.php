<?php
	include_once('inc/conexao.php');

	$debug = FALSE;
	
	$user_md5 = array(
		'13438bd440697201e0e9bc926aedb0fe',
		'f1445d8e07632a69fe2f3812c1eb59a8',
		'b989ecdb55ea98e209c5057e01d7a181',
		'ffd238d130081ddcb0be22a825457487'
	);

	if(in_array($_POST['user_md5'], $user_md5)) #### CRISTIANO | DENIS
	{
		$debug = TRUE;
	}	
	
	if($_POST)
	{
		if($_POST['ds_funcao'] == "montaIdade")
		{
			montaIdade($_POST['dt_nascimento'],$_POST['dt_simulacao'],$_POST['tp_aposentadoria']);
		}
		
		if($_POST['ds_funcao'] == "calculaBeneficio")
		{
			calculaBeneficio(
			$_POST['cd_empresa'], $_POST['cd_registro_empregado'], $_POST['seq_dependencia'], $_POST['dt_ingresso'],
			$_POST['dt_simulacao'],$_POST['dt_nascimento'],$_POST['sexo_titular'],$_POST['tp_aposentadoria'],$_POST['nr_idade_apos'],$_POST['vl_salario'],$_POST['vl_rentabilidade'],
			$_POST['dt_vitalicio'],$_POST['dt_temporario'],$_POST['sexo_vitalicio'],$_POST['sexo_temporario'],
			$_POST['fl_contribuicao_voluntaria'], $_POST['vl_contribuicao_voluntaria'], $_POST['qt_contribuicao_voluntaria'], $_POST['fl_adiantamento'], $_POST['vl_adiantamento'], $_POST['fl_autopatrocinio_pagar_patroc']
			
			);
		}
		
		if($_POST['ds_funcao'] == "buscaMesesApos")
		{
			buscaMesesApos($_POST['dt_nascimento'],$_POST['dt_simulacao'],$_POST['dt_ingresso'],$_POST['nr_idade_apos']);
		}		
	}
	
	function buscaMesesApos($dt_nascimento, $dt_simulacao, $dt_ingresso, $nr_idade_apos)
	{
		$ar_datas       = calculaDatas($dt_nascimento, $dt_ingresso,$nr_idade_apos);
		#echo "<PRE>";print_r($ar_datas);echo"</PRE>";
		$dt_sol_apos    = $ar_datas['dt_sol_apos'];
		
		echo intval(calculaMeses($dt_simulacao, $dt_sol_apos));
	}
	
	function montaIdade($dt_nascimento, $dt_simulacao,$tp_aposentadoria)
	{
		$nr_conta = calculaIdadeMinima($dt_nascimento, $dt_simulacao);
		$nr_fim   = 110;
		
		if($tp_aposentadoria == -1)
		{
			$nr_conta = $nr_fim+1;
		}
		
		if($tp_aposentadoria == 0)
		{
			if($nr_conta < 60)
			{
				$nr_conta = 60;
			}
		}
		
		if($tp_aposentadoria == 1)
		{
			$nr_fim = 59;
		}
		
		if(($nr_conta > $nr_fim) and ($tp_aposentadoria == 1))
		{
			echo "ERRO";
		}
		else
		{
			echo '
				<select name="nr_idade_apos" id="nr_idade_apos"  class="form_simulacao_select" onchange="buscaMesesApos(this.value);novaSimulacao();">				
					<option value="-1">Selecione</option>
				';
			while($nr_conta <= $nr_fim)
			{
				echo '<option value="'.$nr_conta.'">'.$nr_conta.'</option>';
				$nr_conta++;
			}
			echo '</select>';
		}
	}
	
	function calculaIdadeMinima($dt_nascimento, $dt_simulacao)
	{
		$ar_datas = calculaDatas($dt_nascimento, $dt_simulacao,60);
		$qt_meses = calculaMeses($dt_nascimento, $ar_datas['dt_referencia']);
		$nr_idade = (int)($qt_meses / 12);
		return $nr_idade;	
	}
	
	function calculaDatas($dt_nascimento, $dt_simulacao, $nr_idade_apos)
	{
		global $db;
		$qr_sql = "
					SELECT TO_CHAR(CASE WHEN (CASE WHEN (TO_DATE('".$dt_nascimento."','DD/MM/YYYY')+ '50 years'::interval) > (TO_DATE('".$dt_simulacao."','DD/MM/YYYY') + '10 year'::interval)
	                                               THEN (TO_DATE('".$dt_nascimento."','DD/MM/YYYY')+ '50 years'::interval)
		                                           ELSE (TO_DATE('".$dt_simulacao."','DD/MM/YYYY') + '10 years'::interval)
	                                          END) > (TO_DATE('".$dt_nascimento."','DD/MM/YYYY')+ '".$nr_idade_apos." years'::interval)
                                        THEN (CASE WHEN (TO_DATE('".$dt_nascimento."','DD/MM/YYYY')+ '50 years'::interval) > (TO_DATE('".$dt_simulacao."','DD/MM/YYYY') + '10 year'::interval)
			                                       THEN (TO_DATE('".$dt_nascimento."','DD/MM/YYYY')+ '50 years'::interval)
			                                       ELSE (TO_DATE('".$dt_simulacao."','DD/MM/YYYY') + '10 years'::interval)
		                                      END)	
                                        ELSE TO_DATE('".$dt_nascimento."','DD/MM/YYYY')+ '".$nr_idade_apos." years'::interval
							       END,'DD/MM/YYYY') AS dt_sol_apos,
					       TO_CHAR((TO_DATE('".$dt_nascimento."','DD/MM/YYYY')+ '50 years'::interval),'DD/MM/YYYY') AS dt_min_apos,
					       TO_CHAR((TO_DATE('".$dt_simulacao."','DD/MM/YYYY')+ '10 years'::interval),'DD/MM/YYYY') AS dt_min_plano,
					       TO_CHAR(CASE WHEN (TO_DATE('".$dt_nascimento."','DD/MM/YYYY')+ '60 years'::interval) > (TO_DATE('".$dt_simulacao."','DD/MM/YYYY') + '10 year'::interval)
					                    THEN (TO_DATE('".$dt_nascimento."','DD/MM/YYYY')+ '60 years'::interval)
					                    ELSE (TO_DATE('".$dt_simulacao."','DD/MM/YYYY') + '10 years'::interval)
					               END,'DD/MM/YYYY') AS dt_ref_apos,
					       TO_CHAR(CASE WHEN (CASE WHEN (TO_DATE('".$dt_nascimento."','DD/MM/YYYY')+ '50 years'::interval) > (TO_DATE('".$dt_simulacao."','DD/MM/YYYY') + '10 year'::interval)
					                               THEN (TO_DATE('".$dt_nascimento."','DD/MM/YYYY')+ '50 years'::interval)
					                               ELSE (TO_DATE('".$dt_simulacao."','DD/MM/YYYY') + '10 years'::interval)
					                          END) > CURRENT_DATE
										THEN (CASE WHEN (TO_DATE('".$dt_nascimento."','DD/MM/YYYY')+ '50 years'::interval) > (TO_DATE('".$dt_simulacao."','DD/MM/YYYY') + '10 year'::interval)
					                               THEN (TO_DATE('".$dt_nascimento."','DD/MM/YYYY')+ '50 years'::interval)
					                               ELSE (TO_DATE('".$dt_simulacao."','DD/MM/YYYY') + '10 years'::interval)
					                          END)
										ELSE CURRENT_DATE
								   END,'DD/MM/YYYY') AS dt_referencia	
	              ";
		$ob_resul = pg_query($db, $qr_sql);
		$ar_reg   = pg_fetch_array($ob_resul);
		
		if($_SERVER['REMOTE_ADDR'] == '10.63.255.150') #### CRISTIANO
		{
			#echo "<PRE>".$qr_sql."</PRE>";
		}		
		
		return $ar_reg;					  
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
	
	function calculaBeneficio($cd_empresa, $cd_registro_empregado, $seq_dependencia, $dt_ingresso, $dt_simulacao,$dt_nascimento,$sexo_titular, $tp_aposentadoria,$nr_idade_apos,$vl_salario,$vl_rentabilidade,$dt_vitalicio,$dt_temporario, $sexo_vitalicio, $sexo_temporario, $fl_contribuicao_voluntaria, $vl_contribuicao_voluntaria, $qt_contribuicao_voluntaria, $fl_adiantamento, $vl_adiantamento, $fl_autopatrocinio_pagar_patroc)
	{
		global $debug;
		
		$ar_atendimento = Array();
		
		#$ar_datas       = calculaDatas($dt_nascimento, $dt_simulacao,$nr_idade_apos);
		$ar_datas       = calculaDatas($dt_nascimento, $dt_ingresso,$nr_idade_apos);
		echo ($debug ? "<PRE>".print_r($ar_datas,true)."</PRE>" : "");
		$dt_sol_apos    = $ar_datas['dt_sol_apos'];
		$dt_referencia  = $ar_datas['dt_referencia'];
		$dt_ref_apos    = $ar_datas['dt_ref_apos'];
		
		if($tp_aposentadoria == 1)
		{
			$dt_ref_apos = $dt_sol_apos;
		}
		
		echo ($debug ?  "<BR>DT INGRESSO => ".$dt_ingresso : "");
		echo ($debug ?  "<BR>DT VITALICIO => ".$dt_vitalicio : "");
		echo ($debug ?  "<BR>DT TEMPORARIO => ".$dt_temporario : "");
		
		$dt_min_apos    = $ar_datas['dt_min_apos'];
		$dt_min_plano   = $ar_datas['dt_min_plano'];
		$qt_meses_refe  = calculaMeses($dt_simulacao, $dt_ref_apos);
		echo ($debug ?  "<BR>QT MESES REF=> ".$qt_meses_refe : "");
		
		$qt_meses_apos  = calculaMeses($dt_simulacao, $dt_sol_apos);
		if($qt_meses_apos < 0)
		{
			$qt_meses_apos = 0;
			$dt_sol_apos   = $dt_simulacao;
		}		
		echo ($debug ?  "<BR>QT MESES APOS => ".$qt_meses_apos : "");
		
		$qt_meses       = calculaMeses($dt_nascimento, $dt_simulacao);
		echo ($debug ?  "<BR>QT MESES => ".$qt_meses : "");
		
		$nr_idade_atual = ($qt_meses / 12);
		$ar_faixa       = getFaixa($dt_nascimento,$nr_idade_atual);
		
		#### CONTRIBUIÇÕES ####
		$vl_contribuicao_proga = calculaContribuicaoResultado($vl_salario,$ar_faixa[0]['num_faixa']);
		$vl_contribuicao_risco = calculaContribuicaoRisco($vl_salario,$ar_faixa[0]['num_faixa']);
		$vl_contribuicao_admin = calculaContribuicaoAdmin($vl_salario,$ar_faixa[0]['num_faixa']);
		
		$fl_checkAutoPatrocinioComPatrocinadora = checkAutoPatrocinioComPatrocinadora($cd_empresa, $cd_registro_empregado, $seq_dependencia);
		echo ($debug ?  "<BR> AUTOPATROCINIO COM PATROCINADORA => ".$fl_checkAutoPatrocinioComPatrocinadora : "");		
		
		$fl_checkAutoPatrocinioSemPatrocinadora = checkAutoPatrocinioSemPatrocinadora($cd_empresa, $cd_registro_empregado, $seq_dependencia);
		echo ($debug ?  "<BR> AUTOPATROCINIO SEM PATROCINADORA => ".$fl_checkAutoPatrocinioSemPatrocinadora : "");
	
		echo ($debug ?  "<BR> FL_AUTOPATROCINIO_PAGAR_PATROC => ".$fl_autopatrocinio_pagar_patroc : "");
		if(($fl_checkAutoPatrocinioComPatrocinadora == "S") AND ($fl_autopatrocinio_pagar_patroc == "N"))
		{
			#### FORÇA SIMULAÇÃO DO AUTOPATROCINIO SEM PAGAR A PARTE DA PATROCINADORA ####
			$fl_checkAutoPatrocinioSemPatrocinadora = "S";
			echo ($debug ?  "<BR> AUTOPATROCINIO SEM PATROCINADORA (FORÇADO) => ".$fl_checkAutoPatrocinioSemPatrocinadora : "");
		}
		
		#### CIP ####
		$vl_cip_participante  = calculaCIP($vl_salario,$vl_rentabilidade,$dt_simulacao,$dt_sol_apos,$qt_meses_apos,$ar_faixa);
		#$vl_cip_patrocinadora = calculaCIP($vl_salario,$vl_rentabilidade,$dt_simulacao,$dt_sol_apos,$qt_meses_apos,$ar_faixa);
		$vl_cip_patrocinadora = ($fl_checkAutoPatrocinioSemPatrocinadora == "S" ? 0 : calculaCIP($vl_salario,$vl_rentabilidade,$dt_simulacao,$dt_sol_apos,$qt_meses_apos,$ar_faixa));
		
		#echo "<BR> CIP PATROCINADORA => ".$vl_cip_patrocinadora;
		echo ($debug ? "<PRE>".print_r($ar_datas,true)."</PRE>" : "");
		
		#### CONTRIBUICAO VOLUNTARIA ####
		$vl_saldo_voluntaria = 0;
		if($fl_contribuicao_voluntaria == "S")
		{
			#$fl_contribuicao_voluntaria, $vl_contribuicao_voluntaria, $qt_contribuicao_voluntaria)
			$vl_saldo_voluntaria = calculaContribuicaoVoluntaria($vl_contribuicao_voluntaria, $qt_contribuicao_voluntaria, $vl_rentabilidade);
			echo ($debug ?  "<BR> SALDO VOLUNTARIA => ".$vl_saldo_voluntaria : "");
			
			if($qt_contribuicao_voluntaria < $qt_meses_apos)
			{
				$vl_saldo_voluntaria = atualizaContribuicaoVoluntaria(($qt_meses_apos - $qt_contribuicao_voluntaria),$vl_saldo_voluntaria,$vl_rentabilidade);
				echo ($debug ?  "<BR> SALDO VOLUNTARIA ATUAL => ".$vl_saldo_voluntaria : "");
			}
		}		
		
		/*
		#### EVOLUÇÃO SOMENTE PATROCINADORA ATÉ O LIMITE ####
		$vl_cip_patrocinadora = 0;
		if($qt_meses_refe > 0)
		{
			$vl_cip_patrocinadora = calculaCIP($vl_salario,$vl_rentabilidade,$dt_simulacao,$dt_ref_apos,$qt_meses_apos,$ar_faixa);
		}
		*/
		
		$fl_BPD = checkBPD($cd_empresa, $cd_registro_empregado, $seq_dependencia);
		echo ($debug ?  "<br>É BPD => ".$fl_BPD : "");
		
		if($fl_BPD == "S")
		{
			#### BPD NÃO TEM PROJECAO ####
			$vl_cip_participante  = 0;
			$vl_cip_patrocinadora = 0;
		}		
		
		$vl_cip = ($vl_cip_participante + $vl_cip_patrocinadora);	

		echo ($debug ?  "<br>VL CIP PARTICIPANTE => ".$vl_cip_participante : "");
		echo ($debug ?  "<br>VL CIP PATROCINADORA => ".$vl_cip_patrocinadora : "");
		echo ($debug ?  "<br>CIP PROJETADA => ".$vl_cip : "");		
		
		#### BUSCA CIP ATUAL ####
		$qt_cota = calculaCIPAtual($cd_empresa, $cd_registro_empregado, $seq_dependencia, $dt_ingresso);
		$vl_cota = getValorCota();
		$vl_cip_atual = $qt_cota * getValorCota();
		
		$ar_atendimento['vl_cota'] = $vl_cota;
		$ar_atendimento['vl_cip_atual'] = $vl_cip_atual;
		
		echo ($debug ?  "<br>QT COTAS => ".$qt_cota : "");
		echo ($debug ?  "<br>VL COTAS => ".$vl_cota : "");
		echo ($debug ?  "<br>CIP ATUAL SALDO => ".$vl_cip_atual : "");
		$vl_cip_atual = atualizaCIPAtual($qt_meses_apos,$vl_cip_atual,$vl_rentabilidade);
		echo ($debug ?  "<BR>CIP ATUAL CORRIGIDA => ".$vl_cip_atual : "");
		
		
		#### SOMAS AS CIP's #####
		$vl_cip = ($vl_cip + $vl_cip_atual + $vl_saldo_voluntaria);	
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
		
		echo ($debug ?  "<BR> ########### FATOR ATUARIAL ############ " : "");
		echo ($debug ?  "<BR>TITULAR IDADE => ".$nr_idade_apos : "");
		echo ($debug ?  "<BR>TITULAR SEXO => ".$sexo_titular : "");
		echo ($debug ?  "<BR>DEP VITALICIO DATA => ".$dt_vitalicio : "");
		echo ($debug ?  "<BR>DEP VITALICIO SEXO => ".$sexo_vitalicio : "");
		echo ($debug ?  "<BR>DEP TEMPORARIO DATA => ".$dt_temporario : "");
		echo ($debug ?  "<BR>DEP TEMPORARIO SEXO => ".$sexo_temporario : "");			
		
		#### BENEFICIO ####
		if(($dt_vitalicio == "") and ($dt_temporario == ""))
		{
			echo ($debug ?  "<BR>FA 1" : "");
			#$vl_fa = getFatorAtuarial('AX',$nr_idade_apos,0);
			$vl_fa = getFatorAtuarial('AX',$nr_idade_apos,$sexo_titular,0,"");			
		}
		else if(($dt_vitalicio != "") and ($dt_temporario == ""))
		{
			echo ($debug ?  "<BR>FA 2" : "");
			
			$qt_idade_vitalicio = calculaMeses($dt_vitalicio, $dt_sol_apos);
			$nr_idade_vitalicio = (int)($qt_idade_vitalicio / 12);
			$ar_atendimento['nr_idade_vitalicio'] = $nr_idade_vitalicio;
			
			#$vl_fa = getFatorAtuarial('AX' ,$nr_idade_apos,0);
			#$vl_fa+= getFatorAtuarial('AXY',$nr_idade_apos,$nr_idade_vitalicio);
			
			$vl_fa = getFatorAtuarial('AX' ,$nr_idade_apos,$sexo_titular,0,"");
			$vl_fa+= getFatorAtuarial('AXY',$nr_idade_apos,$sexo_titular,$nr_idade_vitalicio,$sexo_vitalicio);			
		}
		else if(($dt_vitalicio == "") and ($dt_temporario != ""))
		{
			echo ($debug ?  "<BR>FA 3" : "");
			
			$qt_idade_dependente = calculaMeses($dt_temporario, $dt_sol_apos);
			$nr_idade_dependente = (int)($qt_idade_dependente / 12);
			$ar_atendimento['nr_idade_temporario'] = $nr_idade_dependente;
			
			#$vl_fa = getFatorAtuarial('AX' ,$nr_idade_apos,0);
			$vl_fa = getFatorAtuarial('AX' ,$nr_idade_apos,$sexo_titular,0,"");			
			
			if( $nr_idade_dependente < 21)
			{
				#$vl_fa+= getFatorAtuarial('AXZ',$nr_idade_apos,$nr_idade_dependente);
				$vl_fa+= getFatorAtuarial('AXZ',$nr_idade_apos,$sexo_titular,$nr_idade_dependente,"");				
			}
			
		}	
		else if(($dt_vitalicio != "") and ($dt_temporario != ""))
		{
			echo ($debug ?  "<BR>FA 3" : "");
			
			$qt_idade_vitalicio = calculaMeses($dt_vitalicio, $dt_sol_apos);
			$nr_idade_vitalicio = (int)($qt_idade_vitalicio / 12);	
			$ar_atendimento['nr_idade_vitalicio'] = $nr_idade_vitalicio;
			
			$qt_idade_dependente = calculaMeses($dt_temporario, $dt_sol_apos);
			$nr_idade_dependente = (int)($qt_idade_dependente / 12);	
			$ar_atendimento['nr_idade_temporario'] = $nr_idade_dependente;
			
			echo ($debug ?  "<BR>nr_idade_vitalicio => ".$nr_idade_vitalicio : "");
			echo ($debug ?  "<BR>nr_idade_dependente => ".$nr_idade_dependente : "");			
			
			#$vl_fa = getFatorAtuarial('AX' ,$nr_idade_apos,0);
			$vl_fa = getFatorAtuarial('AX' ,$nr_idade_apos,$sexo_titular,0,"");
			echo ($debug ?  "<BR>vl_fa AX => ".$vl_fa : "");			
			
			if( $nr_idade_dependente < 21)
			{
				echo ($debug ?  "<BR>vl_fa AXZ => ".getFatorAtuarial('AXZ',$nr_idade_apos,$sexo_titular,$nr_idade_dependente,"") : "");				
				echo ($debug ?  "<BR>vl_fa AXY => ".getFatorAtuarial('AXY',$nr_idade_apos,$sexo_titular,$nr_idade_vitalicio,$sexo_vitalicio) : "");				
				echo ($debug ?  "<BR>vl_fa DX 1 => ".getFatorAtuarial('DX',(($nr_idade_vitalicio + 21) - $nr_idade_dependente),$sexo_titular,0,"") : "");				
				echo ($debug ?  "<BR>vl_fa DX 2 => ".getFatorAtuarial('DX',$nr_idade_vitalicio,$sexo_titular,0,"") : "");	

				$vl_fa+= getFatorAtuarial('AXZ',$nr_idade_apos,$sexo_titular,$nr_idade_dependente,"");
				$vl_fa+= getFatorAtuarial('AXY',$nr_idade_apos,$sexo_titular,$nr_idade_vitalicio,$sexo_vitalicio) * ((getFatorAtuarial('DX',(($nr_idade_vitalicio + 21) - $nr_idade_dependente),$sexo_titular,0,""))/getFatorAtuarial('DX',$nr_idade_vitalicio,$sexo_titular,0,""));
				
				#$vl_fa+= getFatorAtuarial('AXZ',$nr_idade_apos,$nr_idade_dependente);
				#$vl_fa+= getFatorAtuarial('AXY',$nr_idade_apos,$nr_idade_vitalicio) * ((getFatorAtuarial('DX',(($nr_idade_vitalicio + 21) - $nr_idade_dependente),0))/getFatorAtuarial('DX',$nr_idade_vitalicio,0));
			}
			else
			{
				#$vl_fa+= getFatorAtuarial('AXY',$nr_idade_apos,$nr_idade_vitalicio);
				$vl_fa+= getFatorAtuarial('AXY',$nr_idade_apos,$sexo_titular,$nr_idade_vitalicio,$sexo_vitalicio);				
			}
		}
		$ar_atendimento['vl_fa'] = $vl_fa;
		
		echo ($debug ?  "<BR>FA => ".$vl_fa : "");
		echo ($debug ?  "<BR> ####################################### " : "");
		
		echo "
		<style>
			.contrib {
				background: url(img/simulador_ceeeprev_contribuicao.gif) no-repeat;
				width:162px;
				height:95px;
				text-align:center;
			}	
			.contrib_titulo{
				padding-top: 5px;
				font-size: 13px;
				font-weight: bold;
				font-family: Arial,'MS Sans Serif';	
				color: #000;
				text-align:center;
				white-space: nowrap;
				margin-left:20px;
			}	
			
			.contrib_valor{
				margin-top: 12px;
				font-size: 16pt;
				font-weight: bold;
				font-family: Arial,'MS Sans Serif';	
				color: #000;	
			}
			
			.contrib_valor_maior{
				margin-top: 10px;
				font-size: 18pt;
				font-weight: bold;
				font-family: Arial,'MS Sans Serif';	
				color: #000;	
			}
			
			.saldo_acum {
				background: url(img/simulador_ceeeprev_cd.gif) no-repeat;
				width:220px;
				height:120px;
				text-align:center;
			}	
			.saldo_acum_titulo{
				padding-top: 15px;
				font-size: 12pt;
				font-weight: bold;
				font-family: Arial,'MS Sans Serif';	
				color: white;
			}	
			.saldo_acum_valor{
				margin-top: 15px;
				font-size: 18pt;
				font-weight: bold;
				font-family: Arial,'MS Sans Serif';	
				color: white;	
			}
			
			.ben_inicial {
				background: url(img/simulador_ceeeprev_cd.gif) no-repeat;
				width:220px;
				height:120px;
				text-align:center;
			}	
			.ben_inicial_titulo{
				padding-top: 15px;
				font-size: 12pt;
				font-weight: bold;
				font-family: Arial,'MS Sans Serif';	
				color: white;
			}	
			.ben_inicial_valor{
				margin-top: 15px;
				font-size: 24pt;
				font-weight: bold;
				font-family: Arial,'MS Sans Serif';	
				color: white;	
			}		
		
			.texto_resultado {
				font-family: 'Gudea', calibri, arial;
				font-weight: normal;
				
			}
		</style>
		<div style='texto_resultado'>
		";
	
		if($fl_BPD != "S")
		{
			echo "
				<table border='0' width='100%' align='center'> 
					<tr>
						<td align='center'>
							<div class='contrib'>
								<table border='0' width='100%' align='center'>
									<tr>
										<td style='width:115px; text-align:right;'>
											<div class='contrib_titulo'>Contribuição<BR>Programável</div>
										</td>
										<td style='text-align:left;padding-left:5px;'>
											<img src='img/saiba_mais.gif' border='0' onclick='saibaMais(100);' style='cursor:pointer;' title='Saiba mais'>
										</td
									</tr>
									<tr>
										<td colspan='2' style='text-align:center;'>
											<div class='contrib_valor'><span style='font-size: 60%'>R$</span> ".number_format(round(($vl_contribuicao_proga),2),2,',','.')." </div>
										</td>
									</tr>								
								</table>
							</div>			
						</td>
						<td align='center'>
							<div class='contrib'>
								<table border='0' width='100%' align='center'>
									<tr>
										<td style='width:115px; text-align:right;'>
											<div class='contrib_titulo'>Contribuição<BR>de Risco</div>
										</td>
										<td style='text-align:left;padding-left:5px;'>
											<img src='img/saiba_mais.gif' border='0' onclick='saibaMais(101);' style='cursor:pointer;' title='Saiba mais'>
										</td
									</tr>
									<tr>
										<td colspan='2' style='text-align:center;'>
											<div class='contrib_valor'><span style='font-size: 60%'>R$</span> ".number_format(round(($vl_contribuicao_risco),2),2,',','.')."</div>
										</td>
									</tr>								
								</table>
							</div>			
						</td>	
						<td align='center'>
							<div class='contrib'>
								<table border='0' width='100%' align='center'>
									<tr>
										<td style='width:115px; text-align:right;'>
											<div class='contrib_titulo'>Contribuição<BR>Administrativa</div>
										</td>
										<td style='text-align:left;padding-left:5px;'>
											<img src='img/saiba_mais.gif' border='0' onclick='saibaMais(102);' style='cursor:pointer;' title='Saiba mais'>
										</td
									</tr>
									<tr>
										<td colspan='2' style='text-align:center;'>
											<div class='contrib_valor'><span style='font-size: 60%'>R$</span> ".number_format(round(($vl_contribuicao_admin),2),2,',','.')."</div>
										</td>
									</tr>								
								</table>
							</div>			
						</td>
						<td align='center'>
							<div class='contrib'>
								<table border='0' width='100%' align='center'>
									<tr>
										<td style='width:115px; text-align:right;'>
											<div class='contrib_titulo'>Contribuição<BR>Total</div>
										</td>
										<td style='text-align:left;padding-left:5px;'>
											<img src='img/saiba_mais.gif' border='0' onclick='saibaMais(103);' style='cursor:pointer;' title='Saiba mais'>
										</td
									</tr>
									<tr>
										<td colspan='2' style='text-align:center;'>
											<div class='contrib_valor_maior'><span style='font-size: 60%'>R$</span> ".number_format(round(($vl_contribuicao_proga+$vl_contribuicao_risco+$vl_contribuicao_admin),2),2,',','.')."</div>
										</td>
									</tr>								
								</table>
							</div>			
						</td>					
					</tr>
				</table>
			";
		}
		
		echo "
			<BR>
			<table border='0' width='100%' align='center'> 
				<tr>";
		//OS : 53188
		//saldo remanescente não é suficiente para um benefício superior a 1 UPCEEE
		$fl_mensagem_adiantamento = false;

		if(trim(strtoupper($fl_adiantamento)) == "S")
		{
			if(round(($vl_cip/$vl_fa),2) > getUPCEEE())
			{
				echo "
					<td align='center'>
						<div class='saldo_acum'>
							<div class='saldo_acum_titulo'>Adiantamento<BR>".$vl_adiantamento."%</div>
							<div class='saldo_acum_valor'><span style='font-size: 60%'>R$</span> ".number_format(round(($vl_adiantamento_calc),2),2,',','.')."</div>
						</div>			
					</td>";
			}
			else
			{
				$fl_mensagem_adiantamento = true;
			}
		}


		if(!$fl_mensagem_adiantamento)
		{


			echo "
					<td align='center'>
						<div class='saldo_acum'>
							<div class='saldo_acum_titulo'>Saldo Acumulado<BR>Projetado</div>
							<div class='saldo_acum_valor'><span style='font-size: 60%'>R$</span> ".number_format(round(($vl_cip),2),2,',','.')."</div>
						</div>			
					</td>
					<td align='center'>
						<div class='ben_inicial'>
							<div class='ben_inicial_titulo'>Benefício Bruto<BR>Inicial Projetado</div>
							<div class='ben_inicial_valor'><span style='font-size: 60%'>R$</span> ".number_format(round(($vl_cip/$vl_fa),2),2,',','.')."</div>
						</div>
					</td>
			
		     ";
		}

		echo 	"</tr>
			</table>
			<BR>";

		#### CRISTIANO | DENIS | ATENDENTES ####
		#if(in_array($_SERVER['REMOTE_ADDR'], array('10.63.4.150', '10.63.4.102', '10.63.5.44', '10.63.4.87', '10.63.4.74', '10.63.4.76', '10.63.4.75', '10.63.4.78', '10.63.4.73', '10.63.4.71', '10.63.4.79', '10.63.4.81'))) 
		if(verificaIp()) 
		{
		#### INFOS PARA O ATENDIMENTO ####
		echo '
				<table class="sort-table" id="table-1" align="center" cellpadding="2" cellspacing="2">
					<thead>
						<tr>
							<td colspan="2">Informações adicionais</td>
						</tr>
					</thead>
					<tbody>	
						<tr class="sort-par"> 
							<td style="white-space: nowrap; font-weight:bold;">
								Valor Atual das Contas:
							</td>					
							<td style="text-align:right; font-size: 110%;">
								<span style="font-size: 80%;">R$</span> '.number_format($ar_atendimento['vl_cip_atual'],2,',','.').'
							</td>							
						</tr>
						
						<tr class="sort-impar" > 
							<td style="white-space: nowrap; font-weight:bold;">
								Valor da Cota:
							</td>					
							<td style="text-align:right; font-size: 110%;">
								<span style="font-size: 80%;">R$</span> '.number_format($ar_atendimento['vl_cota'],8,',','.').'
							</td>							
						</tr>
						
						<tr class="sort-par"> 
							<td style="white-space: nowrap; font-weight:bold;">
								Idade do Dependente Vitalício:
							</td>					
							<td style="text-align:right; font-size: 110%;">
								'.$ar_atendimento['nr_idade_vitalicio'].'
							</td>							
						</tr>

						<tr class="sort-impar" > 
							<td style="white-space: nowrap; font-weight:bold;">
								Idade do Dependente Temporário:
							</td>					
							<td style="text-align:right; font-size: 110%;">
								'.$ar_atendimento['nr_idade_temporario'].'
							</td>							
						</tr>	

						<tr class="sort-par"> 
							<td style="white-space: nowrap; font-weight:bold;">
								Fator Atuarial:
							</td>					
							<td style="text-align:right; font-size: 110%;">
								'.number_format($ar_atendimento['vl_fa'],4,',','.').'
							</td>							
						</tr>							
						
					</tbody>	
				</table>
				<BR>
		     ';
		}

		if($fl_mensagem_adiantamento)
		{
			echo "<center><span style='color:red; font-weight:bold; font-size:18px;'>Saldo remanescente não é suficiente para um benefício superior a 1 UPCEEE. Diminua o percentual do adiantamento.</span></center>";
			echo "<BR><BR>";
		}
		
		echo "</div>";
	}
	

	function calculaCIP($vl_salario,$vl_rentabilidade,$dt_simulacao,$dt_sol_apos,$qt_meses_apos,$ar_faixa)
	{
		#echo "calculaCIP($vl_salario,$vl_rentabilidade,$dt_simulacao,$dt_sol_apos,$qt_meses_apos,$ar_faixa)";
		
		$vl_cip = 0;
		$nr_conta = 0;
		$nr_fim   =  count($ar_faixa);
		#echo "<PRE>";
		#echo "FAIXA - TROCA - MESES - CONTRIBUICAO - CIP FAIXA - CIP ACUM";
		while($nr_conta < $nr_fim)
		{
			#echo "<br>";
			if($nr_conta == 0)
			{
				#echo "A";
				$dt_troca = $dt_simulacao;
				if($nr_conta == ($nr_fim - 1))
				{
					$ar_faixa[$nr_conta]['dt_troca'] = $dt_sol_apos;
					#echo "1";
				}				
			}
			else if($nr_conta == ($nr_fim - 1))
			{
				$dt_troca = $ar_faixa[$nr_conta-1]['dt_troca'];
				$ar_faixa[$nr_conta]['dt_troca'] = $dt_sol_apos;
				#echo "B";
			}
			else
			{
				$dt_troca = $ar_faixa[$nr_conta-1]['dt_troca'];
				#echo "C";
			}			
			/*
			if($nr_conta == 0)
			{
				$dt_troca = $dt_simulacao;
				echo "A";
			}
			else if($nr_conta == ($nr_fim - 1))
			{
				$dt_troca = $ar_faixa[$nr_conta-1]['dt_troca'];
				$ar_faixa[$nr_conta]['dt_troca'] = $dt_sol_apos;
				echo "B";
			}
			else
			{
				$dt_troca = $ar_faixa[$nr_conta-1]['dt_troca'];
				echo "C";
			}
			*/
			$nr_faixa_atual        = $ar_faixa[$nr_conta]['num_faixa'];
			$dt_troca_faixa_atual  = $ar_faixa[$nr_conta]['dt_troca'];
			$qt_mes_faixa          = calculaMeses($dt_troca, $dt_troca_faixa_atual);
			$qt_mes_acumulado      += $qt_mes_faixa;
			$qt_mes_atualiza       = $qt_meses_apos - $qt_mes_acumulado;
			$vl_contribuicao_faixa = calculaContribuicao($vl_salario,$nr_faixa_atual);
			$vl_cip_faixa          = calculaFaixaCIP($qt_mes_faixa,$vl_contribuicao_faixa,$vl_rentabilidade);
			$vl_cip_faixa_saldo    = atualizaFaixaCIP($qt_mes_atualiza,$vl_cip_faixa,$vl_rentabilidade);
			$vl_cip                += $vl_cip_faixa_saldo ;
			
			#echo "<BR>$nr_faixa_atual - $dt_troca | ".$ar_faixa[$nr_conta]['dt_troca']." - $qt_mes_faixa - $vl_contribuicao_faixa - $vl_cip_faixa - $vl_cip_faixa_saldo - $vl_cip";
			$nr_conta++;
		}
		#echo "</PRE>";
		return $vl_cip;
	}	

	function calculaCIPAtual($cd_empresa, $cd_registro_empregado, $seq_dependencia, $dt_ingresso)
	{	
		global $db;
		/*
		$qr_sql = "
					SELECT (COALESCE(a.qt_cip,0) +  COALESCE(b.qt_cpi,0) + COALESCE(c.qt_port,0) + COALESCE(d.qt_portea,0)) AS qt_cota
					  FROM (SELECT fnc_saldo_cd_contas::NUMERIC AS qt_cip 
					          FROM oracle.fnc_saldo_cd_contas(2, ".$cd_empresa.", ".$cd_registro_empregado.", ".$seq_dependencia.", '".$dt_ingresso."', 11)) a,
					       (SELECT fnc_saldo_cd_contas::NUMERIC AS qt_cpi 
					          FROM oracle.fnc_saldo_cd_contas(2, ".$cd_empresa.", ".$cd_registro_empregado.", ".$seq_dependencia.", '".$dt_ingresso."', 1301)) b,
					       (SELECT fnc_saldo_cd_contas::NUMERIC AS qt_port 
					          FROM oracle.fnc_saldo_cd_contas(2, ".$cd_empresa.", ".$cd_registro_empregado.", ".$seq_dependencia.", '".$dt_ingresso."', 1401)) c,
					       (SELECT fnc_saldo_cd_contas::NUMERIC AS qt_portea 
					          FROM oracle.fnc_saldo_cd_contas(2, ".$cd_empresa.", ".$cd_registro_empregado.", ".$seq_dependencia.", '".$dt_ingresso."', 1402)) d					
	              ";
		*/
		$qr_sql = "
					SELECT ceeeprev_cip_quota AS qt_cota
					  FROM simulador.ceeeprev_cip_quota(".$cd_empresa.", ".$cd_registro_empregado.", ".$seq_dependencia.", TO_DATE('".$dt_ingresso."','DD/MM/YYYY'));
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

	function atualizaContribuicaoVoluntaria($qt_meses,$vl_cip,$vl_rentabilidade)
	{
		$vl_cip = (pow((1.000000000 + $vl_rentabilidade), $qt_meses)) * $vl_cip;
		return $vl_cip;
	}	
	
	function calculaContribuicaoVoluntaria($vl_contribuicao,$qt_meses, $vl_rentabilidade)
	{
		$vl_cip = (pow((1.000000000 + $vl_rentabilidade), $qt_meses)) - 1;
		$vl_cip = $vl_cip / $vl_rentabilidade;
		$vl_cip = $vl_cip * $vl_contribuicao;
		
		return $vl_cip;
	}	
	
	function atualizaFaixaCIP($qt_meses,$vl_cip,$vl_rentabilidade)
	{
		$vl_cip = (pow((1.000000000 + $vl_rentabilidade), $qt_meses)) * $vl_cip;
		return $vl_cip;
	}
	
	function calculaFaixaCIP($qt_meses,$vl_contribuicao,$vl_rentabilidade)
	{
		$vl_cip = (pow((1.000000000 + $vl_rentabilidade), $qt_meses)) - 1;
		$vl_cip = $vl_cip / $vl_rentabilidade;
		$vl_cip = $vl_cip * $vl_contribuicao;
		
		return $vl_cip;
	}
	
	function calculaContribuicao($vl_salario,$nr_faixa)
	{
		$vl_contribuicao = ($vl_salario * getFaixaValor($nr_faixa,'basica'));
		if($vl_salario > (5 * getUPCEEE()))
		{
			$vl_contribuicao = $vl_contribuicao + (($vl_salario - (5 * getUPCEEE())) * getFaixaValor($nr_faixa,'unidade'));
		}
		$vl_contribuicao = ($vl_contribuicao * 13) / 12;
		
		return round($vl_contribuicao,2);
	}

	function calculaContribuicaoResultado($vl_salario,$nr_faixa)
	{
		$vl_contribuicao = ($vl_salario * getFaixaValor($nr_faixa,'basica'));
		if($vl_salario > (5 * getUPCEEE()))
		{
			$vl_contribuicao = $vl_contribuicao + (($vl_salario - (5 * getUPCEEE())) * getFaixaValor($nr_faixa,'unidade'));
		}
		
		return round($vl_contribuicao,2);
	}	
	
	function calculaContribuicaoRisco($vl_salario,$nr_faixa)
	{
		$vl_contribuicao = ($vl_salario * getFaixaValor($nr_faixa,'risco'));
		if($vl_salario > (5 * getUPCEEE()))
		{
			$vl_contribuicao = $vl_contribuicao + (($vl_salario - (5 * getUPCEEE())) * getFaixaValor($nr_faixa,'risco_upceee'));
		}
		
		return round($vl_contribuicao,2);
	}

	function calculaContribuicaoAdmin($vl_salario,$nr_faixa)
	{
		$vl_contribuicao = (calculaContribuicaoResultado($vl_salario,$nr_faixa) + calculaContribuicaoRisco($vl_salario,$nr_faixa)) * getFaixaValor($nr_faixa,'adm');
		return round($vl_contribuicao,2);
	}
	
	function getUPCEEE()
	{
		global $db;
		$qr_sql = "
					SELECT vlr_taxa
					  FROM public.taxas
					 WHERE cd_indexador = 87
					 ORDER BY dt_taxa DESC
					 LIMIT 1						
	              ";
		$ob_resul = pg_query($db, $qr_sql);
		$ar_reg   = pg_fetch_array($ob_resul);
		return $ar_reg['vlr_taxa'];		

	}
	
	function getValorCota()
	{
		global $db;
		$qr_sql = "
					SELECT vlr_indice 
					  FROM public.indices  
					 WHERE cd_indexador = 70
					 ORDER BY dt_indice DESC
					 LIMIT 1						
	              ";
		$ob_resul = pg_query($db, $qr_sql);
		$ar_reg   = pg_fetch_array($ob_resul);
		return $ar_reg['vlr_indice'];		

	}	
	
	function getFaixaValor($nr_faixa,$tp_faixa)
	{
		global $db;
		$qr_sql = "
					SELECT num_faixa, taxa_basica, taxa_upceee, taxa_risco, taxa_risco_upceee, taxa_adm
					  FROM public.faixas_contrib_planos f
					 WHERE f.cd_empresa      = 0
					   AND f.cd_plano        = 2
					   AND f.num_faixa       = ".$nr_faixa."
					   AND f.data_referencia = (SELECT MAX(f1.data_referencia)
					                              FROM faixas_contrib_planos f1
					                             WHERE f1.cd_empresa = f.cd_empresa
					                               AND f1.cd_plano   = f.cd_plano)
					 ORDER BY f.num_faixa ASC
	              ";
		$ob_resul = pg_query($db, $qr_sql);
		$ar_reg   = pg_fetch_array($ob_resul);
		
		if($tp_faixa == 'basica')
		{
			return $ar_reg['taxa_basica'];
		}
		else if($tp_faixa == 'unidade')
		{
			return $ar_reg['taxa_upceee'];
		}
		else if($tp_faixa == 'risco')
		{
			return $ar_reg['taxa_risco'];
		}	
		else if($tp_faixa == 'risco_upceee')
		{
			return $ar_reg['taxa_risco_upceee'];
		}
		else if($tp_faixa == 'adm')
		{
			return $ar_reg['taxa_adm'];
		}			
		else
		{
			return 0;
		}
		
	}
	
	function getFaixa($dt_nascimento,$nr_idade)
	{
		global $db;
		$ar_retorno = Array();
		$qr_sql = "
					SELECT num_faixa, 
					       idade,
					       TO_CHAR((TO_DATE('".$dt_nascimento."','DD/MM/YYYY') + ((idade + 1) || ' years')::interval),'DD/MM/YYYY') AS dt_troca
					  FROM public.faixas_contrib_planos f
					 WHERE f.cd_empresa      = 0
					   AND f.cd_plano        = 2
					   AND f.idade           >= TRUNC(".$nr_idade.")
					   AND f.data_referencia = (SELECT MAX(f1.data_referencia)
					                              FROM faixas_contrib_planos f1
					                             WHERE f1.cd_empresa = f.cd_empresa
					                               AND f1.cd_plano   = f.cd_plano)
					 ORDER BY f.num_faixa ASC
	              ";
		$ob_resul = pg_query($db, $qr_sql);
		while($ar_reg   = pg_fetch_array($ob_resul))
		{
			$ar_retorno[] = $ar_reg;
		}
		return $ar_retorno;		
	}

	function getFatorAtuarial($cd_fa,$nr_idade_titular,$sexo_titular,$nr_idade_dependente,$sexo_dependente)
	{
		global $db;
		$qr_sql = "
			         SELECT fab.indice
			           FROM public.fatores_atuariais_beneficios fab
			          WHERE fab.cd_fa                        = '".$cd_fa."' 
			            AND fab.indicador_a                  = TRUNC(".$nr_idade_titular.")
				        AND COALESCE(fab.indicador_b,0)      = TRUNC(".$nr_idade_dependente.")
			            AND UPPER(fab.sexo_titular)          = UPPER('".trim($sexo_titular)."')
				        AND COALESCE(fab.sexo_dependente,'') = UPPER('".trim($sexo_dependente)."')						
						AND fab.cd_empresa                   = 0
			            AND fab.cd_plano                     = 2 
					 ORDER BY fab.dt_inicio DESC
				     LIMIT 1
	              ";
		$ob_resul = pg_query($db, $qr_sql);
		$ar_reg   = pg_fetch_array($ob_resul);
		return $ar_reg['indice'];		
	}

	function checkAutoPatrocinioComPatrocinadora($cd_empresa, $cd_registro_empregado, $seq_dependencia)
	{
		global $db;
		
		$qr_sql = "
					SELECT auto_patrocinio_com_patroc AS fl_auto_com_patroc
					  FROM simulador.auto_patrocinio_com_patroc(".$cd_empresa.",".$cd_registro_empregado.",".$seq_dependencia.")
		          ";
		$ob_resul = pg_query($db, $qr_sql);
		$ar_reg   = pg_fetch_array($ob_resul);
		return $ar_reg["fl_auto_com_patroc"];
	}	
	
	
	function checkAutoPatrocinioSemPatrocinadora($cd_empresa, $cd_registro_empregado, $seq_dependencia)
	{
		global $db;
		
		$qr_sql = "
					SELECT auto_patrocinio_sem_patroc AS fl_auto_sem_patroc
					  FROM simulador.auto_patrocinio_sem_patroc(".$cd_empresa.",".$cd_registro_empregado.",".$seq_dependencia.")
		          ";
		  
		$ob_resul = pg_query($db, $qr_sql);
		$ar_reg   = pg_fetch_array($ob_resul);
		return $ar_reg["fl_auto_sem_patroc"];
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
	
	function getTabuaExpectativaVida($cd_empresa, $nr_idade_atual)
	{
		global $db;

		$qr_sql = "
					SELECT te.expectativa_vida AS qt_expect_vida,
						   tc.desc_tabua_expect_vida AS ds_expect_vida
					  FROM planos_patrocinadoras pp
					  JOIN tabuas_expect_vida_cadastro tc
						ON tc.cd_tabua_expect_vida = pp.cd_tabua_expect_vida
					  JOIN tabuas_expectativa_vida te
						ON tc.cd_tabua_expect_vida = te.cd_tabua_expect_vida    
					 WHERE pp.cd_plano   = 2
					   AND pp.cd_empresa = ".$cd_empresa."
                       AND te.idade      = ".intval($nr_idade_atual)."
		          ";
		$ob_resul = pg_query($db, $qr_sql);
		$ar_reg   = pg_fetch_array($ob_resul);
		return $ar_reg;		
	}
	
	function verificaIp()
	{
		global $db;

		$qr_sql = "

		SELECT COUNT(*) AS tl
		  FROM projetos.usuarios_controledi 
		 WHERE (codigo IN (38, 170, 251) OR divisao = 'GCM')
		   AND estacao_trabalho = '".trim($_SERVER['REMOTE_ADDR'])."';";

		$ob_resul = pg_query($db, $qr_sql);
		$ar_reg   = pg_fetch_array($ob_resul);

		if(intval($ar_reg['tl']) > 0)
		{
			#echo '<!-- teste: true '.$ar_reg['tl'].' -->';
			return true;
		}
		else
		{
			#echo '<!-- teste: flase '.$ar_reg['tl'].' -->';
			return false;
		}
	}	
?>