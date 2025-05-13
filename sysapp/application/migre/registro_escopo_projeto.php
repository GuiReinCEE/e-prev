<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/nextval_sequence.php');
	
	if(count($_POST) > 0) 
	{
		// ---> ABRE TRANSACAO COM O BD <--- //
		pg_query($db,"BEGIN TRANSACTION");		
		
		if ($_POST['cd_escopo'] != '') 
		{
 			#### UPDATE ####
			$qr_sql = "
				        UPDATE projetos.acompanhamento_escopos 
						   SET ds_objetivos    = '".$_POST['ds_objetivo']."',
							   ds_regras       = '".$_POST['ds_regras']."',
							   ds_impacto      = '".$_POST['ds_impacto']."',
							   ds_responsaveis = '".$_POST['ds_responsaveis']."',
							   ds_solucao      = '".$_POST['ds_solucao']."',
							   ds_recurso      = '".$_POST['ds_recurso']."',
							   ds_viabilidade  = '".$_POST['ds_viabilidade']."',
							   ds_modelagem    = '".$_POST['ds_modelagem']."',
							   ds_produtos     = '".$_POST['ds_produtos']."',
							   cd_usuario      = ".$_SESSION['Z']."
						 WHERE cd_acompanhamento_escopos = ".$_POST['cd_escopo']." 
						   AND cd_acomp                  = ".$_POST['cd_acomp'];
		}
		else 
		{
			#### INSERT ####
			$cd_escopo_novo = getNextval("projetos", "acompanhamento_escopos", "cd_acompanhamento_escopos", $db); // PEGA NEXTVAL DA SEQUENCE DO CAMPO
			if ($cd_escopo_novo > 0) // TESTA SE RETORNOU ALGUM VALOR
			{
				$qr_sql = "
						INSERT INTO projetos.acompanhamento_escopos
								  (
									cd_acompanhamento_escopos,
									cd_acomp,
									ds_objetivos,
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
									".$cd_escopo_novo.",
									".$_POST['cd_acomp'].", 
									'".$_POST['ds_objetivo']."', 
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
			if($cd_escopo_novo > 0)
			{
				$_REQUEST['cd_escopo'] = $cd_escopo_novo;
				echo "	<script>			
							opener.location.reload(true);
						</script>";				
			}
			else
			{
				$_REQUEST['cd_escopo'] = $_POST['cd_escopo'];
			}
			
			$_REQUEST['cd_acomp']  = $_POST['cd_acomp'];
		}		
	}	     

	$fl_imprimir = "display:none;";
	if ($_REQUEST['cd_escopo'] != '') 
	{
		$qr_select = "
						SELECT cd_acompanhamento_escopos, 
							   ' - ' || TO_CHAR(dt_cadastro,'DD/MM/YYYY') AS dt_cadastro, 
							   ds_objetivos,
							   ds_regras,
							   ds_impacto,
							   ds_responsaveis,
							   ds_solucao,
							   ds_recurso,
							   ds_viabilidade,
							   ds_modelagem,
							   ds_produtos
						  FROM projetos.acompanhamento_escopos 
						 WHERE cd_acompanhamento_escopos = ".$_REQUEST['cd_escopo']."
						   AND cd_acomp                  = ".$_REQUEST['cd_acomp']." 
					 ";
		$ob_resul  = pg_query($db, $qr_select);
		$ar_select = pg_fetch_array($ob_resul);
		pg_close($db);
		$fl_imprimir = "";
	}
?>
<html>
<head>
	<title>...:: Registro do Escopo ::...</title>
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
	</style>
	<script language="JavaScript">
		var ob_window = "";
		
		function trimValue(str)
		{
		    while (str.charAt(0) == " ")
		    {
		        str = str.substr(1,str.length -1);
		    }

		    while (str.charAt(str.length-1) == " ")
		    {
		        str = str.substr(0,str.length-1);
		    }
		    str = str.replace(/\r|\n|\r\n/g,"");
		    return str;
		}
		
		function validForm()
		{
			var ds_msg_erro = "";
			
			if(trimValue(document.getElementById("ds_objetivo").value) == "")
			{
				ds_msg_erro += "\n- Informe o Objetivo";
			}

			if(trimValue(document.getElementById("ds_regras").value) == "")
			{
				ds_msg_erro += "\n- Informe as Regras do Neg�cio/Funcionalidades";
			}

			if(trimValue(document.getElementById("ds_impacto").value) == "")
			{
				ds_msg_erro += "\n- Informe o Impacto";
			}	

			if(trimValue(document.getElementById("ds_responsaveis").value) == "")
			{
				ds_msg_erro += "\n- Informe os Respons�veis";
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
				alert("Os seguinte itens s�o necess�rios:\n" + ds_msg_erro)
			}
			else
			{		
				document.getElementById("ob_form").submit();
			}
		}
		
		function imprimirEscopo(cd_acomp, cd_escopo) 
		{
		    if(ob_window != "")
			{
				ob_window.close();
			}

			var ds_url = "registro_escopo_projeto_rel.php";
				ds_url += "?cd_acomp="  + cd_acomp;
				ds_url += "&cd_escopo=" + cd_escopo;
			
			var nr_width = document.body.clientWidth - 50;
			var nr_height = document.body.clientHeight - 50;
			var nr_left = ((screen.width - 10) - nr_width) / 2;
			var nr_top = ((screen.height - 80) - nr_height) / 2;

			ob_window = window.open(ds_url, "wEscopoRel", "left="+nr_left+",top="+nr_top+",width="+nr_width+",height="+nr_height+",scrollbars=yes,resizable=yes,directories=no,location=no,menubar=yes,status=no,titlebar=no,toolbar=yes");		 		
		}		
	</script>
</head>
<body>
	<form name="ob_form" id="ob_form" action="" method="post" enctype="multipart/form-data">
		<?
			echo "<input type='hidden' name='cd_acomp'  value='".$_REQUEST['cd_acomp']."'>";
			echo "<input type='hidden' name='cd_escopo' value='".$_REQUEST['cd_escopo']."'>";
		?>
		<fieldset>
			<legend>Registro de Escopo <? echo $ar_select['dt_cadastro']; ?></legend>
			
			<div class="css_botao">
				<img src="img/salvar_p.gif"        onclick="validForm();" style="cursor:pointer;" border="0" title="Salvar registro de reuni�o">
				<img src="img/registro_operacional_imp_p.gif" border="0" onClick="imprimirEscopo('<? echo $_REQUEST['cd_acomp']; ?>','<? echo $_REQUEST['cd_escopo']; ?>')" style="cursor:pointer; <? echo $fl_imprimir; ?>" title="Imprimir Escopo">
				<img src="img/fechar_janela_p.gif" onClick="window.close();" style="cursor:pointer;" border="0" title="Fechar janela">								
			</div>
			
			<label for="ds_objetivo">1) Objetivo</label>
			<br>
			<textarea name="ds_objetivo" id="ds_objetivo"><? echo $ar_select['ds_objetivos']; ?></textarea>
			<br>
			<span>Descrever o que se deseja alcan�ar com a implementa��o do projeto. A identifica��o do objetivo do projeto acontece na fase de an�lise de requisitos, durante a avalia��o do que deve ser feito e o porque.</span>
			<br>
			<br>
			
			<label for="ds_regras">2) Regras de Neg�cio/Funcionalidas</label>
			<br>
			<textarea name="ds_regras" id="ds_regras"><? echo $ar_select['ds_regras']; ?></textarea>
			<br>
			<span>Descrever as regras de neg�cio necess�rias para o desenvolvimento do projeto. Estas regras s�o identificadas durante a defini��o/ revis�o do processo de neg�cio. Defini��o das funcionalidades do projeto, conforme as reuni�es com os respons�veis pelo projeto.</span>
			<br>
			<br>
			
			<label for="ds_impacto">3) Impacto</label>
			<br>
			<textarea name="ds_impacto" id="ds_impacto"><? echo $ar_select['ds_impacto']; ?></textarea>
			<br>
			<span>Descrever a avalia��o realizada sobre o impacto que este projeto causa nos demais processos e sistemas, tais como integra��es e mudan�as.</span>
			<br>
			<br>

			<label for="ds_responsaveis">4) Respons�veis</label>
			<br>
			<textarea name="ds_responsaveis" id="ds_responsaveis"><? echo $ar_select['ds_responsaveis']; ?></textarea>
			<br>
			<span>Apontar os respons�veis pelos processos envolvidos no projeto. Estas pessoas dever�o estar envolvidas na defini��o, execu��o e testes do projeto.  Dever� existir tamb�m um ou mais respons�veis pela aprova��o do pr�-escopo (conforme acordo entre analista e envolvidos).</span>
			<br>
			<br>
			
			<label for="ds_solucao">5) Solu��o Imediata (opcional)</label>
			<br>
			<textarea name="ds_solucao" id="ds_solucao"><? echo $ar_select['ds_solucao']; ?></textarea>
			<br>
			<span>Descrever a solu��o imediata e tempor�ria de como ser� realizado o processo at� que o projeto seja implementado. Esta solu��o imediata � utilizada quando n�o � poss�vel esperar a conclus�o do projeto para iniciar o processo, sendo definido um fluxo alternativo e imediatamente vi�vel em comum acordo entre o analista de neg�cios/sistemas e o respons�vel pelo processo.</span>
			<br>
			<br>			
			
			<label for="ds_recurso">6) Recurso/Custo</label>
			<br>
			<textarea name="ds_recurso" id="ds_recurso"><? echo $ar_select['ds_recurso']; ?></textarea>
			<br>
			<span>Descri��o do levantamento de recursos e/ou custos a serem utilizados no projeto. A avalia��o s� ser� realizada quando for necess�ria a utiliza��o recursos externos para a realiza��o do projeto.</span>
			<br>
			<br>

			<label for="ds_viabilidade">7) Viabilidade/Sugest�o (opcional)</label>
			<br>
			<textarea name="ds_viabilidade" id="ds_viabilidade"><? echo $ar_select['ds_viabilidade']; ?></textarea>
			<br>
			<span>A avalia��o de viabilidade sempre � realizada durante a an�lise de requisitos, mas este item somente ser� descrito neste documento se for identificado pelo analista de neg�cios/ sistemas que � invi�vel a implementa��o do projeto solicitado. Neste item pode ser descrita uma sugest�o alternativa de como esta solicita��o pode ser atendida.</span>
			<br>
			<br>

			<label for="ds_modelagem">8) Modelagem de Dados</label>
			<br>
			<textarea name="ds_modelagem" id="ds_modelagem"><? echo $ar_select['ds_modelagem']; ?></textarea>
			<br>
			<span>Refer�ncia ao ER no caso de tabelas novas e no caso de manuten��es nas tabelas deve ser descrito as altera��es realizadas.</span>
			<br>
			<br>

			<label for="ds_produtos">9) Produtos</label>
			<br>
			<textarea name="ds_produtos" id="ds_produtos"><? echo $ar_select['ds_produtos']; ?></textarea>
			<br>
			<span>Referenciar o nome da WBS e nome dos formul�rios dos produtos.</span>
			<br>
			<br>			
		</fieldset>
	</form>
</body>
</html>