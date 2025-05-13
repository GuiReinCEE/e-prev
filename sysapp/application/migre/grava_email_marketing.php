<?php
	exit;

    include_once('inc/sessao.php');
    include_once('inc/conexao.php');
    
	#echo "<PRE>".print_r($_POST,true); exit;    

    if ($_POST['insere'] == 'I') 
    {
        $qr_sql = " 
					INSERT INTO projetos.divulgacao 
						 ( 
							cd_divulgacao, 
							assunto, 
							conteudo, 
							cd_usuario, 
							divisao,
							tipo_divulgacao,
							email_avulsos,
							remetente,
							url_link
						 ) 
					VALUES 
						 ( 
							".intval($_POST['cd_divulgacao']).", 
							'".$_POST['assunto']."', 
							'".$_POST['conteudo']."', 
							".$_SESSION['Z'].", 
							'".$_SESSION['D']."',
							'E',
							TRIM('".$_POST['emails_outros']."'),
							TRIM('".$_POST['remetente']."'),
							TRIM('".$_POST['url_link']."')
						 ) 
			      ";
    }
    else 
    {
        $qr_sql = " 
					UPDATE projetos.divulgacao 
					   SET assunto       = '".$_POST['assunto']."', 
						   conteudo      = '".$_POST['conteudo']."', 
						   cd_usuario    = ".$_SESSION['Z'].", 
						   email_avulsos = TRIM('".$_POST['emails_outros']."'),
						   remetente     = TRIM('".$_POST['remetente']."'),                          
						   url_link      = TRIM('".$_POST['url_link']."')                            
					 WHERE cd_divulgacao = ".intval($_POST['cd_divulgacao'])."
                  ";
    }
	#echo "<PRE>$qr_sql</PRE>"; exit;
	
	#### ABRE TRANSACAO COM O BD #####
	pg_query($db,"BEGIN TRANSACTION");			
	$ob_resul = @pg_query($db,$qr_sql);
	if(!$ob_resul)
	{
		$ds_erro = str_replace("ERROR:","",pg_last_error($db));
		#### DESFAZ A TRANSACAO COM BD ####
		pg_query($db,"ROLLBACK TRANSACTION");
		pg_close($db);
		
		echo '<h1 style="color: red; font-weight:bold; font-family: calibri, arial; font-size: 16pt;">ERRO</h1>'; 
		echo '<h1 style="color: red; font-weight:bold; font-family: calibri, arial; font-size: 12pt;">'.$ds_erro.'</h1>'; 
		echo '<BR><BR>'; 
		echo '<PRE>'.$qr_sql.'</PRE>'; 
		exit; 
	}
	else
	{
		#### COMITA DADOS NO BD ####
		pg_query($db,"COMMIT TRANSACTION"); 
	}	
	
    if ($_POST['enviar'] == 'S') 
	{
        $qr_email = "";
		
		#### PUBLICACAO ####
		if(intval($_POST['chk_publicacao']) > 0)
		{
			$qr_email.= "
							DELETE FROM projetos.divulgacoes_publicacoes WHERE cd_divulgacao = ".intval($_POST['cd_divulgacao']).";
							INSERT INTO projetos.divulgacoes_publicacoes
								 (
									cd_divulgacao, 
									cd_publicacao
								 )
							VALUES 
								 (
									".intval($_POST['cd_divulgacao']).",
									".intval($_POST['chk_publicacao'])."
								 );
					    ";
		}    
		
		#### EMAILS AVULSO ####
		if(trim($_POST['emails_outros']) != "")
		{
			$qr_email.= emails_avulsos();
		}		
		
		#### EMAILS GRUPO ####
		if(count($_POST['ar_grupo']) > 0)
		{
			$qr_email.= "
							UPDATE projetos.divulgacao_grupo_selecionado
							   SET dt_exclusao         = CURRENT_TIMESTAMP, 
								   cd_usuario_exclusao = ".$_SESSION['Z']."
							 WHERE cd_divulgacao = ".intval($_POST['cd_divulgacao']).";			
			            ";
		
			foreach($_POST['ar_grupo'] as $cd_grupo)
			{
				$qr_email.= emails_grupo(intval($cd_grupo));
			}
		}

		#echo "<PRE>$qr_email</PRE>"; exit;

		if(trim($qr_email) != "")
		{
			set_time_limit(0);
			#### ABRE TRANSACAO COM O BD #####
			pg_query($db,"BEGIN TRANSACTION");			
			$ob_resul = @pg_query($db,$qr_email);
			if(!$ob_resul)
			{
				$ds_erro = str_replace("ERROR:","",pg_last_error($db));
				#### DESFAZ A TRANSACAO COM BD ####
				pg_query($db,"ROLLBACK TRANSACTION");
				pg_close($db);
				
				echo '<h1 style="color: red; font-weight:bold; font-family: calibri, arial; font-size: 16pt;">ERRO</h1>'; 
				echo '<h1 style="color: red; font-weight:bold; font-family: calibri, arial; font-size: 12pt;">'.$ds_erro.'</h1>'; 
				echo '<BR><BR>'; 
				echo '<PRE>'.$qr_email.'</PRE>'; 
				exit; 
			}
			else
			{
				#### COMITA DADOS NO BD ####
				pg_query($db,"COMMIT TRANSACTION"); 
			}
		}
		
		header("location: lst_email_marketing.php");
    }
	else
	{
		header("location: cad_email_marketing.php?op=A&c=".intval($_POST['cd_divulgacao']));
	}
	
    
//-----------------------------------------------------------------------------------------------

#### ENVIA EMAILS ####
function emails_grupo($cd_grupo)
{
    global $db;
    
	$qr_email = "";
	
    if(intval($_POST['cd_divulgacao']) > 0) 
	{
		$qr_sql = "
					SELECT cd_divulgacao_grupo,
						   qr_sql, 
						   cd_lista
					  FROM projetos.divulgacao_grupo
					 WHERE dt_exclusao IS NULL
                       AND cd_divulgacao_grupo = ".intval($cd_grupo)."
		          ";
		$ob_resul = pg_query($db, $qr_sql);
		$ar_pub = pg_fetch_array($ob_resul);
		$qr_sql_grupo = $ar_pub['qr_sql'];
		$cd_lista     = $ar_pub['cd_lista'];

		#echo "<PRE>$qr_sql_grupo</PRE>"; exit;

        if(trim($qr_sql_grupo) != "")
        {
			#### MARCA GRUPOS EMAILS #####
			$qr_email.= "
							INSERT INTO projetos.divulgacao_grupo_selecionado
								 (
									cd_divulgacao, 
									cd_divulgacao_grupo,
									cd_usuario_inclusao
								 )
							VALUES 
								 (
									".intval($_POST['cd_divulgacao']).",
									".intval($cd_grupo).",
									".$_SESSION['Z']."
								 );
					    ";			
			
			#### BUSCA REGISTRO DO GRUPO ####
			$ob_resul = pg_query($db, $qr_sql_grupo);
			
			while ($reg = pg_fetch_array($ob_resul)) 
			{
				$v_texto = $_POST['conteudo'];
				$v_nome = str_replace(' Das ', ' das ',(str_replace(' Da ', ' da ',(str_replace(' Dos ', ' dos ', str_replace(' De ', ' de ',(($reg['nome']))))))));    
				$v_texto = str_replace("{nome}", (($v_nome)), $v_texto);
				if ($cd_lista == "CS1W") { $v_texto = str_replace("{cd_inscricao}", $reg['codigo'], $v_texto); }
				if ($cd_lista == "CS2W") { $v_texto = str_replace("{cd_inscricao}", $reg['codigo'], $v_texto); }
				
				$v_texto = str_replace("'", "´", $v_texto);
				$v_texto = str_replace("{link_arquivo}", (trim($_POST['arquivo']) != "" ? "<a href='http://www.e-prev.com.br/controle_projetos/upload/".$_POST['arquivo']."'>".$_POST['arquivo']."</a>" : ""), $v_texto);
				$v_texto = str_replace("[EMP]",  $reg['cd_empresa'], $v_texto);
				$v_texto = str_replace("[RE]",   $reg['cd_registro_empregado'], $v_texto);
				$v_texto = str_replace("[SEQ]",  $reg['seq_dependencia'], $v_texto);
				$v_texto = str_replace("[NOME]", $reg['nome'], $v_texto);
				$v_texto = str_replace("[RE_CRIPTO]", $reg['re_cripto'], $v_texto);
				$v_texto = str_replace("'","", $v_texto);
				
				#### RE ####
				if(intval($reg['cd_registro_empregado']) == 0)
				{
					$emp = "DEFAULT";
					$re  = "DEFAULT";
					$seq = "DEFAULT";
				}
				else
				{
					$emp = (trim($reg['cd_empresa']) == ""            ? "DEFAULT" : intval($reg['cd_empresa']));
					$re  = (trim($reg['cd_registro_empregado']) == "" ? "DEFAULT" : intval($reg['cd_registro_empregado']));
					$seq = (trim($reg['seq_dependencia']) == ""       ? "DEFAULT" : intval($reg['seq_dependencia']));
				}
				
				#### LINK ####
				$link_email = "";
				if(trim($_POST['url_link']) != "")
				{
					$link = str_replace("[RE_CRIPTO]", $reg['re_cripto'], trim($_POST['url_link']));
					$link_emp = (trim($emp) == "DEFAULT" ? "NULL" : $emp);
					$link_re  = (trim($re)  == "DEFAULT" ? "NULL" : $re);
					$link_seq = (trim($seq) == "DEFAULT" ? "NULL" : $seq);
					
					$link_email = "' || (funcoes.gera_link('".$link."',".$link_emp.",".$link_re.",".$link_seq.")) || '";
				}
				$v_texto = str_replace("[LINK_1]", $link_email, $v_texto);

				#### AJUSTE NO EMAIL ####
				$reg['email'] = trim(strtolower(str_replace("'","",$reg['email'])));
				$reg['email_profissional'] = trim(strtolower(str_replace("'","",$reg['email_profissional'])));
				
				if(!ereg(".*@.*", $reg['email'])) 
				{
					$reg['email'] = $reg['email_profissional'];
					$reg['email_profissional'] = "";
				}
				$email = $reg['email'];
				
				$v_cc = "";
				if(ereg(".*@.*", $reg['email_profissional'])) 
				{
					if(trim($reg['email']) != trim($reg['email_profissional']))
					{
						$v_cc = $reg['email_profissional'];
					}
				}           

				#### REMETENTE (DE) ####
				if(trim($_POST['remetente']) != "")
				{
					$_POST['remetente'] = trim($_POST['remetente']);
				}
				elseif($emp == 7)
				{
					$_POST['remetente'] = "Senge Previdência";
				}               
				elseif($emp == 8)
				{
					$_POST['remetente'] = "SINPRORS Previdência";
				}
				elseif($emp == 10)
				{
					$_POST['remetente'] = "SINPRORS Previdência";
				}
				elseif($emp == 19)
				{
					$_POST['remetente'] = "Família Previdência";
				}
				
				$qr_email.= "
								INSERT INTO projetos.envia_emails 
										  ( 
											dt_schedule_email, 
											de, 
											para,
											cc, 
											cco,
											assunto,
											texto,
											arquivo_anexo,
											cd_divulgacao,
											cd_empresa,
											cd_registro_empregado,
											seq_dependencia,
											div_solicitante,
											cd_usuario             
										  ) 	
									 VALUES
										  (
											".(trim($_POST['dt_envio']) == "" ? "DEFAULT" : " TO_TIMESTAMP('".$_POST['dt_envio']."','DD/MM/YYYY') ").", --AGENDAMENTO DO ENVIO
											".(trim($_POST['remetente']) == "" ? "DEFAULT" : "'".trim($_POST['remetente'])."'" ).",
											'".trim($email)."',
											'".trim($v_cc)."',
											'',
											".(trim($_POST['assunto']) == "" ? "''" : "'".trim($_POST['assunto'])."'" ).",
											'".$v_texto."',
											'".trim($arquivo)."',
											".intval($_POST['cd_divulgacao']).",
											".$emp.",
											".$re.", 
											".$seq.",
											'".$_SESSION['D']."',
											".intval($_SESSION['Z'])."
										  );
				            ";
							
				#echo "<PRE>$qr_email</PRE>"; exit;
			}
		}
    }

	return $qr_email;
}

#### ENVIA EMAIL AVULSO ####
function emails_avulsos() 
{
    global $db;
	
	$ar_email = explode(";",$_POST['emails_outros']);
	$qr_sql = "";
	foreach($ar_email as $email)
	{
        $v_texto = str_replace("{link_arquivo}", (trim($_POST['arquivo']) != "" ? "<a href='http://www.e-prev.com.br/controle_projetos/upload/".$_POST['arquivo']."'>".$_POST['arquivo']."</a>" : ""), $_POST['conteudo']);		
		
		$qr_sql.= "
					INSERT INTO projetos.envia_emails
					     (
							de,
							para,
							assunto,
							texto,
							arquivo_anexo,
							div_solicitante,
							cd_divulgacao,
							cd_usuario 						 
						 )
					VALUES
					     (
							".(trim($_POST['remetente']) == "" ? "DEFAULT" : "'".trim($_POST['remetente'])."'" ).",
							'".trim($email)."',
							'".$_POST['assunto']."',
							'".$v_texto."',
							'".$arquivo."',
							'".$_SESSION['D']."',
							".intval(intval($_POST['cd_divulgacao'])).",
							".intval($_SESSION['Z'])."
						 );
		          ";
	}
	
	#echo "<PRE>$qr_sql</PRE>"; exit;
	
	return $qr_sql;
}

?>