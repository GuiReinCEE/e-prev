<?php
	require_once('inc/conexao.php');
	require_once('inc/ldap.php');
	
	session_start("VERIFICA_LOGIN");
	
	$_SESSION['nr_tentativa']  = $_SESSION['nr_tentativa'] + 1;
	$msg_tentativa = "Usuário ou senha inválido!";
	if($_SESSION['nr_tentativa'] == 2)
	{
		$msg_tentativa = "Você realizou 2 tentativas consecutivas de logon erradas e se errar novamente bloqueará sua senha da rede.";
	}
	
	$_POST['usuario'] = strtolower($_POST['usuario']);
	
	#### VALIDA PERIODO DE FERIAS ####
	/*
	$qr_sql = "
				SELECT (CASE WHEN COUNT(*) > 0 THEN 'S' ELSE 'N' END) AS fl_ferias
				  FROM public.benef_rh_ferias brf
				  JOIN projetos.usuarios_controledi uc
					ON COALESCE(uc.cd_registro_empregado,0) = brf.cd_registro_empregado
				   AND uc.codigo = funcoes.get_usuario(UPPER(TRIM('".$_POST['usuario']."')))
				 WHERE brf.cd_empresa            = 9
				   AND brf.seq_dependencia       = 0
				   AND CURRENT_DATE BETWEEN brf.dt_ini_ferias AND brf.dt_fim_ferias
	          ";
	*/

	$qr_sql = "
				SELECT get_usuario_ferias AS fl_ferias 
				  FROM funcoes.get_usuario_ferias(UPPER(TRIM('".$_POST['usuario']."')))
			  ";
	$ob_resul = pg_query($db, $qr_sql);		  
	$ar_reg   = pg_fetch_array($ob_resul);
	
	##### SOLICITAÇÃO DO DIA 17/06/2024 #####
	#$array_usuario = array('cgoncalves', 'diandrar', 'jseidler', 'csantos', 'mariag', 'lferreira', 'rodrigof');
	
	$array_usuario = array();
	
	if (!in_array($_POST['usuario'], $array_usuario))
	{
		#echo $ar_reg['fl_horario']; exit; 
		if($ar_reg['fl_ferias'] == 'S')
		{
			header("location: index.php?m=Acesso não permitido no período de férias.");
			exit;		
		}	
	}
	
	#### VERIFICAR HORARIO DE ACESSO ####
	$qr_sql = "
				SELECT get_usuario_horario AS fl_horario
				  FROM funcoes.get_usuario_horario(UPPER(TRIM('".$_POST['usuario']."')))				 
	          ";
	$ob_resul = pg_query($db, $qr_sql);		  
	$ar_reg   = pg_fetch_array($ob_resul);
	
	#echo $ar_reg['fl_horario']; exit; 
	if($ar_reg['fl_horario'] == 'N')
	{
		header("location: index.php?m=Horário de acesso não permitido.");
		exit;		
	}
	
	#### VERIFICAR TIPO DE AUTENTICAÇÃO ####
	$qr_sql = "
				SELECT uc.fl_ldap_autenticar
				  FROM projetos.usuarios_controledi uc
				 WHERE UPPER(TRIM(uc.usuario)) = UPPER(TRIM('".$_POST['usuario']."'))
				 ORDER BY (CASE WHEN uc.dt_hora_scanner_computador < uc.dt_ult_login
								THEN uc.dt_ult_login
								ELSE uc.dt_hora_scanner_computador
						  END) DESC
				 LIMIT 1				 
	          ";
	$ob_resul = pg_query($db, $qr_sql);		  
	$ar_reg   = pg_fetch_array($ob_resul);
	
	#echo $ar_reg['fl_ldap_autenticar']; exit;
	
	if($ar_reg['fl_ldap_autenticar'] == 'S')
	{
		if (valida_ldap($_POST['usuario'],$_POST['senha'])) 
		{
			$qr_login = "
							SELECT * 
							  FROM projetos.usuarios_controledi uc
							 WHERE UPPER(TRIM(uc.usuario)) = UPPER(TRIM('".$_POST['usuario']."'))
							   AND uc.tipo      NOT IN ('X', 'T')
							 ORDER BY (CASE WHEN uc.dt_hora_scanner_computador < uc.dt_ult_login
											THEN uc.dt_ult_login
											ELSE uc.dt_hora_scanner_computador
									  END) DESC
							 LIMIT 1							   
					    ";				
		}
		else
		{
			header("location: index.php?m=".$msg_tentativa."&ir_para=".$_POST['ir_para']); 	
			exit;
		}
	}
	else
	{
		$qr_login = "
						SELECT * 
						  FROM projetos.usuarios_controledi uc
						 WHERE UPPER(TRIM(uc.usuario)) = UPPER(TRIM('".$_POST['usuario']."')) 
						   AND uc.senha_md5            = MD5('".$_POST['senha']."')
						   AND uc.tipo                 NOT IN ('X', 'T')
						 ORDER BY (CASE WHEN uc.dt_hora_scanner_computador < uc.dt_ult_login
										THEN uc.dt_ult_login
										ELSE uc.dt_hora_scanner_computador
								  END) DESC
						 LIMIT 1						   
				    ";
	}
	$ob_resul = pg_query($db, $qr_login);
	
	if(pg_num_rows($ob_resul) > 0) 
	{
		$reg = pg_fetch_array($ob_resul);

		session_start();
		
		$_SESSION['nr_tentativa'] = 0;
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

		$qr_update = " 
						UPDATE projetos.usuarios_controledi 
						   SET estacao_trabalho = '".$_SERVER['REMOTE_ADDR']."',
							   dt_ult_login     = CURRENT_TIMESTAMP 
						 WHERE codigo = ".$_SESSION['Z']." 
					 ";
		if(pg_query($db, $qr_update)) 
		{
			if ((preg_match('/http:/',$_REQUEST['ir_para'])) or (preg_match('/https:/',$_REQUEST['ir_para'])))
			{
				if ((($_SERVER['SERVER_ADDR'] == '10.63.255.5') OR ($_SERVER['SERVER_ADDR'] == '10.63.255.7')) AND ($_SERVER['HTTPS'] == 'on'))					
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
				
				header("location: ".$_REQUEST['ir_para']);
				exit;
			}
			else
			{
				if(trim($reg['tela_inicial']) != "")
				{
					header("location: ".$reg['tela_inicial']);
					exit;
				}
				else
				{
					header("location: http://".$_SERVER['SERVER_NAME']."/cieprev/index.php/home");
					exit;
				}
			}
		}
		else 
		{
			header("location: index.php?m=Erro de login. Por favor, entre em contato com o Suporte da Informática.");
			exit;
		}			
		
	}
	else 
	{
		header("location: index.php?m=Usuário não cadastrado no E-prev!&ir_para=".$_POST['ir_para']);
		exit;
	}
?>