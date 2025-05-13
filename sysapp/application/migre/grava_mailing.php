<?
   include_once('inc/sessao.php');
   include_once('inc/conexao.php');
//   include_once('./../inc/class.Email.inc.php');
// ---------------------------------------------------------------------	
	$txt_dt_cad  = ( $dt_cadastro  == '' ? 'Null' : "'".convdata_br_iso($dt_cadastro)."'" );
	$txt_dt_fechado  = ( $dt_encerramento  == '' ? 'Null' : "'".convdata_br_iso($dt_encerramento)."'" );
	$causa  = ( $causa  == '' ? 'Null' : $causa);
	$cod_responsavel = ($responsavel == '' ? '0' : $responsavel);
	$cod_processo = substr($processo,0,2);
	$cep  = ( $cep  == '' ? 'Null' :  str_replace('-','',$cep));
	$cnpj  = ( $cnpj  == '' ? 'Null' :  str_replace('-','',$cnpj));
	$telefone  = ( is_numeric(str_replace(')','',str_replace('(','',str_replace('.','',str_replace('-','',$telefone))))) ?  str_replace(')','',str_replace('(','',str_replace('.','',str_replace('-','',$telefone)))) : 'Null');
	$celular  = ( $celular  == '' ? 'Null' :  str_replace('-','',$celular));
	$fax  = ( $fax  == '' ? 'Null' :  str_replace('-','',$fax));
	if ($ddd == '') { $ddd = 0; }
	$patrocinadora  = ( $patrocinadora  == '' ? 'Null' : $patrocinadora);
	$cd_registro_empregado  = ( $cd_registro_empregado  == '' ? 'Null' : $cd_registro_empregado);
	$cidade  = ( $cidade  == '' ? 'Null' : $cidade);
	$seq_dependencia  = ( $seq_dependencia  == '' ? 'Null' : $seq_dependencia);
	$numero  = ( $numero  == '' ? 'Null' : $numero);
// ---------------------------------------------------------------------
	if ($insere=='I') {
		$sql =        " insert into expansao.mailing ( ";
		$sql = $sql . "	 nome_pessoa,			";
		$sql = $sql . "  nome_empresa_entidade, ";
		$sql = $sql . "	 cargo,					";
		$sql = $sql . "	 endereco,				";
		$sql = $sql . "	 numero,				";
		$sql = $sql . "	 complemento,			";
		$sql = $sql . "	 bairro,				";
		$sql = $sql . "  estado,             	";
		$sql = $sql . "  cep,       	      	";
		$sql = $sql . "  cnpj,			      	";
		$sql = $sql . "  url,   		       	";
		$sql = $sql . "  email_1,     		  	";
		$sql = $sql . "  ddd,					";
		$sql = $sql . "  telefone_comercial,	";
		$sql = $sql . "	 celular,				";
		$sql = $sql . "	 fax,					";
		$sql = $sql . "	 cd_municipio,			";
		$sql = $sql . "	 cd_comunidade,			";
		$sql = $sql . "	 cd_com_secundaria,		";
		$sql = $sql . "	 cd_empresa,			";
		$sql = $sql . "	 cd_registro_empregado,	";
		$sql = $sql . "	 seq_dependencia,		";
		$sql = $sql . "	 flag_confirmado,		";
		$sql = $sql . "  nome_sem_acento,		";
		$sql = $sql . "  cd_emp_inst		";
		$sql = $sql . " )                      	";
		$sql = $sql . " VALUES (            ";
		$sql = $sql . " '$nome',			";
		$sql = $sql . " '$nome_empresa',  	";
		$sql = $sql . " '$cargo',  			"; 
		$sql = $sql . "	'$logradouro',  	";
		$sql = $sql . "	 $numero,			";
		$sql = $sql . "	'$complemento',		";
		$sql = $sql . "	'$bairro',		  	";
		$sql = $sql . "	'$lista_estados', 	";
		$sql = $sql . "	$cep,  				";
		$sql = $sql . " $cnpj,  			";
		$sql = $sql . " '$site',			";
		$sql = $sql . " '$email',		   	";
		$sql = $sql . " $ddd,				";
		$sql = $sql . " $telefone,			";
		$sql = $sql . " $celular,			";
		$sql = $sql . " $fax,				";
		$sql = $sql . " $cidade,			";
		$sql = $sql . " '$comunidade',		";
		$sql = $sql . " '$com_secundaria',	";
		$sql = $sql . " $patrocinadora,		";
		$sql = $sql . " $cd_registro_empregado,	";
		$sql = $sql . " $seq_dependencia,	";
		$sql = $sql . "	'$chk_presenca_confirmada', ";
		$sql = $sql . " upper('$nome'),		";
		$sql = $sql . " $cd_emp_inst		";
		$sql = $sql . ")";
	}
   else {
      $sql =        " update expansao.mailing set ";	  
      $sql = $sql . "        nome_pessoa   		= '$nome', ";
      $sql = $sql . "        nome_empresa_entidade	= '$nome_empresa', "; 	  
	  $sql = $sql . "        cargo			    = '$cargo', ";
	  $sql = $sql . "		 endereco			= '$logradouro', ";	
	  $sql = $sql . "		 numero				= $numero, ";	
	  $sql = $sql . "		 complemento		= '$complemento', ";	
	  $sql = $sql . "		 bairro				= '$bairro', ";	
	  $sql = $sql . "        estado     		= '$lista_estados', "; 
	  $sql = $sql . "        cep     			= $cep, "; 
      $sql = $sql . "        cnpj         		= $cnpj, ";
      $sql = $sql . "        url      			= '$site', ";
	  $sql = $sql . "        ddd				= $ddd, ";
	  $sql = $sql . "        telefone_comercial	= $telefone, ";
	  $sql = $sql . "		 email_1		 	= '$email',	";
	  $sql = $sql . "		 celular		 	= $celular, ";
	  $sql = $sql . "		 fax		 		= $fax, ";
	  $sql = $sql . "		 cd_municipio		= $cidade, ";
	  $sql = $sql . "		 cd_emp_inst		= $cd_emp_inst, ";
	  $sql = $sql . "		 cd_comunidade		= '$comunidade', ";
	  $sql = $sql . "		 cd_com_secundaria	= '$com_secundaria', ";
	  $sql = $sql . "		 cd_empresa			= $patrocinadora, ";
	  $sql = $sql . "		 cd_registro_empregado = $cd_registro_empregado, ";
	  $sql = $sql . "		 seq_dependencia	= $seq_dependencia, ";
	  $sql = $sql . "		 flag_confirmado	= '$chk_presenca_confirmada', ";
		$sql = $sql . " 	nome_sem_acento 	= upper('$nome') ";
	  $sql = $sql . " where cd_mailing  		= $codigo  	";
   }
//   echo $sql;
// ---------------------------------------------------------------------

   if (pg_exec($db, $sql)) {
	   if ($insere=='I') {
		    $tpEmail = 'I'; 
            $sql =        " select max(cd_mailing) as num ";
            $sql = $sql . " from   expansao.mailing ";
            $rs = pg_exec($db, $sql);
            $reg = pg_fetch_array($rs);
            $codigo = $reg['num'];
         }		
		pg_close($db);
		if ($scep == 'N') {
			header('location: lst_mailing.php?cep='.$scep);
		} else {
			header('location: cad_mailing.php?c='.$codigo.'&tr=U');
		}
	}
	else {
		pg_close($db);
		header('location: cad_mailing.php?c='.$codigo.'&tr=U&msg=Ocorreu um erro ao tentar incluir este registro');
	}
 
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