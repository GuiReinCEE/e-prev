<?
   include_once('inc/sessao.php');
   include_once('inc/conexao.php');
   include_once('inc/class.Email.inc.php');
// ------------------------------------------------------------
	if ($cnpj == '') { $cnpj = 0; }
	if ($cep == '') { $cep = 0; }
	if ($ddd == '') { $ddd = 0; }
	if ($telefone == '') { $telefone = 0; }
	if ($fax == '') { $fax = 0; }
	if ($num_func == '') { $num_func = 0; }
	if ($cd_ramo == '') { $cd_ramo = 0; }
	if ($cd_mailing == '') { $cd_mailing = 0; }	
	if ($lista_cidades == '') { $lista_cidades = 0; }
	if ($relacionamento == '') { $relacionamento = 0; }
// ------------------------------------------------------------
	if ($insere=='I') {
		$sql =        "insert into 	expansao.empresas_instituicoes ( ";
		$sql = $sql . "		nome_empresa_entidade, ";
	    $sql = $sql . "		cnpj, ";	
		$sql = $sql . "		endereco, ";
		$sql = $sql . "		complemento, ";
		$sql = $sql . "		cep, ";
		$sql = $sql . "		ddd, ";
		$sql = $sql . "		telefone_comercial, ";
		$sql = $sql . "		fax, ";
		$sql = $sql . "		url, ";
		$sql = $sql . "		bairro, ";
		$sql = $sql . "		email, ";
		$sql = $sql . "		num_funcionarios, ";
		$sql = $sql . "		cd_segmento, ";
		$sql = $sql . "		cd_ramo, ";
		$sql = $sql . "		cd_municipio, ";
		$sql = $sql . "		cd_porte, ";
		$sql = $sql . "		estado, ";
		$sql = $sql . "		possui_plano, ";
		$sql = $sql . "		com_quem, ";
		$sql = $sql . "		relacionamento ";
	    $sql = $sql . " ) ";
    	$sql = $sql . " VALUES ( ";
	    $sql = $sql . "		'$nome_empresa_entidade', ";
    	$sql = $sql . "		$cnpj, ";
	    $sql = $sql . "		'$endereco', ";
		$sql = $sql . "		'$complemento',	";
		$sql = $sql . "		$cep, ";
		$sql = $sql . "		$ddd,	";
    	$sql = $sql . "		$telefone, ";
		$sql = $sql . "		$fax, ";
		$sql = $sql . "		'$url', ";
	    $sql = $sql . "    	'$bairro',	";
		$sql = $sql . "    	'$email',	";
		$sql = $sql . "    	$num_func,	";
		$sql = $sql . "    	'$segmento',	";
		$sql = $sql . "    	$cd_ramo,	";
		$sql = $sql . "    	$lista_cidades,	";
		$sql = $sql . "    	'$porte',	";
		$sql = $sql . "    	'$lista_estados',	";
		$sql = $sql . "		'$possui_plano', ";
		$sql = $sql . "		'$plano_previdencia', ";
		$sql = $sql . "    	$relacionamento	";
    	$sql = $sql . ")";
	}
	else {
		$sql = " update 	expansao.empresas_instituicoes set ";
		$sql = $sql . "		nome_empresa_entidade = '$nome_empresa_entidade', ";
	    $sql = $sql . "		cnpj = $cnpj, ";	
		$sql = $sql . "		endereco = '$endereco', ";
		$sql = $sql . "		complemento = '$complemento', ";
		$sql = $sql . "		cep = $cep, ";
		$sql = $sql . "		ddd = $ddd, ";
		$sql = $sql . "		telefone_comercial = $telefone, ";
		$sql = $sql . "		fax = $fax, ";
		$sql = $sql . "		url = '$url', ";
		$sql = $sql . "		bairro = '$bairro', ";
		$sql = $sql . "		email = '$email', ";
		$sql = $sql . "		num_funcionarios = $num_func, ";
		$sql = $sql . "		cd_segmento = '$segmento', ";
		$sql = $sql . "		cd_ramo = $cd_ramo, ";
		$sql = $sql . "		cd_municipio = $lista_cidades, ";
		$sql = $sql . "		cd_porte = '$porte', ";
		$sql = $sql . "		estado = '$lista_estados',";
		$sql = $sql . "		possui_plano = '$possui_plano', ";
		$sql = $sql . "		com_quem = '$plano_previdencia', ";
		$sql = $sql . "		relacionamento = $relacionamento";
		$sql = $sql . "		where cd_emp_inst	= $codigo ";	
	}
//	echo $sql;
	if (pg_exec($db, $sql)) {
		if ($insere == 'I') {
			$sql = "select max(cd_emp_inst) as ultimo_reg from expansao.empresas_instituicoes ";
			$rs = pg_exec($db, $sql);
			$reg = pg_fetch_array($rs);
			$codigo = $reg['ultimo_reg'];
		}
		pg_close($db);
		header('location: cad_empresas.php?c='.$codigo);
   }
   else {
      pg_close($db);
	  header('location: cad_empresas.php?c='.$codigo.'&msg=Ocorreu um erro ao tentar incluir este evento');
   }
// ------------------------------------------------------------
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
//-----------------------------------------------------------------------------------------------
?>