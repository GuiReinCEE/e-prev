<?
	header('Content-Type: text/html; charset=ISO-8859-1'); 
	include_once('inc/conexao.php');
	
	if($_POST)
	{
		if($_POST['ds_funcao'] == "gravar")
		{
			gravar($_POST['cd_chave'],$_POST['ds_nome']);
		}

		if($_POST['ds_funcao'] == "marcaRetorno")
		{
			marcaRetorno($_POST['cd_chave'],$_POST['ds_nome_retorno']);
		}
		
		if($_POST['ds_funcao'] == "buscaMovimento")
		{
			buscaMovimento();
		}		
		
		if($_POST['ds_funcao'] == "montaArrayChaves")
		{
			montaArrayChaves();
		}		
	}
	else
	{
		echo "ERRO: NENHUM DADO POSTADO";
	}
	
	#### GRAVA MOVIMENTO ####
	function gravar($cd_chave,$ds_nome)
	{
		global $db;
		// ---> ABRE TRANSACAO COM O BD <--- //
		pg_query($db,"BEGIN TRANSACTION");			
		$qr_update = "
						INSERT INTO projetos.chaves_movimento
						     (
							   cd_chave,
							   ds_nome
							 )
						VALUES
						     (
								".$cd_chave.",
								UPPER('".$ds_nome."')
							 )
				     ";
		$ob_resul = @pg_query($db,$qr_update);
		if(!$ob_resul)
		{
			$ds_erro = "ERRO: ".str_replace("ERROR:","",pg_last_error($db));
			// ---> DESFAZ A TRANSACAO COM BD<--- //
			pg_query($db,"ROLLBACK TRANSACTION");
			pg_close($db);
			echo $ds_erro;
		}
		else
		{
			// ---> COMITA DADOS NO BD <--- //
			pg_query($db,"COMMIT TRANSACTION"); 
		}	
	}
	
	
	#### MONTA ARRAY MOVIMENTO ####
	function montaArrayChaves()
	{
		global $db;
		$qr_select = "
					SELECT cm.cd_chave
					  FROM projetos.chaves c,
					       projetos.chaves_movimento cm
					 WHERE cm.cd_chave   = c.cd_chave
					   AND cm.dt_retorno IS NULL
                     ORDER BY cm.dt_saida,
					          c.cd_sala,
							  c.ds_chave
				     ";
		$ob_result = pg_query($db, $qr_select);	
		$lt_chave  = "";
		while ($ar_reg = pg_fetch_array($ob_result)) 
		{		
			if(trim($lt_chave) == "")
			{
				$lt_chave = $ar_reg['cd_chave'];
			}
			else
			{
				$lt_chave.= ",".$ar_reg['cd_chave'];
			}
			
		}
		echo " ar_chave = new Array(".$lt_chave."); ";
		//echo $lt_chave;
	}	
	
	function marcaRetorno($cd_chave,$ds_nome_retorno)
	{
		global $db;
		
		if(trim($ds_nome_retorno) == "")
		{
			$ds_nome_retorno = "ds_nome";
		}
		else
		{
			$ds_nome_retorno = "UPPER('".$ds_nome_retorno."')";		
		}
		
		// ---> ABRE TRANSACAO COM O BD <--- //
		pg_query($db,"BEGIN TRANSACTION");			
		$qr_update = "
						UPDATE projetos.chaves_movimento
						   SET dt_retorno      = CURRENT_TIMESTAMP,
						       ds_nome_retorno = ".$ds_nome_retorno."
						 WHERE cd_chave   = ".$cd_chave."
						   AND dt_retorno IS NULL
				     ";
		$ob_resul = @pg_query($db,$qr_update);
		if(!$ob_resul)
		{
			$ds_erro = "ERRO: ".str_replace("ERROR:","",pg_last_error($db));
			// ---> DESFAZ A TRANSACAO COM BD<--- //
			pg_query($db,"ROLLBACK TRANSACTION");
			pg_close($db);
			echo $ds_erro;
		}
		else
		{
			// ---> COMITA DADOS NO BD <--- //
			pg_query($db,"COMMIT TRANSACTION"); 
		}	
	}	
	
	
	#### LISTA MOVIMENTO ####
	function buscaMovimento()
	{
		global $db;
		echo '	<BR>
				<table class="tb_lista_resultado">
					<tr>
						<th>
							Chave
						</th>
						<th>
							Nome
						</th>						
						<th>
							Data
						</th>		
						<th>
							Editar
						</th>						
					</tr>
			';
		$qr_select = "
					SELECT cm.cd_chave_movimento,
					       cm.cd_chave,
					       cm.ds_nome,
						   TO_CHAR(cm.dt_saida,'DD/MM/YYYY HH24:MI:SS') AS dt_saida_formatada,
						   c.cd_sala,
						   c.ds_chave
					  FROM projetos.chaves c,
					       projetos.chaves_movimento cm
					 WHERE cm.cd_chave   = c.cd_chave
					   AND cm.dt_retorno IS NULL
                     ORDER BY cm.dt_saida,
					          c.cd_sala,
							  c.ds_chave
				     ";
		$ob_result = pg_query($db, $qr_select);	
		$nr_conta  = 0;
		$lt_chave  = "";
		while ($ar_reg = pg_fetch_array($ob_result)) 
		{		
			if(trim($lt_chave) == "")
			{
				$lt_chave = "'".$ar_reg['ds_chave']."'";
			}
			else
			{
				$lt_chave.= ",'".$ar_reg['ds_chave']."'";
			}
			
			if(($nr_conta % 2) != 0)
			{
				$bg_color = '#F4F4F4';
			}
			else
			{
				$bg_color = '#FFFFFF';		
			}			
			$js_saida = 'title="Clique para dar retorno" onclick="marcaRetorno(\''.$ar_reg['cd_chave'].'\');"';
			$js_editar = 'title="Clique para editar" onclick="editarRegistro(\''.$ar_reg['cd_chave_movimento'].'\');"';
			echo '			
					<tr bgcolor="'.$bg_color.'" onmouseover="this.className=\'tb_resultado_selecionado\';" onmouseout="this.className=\'\';">
						<td style="white-space:nowrap;" '.$js_saida.'>
							'.$ar_reg['cd_sala']." - ".$ar_reg['ds_chave'].'
						</td>						
						<td style="white-space:nowrap;" '.$js_saida.'>
							'.$ar_reg['ds_nome'].'
						</td>						
						<td style="white-space:nowrap;text-align:center;" '.$js_saida.'>
							'.$ar_reg['dt_saida_formatada'].'
						</td>								
						<td style="white-space:nowrap;text-align:center;" '.$js_editar.'>
							<img src="img/key_go.png" border="0" >
						</td>						
					</tr>	
				';
			$nr_conta++;				
		}
		echo '	</table>';
		echo "
				<script>
					ar_chave = new array(".$lt_chave.");
				</script>
		     ";
	}
	
?>