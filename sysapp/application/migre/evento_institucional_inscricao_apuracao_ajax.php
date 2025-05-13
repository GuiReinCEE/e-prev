<?php
	include_once('inc/sessao.php');
	header('Content-Type: text/html; charset=ISO-8859-1'); 
	include_once('inc/conexao.php');

	if($_POST)
	{
		if($_POST['ds_funcao'] == "buscaVoto")
		{
			buscaVoto($_POST['cd_apuracao']);
		}
		
		if($_POST['ds_funcao'] == "gravaVoto")
		{
			gravaVoto($_POST['cd_apuracao'],$_POST['cd_inscricao']);
		}	

		if($_POST['ds_funcao'] == "delVoto")
		{
			delVoto($_POST['cd_apuracao'],$_POST['cd_inscricao']);
		}		
	}
	
	function gravaVoto($cd_apuracao, $cd_inscricao)
	{
		global $db;
		$qr_sql = "
					INSERT INTO projetos.eventos_institucionais_apuracao_voto
					     (
					       cd_eventos_institucionais_apuracao, 
					       cd_eventos_institucionais_inscricao
						 )
					VALUES 
					     (
						   ".$cd_apuracao.",
						   ".$cd_inscricao."
						 );
					
				  ";

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
				ECHO "OK";
			}	
		}
	}
	
	function delVoto($cd_apuracao, $cd_inscricao)
	{
		global $db;
		$qr_sql = "
					DELETE FROM projetos.eventos_institucionais_apuracao_voto
					 WHERE cd_eventos_institucionais_apuracao  = ".$cd_apuracao."
					   AND cd_eventos_institucionais_inscricao = ".$cd_inscricao."
				  ";

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
				ECHO "OK";
			}	
		}
	}	
	
	function buscaVoto($cd_apuracao)
	{
		global $db;
		$qr_sql = "
					SELECT cd_eventos_institucionais_inscricao AS cd_inscricao,
					       COUNT(*) AS qt_voto
					  FROM projetos.eventos_institucionais_apuracao_voto
					 WHERE cd_eventos_institucionais_apuracao = ".$cd_apuracao."
					 GROUP BY cd_inscricao
					 ORDER BY qt_voto DESC, cd_inscricao ASC
				  ";
		$ob_resul = pg_query($db,$qr_sql);
		if(pg_num_rows($ob_resul) > 0)
		{
			echo '
			<table class="sort-table" id="table-1" align="center" cellspacing="2" cellpadding="2">
				<thead>
				<tr>
					<td>
						Posição
					</td>				
					<td>
						Código
					</td>
					<td>
						Quantidade
					</td>
					<td>
						#
					</td>					
				</tr>
				</thead>
				<tbody>					
			     ';
			$total = 0;
			$nr_conta = 1;
			while($ar_reg = pg_fetch_array($ob_resul))
			{
				echo '
					<tr onmouseover="sortSetClassOver(this);" onmouseout="sortSetClassOut(this);">
						<td>
							'.$nr_conta.'
						</td>						
						<td>
							'.$ar_reg['cd_inscricao'].'
						</td>
						<td align="right">
							'.$ar_reg['qt_voto'].'
						</td>
						<td align="right">
							<input type="button" value="Excluir" onclick="delVoto(\''.$cd_apuracao.'\',\''.$ar_reg['cd_inscricao'].'\')" class="botao">
						</td>						
					</tr>
				     ';
				$total += $ar_reg['qt_voto'];
				$nr_conta++;
			}
			echo '</tbody>
			
				<tbody>
					<tr onmouseover="sortSetClassOver(this);" onmouseout="sortSetClassOut(this);">
						<td colspan="2"><b>Total</b></td>
						<td align="right"><b>'.$total.'</b></td>
					</tr>
				</tbody>			
			</table>';
		}
		else
		{
			echo "Não há registros.";
		}
	}
?>