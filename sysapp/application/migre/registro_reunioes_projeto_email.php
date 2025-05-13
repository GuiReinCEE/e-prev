<?php
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	$cd_enter = chr(13);
	
	#### NOME DO PROJETO ####
	$sql = " 
			SELECT p.nome
			  FROM projetos.projetos p,
			       projetos.acompanhamento_projetos ap
			 WHERE ap.cd_acomp   = ".$_REQUEST['cd_acomp']."
			   AND ap.cd_projeto = p.codigo	
		   ";
	$rs  = pg_query($db, $sql);
	$ar_projeto = pg_fetch_array($rs);
	
	#### REUNIÃO REALIZADA ####	   
	$sql = "
			 SELECT rp.cd_reuniao, 
					rp.assunto, 
					rp.ds_arquivo_fisico,
					TO_CHAR(rp.dt_reuniao, 'DD/MM/YYYY') AS dt_reuniao_ed
			   FROM projetos.reunioes_projetos rp
			  WHERE rp.dt_exclusao IS NULL
				AND rp.cd_acomp    = ".$_REQUEST['cd_acomp']."
				AND rp.cd_reuniao  = ".$_REQUEST['cd_reuniao']."	
	       ";
	$rs         = pg_query($db, $sql);
	$ar_reuniao = pg_fetch_array($rs);
	
	#### REUNIÃO PRESENTES ####
	$sql = " 
			 SELECT uc.nome
			   FROM projetos.reunioes_projetos rp
			   LEFT JOIN projetos.reunioes_projetos_envolvidos rpe
				 ON rpe.cd_acomp   = rp.cd_acomp
				AND rpe.cd_reuniao = rp.cd_reuniao
			   LEFT JOIN projetos.usuarios_controledi uc
				 ON rpe.cd_usuario = uc.codigo
			  WHERE rp.dt_exclusao IS NULL
				AND rp.cd_acomp    = ".$_REQUEST['cd_acomp']."
				AND rp.cd_reuniao  = ".$_REQUEST['cd_reuniao']."
		   ";
	$rs = pg_query($db, $sql);
	while ($reg = pg_fetch_array($rs)) 
	{
		if(trim($reg['nome']) != "")
		{
			if(trim($lt_presentes) == "")
			{
				$lt_presentes = $reg['nome'];
			}
			else
			{
				$lt_presentes.= ", ".$reg['nome'];
			}
		}
	}
	
	#### REUNIÃO ENVOLVIDOS ####
	$sql = " 
			 SELECT uc.nome,
					uc.usuario
			   FROM projetos.reunioes_projetos rp
			   LEFT JOIN projetos.reunioes_projetos_envolvidos rpe
				 ON rpe.cd_acomp   = rp.cd_acomp
				AND rpe.cd_reuniao = rp.cd_reuniao
			   LEFT JOIN projetos.usuarios_controledi uc
				 ON rpe.cd_usuario = uc.codigo
			  WHERE rp.dt_exclusao IS NULL
				AND rp.cd_acomp    = ".$_REQUEST['cd_acomp']."
				AND rp.cd_reuniao  = ".$_REQUEST['cd_reuniao']."
		   ";
	$rs = pg_query($db, $sql);
	$qr_email = "";
	while ($reg = pg_fetch_array($rs)) 
	{
		if(trim($reg['usuario']) != "")
		{
			$ds_assunto = "Registro de Reunião - ".$ar_projeto['nome'];
			$ds_msg = $ds_assunto.$cd_enter;
			$ds_msg.= "-----------------------------------------------------------------------".$cd_enter;
			$ds_msg.= "DATA: ".$ar_reuniao['dt_reuniao_ed'].$cd_enter;
			$ds_msg.= "-----------------------------------------------------------------------".$cd_enter;
			$ds_msg.= "PRESENTES: ".$lt_presentes.".".$cd_enter;
			$ds_msg.= "-----------------------------------------------------------------------".$cd_enter;
			$ds_msg.= "ANEXO: ".$ar_reuniao['ds_arquivo_fisico'].$cd_enter;			
			$ds_msg.= "-----------------------------------------------------------------------".$cd_enter;
			$ds_msg.= "ASSUNTO TRATADOS: ".$cd_enter.$ar_reuniao['assunto'].$cd_enter;
			$ds_msg.= "-----------------------------------------------------------------------".$cd_enter;
			$ds_msg.= "Esta mensagem foi enviada pelo Sistema de controle.".$cd_enter;
			$qr_email.= " INSERT INTO projetos.envia_emails 
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
								 'Controle de Projetos',
								 '".$reg['usuario']."@eletroceee.com.br', 
								 '',
                                 '',
								 '".$ds_assunto."', 
								 '".str_replace("'", "`", $ds_msg)."',
								 29
							   );
							   
						  UPDATE projetos.reunioes_projetos
						     SET dt_email = CURRENT_TIMESTAMP
						   WHERE cd_acomp    = ".intval($_REQUEST['cd_acomp'])."
				             AND cd_reuniao  = ".intval($_REQUEST['cd_reuniao']).";
						";		
		}
	}	
	#### ABRE TRANSACAO COM O BD ####
	pg_query($db,"BEGIN TRANSACTION");	
	$ob_resul= @pg_query($db,$qr_email);
	if(!$ob_resul)
	{
		$ds_erro = "ERRO: ".str_replace("ERROR:","",pg_last_error($db));
		#### DESFAZ A TRANSACAO COM O BD ####
		pg_query($db,"ROLLBACK TRANSACTION");
		pg_close($db);
		echo $ds_erro;
		exit;
	}
	else
	{
		#### GRAVA DADOS NO BD ####
		pg_query($db,"COMMIT TRANSACTION");
		echo '<META HTTP-EQUIV="Refresh" CONTENT="0;URL='.site_url("atividade/acompanhamento/reuniao")."/".$_REQUEST['cd_acomp'].'">';
	}
?>