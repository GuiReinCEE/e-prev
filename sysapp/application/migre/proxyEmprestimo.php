<?php
	include_once('inc/class.SocketAbstraction2.inc.php');
	include_once('inc/class.TemplatePower.inc.php');
	require_once('inc/config.inc.php');

	#define(SKT_IP, '10.63.255.16'); 
	#define(SKT_PORTA, '4444');  
   
	$LISTNER_IP    = SKT_IP;
	$LISTNER_PORTA = SKT_PORTA;
	$IP_CLIENTE    = $_SERVER['REMOTE_ADDR'];
   
	// Serialização 
	$req = '';
   
	$cont = 0;
	if ($_GET) 
	{ // Dados enviados pelo método GET
		foreach($_GET as $campo => $valor) 
		{
			$cont++;
			if ($campo=='call') 
			{
				$fnc = $valor;
			} 
			else 
			{
				if ($req != '') 
				{
					$req .= ';';
				}
				
				if ( ($fnc == 'fnc_busca_informacoes_emp') and ($cont == 6) ) 
				{
					$req .= $IP_CLIENTE.';';
					$cont++;
				}
				
				$req .= $valor;
			}
		}
	} 
	else 
	{
		if ($_POST) 
		{ // Dados enviados pelo método POST
			foreach($_POST as $campo => $valor) 
			{
				$cont++;
				if ($campo=='call') 
				{
					$fnc = $valor;
				} 
				else 
				{
				
					if ($req != '') 
					{
						$req .= ';';
					}
					$req .= $valor;
				}
			}
		}
	}
	
	$param = $req;
	$req   = $fnc.";".$req;
   
	switch ($fnc) 
	{
		case 'novaSessao': 
			/*
			// Destroi sessão anterior (caso exista) e cria uma nova sessão
			session_start();
			$_SESSION = array();
			session_destroy();
			session_start();
			session_regenerate_id();
			header('Content-Type: text/xml'); // Essencial para garantir que o objeto vai reconhecer como um arquivo XML
			header('Cache-Control: must-revalidate, max-age=0'); // Importante para evitar que o IE utilize o cache após a primeira requisição e não mostre mais as atualizações
			echo '<?xml version="1.0" encoding="ISO-8859-1" standalone="yes"?><response><fld tp="SYS" id="FNC">novaSessao</fld><fld tp="SYS" id="ERR">NULL</fld><fld tp="DAT" id="session_id">'.session_id().'</fld></response>';
			*/
			
			header('Content-Type: text/xml'); // Essencial para garantir que o objeto vai reconhecer como um arquivo XML
			header('Cache-Control: must-revalidate, max-age=0'); // Importante para evitar que o IE utilize o cache após a primeira requisição e não mostre mais as atualizações
			echo '<?xml version="1.0" encoding="ISO-8859-1" standalone="yes"?><response><fld tp="SYS" id="FNC">novaSessao</fld><fld tp="SYS" id="ERR">NULL</fld><fld tp="DAT" id="session_id">'.(md5(uniqid(rand(),true))).'</fld></response>';
			
			break;
		
		case 'mostraIP': 
			header('Content-Type: text/xml'); // Essencial para garantir que o objeto vai reconhecer como um arquivo XML
			header('Cache-Control: must-revalidate, max-age=0'); // Importante para evitar que o IE utilize o cache após a primeira requisição e não mostre mais as atualizações
			echo '<?xml version="1.0" encoding="ISO-8859-1" standalone="yes"?><response><fld tp="SYS" id="FNC">mostraIP</fld><fld tp="SYS" id="ERR">NULL</fld><fld tp="DAT" id="ip_cliente">'.$IP_CLIENTE.'</fld></response>';
			break;

		case 'popList':
			header('Content-Type: text/xml'); // Essencial para garantir que o objeto vai reconhecer como um arquivo XML
			header('Cache-Control: must-revalidate, max-age=0'); // Importante para evitar que o IE utilize o cache após a primeira requisição e não mostre mais as atualizações

			$fncLst = $_GET['fncLst'];
			$emp    = $_GET['emp'];
			$plano  = $_GET['plano'];

			$cn = new Socket();
			$cn->SetRemoteHost($LISTNER_IP);
			$cn->SetRemotePort($LISTNER_PORTA);
			$cn->SetBufferLength(262144); // 256KB
			$cn->SetConnectTimeOut(1);
			if ($cn->Connect()) 
			{
				$ret = $cn->Ask($param);
				if ($cn->Error()) 
				{
					$tpl = new TemplatePower('tpl/xmlformerror.txt');
					$tpl->prepare();
					$tpl->assign('fnc', $fnc);
					$tpl->assign('err', $cn->GetErrStr());
				} 
				else 
				{
					echo '<?xml version="1.0" encoding="ISO-8859-1" standalone="yes"?>'.$ret;
				}
			} 
			else 
			{
				$tpl = new TemplatePower('tpl/xmlformerror.txt');
				$tpl->prepare();
				$tpl->assign('fnc', $fnc);
				$tpl->assign('err', $cn->GetErrStr());
			}
			break;
         
		default:
			header('Content-Type: text/xml'); // Essencial para garantir que o objeto vai reconhecer como um arquivo XML
			header('Cache-Control: must-revalidate, max-age=0'); // Importante para evitar que o IE utilize o cache após a primeira requisição e não mostre mais as atualizações

			$cn = new Socket();
			$cn->SetRemoteHost($LISTNER_IP);
			$cn->SetRemotePort($LISTNER_PORTA);
			$cn->SetBufferLength(262144); // 256KB
			$cn->SetConnectTimeOut(1);
			if ($cn->Connect()) 
			{
				$ret = $cn->Ask($req);
				if ($cn->Error()) 
				{
					$tpl = new TemplatePower('tpl/xmlformerror.txt');
					$tpl->prepare();
					$tpl->assign('fnc', $fnc);
					$tpl->assign('err', $cn->GetErrStr());
				} 
				else 
				{
					echo '<?xml version="1.0" encoding="ISO-8859-1" standalone="yes"?>'.$ret;
				}
			} 
			else 
			{
				$tpl = new TemplatePower('tpl/xmlformerror.txt');
				$tpl->prepare();
				$tpl->assign('fnc', $fnc);
				$tpl->assign('err', $cn->GetErrStr());
			}
			break;
   }
?>
