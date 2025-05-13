<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/class.Email.inc.php');
	include_once('inc/nextval_sequence.php');

	// ---> ABRE TRANSACAO COM O BD <--- //
	pg_query($db,"BEGIN TRANSACTION");
	if ($insere == 'A') 
	{
		#### UPDATE ####
		$sql = "
		        UPDATE projetos.acompanhamento_projetos 
		           SET cd_projeto  = ".$_POST['projeto'].",
				       texto_acomp = NULL,
					   desc_ar     = '".$_POST['desc_ar']."',
					   desc_es     = '".$_POST['desc_es']."',
					   desc_au     = '".$_POST['desc_au']."',
					   desc_de     = '".$_POST['desc_de']."',
					   desc_me     = '".$_POST['desc_me']."',
					   status_ar   = '".$_POST['status_ar']."',
					   status_es   = '".$_POST['status_es']."',
					   status_au   = '".$_POST['status_au']."',
					   status_de   = '".$_POST['status_de']."',
					   status_me   = '".$_POST['status_me']."',
					   dt_acomp    = ".($_POST['dt_acomp'] == '' ? 'NULL' : "TO_DATE('".$_POST['dt_acomp']."','DD/MM/YYYY')")."
				 WHERE cd_acomp    = ".$_POST['cd_acomp']."; 
			   ";
		if(count($_POST['opt_analista']) > 0)
		{
			$sql.= " DELETE 
					   FROM projetos.analista_projeto 
					  WHERE cd_projeto = ".$_POST['projeto']."
					    AND cd_acomp   = ".$_POST['cd_acomp'].";
				   ";
			$nr_conta = 0;
			$nr_fim   = count($_POST['opt_analista']);
			while($nr_conta < $nr_fim)
			{
				$sql.= " INSERT INTO projetos.analista_projeto 
				                   ( 
								     cd_projeto, 
									 cd_acomp,
									 cd_analista 
								   ) 
						      VALUES 
							       ( 
								     ".$_POST['projeto'].", 
									 ".$_POST['cd_acomp'].",
									 ".$_POST['opt_analista'][$nr_conta]." 
								   );
					   ";	
				$nr_conta++;
			}
        }	   
			   
		$qr_roteiro = " 
				        SELECT rpr.cd_reunioes_projetos_roteiro,
                               rpi.cd_reunioes_projetos_inicio
                          FROM projetos.reunioes_projetos_roteiro rpr
                          LEFT JOIN projetos.reunioes_projetos_inicio rpi
                            ON rpr.cd_reunioes_projetos_roteiro = rpi.cd_reunioes_projetos_roteiro
                           AND rpi.cd_acomp                     = ".$_POST['cd_acomp']."
                         ORDER BY rpr.nr_ordem ";
		$ob_resul = pg_query($db, $qr_roteiro);
		$qr_respostas = "";
		while ($ob_reg = pg_fetch_object($ob_resul)) 
		{
			if($ob_reg->cd_reunioes_projetos_inicio == "")
			{
				#### INSERT ####
				$qr_respostas.= "
								INSERT INTO projetos.reunioes_projetos_inicio
                                          (
										    cd_acomp,
											cd_reunioes_projetos_roteiro, 
											ds_resposta, 
                                            cd_usuario
										  )
                                     VALUES 
									      (
											".$_POST['cd_acomp'].",
											".$ob_reg->cd_reunioes_projetos_roteiro.",
											'".$_POST['ds_resposta'][$ob_reg->cd_reunioes_projetos_roteiro]."',
											".$_SESSION['Z']."
                                          );
				                ";

			}
			else
			{
				#### UPDATE ####
				$qr_respostas.= "
								UPDATE projetos.reunioes_projetos_inicio
								   SET ds_resposta = '".$_POST['ds_resposta'][$ob_reg->cd_reunioes_projetos_roteiro]."', 
                                       cd_usuario  = ".$_SESSION['Z']."
								 WHERE cd_acomp                     = ".$_POST['cd_acomp']."
								   AND cd_reunioes_projetos_roteiro = ".$ob_reg->cd_reunioes_projetos_roteiro.";
				                ";				
			}
		}
	}
	else 
	{
		#### INSERT ####
		$cd_acomp_new = getNextval("projetos", "acompanhamento_projetos", "cd_acomp", $db); // PEGA NEXTVAL DA SEQUENCE DO CAMPO
		if ($cd_acomp_new > 0) // TESTA SE RETORNOU ALGUM VALOR
		{
			$sql = " INSERT INTO projetos.acompanhamento_projetos 
			                   ( 
			                     cd_acomp,
								 texto_acomp,
								 dt_acomp
							   )
						  VALUES
						       (
							     ".$cd_acomp_new.",
								 'novo',
								 current_date
							   ); 
				   ";
			
			$qr_roteiro = " 
					        SELECT rpr.cd_reunioes_projetos_roteiro
	                          FROM projetos.reunioes_projetos_roteiro rpr
	                         ORDER BY rpr.nr_ordem ";
			$ob_resul = pg_query($db, $qr_roteiro);
			$qr_respostas = "";
			while ($ob_reg = pg_fetch_object($ob_resul)) 
			{
				$qr_respostas.= "
								INSERT INTO projetos.reunioes_projetos_inicio
										  (
											cd_acomp,
											cd_reunioes_projetos_roteiro, 
											ds_resposta, 
											cd_usuario
										  )
									 VALUES 
										  (
											".$cd_acomp_new.",
											".$ob_reg->cd_reunioes_projetos_roteiro.",
											'".$_POST['ds_resposta'][$ob_reg->cd_reunioes_projetos_roteiro]."',
											".$_SESSION['Z']."
										  );
								";
			}
		}
		else
		{
			pg_close($db);
			echo "Erro a tentar incluir este acompanhamento (SEQ)";	
			exit;
		}
	}
	
	//echo "<PRE>DEBUG<br>".$sql.$qr_respostas; print_r($_POST);pg_query($db,"ROLLBACK TRANSACTION");exit;
	$ob_resul= @pg_query($db,$sql.$qr_respostas);
	if(!$ob_resul)
	{
		$ds_erro = "ERRO: ".str_replace("ERROR:","",pg_last_error($db));
		// ---> DESFAZ A TRANSACAO COM BD<--- //
		pg_query($db,"ROLLBACK TRANSACTION");
		echo $ds_erro;
		exit;
	}		
	else
	{
		// ---> COMITA DADOS NO BD <--- //
		pg_query($db,"COMMIT TRANSACTION"); 
		pg_close($db);
		if ((trim($cd_acomp_new) != "") and ($cd_acomp_new > 0))
		{ 
			$_POST['cd_acomp'] = $cd_acomp_new; // PASSA NOVO CODIGO PARA RETORNAR PARA TELA, SOMENTE NO INSERT DE ACOMPANHAMENTO
		}
		header('location: cad_acomp_projetos.php?c='.$_POST['cd_acomp']);
	}
?>