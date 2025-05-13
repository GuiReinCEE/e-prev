<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/nextval_sequence.php');

	$_POST['cbo_agrupamento'] = intval($_POST['cbo_agrupamento']);
	if (intval($_POST['codigo']) > 0)
	{
		$li_resposta = "";
		for ($i = 1; $i <= 12; $i++)
		{
			$li_resposta.= " 
			               r".$i." = '".$_POST['ac_q'.$i]."', 
			               rotulo".$i." = '".$_POST['rotulo'.$i]."', 
			               legenda".$i." = '".$_POST['legenda'.$i]."',
			               r".$i."_complemento = '".$_POST['r'.$i.'_complemento']."', 
			               r".$i."_complemento_rotulo = '".$_POST['r'.$i.'_complemento_rotulo']."', 
						   ";
		}	

		$qr_sql = " 
					UPDATE projetos.enquete_perguntas 
		               SET ".$li_resposta."
		                   texto                = '".$_POST['questao']."',  
		                   cd_agrupamento       = ".$_POST['cbo_agrupamento'].",
		                   r_diss               = '".$_POST['ac_q_diss']."', 
		                   r_justificativa      = '".$_POST['ac_q_justificativa']."', 
		                   rotulo_dissertativa  = '".$_POST['rotulo_dissertativa']."', 
		                   rotulo_justificativa = '".$_POST['rotulo_justificativa']."' 
		             WHERE cd_enquete  = ".$_POST['eq']." 
		               AND cd_pergunta = ".$_POST['codigo']." 
			   ";
	}
	else 
	{
		$li_resposta_campo = "";
		$li_resposta_valor = "";
		for ($i = 1; $i <= 12; $i++)
		{
			$li_resposta_campo.= " 
					               r".$i.", 
					               rotulo".$i.", 
					               legenda".$i.",
					               r".$i."_complemento, 
					               r".$i."_complemento_rotulo, 
								 ";
								 
			$li_resposta_valor.= " 
					               '".$_POST['ac_q'.$i]."', 
					               '".$_POST['rotulo'.$i]."', 
					               '".$_POST['legenda'.$i]."',
					               '".$_POST['r'.$i.'_complemento']."', 
					               '".$_POST['r'.$i.'_complemento_rotulo']."', 
								 ";								 
		}	
		
		#### PEGA NEXTVAL DA SEQUENCE DO CAMPO ####
		$cd_pergunta = getNextval("projetos", "enquete_perguntas", "cd_pergunta", $db);
		$qr_sql = " 
				INSERT INTO projetos.enquete_perguntas 
				     ( 
				       cd_pergunta,
					   cd_enquete, 
		               texto, 
		               cd_agrupamento, 
		               ".$li_resposta_campo."
					   r_diss, 
					   r_justificativa,
					   rotulo_dissertativa, 
					   rotulo_justificativa 
					 )
		        VALUES 
				     ( 
		               ".$cd_pergunta.",
					   ".$eq.", 
		               '".$questao."', 
		               ".$cbo_agrupamento.", 
                       ".$li_resposta_valor."
		               '".$ac_q_diss."', 
		               '".$ac_q_justificativa."', 
			           '".$rotulo_dissertativa."', 
		               '".$rotulo_justificativa."'
			         ) 
			   ";
		$_POST['codigo'] = $cd_pergunta;
	}

	
	#### ABRE TRANSACAO COM O BD ####
	pg_query($db,"BEGIN TRANSACTION");	
	$ob_resul= @pg_query($db,$qr_sql);
	if(!$ob_resul)
	{
		$ds_erro = "ERRO:<BR>".str_replace("ERROR:","",pg_last_error($db));
		#### DESFAZ A TRANSACAO COM O BD ####
		pg_query($db,"ROLLBACK TRANSACTION");
		pg_close($db);
		echo "<BR><BR><BR><center><h1 style='font-family: calibri, arial; font-size: 20pt; color:red;'>".$ds_erro."</h1></center>";
		exit;		
	}
	else
	{
		#### GRAVA DADOS NO BD ####
		pg_query($db,"COMMIT TRANSACTION");
		header('location: cad_enquetes_perguntas.php?c='.$_POST['codigo'].'&eq='.$_POST['eq']);
	}	
?>