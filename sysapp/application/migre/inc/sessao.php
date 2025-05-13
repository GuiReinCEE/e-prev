<?php
	// CARREGAR DEPENDENCIAS DO CI PARA ACESSO AO BANCO DE DADOS
	define('BASEPATH', '../../../sysapp/');
	define('APPPATH', BASEPATH.'application/eprev/');
	define('EXT', '.php');
	define('CI_VERSION',	'1.6.3');
	require(BASEPATH.'codeigniter/Common'.EXT);
	require(BASEPATH.'codeigniter/Compat'.EXT);
	require(APPPATH.'config/constants'.EXT);
	
	$BM =& load_class('Benchmark');
	$BM->mark('total_execution_time_start');
	$BM->mark('loading_time_base_classes_start');
	$EXT =& load_class('Hooks');
	$EXT->_call_hook('pre_system');
	$CFG =& load_class('Config');
	$URI =& load_class('URI');
	//$RTR =& load_class('Router');
	$OUT =& load_class('Output');
	// $IN		=& load_class('Input');
	// $LANG	=& load_class('Language');
	require(BASEPATH.'codeigniter/Base5'.EXT);
	load_class('Controller', FALSE);
	include(APPPATH.'controllers/home.php');
	$BM->mark('loading_time_base_classes_end');
	$class = "home";
	$method = "index";
	$EXT->_call_hook('pre_controller');
	$BM->mark('controller_execution_time_( '.$class.' / '.$method.' )_start');

	$CI = new $class();
	foreach($_REQUEST as $key=>$val)
	{
		$$key=$val;
	}
	$tthis=&get_instance();
	$siht=&get_instance();

	session_start();

	if(is_null($_SESSION['U'])) 
	{	
		if (($_SERVER['SERVER_ADDR'] == '10.63.255.5') or ($_SERVER['SERVER_ADDR'] == '10.63.255.7'))
		{
			$ip_host = 'srvpg.eletroceee.com.br';
		}
		else
		{
			$ip_host = 'srvpgdsv.eletroceee.com.br'; 
		}
		
		$dbx = pg_connect('host='.$ip_host.' port=5555 dbname=fundacaoweb user=gerente');
		
		if(is_null($usuario_emp))
		{
			$_IP = $_SERVER['REMOTE_ADDR'];
			$qr_sql = "
						SELECT * 
						  FROM projetos.usuarios_controledi uc
						 WHERE uc.estacao_trabalho = '".$_IP."' 
						   AND uc.tipo NOT IN ('X', 'T')
						   AND (
								(uc.dt_hora_scanner_computador::DATE = CURRENT_DATE AND uc.estacao_trabalho LIKE '10.63.255%' ) 
								OR 
								(uc.estacao_trabalho LIKE '10.65.255%') 
								)
						   AND uc.opt_interatividade = 'S'
						 ORDER BY (CASE WHEN uc.dt_hora_scanner_computador < uc.dt_ult_login
										THEN uc.dt_ult_login
										ELSE uc.dt_hora_scanner_computador
								  END) DESC
						 LIMIT 1
				      ";
		}
		else
		{
			$qr_sql = "
						SELECT * 
						  FROM projetos.usuarios_controledi uc
						 WHERE UPPER(TRIM(uc.usuario)) = UPPER(TRIM('".$usuario_emp."'))
						   AND uc.tipo NOT IN ('X', 'T') 
						   AND (
								(uc.dt_hora_scanner_computador::DATE = CURRENT_DATE AND uc.estacao_trabalho LIKE '10.63.255%' ) 
								OR 
								(uc.estacao_trabalho LIKE '10.65.255%') 
								)
						   AND uc.opt_interatividade = 'S'
						 ORDER BY (CASE WHEN uc.dt_hora_scanner_computador < uc.dt_ult_login
										THEN uc.dt_ult_login
										ELSE uc.dt_hora_scanner_computador
								  END) DESC
						 LIMIT 1						   
				      ";
		}
				
		$ob_resul = pg_query($dbx, $qr_sql);
		
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
		}
		else 
		{
		   header("location: index.php?m=É necessário logar-se para acessar.&ir_para=".urlencode($_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'])); // Sessão Expirou
		}
	}
?>