<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');

	
	$ano = $_POST['a'];

	$meta = app_decimal_para_db($_POST['nr_meta']);

	$cd_indicador_tabela = $_POST['c'];

	#### VARIAVEIS RESUMO ATIVIDADES ####
	$qt_resumo_anterior_aberta    = 0;
	$qt_resumo_anterior_concluida_crit_auto = 0;
	$qt_resumo_anterior_concluida_crit_user = 0;
	$qt_resumo_anterior_concluida_crit_nao = 0;
	$qt_resumo_anterior_concluida = 0;
	$qt_resumo_anterior_cancelada = 0;
	$qt_resumo_anterior_suspensa  = 0;
	$qt_resumo_anterior_atendida  = 0; 

	##################################################### ATIVIDADES DE SUPORTE #######################################################
	#### ATIVIDADES DE SUPORTE - ACUMULADO ####
	#### ABERTAS ####
	$qr_anterior = "
					SELECT COUNT(a.numero) as qt_ant_aberta 
					  FROM projetos.atividades a
					 WHERE a.area   = 'GI'
					   AND DATE_TRUNC('day',a.dt_cad) < TO_DATE('01/01/".$ano."','DD/MM/YYYY') 
					   AND a.tipo   = 'S'
				   ";
	if($_REQUEST['cd_atendente'] != "")
	{
		$qr_anterior.= " AND a.cod_atendente = ".$_REQUEST['cd_atendente'];
	}				

	$ob_resul = pg_query($db, $qr_anterior);
	$ob_reg   = pg_fetch_object($ob_resul); 
	$qt_anterior_aberta = $ob_reg->qt_ant_aberta;

	#### CONCLUIDAS ####
	$qr_anterior = "
					SELECT COUNT(a.numero) as qt_ant_concluida 
					  FROM projetos.atividades a
					 WHERE a.status_atual = 'CONC'
					   AND a.area         = 'GI'					 
					   AND DATE_TRUNC('day',a.dt_fim_real) < TO_DATE('01/01/".$ano."','DD/MM/YYYY') 
					   AND a.tipo         = 'S'
				   ";
	if($_REQUEST['cd_atendente'] != "")
	{
		$qr_anterior.= " AND a.cod_atendente = ".$_REQUEST['cd_atendente'];
	}					   
	$ob_resul = pg_query($db, $qr_anterior);
	$ob_reg   = pg_fetch_object($ob_resul); 
	$qt_anterior_concluida = $ob_reg->qt_ant_concluida;

	#### CONCLUIDAS (TESTE RELEVANTE E ENCERRADA AUTOMATICO) ####
	$qr_anterior = "
		SELECT COUNT(a.numero) as qt_ant_concluida 
		  FROM projetos.atividades a
		 WHERE a.status_atual = 'CONC'
		   AND a.area         = 'GI'	
		   AND a.fl_encerrado_automatico = 'S'
		   AND a.fl_teste_relevante      = 'S'						   
		   AND DATE_TRUNC('day',a.dt_fim_real) < TO_DATE('01/01/".$ano."','DD/MM/YYYY')
		   AND a.tipo         = 'S'
	";
	if($_REQUEST['cd_atendente'] != "")
	{
		$qr_anterior.= " AND a.cod_atendente = ".$_REQUEST['cd_atendente'];
	}
	$ob_resul = pg_query($db, $qr_anterior);
	$ob_reg   = pg_fetch_object($ob_resul); 
	$qt_anterior_concluida_crit_auto = $ob_reg->qt_ant_concluida;	

	#### CONCLUIDA (TESTE RELEVANTE E ENCERRADA PELO USUARIO) ####
	$qr_anterior = "
					SELECT COUNT(a.numero) as qt_ant_concluida 
					  FROM projetos.atividades a
					 WHERE a.status_atual = 'CONC'
					   AND a.area         = 'GI'	
					   AND a.fl_encerrado_automatico = 'N'
					   AND a.fl_teste_relevante      = 'S'						   
					   AND DATE_TRUNC('day',a.dt_fim_real) < TO_DATE('01/01/".$ano."','DD/MM/YYYY')
					   AND a.tipo         = 'S'
				   ";	
	if($_REQUEST['cd_atendente'] != "")
	{
		$qr_anterior.= " AND a.cod_atendente = ".$_REQUEST['cd_atendente'];
	}					   
	$ob_resul = pg_query($db, $qr_anterior);
	$ob_reg   = pg_fetch_object($ob_resul); 
	$qt_anterior_concluida_crit_user = $ob_reg->qt_ant_concluida;
	
	#### CONCLUIDA (TESTE NÃO RELEVANTE) ####
	$qr_anterior = "
		SELECT COUNT(a.numero) as qt_ant_concluida 
		  FROM projetos.atividades a
		 WHERE a.status_atual = 'CONC'
		   AND a.area         = 'GI'	
		   AND a.fl_teste_relevante      = 'N'						   
		   AND DATE_TRUNC('day',a.dt_fim_real) < TO_DATE('01/01/".$ano."','DD/MM/YYYY')
		   AND a.tipo         = 'S'
	   ";	
	if($_REQUEST['cd_atendente'] != "")
	{
		$qr_anterior.= " AND a.cod_atendente = ".$_REQUEST['cd_atendente'];
	}					   
	$ob_resul = pg_query($db, $qr_anterior);
	$ob_reg   = pg_fetch_object($ob_resul); 
	$qt_anterior_concluida_crit_nao = $ob_reg->qt_ant_concluida;		

	#### CANCELADAS ####
	$qr_anterior = " 
					SELECT COUNT(a.numero) as qt_ant_cancelada 
					  FROM projetos.atividades a
					 WHERE a.status_atual IN ('CANC','AGDF')
					   AND a.area         = 'GI'					 
					   --AND a.dt_fim_real  < TO_DATE('01/01/".$ano."','DD/MM/YYYY')
					   AND DATE_TRUNC('day',a.dt_fim_real) < TO_DATE('01/01/".$ano."','DD/MM/YYYY') 
					   AND a.tipo         = 'S'
				   ";	
	if($_REQUEST['cd_atendente'] != "")
	{
		$qr_anterior.= " AND a.cod_atendente = ".$_REQUEST['cd_atendente'];
	}					   
	$ob_resul = pg_query($db, $qr_anterior);
	$ob_reg   = pg_fetch_object($ob_resul); 
	$qt_anterior_cancelada = $ob_reg->qt_ant_cancelada;
	
	#### SUSPENSAS ####
	$qr_anterior = " 
					SELECT COUNT(DISTINCT(a.numero)) as qt_ant_suspensa 
					  FROM projetos.atividades a,
					       projetos.atividade_historico h
					 WHERE a.status_atual   IN ('SUSP','AUSR','ADIR')
					   AND a.numero         = h.cd_atividade
					   AND h.dt_inicio_prev = (SELECT MAX(h2.dt_inicio_prev) FROM projetos.atividade_historico h2 WHERE h2.cd_atividade = a.numero)
					   AND h.status_atual   IN ('SUSP','AUSR','ADIR')
					   AND a.area           = 'GI'
					   --AND h.dt_inicio_prev < TO_DATE('01/01/".$ano."','DD/MM/YYYY')  
					   AND DATE_TRUNC('day',h.dt_inicio_prev) < TO_DATE('01/01/".$ano."','DD/MM/YYYY') 					   
					   AND a.tipo           = 'S'
				   ";
	if($_REQUEST['cd_atendente'] != "")
	{
		$qr_anterior.= " AND a.cod_atendente = ".$_REQUEST['cd_atendente'];
	}					   
	$ob_resul = pg_query($db, $qr_anterior);
	$ob_reg   = pg_fetch_object($ob_resul); 
	$qt_anterior_suspensa = $ob_reg->qt_ant_suspensa;
	###############################################################################################################
	
	#### ATIVIDADES DE SUPORTE - MENSAL #####
	#### ABERTAS ####
	$qr_mensal = "
					SELECT EXTRACT(month FROM a.dt_cad) AS nr_mes, 
					       COUNT(a.numero) AS qt_atividade 
					  FROM projetos.atividades a
					 WHERE a.area   = 'GI'
					   --AND a.dt_cad BETWEEN TO_DATE('01/01/".$ano."','DD/MM/YYYY') AND TO_DATE('31/12/".$ano."','DD/MM/YYYY') 
					   AND DATE_TRUNC('day',a.dt_cad) BETWEEN TO_DATE('01/01/".$ano."','DD/MM/YYYY') AND TO_DATE('31/12/".$ano."','DD/MM/YYYY') 
					   AND a.tipo   = 'S'
			     ";
	if($_REQUEST['cd_atendente'] != "")
	{
		$qr_mensal.= " AND a.cod_atendente = ".$_REQUEST['cd_atendente'];
	}
	$qr_mensal.= "
					   GROUP BY extract(month FROM a.dt_cad) 
					   ORDER BY extract(month FROM a.dt_cad)
	             ";

	$ob_resul = pg_query($db, $qr_mensal);
    $ar_mes_aberta = montaMeses($ob_resul);
	
	#### CONCLUIDAS ####
	$qr_mensal = "
					SELECT EXTRACT(month FROM a.dt_fim_real) AS nr_mes, 
					       COUNT(a.numero) AS qt_atividade 
					  FROM projetos.atividades a
					 WHERE a.status_atual = 'CONC'
					   AND a.area         = 'GI'
					   --AND a.dt_fim_real  BETWEEN TO_DATE('01/01/".$ano."','DD/MM/YYYY') AND TO_DATE('31/12/".$ano."','DD/MM/YYYY') 
					   AND DATE_TRUNC('day',a.dt_fim_real) BETWEEN TO_DATE('01/01/".$ano."','DD/MM/YYYY') AND TO_DATE('31/12/".$ano."','DD/MM/YYYY') 
					   AND a.tipo         = 'S'
";
	if($_REQUEST['cd_atendente'] != "")
	{
		$qr_mensal.= " AND a.cod_atendente = ".$_REQUEST['cd_atendente'];
	}
	$qr_mensal.= "					   
					 GROUP BY extract(month FROM a.dt_fim_real) 
					 ORDER BY extract(month FROM a.dt_fim_real)	
                 ";	
 
	$ob_resul = pg_query($db, $qr_mensal);
    $ar_mes_concluida = montaMeses($ob_resul);
	
	#### CONCLUIDA (TESTE RELEVANTE E ENCERRADA AUTOMATICO) ####
	$qr_mensal = "
					SELECT EXTRACT(month FROM a.dt_fim_real) AS nr_mes, 
					       COUNT(a.numero) AS qt_atividade 
					  FROM projetos.atividades a
					 WHERE a.status_atual = 'CONC'
					   AND a.area         = 'GI'
					   AND a.fl_encerrado_automatico = 'S'
					   AND a.fl_teste_relevante      = 'S'					   
					   AND DATE_TRUNC('day',a.dt_fim_real) BETWEEN TO_DATE('01/01/".$ano."','DD/MM/YYYY') AND TO_DATE('31/12/".$ano."','DD/MM/YYYY') 
					   AND a.tipo         = 'S'
                 ";
	if($_REQUEST['cd_atendente'] != "")
	{
		$qr_mensal.= " AND a.cod_atendente = ".$_REQUEST['cd_atendente'];
	}
	$qr_mensal.= "					   
					 GROUP BY extract(month FROM a.dt_fim_real) 
					 ORDER BY extract(month FROM a.dt_fim_real)	
                 ";	
	$ob_resul = pg_query($db, $qr_mensal);
    $ar_mes_concluida_crit_auto = montaMeses($ob_resul);	
	
	#### CONCLUIDA (TESTE RELEVANTE E ENCERRADA PELO USUARIO) ####
	$qr_mensal = "
					SELECT EXTRACT(month FROM a.dt_fim_real) AS nr_mes, 
					       COUNT(a.numero) AS qt_atividade 
					  FROM projetos.atividades a
					 WHERE a.status_atual = 'CONC'
					   AND a.area         = 'GI'
					   AND a.fl_encerrado_automatico = 'N'
					   AND a.fl_teste_relevante      = 'S'				   
					   AND DATE_TRUNC('day',a.dt_fim_real) BETWEEN TO_DATE('01/01/".$ano."','DD/MM/YYYY') AND TO_DATE('31/12/".$ano."','DD/MM/YYYY') 
					   AND a.tipo         = 'S'
";
	if($_REQUEST['cd_atendente'] != "")
	{
		$qr_mensal.= " AND a.cod_atendente = ".$_REQUEST['cd_atendente'];
	}
	$qr_mensal.= "					   
					 GROUP BY extract(month FROM a.dt_fim_real) 
					 ORDER BY extract(month FROM a.dt_fim_real)	
                 ";	
	$ob_resul = pg_query($db, $qr_mensal);
    $ar_mes_concluida_crit_user = montaMeses($ob_resul);	
	
	#### CONCLUIDA (TESTE NÃO RELEVANTE) ####
	$qr_mensal = "
					SELECT EXTRACT(month FROM a.dt_fim_real) AS nr_mes, 
					       COUNT(a.numero) AS qt_atividade 
					  FROM projetos.atividades a
					 WHERE a.status_atual = 'CONC'
					   AND a.area         = 'GI'
					   AND a.fl_teste_relevante      = 'N'				   
					   AND DATE_TRUNC('day',a.dt_fim_real) BETWEEN TO_DATE('01/01/".$ano."','DD/MM/YYYY') AND TO_DATE('31/12/".$ano."','DD/MM/YYYY') 
					   AND a.tipo         = 'S'
";
	if($_REQUEST['cd_atendente'] != "")
	{
		$qr_mensal.= " AND a.cod_atendente = ".$_REQUEST['cd_atendente'];
	}
	$qr_mensal.= "					   
					 GROUP BY extract(month FROM a.dt_fim_real) 
					 ORDER BY extract(month FROM a.dt_fim_real)	
                 ";	
	$ob_resul = pg_query($db, $qr_mensal);
    $ar_mes_concluida_crit_nao = montaMeses($ob_resul);			
	
	#### CANCELADAS ####
	$qr_mensal = " 
					SELECT EXTRACT(month FROM a.dt_fim_real) AS nr_mes, 
					       COUNT(a.numero) AS qt_atividade 
					  FROM projetos.atividades a
					 WHERE a.status_atual IN ('CANC','AGDF')
					   AND a.area         = 'GI'					 
					   --AND a.dt_fim_real  BETWEEN TO_DATE('01/01/".$ano."','DD/MM/YYYY') AND TO_DATE('31/12/".$ano."','DD/MM/YYYY') 
					   AND DATE_TRUNC('day',a.dt_fim_real) BETWEEN TO_DATE('01/01/".$ano."','DD/MM/YYYY') AND TO_DATE('31/12/".$ano."','DD/MM/YYYY') 
					   AND a.tipo         = 'S'	
";
	if($_REQUEST['cd_atendente'] != "")
	{
		$qr_mensal.= " AND a.cod_atendente = ".$_REQUEST['cd_atendente'];
	}
	$qr_mensal.= "					   
					 GROUP BY extract(month FROM a.dt_fim_real) 
					 ORDER BY extract(month FROM a.dt_fim_real)						   
				 ";
	$ob_resul = pg_query($db, $qr_mensal);
	$ar_mes_cancelada = montaMeses($ob_resul);	
	
	#### SUSPENSAS ####
	$qr_mensal = " 
					SELECT EXTRACT(month FROM h.dt_inicio_prev) AS nr_mes, 
					       COUNT(DISTINCT(a.numero)) AS qt_atividade 
					  FROM projetos.atividades a,
					       projetos.atividade_historico h
					 WHERE a.status_atual    IN ('SUSP','AUSR','ADIR')
					   AND a.numero          = h.cd_atividade
					   AND h.dt_inicio_prev  = (SELECT MAX(h2.dt_inicio_prev) FROM projetos.atividade_historico h2 WHERE h2.cd_atividade = a.numero)
					   AND h.status_atual    IN ('SUSP','AUSR','ADIR')
					   --AND h.dt_inicio_prev  BETWEEN TO_DATE('01/01/".$ano."','DD/MM/YYYY') AND TO_DATE('31/12/".$ano."','DD/MM/YYYY') 
					   AND DATE_TRUNC('day',h.dt_inicio_prev) BETWEEN TO_DATE('01/01/".$ano."','DD/MM/YYYY') AND TO_DATE('31/12/".$ano."','DD/MM/YYYY') 
					   AND a.area            = 'GI'					 
					   AND a.tipo            = 'S'	
					   ";
	if($_REQUEST['cd_atendente'] != "")
	{
		$qr_mensal.= " AND a.cod_atendente = ".$_REQUEST['cd_atendente'];
	}
	$qr_mensal.= "
					 GROUP BY extract(month FROM h.dt_inicio_prev) 
					 ORDER BY extract(month FROM h.dt_inicio_prev)					 
				 ";
			 
	$ob_resul = pg_query($db, $qr_mensal);
	$ar_mes_suspensa = montaMeses($ob_resul);	
	
	$qt_resumo_anterior_aberta    += $qt_anterior_aberta;
	$qt_resumo_anterior_concluida_crit_auto += $qt_anterior_concluida_crit_auto;
	$qt_resumo_anterior_concluida_crit_user += $qt_anterior_concluida_crit_user;
	$qt_resumo_anterior_concluida_crit_nao  += $qt_anterior_concluida_crit_nao;
	$qt_resumo_anterior_concluida += $qt_anterior_concluida;
	$qt_resumo_anterior_cancelada += $qt_anterior_cancelada;
	$qt_resumo_anterior_suspensa  += $qt_anterior_suspensa;
	$qt_resumo_anterior_atendida  += ($qt_anterior_concluida + $qt_anterior_cancelada);	
	
	$qt_tot_mes_aberta    = 0;
	$qt_tot_mes_concluida_crit_auto = 0;
	$qt_tot_mes_concluida_crit_user = 0;
	$qt_tot_mes_concluida_crit_nao = 0;
	$qt_tot_mes_concluida = 0;
	$qt_tot_mes_cancelada = 0;
	$qt_tot_mes_suspensa  = 0;
	$qt_tot_mes_atendida  = 0;
	$nr_conta = 1;
	while($nr_conta <= 12) 
	{
		$qt_resumo_mes_aberta[$nr_conta]  += $ar_mes_aberta[$nr_conta];
		$qt_resumo_mes_concluida_crit_auto[$nr_conta] += $ar_mes_concluida_crit_auto[$nr_conta];
		$qt_resumo_mes_concluida_crit_user[$nr_conta] += $ar_mes_concluida_crit_user[$nr_conta];
		$qt_resumo_mes_concluida_crit_nao[$nr_conta]  += $ar_mes_concluida_crit_nao[$nr_conta];
		$qt_resumo_mes_concluida[$nr_conta] += $ar_mes_concluida[$nr_conta];
		$qt_resumo_mes_cancelada[$nr_conta] += $ar_mes_cancelada[$nr_conta];
		$qt_resumo_mes_suspensa[$nr_conta]  += $ar_mes_suspensa[$nr_conta];		
		
		$qt_anterior_aberta    += $ar_mes_aberta[$nr_conta];
		$qt_anterior_concluida_crit_auto += $ar_mes_concluida_crit_auto[$nr_conta];
		$qt_anterior_concluida_crit_user += $ar_mes_concluida_crit_user[$nr_conta];
		$qt_anterior_concluida_crit_nao  += $ar_mes_concluida_crit_nao[$nr_conta];
		$qt_anterior_concluida += $ar_mes_concluida[$nr_conta];
		$qt_anterior_cancelada += $ar_mes_cancelada[$nr_conta];
		$qt_anterior_suspensa  += $ar_mes_suspensa[$nr_conta];		
		
		//$tpl->newBlock('qt_ano_mes_suporte');
		if(($nr_conta % 2) != 0)
		{
			//$tpl->assign('bg_color', '#F4F4F4');
		}		
		
		$nr_zero = "";
		if($nr_conta < 10)
		{
			$nr_zero = 0;
		}
		
		/*$tpl->assign('mes_ano',          $nr_zero.$nr_conta."/".$ano);
		$tpl->assign('qt_mes_aberta',    $ar_mes_aberta[$nr_conta]);
		$tpl->assign('qt_mes_concluida_crit_auto', $ar_mes_concluida_crit_auto[$nr_conta]);
		$tpl->assign('qt_mes_concluida_crit_user', $ar_mes_concluida_crit_user[$nr_conta]);
		$tpl->assign('qt_mes_concluida_crit_nao',  $ar_mes_concluida_crit_nao[$nr_conta]);
		$tpl->assign('qt_mes_concluida', $ar_mes_concluida[$nr_conta]);
		$tpl->assign('qt_mes_cancelada', $ar_mes_cancelada[$nr_conta]);
		$tpl->assign('qt_mes_suspensa',  $ar_mes_suspensa[$nr_conta]);
		$tpl->assign('qt_mes_atendida',  ($ar_mes_concluida[$nr_conta]+$ar_mes_cancelada[$nr_conta]));
		if($ar_mes_aberta[$nr_conta] > 0)
		{
			$tpl->assign('qt_mes_atendida_perc',  number_format((($ar_mes_concluida[$nr_conta]+$ar_mes_cancelada[$nr_conta]) * 100)/ $ar_mes_aberta[$nr_conta],2));
		}		

		$tpl->assign('qt_mes_acumulado_aberta',    $qt_anterior_aberta);
		$tpl->assign('qt_mes_acumulado_concluida_crit_auto', $qt_anterior_concluida_crit_auto);
		$tpl->assign('qt_mes_acumulado_concluida_crit_user', $qt_anterior_concluida_crit_user);
		$tpl->assign('qt_mes_acumulado_concluida_crit_nao',  $qt_anterior_concluida_crit_nao);
		$tpl->assign('qt_mes_acumulado_concluida', $qt_anterior_concluida);
		$tpl->assign('qt_mes_acumulado_cancelada', $qt_anterior_cancelada);
		$tpl->assign('qt_mes_acumulado_suspensa',  $qt_anterior_suspensa);
		$tpl->assign('qt_mes_acumulado_atendida',  ($qt_anterior_concluida + $qt_anterior_cancelada));
		if($qt_anterior_aberta > 0)
		{		
			$tpl->assign('qt_mes_acumulado_atendida_perc',  number_format((($qt_anterior_concluida + $qt_anterior_cancelada) * 100)/ $qt_anterior_aberta,2));		
		}*/		
		
		$qt_tot_mes_aberta    += $ar_mes_aberta[$nr_conta];
		$qt_tot_mes_concluida_crit_auto += $ar_mes_concluida_crit_auto[$nr_conta];
		$qt_tot_mes_concluida_crit_user += $ar_mes_concluida_crit_user[$nr_conta];
		$qt_tot_mes_concluida_crit_nao  += $ar_mes_concluida_crit_nao[$nr_conta];
		$qt_tot_mes_concluida += $ar_mes_concluida[$nr_conta];
		$qt_tot_mes_cancelada += $ar_mes_cancelada[$nr_conta];
		$qt_tot_mes_suspensa  += $ar_mes_suspensa[$nr_conta];
		$qt_tot_mes_atendida  += ($ar_mes_concluida[$nr_conta]+$ar_mes_cancelada[$nr_conta]);
		
		$nr_conta++;
	}	
	
	/*$tpl->newBlock('qt_tot_suporte');
	$tpl->assign('qt_tot_mes_aberta',    $qt_tot_mes_aberta);
	$tpl->assign('qt_tot_mes_concluida_crit_auto', $qt_tot_mes_concluida_crit_auto);
	$tpl->assign('qt_tot_mes_concluida_crit_user', $qt_tot_mes_concluida_crit_user);
	$tpl->assign('qt_tot_mes_concluida_crit_nao',  $qt_tot_mes_concluida_crit_nao);
	$tpl->assign('qt_tot_mes_concluida', $qt_tot_mes_concluida);
	$tpl->assign('qt_tot_mes_cancelada', $qt_tot_mes_cancelada);
	$tpl->assign('qt_tot_mes_suspensa',  $qt_tot_mes_suspensa);
	$tpl->assign('qt_tot_mes_atendida',  $qt_tot_mes_atendida);
	if($qt_tot_mes_aberta > 0)
	{
		$tpl->assign('qt_tot_mes_atendida_perc',  number_format(($qt_tot_mes_atendida * 100) / $qt_tot_mes_aberta,2));
	}
	
	$tpl->assign('qt_tot_acumulado_aberta',    $qt_anterior_aberta);
	$tpl->assign('qt_tot_acumulado_concluida_crit_auto', $qt_anterior_concluida_crit_auto);
	$tpl->assign('qt_tot_acumulado_concluida_crit_user', $qt_anterior_concluida_crit_user);
	$tpl->assign('qt_tot_acumulado_concluida_crit_nao',  $qt_anterior_concluida_crit_nao);
	$tpl->assign('qt_tot_acumulado_concluida', $qt_anterior_concluida);
	$tpl->assign('qt_tot_acumulado_cancelada', $qt_anterior_cancelada);
	$tpl->assign('qt_tot_acumulado_suspensa',  $qt_anterior_suspensa);
	$tpl->assign('qt_tot_acumulado_atendida',  ($qt_anterior_concluida + $qt_anterior_cancelada));		
	if($qt_anterior_aberta > 0)
	{
		$tpl->assign('qt_tot_acumulado_atendida_perc',  number_format((($qt_anterior_concluida + $qt_anterior_cancelada) * 100) / $qt_anterior_aberta,2));		
	}*/
	##########################################################################################################################
	
	
	
	##################################################### ATIVIDADES DE SISTEMAS #############################################
	#### ATIVIDADES DE SISTEMAS - ACUMULADO ####
	#### ABERTAS ####
	$qr_anterior = "
					SELECT COUNT(a.numero) as qt_ant_aberta 
					  FROM projetos.atividades a
					 WHERE a.area   = 'GI'
					   --AND a.dt_cad < TO_DATE('01/01/".$ano."','DD/MM/YYYY') 
					   AND DATE_TRUNC('day',a.dt_cad) < TO_DATE('01/01/".$ano."','DD/MM/YYYY')
					   AND a.tipo   NOT IN('S','L')
				   ";
	if($_REQUEST['cd_atendente'] != "")
	{
		$qr_anterior.= " AND a.cod_atendente = ".$_REQUEST['cd_atendente'];
	}					   
	$ob_resul = pg_query($db, $qr_anterior);
	$ob_reg   = pg_fetch_object($ob_resul); 
	$qt_anterior_aberta = $ob_reg->qt_ant_aberta;
	
	#### CONCLUIDAS ####
	$qr_anterior = "
					SELECT COUNT(a.numero) as qt_ant_concluida 
					  FROM projetos.atividades a
					 WHERE a.status_atual = 'CONC'
					   AND a.area         = 'GI'					 
					   AND DATE_TRUNC('day',a.dt_fim_real) < TO_DATE('01/01/".$ano."','DD/MM/YYYY')
					   AND a.tipo         NOT IN('S','L')
				   ";	
	if($_REQUEST['cd_atendente'] != "")
	{
		$qr_anterior.= " AND a.cod_atendente = ".$_REQUEST['cd_atendente'];
	}					   
	$ob_resul = pg_query($db, $qr_anterior);
	$ob_reg   = pg_fetch_object($ob_resul); 
	$qt_anterior_concluida = $ob_reg->qt_ant_concluida;

	#### CONCLUIDAS (TESTE RELEVANTE E ENCERRADA AUTOMATICO) ####
	$qr_anterior = "
					SELECT COUNT(a.numero) as qt_ant_concluida 
					  FROM projetos.atividades a
					 WHERE a.status_atual = 'CONC'
					   AND a.area         = 'GI'	
					   AND a.fl_encerrado_automatico = 'S'
					   AND a.fl_teste_relevante      = 'S'						   
					   AND DATE_TRUNC('day',a.dt_fim_real) < TO_DATE('01/01/".$ano."','DD/MM/YYYY')
					   AND a.tipo         NOT IN('S','L')
				   ";	
	if($_REQUEST['cd_atendente'] != "")
	{
		$qr_anterior.= " AND a.cod_atendente = ".$_REQUEST['cd_atendente'];
	}					   
	$ob_resul = pg_query($db, $qr_anterior);
	$ob_reg   = pg_fetch_object($ob_resul); 
	$qt_anterior_concluida_crit_auto = $ob_reg->qt_ant_concluida;	
	

	#### CONCLUIDA (TESTE RELEVANTE E ENCERRADA PELO USUARIO) ####
	$qr_anterior = "
					SELECT COUNT(a.numero) as qt_ant_concluida 
					  FROM projetos.atividades a
					 WHERE a.status_atual = 'CONC'
					   AND a.area         = 'GI'	
					   AND a.fl_encerrado_automatico = 'N'
					   AND a.fl_teste_relevante      = 'S'						   
					   AND DATE_TRUNC('day',a.dt_fim_real) < TO_DATE('01/01/".$ano."','DD/MM/YYYY')
					   AND a.tipo         NOT IN('S','L')
				   ";	
	if($_REQUEST['cd_atendente'] != "")
	{
		$qr_anterior.= " AND a.cod_atendente = ".$_REQUEST['cd_atendente'];
	}					   
	$ob_resul = pg_query($db, $qr_anterior);
	$ob_reg   = pg_fetch_object($ob_resul); 
	$qt_anterior_concluida_crit_user = $ob_reg->qt_ant_concluida;
	
	
	#### CONCLUIDA (TESTE NÃO RELEVANTE) ####
	$qr_anterior = "
					SELECT COUNT(a.numero) as qt_ant_concluida 
					  FROM projetos.atividades a
					 WHERE a.status_atual = 'CONC'
					   AND a.area         = 'GI'	
					   AND a.fl_teste_relevante      = 'N'						   
					   AND DATE_TRUNC('day',a.dt_fim_real) < TO_DATE('01/01/".$ano."','DD/MM/YYYY')
					   AND a.tipo         NOT IN('S','L')
				   ";	
	if($_REQUEST['cd_atendente'] != "")
	{
		$qr_anterior.= " AND a.cod_atendente = ".$_REQUEST['cd_atendente'];
	}					   
	$ob_resul = pg_query($db, $qr_anterior);
	$ob_reg   = pg_fetch_object($ob_resul); 
	$qt_anterior_concluida_crit_nao = $ob_reg->qt_ant_concluida;	

	
	#### CANCELADAS ####
	$qr_anterior = " 
	                SELECT COUNT(a.numero) as qt_ant_cancelada 
					  FROM projetos.atividades a
					 WHERE a.status_atual IN ('CANC','AGDF')
					   AND a.area         = 'GI'					 
					   --AND a.dt_fim_real  < TO_DATE('01/01/".$ano."','DD/MM/YYYY')
					   AND DATE_TRUNC('day',a.dt_fim_real) < TO_DATE('01/01/".$ano."','DD/MM/YYYY')
					   AND a.tipo         NOT IN('S','L')
				   ";
	if($_REQUEST['cd_atendente'] != "")
	{
		$qr_anterior.= " AND a.cod_atendente = ".$_REQUEST['cd_atendente'];
	}					   
	$ob_resul = pg_query($db, $qr_anterior);
	$ob_reg   = pg_fetch_object($ob_resul); 
	$qt_anterior_cancelada = $ob_reg->qt_ant_cancelada;
	
	#### SUSPENSAS ####
	$qr_anterior = " 
					SELECT COUNT(DISTINCT(a.numero)) as qt_ant_suspensa 
					  FROM projetos.atividades a,
					       projetos.atividade_historico h
					 WHERE a.status_atual   IN ('SUSP','AUSR','ADIR')
					   AND a.numero         = h.cd_atividade
					   AND h.dt_inicio_prev = (SELECT MAX(h2.dt_inicio_prev) FROM projetos.atividade_historico h2 WHERE h2.cd_atividade = a.numero)
					   AND h.status_atual   IN ('SUSP','AUSR','ADIR')
					   AND a.area           = 'GI'
					   --AND h.dt_inicio_prev < TO_DATE('01/01/".$ano."','DD/MM/YYYY')  	
					   AND DATE_TRUNC('day',h.dt_inicio_prev) < TO_DATE('01/01/".$ano."','DD/MM/YYYY')
					   AND a.tipo           NOT IN('S','L')
				   ";
	if($_REQUEST['cd_atendente'] != "")
	{
		$qr_anterior.= " AND a.cod_atendente = ".$_REQUEST['cd_atendente'];
	}				   
	$ob_resul = pg_query($db, $qr_anterior);
	$ob_reg   = pg_fetch_object($ob_resul); 
	$qt_anterior_suspensa = $ob_reg->qt_ant_suspensa;
	###############################################################################################################
	
	#### ATIVIDADES DE SISTEMAS - MENSAL #####
	#### ABERTAS ####
	$qr_mensal = "
					SELECT EXTRACT(month FROM a.dt_cad) AS nr_mes, 
					       COUNT(a.numero) AS qt_atividade 
					  FROM projetos.atividades a
					 WHERE a.area   = 'GI'
					   --AND a.dt_cad BETWEEN TO_DATE('01/01/".$ano."','DD/MM/YYYY') AND TO_DATE('31/12/".$ano."','DD/MM/YYYY') 
					   AND DATE_TRUNC('day',a.dt_cad) BETWEEN TO_DATE('01/01/".$ano."','DD/MM/YYYY') AND TO_DATE('31/12/".$ano."','DD/MM/YYYY') 
					   AND a.tipo   NOT IN('S','L')
				 ";
	if($_REQUEST['cd_atendente'] != "")
	{
		$qr_mensal.= " AND a.cod_atendente = ".$_REQUEST['cd_atendente'];
	}
	$qr_mensal.= "					   
					 GROUP BY extract(month FROM a.dt_cad) 
					 ORDER BY extract(month FROM a.dt_cad)
	             ";
	$ob_resul = pg_query($db, $qr_mensal);
    $ar_mes_aberta = montaMeses($ob_resul);
	
	#### CONCLUIDAS TOTAL ####
	$qr_mensal = "
					SELECT EXTRACT(month FROM a.dt_fim_real) AS nr_mes, 
					       COUNT(a.numero) AS qt_atividade 
					  FROM projetos.atividades a
					 WHERE a.status_atual = 'CONC'
					   AND a.area         = 'GI'
					   --AND a.dt_fim_real  BETWEEN TO_DATE('01/01/".$ano."','DD/MM/YYYY') AND TO_DATE('31/12/".$ano."','DD/MM/YYYY') 
					   AND DATE_TRUNC('day',a.dt_fim_real) BETWEEN TO_DATE('01/01/".$ano."','DD/MM/YYYY') AND TO_DATE('31/12/".$ano."','DD/MM/YYYY') 
					   AND a.tipo         NOT IN('S','L')
";
	if($_REQUEST['cd_atendente'] != "")
	{
		$qr_mensal.= " AND a.cod_atendente = ".$_REQUEST['cd_atendente'];
	}
	$qr_mensal.= "					   
					 GROUP BY extract(month FROM a.dt_fim_real) 
					 ORDER BY extract(month FROM a.dt_fim_real)	
                 ";	
	$ob_resul = pg_query($db, $qr_mensal);
    $ar_mes_concluida = montaMeses($ob_resul);
	
	#### CONCLUIDA (TESTE RELEVANTE E ENCERRADA AUTOMATICO) ####
	$qr_mensal = "
					SELECT EXTRACT(month FROM a.dt_fim_real) AS nr_mes, 
					       COUNT(a.numero) AS qt_atividade 
					  FROM projetos.atividades a
					 WHERE a.status_atual = 'CONC'
					   AND a.area         = 'GI'
					   AND a.fl_encerrado_automatico = 'S'
					   AND a.fl_teste_relevante      = 'S'					   
					   AND DATE_TRUNC('day',a.dt_fim_real) BETWEEN TO_DATE('01/01/".$ano."','DD/MM/YYYY') AND TO_DATE('31/12/".$ano."','DD/MM/YYYY') 
					   AND a.tipo         NOT IN('S','L')
                 ";
	if($_REQUEST['cd_atendente'] != "")
	{
		$qr_mensal.= " AND a.cod_atendente = ".$_REQUEST['cd_atendente'];
	}
	$qr_mensal.= "					   
					 GROUP BY extract(month FROM a.dt_fim_real) 
					 ORDER BY extract(month FROM a.dt_fim_real)	
                 ";	
	$ob_resul = pg_query($db, $qr_mensal);
    $ar_mes_concluida_crit_auto = montaMeses($ob_resul);	
	
	#### CONCLUIDA (TESTE RELEVANTE E ENCERRADA PELO USUARIO) ####
	$qr_mensal = "
					SELECT EXTRACT(month FROM a.dt_fim_real) AS nr_mes, 
					       COUNT(a.numero) AS qt_atividade 
					  FROM projetos.atividades a
					 WHERE a.status_atual = 'CONC'
					   AND a.area         = 'GI'
					   AND a.fl_encerrado_automatico = 'N'
					   AND a.fl_teste_relevante      = 'S'				   
					   AND DATE_TRUNC('day',a.dt_fim_real) BETWEEN TO_DATE('01/01/".$ano."','DD/MM/YYYY') AND TO_DATE('31/12/".$ano."','DD/MM/YYYY') 
					   AND a.tipo         NOT IN('S','L')
";
	if($_REQUEST['cd_atendente'] != "")
	{
		$qr_mensal.= " AND a.cod_atendente = ".$_REQUEST['cd_atendente'];
	}
	$qr_mensal.= "					   
					 GROUP BY extract(month FROM a.dt_fim_real) 
					 ORDER BY extract(month FROM a.dt_fim_real)	
                 ";	
	$ob_resul = pg_query($db, $qr_mensal);
    $ar_mes_concluida_crit_user = montaMeses($ob_resul);	
	
	#### CONCLUIDA (TESTE NÃO RELEVANTE) ####
	$qr_mensal = "
					SELECT EXTRACT(month FROM a.dt_fim_real) AS nr_mes, 
					       COUNT(a.numero) AS qt_atividade 
					  FROM projetos.atividades a
					 WHERE a.status_atual = 'CONC'
					   AND a.area         = 'GI'
					   AND a.fl_teste_relevante      = 'N'				   
					   AND DATE_TRUNC('day',a.dt_fim_real) BETWEEN TO_DATE('01/01/".$ano."','DD/MM/YYYY') AND TO_DATE('31/12/".$ano."','DD/MM/YYYY') 
					   AND a.tipo         NOT IN('S','L')
";
	if($_REQUEST['cd_atendente'] != "")
	{
		$qr_mensal.= " AND a.cod_atendente = ".$_REQUEST['cd_atendente'];
	}
	$qr_mensal.= "					   
					 GROUP BY extract(month FROM a.dt_fim_real) 
					 ORDER BY extract(month FROM a.dt_fim_real)	
                 ";	
	$ob_resul = pg_query($db, $qr_mensal);
    $ar_mes_concluida_crit_nao = montaMeses($ob_resul);		
	
	#### CANCELADAS ####
	$qr_mensal = " 
					SELECT EXTRACT(month FROM a.dt_fim_real) AS nr_mes, 
					       COUNT(a.numero) AS qt_atividade 
					  FROM projetos.atividades a
					 WHERE a.status_atual IN ('CANC','AGDF')
					   AND a.area         = 'GI'					 
					   --AND a.dt_fim_real  BETWEEN TO_DATE('01/01/".$ano."','DD/MM/YYYY') AND TO_DATE('31/12/".$ano."','DD/MM/YYYY') 
					   AND DATE_TRUNC('day',a.dt_fim_real) BETWEEN TO_DATE('01/01/".$ano."','DD/MM/YYYY') AND TO_DATE('31/12/".$ano."','DD/MM/YYYY') 
					   AND a.tipo         NOT IN('S','L')
";
	if($_REQUEST['cd_atendente'] != "")
	{
		$qr_mensal.= " AND a.cod_atendente = ".$_REQUEST['cd_atendente'];
	}
	$qr_mensal.= "					   
					 GROUP BY extract(month FROM a.dt_fim_real) 
					 ORDER BY extract(month FROM a.dt_fim_real)						   
				 ";
	$ob_resul = pg_query($db, $qr_mensal);
	$ar_mes_cancelada = montaMeses($ob_resul);	
	
	#### SUSPENSAS ####
	$qr_mensal = " 
					SELECT EXTRACT(month FROM h.dt_inicio_prev) AS nr_mes, 
					       COUNT(DISTINCT(a.numero)) AS qt_atividade 
					  FROM projetos.atividades a,
					       projetos.atividade_historico h
					 WHERE a.status_atual    IN ('SUSP','AUSR','ADIR')
					   AND a.numero          = h.cd_atividade
					   AND h.dt_inicio_prev  = (SELECT MAX(h2.dt_inicio_prev) FROM projetos.atividade_historico h2 WHERE h2.cd_atividade = a.numero)
					   AND h.status_atual    IN ('SUSP','AUSR','ADIR')
					   --AND h.dt_inicio_prev  BETWEEN TO_DATE('01/01/".$ano."','DD/MM/YYYY') AND TO_DATE('31/12/".$ano."','DD/MM/YYYY') 
					   AND DATE_TRUNC('day',h.dt_inicio_prev) BETWEEN TO_DATE('01/01/".$ano."','DD/MM/YYYY') AND TO_DATE('31/12/".$ano."','DD/MM/YYYY') 
					   AND a.area            = 'GI'					 
					   AND a.tipo            NOT IN('S','L')
";
	if($_REQUEST['cd_atendente'] != "")
	{
		$qr_mensal.= " AND a.cod_atendente = ".$_REQUEST['cd_atendente'];
	}
	$qr_mensal.= "					   
					 GROUP BY extract(month FROM h.dt_inicio_prev) 
					 ORDER BY extract(month FROM h.dt_inicio_prev)					 
				 ";
	$ob_resul = pg_query($db, $qr_mensal);
	$ar_mes_suspensa = montaMeses($ob_resul);

	$qt_resumo_anterior_aberta    += $qt_anterior_aberta;
	$qt_resumo_anterior_concluida_crit_auto += $qt_anterior_concluida_crit_auto;
	$qt_resumo_anterior_concluida_crit_user += $qt_anterior_concluida_crit_user;
	$qt_resumo_anterior_concluida_crit_nao  += $qt_anterior_concluida_crit_nao;
	$qt_resumo_anterior_concluida += $qt_anterior_concluida;
	$qt_resumo_anterior_cancelada += $qt_anterior_cancelada;
	$qt_resumo_anterior_suspensa  += $qt_anterior_suspensa;
	$qt_resumo_anterior_atendida  += ($qt_anterior_concluida + $qt_anterior_cancelada);	
	
	/*$tpl->newBlock('qt_ano_anterior_sistema');
	$tpl->assign('qt_anterior_aberta',    $qt_anterior_aberta);
	$tpl->assign('qt_anterior_concluida_crit_auto', $qt_anterior_concluida_crit_auto);
	$tpl->assign('qt_anterior_concluida_crit_user', $qt_anterior_concluida_crit_user);
	$tpl->assign('qt_anterior_concluida_crit_nao',  $qt_anterior_concluida_crit_nao);
	$tpl->assign('qt_anterior_concluida', $qt_anterior_concluida);
	$tpl->assign('qt_anterior_cancelada', $qt_anterior_cancelada);
	$tpl->assign('qt_anterior_suspensa',  $qt_anterior_suspensa);
	$tpl->assign('qt_anterior_atendida',  ($qt_anterior_concluida + $qt_anterior_cancelada));
	if($qt_anterior_aberta > 0)
	{
		$tpl->assign('qt_anterior_atendida_perc',  number_format((($qt_anterior_concluida + $qt_anterior_cancelada) * 100)/$qt_anterior_aberta,2));
	}*/
	
	$qt_tot_mes_aberta    = 0;
	$qt_tot_mes_concluida_crit_auto = 0;
	$qt_tot_mes_concluida_crit_user = 0;
	$qt_tot_mes_concluida_crit_nao = 0;
	$qt_tot_mes_concluida = 0;
	$qt_tot_mes_cancelada = 0;
	$qt_tot_mes_suspensa  = 0;
	$qt_tot_mes_atendida  = 0;
	$nr_conta = 1;
	while($nr_conta <= 12) 
	{
		$qt_resumo_mes_aberta[$nr_conta]  += $ar_mes_aberta[$nr_conta];
		$qt_resumo_mes_concluida_crit_auto[$nr_conta] += $ar_mes_concluida_crit_auto[$nr_conta];
		$qt_resumo_mes_concluida_crit_user[$nr_conta] += $ar_mes_concluida_crit_user[$nr_conta];
		$qt_resumo_mes_concluida_crit_nao[$nr_conta]  += $ar_mes_concluida_crit_nao[$nr_conta];
		$qt_resumo_mes_concluida[$nr_conta] += $ar_mes_concluida[$nr_conta];
		$qt_resumo_mes_cancelada[$nr_conta] += $ar_mes_cancelada[$nr_conta];
		$qt_resumo_mes_suspensa[$nr_conta]  += $ar_mes_suspensa[$nr_conta];		
		
		$qt_anterior_aberta    += $ar_mes_aberta[$nr_conta];
		$qt_anterior_concluida_crit_auto += $ar_mes_concluida_crit_auto[$nr_conta];
		$qt_anterior_concluida_crit_user += $ar_mes_concluida_crit_user[$nr_conta];
		$qt_anterior_concluida_crit_nao  += $ar_mes_concluida_crit_nao[$nr_conta];
		$qt_anterior_concluida += $ar_mes_concluida[$nr_conta];
		$qt_anterior_cancelada += $ar_mes_cancelada[$nr_conta];
		$qt_anterior_suspensa  += $ar_mes_suspensa[$nr_conta];		
		
		/*$tpl->newBlock('qt_ano_mes_sistema');
		if(($nr_conta % 2) != 0)
		{
			$tpl->assign('bg_color', '#F4F4F4');
		}*/		
		
		$nr_zero = "";
		if($nr_conta < 10)
		{
			$nr_zero = 0;
		}

		/*$tpl->assign('mes_ano',          $nr_zero.$nr_conta."/".$ano);
		$tpl->assign('qt_mes_aberta',    $ar_mes_aberta[$nr_conta]);
		$tpl->assign('qt_mes_concluida_crit_auto', $ar_mes_concluida_crit_auto[$nr_conta]);
		$tpl->assign('qt_mes_concluida_crit_user', $ar_mes_concluida_crit_user[$nr_conta]);
		$tpl->assign('qt_mes_concluida_crit_nao',  $ar_mes_concluida_crit_nao[$nr_conta]);
		$tpl->assign('qt_mes_concluida', $ar_mes_concluida[$nr_conta]);
		$tpl->assign('qt_mes_cancelada', $ar_mes_cancelada[$nr_conta]);
		$tpl->assign('qt_mes_suspensa',  $ar_mes_suspensa[$nr_conta]);
		$tpl->assign('qt_mes_atendida',  ($ar_mes_concluida[$nr_conta]+$ar_mes_cancelada[$nr_conta]));

		if($ar_mes_aberta[$nr_conta] > 0)
		{
			$tpl->assign('qt_mes_atendida_perc',  number_format((($ar_mes_concluida[$nr_conta]+$ar_mes_cancelada[$nr_conta]) * 100)/ $ar_mes_aberta[$nr_conta],2));
		}		

		$tpl->assign('qt_mes_acumulado_aberta',    $qt_anterior_aberta);
		$tpl->assign('qt_mes_acumulado_concluida_crit_auto', $qt_anterior_concluida_crit_auto);
		$tpl->assign('qt_mes_acumulado_concluida_crit_user', $qt_anterior_concluida_crit_user);
		$tpl->assign('qt_mes_acumulado_concluida_crit_nao',  $qt_anterior_concluida_crit_nao);
		$tpl->assign('qt_mes_acumulado_concluida', $qt_anterior_concluida);
		$tpl->assign('qt_mes_acumulado_cancelada', $qt_anterior_cancelada);
		$tpl->assign('qt_mes_acumulado_suspensa',  $qt_anterior_suspensa);
		$tpl->assign('qt_mes_acumulado_atendida',  ($qt_anterior_concluida + $qt_anterior_cancelada));
		
		if($qt_anterior_aberta > 0)
		{		
			$tpl->assign('qt_mes_acumulado_atendida_perc',  number_format((($qt_anterior_concluida + $qt_anterior_cancelada) * 100)/ $qt_anterior_aberta,2));		
		}*/
		
		$qt_tot_mes_aberta    += $ar_mes_aberta[$nr_conta];
		$qt_tot_mes_concluida_crit_auto += $ar_mes_concluida_crit_auto[$nr_conta];
		$qt_tot_mes_concluida_crit_user += $ar_mes_concluida_crit_user[$nr_conta];
		$qt_tot_mes_concluida_crit_nao  += $ar_mes_concluida_crit_nao[$nr_conta];
		$qt_tot_mes_concluida += $ar_mes_concluida[$nr_conta];
		$qt_tot_mes_cancelada += $ar_mes_cancelada[$nr_conta];
		$qt_tot_mes_suspensa  += $ar_mes_suspensa[$nr_conta];
		$qt_tot_mes_atendida  += ($ar_mes_concluida[$nr_conta]+$ar_mes_cancelada[$nr_conta]);
		
		$nr_conta++;
	}	
	
	/*$tpl->newBlock('qt_tot_sistema');
	$tpl->assign('qt_tot_mes_aberta',    $qt_tot_mes_aberta);
	$tpl->assign('qt_tot_mes_concluida_crit_auto', $qt_tot_mes_concluida_crit_auto);
	$tpl->assign('qt_tot_mes_concluida_crit_user', $qt_tot_mes_concluida_crit_user);
	$tpl->assign('qt_tot_mes_concluida_crit_nao', $qt_tot_mes_concluida_crit_nao);
	$tpl->assign('qt_tot_mes_concluida', $qt_tot_mes_concluida);
	$tpl->assign('qt_tot_mes_cancelada', $qt_tot_mes_cancelada);
	$tpl->assign('qt_tot_mes_suspensa',  $qt_tot_mes_suspensa);
	$tpl->assign('qt_tot_mes_atendida',  $qt_tot_mes_atendida);
	if($qt_tot_mes_aberta > 0)
	{
		$tpl->assign('qt_tot_mes_atendida_perc',  number_format(($qt_tot_mes_atendida * 100) / $qt_tot_mes_aberta,2));
	}
	
	$tpl->assign('qt_tot_acumulado_aberta',    $qt_anterior_aberta);
	$tpl->assign('qt_tot_acumulado_concluida_crit_auto', $qt_anterior_concluida_crit_auto);
	$tpl->assign('qt_tot_acumulado_concluida_crit_user', $qt_anterior_concluida_crit_user);
	$tpl->assign('qt_tot_acumulado_concluida_crit_nao', $qt_anterior_concluida_crit_nao);
	$tpl->assign('qt_tot_acumulado_concluida', $qt_anterior_concluida);
	$tpl->assign('qt_tot_acumulado_cancelada', $qt_anterior_cancelada);
	$tpl->assign('qt_tot_acumulado_suspensa',  $qt_anterior_suspensa);
	$tpl->assign('qt_tot_acumulado_atendida',  ($qt_anterior_concluida + $qt_anterior_cancelada));		
	if($qt_anterior_aberta > 0)
	{
		$tpl->assign('qt_tot_acumulado_atendida_perc',  number_format((($qt_anterior_concluida + $qt_anterior_cancelada) * 100) / $qt_anterior_aberta,2));		
	}*/
	###################################################################################################################################

	###################################################### RESUMO ATIVIDADES ##########################################################
	/*$tpl->newBlock('qt_ano_anterior_resumo');
	$tpl->assign('qt_anterior_aberta',    $qt_resumo_anterior_aberta);
	$tpl->assign('qt_anterior_concluida_crit_auto', $qt_resumo_anterior_concluida_crit_auto);
	$tpl->assign('qt_anterior_concluida_crit_user', $qt_resumo_anterior_concluida_crit_user);
	$tpl->assign('qt_anterior_concluida_crit_nao',  $qt_resumo_anterior_concluida_crit_nao);
	$tpl->assign('qt_anterior_concluida', $qt_resumo_anterior_concluida);
	$tpl->assign('qt_anterior_cancelada', $qt_resumo_anterior_cancelada);
	$tpl->assign('qt_anterior_suspensa',  $qt_resumo_anterior_suspensa);
	$tpl->assign('qt_anterior_atendida',  $qt_resumo_anterior_atendida);
	$tpl->assign('qt_anterior_atendida_perc',  number_format(($qt_resumo_anterior_atendida * 100)/$qt_resumo_anterior_aberta,2));*/
	
	$qt_tot_mes_aberta    = 0;
	$qt_tot_mes_concluida_crit_auto = 0;
	$qt_tot_mes_concluida_crit_user = 0;
	$qt_tot_mes_concluida_crit_nao = 0;
	$qt_tot_mes_concluida = 0;
	$qt_tot_mes_cancelada = 0;
	$qt_tot_mes_suspensa  = 0;
	$qt_tot_mes_atendida  = 0;
	$nr_conta = 1;

	$sql=""; //" DELETE FROM indicador_plugin.info_atividade WHERE extract( 'year' from dt_referencia ) = extract( 'year' from current_timestamp ) ; ";
	while($nr_conta <= 12) 
	{
		$qt_resumo_anterior_aberta += $qt_resumo_mes_aberta[$nr_conta];
		$qt_resumo_anterior_concluida_crit_auto += $qt_resumo_mes_concluida_crit_auto[$nr_conta];
		$qt_resumo_anterior_concluida_crit_user += $qt_resumo_mes_concluida_crit_user[$nr_conta];
		$qt_resumo_anterior_concluida_crit_nao += $qt_resumo_mes_concluida_crit_nao[$nr_conta];
		$qt_resumo_anterior_concluida += $qt_resumo_mes_concluida[$nr_conta];
		$qt_resumo_anterior_cancelada += $qt_resumo_mes_cancelada[$nr_conta];
		$qt_resumo_anterior_suspensa += $qt_resumo_mes_suspensa[$nr_conta];		
		$qt_resumo_anterior_atendida += ($qt_resumo_mes_concluida[$nr_conta]+$qt_resumo_mes_cancelada[$nr_conta]);

		$qt_tot_mes_aberta += $qt_resumo_mes_aberta[$nr_conta];
		$qt_tot_mes_concluida_crit_auto += $qt_resumo_mes_concluida_crit_auto[$nr_conta];
		$qt_tot_mes_concluida_crit_user += $qt_resumo_mes_concluida_crit_user[$nr_conta];
		$qt_tot_mes_concluida_crit_nao += $qt_resumo_mes_concluida_crit_nao[$nr_conta];
		$qt_tot_mes_concluida += $qt_resumo_mes_concluida[$nr_conta];
		$qt_tot_mes_cancelada += $qt_resumo_mes_cancelada[$nr_conta];
		$qt_tot_mes_suspensa += $qt_resumo_mes_suspensa[$nr_conta];
		$qt_tot_mes_atendida += ($qt_resumo_mes_concluida[$nr_conta]+$qt_resumo_mes_cancelada[$nr_conta]);

		if(intval($nr_conta)<10)
		{
			$nr_zero='0';
		}
		else
		{
			$nr_zero='';
		}

		$indicador['DT_REFERENCIA'] = '01/' . $nr_zero.$nr_conta."/".$ano;
		$indicador['ABERTAS'] = floatval( $qt_resumo_mes_aberta[$nr_conta] );
		$indicador['ATENDIDAS'] = floatval( ($qt_resumo_mes_concluida[$nr_conta]+$qt_resumo_mes_cancelada[$nr_conta]) );

		$query = pg_query($db, " SELECT count(*) as q
		FROM indicador_plugin.info_atividade 
		WHERE dt_referencia = TO_DATE('".$indicador['DT_REFERENCIA']."', 'DD/MM/YYYY') AND dt_exclusao IS NULL; ");
		$row = pg_fetch_array($query); 

		if( $row['q']==0 )
		{
			$ar=explode("/",$indicador['DT_REFERENCIA']);
			$dtinv=$ar[2]."/".$ar[1]."/".$ar[0];
			/*echo $dtinv." < ".date('Y/m/01');
			if($dtinv<date('Y/m/01')){echo " = true";}else{echo " = false";}
			echo "<br />";*/

			if($dtinv<date('Y/m/01'))
			{
				$sql .= "
				INSERT INTO indicador_plugin.info_atividade 
				( dt_referencia , nr_abertas_mes , nr_atendidas_mes , nr_meta , dt_inclusao , cd_usuario_inclusao, cd_indicador_tabela, fl_media ) 
				VALUES 
				( TO_DATE('".$indicador['DT_REFERENCIA']."','DD/MM/YYYY') , " . $indicador['ABERTAS'] . ", " . $indicador['ATENDIDAS'] . ", " . $meta . " , current_timestamp , " . usuario_id() . ", ".$cd_indicador_tabela.", 'N' );
				";
			}
		}

		$indicadores[] = $indicador;

		$nr_conta++;
	}

	if($sql!='')
	{
		$ob_resul = pg_query($db, $sql);
	}

	echo 'Índices mensais gerados com sucesso!';

	############################################################################################################################################

	function montaMeses($ob_resul)
	{
		while($ob_reg = pg_fetch_object($ob_resul))
		{
			$ar_mes_qt[$ob_reg->nr_mes] = $ob_reg->qt_atividade;
		}	
		return $ar_mes_qt;
	}
?>