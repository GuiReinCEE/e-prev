<?php
	include_once('inc/conexao.php');

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
			$_POST['dt_simulacao'],$_POST['dt_nascimento'],$_POST['sexo_titular'],$_POST['tp_aposentadoria'],$_POST['nr_idade_apos'],$_POST['vl_salario'],$_POST['vl_salario_perc'],$_POST['vl_rentabilidade'],$_POST['dt_vitalicio'],$_POST['dt_temporario'],
			$_POST['fl_contribuicao_voluntaria'], $_POST['vl_contribuicao_voluntaria'], $_POST['qt_contribuicao_voluntaria'], $_POST['fl_adiantamento'], $_POST['vl_adiantamento'], $_POST['vl_contrib_esporadica'], 
			$_POST['fl_dt_vitalicio_1'], $_POST['dt_vitalicio_1'], $_POST['sexo_vitalicio_1'], 
			$_POST['fl_dt_vitalicio_2'], $_POST['dt_vitalicio_2'], $_POST['sexo_vitalicio_2'], 
			$_POST['fl_dt_vitalicio_3'], $_POST['dt_vitalicio_3'], $_POST['sexo_vitalicio_3'], 
			$_POST['fl_dt_vitalicio_4'], $_POST['dt_vitalicio_4'], $_POST['sexo_vitalicio_4'], 
			$_POST['fl_autopatrocinio_pagar_patroc']
			);
		}
		
		if($_POST['ds_funcao'] == "buscaMesesApos")
		{
			buscaMesesApos($_POST['dt_nascimento'],$_POST['dt_simulacao'],$_POST['dt_ingresso'],$_POST['nr_idade_apos']);
		}

		#print_r($_POST);
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
			if($nr_conta < 50)
			{
				$nr_conta = 50;
			}
			$nr_fim = 59;
		}

		if($tp_aposentadoria == 2)
		{
			$nr_fim = 49;
		}
		
		if(($nr_conta > $nr_fim) and ($tp_aposentadoria == 1))
		{
			#echo "ERRO_1 [if(($nr_conta > $nr_fim) and ($tp_aposentadoria == 1))]";
			
			echo '
				<select name="nr_idade_apos" id="nr_idade_apos"  class="form_simulacao_select" onchange="buscaMesesApos(this.value);novaSimulacao();">				
					<option value="0">Não é possível</option>
				</select>
				';
		}
		
		else if(($nr_conta > $nr_fim) and ($tp_aposentadoria == 2))
		{
			#echo "ERRO_2 [else if(($nr_conta > $nr_fim) and ($tp_aposentadoria == 2))]";
			
			echo '
				<select name="nr_idade_apos" id="nr_idade_apos"  class="form_simulacao_select" onchange="buscaMesesApos(this.value);novaSimulacao();">				
					<option value="0">Não é possível</option>
				</select>
				';			
		}
			
		else
		{
			echo '
				<select name="nr_idade_apos" id="nr_idade_apos"  class="form_simulacao_select" onchange="buscaMesesApos(this.value);novaSimulacao();">				
					<option value="0">Selecione</option>
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
							TO_CHAR(CASE WHEN (CASE WHEN (TO_DATE('".$dt_nascimento."','DD/MM/YYYY')+ '45 years'::interval) > (TO_DATE('".$dt_simulacao."','DD/MM/YYYY') + '10 year'::interval)
	                                               THEN (TO_DATE('".$dt_nascimento."','DD/MM/YYYY')+ '45 years'::interval)
		                                           ELSE (TO_DATE('".$dt_simulacao."','DD/MM/YYYY') + '10 years'::interval)
	                                          END) > (TO_DATE('".$dt_nascimento."','DD/MM/YYYY')+ '".$nr_idade_apos." years'::interval)
                                        THEN (CASE WHEN (TO_DATE('".$dt_nascimento."','DD/MM/YYYY')+ '45 years'::interval) > (TO_DATE('".$dt_simulacao."','DD/MM/YYYY') + '10 year'::interval)
			                                       THEN (TO_DATE('".$dt_nascimento."','DD/MM/YYYY')+ '45 years'::interval)
			                                       ELSE (TO_DATE('".$dt_simulacao."','DD/MM/YYYY') + '10 years'::interval)
		                                      END)	
                                        ELSE TO_DATE('".$dt_nascimento."','DD/MM/YYYY')+ '".$nr_idade_apos." years'::interval
							       END,'DD/MM/YYYY') AS dt_sol_apos_espec,								   
					       TO_CHAR((TO_DATE('".$dt_nascimento."','DD/MM/YYYY')+ '50 years'::interval),'DD/MM/YYYY') AS dt_min_apos,
					       TO_CHAR((TO_DATE('".$dt_simulacao."','DD/MM/YYYY')+ '10 years'::interval),'DD/MM/YYYY') AS dt_min_plano,
					       TO_CHAR(CASE WHEN (TO_DATE('".$dt_nascimento."','DD/MM/YYYY')+ '60 years'::interval) > (TO_DATE('".$dt_simulacao."','DD/MM/YYYY') + '10 year'::interval)
					                    THEN (TO_DATE('".$dt_nascimento."','DD/MM/YYYY')+ '60 years'::interval)
					                    ELSE (TO_DATE('".$dt_simulacao."','DD/MM/YYYY') + '10 years'::interval)
					               END,'DD/MM/YYYY') AS dt_ref_apos,
					       TO_CHAR(CASE WHEN (CASE WHEN (TO_DATE('".$dt_nascimento."','DD/MM/YYYY')+ '45 years'::interval) > (TO_DATE('".$dt_simulacao."','DD/MM/YYYY') + '10 year'::interval)
					                               THEN (TO_DATE('".$dt_nascimento."','DD/MM/YYYY')+ '45 years'::interval)
					                               ELSE (TO_DATE('".$dt_simulacao."','DD/MM/YYYY') + '10 years'::interval)
					                          END) > CURRENT_DATE
										THEN (CASE WHEN (TO_DATE('".$dt_nascimento."','DD/MM/YYYY')+ '45 years'::interval) > (TO_DATE('".$dt_simulacao."','DD/MM/YYYY') + '10 year'::interval)
					                               THEN (TO_DATE('".$dt_nascimento."','DD/MM/YYYY')+ '45 years'::interval)
					                               ELSE (TO_DATE('".$dt_simulacao."','DD/MM/YYYY') + '10 years'::interval)
					                          END)
										ELSE CURRENT_DATE
								   END,'DD/MM/YYYY') AS dt_referencia

								   
	              ";
		$ob_resul = pg_query($db, $qr_sql);
		$ar_reg   = pg_fetch_array($ob_resul);
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


	function calculaBeneficio($cd_empresa, $cd_registro_empregado, $seq_dependencia, $dt_ingresso, $dt_simulacao,$dt_nascimento,$sexo_titular, $tp_aposentadoria,$nr_idade_apos,$vl_salario,$vl_salario_perc,$vl_rentabilidade,$dt_vitalicio,$dt_temporario, $fl_contribuicao_voluntaria, $vl_contribuicao_voluntaria, $qt_contribuicao_voluntaria, $fl_adiantamento, $vl_adiantamento, $vl_contrib_esporadica, 	$fl_dt_vitalicio_1, $dt_vitalicio_1, $sexo_vitalicio_1,	$fl_dt_vitalicio_2, $dt_vitalicio_2, $sexo_vitalicio_2,	$fl_dt_vitalicio_3, $dt_vitalicio_3, $sexo_vitalicio_3,	$fl_dt_vitalicio_4, $dt_vitalicio_4, $sexo_vitalicio_4,	$fl_autopatrocinio_pagar_patroc)
	{
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
			
		
		$ar_datas       = calculaDatas($dt_nascimento, $dt_ingresso,$nr_idade_apos);
		echo ($debug ? "<PRE>".print_r($ar_datas,true)."</PRE>" : "");
	
		$dt_sol_apos       = $ar_datas['dt_sol_apos'];
		$dt_referencia     = $ar_datas['dt_referencia'];
		$dt_ref_apos       = $ar_datas['dt_ref_apos'];
		$dt_sol_apos_espec = $ar_datas['dt_sol_apos_espec'];
		
		if($tp_aposentadoria == 1)
		{
			$dt_ref_apos = $dt_sol_apos;
		}
		
		echo ($debug ?  "<BR>DT INGRESSO => ".$dt_ingresso : "");
		/*
		echo ($debug ?  "<BR>DT VITALICIO => ".$dt_vitalicio : "");
		echo ($debug ?  "<BR>DT TEMPORARIO => ".$dt_temporario : "");
		*/

		if($tp_aposentadoria == 2)
		{
			$dt_ref_apos = $dt_sol_apos_espec;
			$dt_sol_apos = $dt_sol_apos_espec;
		}		
		
		$dt_min_apos    = $ar_datas['dt_min_apos'];
		$dt_min_plano   = $ar_datas['dt_min_plano'];
		
		$qt_meses_ref   = calculaMeses($dt_simulacao, $dt_ref_apos);
		if($qt_meses_ref < 0)
		{
			$qt_meses_ref = 0;
			$dt_ref_apos   = $dt_simulacao;
		}		
		
		$qt_meses_apos  = calculaMeses($dt_simulacao, $dt_sol_apos);
		if($qt_meses_apos < 0)
		{
			$qt_meses_apos = 0;
			$dt_sol_apos   = $dt_simulacao;
		}		
		
		$qt_meses       = calculaMeses($dt_nascimento, $dt_simulacao);
		$nr_idade_atual = ($qt_meses / 12);
		
		echo ($debug ?  "<br>DT REF => ".$dt_ref_apos : "");
		echo ($debug ?  "<br>DT APOS => ".$dt_sol_apos : "");
		echo ($debug ?  "<br>QT MES REF => ".$qt_meses_ref : "");
		echo ($debug ?  "<br>QT MES APOS => ".$qt_meses_apos : "");
		echo ($debug ?  "<br>QT MES => ".$qt_meses : "");
		echo ($debug ?  "<br>NR IDADE => ".$nr_idade_atual : "");
		
		#### CONTRIBUIÇÕES ####
		$vl_contribuicao_proga = calculaContribuicao($vl_salario,$vl_salario_perc);
		$vl_contribuicao_risco = calculaContribuicaoRisco($vl_salario,($vl_salario_perc - 2));
		$vl_contribuicao_admin = calculaContribuicaoAdmin(($vl_contribuicao_proga+$vl_contribuicao_risco),($vl_salario_perc - 2));

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
		
		echo ($debug ?  "<br>CONTRIB PROGRAMADA => ".$vl_contribuicao_proga : "");
		echo ($debug ?  "<br>CONTRIB RISCO => ".$vl_contribuicao_risco : "");
		echo ($debug ?  "<br>CONTRIB ADM => ".$vl_contribuicao_admin : "");		
		
		#### CIP ####
		echo ($debug ?  "<br>CIP CALC CONTRIB => ".$vl_contribuicao_proga : "");
		echo ($debug ?  "<br>CIP CALC RENTAB => ".$vl_rentabilidade : "");
		echo ($debug ?  "<br>CIP CALC MESES => ".$qt_meses_apos : "");
		$vl_cip_participante  = calculaCIP($vl_contribuicao_proga,$vl_rentabilidade,$qt_meses_apos);
		echo ($debug ?  "<br>CIP PARTIC => ".$vl_cip_participante : "");
		
		#$vl_cip_patrocinadora = calculaCIP($vl_contribuicao_proga,$vl_rentabilidade,$qt_meses_ref);
		$vl_cip_patrocinadora = ($fl_checkAutoPatrocinioSemPatrocinadora == "S" ? 0 : calculaCIP($vl_contribuicao_proga,$vl_rentabilidade,$qt_meses_ref));
		echo ($debug ?  "<br>CIP PATROC => ".$vl_cip_patrocinadora : "");
		

		$vl_cip_patrocinadora = atualizaCIPAtual(($qt_meses_apos - $qt_meses_ref),$vl_cip_patrocinadora,$vl_rentabilidade);
		echo ($debug ?  "<br>CIP QT MES ATUAL (qt_meses_apos - qt_meses_ref) => ".($qt_meses_apos - $qt_meses_ref) : "");
		echo ($debug ?  "<br>CIP PATROC ATUAL => ".$vl_cip_patrocinadora : "");
		
		$fl_BPD = checkBPD($cd_empresa, $cd_registro_empregado, $seq_dependencia);
		echo ($debug ?  "<br>É BPD => ".$fl_BPD : "");
		
		if($fl_BPD == "S")
		{
			#### BPD NÃO TEM PROJECAO ####
			$vl_cip_participante  = 0;
			$vl_cip_patrocinadora = 0;
		}	
		
		$vl_cip = ($vl_cip_participante + $vl_cip_patrocinadora);	
		echo ($debug ?  "<br>CIP PROJETADA => ".$vl_cip : "");
		
		
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

		#### CONTRIBUICAO ESPORADICA ####
		$vl_saldo_esporadica = 0;

		if(floatval($vl_contrib_esporadica) > 0)
		{
			$vl_saldo_esporadica = calculaContribuicaoEsporadica($qt_meses_apos, $vl_contrib_esporadica, $vl_rentabilidade);

			echo ($debug ?  "<BR> SALDO ESPORADICA => ".$vl_saldo_esporadica : "");
		}
		#### CONTRIBUICAO ESPORADICA ####
		
		#### BUSCA CIP ATUAL ####
		$qt_cota = calculaCIPAtual($cd_empresa, $cd_registro_empregado, $seq_dependencia, $dt_ingresso);
		$vl_cota = getValorCota();
		$vl_cip_atual = $qt_cota * getValorCota();
		echo ($debug ?  "<br>QT COTAS => ".$qt_cota : "");
		echo ($debug ?  "<br>VL COTAS => ".$vl_cota : "");
		echo ($debug ?  "<br>CIP ATUAL => ".$vl_cip_atual : "");

		$vl_cip_atual = atualizaCIPAtual($qt_meses_apos,$vl_cip_atual,$vl_rentabilidade);
		echo ($debug ?  "<BR>CIP ATUAL CORRIGIDA => ".$vl_cip_atual : "");
		
		
		#### SOMAS AS CIP's #####
		$vl_cip = ($vl_cip + $vl_cip_atual + $vl_saldo_voluntaria + $vl_saldo_esporadica);	
		echo ($debug ?  "<BR>CIP TOTAL => ".$vl_cip : "");
		#echo "<BR>";	

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
		
		#### BENEFICIO ####
		/*
		if(($dt_vitalicio == "") and ($dt_temporario == ""))
		{
			#echo "1) ";
			$vl_fa = getFatorAtuarial('AX',$nr_idade_apos,0);
		}
		else if(($dt_vitalicio != "") and ($dt_temporario == ""))
		{
			#echo "2) ";
			$qt_idade_vitalicio = calculaMeses($dt_vitalicio, $dt_sol_apos);
			$nr_idade_vitalicio = (int)($qt_idade_vitalicio / 12);
			$vl_fa = getFatorAtuarial('AX' ,$nr_idade_apos,0);
			$vl_fa+= getFatorAtuarial('AXY',$nr_idade_apos,$nr_idade_vitalicio);
		}
		else if(($dt_vitalicio == "") and ($dt_temporario != ""))
		{
			#echo "3) ";
			$qt_idade_dependente = calculaMeses($dt_temporario, $dt_sol_apos);
			$nr_idade_dependente = (int)($qt_idade_dependente / 12);
			$vl_fa = getFatorAtuarial('AX' ,$nr_idade_apos,0);
			if( $nr_idade_dependente < 21)
			{
				$vl_fa+= getFatorAtuarial('AXZ',$nr_idade_apos,$nr_idade_dependente);
			}
			
		}	
		else if(($dt_vitalicio != "") and ($dt_temporario != ""))
		{
			#echo "4) ";
			$qt_idade_vitalicio = calculaMeses($dt_vitalicio, $dt_sol_apos);
			$nr_idade_vitalicio = (int)($qt_idade_vitalicio / 12);	
			$qt_idade_dependente = calculaMeses($dt_temporario, $dt_sol_apos);
			$nr_idade_dependente = (int)($qt_idade_dependente / 12);	
			
			$vl_fa = getFatorAtuarial('AX' ,$nr_idade_apos,0);
			if( $nr_idade_dependente < 21)
			{
				$vl_fa+= getFatorAtuarial('AXZ',$nr_idade_apos,$nr_idade_dependente);
				$vl_fa+= getFatorAtuarial('AXY',$nr_idade_apos,$nr_idade_vitalicio) * ((getFatorAtuarial('DX',(($nr_idade_vitalicio + 21) - $nr_idade_dependente),0))/getFatorAtuarial('DX',$nr_idade_vitalicio,0));
			}
			else
			{
				$vl_fa+= getFatorAtuarial('AXY',$nr_idade_apos,$nr_idade_vitalicio);
			}
		}
		*/

		echo ($debug ?  "<BR> ########### FATOR ATUARIAL ############ " : "");
		echo ($debug ?  "<BR>TITULAR IDADE => ".$nr_idade_apos : "");
		echo ($debug ?  "<BR>TITULAR SEXO => ".$sexo_titular : "");

		echo ($debug ?  "<BR>DEP 1 USAR => ".$fl_dt_vitalicio_1 : "");
		echo ($debug ?  "<BR>DEP 1 DATA => ".$dt_vitalicio_1 : "");
		echo ($debug ?  "<BR>DEP 1 SEXO => ".$sexo_vitalicio_1 : "");
		
		echo ($debug ?  "<BR>DEP 2 USAR => ".$fl_dt_vitalicio_2 : "");
		echo ($debug ?  "<BR>DEP 2 DATA => ".$dt_vitalicio_2 : "");
		echo ($debug ?  "<BR>DEP 2 SEXO => ".$sexo_vitalicio_2 : "");
		
		echo ($debug ?  "<BR>DEP 3 USAR => ".$fl_dt_vitalicio_3 : "");
		echo ($debug ?  "<BR>DEP 3 DATA => ".$dt_vitalicio_3 : "");
		echo ($debug ?  "<BR>DEP 3 SEXO => ".$sexo_vitalicio_3 : "");	

		echo ($debug ?  "<BR>DEP 4 USAR => ".$fl_dt_vitalicio_4 : "");
		echo ($debug ?  "<BR>DEP 4 DATA => ".$dt_vitalicio_4 : "");
		echo ($debug ?  "<BR>DEP 4 SEXO => ".$sexo_vitalicio_4 : "");		

		
		#$vl_fa = getFatorAtuarial('AX',$nr_idade_apos,0);
		$vl_fa = getFatorAtuarial('AX',$nr_idade_apos,$sexo_titular,0,"");
		
		echo ($debug ?  "<BR>vl_fa AX => ".getFatorAtuarial('AX',$nr_idade_apos,$sexo_titular,0,"") : "");	

		$arr_dep = array();

		if($fl_dt_vitalicio_1 == 'S')
		{
			#$arr_dep[] = $dt_vitalicio_1;
			$arr_dep[] = array($dt_vitalicio_1,$sexo_vitalicio_1);
		}

		if($fl_dt_vitalicio_2 == 'S')
		{
			#$arr_dep[] = $dt_vitalicio_2;
			$arr_dep[] = array($dt_vitalicio_2,$sexo_vitalicio_2);
		}

		if($fl_dt_vitalicio_3 == 'S')
		{
			#$arr_dep[] = $dt_vitalicio_3;
			$arr_dep[] = array($dt_vitalicio_3,$sexo_vitalicio_3);
		}

		if($fl_dt_vitalicio_4 == 'S')
		{
			#$arr_dep[] = $dt_vitalicio_4;
			$arr_dep[] = array($dt_vitalicio_4,$sexo_vitalicio_4);
		}

		if(count($arr_dep) > 0)
		{
			global $db;

			$ar_dt_vitalicio = array();
			foreach($arr_dep as $item)
			{
				$ar_dt_vitalicio[] = "(TO_DATE('".$item[0]."', 'DD/MM/YYYY'), '".$item[1]."')";
			}
			
			$qr_sql = "
					SELECT TO_CHAR(x.column1, 'DD/MM/YYYY') AS dt_dep,
					       x.column2 AS sx_dep
				      FROM (VALUES ".implode(",", $ar_dt_vitalicio).") x
				     ORDER BY X.column1 DESC
				     LIMIT 1;";			
			#echo ($debug ?  "<BR>QR SQL DEPENDENTE => ".$qr_sql : "");
			$ob_resul = pg_query($db, $qr_sql);
			$ar_reg   = pg_fetch_array($ob_resul);

			echo ($debug ?  "<BR>PENSAO DATA => ".$ar_reg['dt_dep'] : "");
			echo ($debug ?  "<BR>PENSAO SEXO => ".$ar_reg['sx_dep'] : "");

			$qt_idade_vitalicio = calculaMeses($ar_reg['dt_dep'], $dt_sol_apos);
			$nr_idade_vitalicio = (int)($qt_idade_vitalicio / 12);
			
			echo ($debug ?  "<BR>PENSAO IDADE => ".$nr_idade_vitalicio : "");

			#$vl_fa+= getFatorAtuarial('AXY',$nr_idade_apos,$nr_idade_vitalicio);
			$vl_fa+= getFatorAtuarial('AXY',$nr_idade_apos,$sexo_titular,$nr_idade_vitalicio,$ar_reg['sx_dep']);
			
			echo ($debug ?  "<BR>vl_fa AXY => ".getFatorAtuarial('AXY',$nr_idade_apos,$sexo_titular,$nr_idade_vitalicio,$ar_reg['sx_dep']) : "");
		}


		echo ($debug ?  "<BR>FA => ".$vl_fa : "");
		
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
											<div class='contrib_valor'>R$ ".number_format(round(($vl_contribuicao_proga),2),2,',','.')." </div>
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
											<div class='contrib_valor'>R$ ".number_format(round(($vl_contribuicao_risco),2),2,',','.')."</div>
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
											<div class='contrib_valor'>R$ ".number_format(round(($vl_contribuicao_admin),2),2,',','.')."</div>
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
											<div class='contrib_valor_maior'>R$ ".number_format(round(($vl_contribuicao_proga+$vl_contribuicao_risco+$vl_contribuicao_admin),2),2,',','.')."</div>
										</td>
									</tr>								
								</table>
							</div>			
						</td>					
					</tr>
				</table>";
		}

		echo "
			<BR>
			<table border='0' width='100%' align='center'> 
				<tr>";

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
					</td>
			";
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
							<div class='saldo_acum_titulo'>Saldo Acumulado</div>
							<br/>
							<div class='saldo_acum_valor'>R$ ".number_format(round(($vl_cip),2),2,',','.')."</div>
						</div>			
					</td>";

			if(round(($vl_cip/$vl_fa),2) > getUPCEEE())
			{
				echo "
					<td align='center'>
						<div class='ben_inicial'>
							<div class='ben_inicial_titulo'>Benefício Inicial</div>
							<br/>
							<div class='ben_inicial_valor'>R$ ".number_format(round(($vl_cip/$vl_fa),2),2,',','.')."</div>
						</div>
					</td>
		     ";		
			}
			else
			{
				echo "<center><span style='font-weight:bold; font-size:18px;'>Este valor de saldo projetado é insuficiente para gerar benefício de aposentadoria mensal, pois seu valor seria inferior a 1 URCRM.<br/>
Nestes casos o pagamento do benefício será realizado de uma única vez, sem recebimento mensal.<br/>
Altere os parâmetros da simulação para que seu benefício fique igual ou superior ao valor da URCRM.</span></center>";
					echo "<BR><BR>";
			}
			
		 }

		 echo 	"</tr>
			</table>
			<BR>";


		if($fl_mensagem_adiantamento)
		{
			echo "<center><span style='color:red; font-weight:bold; font-size:18px;'>Saldo remanescente não é suficiente para um benefício superior a 1 URCRM. Diminua o percentual do adiantamento.</span></center>";
			echo "<BR><BR>";
		}
		
		echo "</div>";
	}
	
	


	function calculaCIPAtual($cd_empresa, $cd_registro_empregado, $seq_dependencia, $dt_ingresso)
	{	
		global $db;
		/*
		$qr_sql = "
					SELECT (COALESCE(qt_cip,0) +  COALESCE(qt_cpi,0) + COALESCE(qt_port,0)) AS qt_cota
					  FROM (SELECT fnc_saldo_cd_contas::NUMERIC AS qt_cip 
					          FROM oracle.fnc_saldo_cd_contas(6, ".$cd_empresa.", ".$cd_registro_empregado.", ".$seq_dependencia.", '".$dt_ingresso."', 11)) a,
					       (SELECT fnc_saldo_cd_contas::NUMERIC AS qt_cpi 
					          FROM oracle.fnc_saldo_cd_contas(6, ".$cd_empresa.", ".$cd_registro_empregado.", ".$seq_dependencia.", '".$dt_ingresso."', 1301)) b,
					       (SELECT fnc_saldo_cd_contas::NUMERIC AS qt_port 
					          FROM oracle.fnc_saldo_cd_contas(6, ".$cd_empresa.", ".$cd_registro_empregado.", ".$seq_dependencia.", '".$dt_ingresso."', 1401)) c					
	              ";
		*/		  
		$qr_sql = "
					SELECT crmprev_cip_quota AS qt_cota
					  FROM simulador.crmprev_cip_quota(".$cd_empresa.", ".$cd_registro_empregado.", ".$seq_dependencia.", TO_DATE('".$dt_ingresso."','DD/MM/YYYY'));
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

	function calculaContribuicaoEsporadica($qt_meses,$vl_cip,$vl_rentabilidade)
	{
		$vl_cip = (pow((1.000000000 + $vl_rentabilidade), $qt_meses)) * $vl_cip;
		return $vl_cip;
	}		

	function calculaCIP($vl_contribuicao,$vl_rentabilidade,$qt_meses)
	{
		$vl_a = ((pow((1.000000000 + $vl_rentabilidade), $qt_meses)) - 1);
		#echo "<br>CIP: 1) vl_a => ".$vl_a;
		
		$vl_b = ($vl_a / $vl_rentabilidade);
		#echo "<br>CIP: 2) vl_b => ".$vl_b;
		
		$vl_c = (13/12);
		#echo "<br>CIP: 3) vl_c => ".$vl_c;		
		
		$vl_cip = $vl_b * ($vl_contribuicao * $vl_c);
		#echo "<br>CIP: 4) vl_cip => ".$vl_cip;
		
		return $vl_cip;
	}
	
	function calculaContribuicao($vl_salario,$vl_salario_perc)
	{
		$vl_contribuicao = ($vl_salario * $vl_salario_perc) / 100;
		
		return round($vl_contribuicao,2);
	}	
	
	function calculaContribuicaoRisco($vl_salario,$nr_faixa)
	{
		$vl_contribuicao = ($vl_salario * getFaixaValor($nr_faixa,'risco'));
		
		return round($vl_contribuicao,2);
	}
	
	function calculaContribuicaoAdmin($vl_salario,$nr_faixa)
	{
		$vl_contribuicao = ($vl_salario * getFaixaValor($nr_faixa,'adm'));
		return round($vl_contribuicao,2);
	}

	function getFaixaValor($nr_faixa,$tp_faixa)
	{
		global $db;
		$qr_sql = "
					SELECT num_faixa, taxa_basica, taxa_upceee, taxa_risco, taxa_risco_upceee, taxa_adm
					  FROM public.faixas_contrib_planos f
					 WHERE f.cd_empresa      = 6
					   AND f.cd_plano        = 6
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
				        AND COALESCE(fab.sexo_dependente,'') = UPPER(TRIM('".trim($sexo_dependente)."'))						
						AND fab.cd_empresa                   = 6
			            AND fab.cd_plano                     = 6 
					 ORDER BY fab.dt_inicio DESC
				     LIMIT 1
	              ";
		$ob_resul = pg_query($db, $qr_sql);
		$ar_reg   = pg_fetch_array($ob_resul);
		return $ar_reg['indice'];		
	}
	
	/*
	function getFatorAtuarial($cd_fa,$nr_idade_titular,$nr_idade_dependente)
	{
		global $db;
		$ar_retorno = Array();
		$qr_sql = "
			         SELECT fab.indice
			           FROM fatores_atuariais_beneficios fab
			          WHERE fab.cd_fa                   = '".$cd_fa."' 
			            AND fab.indicador_a             = TRUNC(".$nr_idade_titular.")
				        AND COALESCE(fab.indicador_b,0) = TRUNC(".$nr_idade_dependente.")
			            AND fab.cd_empresa = 6
			            AND fab.cd_plano   = 6 
						AND fab.dt_inicio < '2022-01-01'
					  ORDER BY fab.dt_inicio DESC
				      LIMIT 1
	              ";
		$ob_resul = pg_query($db, $qr_sql);
		$ar_reg   = pg_fetch_array($ob_resul);
		return $ar_reg['indice'];		
	}
	*/

	function getValorCota()
	{
		global $db;
		$qr_sql = "
					SELECT vlr_indice 
					  FROM public.indices  
					 WHERE cd_indexador = 74
					 ORDER BY dt_indice DESC
					 LIMIT 1						
	              ";
		$ob_resul = pg_query($db, $qr_sql);
		$ar_reg   = pg_fetch_array($ob_resul);
		return $ar_reg['vlr_indice'];		

	}

	function getUPCEEE()
	{
		global $db;
		$qr_sql = "
					SELECT vlr_taxa
					  FROM public.taxas
					 WHERE cd_indexador = 84
					 ORDER BY dt_taxa DESC
					 LIMIT 1						
	              ";
		$ob_resul = pg_query($db, $qr_sql);
		$ar_reg   = pg_fetch_array($ob_resul);
		return $ar_reg['vlr_taxa'];		

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
		
		/*
		$qr_sql = "
			        SELECT COUNT(*) AS fl_auto_sem_patroc
					  FROM public.afastados a
					 WHERE a.cd_empresa            = ".$cd_empresa."
					   AND a.cd_registro_empregado = ".$cd_registro_empregado."
					   AND a.seq_dependencia       = ".$seq_dependencia."
					   AND a.tipo_afastamento      = 67 -- AUTOPATROCINIO
					   AND a.id_tipo_contribuicao  = 5  -- CONTRIB NORMAL SEM PATROCINADORA
					   AND ((a.dt_final_afastamento IS NULL) OR (DATE_TRUNC('month', a.dt_final_afastamento) >= DATE_TRUNC('month',CURRENT_DATE)))
	              ";
		*/		  
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
?>