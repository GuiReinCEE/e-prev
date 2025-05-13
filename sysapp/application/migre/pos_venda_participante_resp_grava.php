<?php
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');

	//echo "<PRE>";
	//print_r($_REQUEST);
	
	$qr_sql = "
				DELETE FROM projetos.pos_venda_participante_resposta
				 WHERE cd_pos_venda_participante = ".$_POST['cd_pos_venda_participante'].";
			  ";
	foreach ($_REQUEST as $chave => $valor) 
	{
		if(substr( $chave, 0, 2) == "R_")
		{
			foreach ($valor as $cd_resposta) 
			{
				$qr_sql.= "
							INSERT INTO projetos.pos_venda_participante_resposta
								 (
									cd_pos_venda_participante, 
									cd_pos_venda_resposta, 
									cd_usuario_inclusao, 
									complemento
								 )
							VALUES 
								 (
									".$_POST['cd_pos_venda_participante'].",
									".$cd_resposta.",
									".$_SESSION['Z'].",
									".(trim($_REQUEST["C_".$cd_resposta]) == "" ? "NULL" : "'".$_REQUEST["C_".$cd_resposta]."'")."
								 );								
				          ";
			}
		}
	}
	
	if($_REQUEST['fl_encerra'] == "S")
	{
		$qr_sql.= "
					UPDATE projetos.pos_venda_participante
					   SET dt_final         = CURRENT_TIMESTAMP,
                           cd_usuario_final = ".$_SESSION['Z']."
					 WHERE cd_pos_venda_participante = ".$_POST['cd_pos_venda_participante'].";
		          ";
	}


	//echo $qr_sql; exit;	
			  
	if(trim($qr_sql) != "")
	{
		#### ---> ABRE TRANSACAO COM O BD <--- ####
		pg_query($db,"BEGIN TRANSACTION");	
		$ob_resul= @pg_query($db,$qr_sql);
		if(!$ob_resul)
		{
			$ds_erro = "ERRO: ".str_replace("ERROR:","",pg_last_error($db));
			#### ---> DESFAZ A TRANSACAO COM BD <--- ####
			pg_query($db,"ROLLBACK TRANSACTION");
		}
		else
		{
			#### ---> COMITA DADOS NO BD <--- ####
			pg_query($db,"COMMIT TRANSACTION"); 
			echo '<META HTTP-EQUIV="Refresh" CONTENT="0;URL=pos_venda_participante_resp.php?EMP_GA='.$_REQUEST['cd_empresa'].'&RE_GA='.$_REQUEST['cd_registro_empregado'].'&SEQ_GA='.$_REQUEST['seq_dependencia'].'">';
		}	
	}	
?>