<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
    include_once('inc/nextval_sequence.php');
	
	$_POST['cd_divisao_responsavel'] = (trim($_POST['cd_divisao_responsavel']) == '' ? $_SESSION['D'] : $_POST['cd_divisao_responsavel']);
	$_POST['site'] = (trim($_POST['site']) == '' ? 0 : $_POST['site']);
	$_POST['evento'] = (trim($_POST['evento']) == '' ? 0 : $_POST['evento']);
	$_POST['servico'] = (trim($_POST['servico']) == '' ? 0 : $_POST['servico']);
	$_POST['publicacao'] = (trim($_POST['publicacao']) == '' ? 0 : $_POST['publicacao']);
	$_POST['ultimo_respondente'] = (trim($_POST['ultimo_respondente']) == '' ? 0 : $_POST['ultimo_respondente']);
	$_POST['dt_inicio'] = ($_POST['dt_inicio'] == '' ? 'NULL' : "TO_TIMESTAMP('".$_POST['dt_inicio']." ".$_POST['hr_inicio']."','DD/MM/YYYY HH24:MI')");
	$_POST['dt_fim']    = ($_POST['dt_fim']    == '' ? 'NULL' : "TO_TIMESTAMP('".$_POST['dt_fim']." ".$_POST['hr_fim']."','DD/MM/YYYY HH24:MI')");
	
	if ($_POST['codigo'] <> "") 
	{
		$sql = " 
		         UPDATE	projetos.enquetes 
					SET	titulo                  = '".$_POST['titulo']."',  
		     	        dt_inicio               = ".$_POST['dt_inicio'].", 
		     	        dt_fim                  = ".$_POST['dt_fim'].", 
		     	        cd_site                 = ".$_POST['site'].", 
		     	        cd_evento_institucional = ".$_POST['evento'].", 
		     	        cd_publicacao           = ".$_POST['publicacao'].", 
		     	        cd_servico              = ".$_POST['servico'].", 
		     	        tipo_enquete            = '".$_POST['sel_tipo_enquete']."', 
		     	        tipo_layout             = '".$_POST['sel_layout']."',  
			   ";
		if (trim($_POST['cd_divisao_responsavel']) != '') 
		{ 
			$sql.= "    cd_divisao_responsavel          = '".$_POST['cd_divisao_responsavel']."',"; 
		}		
		if (trim($cbo_responsavel) != '') 
		{ 
			$sql.= "    cd_responsavel          = ".$_POST['cbo_responsavel'].","; 
		}
		$sql.= "     	texto_abertura          = '".$_POST['abertura']."', 
		                texto_encerramento      = '".$_POST['encerramento']."',
						controle_respostas      = '".$_POST['opc_preenchimento']."',
						nr_publico_total        = ".$_POST['nr_publico_total']."
				  WHERE cd_enquete              = ".$_POST['codigo'];

        $cd_enquete = $_POST['codigo'];

	}
	else 
	{
        $cd_enquete = getNextval("projetos", "enquetes", "cd_enquete", $db);
		$sql = " 
				INSERT INTO projetos.enquetes 
				     ( 
                       cd_enquete,
			           titulo,
					   dt_inicio, 
			           dt_fim, 
			           cd_site, 
			           cd_evento_institucional, 
			           cd_publicacao, 
			           cd_servico, 
			           tipo_enquete, 
			           tipo_layout, 
			           cd_responsavel, 
			           texto_abertura, 
			           texto_encerramento, 
			     	   controle_respostas,
	                   nr_publico_total,
					   cd_divisao_responsavel
					 ) 
		        VALUES 	
				     ( 
                       ".$cd_enquete.", 
		        	   '".$_POST['titulo']."', 
		        	   ".$_POST['dt_inicio'].", 
		        	   ".$_POST['dt_fim'].", 
		        	   ".$_POST['site'].", 
		        	   ".$_POST['evento'].", 
		        	   ".$_POST['publicacao'].", 
		        	   ".$_POST['servico'].", 
		        	   '".$_POST['sel_tipo_enquete']."', 
		        	   '".$_POST['sel_layout']."', 
		        	   ".$_POST['cbo_responsavel'].", 
		        	   '".$_POST['abertura']."', 
		        	   '".$_POST['encerramento']."', 
		     	       '".$_POST['opc_preenchimento']."',
		               ".($_POST['nr_publico_total'] == "" ? 0 : $_POST['nr_publico_total']).",
					   '".$_POST['cd_divisao_responsavel']."'
					 )
		       ";
	}

	
	#### ---> ABRE TRANSACAO COM O BD <--- ####
	pg_query($db,"BEGIN TRANSACTION");	
	$ob_resul= @pg_query($db,$sql);
	if(!$ob_resul)
	{
		$ds_erro = "ERRO: ".str_replace("ERROR:","",pg_last_error($db));
		#### ---> DESFAZ A TRANSACAO COM BD <--- ####
		pg_query($db,"ROLLBACK TRANSACTION");
		pg_close($db);
		echo $ds_erro;
		echo "<pre>".$sql;
		exit;
	}
	else
	{
		#### ---> COMITA DADOS NO BD <--- ####
		pg_query($db,"COMMIT TRANSACTION"); 		
		
        header('location: cad_enquetes_definicao.php?c=' . $cd_enquete . '');
        /*if ($cd_enquete != "") {
            header('location: cad_enquetes_definicao.php?c=' . $cd_enquete . '');
		}
        else{
            header('location: lst_enquetes.php');
        }*/
		
        pg_close($db);
	}		
?>