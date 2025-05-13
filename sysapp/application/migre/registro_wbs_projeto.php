<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');

	if($_POST['cd_acomp'] != "")
	{
		if(count($_FILES) > 0)
		{
			// ---> ABRE TRANSACAO COM O BD <--- //
			pg_query($db,"BEGIN TRANSACTION");		
			#### INSERT ####
			$qr_sql = "
					INSERT INTO projetos.acompanhamento_wbs
							  (
								cd_acomp,
								cd_usuario,
								ds_arquivo,
								ds_arquivo_fisico
							  ) 
						 VALUES
							  (
								".$_POST['cd_acomp'].", 
								".$_SESSION['Z'].",
								'".$_FILES['ds_arquivo']['name']."', 
								'".$_POST['ds_arquivo_local']."'
							  )";
			
			$ob_resul= @pg_query($db,$qr_sql);
			if(!$ob_resul)
			{
				$ds_erro = "ERRO: ".str_replace("ERROR:","",pg_last_error($db));
				// ---> DESFAZ A TRANSACAO COM BD<--- //
				pg_query($db,"ROLLBACK TRANSACTION");
				pg_close($db);
				echo $ds_erro;
				exit;
			}
			else
			{
				// ---> COMITA DADOS NO BD <--- //
				pg_query($db,"COMMIT TRANSACTION"); 	
				echo "	<script>			
							opener.location.reload(true);
							window.close();
						</script>";				
			}
        }
	}	     
?>
<html>
<head>
	<title>...:: Registro de WBS ::...</title>
	<style>
		*{
			background: #D4D0C8;
			font-size: 10pt;
			font-weight: normal;
			font-family: Verdana, Arial, 'MS Sans Serif';			
		}
		fieldset {
			padding-left: 10px;
			padding-right: 10px;
		}
		
		legend{
			font-size: 14pt;
			font-weight: normal;
		}

		label{
			font-weight: bold;
		}
		
		textarea{
			background: #FFFFFF;
			height:100px; 
			width:100%;
		}

		input{
			background: #FFFFFF;
		}
		
		span{
			font-size: 8pt;
			color:#666666;
		}
		
		.css_botao{
			text-align:right;
			width: 100%;
		}
		
		.complemento {
			font-size: 8pt;
			color:#666666;
		}
		
		textarea.complemento {
			background:#F4F4F4;
		}		
	</style>

</head>
<body>
	<form name="ob_form" id="ob_form" action="" method="post" enctype="multipart/form-data">
		<?
			echo "<input type='hidden' name='cd_acomp'  value='".$_REQUEST['cd_acomp']."'>";
		?>
		<fieldset>
			<legend>Registro de WBS</legend>
			<div class="css_botao">
				<input name="image" type="image" src="img/btn_salvar.jpg" border="0" title="Salvar escopo">
				<img src="img/btn_retorna.jpg"  onClick="window.close();" style="cursor:pointer;" title="Fechar janela">				
			</div>
			<br>
			<label for="ds_arquivo">Arquivo WBS: </label>
			<br>
			<input type="hidden" name="ds_arquivo_local" id="ds_arquivo_local" value="">
			<input type="file"   name="ds_arquivo" id="ds_arquivo" onchange="document.getElementById('ds_arquivo_local').value = this.value;">								
			<br><br>
		</fieldset>
	</form>
</body>
</html>