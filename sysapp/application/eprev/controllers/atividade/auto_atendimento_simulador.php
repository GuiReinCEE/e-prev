<?php
	$_APP_ID  = "e7a9e3f647dd33941430647118aaf2b7";
	
	ini_set('max_execution_time', 0);

	if((preg_match('/10.63./',$_SERVER['REMOTE_ADDR'])) and ($_REQUEST['EMP'] != "") and ($_REQUEST['RE'] != "") and ($_REQUEST['SEQ'] != ""))
	{
			session_start();
			$_SESSION['SID']      = 0;
			$_SESSION['EMP']      = $_REQUEST['EMP'];
			$_SESSION['RE']       = $_REQUEST['RE'];
			$_SESSION['SEQ']      = $_REQUEST['SEQ'];
			$_SESSION['USER_MD5'] = $_REQUEST['USER_MD5'];
			$fl_menu = "
						<STYLE>
							#menu_primeiro_nivel { display: none;  width:0px; height:0px;}
							#menu_segundo_nivel { display: none;  width:0px; height:0px;}
							#bloco_contato { display: none;  width:0px; height:0px;}	
						</STYLE>
			           ";
	}
	else
	{
		include_once('inc/sessao_auto_atendimento.php');
	}
	
	include_once('inc/conexao.php');
	require_once('inc/ajaxobject.php');
	include_once('inc/class.TemplatePower.inc.php');
	$tpl = new TemplatePower('tpl/tpl_auto_atendimento.html');
	$tpl->prepare();
	include_once('auto_atendimento_monta_sessao.php');
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
					   'SIMULADOR_BENEFICIO'
					 )
			  ";
	@pg_query($db,$qr_sql);	
   
   
	#### BUSCA PLANO DO PARTICIPANTE ####
	$qr_sql = "
				SELECT tp.cd_registro_empregado,
				       CASE WHEN tp.dt_migracao IS NOT NULL 
					        THEN 'S'
							ELSE 'N'
					   END AS fl_migrado, 
					   CASE WHEN projetos.participante_tipo(t.cd_empresa, t.cd_registro_empregado, t.seq_dependencia) IN ('ATIV', 'AUXD') 
					        THEN 'S'
					        ELSE 'N'
					   END AS fl_ativo,
				       tp.cd_plano,
				       CASE WHEN tp.dt_migracao IS NOT NULL 
					        THEN TO_CHAR(t.dt_ingresso_eletro,'DD/MM/YYYY') 
							ELSE TO_CHAR(tp.dt_ingresso_plano,'DD/MM/YYYY') 
					   END AS dt_ingresso_plano,					   
					   TO_CHAR(tp.dt_migracao,'DD/MM/YYYY') AS dt_migracao,
					   TO_CHAR(p.dt_nascimento,'DD/MM/YYYY') AS dt_nascimento,
					   p.sexo,
					   TO_CHAR(CURRENT_DATE,'DD/MM/YYYY') AS dt_simulacao,
					   (SELECT CASE WHEN COUNT(*) > 0 
					                THEN 'S'
								    ELSE 'N'
					           END AS fl_bpd
					      FROM public.afastados a
					     WHERE a.cd_empresa            = p.cd_empresa
					       AND a.cd_registro_empregado = p.cd_registro_empregado
					       AND a.seq_dependencia       = p.seq_dependencia
					       AND a.tipo_afastamento      = 72 -- BPD
					       AND ((a.dt_final_afastamento IS NULL) OR (DATE_TRUNC('month', a.dt_final_afastamento) >= DATE_TRUNC('month',CURRENT_DATE)))) AS fl_bpd,
					   (SELECT auto_patrocinio_com_patroc 
					      FROM simulador.auto_patrocinio_com_patroc(p.cd_empresa,p.cd_registro_empregado,p.seq_dependencia)) AS fl_autopatrocinio_com_patroc,
					   (SELECT (calcula_meses / 12)::integer 
			              FROM simulador.calcula_meses(
			                    p.dt_nascimento::date,
			                    CURRENT_DATE
			              )) AS nr_idade	   
 				  FROM public.titulares_planos tp
				  JOIN public.titulares t
				    ON t.cd_empresa            = tp.cd_empresa            
				   AND t.cd_registro_empregado = tp.cd_registro_empregado 
				   AND t.seq_dependencia       = tp.seq_dependencia 				  
				  JOIN public.participantes p
				    ON p.cd_empresa            = tp.cd_empresa            
				   AND p.cd_registro_empregado = tp.cd_registro_empregado 
				   AND p.seq_dependencia       = tp.seq_dependencia       
				 WHERE tp.cd_empresa            = ".$_SESSION['EMP']."
				   AND tp.cd_registro_empregado = ".$_SESSION['RE']."
				   AND tp.seq_dependencia       = ".$_SESSION['SEQ']."
				   AND tp.dt_deslig_plano       IS NULL
				   AND tp.dt_ingresso_plano     = (SELECT MAX(tp1.dt_ingresso_plano)
					                                 FROM public.titulares_planos tp1 
					                                WHERE tp1.cd_empresa            = tp.cd_empresa 
					                                  AND tp1.cd_registro_empregado = tp.cd_registro_empregado 
					                                  AND tp1.seq_dependencia       = tp.seq_dependencia)
              ";         
	$ob_resul = pg_query($db,$qr_sql);
	$ar_plano = pg_fetch_array($ob_resul);
	
	if(pg_num_rows($ob_resul) == 0) 
	{
		$conteudo = "
						<br><br><br>
						<center>
							<h1 style='font-family: Calibri, Arial; font-size: 15pt;'>Somente ATIVOS podem simular.</h1>
						</center>
						<br><br><br>
					";	
	}	
	else
	{
		if(($ar_plano['cd_plano'] == 2) and ($ar_plano['fl_ativo'] == "S")) 
		{
			if ($ar_plano['fl_migrado'] == "S")
			{
				#### CEEEPREV MIGRADOS ####
				$ds_arq   = "tpl/tpl_auto_atendimento_simulador_ceeeprev_migrado.html";	
			}
			else
			{
				#### CEEEPREV NOVOS ####
				$ds_arq   = "tpl/tpl_auto_atendimento_simulador_ceeeprev.html";
			}		
			
			$ob_arq   = fopen($ds_arq, 'r');
			$conteudo = fread($ob_arq, filesize($ds_arq));
			fclose($ob_arq);			
			
			
			#### BUSCA DEPENDENTES VITALICIO E TEMPORÁRIO ####
			/*
			$qr_sql = "
						SELECT CASE WHEN TRIM((funcoes.pg_split(prc_calcula_idades_dependentes, '|'))[1]) IS NOT NULL AND TRIM((funcoes.pg_split(prc_calcula_idades_dependentes, '|'))[1]) <> ''
									THEN TO_CHAR(CAST(TRIM((funcoes.pg_split(prc_calcula_idades_dependentes, '|'))[1]) AS DATE),'DD/MM/YYYY')
									ELSE NULL
							   END AS dt_vitalicio,
							   CASE WHEN TRIM((funcoes.pg_split(prc_calcula_idades_dependentes, '|'))[1]) IS NOT NULL AND TRIM((funcoes.pg_split(prc_calcula_idades_dependentes, '|'))[1]) <> ''
									THEN 'readonly'
									ELSE NULL
							   END AS fl_vitalicio,
							   CASE WHEN TRIM((funcoes.pg_split(prc_calcula_idades_dependentes, '|'))[2]) IS NOT NULL AND TRIM((funcoes.pg_split(prc_calcula_idades_dependentes, '|'))[2]) <> ''
									THEN TO_CHAR(CAST(TRIM((funcoes.pg_split(prc_calcula_idades_dependentes, '|'))[2]) AS DATE),'DD/MM/YYYY')
									ELSE NULL
							   END AS dt_temporario,
							   CASE WHEN TRIM((funcoes.pg_split(prc_calcula_idades_dependentes, '|'))[2]) IS NOT NULL AND TRIM((funcoes.pg_split(prc_calcula_idades_dependentes, '|'))[2]) <> ''
									THEN 'readonly'
									ELSE NULL
							   END AS fl_temporario
						  FROM oracle.prc_calcula_idades_dependentes(".$_SESSION['EMP'].", ".$_SESSION['RE'].", ".$_SESSION['SEQ'].", TO_CHAR(CURRENT_DATE, 'DD/MM/YYYY'))
					  ";
			*/		

			$qr_sql = "
						SELECT TO_CHAR(dt_vitalicio,'DD/MM/YYYY') AS dt_vitalicio,
							   (CASE WHEN dt_vitalicio IS NOT NULL THEN 'readonly' ELSE NULL END) AS fl_vitalicio,
							   sexo_vitalicio,							   
							   TO_CHAR(dt_temporario,'DD/MM/YYYY') AS dt_temporario,		
							   (CASE WHEN dt_temporario IS NOT NULL THEN 'readonly' ELSE NULL END) AS fl_temporario,
							   sexo_temporario
						  FROM simulador.ceeeprev_dependente(".$_SESSION['EMP'].", ".$_SESSION['RE'].", ".$_SESSION['SEQ'].", TO_CHAR(CURRENT_DATE, 'DD/MM/YYYY'))
					  ";			
			$ob_resul      = pg_query($db,$qr_sql);
			$ar_dependente = pg_fetch_array($ob_resul);

			#### BUSCA IPs DOS ATENDENTES ####
			$qr_sql = "
				SELECT nr_ip_callcenter
				  FROM projetos.usuarios_controledi
				 WHERE divisao = 'GP'
				   AND nr_ip_callcenter IS NOT NULL
				   AND tipo != 'X';";

			$ob_resul      = pg_query($db,$qr_sql);

			$ar_ip = array(
				'10.63.4.150',
				'10.63.4.102',
				'10.63.4.87'
			);

			while ($ar_reg = pg_fetch_array($ob_resul))
			{
				$ar_ip[] = $ar_reg['nr_ip_callcenter'];
			}
		
			#### CRISTIANO | DENIS | ATENDENTES (OS: 44258) ####
			if(in_array($_SERVER['REMOTE_ADDR'], $ar_ip)) 
			{
				if($ar_dependente['fl_vitalicio'] != "")
				{
					$ar_dependente['fl_vitalicio'] = "";
				}
				
				if($ar_dependente['fl_temporario'] != "")
				{
					$ar_dependente['fl_temporario'] = "";
				}		

				$conteudo = str_replace("{fl_vitalicio}", '', $conteudo);	
				$conteudo = str_replace("{fl_temporario}", '', $conteudo);			
			}
			else
			{
				$conteudo = str_replace("{fl_vitalicio}", $ar_dependente['fl_vitalicio'], $conteudo);	
				$conteudo = str_replace("{fl_temporario}", $ar_dependente['fl_temporario'], $conteudo);	
			}
			
			$conteudo = str_replace("{dt_vitalicio}", $ar_dependente['dt_vitalicio'], $conteudo);
			$conteudo = str_replace("{dt_temporario}", $ar_dependente['dt_temporario'], $conteudo);	
			
			$conteudo = str_replace("{fl_sexo_vitalicio_F}", ($ar_dependente['sexo_vitalicio'] == "F" ? "selected": ""), $conteudo);
			$conteudo = str_replace("{fl_sexo_vitalicio_M}", ($ar_dependente['sexo_vitalicio'] == "M" ? "selected": ""), $conteudo);
			$conteudo = str_replace("{fl_sexo_temporario_F}", ($ar_dependente['sexo_temporario'] == "F" ? "selected": ""), $conteudo);
			$conteudo = str_replace("{fl_sexo_temporario_M}", ($ar_dependente['sexo_temporario'] == "M" ? "selected": ""), $conteudo);			
			$conteudo = str_replace("{sexo_vitalicio}", $ar_dependente['sexo_vitalicio'], $conteudo);				
			$conteudo = str_replace("{sexo_temporario}", $ar_dependente['sexo_temporario'], $conteudo);				
						
			$conteudo = str_replace("{user_md5}", $_SESSION['USER_MD5'], $conteudo);
			$conteudo = str_replace("{cd_empresa}", $_SESSION['EMP'], $conteudo);
			$conteudo = str_replace("{cd_registro_empregado}", $_SESSION['RE'], $conteudo);
			$conteudo = str_replace("{seq_dependencia}", $_SESSION['SEQ'], $conteudo);
			
			$conteudo = str_replace("{fl_bpd}", $ar_plano['fl_bpd'], $conteudo);
			$conteudo = str_replace("{fl_autopatrocinio_com_patroc}", $ar_plano['fl_autopatrocinio_com_patroc'], $conteudo);
			$conteudo = str_replace("{dt_simulacao}", $ar_plano['dt_simulacao'], $conteudo);
			$conteudo = str_replace("{dt_nascimento}", $ar_plano['dt_nascimento'], $conteudo);
			$conteudo = str_replace("{dt_ingresso_plano}", $ar_plano['dt_ingresso_plano'], $conteudo);
			$conteudo = str_replace("{dt_migracao}", $ar_plano['dt_migracao'], $conteudo);
			$conteudo = str_replace("{nr_idade}", $ar_plano['nr_idade'], $conteudo);
			$conteudo = str_replace("{sexo_titular}", $ar_plano['sexo'], $conteudo);
			
			#### BUSCA A MEDIA DOS ÚLTIMO 12 SALARIOS ####
			$qr_sql = "
						SELECT ROUND(ceeeprev_salario,2) AS vl_salario
                          FROM simulador.ceeeprev_salario(".$_SESSION['EMP'].",".$_SESSION['RE'].",".$_SESSION['SEQ'].", CURRENT_DATE)
			          ";
	  
			#echo "<PRE>$qr_sql</PRE>"; exit;
			$ob_resul = pg_query($db,$qr_sql);
			$ar_sal   = pg_fetch_array($ob_resul);	
			
			#### VERIFICA SE POSSUI SALARIO ####
			$conteudo = str_replace("{vl_salario}", $ar_sal['vl_salario'], $conteudo);		
			$conteudo = str_replace("{vl_salario_formatado}", number_format($ar_sal['vl_salario'],2,',','.'), $conteudo);

			#### BUSCA RENTABILIDADES PARA SIMULACAO ####
			$ar_rentab = rentabilidade(2);
			#echo "<PRE>".print_r($ar_rentab,true)."</PRE>";
			$cb_rentabilidade = "";
			foreach ($ar_rentab as $ar_item)
			{
				$saiba_mais = "";
				/*
				if($ar_item["tipo"] == "MIN")
				{
					$saiba_mais = " (1)";
				}
				elseif($ar_item["tipo"] == "MAX")
				{
					$saiba_mais = " (2)";
				}
				
				if($ar_item["tipo"] == "MAX")
				{
					$saiba_mais = " (1)";
				}
				*/				
				
				$cb_rentabilidade.= '<option value="'.floatval($ar_item['vl_rentabilidade']).'" '.($ar_item['selecionada'] == "S" ? "selected" : "").'>'.number_format($ar_item['pr_rentabilidade'],2,',','.').'%'.$saiba_mais.'</option>';
			}
			$conteudo = str_replace("{cb_rentabilidade}", $cb_rentabilidade, $conteudo);	 
		}
		elseif(($ar_plano['cd_plano'] == 6) and ($ar_plano['fl_ativo'] == "S")) 
		{
			#### CRMPREV ####

			$ds_arq   = "tpl/tpl_auto_atendimento_simulador_crmprev.html";
			$ob_arq   = fopen($ds_arq, 'r');
			$conteudo = fread($ob_arq, filesize($ds_arq));
			fclose($ob_arq);			

			$conteudo = str_replace("{user_md5}", $_SESSION['USER_MD5'], $conteudo);
			$conteudo = str_replace("{cd_empresa}", $_SESSION['EMP'], $conteudo);
			$conteudo = str_replace("{cd_registro_empregado}", $_SESSION['RE'], $conteudo);
			$conteudo = str_replace("{seq_dependencia}", $_SESSION['SEQ'], $conteudo);
			
			$conteudo = str_replace("{fl_bpd}", $ar_plano['fl_bpd'], $conteudo);
			$conteudo = str_replace("{fl_autopatrocinio_com_patroc}", $ar_plano['fl_autopatrocinio_com_patroc'], $conteudo);
			$conteudo = str_replace("{dt_simulacao}", $ar_plano['dt_simulacao'], $conteudo);
			$conteudo = str_replace("{dt_nascimento}", $ar_plano['dt_nascimento'], $conteudo);
			$conteudo = str_replace("{sexo_titular}", $ar_plano['sexo'], $conteudo);
			$conteudo = str_replace("{dt_ingresso_plano}", $ar_plano['dt_ingresso_plano'], $conteudo);
			$conteudo = str_replace("{dt_migracao}", $ar_plano['dt_migracao'], $conteudo);

			$conteudo = str_replace("{vl_contrib_esporadica_formatado}", 0, $conteudo);
			$conteudo = str_replace("{vl_contrib_esporadica}", 0, $conteudo);
			$conteudo = str_replace("{nr_idade}", $ar_plano['nr_idade'], $conteudo);
			
			#### BUSCA A MEDIA DOS ÚLTIMO 12 SALARIOS ####
			$qr_sql = "
						SELECT ROUND(crmprev_salario,2) AS vl_salario
                          FROM simulador.crmprev_salario(".$_SESSION['EMP'].",".$_SESSION['RE'].",".$_SESSION['SEQ'].", CURRENT_DATE)			
			          ";
			/*
			$qr_sql = "
						SELECT ROUND(AVG(s.vl_salario),2) AS vl_salario
						  FROM (SELECT sp1.ano, 
									   sp1.mes,
									   COALESCE(CASE WHEN sp1.sp_recomposto = 0
													 THEN NULL
													 ELSE sp1.sp_recomposto
												END, sp1.sp_competencia) AS vl_salario
								  FROM public.salarios_participacoes sp1
								 WHERE sp1.cd_empresa            = ".$_SESSION['EMP']."
								   AND sp1.cd_registro_empregado = ".$_SESSION['RE']."
								   AND sp1.seq_dependencia       = ".$_SESSION['SEQ']."
								   AND sp1.mes                   <> 13   
								   AND sp1.dt_lancamento         = (SELECT MAX(sp2.dt_lancamento)
																	  FROM public.salarios_participacoes sp2
																	 WHERE sp2.cd_empresa            = sp1.cd_empresa
																	   AND sp2.cd_registro_empregado = sp1.cd_registro_empregado
																	   AND sp2.seq_dependencia       = sp1.seq_dependencia
																	   AND sp2.ano                   = sp1.ano
																	   AND sp2.mes                   = sp1.mes)
								   AND TO_DATE(sp1.ano || '-' || TRIM(TO_CHAR(sp1.mes,'00')) || '-01','YYYY-MM-DD') < (SELECT TO_DATE(sp3.ano || '-' || TRIM(TO_CHAR(sp3.mes,'00')) || '-01','YYYY-MM-DD')
																														 FROM public.salarios_participacoes sp3
																														WHERE sp3.cd_empresa            = sp1.cd_empresa
																														  AND sp3.cd_registro_empregado = sp1.cd_registro_empregado
																														  AND sp3.seq_dependencia       = sp1.seq_dependencia
																														  AND sp3.dt_lancamento         = (SELECT MAX(sp4.dt_lancamento)
																																							 FROM public.salarios_participacoes sp4
																																							WHERE sp4.cd_empresa            = sp3.cd_empresa
																																							  AND sp4.cd_registro_empregado = sp3.cd_registro_empregado
																																							  AND sp4.seq_dependencia       = sp3.seq_dependencia
																																							  AND sp4.ano                   = sp3.ano
																																							  AND sp4.mes                   = sp3.mes)
																														ORDER BY sp3.ano DESC, 
																																 sp3.mes DESC
																														LIMIT 1)
						 ORDER BY sp1.ano DESC,
								  sp1.mes DESC
						 LIMIT 12) s		
					  ";
			*/		  
			$ob_resul = pg_query($db,$qr_sql);
			$ar_sal   = pg_fetch_array($ob_resul);	
			#### VERIFICA SE POSSUI SALARIO ####
			$conteudo = str_replace("{vl_salario}", $ar_sal['vl_salario'], $conteudo);		
			$conteudo = str_replace("{vl_salario_formatado}", number_format($ar_sal['vl_salario'],2,',','.'), $conteudo);		
			
			#### BUSCA PERCENTUAL DO SALARIO PARA CONTRIBUIÇÃO ####
			$qr_sql = "
						SELECT CAST((tfp.perc_contrib * 100) AS INTEGER) AS  vl_salario_perc
						  FROM public.trocas_faixas_planos tfp
						 WHERE tfp.cd_empresa            = ".$_SESSION['EMP']."
						   AND tfp.cd_registro_empregado = ".$_SESSION['RE']."
						   AND tfp.seq_dependencia       = ".$_SESSION['SEQ']."
						   AND tfp.dt_inicio = (SELECT MAX(tfp2.dt_inicio)
						                          FROM public.trocas_faixas_planos tfp2
												 WHERE tfp2.cd_plano              = tfp.cd_plano
												   AND tfp2.cd_empresa            = tfp.cd_empresa
												   AND tfp2.cd_registro_empregado = tfp.cd_registro_empregado
												   AND tfp2.seq_dependencia       = tfp.seq_dependencia
												   AND tfp2.dt_ingresso_plano     = tfp.dt_ingresso_plano)
			          ";
			$ob_resul    = pg_query($db,$qr_sql);
			$ar_sal_perc = pg_fetch_array($ob_resul);	
			$conteudo = str_replace("{vl_salario_perc}", $ar_sal_perc['vl_salario_perc'], $conteudo);	
			
			#### BUSCA RENTABILIDADES PARA SIMULACAO ####
			$ar_rentab = rentabilidade(6);
			#echo "<PRE>".print_r($ar_rentab,true)."</PRE>";exit;
			$cb_rentabilidade = "";
			foreach ($ar_rentab as $ar_item)
			{
				$saiba_mais = "";
				/*
				if($ar_item["tipo"] == "MAX")
				{
					$saiba_mais = " (1)";
				}
				*/
				
				$cb_rentabilidade.= '<option value="'.floatval($ar_item['vl_rentabilidade']).'" '.($ar_item['selecionada'] == "S" ? "selected" : "").'>'.number_format($ar_item['pr_rentabilidade'],2,',','.').'%'.$saiba_mais.'</option>';
			}
			$conteudo = str_replace("{cb_rentabilidade}", $cb_rentabilidade, $conteudo);			


			#### BUSCA DEPENDENTES VITALICIO E TEMPORÁRIO ####
			$qr_sql = "
						SELECT TO_CHAR(dt_nascimento,'DD/MM/YYYY') AS dt_vitalicio,
							   sexo
						  FROM simulador.crmprev_dependente(".$_SESSION['EMP'].", ".$_SESSION['RE'].", ".$_SESSION['SEQ'].", TO_CHAR(CURRENT_DATE, 'DD/MM/YYYY'))
						 ORDER BY dt_nascimento ASC
					  ";

			$ob_resul = pg_query($db,$qr_sql);
			$i = 1;
			while($ar_dependente = pg_fetch_array($ob_resul))
			{
				$conteudo = str_replace("{dt_vitalicio_".$i."}", $ar_dependente['dt_vitalicio'], $conteudo);
				$conteudo = str_replace("{sexo_vitalicio_".$i."}", $ar_dependente['sexo'], $conteudo);
				$conteudo = str_replace("{fl_sexo_vitalicio_F_".$i."}", ($ar_dependente['sexo'] == "F" ? "selected": ""), $conteudo);
				$conteudo = str_replace("{fl_sexo_vitalicio_M_".$i."}", ($ar_dependente['sexo'] == "M" ? "selected": ""), $conteudo);				
				$i++;
			}
		}
		elseif(($ar_plano['cd_plano'] == 21) and ($ar_plano['fl_ativo'] == "S")) 
		{
			#### INPELPREV ####

			$ds_arq   = "tpl/tpl_auto_atendimento_simulador_inpelprev.html";
			$ob_arq   = fopen($ds_arq, 'r');
			$conteudo = fread($ob_arq, filesize($ds_arq));
			fclose($ob_arq);			

			$conteudo = str_replace("{user_md5}", $_SESSION['USER_MD5'], $conteudo);
			$conteudo = str_replace("{cd_empresa}", $_SESSION['EMP'], $conteudo);
			$conteudo = str_replace("{cd_registro_empregado}", $_SESSION['RE'], $conteudo);
			$conteudo = str_replace("{seq_dependencia}", $_SESSION['SEQ'], $conteudo);
			
			$conteudo = str_replace("{fl_bpd}", $ar_plano['fl_bpd'], $conteudo);
			$conteudo = str_replace("{fl_autopatrocinio_com_patroc}", $ar_plano['fl_autopatrocinio_com_patroc'], $conteudo);
			$conteudo = str_replace("{dt_simulacao}", $ar_plano['dt_simulacao'], $conteudo);
			$conteudo = str_replace("{dt_nascimento}", $ar_plano['dt_nascimento'], $conteudo);
			$conteudo = str_replace("{dt_ingresso_plano}", $ar_plano['dt_ingresso_plano'], $conteudo);
			$conteudo = str_replace("{dt_migracao}", $ar_plano['dt_migracao'], $conteudo);
			$conteudo = str_replace("{nr_idade}", $ar_plano['nr_idade'], $conteudo);
			
			
			
			#### BUSCA OPCAO DE PERCENTUAL DE CONTRIBUICAO ####
			$cb_percentual_salario    = "";
			$vl_perc_sal_partic_atual = 0;
			$vl_perc_sal_patroc_atual = 0;
			
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, APP_SRV."/srvautoatendimento/index.php/get_percentual_contribuicao_atual");
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
			
			if($FL_RETORNO)
			{
				#echo $_RETORNO['error']['status'];echo "<HR>";
				if(intval($_RETORNO['error']['status']) == 0)
				{
					$vl_perc_sal_partic_atual = str_replace(",",".",str_replace(".","",$_RETORNO['result']['percentual_contribuicao_atual']['vl_percentual_partic']));
					$vl_perc_sal_patroc_atual = str_replace(",",".",str_replace(".","",$_RETORNO['result']['percentual_contribuicao_atual']['vl_percentual_patroc']));					
				}
			}			
			
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, APP_SRV."/srvautoatendimento/index.php/get_lista_percentual_contribuicao");
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
			
			if($FL_RETORNO)
			{
				#echo $_RETORNO['error']['status'];echo "<HR>";
				if(intval($_RETORNO['error']['status']) == 0)
				{
					 //['vl_percentual_partic']
					
					foreach($_RETORNO['result']['lista_faixa'] as $ar_perc_sal)
					{
						$vl_perc_sal_partic = str_replace(",",".",str_replace(".","",$ar_perc_sal['vl_percentual_partic']));
						$vl_perc_sal_patroc = str_replace(",",".",str_replace(".","",$ar_perc_sal['vl_percentual_patroc']));
						
						$cb_percentual_salario.= '<option value="'.$vl_perc_sal_partic.'" '.($vl_perc_sal_partic_atual == $vl_perc_sal_partic ? "selected" : "").'>'.$ar_perc_sal['vl_percentual_partic'].'%</option>';
					}					
				}
			}			
			
			
			/*
			$qr_sql = "
						SELECT fcp.num_faixa,
							   CAST((fcp.taxa_basica  * 100) AS NUMERIC) AS vl_perc_partic,
							   CASE WHEN (SELECT CAST((tfp.perc_contrib * 100) AS NUMERIC)
										    FROM public.trocas_faixas_planos tfp
										   WHERE tfp.cd_empresa            = ".$_SESSION['EMP']."
										     AND tfp.cd_registro_empregado = ".$_SESSION['RE']."
										     AND tfp.seq_dependencia       = ".$_SESSION['SEQ']."
										     AND tfp.dt_inicio = (SELECT MAX(tfp2.dt_inicio)
																    FROM public.trocas_faixas_planos tfp2
																   WHERE tfp2.cd_plano              = tfp.cd_plano
																     AND tfp2.cd_empresa            = tfp.cd_empresa
																     AND tfp2.cd_registro_empregado = tfp.cd_registro_empregado
																     AND tfp2.seq_dependencia       = tfp.seq_dependencia
																     AND tfp2.dt_ingresso_plano     = tfp.dt_ingresso_plano)) = CAST((fcp.taxa_basica  * 100) AS NUMERIC)
									THEN 'S'
									ELSE 'N'
							   END AS fl_selecionado
						  FROM faixas_contrib_planos fcp
						 WHERE fcp.cd_empresa = ".$_SESSION['EMP']."
						   AND fcp.data_referencia =(SELECT MAX(fcp2.data_referencia)
													   FROM public.faixas_contrib_planos fcp2
													  WHERE fcp2.cd_empresa = fcp.cd_empresa
														AND fcp2.cd_plano = fcp.cd_plano
														AND fcp2.num_faixa = fcp.num_faixa)
						ORDER BY fcp.num_faixa								
			          ";			
			$ob_resul = pg_query($db,$qr_sql);			
			$cb_percentual_salario = "";
			while($ar_perc_sal = pg_fetch_array($ob_resul))
			{
				$cb_percentual_salario.= '<option value="'.$ar_perc_sal['vl_perc_partic'].'" '.($ar_perc_sal['fl_selecionado'] == "S" ? "selected" : "").'>'.number_format($ar_perc_sal['vl_perc_partic'],2,',','.').'%</option>';
			}
			*/
			$conteudo = str_replace("{cb_percentual_salario}", $cb_percentual_salario, $conteudo);
			$conteudo = str_replace("{vl_max_salario_perc_patroc}", floatval($vl_perc_sal_patroc), $conteudo);
			$conteudo = str_replace("{vl_salario_perc_patroc}", number_format(floatval($vl_perc_sal_patroc),2,',','.')."%", $conteudo);
			
			
			#### BUSCA A MEDIA DOS ÚLTIMO 12 SALARIOS ####
			$qr_sql = "
						SELECT ROUND(inpelprev_salario,2) AS vl_salario
                          FROM simulador.inpelprev_salario(".$_SESSION['EMP'].",".$_SESSION['RE'].",".$_SESSION['SEQ'].", CURRENT_DATE)			
			          ";			
			$ob_resul = pg_query($db,$qr_sql);
			$ar_sal   = pg_fetch_array($ob_resul);	
			#### VERIFICA SE POSSUI SALARIO ####
			$conteudo = str_replace("{vl_salario}", $ar_sal['vl_salario'], $conteudo);		
			$conteudo = str_replace("{vl_salario_formatado}", number_format($ar_sal['vl_salario'],2,',','.'), $conteudo);		
			
		}
		else if (($ar_plano['cd_plano'] == 7) and ($ar_plano['fl_ativo'] == "S")) 
		{
			#### SENGE ####
			$ds_arq   = "tpl/tpl_auto_atendimento_simulador_senge.html";
			$ob_arq   = fopen($ds_arq, 'r');
			$conteudo = fread($ob_arq, filesize($ds_arq));
			fclose($ob_arq);	
			
			$conteudo = str_replace("{user_md5}", $_SESSION['USER_MD5'], $conteudo);
			$conteudo = str_replace("{cd_empresa}", $_SESSION['EMP'], $conteudo);
			$conteudo = str_replace("{cd_registro_empregado}", $_SESSION['RE'], $conteudo);
			$conteudo = str_replace("{seq_dependencia}", $_SESSION['SEQ'], $conteudo);
			
			$conteudo = str_replace("{fl_bpd}", $ar_plano['fl_bpd'], $conteudo);
			$conteudo = str_replace("{dt_simulacao}", $ar_plano['dt_simulacao'], $conteudo);
			$conteudo = str_replace("{dt_nascimento}", $ar_plano['dt_nascimento'], $conteudo);
			$conteudo = str_replace("{dt_ingresso_plano}", $ar_plano['dt_ingresso_plano'], $conteudo);	
			$conteudo = str_replace("{nr_idade}", $ar_plano['nr_idade'], $conteudo);	
			
			
			#### CALCULA IDADE MÍNIMA PARA APOSENTADORIA ####
			$qr_sql = "
						SELECT  TO_CHAR(CASE WHEN (TO_DATE('".$ar_plano['dt_nascimento']."','DD/MM/YYYY')+ '50 years'::interval) > (TO_DATE('".$ar_plano['dt_ingresso_plano']."','DD/MM/YYYY') + '5 year'::interval)
											 THEN (TO_DATE('".$ar_plano['dt_nascimento']."','DD/MM/YYYY')+ '50 years'::interval)
											 ELSE CASE WHEN (TO_DATE('".$ar_plano['dt_ingresso_plano']."','DD/MM/YYYY') + '5 years'::INTERVAL)::DATE > CURRENT_DATE 
											           THEN (TO_DATE('".$ar_plano['dt_ingresso_plano']."','DD/MM/YYYY') + '5 years'::INTERVAL)::DATE
													   ELSE CURRENT_DATE
												  END
										END,'DD/MM/YYYY') AS dt_referencia			
					  ";					  
			$ob_resul = pg_query($db, $qr_sql);
			$ar_data  = pg_fetch_array($ob_resul);
			$qt_meses = calculaMeses($ar_plano['dt_nascimento'], $ar_data['dt_referencia']);
			$nr_idade = (int)($qt_meses / 12);		
			
			#### MONTA COMBO IDADE ####
			$idade_aposentadoria = "";
			$nr_conta = $nr_idade;
			$nr_fim   = 110;
			while ($nr_conta <= $nr_fim)
			{
				$idade_aposentadoria.= '<option value="'.$nr_conta.'">'.$nr_conta.'</option>';
				$nr_conta++;
			}
			$conteudo = str_replace("{idade_aposentadoria}", $idade_aposentadoria, $conteudo);
			
			
			#### VALOR DA CONTRIBUIÇÃO (MÉDIA 12 ULTIMAS) ####
			$qr_sql = " 
						SELECT ROUND(AVG(v.vl_contribuicao),2) AS vl_contribuicao
						  FROM (SELECT ano_competencia,
									   mes_competencia, 
									   SUM(p.valor_pago) AS vl_contribuicao
								  FROM (-- PAGAMENTO ADICIONAL
										SELECT ano_competencia, 
											   mes_competencia, 
											   valor_pago
										  FROM public.cobrancas
										 WHERE cd_empresa            = ".$_SESSION['EMP']."
										   AND cd_registro_empregado = ".$_SESSION['RE']."
										   AND seq_dependencia       = ".$_SESSION['SEQ']."
										   AND sit_lancamento        = 'P'
										   AND codigo_lancamento     = 2420 

										UNION
										   
										-- PAGAMENTO DEBITO EM CONTA
										SELECT ano_competencia, 
											   mes_competencia, 
											   SUM(valor_pago) AS valor_pago
										  FROM public.cobrancas
										 WHERE cd_empresa            = ".$_SESSION['EMP']."
										   AND cd_registro_empregado = ".$_SESSION['RE']."
										   AND seq_dependencia       = ".$_SESSION['SEQ']."
										   AND codigo_lancamento     = 2410
										   AND sit_lancamento = 'P'
										 GROUP BY ano_competencia, 
												  mes_competencia

										UNION
										
										-- PAGAMENTO BDL/ARRECADAÇÃO
										SELECT ano_competencia, 
											   mes_competencia, 
											   SUM(valor_pago) AS valor_pago
										  FROM public.cobrancas
										 WHERE cd_empresa            = ".$_SESSION['EMP']."
										   AND cd_registro_empregado = ".$_SESSION['RE']."
										   AND seq_dependencia       = ".$_SESSION['SEQ']."
										   AND codigo_lancamento     = 2400
										   AND sit_lancamento        = 'P'
										 GROUP BY ano_competencia, 
												  mes_competencia) p
												  
								 GROUP BY p.ano_competencia, 
										  p.mes_competencia	
								 ORDER BY p.ano_competencia DESC,  p.mes_competencia  DESC				
								 LIMIT 12) v
					  ";
			$ob_resul  = pg_query($db, $qr_sql);
			$ar_contrib_media = pg_fetch_array($ob_resul);					  
			$conteudo = str_replace("{vl_contribuicao_formatado}", number_format($ar_contrib_media['vl_contribuicao'],2,'.',','), $conteudo);
			$conteudo = str_replace("{vl_contribuicao}", $ar_contrib_media['vl_contribuicao'], $conteudo);
			
			#### BUSCA RENTABILIDADES PARA SIMULACAO ####
			$ar_rentab = rentabilidade(7);
			#echo "<PRE>".print_r($ar_rentab,true)."</PRE>";exit;
			$cb_rentabilidade = "";
			foreach ($ar_rentab as $ar_item)
			{
				$saiba_mais = "";
				/*
				if($ar_item["tipo"] == "MAX")
				{
					$saiba_mais = " (1)";
				}
				*/
				
				$cb_rentabilidade.= '<option value="'.floatval($ar_item['vl_rentabilidade']).'" '.($ar_item['selecionada'] == "S" ? "selected" : "").'>'.number_format($ar_item['pr_rentabilidade'],2,',','.').'%'.$saiba_mais.'</option>';
			}
			$conteudo = str_replace("{cb_rentabilidade}", $cb_rentabilidade, $conteudo);				
			
		}	
		else if (($ar_plano['cd_plano'] == 8) and ($ar_plano['fl_ativo'] == "S")) 
		{
			#### SINPRORS / SINTAE ####
			$ds_arq   = "tpl/tpl_auto_atendimento_simulador_sinprors.html";
			$ob_arq   = fopen($ds_arq, 'r');
			$conteudo = fread($ob_arq, filesize($ds_arq));
			fclose($ob_arq);	
			
			$conteudo = str_replace("{user_md5}", $_SESSION['USER_MD5'], $conteudo);
			$conteudo = str_replace("{cd_empresa}", $_SESSION['EMP'], $conteudo);
			$conteudo = str_replace("{cd_registro_empregado}", $_SESSION['RE'], $conteudo);
			$conteudo = str_replace("{seq_dependencia}", $_SESSION['SEQ'], $conteudo);
			
			$conteudo = str_replace("{fl_bpd}", $ar_plano['fl_bpd'], $conteudo);
			$conteudo = str_replace("{dt_simulacao}", $ar_plano['dt_simulacao'], $conteudo);
			$conteudo = str_replace("{dt_nascimento}", $ar_plano['dt_nascimento'], $conteudo);
			$conteudo = str_replace("{dt_ingresso_plano}", $ar_plano['dt_ingresso_plano'], $conteudo);	
			$conteudo = str_replace("{nr_idade}", $ar_plano['nr_idade'], $conteudo);	
			
			
			#### CALCULA IDADE MÍNIMA PARA APOSENTADORIA ####
			$qr_sql = "
						SELECT  TO_CHAR(CASE WHEN (TO_DATE('".$ar_plano['dt_nascimento']."','DD/MM/YYYY')+ '50 years'::interval) > (TO_DATE('".$ar_plano['dt_ingresso_plano']."','DD/MM/YYYY') + '5 year'::interval)
											 THEN (TO_DATE('".$ar_plano['dt_nascimento']."','DD/MM/YYYY')+ '50 years'::interval)
											 ELSE CASE WHEN (TO_DATE('".$ar_plano['dt_ingresso_plano']."','DD/MM/YYYY') + '5 years'::INTERVAL)::DATE > CURRENT_DATE 
											           THEN (TO_DATE('".$ar_plano['dt_ingresso_plano']."','DD/MM/YYYY') + '5 years'::INTERVAL)::DATE
													   ELSE CURRENT_DATE
												  END
										END,'DD/MM/YYYY') AS dt_referencia			
					  ";					  
			$ob_resul = pg_query($db, $qr_sql);
			$ar_data  = pg_fetch_array($ob_resul);
			$qt_meses = calculaMeses($ar_plano['dt_nascimento'], $ar_data['dt_referencia']);
			$nr_idade = (int)($qt_meses / 12);		
			
			#### MONTA COMBO IDADE ####
			$idade_aposentadoria = "";
			$nr_conta = $nr_idade;
			$nr_fim   = 110;
			while ($nr_conta <= $nr_fim)
			{
				$idade_aposentadoria.= '<option value="'.$nr_conta.'">'.$nr_conta.'</option>';
				$nr_conta++;
			}
			$conteudo = str_replace("{idade_aposentadoria}", $idade_aposentadoria, $conteudo);
			
			
			#### VALOR DA CONTRIBUIÇÃO (MÉDIA 12 ULTIMAS) ####
			$qr_sql = " 
						SELECT ROUND(AVG(v.vl_contribuicao),2) AS vl_contribuicao
						  FROM (SELECT ano_competencia,
									   mes_competencia, 
									   SUM(p.valor_pago) AS vl_contribuicao
								  FROM (-- PAGAMENTO ADICIONAL
										SELECT ano_competencia, 
											   mes_competencia, 
											   valor_pago
										  FROM public.cobrancas
										 WHERE cd_empresa            = ".$_SESSION['EMP']."
										   AND cd_registro_empregado = ".$_SESSION['RE']."
										   AND seq_dependencia       = ".$_SESSION['SEQ']."
										   AND sit_lancamento        = 'P'
										   AND codigo_lancamento     = 2470 

										UNION
										   
										-- PAGAMENTO DEBITO EM CONTA
										SELECT ano_competencia, 
											   mes_competencia, 
											   SUM(valor_pago) AS valor_pago
										  FROM public.cobrancas
										 WHERE cd_empresa            = ".$_SESSION['EMP']."
										   AND cd_registro_empregado = ".$_SESSION['RE']."
										   AND seq_dependencia       = ".$_SESSION['SEQ']."
										   AND codigo_lancamento     = 2460
										   AND sit_lancamento = 'P'
										 GROUP BY ano_competencia, 
												  mes_competencia

										UNION
																
										-- PAGAMENTO DESCONTO EM FOLHA
										SELECT ano_competencia, 
											   mes_competencia, 
											   SUM(valor_pago) AS valor_pago
										  FROM public.cobrancas
										 WHERE cd_empresa            = ".$_SESSION['EMP']."
										   AND cd_registro_empregado = ".$_SESSION['RE']."
										   AND seq_dependencia       = ".$_SESSION['SEQ']."
										   AND codigo_lancamento     = 2480
										   AND sit_lancamento        = 'P'
										 GROUP BY ano_competencia, 
												  mes_competencia
										   
										UNION 
										
										-- PAGAMENTO BDL/ARRECADAÇÃO
										SELECT ano_competencia, 
											   mes_competencia, 
											   SUM(valor_pago) AS valor_pago
										  FROM public.cobrancas
										 WHERE cd_empresa            = ".$_SESSION['EMP']."
										   AND cd_registro_empregado = ".$_SESSION['RE']."
										   AND seq_dependencia       = ".$_SESSION['SEQ']."
										   AND codigo_lancamento     = 2450
										   AND sit_lancamento        = 'P'
										 GROUP BY ano_competencia, 
												  mes_competencia) p
												  
								 GROUP BY p.ano_competencia, 
										  p.mes_competencia	
								 ORDER BY p.ano_competencia DESC,  p.mes_competencia  DESC				
								 LIMIT 12) v
					  ";
			$ob_resul  = pg_query($db, $qr_sql);
			$ar_contrib_media = pg_fetch_array($ob_resul);					  
			$conteudo = str_replace("{vl_contribuicao_formatado}", number_format($ar_contrib_media['vl_contribuicao'],2,'.',','), $conteudo);
			$conteudo = str_replace("{vl_contribuicao}", $ar_contrib_media['vl_contribuicao'], $conteudo);
			
			
			#### VALOR DO ÚLTIMO APORTE DO EMPREGADOR ####
			$qr_sql = " 
						SELECT sc.valor_contribuicao AS vl_contribuicao_empregador
						  FROM public.sp_contribuicoes sc
						 WHERE sc.cd_empresa             = ".$_SESSION['EMP']."
						   AND sc.cd_registro_empregado  = ".$_SESSION['RE']."
						   AND sc.seq_dependencia        = ".$_SESSION['SEQ']."
						   AND sc.contrib_partic_patroc  = 'P'
						   AND sc.codigo_lancamento      IS NULL
						   AND sc.id_pagamento           = 'S'
						   AND sc.id_cobranca            = 'N'
						   AND sc.dt_efetiva_pgto        IS NOT NULL
						   AND sc.cd_calculo             = 6
						   AND sc.tipo_lancamento        = 'M'
						   AND sc.seq_lancamento_cobr    = 0
						   AND COALESCE(sc.valor_pago,0) > 0
						   AND sc.cd_evento              = 36
						 ORDER BY sc.dt_lancamento DESC
						 LIMIT 1
					  ";
			$ob_resul  = pg_query($db, $qr_sql);
			$ar_contrib_media = pg_fetch_array($ob_resul);					  

			if(trim($ar_contrib_media['vl_contribuicao_empregador']) == "")
			{
				$ar_contrib_media['vl_contribuicao_empregador'] = 0;
			}
			$conteudo = str_replace("{vl_contribuicao_empregador_formatado}", number_format($ar_contrib_media['vl_contribuicao_empregador'],2,',','.'), $conteudo);
			$conteudo = str_replace("{vl_contribuicao_empregador}", $ar_contrib_media['vl_contribuicao_empregador'], $conteudo);		
			
			#### RISCO INVALIDEZ ####
			$qr_sql = "
						SELECT capital, 
							   premio
						  FROM public.coberturas_apol_participantes cp
						  JOIN public.apolices_participantes ap
							ON ap.cd_apolice            = cp.cd_apolice  
						   AND ap.cd_empresa            = cp.cd_empresa
						   AND ap.cd_registro_empregado = cp.cd_registro_empregado
						   AND ap.seq_dependencia       = cp.seq_dependencia
						  JOIN public.apolices a
							ON a.cd_apolice = ap.cd_apolice
						 WHERE ap.cd_empresa            = ".$_SESSION['EMP']."
						   AND ap.cd_registro_empregado = ".$_SESSION['RE']."
						   AND ap.seq_dependencia       = ".$_SESSION['SEQ']."
						   AND ap.dt_exclusao IS NULL	
						   AND ap.cd_apolice  = 78
					  ";
			#echo "<PRE>$qr_sql</PRE>"; #exit;
			$ob_resul = pg_query($db,$qr_sql);
			$ar_invalidez = pg_fetch_array($ob_resul);		
			$conteudo = str_replace("{vl_capital_invalidez}",number_format($ar_invalidez['capital'],2,",","."),$conteudo);
			$conteudo = str_replace("{vl_premio_invalidez}",number_format($ar_invalidez['premio'],2,",","."),$conteudo);	
			$conteudo = str_replace("{vl_premio_invalidez_contratado}",number_format($ar_invalidez['premio'],2,",","."),$conteudo);	

			
			
			#### RISCO MORTE ####
			$qr_sql = "
						SELECT capital, 
							   premio
						  FROM public.coberturas_apol_participantes cp
						  JOIN public.apolices_participantes ap
							ON ap.cd_apolice            = cp.cd_apolice  
						   AND ap.cd_empresa            = cp.cd_empresa
						   AND ap.cd_registro_empregado = cp.cd_registro_empregado
						   AND ap.seq_dependencia       = cp.seq_dependencia
						  JOIN public.apolices a
							ON a.cd_apolice = ap.cd_apolice
						 WHERE ap.cd_empresa            = ".$_SESSION['EMP']."
						   AND ap.cd_registro_empregado = ".$_SESSION['RE']."
						   AND ap.seq_dependencia       = ".$_SESSION['SEQ']."
						   AND ap.dt_exclusao IS NULL	
						   AND ap.cd_apolice  = 77				
					  ";
			#echo "<PRE>$qr_sql</PRE>"; #exit;
			$ob_resul = pg_query($db,$qr_sql);
			$ar_morte = pg_fetch_array($ob_resul);		
			$conteudo = str_replace("{vl_capital_pensao}",number_format($ar_morte['capital'],2,",","."),$conteudo);			
			$conteudo = str_replace("{vl_premio_pensao}",number_format($ar_morte['premio'],2,",","."),$conteudo);	
			$conteudo = str_replace("{vl_premio_pensao_contratado}",number_format($ar_morte['premio'],2,",","."),$conteudo);	

			#### BUSCA RENTABILIDADES PARA SIMULACAO ####
			$ar_rentab = rentabilidade(8);
			#echo "<PRE>".print_r($ar_rentab,true)."</PRE>";exit;
			$cb_rentabilidade = "";
			foreach ($ar_rentab as $ar_item)
			{
				$saiba_mais = "";
				/*
				if($ar_item["tipo"] == "MAX")
				{
					$saiba_mais = " (1)";
				}
				*/
				
				$cb_rentabilidade.= '<option value="'.floatval($ar_item['vl_rentabilidade']).'" '.($ar_item['selecionada'] == "S" ? "selected" : "").'>'.number_format($ar_item['pr_rentabilidade'],2,',','.').'%'.$saiba_mais.'</option>';
			}
			$conteudo = str_replace("{cb_rentabilidade}", $cb_rentabilidade, $conteudo);			
			
		}
		else if (($ar_plano['cd_plano'] == 9) and ($ar_plano['fl_ativo'] == "S")) 
		{
			#### FAMILIA ####
			$ds_arq   = "tpl/tpl_auto_atendimento_simulador_familia.html";
			$ob_arq   = fopen($ds_arq, 'r');
			$conteudo = fread($ob_arq, filesize($ds_arq));
			fclose($ob_arq);	
			
			$conteudo = str_replace("{user_md5}", $_SESSION['USER_MD5'], $conteudo);
			$conteudo = str_replace("{cd_empresa}", $_SESSION['EMP'], $conteudo);
			$conteudo = str_replace("{cd_registro_empregado}", $_SESSION['RE'], $conteudo);
			$conteudo = str_replace("{seq_dependencia}", $_SESSION['SEQ'], $conteudo);
			
			$conteudo = str_replace("{fl_bpd}", $ar_plano['fl_bpd'], $conteudo);
			$conteudo = str_replace("{dt_simulacao}", $ar_plano['dt_simulacao'], $conteudo);
			$conteudo = str_replace("{dt_nascimento}", $ar_plano['dt_nascimento'], $conteudo);
			$conteudo = str_replace("{dt_ingresso_plano}", $ar_plano['dt_ingresso_plano'], $conteudo);	
			$conteudo = str_replace("{nr_idade}", $ar_plano['nr_idade'], $conteudo);	
			
			
			#### CALCULA IDADE MÍNIMA PARA APOSENTADORIA ####
			/*
			$qr_sql = "
						SELECT  TO_CHAR(CASE WHEN (TO_DATE('".$ar_plano['dt_nascimento']."','DD/MM/YYYY')+ '50 years'::interval) > (TO_DATE('".$ar_plano['dt_ingresso_plano']."','DD/MM/YYYY') + '5 year'::interval)
											 THEN (TO_DATE('".$ar_plano['dt_nascimento']."','DD/MM/YYYY')+ '50 years'::interval)
											 ELSE CASE WHEN (TO_DATE('".$ar_plano['dt_ingresso_plano']."','DD/MM/YYYY') + '5 years'::INTERVAL)::DATE > CURRENT_DATE 
											           THEN (TO_DATE('".$ar_plano['dt_ingresso_plano']."','DD/MM/YYYY') + '5 years'::INTERVAL)::DATE
													   ELSE CURRENT_DATE
												  END
										END,'DD/MM/YYYY') AS dt_referencia			
					  ";
			*/
			$qr_sql = "
						SELECT dt_aposentadoria,
							   TO_CHAR(dt_referencia,'DD/MM/YYYY') AS dt_referencia					
						  FROM simulador.familia_calcula_datas(TO_DATE('".$ar_plano['dt_nascimento']."','DD/MM/YYYY'), TO_DATE('".$ar_plano['dt_ingresso_plano']."','DD/MM/YYYY'), 50)
					  ";
			$ob_resul = pg_query($db, $qr_sql);
			$ar_data  = pg_fetch_array($ob_resul);
			$qt_meses = calculaMeses($ar_plano['dt_nascimento'], $ar_data['dt_referencia']);
			$nr_idade = (int)($qt_meses / 12);		
			
			#### MONTA COMBO IDADE ####
			$idade_aposentadoria = "";
			$nr_conta = $nr_idade;
			$nr_fim   = 110;
			while ($nr_conta <= $nr_fim)
			{
				$idade_aposentadoria.= '<option value="'.$nr_conta.'">'.$nr_conta.'</option>';
				$nr_conta++;
			}
			$conteudo = str_replace("{idade_aposentadoria}", $idade_aposentadoria, $conteudo);
			
			
			#### VALOR DA CONTRIBUIÇÃO (MÉDIA 12 ULTIMAS) ####
			/*
			$qr_sql = " 
						SELECT ROUND(AVG(v.vl_contribuicao),2) AS vl_contribuicao
						  FROM (SELECT ano_competencia,
									   mes_competencia, 
									   SUM(p.valor_pago) AS vl_contribuicao
								  FROM (-- PAGAMENTO FOLHA
										SELECT ano_competencia, 
											   mes_competencia, 
											   valor_pago
										  FROM public.cobrancas
										 WHERE cd_empresa            = ".$_SESSION['EMP']."
										   AND cd_registro_empregado = ".$_SESSION['RE']."
										   AND seq_dependencia       = ".$_SESSION['SEQ']."
										   AND sit_lancamento        = 'P'
										   AND codigo_lancamento     = 2500 

										UNION
										   
										-- PAGAMENTO DEBITO EM CONTA
										SELECT ano_competencia, 
											   mes_competencia, 
											   SUM(valor_pago) AS valor_pago
										  FROM public.cobrancas
										 WHERE cd_empresa            = ".$_SESSION['EMP']."
										   AND cd_registro_empregado = ".$_SESSION['RE']."
										   AND seq_dependencia       = ".$_SESSION['SEQ']."
										   AND codigo_lancamento     = 2501
										   AND sit_lancamento = 'P'
										 GROUP BY ano_competencia, 
												  mes_competencia

										UNION
										
										-- PAGAMENTO BDL/ARRECADAÇÃO
										SELECT ano_competencia, 
											   mes_competencia, 
											   SUM(valor_pago) AS valor_pago
										  FROM public.cobrancas
										 WHERE cd_empresa            = ".$_SESSION['EMP']."
										   AND cd_registro_empregado = ".$_SESSION['RE']."
										   AND seq_dependencia       = ".$_SESSION['SEQ']."
										   AND codigo_lancamento     = 2502
										   AND sit_lancamento        = 'P'
										 GROUP BY ano_competencia, 
												  mes_competencia
												  
										UNION
										
										-- PAGAMENTO FOLHA 3º
										SELECT ano_competencia, 
											   mes_competencia, 
											   SUM(valor_pago) AS valor_pago
										  FROM public.cobrancas
										 WHERE cd_empresa            = ".$_SESSION['EMP']."
										   AND cd_registro_empregado = ".$_SESSION['RE']."
										   AND seq_dependencia       = ".$_SESSION['SEQ']."
										   AND codigo_lancamento     IN (2503,2509)
										   AND sit_lancamento        = 'P'
										 GROUP BY ano_competencia, 
												  mes_competencia) p
												  
								 GROUP BY p.ano_competencia, 
										  p.mes_competencia	
								 ORDER BY p.ano_competencia DESC,  p.mes_competencia  DESC				
								 LIMIT 12) v
					  ";
			*/	

			$qr_sql = " 
                        SELECT ROUND(AVG(v.vl_pago),2) AS vl_contribuicao
						  FROM (SELECT a.vl_pago
						          FROM boleto.pagamentos(funcoes.cripto_re(".$_SESSION['EMP'].",".$_SESSION['RE'].",".$_SESSION['SEQ'].")) a
						         WHERE a.cd_tipo = 'PREVI'
						         ORDER BY a.ds_competencia DESC
						         LIMIT 12) v			
			          ";
			#echo "<PRE>".$qr_sql."</PRE>";exit;
			$ob_resul  = pg_query($db, $qr_sql);
			$ar_contrib_media = pg_fetch_array($ob_resul);					  
			$conteudo = str_replace("{vl_contribuicao_formatado}", number_format($ar_contrib_media['vl_contribuicao'],2,'.',','), $conteudo);
			$conteudo = str_replace("{vl_contribuicao}", $ar_contrib_media['vl_contribuicao'], $conteudo);
			
			#### VALOR DO ÚLTIMO APORTE DO EMPREGADOR ####
			$qr_sql = " 
						SELECT sc.valor_contribuicao AS vl_contribuicao_empregador
						  FROM public.sp_contribuicoes sc
						 WHERE sc.cd_empresa             = ".$_SESSION['EMP']."
						   AND sc.cd_registro_empregado  = ".$_SESSION['RE']."
						   AND sc.seq_dependencia        = ".$_SESSION['SEQ']."
						   AND sc.contrib_partic_patroc  = 'P'
						   AND sc.codigo_lancamento      IS NULL
						   AND sc.id_pagamento           = 'S'
						   AND sc.id_cobranca            = 'N'
						   AND sc.dt_efetiva_pgto        IS NOT NULL
						   AND sc.cd_calculo             = 6
						   AND sc.tipo_lancamento        = 'M'
						   AND sc.seq_lancamento_cobr    = 0
						   AND COALESCE(sc.valor_pago,0) > 0
						   AND sc.cd_evento              = 36
						 ORDER BY sc.dt_lancamento DESC
						 LIMIT 1
					  ";
			$ob_resul  = pg_query($db, $qr_sql);
			$ar_contrib_media = pg_fetch_array($ob_resul);					  

			if(trim($ar_contrib_media['vl_contribuicao_empregador']) == "")
			{
				$ar_contrib_media['vl_contribuicao_empregador'] = 0;
			}
			$conteudo = str_replace("{vl_contribuicao_empregador_formatado}", number_format($ar_contrib_media['vl_contribuicao_empregador'],2,',','.'), $conteudo);
			$conteudo = str_replace("{vl_contribuicao_empregador}", $ar_contrib_media['vl_contribuicao_empregador'], $conteudo);			
			
			#### BUSCA RENTABILIDADES PARA SIMULACAO ####
			$ar_rentab = rentabilidade(9);
			#echo "<PRE>".print_r($ar_rentab,true)."</PRE>";exit;
			$cb_rentabilidade = "";
			foreach ($ar_rentab as $ar_item)
			{
				$saiba_mais = "";
				/*
				if($ar_item["tipo"] == "MAX")
				{
					$saiba_mais = " (1)";
				}
				*/
				
				$cb_rentabilidade.= '<option value="'.floatval($ar_item['vl_rentabilidade']).'" '.($ar_item['selecionada'] == "S" ? "selected" : "").'>'.number_format($ar_item['pr_rentabilidade'],2,',','.').'%'.$saiba_mais.'</option>';
			}
			$conteudo = str_replace("{cb_rentabilidade}", $cb_rentabilidade, $conteudo);			
		}			
		else if($ar_plano['fl_ativo'] == "N") 
		{
			$conteudo = "
							<br><br><br>
							<center>
								<h1 style='font-family: Calibri, Arial; font-size: 15pt;'>Somente ATIVOS podem simular.</h1>
							</center>
							<br><br><br>
						";
		}	
		else
		{
			$conteudo = "
							<br><br><br>
							<center>
								<h1 style='font-family: Calibri, Arial; font-size: 15pt;'>
									Para informações faça contato com nosso teleatendimento<BR>no 0800 512596, de segunda a sexta, das 8h às 17 horas.
								</h1>
							</center>
							<br><br><br>
						";
		}
	}

	$tpl->assign('conteudo',$fl_menu.$conteudo);
	$tpl->printToScreen();
	
	
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
	
	function rentabilidade($cd_plano)
	{
		global $db;
		
		#### RENTABILIDADE MAXIMA PERMITIDA PARA SIMULACAO ####
		$ar_max_rentab[2] = array("vl_rentabilidade" => 0.007207323, "pr_rentabilidade" => 9.00, "tipo" => "MAX"); #CEEEPREV OS: 42870 - 04/03/2015
		//$ar_max_rentab[6] = array("vl_rentabilidade" => 0.007207323, "pr_rentabilidade" => 9.00, "tipo" => "MAX"); #CEEEPREV OS: 42870 - 04/03/2015
		$ar_max_rentab[7] = array("vl_rentabilidade" => 0.007207323, "pr_rentabilidade" => 9.00, "tipo" => "MAX"); #SENGE OS: 42870 - 04/03/2015
		$ar_max_rentab[8] = array("vl_rentabilidade" => 0.007207323, "pr_rentabilidade" => 9.00, "tipo" => "MAX"); #SINPRORS OS: 42870 - 04/03/2015
		$ar_max_rentab[9] = array("vl_rentabilidade" => 0.007207323, "pr_rentabilidade" => 9.00, "tipo" => "MAX"); #FAMILIA OS: 42870 - 04/03/2015
		
		#### TAXA DE JURO ATUARIAL ####
		$ar_padrao[2] = array("vl_rentabilidade" => 0.004590635); #CEEEPREV 
		$ar_padrao[6] = array("vl_rentabilidade" => 0.004867551); #CRMPREV
		$ar_padrao[7] = array("vl_rentabilidade" => ""); #SENGE
		$ar_padrao[8] = array("vl_rentabilidade" => ""); #SINPRORS
		$ar_padrao[9] = array("vl_rentabilidade" => ""); #FAMILIA		
	
		$qr_sql = "
					SELECT a.*
					  FROM (
								SELECT x.column1 AS vl_rentabilidade, x.column2 AS pr_rentabilidade, x.column3 AS tipo
								  FROM (VALUES 
												(0.003273740,4,   'NOR'),
												(0.004074124,5,   'NOR'),
												--(0.004471699,5.5, 'NOR'),
												--(0.004590635,5.65, 'NOR'),
												(0.004867551,6,   'NOR'),
												(0.005654145,7,   'NOR'),
												(0.006434030,8,   'NOR'),
												(0.007207323,9,   'NOR')--,
												--(0.007974140,10,  'NOR'),
												--(0.008734594,11,  'NOR'),
												--(0.009488793,12,  'NOR')
									   ) x
						   ) a
					 WHERE 1 = 1
					   ".(count($ar_max_rentab[$cd_plano]) > 0 ? "AND a.vl_rentabilidade < ".floatval($ar_max_rentab[$cd_plano]["vl_rentabilidade"]) : "")."
					 ORDER BY pr_rentabilidade		
		          ";
		#echo "<PRE>".$qr_sql."</PRE>";#exit;
		
		$ob_resul = pg_query($db, $qr_sql);
		$ar_rentab = array();
		while($ar_reg = pg_fetch_array($ob_resul))
		{
			$ar_rentab[] = array(
									"vl_rentabilidade" => $ar_reg["vl_rentabilidade"], 
									"pr_rentabilidade" => $ar_reg["pr_rentabilidade"], 
									"tipo" => $ar_reg["tipo"], 
									"selecionada" => ($ar_padrao[$cd_plano]["vl_rentabilidade"] == $ar_reg["vl_rentabilidade"] ? "S" : "N")
							    );
		}
		
		if(count($ar_max_rentab[$cd_plano]) > 0)
		{
			$ar_rentab[] = array(
									"vl_rentabilidade" => $ar_max_rentab[$cd_plano]["vl_rentabilidade"], 
									"pr_rentabilidade" => $ar_max_rentab[$cd_plano]["pr_rentabilidade"], 
									"tipo" => $ar_max_rentab[$cd_plano]["tipo"]
								);
		}
		
		return $ar_rentab;
	}
?>