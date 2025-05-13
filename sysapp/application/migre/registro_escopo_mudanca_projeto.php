<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/nextval_sequence.php');
	
	if(count($_POST) > 0) 
	{
		// ---> ABRE TRANSACAO COM O BD <--- //
		pg_query($db,"BEGIN TRANSACTION");		
		
		if ($_POST['cd_mudanca_escopo'] != '') 
		{
 			#### UPDATE ####
			$qr_sql = "
				        UPDATE projetos.acompanhamento_mudanca_escopo 
						   SET nr_numero       = '".$_POST['nr_numero']."', 
							   cd_solicitante  = ".$_POST['cd_solicitante'].", 
							   cd_analista     = ".$_POST['cd_analista'].", 
							   cd_etapa        = '".$_POST['cd_etapa']."', 
							   dt_mudanca      = TO_DATE('".$_POST['dt_mudanca']."','DD/MM/YYYY'),
							   dt_aprovacao    = TO_DATE('".$_POST['dt_aprovacao']."','DD/MM/YYYY'),
							   nr_dias         = ".$_POST['nr_dias'].",
						       ds_descricao    = '".$_POST['ds_descricao']."',
							   ds_regras       = '".$_POST['ds_regras']."',
							   ds_impacto      = '".$_POST['ds_impacto']."',
							   ds_responsaveis = '".$_POST['ds_responsaveis']."',
							   ds_solucao      = '".$_POST['ds_solucao']."',
							   ds_recurso      = '".$_POST['ds_recurso']."',
							   ds_viabilidade  = '".$_POST['ds_viabilidade']."',
							   ds_modelagem    = '".$_POST['ds_modelagem']."',
							   ds_produtos     = '".$_POST['ds_produtos']."',
							   cd_usuario      = ".$_SESSION['Z']."
						 WHERE cd_acompanhamento_mudanca_escopo = ".$_POST['cd_mudanca_escopo']." 
						   AND cd_acomp                         = ".$_POST['cd_acomp'];
		}
		else 
		{
			#### INSERT ####
			$cd_mudanca_escopo_novo = getNextval("projetos", "acompanhamento_mudanca_escopo", "cd_acompanhamento_mudanca_escopo", $db); // PEGA NEXTVAL DA SEQUENCE DO CAMPO
			if ($cd_mudanca_escopo_novo > 0) // TESTA SE RETORNOU ALGUM VALOR
			{
				$qr_sql = "
						INSERT INTO projetos.acompanhamento_mudanca_escopo
								  (
									cd_acompanhamento_mudanca_escopo,
									cd_acomp,
									nr_numero,
									cd_solicitante,
									cd_analista,
									cd_etapa,
									dt_mudanca,
									dt_aprovacao,
									nr_dias,
									ds_descricao,
									ds_regras,
									ds_impacto,
									ds_responsaveis,
									ds_solucao,
									ds_recurso,
									ds_viabilidade,
									ds_modelagem,
									ds_produtos,
									cd_usuario
								  ) 
							 VALUES
								  (
									".$cd_mudanca_escopo_novo.",
									".$_POST['cd_acomp'].", 
									'".$_POST['nr_numero']."', 
									".$_POST['cd_solicitante'].", 
									".$_POST['cd_analista'].", 
									'".$_POST['cd_etapa']."', 
									TO_DATE('".$_POST['dt_mudanca']."','DD/MM/YYYY'),
									TO_DATE('".$_POST['dt_aprovacao']."','DD/MM/YYYY'),
									".$_POST['nr_dias'].", 
									'".$_POST['ds_descricao']."', 
									'".$_POST['ds_regras']."', 
									'".$_POST['ds_impacto']."', 
									'".$_POST['ds_responsaveis']."',
									'".$_POST['ds_solucao']."',
									'".$_POST['ds_recurso']."',
									'".$_POST['ds_viabilidade']."',
									'".$_POST['ds_modelagem']."',									
									'".$_POST['ds_produtos']."',
									".$_SESSION['Z']."
								  )";
			}
			else
			{
				// ---> DESFAZ A TRANSACAO COM BD<--- //
				pg_query($db,"ROLLBACK TRANSACTION");
				pg_close($db);
				echo "Erro a tentar incluir este acompanhamento (SEQ)";	
				exit;
			}
		}

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
			if($cd_mudanca_escopo_novo > 0)
			{
				$_REQUEST['cd_mudanca_escopo'] = $cd_mudanca_escopo_novo;
			}
			else
			{
				$_REQUEST['cd_mudanca_escopo'] = $_POST['cd_mudanca_escopo'];
			}
			
			$_REQUEST['cd_acomp']  = $_POST['cd_acomp'];

			echo "	<script>			
						opener.location.reload(true);
					</script>";				
		}		
	}	     

	$fl_imprimir = "display:none;";
	if ($_REQUEST['cd_mudanca_escopo'] != '') 
	{
		$qr_select = "
						SELECT cd_acompanhamento_mudanca_escopo, 
							   nr_numero,
							   cd_solicitante,
							   cd_analista,
							   cd_etapa,
							   TO_CHAR(dt_mudanca,'DD/MM/YYYY') AS dt_mudanca,
							   TO_CHAR(dt_aprovacao,'DD/MM/YYYY') AS dt_aprovacao,
							   nr_dias,							   
							   ds_descricao,
							   ds_regras,
							   ds_impacto,
							   ds_responsaveis,
							   ds_solucao,
							   ds_recurso,
							   ds_viabilidade,
							   ds_modelagem,
							   ds_produtos
						  FROM projetos.acompanhamento_mudanca_escopo 
						 WHERE cd_acompanhamento_mudanca_escopo = ".$_REQUEST['cd_mudanca_escopo']."
						   AND cd_acomp                         = ".$_REQUEST['cd_acomp']." 
					 ";
		$ob_resul  = pg_query($db, $qr_select);
		$ar_select = pg_fetch_array($ob_resul);
		$fl_imprimir = "";
	}

?>
<html>
<head>
	<title>...:: Registro Mudança do Escopo ::...</title>
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
		
		textarea{
			background: #FFFFFF;
			height:100px; 
			width:100%;
		}
		
		span{
			font-size: 8pt;
			color:#666666;
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

			if(trimValue(document.getElementById("nr_numero").value) == "")
			{
				ds_msg_erro += "\n- Informe o Número";
			}			
			
			if(trimValue(document.getElementById("cd_solicitante").value) == "")
			{
				ds_msg_erro += "\n- Informe o Solicitante";
			}
			
			if(trimValue(document.getElementById("cd_analista").value) == "")
			{
				ds_msg_erro += "\n- Informe o Analista";
			}
			
			if(trimValue(document.getElementById("cd_etapa").value) == "")
			{
				ds_msg_erro += "\n- Informe a Etapa";
			}
			
			if(trimValue(document.getElementById("dt_mudanca").value) == "")
			{
				ds_msg_erro += "\n- Informe a Dt Mudança";
			}
			
			if(trimValue(document.getElementById("dt_aprovacao").value) == "")
			{
				ds_msg_erro += "\n- Informe a Dt Aprovação";
			}
			
			if(trimValue(document.getElementById("nr_dias").value) == "")
			{
				ds_msg_erro += "\n- Informe o Tempo em dias";
			}
			
			if(trimValue(document.getElementById("ds_descricao").value) == "")
			{
				ds_msg_erro += "\n- Informe a Descrição da Mudança de Escopo";
			}

			if(trimValue(document.getElementById("ds_regras").value) == "")
			{
				ds_msg_erro += "\n- Informe as Regras do Negócio/Funcionalidades";
			}

			if(trimValue(document.getElementById("ds_impacto").value) == "")
			{
				ds_msg_erro += "\n- Informe o Impacto";
			}	

			if(trimValue(document.getElementById("ds_responsaveis").value) == "")
			{
				ds_msg_erro += "\n- Informe os Responsáveis";
			}			
			
			if(trimValue(document.getElementById("ds_modelagem").value) == "")
			{
				ds_msg_erro += "\n- Informe a Modelagem de dados";
			}		

			if(trimValue(document.getElementById("ds_produtos").value) == "")
			{
				ds_msg_erro += "\n- Informe os Produtos";
			}	

			if(trimValue(ds_msg_erro) != "")
			{
				alert("Os seguinte itens são necessários:\n" + ds_msg_erro)
			}
			else
			{		
				document.getElementById('ob_form').submit();
			}
		}
		
		function imprimirMudancaEscopo(cd_acomp, cd_mudanca_escopo) 
		{
		    if(ob_window != "")
			{
				ob_window.close();
			}

			var ds_url = "registro_escopo_mudanca_projeto_rel.php";
				ds_url += "?cd_acomp="  + cd_acomp;
				ds_url += "&cd_mudanca_escopo=" + cd_mudanca_escopo;
			
			var nr_width = document.body.clientWidth - 50;
			var nr_height = document.body.clientHeight - 50;
			var nr_left = ((screen.width - 10) - nr_width) / 2;
			var nr_top = ((screen.height - 80) - nr_height) / 2;

			ob_window = window.open(ds_url, "wEscopoMudancaRel", "left="+nr_left+",top="+nr_top+",width="+nr_width+",height="+nr_height+",scrollbars=yes,resizable=yes,directories=no,location=no,menubar=yes,status=no,titlebar=no,toolbar=yes");		 		
		}			
	</script>
</head>
<body>
	<form name="ob_form" id="ob_form" action="" method="post" enctype="multipart/form-data" onSubmit="return validForm();">
		<?
			echo "<input type='hidden' name='cd_acomp'  value='".$_REQUEST['cd_acomp']."'>";
			echo "<input type='hidden' name='cd_mudanca_escopo' value='".$_REQUEST['cd_mudanca_escopo']."'>";
		?>
		<fieldset>
			<legend>Registro Mudança do Escopo</legend>
			
			<div class="css_botao">
				<img src="img/salvar_p.gif"        onclick="validForm();" style="cursor:pointer;" border="0" title="Salvar">
				<img src="img/registro_operacional_imp_p.gif" border="0" onClick="imprimirMudancaEscopo('<? echo $_REQUEST['cd_acomp']; ?>','<? echo $_REQUEST['cd_mudanca_escopo']; ?>')" style="cursor:pointer; <? echo $fl_imprimir; ?>" title="Imprimir Mudança de Escopo">
				<img src="img/fechar_janela_p.gif" onClick="window.close();" style="cursor:pointer;" border="0" title="Fechar janela">								
			</div>
			
			<div style="float:left; width:18%">
			<label for="nr_numero">Número</label>
			<br>
			<input type="text" name="nr_numero" id="nr_numero" value="<? echo $ar_select['nr_numero']; ?>">
			</div>
			
			<div style="float:left; width:40%; margin-left:1%;">
			<label for="cd_solicitante">Solicitante</label>
			<br>
			<select name="cd_solicitante" id="cd_solicitante">
				<option value="">Selecione</option>
				<?
					$qr_solicitante = " 
								        SELECT codigo,
											   nome
										  FROM projetos.usuarios_controledi 
										 WHERE divisao <> 'SNG'
										   AND tipo    <> 'X'
										 ORDER BY nome";
					$ob_resul = pg_query($db, $qr_solicitante);
					while ($ob_reg = pg_fetch_object($ob_resul)) 
					{
						echo "<option value='".$ob_reg->codigo."' ".($ob_reg->codigo == $ar_select['cd_solicitante'] ? 'selected' : '').">".$ob_reg->nome."</option>";
					}
				?>
			</select>
			</div>
			
			<div style="float:left; width:40%; margin-left:1%;">
			<label for="cd_analista">Analista</label>
			<br>
			<select name="cd_analista" id="cd_analista">
				<option value="">Selecione</option>
				<?
					$qr_analista = " 
								        SELECT codigo,
											   nome
										  FROM projetos.usuarios_controledi 
										 WHERE divisao  <> 'SNG'
										   AND tipo     <> 'X'
										 ORDER BY nome";
					$ob_resul = pg_query($db, $qr_analista);
					while ($ob_reg = pg_fetch_object($ob_resul)) 
					{
						echo "<option value='".$ob_reg->codigo."' ".($ob_reg->codigo == $ar_select['cd_analista'] ? 'selected' : '').">".$ob_reg->nome."</option>";
					}
				?>				
			</select>
			</div>
			<br>
			<br>
			<br>			
			
			<div style="float:left; width:25%;">
			<label for="cd_etapa">Etapa Atual</label>
			<br>
			<select name="cd_etapa" id="cd_etapa">
				<option value="">Selecione</option>
				<option value="AR" <? echo ($ar_select['cd_etapa'] == 'AR' ? 'selected' : ''); ?> >Análise de Requisitos</option>
				<option value="ES" <? echo ($ar_select['cd_etapa'] == 'ES' ? 'selected' : ''); ?> >Escopo</option>
				<option value="AU" <? echo ($ar_select['cd_etapa'] == 'AU' ? 'selected' : ''); ?> >Aprovação do usuário</option>
				<option value="DE" <? echo ($ar_select['cd_etapa'] == 'DE' ? 'selected' : ''); ?> >Desenvolvimento</option>
				<option value="ME" <? echo ($ar_select['cd_etapa'] == 'ME' ? 'selected' : ''); ?> >Mudança do escopo</option>				
			</select>
			</div>
			
			<div style="float:left; width:24%; margin-left:1%;">
			<label for="dt_mudanca">Dt Mudança</label>
			<br>
			<input type="text" name="dt_mudanca" id="dt_mudanca" value="<? echo $ar_select['dt_mudanca']; ?>" OnKeyDown="mascaraData(this,event);" maxlength="10">
			</div>
			
			<div style="float:left; width:24%; margin-left:1%;">
			<label for="dt_aprovacao">Dt Aprovação</label>
			<br>
			<input type="text" name="dt_aprovacao" id="dt_aprovacao" value="<? echo $ar_select['dt_aprovacao']; ?>" OnKeyDown="mascaraData(this,event);" maxlength="10">
			</div>
			
			<div style="float:left; width:24%; margin-left:1%;">
			<label for="nr_dias">Tempo em dias</label>
			<br>
			<input type="text" name="nr_dias" id="nr_dias" value="<? echo $ar_select['nr_dias']; ?>" onKeyUp="mascaraNumero(this);">
			</div>
			<br>
			<br>
			<br>			
			
			<label for="ds_objetivo">1) Descrição da Mudança de Escopo</label>
			<br>
			<textarea name="ds_descricao" id="ds_descricao"><? echo $ar_select['ds_descricao']; ?></textarea>
			<br>
			<span>Definição da alteração  necessário no escopo do projeto.</span>
			<br>
			<br>
			
			<label for="ds_regras">2) Regras de Negócio/Funcionalidas</label>
			<br>
			<textarea name="ds_regras" id="ds_regras"><? echo $ar_select['ds_regras']; ?></textarea>
			<br>
			<span>Descrever as regras de negócio necessárias para o desenvolvimento do projeto. Estas regras são identificadas durante a definição/ revisão do processo de negócio. Definição das funcionalidades do projeto, conforme as reuniões com os responsáveis pelo projeto.</span>
			<br>
			<br>
			
			<label for="ds_impacto">3) Impacto</label>
			<br>
			<textarea name="ds_impacto" id="ds_impacto"><? echo $ar_select['ds_impacto']; ?></textarea>
			<br>
			<span>Descrever a avaliação realizada sobre o impacto que este projeto causa nos demais processos e sistemas, tais como integrações e mudanças.</span>
			<br>
			<br>

			<label for="ds_responsaveis">4) Responsáveis</label>
			<br>
			<textarea name="ds_responsaveis" id="ds_responsaveis"><? echo $ar_select['ds_responsaveis']; ?></textarea>
			<br>
			<span>Apontar os responsáveis pelos processos envolvidos no projeto. Estas pessoas deverão estar envolvidas na definição, execução e testes do projeto.  Deverá existir também um ou mais responsáveis pela aprovação do pré-escopo (conforme acordo entre analista e envolvidos).</span>
			<br>
			<br>
			
			<label for="ds_solucao">5) Solução Imediata (opcional)</label>
			<br>
			<textarea name="ds_solucao" id="ds_solucao"><? echo $ar_select['ds_solucao']; ?></textarea>
			<br>
			<span>Descrever a solução imediata e temporária de como será realizado o processo até que o projeto seja implementado. Esta solução imediata é utilizada quando não é possível esperar a conclusão do projeto para iniciar o processo, sendo definido um fluxo alternativo e imediatamente viável em comum acordo entre o analista de negócios/sistemas e o responsável pelo processo.</span>
			<br>
			<br>			
			
			<label for="ds_recurso">6) Recurso/Custo</label>
			<br>
			<textarea name="ds_recurso" id="ds_recurso"><? echo $ar_select['ds_recurso']; ?></textarea>
			<br>
			<span>Descrição do levantamento de recursos e/ou custos a serem utilizados no projeto. A avaliação só será realizada quando for necessária a utilização recursos externos para a realização do projeto.</span>
			<br>
			<br>

			<label for="ds_viabilidade">7) Viabilidade/Sugestão (opcional)</label>
			<br>
			<textarea name="ds_viabilidade" id="ds_viabilidade"><? echo $ar_select['ds_viabilidade']; ?></textarea>
			<br>
			<span>A avaliação de viabilidade sempre é realizada durante a análise de requisitos, mas este item somente será descrito neste documento se for identificado pelo analista de negócios/ sistemas que é inviável a implementação do projeto solicitado. Neste item pode ser descrita uma sugestão alternativa de como esta solicitação pode ser atendida.</span>
			<br>
			<br>

			<label for="ds_modelagem">8) Modelagem de Dados</label>
			<br>
			<textarea name="ds_modelagem" id="ds_modelagem"><? echo $ar_select['ds_modelagem']; ?></textarea>
			<br>
			<span>Referência ao ER no caso de tabelas novas e no caso de manutenções nas tabelas deve ser descrito as alterações realizadas.</span>
			<br>
			<br>

			<label for="ds_produtos">9) Produtos</label>
			<br>
			<textarea name="ds_produtos" id="ds_produtos"><? echo $ar_select['ds_produtos']; ?></textarea>
			<br>
			<span>Referenciar o nome da WBS e nome dos formulários dos produtos.</span>
			<br>
			<br>			
		</fieldset>
	</form>
</body>
</html>