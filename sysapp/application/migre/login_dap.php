<?php
	if (!preg_match('/10.63./',$_SERVER['REMOTE_ADDR']))
	{
		header("location: index.php?m=Usuário ou senha inválido");
		exit;
	}
	
	//http://www.e-prev.com.br/controle_projetos/login_dap.php?acao=EMP&e=9&r=7366&s=0&o=N&u=COLIVEIRA
	//"c:\program files\internet explorer\iexplore.EXE" "http://www.e-prev.com.br/cieprev/sysapp/application/migre/login_dap.php?acao=EMP&e=9&r=7366&s=0&o=N&u=COLIVEIRA"
	include_once('inc/conexao.php');

	if ($_REQUEST['u'] != '') 
	{
		$qr_sql = "
					SELECT * 
					  FROM projetos.usuarios_controledi uc 
					 WHERE UPPER(TRIM(uc.usuario)) = UPPER(TRIM('".$_REQUEST['u']."'))
					   AND uc.tipo NOT IN ('X', 'T')
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

			if($_REQUEST['acao'] == 'EMP') 
			{
				$_SESSION['MOSTRAR_BANNER'] = 'N';
				
				$qr_update = " 
								UPDATE projetos.usuarios_controledi 
								   SET estacao_trabalho           = '".$_SERVER['REMOTE_ADDR']."', 
								       dt_ult_login               = CURRENT_TIMESTAMP,
								       dt_hora_scanner_computador = CURRENT_TIMESTAMP 
								 WHERE codigo = ".$_SESSION['Z']."
					         ";
				if(pg_query($db, $qr_update)) 
				{
					if ($_REQUEST['e'] == '') 
					{
						header("location: simulacao_dap.php");
					}
					else 
					{
						header("location: simulacao_dap.php?e=".$_REQUEST['e']."&r=".$_REQUEST['r']."&s=".$_REQUEST['s']."&o=".$_REQUEST['o']);
					}
				}
				else 
				{
					header("location: index.php?m=Usuário ou senha inválido");
				}
			}
			else 
			{
				header("location: ".$reg['tela_inicial']);
			}
		}
		else 
		{
			header("location: index.php?m=Usuário ou senha inválido");
		}
	} 
	else 
	{
		$qr_sql = "
					SELECT * 
					  FROM projetos.usuarios_controledi 
					 WHERE estacao_trabalho = '".$_SERVER['REMOTE_ADDR']."'
					   AND uc.tipo          NOT IN ('X', 'T')
					   AND 1 = 0
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

			if($_REQUEST['acao'] == 'EMP')  
			{
				$_SESSION['MOSTRAR_BANNER'] = 'N';
				$qr_update = " 
								UPDATE projetos.usuarios_controledi
								   SET estacao_trabalho           = '".$_SERVER['REMOTE_ADDR']."',
								       dt_ult_login               = CURRENT_TIMESTAMP,
								       dt_hora_scanner_computador = CURRENT_TIMESTAMP  
								 WHERE codigo = ".$_SESSION['Z']." 
							 ";
				if(pg_query($db, $qr_update)) 
				{
					if ($_REQUEST['e']=='') 
					{
						header("location: simulacao_dap.php");
					}
					else 
					{
						header("location: simulacao_dap.php?e=".$_REQUEST['e']."&r=".$_REQUEST['r']."&s=".$_REQUEST['s']."&o=".$_REQUEST['o']);
					}
				}
				else 
				{
					header("location: index.php?m=Erro de login. Por favor, entre em contato com a DI.");
				}
			}
			else 
			{
				if ($reg['chamada_web'] != '') 
				{
					header("location: ".$reg['chamada_web']);
				} 
				else 
				{
					header("location: ".$reg['tela_inicial']);
				}
			}
		}
		else 
		{
			header("location: index.php?m=Usuário ou senha inválido");
		}
	}
?>