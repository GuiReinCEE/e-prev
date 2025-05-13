<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/class.Email.inc.php');
	
	$fl_email_just_impl = false;
	$txt_dt_apres 		 = ($dt_apresentacao == '' ? "TO_DATE('".date('d/m/Y')."','DD/MM/YYYY')" : "TO_DATE('".$dt_apresentacao."','DD/MM/YYYY')");
	$txt_dt_limite_apres = ($dt_limite       == '' ? 'NULL' : "TO_DATE('".$dt_limite."','DD/MM/YYYY')");
	$txt_dt_prop_imp	 = ($dt_prop_imp     == '' ? 'NULL' : "TO_DATE('".$dt_prop_imp."','DD/MM/YYYY')");
	$txt_dt_efe_imp		 = ($dt_efe_imp      == '' ? 'NULL' : "TO_DATE('".$dt_efe_imp."','DD/MM/YYYY')");
	$txt_dt_prop_verif	 = ($dt_prop_verif   == '' ? 'NULL' : "TO_DATE('".$dt_prop_verif."','DD/MM/YYYY')");
	$txt_dt_efe_verif	 = ($dt_efe_verif    == '' ? 'NULL' : "TO_DATE('".$dt_efe_verif."','DD/MM/YYYY')");   
	$txt_dt_prorrogada	 = ($dt_prorrogada   == '' ? 'NULL' : "TO_DATE('".$dt_prorrogada."','DD/MM/YYYY')");   
 
	if ($insere=='I') 
	{
		
		if($raz_nao_imp!="")
		{
			$dt_raz_nao_imp = "CURRENT_TIMESTAMP";
		}
		else
		{
			$dt_raz_nao_imp = "NULL";
		}
		
		$prorrogacao1 = "";
		$prorrogacao2 = "";
		if( $txt_dt_prorrogada!='NULL' )
		{
			$prorrogacao1 = ", dt_prorrogada_em, cd_usuario_prorrogacao ";
			$prorrogacao2 = ", CURRENT_TIMESTAMP, " . $_SESSION['Z'] . " ";
		}
		$qr_sql = "
				INSERT INTO projetos.acao_corretiva 
				          ( 
							cd_processo,
					    	cd_nao_conformidade,
					    	cd_acao,
							tipo_acao,
							dt_limite_apres,
							dt_apres,
							dt_prop_imp,
							dt_efe_imp,
							dt_prop_verif,
							dt_efe_verif,
							dt_prorrogada,
							ac_proposta,
							raz_nao_apres,
							raz_nao_imp,
							dt_raz_nao_imp
							".$prorrogacao1."
					      ) 
				     VALUES
				 	      ( 
					    	".$cod_processo.",
				    		".$cod_nao_conf.",
				    		".$cod_acao.",
					    	'C',
					        ".$txt_dt_limite_apres.",
					        ".$txt_dt_apres.",
					        ".$txt_dt_prop_imp.",
					        ".$txt_dt_efe_imp.",
					        ".$txt_dt_prop_verif.",
					        ".$txt_dt_efe_verif.",
						    ".$txt_dt_prorrogada.",
					        '".$descricao."',
					        '".$raz_nao_apres."',
					        '".$raz_nao_imp."',
					        ".$dt_raz_nao_imp."
					        ".$prorrogacao2."
				    	  )";
		if ($raz_nao_imp != '') 
		{
			$fl_email_just_impl = true; 
		}
	}
	else 
	{
		if ($raz_nao_imp != '')
		{
			$qr_sql = "
						SELECT raz_nao_imp 
			              FROM projetos.acao_corretiva 
					      WHERE cd_acao = ".$cod_acao;

			$ob_resul = pg_query($db, $qr_sql);
	        $ar_reg   = pg_fetch_array($ob_resul);

			if( $raz_nao_imp != addslashes($ar_reg['raz_nao_imp']) ) 
			{
				$fl_email_just_impl = true; 
			}
		}

		if($raz_nao_imp!="")
		{
			$dt_raz_nao_imp = "CURRENT_TIMESTAMP";
		}
		else
		{
			$dt_raz_nao_imp = "NULL";
		}
		
		$prorrogacao = "";
		if( $dt_prorrogada_old_value=='' AND $dt_prorrogada!="" )
		{
			$prorrogacao = "
				, dt_prorrogada_em = CURRENT_TIMESTAMP,
				  cd_usuario_prorrogacao = " . $_SESSION['Z'] . "
				";
		}

		$qr_sql = " 
	          UPDATE projetos.acao_corretiva 
	             SET  tipo_acao       = 'C', 
					  dt_efe_imp      = ".$txt_dt_efe_imp.",
					  dt_prop_verif   = ".$txt_dt_prop_verif.",
					  dt_efe_verif    = ".$txt_dt_efe_verif.",
					  dt_prorrogada   = ".$txt_dt_prorrogada.",
					  ac_proposta     = '".$descricao."',
					  raz_nao_apres   = '".$raz_nao_apres."',
					  raz_nao_imp     = '".$raz_nao_imp."',
					  dt_raz_nao_imp  = COALESCE( dt_raz_nao_imp, ".$dt_raz_nao_imp.")
					  
			      " . $prorrogacao . "
					   
			    WHERE cd_acao = " . $cod_acao . ";";
						
		if ($txt_dt_efe_verif != 'NULL') 
		{
			$qr_sql.= " 
						UPDATE projetos.nao_conformidade 
			               SET data_fechamento     = ".$txt_dt_efe_verif."
					     WHERE cd_nao_conformidade = ".$cod_acao.";";
		}
	}
	
	#### ---> ABRE TRANSACAO COM O BD <--- ####
	pg_query($db, "BEGIN TRANSACTION");			
	$ob_resul = @pg_query($db, $qr_sql);
	if(!$ob_resul)
	{
		$ds_erro = "ERRO: ".str_replace("ERROR:","",pg_last_error($db));
		#### ---> DESFAZ A TRANSACAO COM BD <--- ####
		pg_query($db,"ROLLBACK TRANSACTION");
		if (ereg('10.63.255.',$_SERVER['REMOTE_ADDR']))
		{
			echo "<PRE style='color:red;'>ERRO AO GRAVAR:</PRE>";
			echo "<PRE style='color:red;'>".trim($ds_erro)."</PRE>";
			//echo "<PRE style='color:red;'>QUERY:";echo $qr_sql."</PRE>";
			echo "<PRE style='color:red;'>ARQUIVO: ".$_SERVER["REQUEST_URI"]."</PRE>";
		}		
		pg_close($db);
		exit;
	}
	else
	{
		#### ---> COMITA DADOS NO BD <--- ####
		pg_query($db,"COMMIT TRANSACTION"); 
		if($fl_email_just_impl)
		{
			envia_email_just_impl($cod_acao, $db);
		}
		header('location: cad_acao_corretiva.php?ac='.$cod_acao.'&pro='.$cod_processo);
	}	
	
	#################################################### ENVIA EMAIL #####################################################
	function envia_email_just_impl($cod_acao, $db) 
	{	
		$cd_enter = chr(13);
		
		#### BUSCA CONTEUDO DO EMAIL ####
		$qr_sql = "
					SELECT cd_evento, 
					       nome, 
						   email 
		              FROM projetos.eventos
					 WHERE cd_evento = 8 
			   ";
		$ob_resul       = pg_query($db, $qr_sql);
		$ar_reg         = pg_fetch_array($ob_resul);
		$ds_assunto     = $ar_reg['nome'];
		$ds_texto_email = $ar_reg['email'];
		$cd_evento      = $ar_reg['cd_evento'];

		//--------- Responsável (se houver)
		$qr_sql = "
					SELECT uc.nome,
					       uc.usuario
					  FROM projetos.usuarios_controledi uc,
					       projetos.nao_conformidade nc
					 WHERE nc.cd_responsavel      = uc.codigo
					   AND nc.cd_nao_conformidade = ".$cod_acao;
		$ob_resul = pg_exec($qr_sql);
		$ar_reg = pg_fetch_array($ob_resul);
		$ds_responsavel = $ar_reg['nome'];
		
		$ds_msg = $ds_assunto.$cd_enter.$cd_enter;
		$ds_msg.= $ds_texto_email.$cd_enter.$cd_enter;
		$ds_msg.= "-----------------------------------------------------------------------".$cd_enter;
		$ds_msg.= "AÇÃO CORRETIVA: ".conv_num_nc($cod_acao).$cd_enter;			
		$ds_msg.= "RESPONSÁVEL: ".$ds_responsavel.$cd_enter;
		$ds_msg.= "-----------------------------------------------------------------------".$cd_enter.$cd_enter;		
		$ds_msg.= "Esta mensagem foi enviada pelo Sistema de Controle de Não Conformidades.".$cd_enter;
		$qr_sql= " 
				  INSERT INTO projetos.envia_emails 
					   ( 
						 dt_envio, 
						 de,
						 para, 
						 cc,
						 cco,
						 assunto,
						 texto,
						 cd_evento
					   ) 
				  VALUES
					   ( 
						 CURRENT_TIMESTAMP, 
						 'Sistema de Controle de Não Conformidades',
						 'comite@eletroceee.com.br', 
						 '',
						 '',
						 '".$ds_assunto."', 
						 '".str_replace("'", "`", $ds_msg)."',
						 8
					   );
				 ";		
		#### ---> ABRE TRANSACAO COM O BD <--- ####
		pg_query($db,"BEGIN TRANSACTION");			
		$ob_resul = @pg_query($db,$qr_sql);
		if(!$ob_resul)
		{
			$ds_erro = "ERRO: ".str_replace("ERROR:","",pg_last_error($db));
			#### ---> DESFAZ A TRANSACAO COM BD <--- ####
			pg_query($db,"ROLLBACK TRANSACTION");
			if (ereg('10.63.255.',$_SERVER['REMOTE_ADDR']))
			{
				echo "<PRE style='color:red;'>ERRO AO GRAVAR EMAIL:</PRE>";
				echo "<PRE style='color:red;'>".trim($ds_erro)."</PRE>";
				//echo "<PRE style='color:red;'>QUERY:";echo $qr_sql."</PRE>";
				echo "<PRE style='color:red;'>ARQUIVO: ".$_SERVER["REQUEST_URI"]."</PRE>";
			}		
			pg_close($db);
			exit;
		}
		else
		{
			#### ---> COMITA DADOS NO BD <--- ####
			pg_query($db,"COMMIT TRANSACTION"); 
		}				 
	}
	
	function conv_num_nc($n) 
	{
		// Pressupõe que o num esteja no formato AAAANNN
		$aaaa = substr($n, 0, 4);
		$nc = substr($n, 4, 3);
		return $nc.'/'.$aaaa;
	}

?>