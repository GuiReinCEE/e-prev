<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/nextval_sequence.php');
	
    header( 'location:'.base_url().'index.php/atividade/registro_operacional/cadastro/'.$_REQUEST['cd_operacional']);

	if(count($_POST) > 0) 
	{
		$fl_arquivo = false;
		if(count($_FILES) > 0)
		{
			$fl_arquivo = true;
		}		
		
		#### ABRE TRANSACAO COM O BD ####
		pg_query($db,"BEGIN TRANSACTION");		
		if ($_POST['cd_operacional'] != '') 
		{
			$dt_finalizado = "";
			if($_REQUEST['fl_finaliza'] == "S")
			{
				$dt_finalizado = "dt_finalizado  = CURRENT_TIMESTAMP,";
			}
			
			if($_REQUEST['fl_finaliza'] == "A")
			{
				$dt_finalizado = "dt_finalizado  = NULL,";
			}	

			$qr_arquivo = "";
			if($fl_arquivo)
			{
				$qr_arquivo.=" ds_arquivo        = '".$_FILES['ds_arquivo']['name']."',";
				$qr_arquivo.=" ds_arquivo_fisico = '".$_POST['ds_arquivo_local']."',";
			}
			
			if(trim($_REQUEST['fl_finaliza']) == 'AN')
			{
				$qr_arquivo.=" ds_arquivo        = NULL,";
				$qr_arquivo.=" ds_arquivo_fisico = NULL,";
			}			
			
			#### UPDATE ####
			$qr_sql = "
				        UPDATE projetos.acompanhamento_registro_operacional 
						   SET ds_nome                           = '".$_POST['ds_nome']."', 
							   ds_processo_faz                   = '".$_POST['ds_processo_faz']."',
							   ds_processo_faz_complemento       = '".$_POST['ds_processo_faz_complemento']."',							   
							   ds_processo_executado             = '".$_POST['ds_processo_executado']."', 
							   ds_processo_executado_complemento = '".$_POST['ds_processo_executado_complemento']."',
							   ds_calculo                        = '".$_POST['ds_calculo']."',
							   ds_calculo_complemento            = '".$_POST['ds_calculo_complemento']."',
							   ds_responsaveis                   = '".$_POST['ds_responsaveis']."',
							   ds_requesito                      = '".$_POST['ds_requesito']."',
							   ds_requesito_complemento          = '".$_POST['ds_requesito_complemento']."',
							   ds_necessario                     = '".$_POST['ds_necessario']."',
							   ds_necessario_complemento         = '".$_POST['ds_necessario_complemento']."',
							   ds_integridade                    = '".$_POST['ds_integridade']."',									
							   ds_integridade_complemento        = '".$_POST['ds_integridade_complemento']."',
							   ds_resultado                      = '".$_POST['ds_resultado']."',
							   ds_resultado_complemento          = '".$_POST['ds_resultado_complemento']."',
							   ".$dt_finalizado."
							   ".$qr_arquivo."
							   ds_local                          = '".$_POST['ds_local']."'							   
						 WHERE cd_acompanhamento_registro_operacional = ".$_POST['cd_operacional']." 
						   AND cd_acomp                               = ".$_POST['cd_acomp'].";
					  ";
			#### ENVIA EMAIL ####
			$qr_sql.= sqlEmail($_REQUEST['fl_finaliza']);
		}
		else 
		{
			#### INSERT ####
			$cd_processo_novo = getNextval("projetos", "acompanhamento_registro_operacional", "cd_acompanhamento_registro_operacional", $db); // PEGA NEXTVAL DA SEQUENCE DO CAMPO
			if ($cd_processo_novo > 0) // TESTA SE RETORNOU ALGUM VALOR
			{
				$qr_sql = "
							INSERT INTO projetos.acompanhamento_registro_operacional
									  (
										cd_acompanhamento_registro_operacional,
										cd_acomp,
										ds_nome,
										ds_processo_faz,
										ds_processo_faz_complemento,
										ds_processo_executado,
										ds_processo_executado_complemento,
										ds_calculo,
										ds_calculo_complemento,
										ds_responsaveis,
										ds_requesito,
										ds_requesito_complemento,
										ds_necessario,
										ds_necessario_complemento,
										ds_integridade,
										ds_integridade_complemento,
										ds_resultado,
										ds_resultado_complemento,
										ds_local,
					      ";
				if($fl_arquivo)
				{
					$qr_sql.="         ds_arquivo,
	                                   ds_arquivo_fisico,";
				}							  
				if(($_REQUEST['fl_finaliza'] == "S") or ($_REQUEST['fl_finaliza'] == "A"))
				{						  
					$qr_sql.= "         dt_finalizado,";		  
				}
				$qr_sql.= "				cd_usuario
									  ) 
								 VALUES
									  (
										".$cd_processo_novo.",
										".$_POST['cd_acomp'].", 
										'".$_POST['ds_nome']."', 
										'".$_POST['ds_processo_faz']."', 
										'".$_POST['ds_processo_faz_complemento']."',
										'".$_POST['ds_processo_executado']."', 
										'".$_POST['ds_processo_executado_complemento']."', 
										'".$_POST['ds_calculo']."',
										'".$_POST['ds_calculo_complemento']."',
										'".$_POST['ds_responsaveis']."',
										'".$_POST['ds_requesito']."',
										'".$_POST['ds_requesito_complemento']."',
										'".$_POST['ds_necessario']."',
										'".$_POST['ds_necessario_complemento']."',
										'".$_POST['ds_integridade']."',									
										'".$_POST['ds_integridade_complemento']."',									
										'".$_POST['ds_resultado']."',
										'".$_POST['ds_resultado_complemento']."',
										'".$_POST['ds_local']."',
						  ";
				if($fl_arquivo)
				{
					$qr_sql.="          '".$_FILES['ds_arquivo']['name']."',
					                    '".$_POST['ds_arquivo_local']."',";
				}						  
				if($_REQUEST['fl_finaliza'] == "S")
				{						  
					$qr_sql.= "         CURRENT_TIMESTAMP,";		  
				}	
				if($_REQUEST['fl_finaliza'] == "A")
				{						  
					$qr_sql.= "         NULL,";		  
				}				
				$qr_sql.= "		        ".$_SESSION['Z']."
									  );";
				
				$_POST['cd_operacional'] = $cd_processo_novo;
				#### ENVIA EMAIL ####
				$qr_sql.= sqlEmail("I");
			}
			else
			{
				#### DESFAZ A TRANSACAO COM BD ####
				pg_query($db,"ROLLBACK TRANSACTION");
				pg_close($db);
				echo "Erro a tentar incluir este registro operacional (SEQ)";	
				exit;
			}
		}
			
			//echo "<PRE>".$qr_sql; exit;
		
		$ob_resul= @pg_query($db,$qr_sql);
		if(!$ob_resul)
		{
			$ds_erro = "ERRO: ".str_replace("ERROR:","",pg_last_error($db));
			#### DESFAZ A TRANSACAO COM BD ####
			pg_query($db,"ROLLBACK TRANSACTION");
			pg_close($db);
			echo $ds_erro;
			exit;
		}
		else
		{
			#### COMITA DADOS NO BD ####
			pg_query($db,"COMMIT TRANSACTION"); 	
			if($cd_processo_novo > 0)
			{
				$_REQUEST['cd_operacional'] = $cd_processo_novo;
			}
			else
			{
				$_REQUEST['cd_operacional'] = $_POST['cd_operacional'];
			}
			
			$_REQUEST['cd_acomp']  = $_POST['cd_acomp'];
			
			echo "	<script>			
						opener.location.href = opener.location.href;
					</script>";				
		}		
	}	     

	if ($_REQUEST['cd_operacional'] != '') 
	{
		$qr_select = "
						SELECT 
						       aro.ds_nome,
							   aro.ds_processo_faz,
							   aro.ds_processo_faz_complemento,
							   aro.ds_processo_executado,
							   aro.ds_processo_executado_complemento,
							   aro.ds_calculo,
							   aro.ds_calculo_complemento,
							   aro.ds_responsaveis,
							   aro.ds_requesito,
							   aro.ds_requesito_complemento,
							   aro.ds_necessario,
							   aro.ds_necessario_complemento,
							   aro.ds_integridade,
							   aro.ds_integridade_complemento,
							   aro.ds_resultado,
							   aro.ds_resultado_complemento,
							   aro.ds_local,
							   aro.dt_finalizado,
							   aro.cd_usuario,
							   aro.ds_arquivo,
							   aro.ds_arquivo_fisico,
							   uc.nome AS ds_usuario
						  FROM projetos.acompanhamento_registro_operacional aro,
                               projetos.usuarios_controledi uc						  
						 WHERE aro.cd_acompanhamento_registro_operacional = ".$_REQUEST['cd_operacional']."
						   AND aro.cd_acomp                               = ".$_REQUEST['cd_acomp']." 
						   AND aro.cd_usuario                             = uc.codigo
					 ";
		$ob_resul  = pg_query($db, $qr_select);
		$ar_select = pg_fetch_array($ob_resul);
		
		$fl_display_imprimir  = "";
		$fl_display_salvar    = "display:none;";
		$fl_display_finalizar = "display:none;";
		$fl_display_reiniciar = "display:none;";
		$fl_editar            = 1;
		$fl_editar_analista   = 1;
		
		if((trim($ar_select['cd_usuario']) == $_SESSION['Z']) and (trim($ar_select['dt_finalizado']) == "")) 
		{
			$fl_display_salvar    = "";
			$fl_display_finalizar = "";
			$fl_editar            = 0;
		}	
		else
		{
			$qr_analista = " 
							  SELECT COUNT(cd_analista) AS fl_analista
								FROM projetos.analista_projeto 
							   WHERE cd_projeto  = (SELECT cd_projeto
							                          FROM projetos.acompanhamento_projetos
													 WHERE cd_acomp = ".$_REQUEST['cd_acomp'].")
								 AND cd_acomp    = ".$_REQUEST['cd_acomp']."
								 AND cd_analista = ".$_SESSION['Z'];
			$ob_resul    = pg_query($db, $qr_analista);
			$ob_analista = pg_fetch_object($ob_resul);							 
			if(($ob_analista->fl_analista > 0) and (trim($ar_select['dt_finalizado']) != ""))
			{
				$fl_display_salvar    = "";
				$fl_editar_analista   = 0;
				$fl_display_reiniciar = "";
			}
		}
	}
	else
	{
		$fl_display_imprimir  = "display:none;";
		$fl_display_reiniciar = "display:none;";
		$fl_editar            = 0;
		$fl_editar_analista   = 1;
		$qr_autor = " 
					  SELECT uc.nome AS ds_usuario
						FROM projetos.usuarios_controledi uc 
					   WHERE uc.codigo = ".$_SESSION['Z'];
		$ob_resul  = pg_query($db, $qr_autor);
		$ar_select = pg_fetch_array($ob_resul);			
	}
?>
<html>
<head>
	<title>...:: Registro Operacional ::...</title>
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
		
		input{
			background: #FFFFFF;
			width:100%;			
		}
		
		textarea{
			background: #FFFFFF;
			height:100px; 
			width:100%
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
	<script src="inc/mascara.js"></script>
	<script language="JavaScript">
		function validForm(fl_finaliza)
		{
			var ds_msg_erro = "";

			if(trimValue(document.getElementById("cd_acomp").value) == "")
			{
				ds_msg_erro += "\n- Informe o Projeto";
			}			
			
			if(trimValue(document.getElementById("ds_nome").value) == "")
			{
				ds_msg_erro += "\n- Informe o Nome Processo";
			}			
			
			if(trimValue(ds_msg_erro) != "")
			{
				alert("Os seguinte itens são necessários:\n" + ds_msg_erro)
			}
			else
			{		
				if(fl_finaliza == "N")
				{
					<?
						if($fl_editar_analista == 1)
						{
							echo 'alert("Para encaminhar o Registro Operacional clique em Finalizar.");';
						}
					?>
					
					document.getElementById('ob_form').action = "?fl_finaliza=" + fl_finaliza;
					document.getElementById('ob_form').submit();					
				}
				
				if(fl_finaliza == "S")
				{
					if(confirm("Você realmente deseja Finalizar e encaminhar o Registro Operacional?"))
					{
						document.getElementById('ob_form').action = "?fl_finaliza=" + fl_finaliza;
						document.getElementById('ob_form').submit();
					}
				}
				
				if(fl_finaliza == "A")
				{
					if(confirm("Você realmente deseja Reiniciar e encaminhar o Registro Operacional?"))
					{
						document.getElementById('ob_form').action = "?fl_finaliza=" + fl_finaliza;
						document.getElementById('ob_form').submit();
					}
				}		

				if(fl_finaliza == "AN")
				{
					document.getElementById('ob_form').action = "?fl_finaliza=" + fl_finaliza;
					document.getElementById('ob_form').submit();
				}				
			}
		}
		
		function anexoDel(ds_arquivo_fisico)
		{
			if(confirm('Deseja realmente excluir o anexo?'))
			{
				validForm('AN');
			}
		}		
		
		function imprimir(cd_acomp, cd_operacional)
		{
			var ds_url = "registro_operacional_projeto_rel.php";
				ds_url += "?cd_acomp="       + cd_acomp;
				ds_url += "&cd_operacional=" + cd_operacional;	

			document.location.href = ds_url;
		}
	</script>
</head>
<body>
	<form name="ob_form" id="ob_form" action="" method="post" enctype="multipart/form-data" onSubmit="return validForm();">
		<?
			echo "<input type='hidden' name='cd_operacional' value='".$_REQUEST['cd_operacional']."'>";
		?>
		<fieldset>
			<legend>Registro Operacional</legend>
			
			<div class="css_botao">
				<img src="img/salvar_p.gif"                      onclick="validForm('N');" style="cursor:pointer; <? echo $fl_display_salvar;    ?>" title="Salvar Registro Operacional">
				<img src="img/registro_operacional_ok_p.gif"     onclick="validForm('S');" style="cursor:pointer; <? echo $fl_display_finalizar; ?>" title="Finalizar e encaminhar Registro Operacional">
				<img src="img/registro_operacional_ok_nao_p.gif" onclick="validForm('A');" style="cursor:pointer; <? echo $fl_display_reiniciar; ?>" title="Reiniciar e encaminhar Registro Operacional">				
				<img src="img/registro_operacional_imp_p.gif"    onClick="imprimir(<? echo "'".$_REQUEST['cd_acomp']."','".$_REQUEST['cd_operacional']."'"; ?>)" style="cursor:pointer; <? echo $fl_display_imprimir; ?>" title="Imprimir Registro Operacional">
				<img src="img/fechar_janela_p.gif"               onClick="window.close();" style="cursor:pointer;" title="Fechar janela">				
			</div>
			
			<label for='cd_acomp'>Projeto:</label>
			<br>
			<?	
					
				if((trim($_REQUEST['cd_acomp']) != "") and (trim($ar_select['dt_finalizado']) != ""))
				{
					$qr_select = "
									SELECT ap.cd_acomp,
										   p.nome
									  FROM projetos.acompanhamento_projetos ap
									  JOIN projetos.projetos p 
										ON ap.cd_projeto  = p.codigo
									 WHERE ap.cd_acomp    = ".$_REQUEST['cd_acomp']."
								 ";	
					$ob_resul_acomp = pg_query($db, $qr_select);	
					$ar_reg_acomp = pg_fetch_array($ob_resul_acomp);
					echo "
					<input type='hidden' name='cd_acomp' id='cd_acomp' value='".$_REQUEST['cd_acomp']."'>
					<input type='text' name='ds_acomp'  value='".$ar_reg_acomp['nome']."' style='width:100%; background:white;' readonly>
					<br>
					<br>";
				}
				else
				{
					echo "
						<select name='cd_acomp' id='cd_acomp' style='width:50%; background:white;'>
							<option value='' style='width:50%;background:white;'>Selecione</option>";
							$qr_select = "
											SELECT ap.cd_acomp,
											       p.nome
											  FROM projetos.acompanhamento_projetos ap
											  JOIN projetos.projetos p 
											    ON ap.cd_projeto      = p.codigo
											   AND ap.dt_encerramento IS NULL
											 WHERE (0 < (SELECT COUNT(*)
											              FROM projetos.projetos_envolvidos pe
											             WHERE pe.cd_projeto   = p.codigo
											               AND pe.cd_envolvido = ".$_SESSION['Z']."))
											   OR (0 < (SELECT COUNT(*)
											              FROM projetos.analista_projeto ap
											             WHERE ap.cd_projeto   = p.codigo
											               AND ap.cd_analista = ".$_SESSION['Z']."))
											 ORDER BY p.nome								
										 ";	
							$ob_resul_acomp = pg_query($db, $qr_select);
							while ($ar_reg_acomp = pg_fetch_array($ob_resul_acomp)) 
							{
								$selecionado = "";
								if($_REQUEST['cd_acomp'] == $ar_reg_acomp['cd_acomp'])
								{
									$selecionado = "selected";
								}
								echo "<option value='".$ar_reg_acomp['cd_acomp']."' style='width:50%;background:white;' ".$selecionado.">".$ar_reg_acomp['nome']."</option>";
							}
						
					echo "
						</select>
						<br>
						<br>";
				}
			?>	
			
			<label for="ds_autor">Autor</label>
			<br>
			<input type="text" name="ds_autor" id="ds_autor" value="<? echo $ar_select['ds_usuario']; ?>" readonly style="width:100%; background:white;">
			<br>
			<br>			
			
			<label for="ds_nome">Nome Processo</label>
			<br>
			<input type="text" name="ds_nome" id="ds_nome" value="<? echo $ar_select['ds_nome']; ?>" <? if($fl_editar == 1) { echo "readonly"; }?> style="width:100%; background:white;">
			<br>
			<br>			
			
			<label for="ds_processo_faz">1) O que o processo faz?</label>
			<br>
			<textarea name="ds_processo_faz" id="ds_processo_faz" <? if($fl_editar == 1) { echo "readonly"; } ?>><? echo $ar_select['ds_processo_faz']; ?></textarea>
			<br>
			<span>...</span>
			<div>
				<label class="complemento" for="ds_processo_faz_complemento">Complemento do Analista</label>
				<br>
				<textarea class="complemento" name="ds_processo_faz_complemento" id="ds_processo_faz_complemento" <? if($fl_editar_analista == 1) { echo "readonly"; } ?>><? echo $ar_select['ds_processo_faz_complemento']; ?></textarea>
			</div>
			<br>
			<br>
			
			<label for="ds_processo_executado">2) De que maneira é executado o processo?</label>
			<br>
			<textarea name="ds_processo_executado" id="ds_processo_executado" <? if($fl_editar == 1) { echo "readonly"; } ?>><? echo $ar_select['ds_processo_executado']; ?></textarea>
			<br>
			<span>...</span>
			<div>
				<label class="complemento" for="ds_processo_executado_complemento">Complemento do Analista</label>
				<br>
				<textarea class="complemento" name="ds_processo_executado_complemento" id="ds_processo_executado_complemento" <? if($fl_editar_analista == 1) { echo "readonly"; } ?>><? echo $ar_select['ds_processo_executado_complemento']; ?></textarea>
			</div>			
			<br>
			<br>
			
			<label for="ds_calculo">3) Cálculos</label>
			<br>
			<textarea name="ds_calculo" id="ds_calculo" <? if($fl_editar == 1) { echo "readonly"; } ?>><? echo $ar_select['ds_calculo']; ?></textarea>
			<br>
			<span>Descrever os cálculos em fórmulas.</span>
			<div>
				<label class="complemento" for="ds_calculo_complemento">Complemento do Analista</label>
				<br>
				<textarea class="complemento" name="ds_calculo_complemento" id="ds_calculo_complemento" <? if($fl_editar_analista == 1) { echo "readonly"; } ?>><? echo $ar_select['ds_calculo_complemento']; ?></textarea>
			</div>			
			<br>
			<br>

			<label for="ds_responsaveis">4) Responsáveis</label>
			<br>
			<textarea name="ds_responsaveis" id="ds_responsaveis" <? if($fl_editar == 1) { echo "readonly"; } ?>><? echo $ar_select['ds_responsaveis']; ?></textarea>
			<br>
			<span>Pessoas que executam e respondem pelo processo.</span>
			<br>
			<br>
			
			<label for="ds_requesito">5) O que é necessário para que este processo possa ocontecer?</label>
			<br>
			<textarea name="ds_requesito" id="ds_requesito" <? if($fl_editar == 1) { echo "readonly"; } ?>><? echo $ar_select['ds_requesito']; ?></textarea>
			<br>
			<span>Descreva os procedimentos e processos que devem ocorrer antes da execução deste processo.</span>
			<div>
				<label class="complemento" for="ds_requesito_complemento">Complemento do Analista</label>
				<br>
				<textarea class="complemento" name="ds_requesito_complemento" id="ds_requesito_complemento" <? if($fl_editar_analista == 1) { echo "readonly"; } ?>><? echo $ar_select['ds_requesito_complemento']; ?></textarea>
			</div>			
			<br>
			<br>			
			
			<label for="ds_necessario">6) Este processo é necessário para qual(is) outro(s) processo(s)?</label>
			<br>
			<textarea name="ds_necessario" id="ds_necessario" <? if($fl_editar == 1) { echo "readonly"; } ?>><? echo $ar_select['ds_necessario']; ?></textarea>
			<br>
			<span>Descreva quais os outros processos que dependem deste processo para acontecerem.</span>
			<div>
				<label class="complemento" for="ds_necessario_complemento">Complemento do Analista</label>
				<br>
				<textarea class="complemento" name="ds_necessario_complemento" id="ds_necessario_complemento" <? if($fl_editar_analista == 1) { echo "readonly"; } ?>><? echo $ar_select['ds_necessario_complemento']; ?></textarea>
			</div>			
			<br>
			<br>

			<label for="ds_integridade">7) Integração com outros sistemas</label>
			<br>
			<textarea name="ds_integridade" id="ds_integridade" <? if($fl_editar == 1) { echo "readonly"; } ?>><? echo $ar_select['ds_integridade']; ?></textarea>
			<br>
			<span>Descreva as integrações com outros sistemas, indicando o que ocorre em cada uma destas integrações.</span>
			<div>
				<label class="complemento" for="ds_integridade_complemento">Complemento do Analista</label>
				<br>
				<textarea class="complemento" name="ds_integridade_complemento" id="ds_integridade_complemento" <? if($fl_editar_analista == 1) { echo "readonly"; } ?>><? echo $ar_select['ds_integridade_complemento']; ?></textarea>
			</div>						
			<br>
			<br>

			<label for="ds_resultado">8) Resultados</label>
			<br>
			<textarea name="ds_resultado" id="ds_resultado" <? if($fl_editar == 1) { echo "readonly"; } ?>><? echo $ar_select['ds_resultado']; ?></textarea>
			<br>
			<span>Descreva os resultados desejados após a execução deste processo.</span>
			<div>
				<label class="complemento" for="ds_resultado_complemento">Complemento do Analista</label>
				<br>
				<textarea class="complemento" name="ds_resultado_complemento" id="ds_resultado_complemento" <? if($fl_editar_analista == 1) { echo "readonly"; } ?>><? echo $ar_select['ds_resultado_complemento']; ?></textarea>
			</div>			
			<br>
			<br>

			<label for="ds_local">9) Telas / Relatórios / Planilhas</label>
			<br>
			<textarea name="ds_local" id="ds_local" <? if($fl_editar == 1) { echo "readonly"; } ?>><? echo $ar_select['ds_local']; ?></textarea>
			<br>
			<span>Informe o caminho do menu das telas e dos relatórios e/ou envie as planilhas e documentos utilizados atualmente para execução deste processo.</span>
			<br>
			<br>	

			<?
				if($fl_display_reiniciar == "")
				{
			?>
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
			<br>
			<?
				}
			?>			
		</fieldset>
	</form>
</body>
</html>

<?

	function sqlEmail($tp_envio)
	{
		global $db;
		$cd_enter  = chr(13);
		$ds_status = "";
		
		if($tp_envio == "S")
		{
			$ds_status = "FINALIZADO";
		}
		
		if($tp_envio == "A")
		{
			$ds_status = "REINICIADO";
		}

		if($tp_envio == "I")
		{
			$ds_status = "INICIADO";
		}		
		
		if(trim($ds_status) != "")
		{
			$qr_analista = "
							SELECT uc.nome
							  FROM projetos.analista_projeto ap,
                                   projetos.usuarios_controledi uc 
							 WHERE ap.cd_projeto  = (SELECT cd_projeto
							                           FROM projetos.acompanhamento_projetos
													  WHERE cd_acomp = ".$_POST['cd_acomp'].")
							   AND ap.cd_acomp    = ".$_POST['cd_acomp']."
                               AND ap.cd_analista = uc.codigo			
			               ";
			$rs = pg_query($db, $qr_analista);
			$lt_analista = "";
			while ($ar_reg = pg_fetch_array($rs)) 
			{
				$lt_analista.= $cd_enter."- ".$ar_reg['nome'];
			}
			$qr_email = "
						  SELECT uc.usuario AS ds_email,
						         uc.nome,
								 (SELECT p.nome
									FROM projetos.acompanhamento_projetos ap,
									     projetos.projetos p
								   WHERE ap.cd_acomp   = ".$_POST['cd_acomp']."
								     AND ap.cd_projeto = p.codigo)	AS ds_projeto							   
							FROM projetos.analista_projeto ap,
								 projetos.usuarios_controledi uc
						   WHERE ap.cd_projeto  = (SELECT cd_projeto
													 FROM projetos.acompanhamento_projetos
													WHERE cd_acomp = ".$_POST['cd_acomp'].")
							 AND ap.cd_analista = uc.codigo
							 AND ap.cd_acomp    = ".$_POST['cd_acomp']."
						";
			
			#### EMAIL DO AUTOR ####
			if($tp_envio == "I")
			{
				$qr_email.= "
						   UNION 
					      SELECT uc.usuario AS ds_email,
						         uc.nome,
								 (SELECT p.nome
									FROM projetos.acompanhamento_projetos ap,
									     projetos.projetos p
								   WHERE ap.cd_acomp   = ".$_POST['cd_acomp']."
								     AND ap.cd_projeto = p.codigo)	AS ds_projeto
							FROM projetos.usuarios_controledi uc
						   WHERE uc.codigo = ".$_SESSION['Z']."
				            ";
			}
			else
			{
				$qr_email.= "
						   UNION 
					      SELECT uc.usuario AS ds_email,
						         uc.nome,
								 (SELECT p.nome
									FROM projetos.acompanhamento_projetos ap,
									     projetos.projetos p
								   WHERE ap.cd_acomp   = ".$_POST['cd_acomp']."
								     AND ap.cd_projeto = p.codigo)	AS ds_projeto
							FROM projetos.acompanhamento_registro_operacional aro,
							     projetos.usuarios_controledi uc
						   WHERE aro.cd_acomp                               = ".$_POST['cd_acomp']."
					         AND aro.cd_acompanhamento_registro_operacional = ".$_POST['cd_operacional']."
							 AND aro.cd_usuario                             = uc.codigo
				            ";			
			}
			
			$rs = pg_query($db, $qr_email);
			while ($ar_email = pg_fetch_array($rs)) 
			{
				if(trim($ar_email['ds_email']) != "")
				{
					$ds_assunto = "Registro Operacional - ".$ar_email['ds_projeto'];
					$ds_msg = "REGISTRO OPERACIONAL".$cd_enter;
					$ds_msg.= "-----------------------------------------------------------------------".$cd_enter;
					$ds_msg.= "PROJETO: ".$ar_email['ds_projeto'].$cd_enter;
					$ds_msg.= "-----------------------------------------------------------------------".$cd_enter;
					$ds_msg.= "NOME PROCESSO: ".$_POST['ds_nome'].$cd_enter;
					$ds_msg.= "-----------------------------------------------------------------------".$cd_enter;
					$ds_msg.= "AUTOR: ".$_POST['ds_autor'].$cd_enter;
					$ds_msg.= "-----------------------------------------------------------------------".$cd_enter;
					$ds_msg.= "STATUS: ".$ds_status." (".$_SESSION['N'].")".$cd_enter;
					$ds_msg.= "-----------------------------------------------------------------------".$cd_enter;
					$ds_msg.= "ANALISTAS: ".$lt_analista.$cd_enter;
					$ds_msg.= "-----------------------------------------------------------------------".$cd_enter;
					$ds_msg.= "Esta mensagem foi enviada pelo Sistema de controle.".$cd_enter;
					$qr_sql.= "   
								INSERT INTO projetos.envia_emails 
									 ( 
									   dt_envio, 
									   de,
									   para,
									   cc,
									   cco,
									   assunto,
									   texto 
									 ) 
								VALUES
									 ( 
									   CURRENT_DATE, 
									   'Sistema de controle',
									   '".$ar_email['ds_email']."@eletroceee.com.br', 
									   ' ',
									   ' ',
									   '".$ds_assunto." - ".$ds_status."', 
									   '".str_replace("'", "`", $ds_msg)."'
									 );
								";	
				}
			}
		}
		return $qr_sql;
	}

?>