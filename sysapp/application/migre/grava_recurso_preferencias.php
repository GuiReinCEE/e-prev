<?
   include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/funcoes.php');
//---------------------------------------------------------------------------------
	if ($codigo<>"") 
	{
      $sql =        " update projetos.usuarios_controledi  			";
      $sql = $sql . " set	nome			= '$nome'      	, 			";
	  $sql = $sql . "		skin			= '$opt_skin'  	, 			";
	  $sql = $sql . "		guerra			= '$guerra'  	, 			";
	  $sql = $sql . "		opt_tarefas		= '$opt_tarefas', 			";	  
	  $sql = $sql . "		opt_workspace	= '$opt_workspace',		";	  
	  $sql = $sql . "		opt_interatividade	= '$opt_interatividade', ";	  
	  $sql = $sql . "		opt_dicas		= '$opt_dicas',				";	  
	  $sql = $sql . "		observacao		= '$obs'       	, 			";
	  $sql = $sql . " 		formato_mensagem 	= '$formato_mensagem',	";
	  $sql = $sql . " 		e_mail_alternativo 	= '$email_alt' 		";
      $sql = $sql . " where codigo = $codigo   ";
//echo $sql;	  
	}
	else 
	{
        $sql =        " insert into projetos.usuarios_controledi (usuario,senha, ";
        $sql = $sql . "        nome ,     			";
        $sql = $sql . "        observacao ,      	";		
		$sql = $sql . "        skin ,		      	";		
		$sql = $sql . "        opt_workspace ,	   	";
		$sql = $sql . "        opt_tarefas ,      	";		
		$sql = $sql . "        opt_dicas ,      	";		
		$sql = $sql . "        opt_interatividade , ";		
		$sql = $sql . "		   formato_mensagem , 	";
		$sql = $sql . "		   e_mail_alternativo, 	";
		$sql = $sql . "		   guerra ) 			";
        $sql = $sql . " values ('$usuario', null, 	";
        $sql = $sql . "        '$nome',   			";
        $sql = $sql . "        '$obs',			    ";
		$sql = $sql . "        '$opt_skin',			";
		$sql = $sql . "        '$opt_tarefas',	    ";
		$sql = $sql . "        '$opt_workspace',    ";
		$sql = $sql . "        '$opt_dicas',    	";
		$sql = $sql . "        '$opt_interatividade', ";
		$sql = $sql . "			'$formato_mensagem',  ";
		$sql = $sql . "			'$email_alt',       ";
		$sql = $sql . "			'$guerra')         	";
	}
//---------------------------------------------------------------------------------	
//	echo $sql;
	if (!($rs=pg_exec($db, $sql))) {
	   pg_close($db);
	   header('location: cad_recurso.php?msg=Ocorreu um erro ao tentar gravar o projeto.');
	}
	else {
	   pg_close($db);
	   header('location:lst_recursos.php');
	}
?>