<?php
	#### É CHAMADO DO ELETRO NA TELA COBP0355 USADA PELA GAP ###
	#http://10.63.255.150/eletroceee/sinprors_gap_atendimento.php?e=8&r=60&s=0
	#if (preg_match('/10.63./',$_SERVER['REMOTE_ADDR']))
	#{
		include_once('inc/conexao.php');
		if((trim($_REQUEST['e']) != "") and (trim($_REQUEST['r']) != "") and (trim($_REQUEST['s']) != ""))
		{
			$qr_sql = "SELECT cd_plano FROM planos_patrocinadoras tp WHERE dt_encerramento_plano IS NULL AND cd_empresa = ".intval($_REQUEST['e']).";";

			$ob_resul = pg_query($db, $qr_sql);	
			
			if(pg_num_rows($ob_resul) == 0)
			{
				echo '<META HTTP-EQUIV="Refresh" CONTENT="0;URL=index.php">';
				exit;				
			}
			else
			{
				$planos_patrocinadoras = pg_fetch_array($ob_resul);

				if(intval($planos_patrocinadoras['cd_plano']) == 8)
				{
					$qr_sql = " 
						SELECT funcoes.cripto_re(".intval(trim($_REQUEST['e'])).", ".intval(trim($_REQUEST['r'])).", ".intval(trim($_REQUEST['s'])).") AS re,
							   funcoes.cripto_mes_ano(00, 0000) AS comp,
							   'sinprors_pagamento.php' AS url;";	
				}
				else if(intval($planos_patrocinadoras['cd_plano']) == 7)
				{
					$qr_sql = " 
						SELECT funcoes.cripto_re(".intval(trim($_REQUEST['e'])).", ".intval(trim($_REQUEST['r'])).", ".intval(trim($_REQUEST['s'])).") AS re,
									   funcoes.cripto_mes_ano(00, 0000) AS comp,
									   'senge_pagamento.php' AS url;";	
				}
				else if(intval($planos_patrocinadoras['cd_plano']) == 9)
				{
					$qr_sql = " 
						SELECT funcoes.cripto_re(".intval(trim($_REQUEST['e'])).", ".intval(trim($_REQUEST['r'])).", ".intval(trim($_REQUEST['s'])).") AS re,
									   funcoes.cripto_mes_ano(00, 0000) AS comp,
	                                   'familia_pagamento.php' AS url;";
				}
				else
				{
					echo '<META HTTP-EQUIV="Refresh" CONTENT="0;URL=index.php">';
					exit;
				}
				/*
				if(($_REQUEST['e'] == 8) or ($_REQUEST['e'] == 10) or ($_REQUEST['e'] == 12))
				{
					$qr_sql = " 
								SELECT funcoes.cripto_re(".intval(trim($_REQUEST['e'])).", ".intval(trim($_REQUEST['r'])).", ".intval(trim($_REQUEST['s'])).") AS re,
									   funcoes.cripto_mes_ano(00, 0000) AS comp,
									   'sinprors_pagamento.php' AS url
						   ";			   

				}
				else if(($_REQUEST['e'] == 19) or ($_REQUEST['e'] == 20) or ($_REQUEST['e'] == 24) or ($_REQUEST['e'] == 25) or ($_REQUEST['e'] == 26) or ($_REQUEST['e'] == 27) or ($_REQUEST['e'] == 28) or ($_REQUEST['e'] == 29) or ($_REQUEST['e'] == 30) or ($_REQUEST['e'] == 31))
				{
					$qr_sql = " 
								SELECT funcoes.cripto_re(".intval(trim($_REQUEST['e'])).", ".intval(trim($_REQUEST['r'])).", ".intval(trim($_REQUEST['s'])).") AS re,
									   funcoes.cripto_mes_ano(00, 0000) AS comp,
	                                   'familia_pagamento.php' AS url								   
						   ";				
				}
				else if($_REQUEST['e'] == 7)
				{
					$qr_sql = " 
								SELECT funcoes.cripto_re(".intval(trim($_REQUEST['e'])).", ".intval(trim($_REQUEST['r'])).", ".intval(trim($_REQUEST['s'])).") AS re,
									   funcoes.cripto_mes_ano(00, 0000) AS comp,
									   'senge_pagamento.php' AS url
						   ";				
				}
				else
				{
					echo '<META HTTP-EQUIV="Refresh" CONTENT="0;URL=index.php">';
					exit;
				}
				*/
				
				$ob_resul = pg_query($db, $qr_sql);	
				
				if(pg_num_rows($ob_resul) == 0)
				{
					echo '<META HTTP-EQUIV="Refresh" CONTENT="0;URL=index.php">';
					exit;				
				}
				else
				{
					$ar_bloqueto = pg_fetch_array($ob_resul);
					echo '<META HTTP-EQUIV="Refresh" CONTENT="0;URL='.$ar_bloqueto['url'].'?re='.$ar_bloqueto['re'].'&comp='.$ar_bloqueto['comp'].'">';			
				}
			}
		}
		else
		{
			echo '<META HTTP-EQUIV="Refresh" CONTENT="0;URL=index.php">';
			exit;
		}		
	#}
	#else
	#{
	#	echo '<META HTTP-EQUIV="Refresh" CONTENT="0;URL=index.php">';
	#	exit;
	#}
?>