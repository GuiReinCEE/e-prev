<?php
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');

	
	#echo "<PRE>".print_r($_REQUEST,true)."</PRE>";exit;
	#echo "<PRE>".print_r($_REQUEST,true)."</PRE>";exit;
	

	$qr_sql = "
				UPDATE projetos.avaliacao_capa
				   SET fl_acordo         = '".$_REQUEST['fl_acordo']."',
				       dt_acordo         = CURRENT_TIMESTAMP,
					   cd_usuario_acordo = ".intval($_SESSION["Z"])."
				 WHERE cd_avaliacao_capa = '".intval($_REQUEST['cd_avaliacao_capa'])."'
				   AND fl_acordo IS NULL
	          ";

	#### ABRE TRANSACAO COM O BD #####
	pg_query($db,"BEGIN TRANSACTION");			
	$ob_resul= @pg_query($db,$qr_sql);
	if(!$ob_resul)
	{
		$ds_erro = "ERRO: ".str_replace("ERROR:","",pg_last_error($db));
		#### DESFAZ A TRANSACAO COM BD ####
		pg_query($db,"ROLLBACK TRANSACTION");
		pg_close($db);
		echo $ds_erro; 
		echo "<BR><BR>"; 
		echo "<PRE>$qr_sql</PRE>"; exit; #### DEBUG
		exit;
	}
	else
	{
		#### COMITA DADOS NO BD ####
		pg_query($db,"COMMIT TRANSACTION"); 
		@pg_close($db);
		echo '<META HTTP-EQUIV="Refresh" CONTENT="0;URL=avaliacao.php?tipo=F&cd_capa='.$_REQUEST['cd_avaliacao_capa'].'">';
		exit;
	}
?>