<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/nextval_sequence.php');

	if(count($_POST) > 0) 
	{
		#echo "<PRE>"; print_r($_REQUEST);exit;
		
		#echo "<PRE>"; print_r($_FILES) ;exit;
		
		$fl_arquivo = false;
		if(count($_FILES) > 0)
		{
			$fl_arquivo      = true;
		}
		
		// ---> ABRE TRANSACAO COM O BD <--- //
		pg_query($db,"BEGIN TRANSACTION");	
		
		if ($_POST['cd_reuniao'] != '') 
		{
 			#### UPDATE ####
			$sql = "UPDATE projetos.reunioes_projetos 
					   SET dt_reuniao   = TO_DATE('".$_POST['dt_reuniao']."','DD/MM/YYYY'), 
							descricao   = '".$_POST['descricao']."', 
							assunto     = '".$_POST['assunto']."', 
							motivo      = '".$_POST['motivo']."'"; 
			if($fl_arquivo)
			{
				$sql.=" ,   ds_arquivo        = '".$_FILES['ds_arquivo']['name']."'";
				$sql.=" ,   ds_arquivo_fisico = E'".str_replace("\\","\\\\",$_POST['ds_arquivo_local'])."'";
			}
			elseif(trim($_POST['ds_arquivo_fisico']) != '')
			{
				$sql.=" ,   ds_arquivo        = NULL";
				$sql.=" ,   ds_arquivo_fisico = NULL";
			}
			
			$sql.="   WHERE cd_acomp   = ".$_POST['cd_acomp']." 
						AND cd_reuniao = ".$_POST['cd_reuniao'].";

					 DELETE FROM projetos.reunioes_projetos_envolvidos
					  WHERE cd_reuniao = ".$_POST['cd_reuniao']."
					    AND cd_acomp   = ".$_POST['cd_acomp']."; 
				   ";						
			$nr_fim = count($_POST['ar_envolvidos']);
			$nr_conta = 0;
			while($nr_conta < $nr_fim)
			{
				$sql.= "
						INSERT INTO projetos.reunioes_projetos_envolvidos
							 (
							   cd_acomp,
							   cd_reuniao,
							   cd_usuario
							 )
						VALUES
							 (
							   ".$_POST['cd_acomp'].",
							   ".$_POST['cd_reuniao'].",
							   ".$_POST['ar_envolvidos'][$nr_conta]."
							 );
					   ";
				$nr_conta++;
			}						
		}
		else 
		{
			#### INSERT ####
			$cd_reuniao_novo = getNextval("projetos", "reunioes_projetos", "cd_reuniao", $db); // PEGA NEXTVAL DA SEQUENCE DO CAMPO			
			if ($cd_reuniao_novo > 0) // TESTA SE RETORNOU ALGUM VALOR
			{			
				$sql = "INSERT INTO projetos.reunioes_projetos
				                  (
								    cd_reuniao,
									cd_acomp, 
									dt_reuniao, 
									descricao, 
									assunto,
									motivo";
				if($fl_arquivo)
				{
					$sql.="       , ds_arquivo
	                              , ds_arquivo_fisico";
				}								
				$sql.= "
								  ) 
						     VALUES
							      (
								    ".$cd_reuniao_novo.",
									'".$_POST['cd_acomp']."', 
									TO_DATE('".$_POST['dt_reuniao']."','DD/MM/YYYY'), 
									'".$_POST['descricao']."', 
									'".$_POST['assunto']."', 
									'".$_POST['motivo']."'";
				if($fl_arquivo)
				{
					$sql.="       , '".$_FILES['ds_arquivo']['name']."'
					              , '".$_POST['ds_arquivo_local']."'";
				}								
				$sql.= "		  );";
				
				$nr_fim = count($_POST['ar_envolvidos']);
				$nr_conta = 0;
				while($nr_conta < $nr_fim)
				{
					$sql.= "
							INSERT INTO projetos.reunioes_projetos_envolvidos
							     (
								   cd_acomp,
								   cd_reuniao,
								   cd_usuario
								 )
						    VALUES
								 (
								   ".$_POST['cd_acomp'].",
								   ".$cd_reuniao_novo.",
								   ".$_POST['ar_envolvidos'][$nr_conta]."
								 );
					       ";
					$nr_conta++;
				}
			}
			else
			{
				// ---> DESFAZ A TRANSACAO COM BD<--- //
				pg_query($db,"ROLLBACK TRANSACTION");
				pg_close($db);
				echo "Erro a tentar incluir esta reunião (SEQ)";	
				exit;
			}			
		}

		#echo "<PRE>".$sql;	exit;
		
		$ob_resul= @pg_query($db,$sql);
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
			if($cd_reuniao_novo > 0)
			{
				$_REQUEST['cd_reuniao'] = $cd_reuniao_novo;
			}
			else
			{
				$_REQUEST['cd_reuniao'] = $_POST['cd_reuniao'];
			}
			
			$_REQUEST['cd_acomp'] = $_POST['cd_acomp'];
			
			echo "	<script>			
						opener.location.href = opener.location.href;
					</script>";				
		}		
	}	     

	if ($_REQUEST['cd_reuniao'] != '') 
	{
		$sql = "SELECT cd_reuniao, 
					   TO_CHAR(dt_reuniao,'DD/MM/YYYY') AS dt_reuniao, 
					   descricao, 
					   envolvidos,
					   assunto,
					   motivo,
					   ds_arquivo,
					   ds_arquivo_fisico
				  FROM projetos.reunioes_projetos 
				 WHERE cd_acomp   = ".$_REQUEST['cd_acomp']." 
				   AND cd_reuniao = ".$_REQUEST['cd_reuniao'];
		$rs = pg_query($db, $sql);
		$ar_select = pg_fetch_array($rs);
	}
?>
<html>
<head>
	<title>...:: Registro de reuniões de projeto ::...</title>
	<style>
		*{
			font-size: 10pt;
			font-weight: normal;
			font-family: Verdana, Arial, 'MS Sans Serif';			
		}
		
		body{
			background: #D4D0C8;
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
		
		input{
			background: #FFFFFF;
			width:100%;			
		}

		select{
			background: #FFFFFF;
			width:100%;			
		}
		
		optgroup {
			font-weight: bold;
		}
		
		textarea{
			background: #FFFFFF;
			height:100px; 
			width:100%;
		}
		
		span{
			font-size: 8pt;
		}
		
		.css_botao{
			text-align:right;
			width: 100%;
		}
	</style>
	<script src="inc/mascara.js"></script>
	<script language="JavaScript">
		var ob_window = "";
		function validForm() 
		{
			var ds_msg_erro = "";

			if (trimValue(document.getElementById('dt_reuniao').value) == "") 
			{
			   ds_msg_erro += "\n- Informe a Data da reunião";
			}
			
			if (trimValue(document.getElementById('descricao').value) == "") 
			{
			   ds_msg_erro += "\n- Informe o Resumo";
			}
			
			if (document.getElementById('ob_envolvidos').childNodes.length < 2)
		    {
			   ds_msg_erro += "\n- Informe os Envolvidos";
			}
			
			if(trimValue(ds_msg_erro) != "")
			{
				alert("Os seguinte itens são necessários:\n" + ds_msg_erro)
			}
			else
			{		
				document.getElementById('frmHabRec').submit();
			}			
		}
		
		function anexoDel(ds_arquivo_fisico)
		{
			if(confirm('Deseja realmente excluir o anexo da reunião?'))
			{
				document.getElementById('ds_arquivo_fisico').value = ds_arquivo_fisico;
				document.getElementById('frmHabRec').submit();
			}
		}
		
		function incluiEnvolvido()
		{
			if(document.getElementById('envolvidos').value != "")
			{
				if(checkEnvolvido(document.getElementById('envolvidos').value))
				{
					var ob_envolvidos = document.getElementById('ob_envolvidos')
					var id_div        = 'div_env_' + document.getElementById('envolvidos').value;
					var id_input      = 'env_' + document.getElementById('envolvidos').value;
					var sel_text      = getSelectText(document.getElementById('envolvidos'));
					
					var ob_div    = document.createElement('div');
						ob_div.id = id_div;
					
					var ob_input             = document.createElement('input');
						ob_input.id          = id_input;
						ob_input.name        = "ar_envolvidos[]";
						ob_input.type        = "hidden";
						ob_input.style.width = 40;
						ob_input.value       = document.getElementById('envolvidos').value;
						ob_div.appendChild(ob_input);

					var ob_img              = document.createElement('img');
						ob_img.src          = "img/delete.png";
						ob_img.border       = 0;
						ob_img.title        = "Remover " + sel_text;
						ob_img.onclick      = function(){ ob_envolvidos.removeChild(document.getElementById(id_div)); }
						ob_img.style.cursor = "pointer";
						ob_div.appendChild(ob_img);
						
					var ob_span           = document.createElement('span');
						ob_span.innerHTML = " " + sel_text + " ";
						ob_div.appendChild(ob_span);

					ob_envolvidos.appendChild(ob_div);
				}
			}
		}
		
		function checkEnvolvido(id)
		{
			if(document.getElementById('env_'+id))
			{
				return false;
			}
			else
			{
				return true;
			}
		}
		
		function getSelectText(obj)
		{
			for(i = 0; i < obj.options.length; i++)
			{
				if(obj.options[i].selected)
				{
					return obj.options[i].text;
				}
			}
			
			return '';
		}
		
		function excluirEnvolvido(cd_usuario)
		{
			if(confirm("Deseja realmente excluir o envolvido?"))
			{
				document.getElementById('ob_envolvidos').removeChild(document.getElementById('div_env_' + cd_usuario));
			}
		}
		
		function win_reuniao_relatorio(cd_acomp, cd_reuniao) 
		{
			if(ob_window != "")
			{
				ob_window.close();
			}
			
			if(cd_reuniao == 0)
			{
				cd_reuniao = "";
			}		

			var ds_url = "registro_reunioes_projeto_rel.php";
				ds_url += "?cd_acomp="   + cd_acomp;
				ds_url += "&cd_reuniao=" + cd_reuniao;
			
			var nr_width = document.body.clientWidth - 50;
			var nr_height = document.body.clientHeight - 50;
			var nr_left = ((screen.width - 10) - nr_width) / 2;
			var nr_top = ((screen.height - 80) - nr_height) / 2;

			ob_window = window.open(ds_url, "wReuniaoRel", "left="+nr_left+",top="+nr_top+",width="+nr_width+",height="+nr_height+",scrollbars=yes,resizable=yes,directories=no,location=no,menubar=yes,status=no,titlebar=no,toolbar=yes");		 		
		}		
	</script>	
</head>
<body>
<form name="frmHabRec" id="frmHabRec" action="" method="post" enctype="multipart/form-data">
	<?
		echo "<input type='hidden' name='cd_acomp' value='".$_REQUEST['cd_acomp']."'>";
		echo "<input type='hidden' name='cd_reuniao' value='".$_REQUEST['cd_reuniao']."'>";
		echo "<input type='hidden' name='ds_arquivo_fisico' id='ds_arquivo_fisico' value=''>";
	?>
	
	<fieldset>
		<legend>Registro de Reunião</legend>
		
		<div class="css_botao">
			<img src="img/salvar_p.gif"        onclick="validForm();" style="cursor:pointer;" border="0" title="Salvar registro de reunião">
			<img src="img/reuniao_imp_p.gif"   onClick="win_reuniao_relatorio(<? echo "'".$_REQUEST['cd_acomp']."','".$_REQUEST['cd_reuniao']."'";?>)" style="cursor:pointer; <? if(trim($_REQUEST['cd_reuniao']) == ""){ echo "display:none;"; }?>" border="0" title="Imprimir reunião">
			<img src="img/fechar_janela_p.gif" onClick="window.close();" style="cursor:pointer;" border="0" title="Fechar janela">				
		</div>
		
		<label for="dt_reuniao">Data da Reunião:</label>
		<br>
		<input type="text" name="dt_reuniao" id="dt_reuniao" value="<? echo $ar_select['dt_reuniao']; ?>" OnKeyDown="mascaraData(this,event);" maxlength="10" style="width:150px;">
		<br>
		<br>	
		
		<label for="descricao">Resumo:</label>
		<br>
		<textarea name="descricao" id="descricao" wrap="physical"><? echo $ar_select['descricao']; ?></textarea>
		<br>
		<br>	
		
		<label for="motivo">Motivo não ocorrência:</label>
		<br>
		<textarea name="motivo" id="motivo" wrap="physical"><? echo $ar_select['motivo']; ?></textarea>
		<br>
		<br>	

		<div style="float:left; width:48%;">
			<label for="envolvidos">Presentes:</label>
			<br>
				<select name="envolvidos" id="envolvidos" style="width:250px;">
					<option value="">Selecione</option>
				<?
					$qr_select = "
									SELECT codigo,
									       divisao,
										   nome
									  FROM projetos.usuarios_controledi 
									 WHERE divisao NOT IN ('FC','CEE')
									   AND tipo <> 'X'
									 ORDER BY divisao,
									          nome
								 ";	
					$ob_res_envol = pg_query($db, $qr_select);
					$divisao_atual = "";
					while ($ar_reg_envol = pg_fetch_array($ob_res_envol)) 
					{
						if($divisao_atual != $ar_reg_envol['divisao'])
						{
							echo "<optgroup label='".$ar_reg_envol['divisao']."'>";
							$divisao_atual = $ar_reg_envol['divisao'];
						}

						echo "<option value='".$ar_reg_envol['codigo']."'>".$ar_reg_envol['nome']."</option>";
					}
				?>
				</select>
				<img src="img/add.png" border="0" style="cursor:pointer;" title="Incluir envolvido" onclick="incluiEnvolvido();">
			<br><br>
			<div id="ob_envolvidos">
			<?
				if(trim($_REQUEST['cd_reuniao']) != "")
				{
					$qr_select = "
									SELECT rpe.cd_usuario,
										   uc.nome
									  FROM projetos.reunioes_projetos_envolvidos rpe,
										   projetos.usuarios_controledi uc
									 WHERE rpe.cd_usuario = uc.codigo
									   AND rpe.cd_reuniao = ".$_REQUEST['cd_reuniao']."
									   AND rpe.cd_acomp   = ".$_REQUEST['cd_acomp']."
									 ORDER BY uc.nome
								 ";	
					$ob_res_envol = pg_query($db, $qr_select);
					while ($ar_reg_envol = pg_fetch_array($ob_res_envol)) 
					{
						echo "
								<div id='div_env_".$ar_reg_envol['cd_usuario']."'>
									<input type='hidden' name='ar_envolvidos[]' id='env_".$ar_reg_envol['cd_usuario']."' value='".$ar_reg_envol['cd_usuario']."' style='width:40px;'>
									<img src='img/delete.png' border='0' title='Remover ".$ar_reg_envol['nome']."' onclick=\"excluirEnvolvido('".$ar_reg_envol['cd_usuario']."');\" style='cursor:pointer;'>
									<span>".$ar_reg_envol['nome']."</span>
								</div>
							 ";
					}
				}
			?>		
			</div>
			<br>
			<? echo $ar_select['envolvidos']; ?>
		</div>
		<div style="float:left; width:48%;">
			<label for="ds_arquivo">Anexo:</label>
			<br>
				<?
					if(trim($ar_select['ds_arquivo']) != "")
					{
							echo "<a href='file:///".$ar_select['ds_arquivo_fisico']."' class='links3' target='_blank'><img src='img/anexo_ver.gif' border='0' title='Visualizar anexo' style='cursor:pointer;'></a> ";
							echo " <img src='img/anexo_del.gif' border='0' title='Exclui anexo' style='cursor:pointer;' onclick=\"anexoDel('".$ar_select['ds_arquivo_fisico']."')\">";
							echo " ".$ar_select['ds_arquivo'];
					}
					else
					{
						echo '
								<input type="hidden" name="ds_arquivo_local" id="ds_arquivo_local" value="">
								<input type="file"   name="ds_arquivo" id="ds_arquivo"  onchange="document.getElementById(\'ds_arquivo_local\').value = this.value;">					
						     ';
					}
				?>
			<br>		
		</div>
		<br>
		<br>
		
		<div style="clear:both;">
		<label for="assunto">Assuntos Tratados:</label>
		<br>
		<textarea name="assunto" id="assunto" wrap="physical"><? echo $ar_select['assunto']; ?></textarea>
		</div>
		<br>
		<br>
	</fieldset>
	<script>
		MaskInput(document.getElementById('dt_reuniao'),      "99/99/9999");
		
		document.getElementById('envolvidos').onkeydown = checkKeycodeEnvolvidos;
		function checkKeycodeEnvolvidos(e) 
		{
			var keycode;
			if (window.event)
			{		
				keycode = window.event.keyCode;
			}
			else if (e) 
			{
				keycode = e.which;
			}
			
			if ((keycode == 13) && (document.getElementById('envolvidos').value != ''))
			{
				incluiEnvolvido();			
			}
		}

		window.onload = function () { window.focus(); };
	</script>
</form>
</body>
</html>