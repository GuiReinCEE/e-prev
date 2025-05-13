<?
   include_once('inc/sessao.php');
   include_once('inc/conexao.php');
   include_once('inc/class.Email.inc.php');
	
   $txt_dt_inicio  = ( $data_cad  == '' ? 'Null' : "'".convdata_br_iso($data_cad)."'" );
//echo   $data_cad;
   $txt_processo_pai  = ( $processo_pai  == '' ? 'Null' : "'".$processo_pai."'" );	// garcia - 09/02/2004 - para retirar os null
   $txt_envolvido  = ( $envolvido  == '' ? 'Null' : "'".$envolvido."'" );			// garcia - 09/03/2004 - para retirar os null
   
   for( $i=0; $i<sizeof($envolvido); $i++ ) {
 //     echo "aqui :".$envolvido[$i]."<br>";
	  $txt_envolvidos = $txt_envolvidos.",".$envolvido[$i];
   }	  
	$txt_envolvidos = substr($txt_envolvidos, 1,strlen($txt_envolvidos));
// echo "Envolvidos :".$txt_envolvidos;
   if ($insere=='I') {
      $sql =        " insert into projetos.processos ( ";
      $sql = $sql . "    desc_proc,                    ";
      $sql = $sql . "    data,                         ";
      $sql = $sql . "    procedimento,                 ";
	  $sql = $sql . "	 objetivo,						";	// garcia - 09/02/2004
	  $sql = $sql . "	 insumos, 						";	// garcia - 09/02/2004
	  $sql = $sql . "	 produtos, 						";	// garcia - 09/02/2004
	  $sql = $sql . "	 cd_processo_pai,				";	// garcia - 09/02/2004
	  $sql = $sql . "	 envolvidos,					";	// garcia - 09/03/2004
  	  $sql = $sql . "	 requisitos_aplicaveis,			";	// garcia - 17/03/2004
	  $sql = $sql . "	 sub_processos,					";	// garcia - 17/03/2004
      $sql = $sql . "    cod_responsavel                ";
      $sql = $sql . " )                             ";
      $sql = $sql . " VALUES (                      ";
      $sql = $sql . "    '$descricao',      ";
      $sql = $sql . "     $txt_dt_inicio ,  ";
      $sql = $sql . "    '$processo',       ";
	  $sql = $sql . "	 '$objetivo',		";	// garcia - 09/02/2004
	  $sql = $sql . "	 '$insumos',		";	// garcia - 09/02/2004
	  $sql = $sql . "	 '$produtos',		";	// garcia - 09/02/2004
	  $sql = $sql . "	  $txt_processo_pai,";	// garcia - 09/02/2004
	  $sql = $sql . "    '$txt_envolvidos',	";	// garcia - 09/03/2004
	  $sql = $sql . "	  '$requisitos',	";	// garcia - 17/03/2004
	  $sql = $sql . "	  '$sub_processos',	";	// garcia - 17/03/2004
      $sql = $sql . "     '$responsavel'    ";
      $sql = $sql . ")";
//	  echo $sql;
   }
   else {
      $sql =        " update projetos.processos set            ";
      $sql = $sql . "        desc_proc          	= '$descricao', ";
      $sql = $sql . "        data               	= $txt_dt_inicio, "; 
      $sql = $sql . "        procedimento       	= '$processo',  ";
	  $sql = $sql . "		 objetivo				= '$objetivo', ";		// garcia - 09/02/2004
	  $sql = $sql . "		 insumos				= '$insumos', ";		// garcia - 09/02/2004
	  $sql = $sql . "		 produtos				= '$produtos', ";		// garcia - 09/02/2004
	  $sql = $sql . "		 cd_processo_pai		= $txt_processo_pai, ";	// garcia - 09/02/2004
	  $sql = $sql . "		 envolvidos				= '$txt_envolvidos', ";	// garcia - 17/03/2004
  	  $sql = $sql . "		 requisitos_aplicaveis	= '$requisitos', ";		// garcia - 17/03/2004
  	  $sql = $sql . "		 sub_processos			= '$sub_processos', ";	// garcia - 17/03/2004
      $sql = $sql . "        cod_responsavel   		= '$responsavel'  ";
      $sql = $sql . " where cd_processo = $cod_processo        ";
   }
//	echo $sql;  
   if (pg_exec($db, $sql)) {
	 header('location: lst_processos.php');
   }
   else {
      echo "Ocorreu um erro ao tentar incluir este processo";
   }
   pg_close($db);

   function convdata_br_iso($dt) {
      // Pressupѕe que a data esteja no formato DD/MM/AAAA
      // A melhor forma de gravar datas no PostgreSQL щ utilizando 
      // uma string no formato DDDD-MM-AA. Esta funчуo justamente 
      // adequa a data a este formato
      $d = substr($dt, 0, 2);
      $m = substr($dt, 3, 2);
      $a = substr($dt, 6, 4);
      return $a.'-'.$m.'-'.$d;
   }

?>