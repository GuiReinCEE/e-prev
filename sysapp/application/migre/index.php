<?php
	if ((($_SERVER['SERVER_ADDR'] == '10.63.255.5') or ($_SERVER['SERVER_ADDR'] == '10.63.255.7')) and ($_SERVER['HTTPS'] != 'on'))
	{
		#### REDIRECIONA PARA HTTPS ####
		$ir_para_https = $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
		$ir_para_https = str_replace('http://','',$ir_para_https);
		$ir_para_https = str_replace('https://','',$ir_para_https);
		$ir_para_https = 'https://'.$ir_para_https;
		header("location: ".$ir_para_https);
		exit;
	}
	include_once('inc/class.TemplatePower.inc.php');
	include_once('inc/conexao.php');
	
	$_IP  = $_SERVER['REMOTE_ADDR'];
	$_SRV = $_SERVER['SERVER_NAME'];

	#### Verifica se já fez login na rede ####	
	$qr_sql  = " 
				SELECT uc.*, 
				       TO_CHAR(uc.dt_hora_scanner_computador, 'DD/MM/YYYY HH:MI') AS dt_scanner,
					   TO_CHAR(uc.dt_ult_login, 'DD/MM/YYYY HH:MI') AS dt_login
				  FROM projetos.usuarios_controledi uc
				 WHERE uc.estacao_trabalho   = '".$_IP."' 
				   AND uc.estacao_trabalho   LIKE '10.63.%'
				   AND uc.opt_interatividade = 'S'
				   AND uc.tipo               NOT IN ('X', 'T') 
				   AND CAST(uc.dt_hora_scanner_computador AS DATE) = CURRENT_DATE
				   AND 0 = 1
			     ORDER BY (CASE WHEN uc.dt_hora_scanner_computador < uc.dt_ult_login
				                THEN uc.dt_ult_login
								ELSE uc.dt_hora_scanner_computador
						  END) DESC
				 LIMIT 1
		   ";
	$ob_resul = pg_query($db, $qr_sql);
	
	if(pg_num_rows($ob_resul) > 0) 
	{
		$reg = pg_fetch_array($ob_resul);
		
		session_start();
		
		$_SESSION['U']     = $reg['usuario']; // Usuário (mesmo da rede)		
		$_SESSION['CODU']  = $reg['codigo']; // Código do Usuário (mesmo da rede)
		$_SESSION['Z']     = $reg['codigo']; // Código do usuário
		$_SESSION['T']     = $reg['tipo']; // Tipo: U=Usuário; A=Atendente; G=Gerente; D=Diretoria/Presidência
		$_SESSION['D']     = $reg['divisao']; // Divisão à qual pertende o usuário que está se logando
		$_SESSION['N']     = $reg['guerra']; // Nome do usuário
		$_SESSION['S']     = $reg['divisao']; // Sistema: DI=Controle DI; DA=Controle DA; DCG=Controle DCG; ...
		$_SESSION['WRKSP'] = $reg['opt_workspace'];
		$_SESSION['L']     = 'U';
		$_SESSION['O']     = 'DT';
		$_SESSION['R']     = 'TO'; //  Filtro inicial para as OSs e Itens de Cronograma - Filtro para as listagens: TO=Todos; AG=Aguardando; ...
		$_SESSION['CHKIME'] = 'S';  //Imediata
		$_SESSION['CHKFUT'] = 'S';  //Futura
		$_SESSION['CHKAGE'] = 'S';  //Rotina
		$_SESSION['CHKROT'] = 'S';  //Agenda
		$_SESSION['CHKEC']  = NULL;           
		$_SESSION['CHKMS']  = 'S';   //Solicitações Feitas
		$_SESSION['CHKSR']  = 'S';   //Solicitações Recebidas
		$_SESSION['CHKAG']  = 'S';   //Aguardando
		$_SESSION['CHKAN']  = 'S';   //Em Andamento
		$_SESSION['CHKEN']  = 'N';   //Encerradas
		$_SESSION['CHKTE']  = 'S';   //Em Testes
		$_SESSION['DT_SCANNER']     = $reg['dt_scanner'];
		$_SESSION['PROGATU']        = NULL;
		$_SESSION['DT_LOGIN']       = $reg['dt_login'];
		$_SESSION['HIST_CAMINHO']   = NULL; //Caminhos utilizados
		$_SESSION['MOSTRAR_BANNER'] = 'S';
		$_SESSION['IND_ENQ']        = 'N';
		
		//STATUS DAS TAREFAS - SITUAÇÕES DAS TAREFAS
		$_SESSION['AMAN'] = 'S'; //Aguardando 
		$_SESSION['EMAN'] = 'S'; //Em Manutenção
		$_SESSION['CONC'] = 'N'; //Concluida
		$_SESSION['LIBE'] = 'S'; //Liberada	
		
        $_SESSION['INDIC_01'] = $reg['indic_01'];
        $_SESSION['INDIC_02'] = $reg['indic_02'];
        $_SESSION['INDIC_03'] = $reg['indic_03'];
        $_SESSION['INDIC_04'] = $reg['indic_04'];
        $_SESSION['INDIC_05'] = $reg['indic_05'];
        $_SESSION['INDIC_06'] = $reg['indic_06'];
        $_SESSION['INDIC_07'] = $reg['indic_07'];
        $_SESSION['INDIC_08'] = $reg['indic_08'];
        $_SESSION['INDIC_09'] = $reg['indic_09'];
        $_SESSION['INDIC_10'] = $reg['indic_10'];
        $_SESSION['INDIC_11'] = $reg['indic_11'];
        $_SESSION['INDIC_12'] = $reg['indic_12'];
        
		if(trim($reg['tela_inicial']) != "")
		{
			header("location: ".$reg['tela_inicial']);
		}
		else
		{
			header("location: http://".$_SERVER['SERVER_NAME']."/cieprev/index.php/home");
		}
	}
	else 
	{
		$tpl = new TemplatePower('tpl/tpl_index_interno.html');
		$tpl->prepare();
		$tpl->assign('ip', $_IP.' : '.$_SRV);
		if(trim($_REQUEST['ir_para']) != "")
		{
			if ((($_SERVER['SERVER_ADDR'] == '10.63.255.5') or ($_SERVER['SERVER_ADDR'] == '10.63.255.7')) and ($_SERVER['HTTPS'] != 'on') and (preg_match('/http://',$_POST['ir_para'])))
			{
				$_REQUEST['ir_para'] = str_replace('http://','',$_REQUEST['ir_para']);
				$_REQUEST['ir_para'] = str_replace('https://','',$_REQUEST['ir_para']);
				$_REQUEST['ir_para'] = 'https://'.$_REQUEST['ir_para'];
			}
			else
			{
				$_REQUEST['ir_para'] = str_replace('http://','',$_REQUEST['ir_para']);
				$_REQUEST['ir_para'] = str_replace('https://','',$_REQUEST['ir_para']);
				$_REQUEST['ir_para'] = 'http://'.$_REQUEST['ir_para'];			
			}
		}
		$tpl->assign('ir_para', $_REQUEST['ir_para']);	
		$tpl->assign('msg', $_REQUEST['m']);
		
		if(trim($_REQUEST['m']) != "")
		{
			$tpl->newBlock('exibe_mensagem');
		}
		else
		{
			$tpl->newBlock('focus_usuario');
		}

		$tpl->printToScreen();
	}
	#echo "<input type='hidden' name='charset' value='".ini_get('default_charset')."'>";
?>
