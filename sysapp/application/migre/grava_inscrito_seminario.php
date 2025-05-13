<?
	require_once('inc/sessao.php');
	require_once('inc/conexao.php');
	require_once('inc/nextval_sequence.php');
	
	#### ABRE TRANSACAO COM O BD #####
	pg_query($db,"BEGIN TRANSACTION");	

	if(trim($_POST['codigo']) == "")
	{
		#### PEGA NEXTVAL DA SEQUENCE DO CAMPO
		$nr_inscricao = getNextval("acs", "seminario", "codigo", $db); 
		
		#### TESTA SE RETORNOU ALGUM VALOR
		if ($nr_inscricao > 0) 
		{		
			#### INSERT ####
			$qr_sql = "
						INSERT INTO acs.seminario 
							 (
							   codigo,
							   nome,  
							   nome_sem_acento,
							   cargo,   
							   empresa,   
							   endereco,   
							   numero,   
							   complemento,   
							   cidade,   
							   uf,   
							   cep,   
							   telefone_ddd,   
							   telefone,   
							   telefone_ramal,   
							   fax_ddd,   
							   fax,   
							   fax_ramal,   
							   celular_ddd,   
							   celular,   
							   patrocinadora,   
							   re,
							   sequencia,   
							   email,   
							   autoriza_mailing,   
							   data_cadastro,   
							   hora_cadastro,
							   cd_seminario_edicao,
							   cd_barra
							 )   
						VALUES 
							 (
							   ".$nr_inscricao.",
							   ".trim((trim($_POST['nome'])           == '' ? 'NULL' : "'".$_POST['nome']."'")).",
							   ".strtoupper(removeAcentos(trim((trim($_POST['nome']) == '' ? 'NULL' : "'".$_POST['nome']."'")))).",
							   ".trim((trim($_POST['cargo'])          == '' ? 'NULL' : "'".$_POST['cargo']."'")).",
							   ".trim((trim($_POST['empresa'])        == '' ? 'NULL' : "'".$_POST['empresa']."'")).",  
							   ".trim((trim($_POST['endereco'])       == '' ? 'NULL' : "'".$_POST['endereco']."'")).",
							   ".trim((trim($_POST['numero'])         == '' ? 'NULL' : "'".$_POST['numero']."'")).",
							   ".trim((trim($_POST['complemento'])    == '' ? 'NULL' : "'".$_POST['complemento']."'")).",
							   ".trim((trim($_POST['cidade'])         == '' ? 'NULL' : "'".$_POST['cidade']."'")).",
							   ".trim((trim($_POST['uf'])             == '' ? 'NULL' : "'".$_POST['uf']."'")).",
							   ".trim((trim($_POST['cep'])            == '' ? 'NULL' : "'".$_POST['cep']."'")).",
							   ".trim((trim($_POST['telefone_ddd'])   == '' ? 'NULL' : "'".$_POST['telefone_ddd']."'")).",
							   ".trim((trim($_POST['telefone'])       == '' ? 'NULL' : "'".$_POST['telefone']."'")).",
							   ".trim((trim($_POST['telefone_ramal']) == '' ? 'NULL' : "'".$_POST['telefone_ramal']."'")).",
							   ".trim((trim($_POST['fax_ddd'])        == '' ? 'NULL' : "'".$_POST['fax_ddd']."'")).",
							   ".trim((trim($_POST['fax'])            == '' ? 'NULL' : "'".$_POST['fax']."'")).",
							   ".trim((trim($_POST['fax_ramal'])      == '' ? 'NULL' : "'".$_POST['fax_ramal']."'")).",
							   ".trim((trim($_POST['celular_ddd'])    == '' ? 'NULL' : "'".$_POST['celular_ddd']."'")).",
							   ".trim((trim($_POST['celular'])        == '' ? 'NULL' : "'".$_POST['celular']."'")).",
							   ".trim((trim($_POST['patrocinadora'])  == '' ? 'NULL' : $_POST['patrocinadora'])).",  
							   ".trim((trim($_POST['re'])             == '' ? 'NULL' : $_POST['re'])).",  
							   ".trim((trim($_POST['seq'])            == '' ? 'NULL' : $_POST['seq'])).", 
							   ".trim((trim($_POST['email'])          == '' ? 'NULL' : "'".$_POST['email']."'")).",						   
							   ".trim((trim($_POST['mailing'])        == '' ? 'FALSE' : $_POST['mailing'])).",
							   CURRENT_DATE , 
							   TO_CHAR(CURRENT_TIMESTAMP, 'HH24:MI:SS'),
							   ".trim((trim($_POST['seminario'])      == '' ? 'NULL' : $_POST['seminario'])).",
							   ".trim((trim($_POST['cd_barra'])       == '' ? "to_number((funcoes.codigo_barra_seminario()), '999999999999')" : substr($_POST['cd_barra'], 0, 12)))."
							 );					
					  ";
		}
		else
		{
			$ds_erro = "ERRO: ".str_replace("ERROR:","",pg_last_error($db));
			#### DESFAZ A TRANSACAO COM BD ####
			pg_query($db,"ROLLBACK TRANSACTION");
			pg_close($db);
			echo $ds_erro; 
			exit;
		}
	}
	else
	{
		#### UPDATE ####
		$qr_sql = "
					UPDATE acs.seminario 
					   SET nome               = ".trim((trim($_POST['nome'])           == '' ? 'NULL' : "'".$_POST['nome']."'")).",                    
						   nome_sem_acento	  = ".strtoupper(removeAcentos(trim((trim($_POST['nome']) == '' ? 'NULL' : "'".$_POST['nome']."'")))).",
						   cargo              = ".trim((trim($_POST['cargo'])          == '' ? 'NULL' : "'".$_POST['cargo']."'")).",
						   empresa    		  = ".trim((trim($_POST['empresa'])        == '' ? 'NULL' : "'".$_POST['empresa']."'")).",  
						   endereco   		  = ".trim((trim($_POST['endereco'])       == '' ? 'NULL' : "'".$_POST['endereco']."'")).",
						   numero   		  = ".trim((trim($_POST['numero'])         == '' ? 'NULL' : "'".$_POST['numero']."'")).",
						   complemento   	  = ".trim((trim($_POST['complemento'])    == '' ? 'NULL' : "'".$_POST['complemento']."'")).",
						   cidade   		  = ".trim((trim($_POST['cidade'])         == '' ? 'NULL' : "'".$_POST['cidade']."'")).",
						   uf   			  = ".trim((trim($_POST['uf'])             == '' ? 'NULL' : "'".$_POST['uf']."'")).",
						   cep   			  = ".trim((trim($_POST['cep'])            == '' ? 'NULL' : "'".$_POST['cep']."'")).",
						   telefone_ddd   	  =	".trim((trim($_POST['telefone_ddd'])   == '' ? 'NULL' : "'".$_POST['telefone_ddd']."'")).",
						   telefone   		  = ".trim((trim($_POST['telefone'])       == '' ? 'NULL' : "'".$_POST['telefone']."'")).",
						   telefone_ramal     =	".trim((trim($_POST['telefone_ramal']) == '' ? 'NULL' : "'".$_POST['telefone_ramal']."'")).",
						   fax_ddd   		  =	".trim((trim($_POST['fax_ddd'])        == '' ? 'NULL' : "'".$_POST['fax_ddd']."'")).",
						   fax   			  =	".trim((trim($_POST['fax'])            == '' ? 'NULL' : "'".$_POST['fax']."'")).",
						   fax_ramal   		  =	".trim((trim($_POST['fax_ramal'])      == '' ? 'NULL' : "'".$_POST['fax_ramal']."'")).",
						   celular_ddd   	  = ".trim((trim($_POST['celular_ddd'])    == '' ? 'NULL' : "'".$_POST['celular_ddd']."'")).",
						   celular   		  = ".trim((trim($_POST['celular'])        == '' ? 'NULL' : "'".$_POST['celular']."'")).",
						   patrocinadora   	  =	".trim((trim($_POST['patrocinadora'])  == '' ? 'NULL' : $_POST['patrocinadora'])).",  
						   re				  = ".trim((trim($_POST['re'])             == '' ? 'NULL' : $_POST['re'])).",  
						   sequencia   		  = ".trim((trim($_POST['seq'])            == '' ? 'NULL' : $_POST['seq'])).", 
						   email   			  = ".trim((trim($_POST['email'])          == '' ? 'NULL' : "'".$_POST['email']."'")).",						
						   fl_presente		  = ".trim((trim($_POST['fl_presente'])    == 'S' ? "'S'" : "'N'")).",						
						   autoriza_mailing   =	".trim((trim($_POST['mailing'])        == '' ? 'FALSE' : $_POST['mailing'])).",
						   cd_seminario_edicao = ".trim((trim($_POST['seminario'])     == '' ? 'NULL' : $_POST['seminario']))."
					 WHERE codigo = ".$_POST['codigo']."
				  ";		
	}
	
	//echo "<PRE>"; echo $qr_sql; exit; #### DEBUG	
	$ob_resul= @pg_query($db,$qr_sql);
	if(!$ob_resul)
	{
		$ds_erro = "ERRO: ".str_replace("ERROR:","",pg_last_error($db));
		#### DESFAZ A TRANSACAO COM BD ####
		pg_query($db,"ROLLBACK TRANSACTION");
		pg_close($db);
		echo $ds_erro; 
		exit;
	}
	else
	{
		#### COMITA DADOS NO BD ####
		pg_query($db,"COMMIT TRANSACTION"); 

		echo "
				<script>
					document.location.href = 'cad_inscritos_seminario.php?c=".trim((trim($_POST['codigo']) == '' ? $nr_inscricao : $_POST['codigo']))."';
				</script>
			 ";				

		pg_close($db);
	}
	
	
	
	function removeAcentos($ds_string)
	{
		$a = array(
		'/[ÂÀÁÄÃ]/'=>'A',
		'/[âãàáä]/'=>'a',
		'/[ÊÈÉË]/'=>'E',
		'/[êèéë]/'=>'e',
		'/[ÎÍÌÏ]/'=>'I',
		'/[îíìï]/'=>'i',
		'/[ÔÕÒÓÖ]/'=>'O',
		'/[ôõòóö]/'=>'o',
		'/[ÛÙÚÜ]/'=>'U',
		'/[ûúùü]/'=>'u',
		'/ç/'=>'c',
	    '/Ç/'=> 'C');
		return preg_replace(array_keys($a), array_values($a), $ds_string);
	}	

    pg_close($db);
   
   
   
   /*

	if ($sequencia == '') { $sequencia = 0; }
	if ($patrocinadora == '') { $patrocinadora = 0; }
	if ($re == '') { $re = 0; }
   if ($codigo <> "") {
      $sql =        " update acs.seminario      ";
      $sql = $sql . " set nome = '$nome', ";
      $sql = $sql . "     cargo = '$cargo',  ";
	  $sql = $sql . "     empresa = '$empresa',  ";
	  $sql = $sql . "     endereco = '$endereco',  ";
	  $sql = $sql . "     cidade = '$cidade',  ";
	  $sql = $sql . "     uf = '$uf',  ";
	  $sql = $sql . "     cep = '$cep',  ";
	  $sql = $sql . "     telefone = '$telefone',  ";
	  $sql = $sql . "     telefone_ramal = '$telefone_ramal',  ";
	  $sql = $sql . "     fax = '$fax',  ";
	  $sql = $sql . "     fax_ramal = '$fax_ramal',  ";
	  $sql = $sql . "     telefone_ddd = '$telefone_ddd',  ";
	  $sql = $sql . "     fax_ddd = '$fax_ddd',  ";
	  $sql = $sql . "     email = '$email',  ";
//	  $sql = $sql . "     autoriza_mailing = '$autoriza_mailing',  ";
	  $sql = $sql . "     celular_ddd = '$celular_ddd',  ";
	  $sql = $sql . "     celular = '$celular',  ";
	  $sql = $sql . "     numero = '$numero',  ";
	  $sql = $sql . "     complemento = '$complemento',  ";
	  $sql = $sql . "     sequencia = $sequencia,  ";
	  $sql = $sql . "     patrocinadora = $patrocinadora,  ";
	  $sql = $sql . "     re = $re  ";
      $sql = $sql . " where codigo = '$codigo'      ";
   }
   else {
      $sql =        " insert into acs.seminario ( ";
      $sql = $sql . " 	  nome, ";
      $sql = $sql . "     cargo,  ";
	  $sql = $sql . "     empresa,  ";
	  $sql = $sql . "     endereco,  ";
	  $sql = $sql . "     cidade,  ";
	  $sql = $sql . "     uf,  ";
	  $sql = $sql . "     cep,  ";
	  $sql = $sql . "     telefone,  ";
	  $sql = $sql . "     telefone_ramal,  ";
	  $sql = $sql . "     fax,  ";
	  $sql = $sql . "     fax_ramal,  ";
	  $sql = $sql . "     telefone_ddd,  ";
	  $sql = $sql . "     fax_ddd,  ";
	  $sql = $sql . "     email,  ";
//	  $sql = $sql . "     autoriza_mailing,  ";
	  $sql = $sql . "     celular_ddd,  ";
	  $sql = $sql . "     celular,  ";
	  $sql = $sql . "     numero,  ";
	  $sql = $sql . "     complemento,  ";
	  $sql = $sql . "     sequencia,  ";
	  $sql = $sql . "     patrocinadora,  ";
	  $sql = $sql . "     re ) ";
      $sql = $sql . " values (                ";
      $sql = $sql . " 	  '$nome', ";
      $sql = $sql . "     '$cargo',  ";
	  $sql = $sql . "     '$empresa',  ";
	  $sql = $sql . "     '$endereco',  ";
	  $sql = $sql . "     '$cidade',  ";
	  $sql = $sql . "     '$uf',  ";
	  $sql = $sql . "     '$cep',  ";
	  $sql = $sql . "     '$telefone',  ";
	  $sql = $sql . "     '$telefone_ramal',  ";
	  $sql = $sql . "     '$fax',  ";
	  $sql = $sql . "     '$fax_ramal',  ";
	  $sql = $sql . "     '$telefone_ddd',  ";
	  $sql = $sql . "     '$fax_ddd',  ";
	  $sql = $sql . "     '$email',  ";
//	  $sql = $sql . "     '$autoriza_mailing',  ";
	  $sql = $sql . "     '$celular_ddd',  ";
	  $sql = $sql . "     '$celular',  ";
	  $sql = $sql . "     '$numero',  ";
	  $sql = $sql . "     '$complemento',  ";
	  $sql = $sql . "     $sequencia,  ";
	  $sql = $sql . "     $patrocinadora,  ";
	  $sql = $sql . "     $re ) ";
   }

   if ($rs=pg_exec($db, $sql)) {
      pg_close($db);
      header('location: lst_inscritos_seminario.php?msg=Ocorreu um erro ao tentar gravar o projeto.');
   }
   else {
      pg_close($db);
      header('location: lst_inscritos_seminario.php');
   }
   */
?>