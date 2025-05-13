<?php
   include_once('inc/conexao.php');

   if(($_SERVER['SERVER_ADDR'] == '10.63.255.5') or ($_SERVER['SERVER_ADDR'] == '10.63.255.7'))
   {
		$reg = getListener();
		if($reg['ip']!='')
		{
			define(SKT_IP, $reg['ip']); 
			define(SKT_PORTA, $reg['porta']);
		}
		else
		{
			define(SKT_IP, '10.63.255.16'); 
	   		define(SKT_PORTA, '9731');
		}
   }
   else
   {
		if($_SERVER['REMOTE_ADDR'] == '10.63.255.x')//PARA TESTES
		{
			define(SKT_IP, '10.63.255.150'); 
		}
		else
		{
			define(SKT_IP, '10.63.255.16'); 
		}
		define(SKT_PORTA, '4444');   
   }   
   
   function getListener()
   {
	   	global $db;
		//echo $_SERVER['REMOTE_ADDR'];
		if($_SERVER['REMOTE_ADDR'] == '10.63.255.x')//PARA TESTES
		{
			$sql = " 
					SELECT ip,
					       porta,
					       CURRENT_TIMESTAMP - ultima_resposta AS tempo
			          FROM projetos.adm_listner
					 WHERE banco = 'ELETRO1' 
	                   AND situacao = 'A'
					   AND ultima_resposta > CURRENT_DATE 
					   AND ip = '10.63.255.150'
					   AND porta = '3625'
	                 ORDER BY tempo DESC
	                 LIMIT 1									
				   ";
		}
		else
		{
			$sql = " 
					SELECT ip,
					       porta,
					       CURRENT_TIMESTAMP - ultima_resposta AS tempo
			          FROM projetos.adm_listner
					 WHERE banco = 'ELETRO1' 
	                   AND situacao = 'A'
					   AND ultima_resposta > CURRENT_DATE 
					   --AND ip = '10.63.255.16'
					   AND porta NOT IN ('3625','9999')
	                 ORDER BY tempo DESC
	                 LIMIT 1									
				   ";
		}		

		$rs  = pg_query($db, $sql);
		while ($reg = pg_fetch_array($rs))
		{
			$fp = @fsockopen($reg['ip'], $reg['porta'], $errno, $errstr, 5);
			if($fp)
			{
				return $reg;
			}
		}
		return $reg;
   }
?>