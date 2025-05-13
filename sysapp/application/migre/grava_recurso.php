<?
   include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/funcoes.php');
//---------------------------------------------------------------------------------
	if ($codigo<>"") 
	{
      $sql =        " update projetos.usuarios_controledi  			";
      $sql = $sql . " set	nome		= '$nome'      	, 			";
      $sql = $sql . "		tipo		= '$cbo_perfil'	, 			";
	  $sql = $sql . "		divisao		= '$divisao'   	, 			";
	  $sql = $sql . "		indic_01	= '$indic1'		,			";
	  $sql = $sql . "		indic_02	= '$indic2'		,			";
	  $sql = $sql . "		indic_03	= '$indic3'		,			";
	  $sql = $sql . "		indic_04	= '$indic4'		,			";
	  $sql = $sql . "		indic_05	= '$indic5'		,			";
	  $sql = $sql . "		indic_06	= '$indic6'		,			";
	  $sql = $sql . "		indic_07	= '$indic7'		,			";
	  $sql = $sql . "		indic_08	= '$indic8'		,			";
	  $sql = $sql . "		indic_09	= '$indic9'		,			";
	  $sql = $sql . "		indic_10	= '$indic10'	,			";
	  $sql = $sql . "		indic_11	= '$indic11'	,			";
	  $sql = $sql . "		indic_12	= '$indic12'	,			";
  	  $sql = $sql . "		cd_cargo	= $cbo_cargo	,			";
	  $sql = $sql . " 		formato_mensagem 	= '$formato_mensagem',	";	  	// garcia - 30/03/2004 - OS 2321
	  $sql = $sql . " 		e_mail_alternativo 	= '$email_alt' 		";		// garcia - 30/03/2004 - OS 2321
      $sql = $sql . " where codigo = $codigo   ";
//echo $sql;	  
	}
	else 
	{
        $sql =        " insert into projetos.usuarios_controledi (usuario,senha, ";
        $sql = $sql . "        nome ,     			";
		$sql = $sql . "        divisao ,     		";
        $sql = $sql . "        tipo ,     			";
        $sql = $sql . "        observacao ,      	";		
		$sql = $sql . "        skin ,		      	";		
		$sql = $sql . "        opt_workspace ,	   	";
		$sql = $sql . "        opt_tarefas ,      	";		
		$sql = $sql . "			indic_01 ,			";
		$sql = $sql . "			indic_02 ,			";
		$sql = $sql . "			indic_03 ,			";
		$sql = $sql . "			indic_04 ,			";
		$sql = $sql . "			indic_05 ,			";
		$sql = $sql . "			indic_06 ,			";
		$sql = $sql . "			indic_07 ,			";
		$sql = $sql . "			indic_08 ,			";
		$sql = $sql . "			indic_09 ,			";
		$sql = $sql . "			indic_10 ,			";
		$sql = $sql . "			indic_11 ,			";
		$sql = $sql . "			indic_12 ,			";
		$sql = $sql . "			cd_cargo ,			";
		$sql = $sql . "			formato_mensagem , 	";		// garcia - 30/03/2004 - OS 2321
		$sql = $sql . "			e_mail_alternativo )";		// garcia - 30/03/2004 - OS 2321
        $sql = $sql . " values ('$usuario', '123456', 	";
        $sql = $sql . "        '$nome',   			";
		$sql = $sql . "        '$divisao' ,     	";
	    $sql = $sql . "        '$cbo_perfil',   	";
        $sql = $sql . "        '',			    	";
		$sql = $sql . "        'TRAD',		    	";
		$sql = $sql . "        'N',	    			";
		$sql = $sql . "        'N',				    ";
		$sql = $sql . "			'$indic1',			";
		$sql = $sql . "			'$indic2',			";
		$sql = $sql . "			'$indic3',			";
		$sql = $sql . "			'$indic4',			";
		$sql = $sql . "			'$indic5',			";
		$sql = $sql . "			'$indic6',			";
		$sql = $sql . "			'$indic7',			";
		$sql = $sql . "			'$indic8',			";
		$sql = $sql . "			'$indic9',			";
		$sql = $sql . "			'$indic10',			";
		$sql = $sql . "			'$indic11',			";
		$sql = $sql . "			'$indic12',			";
	    $sql = $sql . "        	$cbo_cargo,   	";
		$sql = $sql . "			'$formato_mensagem',	";	// garcia - 30/03/2004 - OS 2321
		$sql = $sql . "			'$email_alt')			";	// garcia - 30/03/2004 - OS 2321
	}
//---------------------------------------------------------------------------------	
//	echo $sql;
	if (!($rs=pg_exec($db, $sql))) {
	   pg_close($db);
	   header('location: lst_recursos.php?msg=Ocorreu um erro ao tentar gravar o projeto.');
	}
	else {
	   pg_close($db);
	   header('location:lst_recursos.php');
	}
?>