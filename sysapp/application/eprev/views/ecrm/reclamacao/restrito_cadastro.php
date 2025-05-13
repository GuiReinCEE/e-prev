<?php
	session_start("FAMILIA_RESTRITO");
	require_once("inc/conexao.php");

	$_FL_SEPRORGS = ((intval($_REQUEST['c']) == 0) AND (intval($_REQUEST['cd_seprorgs']) > 0) ? TRUE : FALSE);
	
	// acesso especial com nome de usuário da rede criptografado
	if($_REQUEST["u"]!='')
	{
		$sql = "
					SELECT cd_usuario, 
					       usuario,
					       nome, 
						   tp_usuario, 
						   fl_troca_senha 
				      FROM familia_previdencia.usuario 
			         WHERE '".pg_escape_string($_REQUEST["u"])."' IN (MD5(UPPER(usuario)), MD5(LOWER(usuario)))
		        ";

		$query=pg_query($db, $sql);
		$rows=pg_fetch_array($query);
		
		if($rows)
		{
			$_SESSION['F_ID_SESSAO'] = MD5('FAMILIA_RESTRITO_' . intval($rows['cd_usuario']));
			$_SESSION['F_CD_USUARIO'] = intval($rows['cd_usuario']);
			$_SESSION['F_NOME']       = trim($rows['nome']);
			$_SESSION['F_USUARIO']    = trim($rows['usuario']);
			$_SESSION['F_TP_USUARIO'] = trim($rows['tp_usuario']);
			$_SESSION['F_FL_TROCA_SENHA'] = trim($rows['fl_troca_senha']);
		}
		else
		{
			echo 'Login inválido para acesso especial.';
			exit;
		}
	}

	require_once("inc/sessao.php");

	// ACESSO ESPECIAL com autenticação pela URL para acesso pelo GAP Atendimento
	
	// código enviado no parametro C que já era usado
	$codigo = ($_REQUEST['c']);
	
	// flag que indica acesso especial
	$acesso_especial = ($_REQUEST["key"]==md5("Ac3ss0_p3l@_G@P"));
	
	// nome de usuário da rede criptografado
	$usuario_acesso_especial = $_REQUEST["u"];
	
    
	#restrito_cadastro.php?c=FCa89a9ee06de2f2052fc82f7bee074821&key=8aae7a4c3f65a54929bd4c29f61456d1&u=9bebca690314c7c0b80ef5cfb155534a
	#restrito_cadastro.php?c=FCbbe456e5f89c2a467eda66eba4ae33f5&key=8aae7a4c3f65a54929bd4c29f61456d1&u=9bebca690314c7c0b80ef5cfb155534a

	
	// busca cadastro de INTERESSE já existente para RE enviado pela url ( $codigo contém o RE informado em $_request['c'] )
	if($acesso_especial)
	{
		
		
		$qr_sql = "
					SELECT MD5(CAST(c.cd_cadastro AS TEXT)) AS cd_cadastro_md5, *
					  FROM familia_previdencia.cadastro c 
					 WHERE dt_exclusao IS NULL 
					   AND ((c.cd_origem = '".$_REQUEST['c']."' AND c.tp_origem = 3)
							OR
							('FC' || funcoes.cripto_re(cd_empresa, cd_registro_empregado, seq_dependencia ) = '".$_REQUEST['c']."'))
					  ORDER BY c.dt_alteracao DESC
				   ";
		#echo "<PRE>".$qr_sql."</PRE>";		
		$ob_resul = pg_query($db, $qr_sql);
		if(pg_num_rows($ob_resul) > 0)
		{
			$ar_reg = pg_fetch_array($ob_resul);
			$_REQUEST['c'] = $ar_reg['cd_cadastro_md5'];
		}
		else
		{
			// continua normalmente a inclusão de interesse, modificando a tela 
			// para exibir apenas os campos relevantes ao atendimento

			#echo 'não encontrado';
			$_REQUEST['ORIG'] = 'FC';
		}

		// echo 'acesso especial';
	}
	else
	{
		// echo 'acesso normal';
		// carrega o formulário com todos os campos visíveis
	}
	#exit;
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd"> 
<html>
<head>
<title>[Corporativa - Cadastro] Família Previdência</title>
	<?php
		include("meta.php");
	?>

	<style>
		#form_cadastro_tabela td {
			font: 11px tahoma,verdana,helvetica;		
		}

	
		.sort-table input{
			border: 1px solid #000000;
			font-family: Verdana, Arial;
			font-size: 8pt;
		}
		
		.sort-table select{
			border: 1px solid #000000;
			font-family: Verdana, Arial;
			font-size: 8pt;
		}

		.sort-table textarea{
			border: 1px solid #000000;
			font-family: Verdana, Arial;
			font-size: 8pt;
		}	

		.obRepresentanteLegal {
			display: none;
		}

		.obAutorizacaoDebitoConta {
			display: none;
		}
		
		.obAutorizacaoFolhaDePagamento {
			display: none;
		}		
	</style>
	
	<script>
		function setResponsavelLegal()
		{
			$(".obRepresentanteLegal").hide();
			
			var dt_nascimento = jQuery.trim($("#dt_nascimento").val());
			
			if(jQuery.trim($("#dt_nascimento").val()) != "")
			{
				var anos = moment().diff(moment(dt_nascimento,"DD/MM/YYYY"), 'years',false); //alert(anos);
				
				if(anos < 18)
				{
					$(".obRepresentanteLegal").show();
				}
			}
			
			/*
				https://momentjs.com/			
				https://qastack.com.br/programming/14057497/moment-js-how-do-i-get-the-number-of-years-since-a-date-not-rounded-up			
				se você não deseja valores de fração:

				var years = moment().diff('1981-01-01', 'years',false);
				alert( years);
				se você deseja valores de fração:

				var years = moment().diff('1981-01-01', 'years',true);
				alert( years);		

				Math.floor(moment(new Date()).diff(moment("02/26/1978","MM/DD/YYYY"),'years',true)))	
			*/
		}
	
		function setFormaPagamento()
		{
			$(".obAutorizacaoDebitoConta").hide();
			$(".obAutorizacaoFolhaDePagamento").hide();
			
			if(($("#tp_forma_pagamento_primeira").val() == "DCC") || ($("#tp_forma_pagamento_mensal").val() == "DCC") || ($("#tp_forma_pagamento_extra_inicial").val() == "DCC"))
			{
				$(".obAutorizacaoDebitoConta").show();
			}
			
			if(($("#tp_forma_pagamento_primeira").val() == "FOL") || ($("#tp_forma_pagamento_mensal").val() == "FOL") || ($("#tp_forma_pagamento_extra_inicial").val() == "FOL"))
			{
				$(".obAutorizacaoFolhaDePagamento").show();
			}			
		}
	
		function setCadastroFundacao(cpf,cd_empresa,cd_registro_empregado,seq_dependencia,telefone_1,telefone_2,email_1,email_2)
		{
			$('#cpf').val(cpf);
			$('#cd_empresa').val(cd_empresa);
			$('#cd_registro_empregado').val(cd_registro_empregado);
			$('#seq_dependencia').val(seq_dependencia);
			$('#telefone').val(telefone_1);
			$('#celular').val(telefone_2);
			
			$('#email').val((email_1 != "" ? email_1 : email_2));
			
			listaDependenteParticipante();
			
			$('html, body').animate({ scrollTop: $("#cpf").offset().top }, 2000);			
		}
	
		function getParticipanteNome()
		{
			if(($('#cpf').val() == '000.000.000-00') || ($('#cpf').val() == ''))
			{
				$("#ob_cadastro_fundacao_item").html('Sem CPF.');
			}
			else
			{
				$.post('restrito_cadastro_ajax.php',
				{
					funcao: 'getParticipanteNome',
					nome: $('#nome').val(),
					cpf: $('#cpf').val()
				},
				function(data)
				{
					$("#ob_cadastro_fundacao_item").html(data);
					getInfoParticipante();
				});
			}
		}	
	
		function janelaDependenteFecha()
		{
			$.modal.close();
		}
		
		function janelaDependente()
		{
			$('#adicionaDependente').modal({
				focus:false,
				containerCss:{
					width:600,
					height:250
					},
				onClose: function (dialog) {
					$.modal.close();
				}					
			});
		}
		
		function adicionaDependente()
		{
			$('#iframeDependente').attr("src","restrito_cadastro_familiar.php?c=" + $('#cd_cadastro').val() + "&d=0");
			janelaDependente();
		}	
	
		function editaDependente(cd_dependente)
		{
			location.href = "restrito_dependente.php?c=" + $("#cd_cadastro_md5").val() + "&d=" + cd_dependente;
		}	
		
		function excluiDependente(cd_dependente)
		{
			if(confirm("ATENÇÃO\n\nDeseja excluir o Familiar?\n\n[OK] para Sim\n[Cancelar] para Não\n\n"))
			{
				$.post('restrito_cadastro_ajax.php',
				{
					funcao: 'excluiDependente',
					cd_dependente: cd_dependente
				},
				function(data)
				{
					listaDependente();
					listaAlteracao();
				});	
			}
		}
		
		function listaAlteracao()
		{
			$.post('restrito_cadastro_ajax.php',
			{
				funcao: 'listaAlteracao',
				cd_cadastro: $('#cd_cadastro').val()
			},
			function(data)
			{
				$("#obAlteracao").html(data);
				listaAlteracaoOrdena();
			});
		}		
		
		function listaAlteracaoOrdena()
		{
			var ob_resul = new SortableTable(document.getElementById("tbAlteracao"),
							[
								"DateTimeBR",
								"CaseInsensitiveString",
								"CaseInsensitiveString",
								"CaseInsensitiveString"
							]);			
			ob_resul.onsort = function () {
					var rows = ob_resul.tBody.rows;
					var l = rows.length;
					for (var i = 0; i < l; i++) {
						removeClassName(rows[i], i % 2 ? "sort-par" : "sort-impar");
						addClassName(rows[i], i % 2 ? "sort-impar" : "sort-par");
					}
				};
			ob_resul.sort(0, true);		
		}		

		function listaDependente()
		{
			$.post('restrito_cadastro_ajax.php',
			{
				funcao: 'listaDependente',
				cd_cadastro: $('#cd_cadastro').val()
			},
			function(data)
			{
				$("#obDependente").html(data);
				listaDepententeOrdena();
			});
		}		
		
		function listaDepententeOrdena()
		{
			var ob_resul = new SortableTable(document.getElementById("tbDependente"),
							[
								"CaseInsensitiveString",
								"DateBR",
								"CaseInsensitiveString",
								"CaseInsensitiveString",
								"CaseInsensitiveString"
							]);			
			ob_resul.onsort = function () {
					var rows = ob_resul.tBody.rows;
					var l = rows.length;
					for (var i = 0; i < l; i++) {
						removeClassName(rows[i], i % 2 ? "sort-par" : "sort-impar");
						addClassName(rows[i], i % 2 ? "sort-impar" : "sort-par");
					}
				};
			ob_resul.sort(0, false);		
		}
	
		function listaDependenteParticipante()
		{
			$("#obDependenteParticipante").html("");
			$.post('restrito_cadastro_ajax.php',
			{
				funcao                : 'getParticipanteDependente',
				cd_empresa            : $('#cd_empresa').val(),
				cd_registro_empregado : $('#cd_registro_empregado').val()
			},
			function(data)
			{
				$("#obDependenteParticipante").html(data);
			});
		}	
	
		function validaForm()
		{
			if(jQuery.trim($('#cd_cadastro_situacao').val()) == "")
			{
				alert("Informe a Situação");
				$('#cd_cadastro_situacao').focus();
				return false;
			}
			else if(jQuery.trim($('#cd_instituidor').val()) == "")
			{
				alert("Informe o Instituidor a qual a pessoa está vinculada");
				$('#cd_instituidor').focus();
				return false;
			}
			else if(jQuery.trim($('#nome').val()) == "")
			{
				alert("Informe o Nome");
				$('#nome').focus();
				return false;
			}		
			else if(jQuery.trim($('#fl_associado').val()) == "")
			{
				//alert("Informe se é Sócio da AFCEEE");
				//$('#fl_associado').focus();
				//return false;
			}	
			else if(jQuery.trim($('#fl_participante').val()) == "")
			{
				//alert("Informe se é Participante da Fundação CEEE");
				//$('#fl_participante').focus();
				//return false;
			}	
			else if(jQuery.trim($('#fl_participante').val()) == "")
			{
				alert("Informe se é Participante da Fundação CEEE");
				$('#fl_participante').focus();
				return false;
			}	
			else if(jQuery.trim($('#fl_interesse_familiar').val()) == "")
			{
				//alert("Informe se há Interesse em fazer o plano para familiares");
				//$('#fl_interesse_familiar').focus();
				//return false;
			}				
			else if(jQuery.trim($('#cd_contato_forma').val()) == "")
			{
				alert("Informe a Melhor forma de contato");
				$('#cd_contato_forma').focus();
				return false;
			}	
			else if(jQuery.trim($('#fl_receber_info').val()) == "")
			{
				alert("Informe se Deseja receber informações do Plano");
				$('#fl_receber_info').focus();
				return false;
			}
			else if((jQuery.trim($('#fl_receber_info').val()) == "S") && (jQuery.trim($('#cd_contato_tipo').val()) == ""))
			{
				alert("Informe Como");
				$('#cd_contato_tipo').focus();
				return false;
			}			
			else
			{
				return true;
			}
		}
		
		function setReceberInfo()
		{
			if(jQuery.trim($('#fl_receber_info').val()) == "S")
			{
				$("#como").show();
				$('#cd_contato_tipo').val("");
				$('#cd_contato_tipo').focus();
			}
			else
			{
				$("#como").hide();
				$('#cd_contato_tipo').val("");
			}
		}
		
		function acesso_especial()
		{
			$("#tr_situacao").show();
			$("#tr_nome").show();
			$("#tr_nascimento").show();
			$("#tr_socio").show();
			$("#tr_delegacia").hide();
			$("#tr_participante").hide();
			$("#tr_empresa").show();
			$("#tr_re").show();
			$("#tr_sequencia").show();
			$("#tr_familiares").hide();
			$("#tr_telefone_1").show();
			$("#tr_telefone_2").show();
			$("#tr_endereco").hide();
			$("#tr_complemento").hide();
			$("#tr_bairro").hide();
			$("#tr_cep").show();
			$("#tr_cidade").show();
			$("#tr_uf").show();
			//$("#tr_estado_civil").hide();
			$("#tr_email").show();
			//$("#tr_contato").hide();
			//$("#tr_informacoes").hide();
			//$("#como").hide();
			$("#tr_observacoes").show();
			$("#tr_adicionar_familiar").hide();
		}

		function excluirCadastro()
		{
			var mensagem ="ATENÇÃO\n\nDeseja EXCLUIR o cadastro?\n\n\nSIM clique [Ok]\n\nNÃO clique [Cancelar]\n\n";
			
			if(confirm(mensagem))
			{
				location.href = "restrito_del_grava.php?c=" + $("#cd_cadastro_md5").val();
			}
		}

		function atualizarDados()
		{
			location.href = "restrito_cadastro_atualiza_dados.php?c=" + $("#cd_cadastro_md5").val();
		}
		
		function getInfoParticipante()
		{
			if($('#cd_registro_empregado').val() > 0)
			{
				$.post('restrito_cadastro_ajax.php',
				{
					funcao               : 'getInfoParticipante',
					cd_empresa           : $('#cd_empresa').val(),
					cd_registro_empregado: $('#cd_registro_empregado').val(),
					seq_dependencia      : $('#seq_dependencia').val()
				},
				function(data)
				{
					$("#ob_info_participante").html(data);
				});
			}
			else
			{
				$("#ob_info_participante").html("");
			}
		}

		function getCampanha()
		{
			$.post('restrito_cadastro_ajax.php',
			{
				funcao : 'getCampanha',
				cpf    : $('#cpf').val()
			},
			function(data)
			{
				$("#ob_info_campanha").html(data);
			});
		}	

		function getCadastroInstituidor()
		{
			$.post('restrito_cadastro_ajax.php',
			{
				funcao : 'getCadastroInstituidor',
				cpf    : $('#cpf').val()
			},
			function(data)
			{
				$("#ob_info_cadastro_instituidor").html(data);
			});
		}	
		
		function imprimirFormularioDependente(cd_dependente_md5)
		{
			if(cd_dependente_md5 != "")
			{
				window.open("https://www.fcprev.com.br/srvfamiliavendas/index.php/solicitacao/formulario_inscricao_area_corporativa/"+cd_dependente_md5+"/D");
			}
			else
			{
				alert("Cadastro não identificado");
			}
		}		
		
		function imprimirFormularioPreenchido()
		{
			if(($("#cd_cadastro_md5").val() != "") && ($("#tp_cadastro").val() == 1))
			{
				window.open("https://www.fcprev.com.br/srvfamiliavendas/index.php/solicitacao/formulario_inscricao_area_corporativa/"+$("#cd_cadastro_md5").val()+"/C");
			}
			else
			{
				alert("Cadastro não identificado");
			}
		}		

		function imprimirFormularioBranco()
		{
			window.open("https://www.fcprev.com.br/srvfamiliavendas/index.php/solicitacao/formulario_inscricao_area_corporativa/0/C");
		}	
	
		function verProtocoloAssinatura()
		{
			window.open("https://www.fcprev.com.br/fundacaofamilia/index.php/assinatura_documento/index/"+$("#id_doc_assinatura").val());
		}
		
		function enviarAssinatura()
		{
			var confirmacao = 'ATENÇÃO\n\nCONFIRA E SALVE OS DADOS DO FORMULÁRIO ANTES DE DE ENVIAR\n\nDeseja enviar para assinatura?\n\n'+
				'Clique [Ok] para Sim\n'+
				'Clique [Cancelar] para Não\n';

			if(confirm(confirmacao))
			{ 
				$('#btEnviarAssinatura').attr("disabled", true);
				
				$('#formCadastro').attr('action', 'restrito_assinar_formulario.php');
				$('#formCadastro').submit();
			}
		}

		function enviarProtocoloInterno()
		{
			var confirmacao = 'Deseja enviar para o protocolo interno?\n\n'+
				'Clique [Ok] para Sim\n'+
				'Clique [Cancelar] para Não\n';

			if(confirm(confirmacao))
			{ 
				$('#btEnviarAssinatura').attr("disabled", true);
				
				$('#formCadastro').attr('action', 'restrito_cadastro_protocolo_interno.php');
				$('#formCadastro').submit();
			}
		}	

		function getAssinaturaStatus()
		{
			$("#status_doc_assinatura").html("");
			
			if($('#id_doc_assinatura').val() != "")
			{
				$("#status_doc_assinatura").html("consultando status, aguarde...");
				$.post('restrito_cadastro_ajax.php',
				{
					funcao            : 'getAssinaturaStatus',
					id_doc_assinatura : $('#id_doc_assinatura').val()
				},
				function(data)
				{
					$("#status_doc_assinatura").html(data.descricao);

					if(data.status == 'CLOSED' && <?= intval($AR_CADASTRO['cd_documento_recebido']) ?> > 0)
					{
						$("#tbEnviarProtocoloInterno").show();
					}
					else
					{
						$("#tbEnviarProtocoloInterno").hide();
					}
				}, 'json');
			}
		}	
	</script>
</head>
<?php
		$tp_cadastro = 0;
		
		if($_REQUEST['ORIG'] == "AF") #### CADASTRO AFCEEE ####
		{
			$cd_instituidor = 19;

			$qr_sql = "
						SELECT re,
							   emp, 
							   nome,
							   TO_CHAR(dt_nascimento::DATE,'DD/MM/YYYY') AS dt_nascimento,
							   endereco, 
							   bairro, 
							   cidade, 
							   cep, 
							   uf, 
							   telefone, 
							   telefone_2 AS celular,
							   email_1,
							   email_2,
							   delegacia,
							   'S' AS fl_associado,
							   'N' AS fl_inscrito,
							   cd_cadastro_afceee,
							   cpf,
							   cd_empresa,
							   cd_registro_empregado,
							   seq_dependencia 
						  FROM familia_previdencia.afceee_cadastro
						 WHERE MD5(CAST(cd_cadastro_afceee AS TEXT)) = '".(trim($_REQUEST['c']))."'
					  ";	
			$ob_resul = pg_query($db, $qr_sql);	
			
			if(pg_num_rows($ob_resul) != 0)
			{
				$tp_cadastro = 2;
			}
		}
		elseif($_REQUEST['ORIG'] == "ST") #### CADASTRO SINTEC ####
		{
			$cd_instituidor = 20;

			$qr_sql = "
						SELECT codigo,
							   nome,
							   TO_CHAR(dt_nascimento::DATE,'DD/MM/YYYY') AS dt_nascimento,
							   endereco, 
							   bairro, 
							   cidade, 
							   cep, 
							   uf, 
							   telefone, 
							   telefone_2 AS celular,
							   email_1,
							   email_2,
							   'S' AS fl_associado,
							   'N' AS fl_inscrito,
							   cd_sintec_cadastro,
							   cpf,
							   cd_empresa,
							   cd_registro_empregado,
							   seq_dependencia 
						  FROM familia_previdencia.sintec_cadastro
						 WHERE 'ST' || MD5(CAST(cd_sintec_cadastro AS TEXT)) = '".(trim($_REQUEST['c']))."'
					  ";	
			$ob_resul = pg_query($db, $qr_sql);		
			if(pg_num_rows($ob_resul) != 0)
			{
				$tp_cadastro = 4;
			}				
		}
		elseif($_REQUEST['ORIG'] == "PMSM") #### CADASTRO PM SANTA MARIA ####
		{
			$cd_instituidor = 29;

			$qr_sql = "
						SELECT cd_pm_santa_maria_cadastro AS codigo,
							   nome,
							   dt_nascimento,
							   endereco, 
							   bairro, 
							   cidade, 
							   cep, 
							   uf, 
							   telefone_1 AS telefone, 
							   telefone_2 AS celular,
							   email_1,
							   email_2,
							   'S' AS fl_associado,
							   'N' AS fl_inscrito,
							   cd_pm_santa_maria_cadastro,
							   cpf,
							   -1 AS cd_empresa,
							   NULL AS cd_registro_empregado,
							   NULL AS seq_dependencia,
							   observacao AS observacoes
						  FROM familia_previdencia.pm_santa_maria_cadastro
						 WHERE 'PMSM' || MD5(CAST(cd_pm_santa_maria_cadastro AS TEXT)) = '".(trim($_REQUEST['c']))."'
					  ";	
			$ob_resul = pg_query($db, $qr_sql);		
			if(pg_num_rows($ob_resul) != 0)
			{
				$tp_cadastro = 111;
			}				
		}		
		elseif($_REQUEST['ORIG'] == "FC") #### CADASTRO PARTICIPANTES FUNDACAO CEEE ####
		{	
			$cd_instituidor = 24;

			$qr_sql = "
						SELECT p.cd_empresa,
							   p.cd_registro_empregado,
							   p.seq_dependencia,
							   p.nome,
							   TO_CHAR(p.dt_nascimento,'DD/MM/YYYY') AS dt_nascimento,
							   COALESCE(p.email,'') AS email_1,
							   COALESCE(p.email_profissional,'') AS email_2,
							   p.logradouro AS endereco,
							   p.bairro,
							   TO_CHAR(p.cep,'FM00000') || '-' || TO_CHAR(p.complemento_cep,'FM000') AS cep,
							   p.cidade,
							   p.unidade_federativa AS uf,
							   '('|| TO_CHAR(COALESCE(p.ddd,0),'FM00') || ') ' || p.telefone AS telefone,
							   '('|| TO_CHAR(COALESCE(p.ddd_celular,0),'FM00') || ') ' || p.celular AS celular,
							   funcoes.format_cpf(p.cpf_mf) AS cpf,
							   'N' AS fl_associado,
							   'N' AS fl_inscrito,
							   f.nome_pai AS ds_nome_pai, 
							   f.nome_mae AS ds_nome_mae, 
							   f.naturalidade AS ds_naturalidade, 
							   f.nacionalidade AS ds_nacionalidade,
                               (SELECT d.nro_documento
								  FROM public.documentos d
								 WHERE d.cd_empresa            = p.cd_empresa
								   AND d.cd_registro_empregado = p.cd_registro_empregado
								   AND d.seq_dependencia       = p.seq_dependencia 
								   AND d.cd_tipo_doc           = 1
								   AND d.nro_documento         IS NOT NULL
								 ORDER BY d.dt_alteracao DESC
								 LIMIT 1) AS ds_rg,
							   '' AS fl_ppe, 
							   '' AS fl_usperson,
							   '' AS fl_pessoa_associada_ffp			 
						  FROM public.participantes p
						  LEFT JOIN public.filiacoes f
						    ON f.cd_empresa            = p.cd_empresa           
						   AND f.cd_registro_empregado = p.cd_registro_empregado 
						   AND f.seq_dependencia       = p.seq_dependencia   
						 WHERE 'FC' || funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) = '".(trim($_REQUEST['c']))."'
					  ";
			$ob_resul = pg_query($db, $qr_sql);	
			if(pg_num_rows($ob_resul) > 0)
			{
				$tp_cadastro = 3;
			}
		}		
		elseif(($_REQUEST['ORIG'] == "SP") AND ($_FL_SEPRORGS)) #### CADASTRO DE EMPRESAS SEPRORGS ####
		{
			$cd_instituidor = 25;

			$qr_sql = "
						SELECT 25 AS cd_empresa,
							   NULL AS cd_registro_empregado,
							   NULL AS seq_dependencia,
							   NULL AS nome,
							   NULL AS dt_nascimento,
							   NULL AS email_1,
							   NULL AS email_2,
							   NULL AS endereco,
							   NULL AS bairro,
							   NULL AS  cep,
							   NULL AS cidade,
							   NULL AS uf,
							   NULL AS telefone,
							   NULL AS celular,
							   NULL AS cpf,
							   'N' AS fl_associado,
							   'N' AS fl_inscrito,
							   ".intval($_REQUEST['cd_seprorgs'])." AS cd_seprorgs
					  ";	
			$ob_resul = pg_query($db, $qr_sql);	
			$tp_cadastro = 5;
			
			$_REQUEST['c'] = intval($_REQUEST['cd_seprorgs']);
		}
		elseif($_REQUEST['ORIG'] == "AP") #### CADASTRO APLUB ####
		{
			$cd_instituidor = 19;

			$qr_sql = "
						SELECT nome,
							   TO_CHAR(dt_nascimento::DATE,'DD/MM/YYYY') AS dt_nascimento,
							   endereco, 
							   complemento,
							   bairro, 
							   cidade, 
							   cep, 
							   uf, 
							   telefone_1 AS celular,
							   telefone_2 AS telefone, 
							   telefone_3,
							   telefone_4,
							   email_1,
							   NULL AS email_2,
							   'N' AS fl_associado,
							   'N' AS fl_inscrito,
							   cd_aplub_cadastro,
							   cpf,
							   -1 AS cd_empresa,
							   NULL AS cd_registro_empregado,
							   NULL AS seq_dependencia,
							   'APLUB - ' || tp_aplub || CHR(13)|| CHR(10) || observacao AS observacoes
						  FROM familia_previdencia.aplub_cadastro
						 WHERE 'AP' || MD5(CAST(cd_aplub_cadastro AS TEXT)) = '".(trim($_REQUEST['c']))."'
					  ";	
			$ob_resul = pg_query($db, $qr_sql);		
			if(pg_num_rows($ob_resul) != 0)
			{
				$tp_cadastro = 6;
			}				
		}		
		else
		{	
			#### BUSCA NO CADASTRO ####
			$qr_sql = "
						SELECT  c.cd_cadastro, 
						        c.id_doc_assinatura,
								c.nome, 
								TO_CHAR(c.dt_nascimento,'DD/MM/YYYY') AS dt_nascimento,
								c.endereco, 
								c.complemento,
								c.bairro,
								c.cep, 
								c.cidade, 
								c.uf, 
								c.telefone,
								c.celular, 
								c.email AS email_1, 
								c.email_2, 
								c.dt_inclusao, 
								c.cd_usuario_inclusao, 
								c.dt_exclusao, 
								c.cd_usuario_exclusao, 
								c.dt_alteracao, 
								c.cd_usuario_alteracao,
								c.cd_empresa, 
								c.cd_registro_empregado, 
								c.seq_dependencia, 
								c.fl_associado,
								c.fl_participante,
								c.fl_interesse_familiar,
								c.fl_receber_info,
								c.observacoes,
								c.cd_cadastro_situacao,
								c.cd_estado_civil,
								c.cd_contato_forma, 
								c.cd_contato_tipo,
								c.delegacia,
								c.cpf,
								c.fl_inscrito,
								c.tp_origem,
								c.cd_origem,
								c.cd_instituidor,
								COALESCE(c.fl_indicacao_interna, 'NÃO') AS fl_indicacao_interna,
							   
								c.tp_sexo, 
								c.fl_ppe, 
								c.fl_usperson, 
								c.fl_pessoa_associada_ffp,
								c.fl_tributacao, 
								c.ds_nome_pai, 
								c.ds_nome_mae, 
								c.ds_naturalidade, 
								c.ds_nacionalidade, 
								c.ds_rg, 
								c.ds_orgao_expedidor, 
								TO_CHAR(c.dt_expedicao, 'DD/MM/YYYY') AS dt_expedicao,

								c.ds_associado, 
								c.ds_vinculo_associado, 
								c.ds_vinculo_grau, 

								c.tp_forma_pagamento_primeira, 
								c.nr_contrib_primeira, 
								c.tp_forma_pagamento_mensal, 
								c.nr_contrib_mensal, 
								c.tp_forma_pagamento_extra_inicial, 
								c.nr_contrib_extra_inicial, 

								c.vendedor_nome, 
								c.vendedor_celular, 
								c.vendedor_email,
								TO_CHAR(c.dt_recebimento, 'DD/MM/YYYY') AS dt_recebimento, 

								c.representante_legal_nome, 
								c.representante_legal_cpf, 
								c.representante_legal_celular, 
								c.representante_legal_email, 

								c.debito_conta_banco, 
								c.debito_conta_agencia, 
								c.debito_conta_nr_conta, 
								c.debito_conta_nome, 
								c.debito_conta_cpf, 
								c.debito_conta_celular, 
								c.debito_conta_email, 

								c.folha_pagamento_re, 
								c.folha_pagamento_empresa, 
								c.folha_pagamento_nome, 
								c.folha_pagamento_cpf, 
								c.folha_pagamento_celular, 
								c.folha_pagamento_email, 

								c.beneficiario_1_nome, TO_CHAR(c.beneficiario_1_dt_nascimento, 'DD/MM/YYYY') AS beneficiario_1_dt_nascimento, c.beneficiario_1_sexo, 
								c.beneficiario_2_nome, TO_CHAR(c.beneficiario_2_dt_nascimento, 'DD/MM/YYYY') AS beneficiario_2_dt_nascimento, c.beneficiario_2_sexo, 
								c.beneficiario_3_nome, TO_CHAR(c.beneficiario_3_dt_nascimento, 'DD/MM/YYYY') AS beneficiario_3_dt_nascimento, c.beneficiario_3_sexo, 
								c.beneficiario_4_nome, TO_CHAR(c.beneficiario_4_dt_nascimento, 'DD/MM/YYYY') AS beneficiario_4_dt_nascimento, c.beneficiario_4_sexo,
					   
							    c.indicacao_interna_nome, c.indicacao_interna_cpf,
						 	    c.indicacao_nome, c.indicacao_telefone, c.indicacao_email,
								
								c.fl_interesse_associar_ffp, c.fl_lgpd, c.cd_formulario,
								c.cd_documento_recebido
						  FROM familia_previdencia.cadastro c
						 WHERE c.dt_exclusao IS NULL
						   AND MD5(CAST(c.cd_cadastro AS TEXT)) = '".(trim($_REQUEST['c']))."'
					  ";		
			$ob_resul = pg_query($db, $qr_sql);
			if(pg_num_rows($ob_resul) > 0)
			{
				$tp_cadastro = 1;
			}
		}
		$AR_CADASTRO = pg_fetch_array($ob_resul);

		$cd_instituidor = (intval($AR_CADASTRO['cd_instituidor']) > 0 ? intval($AR_CADASTRO['cd_instituidor']) : $cd_instituidor);
		
		#echo "<PRE>".$tp_cadastro."</PRE>";
		#echo "<PRE>".$_REQUEST['ORIG']."</PRE>";
		#echo "<PRE>".$qr_sql."</PRE>";
		#echo "<PRE>".print_r($AR_CADASTRO,true)."</PRE>"; 
		#exit;
		
    
        $bool_contato = false;
        if(intval($AR_CADASTRO['cd_cadastro']) > 0)
        {
            $qr_sql = "SELECT COUNT(cpv.*) AS tl_cadastro
                         FROM familia_previdencia.cadastro_pre_venda cpv
                        WHERE cpv.cd_cadastro = ".intval($AR_CADASTRO['cd_cadastro'])."
                        UNION
                       SELECT COUNT(dpv.*) AS tl_cadastro
                         FROM familia_previdencia.dependente_pre_venda dpv
                         JOIN familia_previdencia.dependente d
                           ON d.cd_dependente = dpv.cd_dependente
                        WHERE d.cd_cadastro = ".intval($AR_CADASTRO['cd_cadastro'])."
                ";

            $ob_resul = pg_query($db, $qr_sql);


            while($ar_contato = pg_fetch_array($ob_resul))
            {
                if($ar_contato['tl_cadastro'] > 0)
                {
                    $bool_contato = true;
                }
            }
        }
		
		$qr_sql = "
			SELECT p.cd_empresa,
				   p.cd_registro_empregado,
				   p.seq_dependencia,
				   p.nome,
				   TO_CHAR(p.dt_nascimento,'DD/MM/YYYY') AS dt_nascimento,
				   COALESCE(p.email,'') AS email_1,
				   COALESCE(p.email_profissional,'') AS email_2,
				   p.logradouro AS endereco,
				   p.bairro,
				   TO_CHAR(p.cep,'FM00000') || '-' || TO_CHAR(p.complemento_cep,'FM000') AS cep,
				   p.cidade,
				   p.unidade_federativa AS uf,
				   '('|| TO_CHAR(COALESCE(p.ddd,0),'FM00') || ') ' || p.telefone AS telefone,
				   '('|| TO_CHAR(COALESCE(p.ddd_celular,0),'FM00') || ') ' || p.celular AS celular,
				   funcoes.format_cpf(p.cpf_mf) AS cpf,
				   'N' AS fl_associado,
				   'N' AS fl_inscrito
			  FROM public.participantes p
			 WHERE p.cd_empresa            = ".intval(trim($AR_CADASTRO['cd_empresa']))."
			   AND p.cd_registro_empregado = ".intval(trim($AR_CADASTRO['cd_registro_empregado']))."
			   AND p.seq_dependencia       = ".intval(trim($AR_CADASTRO['seq_dependencia'])).";";
 
		$ob_resul = pg_query($db, $qr_sql);
		$AR_CONTATO = pg_fetch_array($ob_resul);		 
	?>
<body style="margin: 0px; padding: 0px;">
	<?php
		include_once("restrito_topo.php");
	?>
<div class="aba">
	<ul>
		<li>&nbsp;&nbsp;&nbsp;</li>
		<?php echo ($_SESSION['F_TP_USUARIO'] != "E" ? '<li><a href="restrito_afceee.php">AFCEEE</a></li>' : ''); ?>
		<?php echo ($_SESSION['F_TP_USUARIO'] == "F" ? '<li><a href="restrito_pm_sm.php">PM-SM</a></li>' : ''); ?>
		<?php echo ($_SESSION['F_TP_USUARIO'] == "F" ? '<li><a href="restrito_seprorgs.php">SEPRORGS</a></li>' : ''); ?>
		<?php echo ($_SESSION['F_TP_USUARIO'] == "F" ? '<li><a href="restrito_sintec.php">SINTEC</a></li>' : ''); ?>
		<?php echo ($_SESSION['F_TP_USUARIO'] == "F" ? '<li><a href="restrito_aplub.php">APLUB</a></li>' : ''); ?>
		<?php echo ($_SESSION['F_TP_USUARIO'] == "F" ? '<li><a href="restrito_fceee.php">Fundação CEEE</a></li>' : ''); ?>
		<?php echo ($_SESSION['F_TP_USUARIO'] != "E" ? '<li><a href="restrito_lista.php">Interessados</a></li>' : ''); ?>
		
		<?php echo ($_SESSION['F_TP_USUARIO'] != "E" ? '<li><a href="restrito_inscrito.php">Inscritos</a></li>' : ''); ?>
		<?php echo ($_SESSION['F_TP_USUARIO'] == "F" ? '<li><a href="restrito_consulta_agenda.php">Consulta Agenda</a></li>' : ''); ?>
		<?php echo ($_SESSION['F_TP_USUARIO'] == "F" ? '<li><a href="restrito_campanha.php">Campanha</a></li>' : ''); ?>
		<li class="abaSelecionada"><a href="javascript: location.reload();">Cadastro</a></li>
        <?php echo (($_SESSION['F_TP_USUARIO'] != "E" AND $bool_contato) ? '<li><a href="restrito_contato.php?c='.trim($_REQUEST['c']).'">Contato</a></li>' : ''); ?>
        <?php echo (($_SESSION['F_TP_USUARIO'] != "E" AND $bool_contato) ? '<li><a href="restrito_agenda.php?c='.trim($_REQUEST['c']).'">Agenda</a></li>' : ''); ?>
            
    </ul>
</div>
<div class="conteudo" style="text-align:left;">
	<div class="restrito_cadastro">
	<form style="margin-left: 15px; padding:0px;" id="formCadastro" name="formulario" action="restrito_cadastro_grava.php" method="post" onSubmit=" return validaForm();">
		<input type="hidden" name="fl_cadastro" id="fl_cadastro" value="C">
		<input type="hidden" name="tp_cadastro" id="tp_cadastro" value="<?php echo intval($tp_cadastro);?>">
		<input type="hidden" name="cd_cadastro" id="cd_cadastro" value="<?php echo (intval($AR_CADASTRO['cd_cadastro']));?>" readonly>
		<input type="hidden" name="cd_cadastro_md5" id="cd_cadastro_md5" value="<?php echo (trim($_REQUEST['c']));?>">
		<BR>
		<fieldset id="ob_cadastro_fundacao" style="width: 400px; padding: 10px;">
			<legend style="font-weight: bold;">CADASTRO FUNDAÇÃO</legend>
			<BR>
			<div id="ob_cadastro_fundacao_item">
				<img src="img/loader_p.gif" border="0"> Carregando, aguarde...
			</div>
		</fieldset>			
		
		<BR>
		<fieldset style="width: 680px; padding: 10px;" id="form_cadastro">
			<legend style="font-weight: bold;">DADOS</legend>
			<BR>
			

		<table id="form_cadastro_tabela" border="0">
		
			<tr id='tr_doc_assinatur' <?php echo (trim($AR_CADASTRO['id_doc_assinatura']) == "" ? 'style="display:none"': "");?>>
				<td style="color:blue; font-weight: bold;">Protocolo Assinatura:</td>
				<td valign="middle">
					<input type="text" name="id_doc_assinatura" id="id_doc_assinatura" value="<?php echo $AR_CADASTRO['id_doc_assinatura'];?>" style="width:250px; color:blue; font-weight: bold; border: 0px;" readonly>
					<input type="button" value="Ver" class="botao_disabled" onclick="verProtocoloAssinatura();">
					<BR><BR>
					<span id="status_doc_assinatura" style="width:250px; color:green; font-weight: bold;"></span>
				</td>				
			</tr>		
		
			<tr <?php echo (trim($AR_CADASTRO['id_doc_assinatura']) == "" ? 'style="display:none"': "");?>>
				<td>
					<hr>
				</td>
				<td>
					<hr>
				</td>				
			</tr>			
		
		
		
			<tr id='tr_cd_formulario'>
				<td>Código:</td>
				<td>
					<input type="text" name="cd_formulario" id="cd_formulario" value="<?php echo $AR_CADASTRO['cd_formulario'];?>" style="width: 400px; font-weight: bold;" readonly disabled>
				</td>				
			</tr>	
		
			<tr id='tr_nome'>
				<td>Nome:</td>
				<td>
					<input type="text" name="nome" id="nome" value="<?php echo $AR_CADASTRO['nome'];?>" style="width: 400px; font-weight: bold;">
				</td>				
			</tr>		
		
		
		
		
			<tr>
				<td>
					<hr>
				</td>
				<td>
					<hr>
				</td>				
			</tr>		
		
			<tr id='tr_situacao'>
				<td id="coluna_cadastro_1">Situação:</td>
				<td id="coluna_cadastro_2">
					<select name="cd_cadastro_situacao" id="cd_cadastro_situacao">
						<option value="">Selecione</option>
						<?php echo getSituacao(intval($AR_CADASTRO['cd_cadastro_situacao'])); ?>
					</select>
				</td>
			</tr>	
			
			<tr>
				<td valign="top">Campanha:</td>
				<td valign="top" id="ob_info_campanha"></td>				
			</tr>				
			
			<tr>
				<td valign="top">Origem:</td>
				<td valign="top">
					<?php 
						$origem = (intval($AR_CADASTRO['tp_origem']) == 0 ? intval($tp_cadastro) : intval($AR_CADASTRO['tp_origem']));
						switch(intval($origem))
						{
							case 1: echo "1 - Cadastro direto"; break;
							case 2: echo "2 - Cadastro da AFCEEE"; break;
							case 3: echo "3 - Cadastro da Fundação CEEE"; break;
							case 4: echo "4 - Cadastro do SINTEC"; break;
							case 5: echo "5 - Cadastro do SEPRORGS"; break;
						}
						
					?>				
				</td>				
			</tr>	
			<tr id='tr_situacao'>
				<td id="coluna_cadastro_1">Instituidor:</td>
				<td id="coluna_cadastro_2">
					<select name="cd_instituidor" id="cd_instituidor">
						<option value="">Selecione</option>
						
						<option value="8" <?= (intval($cd_instituidor) == 8 ? 'selected' : '') ?>>SINPRO RS</option>
						<option value="10" <?= (intval($cd_instituidor) == 10 ? 'selected' : '') ?>>SINTAE RS</option>
						<option value="11" <?= (intval($cd_instituidor) == 11 ? 'selected' : '') ?>>SINTEE</option>
						<option value="12" <?= (intval($cd_instituidor) == 12 ? 'selected' : '') ?>>SINTEP</option>
						<option value="14" <?= (intval($cd_instituidor) == 14 ? 'selected' : '') ?>>FAMURS</option>

						<option value="19" <?= (intval($cd_instituidor) == 19 ? 'selected' : '') ?>>AFCEEE</option>
						<option value="20" <?= (intval($cd_instituidor) == 20 ? 'selected' : '') ?>>SINTEC</option>
						<option value="24" <?= (intval($cd_instituidor) == 24 ? 'selected' : '') ?>>TCHE PREVIDENCIA</option>
						<option value="25" <?= (intval($cd_instituidor) == 25 ? 'selected' : '') ?>>SEPRORGS</option>
						<option value="26" <?= (intval($cd_instituidor) == 26 ? 'selected' : '') ?>>ABR HRS</option>
						<option value="27" <?= (intval($cd_instituidor) == 27 ? 'selected' : '') ?>>CEAPE</option>
						<option value="28" <?= (intval($cd_instituidor) == 28 ? 'selected' : '') ?>>SINDHA</option>
						<option value="29" <?= (intval($cd_instituidor) == 29 ? 'selected' : '') ?>>FUNDAÇÃO FAMILIA PREVIDENCIA</option>
						<option value="31" <?= (intval($cd_instituidor) == 31 ? 'selected' : '') ?>>ARCOSUL</option>
					</select>
				</td>
			</tr>			
	
			<tr>
				<td valign="top">Cadastro:</td>
				<td valign="top" id="ob_info_cadastro_instituidor"></td>				
			</tr>
			<tr id='tr_inscrito'>
				<td>Inscrito no Família Previdência:</td>
				<td>
					<select name="fl_inscrito" id="fl_inscrito">
						<option value="">Selecione</option>
						<option value="S" <?php echo (trim($AR_CADASTRO['fl_inscrito']) == "S" ? "selected" : "");?>>Sim</option>
						<option value="N" <?php echo (trim($AR_CADASTRO['fl_inscrito']) == "N" ? "selected" : "");?>>Não</option>
					</select>
				</td>				
			</tr>			

			
			<tr id='tr_socio'>
				<td>Sócio da AFCEEE:</td>
				<td>
					<select name="fl_associado" id="fl_associado">
						<option value="">Selecione</option>
						<option value="S" <?php echo (trim($AR_CADASTRO['fl_associado']) == "S" ? "selected" : "");?>>Sim</option>
						<option value="N" <?php echo (trim($AR_CADASTRO['fl_associado']) == "N" ? "selected" : "");?>>Não</option>
					</select>
				</td>				
			</tr>
			<tr id='tr_delegacia'>
				<td>Delegacia:</td>
				<td>
					<select name="delegacia" id="delegacia">
						<option value="">Selecione</option>
						<?php echo getDelegacia(trim($AR_CADASTRO['delegacia'])); ?>
					</select>
				</td>				
			</tr>			
			<tr id='tr_participante'>
				<td>Participante da FFP:</td>
				<td>
					<select name="fl_participante" id="fl_participante">	
						<option value="">Selecione</option>
						<option value="S" <?php echo (  (trim($AR_CADASTRO['fl_participante']) == "S")||$acesso_especial ? "selected" : "");?>>Sim</option>
						<option value="N" <?php echo (trim($AR_CADASTRO['fl_participante']) == "N" ? "selected" : "");?>>Não</option>
					</select>
				</td>				
			</tr>		
			<tr>
				<td valign="top">Dados Parcitipante FFP</td>
				<td id="ob_info_participante"></td>
			</tr>
			<tr id='tr_empresa'>
				<td>Empresa:</td>
				<td>
					<select name="cd_empresa" id="cd_empresa">
						<option value="" <? echo ($AR_CADASTRO['cd_empresa'] == "" ? "selected" : "");?>>Selecione</option>
						<option value="0" <? echo ($AR_CADASTRO['cd_empresa'] == 0 ? "selected" : "");?>>Grupo CEEE</option>
						<option value="3" <? echo ($AR_CADASTRO['cd_empresa'] == 3 ? "selected" : "");?>>CGTEE</option>		
						<option value="2" <? echo ($AR_CADASTRO['cd_empresa'] == 2 ? "selected" : "");?>>AES Sul</option>				
						<option value="1" <? echo ($AR_CADASTRO['cd_empresa'] == 1 ? "selected" : "");?>>RGE</option>
						<option value="6" <? echo ($AR_CADASTRO['cd_empresa'] == 6 ? "selected" : "");?>>CRM</option>
						<option value="7" <? echo ($AR_CADASTRO['cd_empresa'] == 7 ? "selected" : "");?>>Senge</option>						
						<option value="8" <? echo ($AR_CADASTRO['cd_empresa'] == 8 ? "selected" : "");?>>SINPRO/RS</option> 
						<option value="10" <? echo ($AR_CADASTRO['cd_empresa'] == 10 ? "selected" : "");?>>SINTAE</option>
						<option value="9" <? echo ($AR_CADASTRO['cd_empresa'] == 9 ? "selected" : "");?>>Fundação CEEE</option>
						<option value="19" <? echo ($AR_CADASTRO['cd_empresa'] == 19 ? "selected" : "");?>>AFCEEE</option>
						<option value="20" <? echo ($AR_CADASTRO['cd_empresa'] == 20 ? "selected" : "");?>>SINTEC</option>
						<option value="25" <? echo ($AR_CADASTRO['cd_empresa'] == 25 ? "selected" : "");?>>SEPRORGS</option>
					</select>
				</td>				
			</tr>
			<?php
				if($AR_CADASTRO['cd_empresa'] == 25)
				{
					$qr_sql_sp = "
					              SELECT cd_seprorgs_cadastro, 
								         nome 
								    FROM familia_previdencia.seprorgs_cadastro 
					               WHERE cd_seprorgs_cadastro IN (".intval($_REQUEST['cd_seprorgs']).",".intval($AR_CADASTRO['cd_origem']).")
								 ";
					$ob_resul_sp = pg_query($db, $qr_sql_sp);
					$ar_reg_sp = pg_fetch_array($ob_resul_sp);						
					
					echo "
							<tr id='tr_empresa_seprorgs'>
								<td>SEPRORGS - Empresa:</td>
								<td>
									<a href='restrito_seprorgs_cadastro.php?c=".$ar_reg_sp['cd_seprorgs_cadastro']."'>".$ar_reg_sp['nome']."</a>
								</td>				
							</tr>
						 ";
				}
			?>
			<tr id='tr_re'>
				<td>RE:</td>
				<td>
					<input type="text" name="cd_registro_empregado" id="cd_registro_empregado" value="<?php echo $AR_CADASTRO['cd_registro_empregado'];?>" style="width: 80px;">
				</td>				
			</tr>	
			<tr id='tr_sequencia'>
				<td>Sequência:</td>
				<td>
					<input type="text" name="seq_dependencia" id="seq_dependencia" value="<?php echo $AR_CADASTRO['seq_dependencia'];?>" style="width: 30px;">
				</td>				
			</tr>			
			<tr id='tr_familiares'>
				<td>Interesse em fazer o plano para familiares:</td>
				<td>
					<select name="fl_interesse_familiar" id="fl_interesse_familiar">	
						<option value="">Selecione</option>
						<option value="S" <?php echo (trim($AR_CADASTRO['fl_interesse_familiar']) == "S" ? "selected" : "");?>>Sim</option>
						<option value="N" <?php echo (trim($AR_CADASTRO['fl_interesse_familiar']) == "N" ? "selected" : "");?>>Não</option>
					</select>
				</td>				
			</tr>				
			
			<tr id='tr_contato'>
				<td>Melhor forma de contato:</td>
				<td>
					<select name="cd_contato_forma" id="cd_contato_forma">	
						<option value="">Selecione</option>
						<?php echo getContatoForma(intval($AR_CADASTRO['cd_contato_forma'])); ?>
					</select>
				</td>				
			</tr>	
			<tr id='tr_informacoes'>
				<td>Deseja receber informações do Plano:</td>
				<td>
					<select name="fl_receber_info" id="fl_receber_info" onchange="setReceberInfo();">	
						<option value="">Selecione</option>
						<option value="S" <?php echo (trim($AR_CADASTRO['fl_receber_info']) == "S" ? "selected" : "");?>>Sim</option>
						<option value="N" <?php echo (trim($AR_CADASTRO['fl_receber_info']) == "N" ? "selected" : "");?>>Não</option>
					</select>
				</td>				
			</tr>	
			<tr id="como" <?php echo (trim($AR_CADASTRO['cd_contato_tipo']) != "S" ? 'style="display:none;"' : "");?>>
				<td>Como:</td>
				<td>
					<select name="cd_contato_tipo" id="cd_contato_tipo">	
						<option value="">Selecione</option>
						<?php echo getContatoTipo(intval($AR_CADASTRO['cd_contato_tipo'])); ?>
					</select>
				</td>				
			</tr>			
			
			<tr>
				<td>
					<hr>
				</td>
				<td>
					<hr>
				</td>				
			</tr>				
			
			<tr>
				<td>
					<b>Indicação</b>
				</td>
				<td></td>				
			</tr>			
			
	
			<tr>
				<td valign="top">Indicação - Origem:</td>
				<td valign="top">
					<select name="fl_indicacao_interna" id="fl_indicacao_interna">
						<option value="">Selecione</option>
						<option value="N" <?= (trim($AR_CADASTRO['fl_indicacao_interna']) == "NÃO" ? "selected" : "") ?>>NÃO</option>
						<option value="AP" <?= (trim($AR_CADASTRO['fl_indicacao_interna']) == "AP" ? "selected" : "") ?>>Adesão Premiada</option>
						<option value="IP" <?= (trim($AR_CADASTRO['fl_indicacao_interna']) == "IP" ? "selected" : "") ?>>Indicação Premiada</option>
						<option value="AI" <?= (trim($AR_CADASTRO['fl_indicacao_interna']) == "AI" ? "selected" : "") ?>>AI</option>
						<option value="GC" <?= (trim($AR_CADASTRO['fl_indicacao_interna']) == "GC" ? "selected" : "") ?>>GC</option>
						<option value="GCM" <?= (trim($AR_CADASTRO['fl_indicacao_interna']) == "GCM" ? "selected" : "") ?>>GCM</option>
						<option value="GFC" <?= (trim($AR_CADASTRO['fl_indicacao_interna']) == "GFC" ? "selected" : "") ?>>GFC</option>
						<option value="GGPA" <?= (trim($AR_CADASTRO['fl_indicacao_interna']) == "GGPA" ? "selected" : "") ?>>GGPA</option>
						<option value="GIN" <?= (trim($AR_CADASTRO['fl_indicacao_interna']) == "GIN" ? "selected" : "") ?>>GIN</option>
						<option value="GJ" <?= (trim($AR_CADASTRO['fl_indicacao_interna']) == "GJ" ? "selected" : "") ?>>GJ</option>
						<option value="GP" <?= (trim($AR_CADASTRO['fl_indicacao_interna']) == "GP" ? "selected" : "") ?>>GP</option>
						<option value="GRC" <?= (trim($AR_CADASTRO['fl_indicacao_interna']) == "GRC" ? "selected" : "") ?>>GRC</option>
						<option value="GTI" <?= (trim($AR_CADASTRO['fl_indicacao_interna']) == "GTI" ? "selected" : "") ?>>GTI</option>
					</select>
				</td>				
			</tr>		

			<tr id='tr_indicacao_nome'>
				<td>Indicação - Nome:</td>
				<td>
					<input type="text" name="indicacao_nome" id="indicacao_nome" value="<?php echo $AR_CADASTRO['indicacao_nome'];?>" style="width: 400px;">
				</td>				
			</tr>

			<tr id='tr_indicacao_telefone'>
				<td>Indicação - Telefone:</td>
				<td>
					<input type="text" name="indicacao_telefone" id="indicacao_telefone" value="<?php echo $AR_CADASTRO['indicacao_telefone'];?>" style="width: 120px;">
				</td>				
			</tr>
			
			<tr id='tr_indicacao_email'>
				<td>Indicação - E-mail:</td>
				<td>
					<input type="text" name="indicacao_email" id="indicacao_email" value="<?php echo $AR_CADASTRO['indicacao_email'];?>" style="width: 400px;">
				</td>				
			</tr>			

			<tr>
				<td>
					<hr>
				</td>
				<td>
					<hr>
				</td>				
			</tr>				
			
			<tr>
				<td>
					<b>Cadastro</b>
				</td>
				<td></td>				
			</tr>			
			
			<tr id='tr_nascimento'>
				<td>Dt Nascimento:</td>
				<td>
					<input type="text" name="dt_nascimento" id="dt_nascimento" value="<?php echo $AR_CADASTRO['dt_nascimento'];?>" style="width: 120px;" onchange="setResponsavelLegal()">
				</td>				
			</tr>			
			
			<tr id='tr_tp_sexo'>
				<td>Sexo:</td>
				<td>
					<select name="tp_sexo" id="tp_sexo">	
						<option value="">Selecione</option>
						<option value="F" <?php echo (trim($AR_CADASTRO['tp_sexo']) == "F" ? "selected" : "");?>>Feminino</option>
						<option value="M" <?php echo (trim($AR_CADASTRO['tp_sexo']) == "M" ? "selected" : "");?>>Masculino</option>
					</select>
				</td>				
			</tr>			
			
			<tr id='tr_estado_civil'>
				<td>Estado Civil:</td>
				<td>
					<select name="cd_estado_civil" id="cd_estado_civil">	
						<option value="">Selecione</option>
						<?php echo getEstadoCivil(intval($AR_CADASTRO['cd_estado_civil'])); ?>
					</select>
				</td>				
			</tr>			
			
			<tr id='tr_cpf'>
				<td>CPF:</td>
				<td>
					<input type="text" name="cpf" id="cpf" value="<?php echo $AR_CADASTRO['cpf'];?>" style="width: 120px;">
				</td>				
			</tr>	

			<tr id='tr_ds_rg'>
				<td>RG:</td>
				<td>
					<input type="text" name="ds_rg" id="ds_rg" value="<?php echo $AR_CADASTRO['ds_rg'];?>" style="width: 120px;">
				</td>				
			</tr>		
			<tr id='tr_ds_orgao_expedidor'>
				<td>Orgão expedidor:</td>
				<td>
					<input type="text" name="ds_orgao_expedidor" id="ds_orgao_expedidor" value="<?php echo $AR_CADASTRO['ds_orgao_expedidor'];?>" style="width: 120px;">
				</td>				
			</tr>				
			<tr id='tr_dt_expedicao'>
				<td>Data de Expedição:</td>
				<td>
					<input type="text" name="dt_expedicao" id="dt_expedicao" value="<?php echo $AR_CADASTRO['dt_expedicao'];?>" style="width: 120px;">
				</td>				
			</tr>		


			<tr id='tr_ds_nome_pai'>
				<td>Nome do Pai:</td>
				<td>
					<input type="text" name="ds_nome_pai" id="ds_nome_pai" value="<?php echo $AR_CADASTRO['ds_nome_pai'];?>" style="width: 400px;">
				</td>				
			</tr>
			<tr id='tr_ds_nome_mae'>
				<td>Nome da Mãe:</td>
				<td>
					<input type="text" name="ds_nome_mae" id="ds_nome_mae" value="<?php echo $AR_CADASTRO['ds_nome_mae'];?>" style="width: 400px;">
				</td>				
			</tr>			
			
			<tr id='tr_ds_naturalidade'>
				<td>Naturalidade:</td>
				<td>
					<input type="text" name="ds_naturalidade" id="ds_naturalidade" value="<?php echo $AR_CADASTRO['ds_naturalidade'];?>" style="width: 400px;">
				</td>				
			</tr>
			<tr id='tr_ds_nacionalidade'>
				<td>Nacionalidade:</td>
				<td>
					<input type="text" name="ds_nacionalidade" id="ds_nacionalidade" value="<?php echo $AR_CADASTRO['ds_nacionalidade'];?>" style="width: 400px;">
				</td>				
			</tr>			
			
			
			
			<tr id='tr_fl_ppe'>
				<td>PPE:</td>
				<td>
					<select name="fl_ppe" id="fl_ppe">	
						<option value="">Não informado</option>
						<option value="S" <?php echo (trim($AR_CADASTRO['fl_ppe']) == "S" ? "selected" : "");?>>Sim</option>
						<option value="N" <?php echo (trim($AR_CADASTRO['fl_ppe']) == "N" ? "selected" : "");?>>Não</option>
					</select>
				</td>				
			</tr>
			<tr id='tr_fl_usperson'>
				<td>US Person:</td>
				<td>
					<select name="fl_usperson" id="fl_usperson">	
						<option value="">Não informado</option>
						<option value="S" <?php echo (trim($AR_CADASTRO['fl_usperson']) == "S" ? "selected" : "");?>>Sim</option>
						<option value="N" <?php echo (trim($AR_CADASTRO['fl_usperson']) == "N" ? "selected" : "");?>>Não</option>
					</select>
				</td>				
			</tr>
			<!--
			<tr id='tr_fl_pessoa_associada_ffp'>
				<td>Pessoa associada à FFP:</td>
				<td>
					<select name="fl_pessoa_associada_ffp" id="fl_pessoa_associada_ffp">	
						<option value="">Não informado</option>
						<option value="S" <?php echo (trim($AR_CADASTRO['fl_pessoa_associada_ffp']) == "S" ? "selected" : "");?>>Sim</option>
						<option value="N" <?php echo (trim($AR_CADASTRO['fl_pessoa_associada_ffp']) == "N" ? "selected" : "");?>>Não</option>
						<option value="A" <?php echo (trim($AR_CADASTRO['fl_pessoa_associada_ffp']) == "A" ? "selected" : "");?>>Não se Aplica</option>
					</select>
				</td>				
			</tr>
				-->
			<tr id='tr_fl_tributacao'>
				<td>Tributação:</td>
				<td>
					<select name="fl_tributacao" id="fl_tributacao">	
						<option value="">Não informado</option>
						<option value="P" <?php echo (trim($AR_CADASTRO['fl_tributacao']) == "P" ? "selected" : "");?>>Tabela Progressiva - Tradicional</option>
						<option value="R" <?php echo (trim($AR_CADASTRO['fl_tributacao']) == "R" ? "selected" : "");?>>Tabela Regressiva</option>
					</select>
				</td>				
			</tr>
				

			<tr id='tr_email'>
				<td>E-mail 1:</td>
				<td>
					<input type="text" name="email_1" id="email_1" value="<?php echo $AR_CADASTRO['email_1'];?>" style="width: 400px;">
				</td>				
			</tr>
			<tr id='tr_email'>
				<td>E-mail 2:</td>
				<td>
					<input type="text" name="email_2" id="email_2" value="<?php echo $AR_CADASTRO['email_2'];?>" style="width: 400px;">
				</td>				
			</tr>			
			<tr id='tr_telefone_1'>
				<td>Celular:</td>
				<td>
					<input type="text" name="celular" id="celular" value="<?php echo $AR_CADASTRO['celular'];?>" style="width: 100px;">
				</td>				
			</tr>
			<tr id='tr_telefone_2'>
				<td>Telefone 1:</td>
				<td>
					<input type="text" name="telefone" id="telefone" value="<?php echo $AR_CADASTRO['telefone'];?>" style="width: 100px;">
				</td>				
			</tr>		
			<tr id='tr_telefone_3'>
				<td>Telefone 2:</td>
				<td>
					<input type="text" name="telefone_3" id="telefone_3" value="<?php echo $AR_CADASTRO['telefone_3'];?>" style="width: 100px;">
				</td>				
			</tr>
			<tr id='tr_telefone_2'>
				<td>Telefone 3:</td>
				<td>
					<input type="text" name="telefone_4" id="telefone_4" value="<?php echo $AR_CADASTRO['telefone_4'];?>" style="width: 100px;">
				</td>				
			</tr>			
			<tr id='tr_endereco'>
				<td>Endereço:</td>
				<td>
					<input type="text" name="endereco" id="endereco" value="<?php echo $AR_CADASTRO['endereco'];?>" style="width: 400px;">
				</td>				
			</tr>
			<tr id='tr_complemento'>
				<td>Complemento:</td>
				<td>
					<input type="text" name="complemento" id="complemento" value="<?php echo $AR_CADASTRO['complemento'];?>" style="width: 400px;">
				</td>				
			</tr>
			<tr id='tr_bairro'>
				<td>Bairro:</td>
				<td>
					<input type="text" name="bairro" id="bairro" value="<?php echo $AR_CADASTRO['bairro'];?>" style="width: 400px;" />
				</td>				
			</tr>			
			<tr id='tr_cep'>
				<td>CEP:</td>
				<td>
					<input type="text" name="cep" id="cep" value="<?php echo $AR_CADASTRO['cep'];?>" style="width: 70px;">
				</td>				
			</tr>
			<tr id='tr_cidade'>
				<td>Cidade:</td>
				<td>
					<input type="text" name="cidade" id="cidade" value="<?php echo $AR_CADASTRO['cidade'];?>" style="width: 400px;">
				</td>				
			</tr>	
			<tr id='tr_uf'>
				<td>UF:</td>
				<td>
					<select name="uf" id="uf">	
						<option value="">Selecione</option>
						<?php echo getUF(trim($AR_CADASTRO['uf'])); ?>
					</select>
				</td>				
			</tr>	

			
			<tr class="obRepresentanteLegal">
				<td>
					<hr>
				</td>
				<td>
					<hr>
				</td>				
			</tr>				
			
			<tr class="obRepresentanteLegal">
				<td>
					<b>Representante Legal</b>
				</td>
				<td><span style="color: red; font-weight:bold">Cliente menor de idade, é necessário informar o representante legal.</span></td>				
			</tr>	
			<tr class="obRepresentanteLegal" id='tr_representante_legal_nome'>
				<td>Nome:</td>
				<td>
					<input type="text" name="representante_legal_nome" id="representante_legal_nome" value="<?php echo $AR_CADASTRO['representante_legal_nome'];?>" style="width: 400px;">
				</td>				
			</tr>
			
			<tr class="obRepresentanteLegal" id='tr_representante_legal_cpf'>
				<td>CPF:</td>
				<td>
					<input type="text" name="representante_legal_cpf" id="representante_legal_cpf" value="<?php echo $AR_CADASTRO['representante_legal_cpf'];?>" style="width: 120px;">
				</td>				
			</tr>			

			<tr class="obRepresentanteLegal" id='tr_representante_legal_celular'>
				<td>Celular:</td>
				<td>
					<input type="text" name="representante_legal_celular" id="representante_legal_celular" value="<?php echo $AR_CADASTRO['representante_legal_celular'];?>" style="width: 100px;">
				</td>				
			</tr>
			
			<tr class="obRepresentanteLegal" id='tr_representante_legal_email'>
				<td>E-mail:</td>
				<td>
					<input type="text" name="representante_legal_email" id="representante_legal_email" value="<?php echo $AR_CADASTRO['representante_legal_email'];?>" style="width: 400px;">
				</td>				
			</tr>
			
			<tr>
				<td>
					<hr>
				</td>
				<td>
					<hr>
				</td>				
			</tr>			
			
			<tr>
				<td>
					<b>Vinculação</b>
				</td>
				<td></td>				
			</tr>	
			<tr id='tr_ds_associado'>
				<td>Associado a:</td>
				<td>
					<input type="text" name="ds_associado" id="ds_associado" value="<?php echo $AR_CADASTRO['ds_associado'];?>" style="width: 400px;">
				</td>				
			</tr>
			<tr id='tr_ds_vinculo_associado'>
				<td>Vínculo:</td>
				<td>
					<input type="text" name="ds_vinculo_associado" id="ds_vinculo_associado" value="<?php echo $AR_CADASTRO['ds_vinculo_associado'];?>" style="width: 400px;">
				</td>				
			</tr>
			<tr id='tr_ds_vinculo_grau'>
				<td>Grau de Vínculo:</td>
				<td>
					<input type="text" name="ds_vinculo_grau" id="ds_vinculo_grau" value="<?php echo $AR_CADASTRO['ds_vinculo_grau'];?>" style="width: 400px;">
				</td>				
			</tr>	
			</tr>
			<tr id='tr_fl_interesse_associar_ffp'>
				<td>Interesse em tornar-se pessoa associada à FFP:</td>
				<td>
					<select name="fl_interesse_associar_ffp" id="fl_interesse_associar_ffp">	
						<option value="">Não informado</option>
						<option value="S" <?php echo (trim($AR_CADASTRO['fl_interesse_associar_ffp']) == "S" ? "selected" : "");?>>Sim</option>
						<option value="N" <?php echo (trim($AR_CADASTRO['fl_interesse_associar_ffp']) == "N" ? "selected" : "");?>>Não</option>
						<option value="A" <?php echo (trim($AR_CADASTRO['fl_interesse_associar_ffp']) == "A" ? "selected" : "");?>>Não se Aplica</option>
					</select>					
				</td>				
			</tr>	



			<tr>
				<td>
					<hr>
				</td>
				<td>
					<hr>
				</td>				
			</tr>			
			
			<tr>
				<td>
					<b>Primeira Contribuição</b>
				</td>
				<td></td>				
			</tr>	
			<tr id='tr_tp_forma_pagamento_primeira'>
				<td>Forma de Pagamento:</td>
				<td>
					<select name="tp_forma_pagamento_primeira" id="tp_forma_pagamento_primeira" onchange="setFormaPagamento();">	
						<option value="">Selecione</option>
						<option value="BDL" <?php echo (trim($AR_CADASTRO['tp_forma_pagamento_primeira']) == "BDL" ? "selected" : "");?>>Boleto</option>
						<option value="DCC" <?php echo (trim($AR_CADASTRO['tp_forma_pagamento_primeira']) == "DCC" ? "selected" : "");?>>Débito em Conta</option>
						<option value="FOL" <?php echo (trim($AR_CADASTRO['tp_forma_pagamento_primeira']) == "FOL" ? "selected" : "");?>>Folha de Pagamento</option>
					</select>
				</td>				
			</tr>
			<tr id='tr_nr_contrib_primeira'>
				<td>Valor Contribuição:</td>
				<td>
					<input type="text" name="nr_contrib_primeira" id="nr_contrib_primeira" value="<?php echo $AR_CADASTRO['nr_contrib_primeira'];?>" style="width: 200px;">
				</td>				
			</tr>
		
		
			<tr>
				<td>
					<hr>
				</td>
				<td>
					<hr>
				</td>				
			</tr>			
			
			<tr>
				<td>
					<b>Contribuição Mensal</b>
				</td>
				<td></td>				
			</tr>	
			<tr id='tr_tp_forma_pagamento_mensal'>
				<td>Forma de Pagamento:</td>
				<td>
					<select name="tp_forma_pagamento_mensal" id="tp_forma_pagamento_mensal" onchange="setFormaPagamento();">	
						<option value="">Selecione</option>
						<option value="BDL" <?php echo (trim($AR_CADASTRO['tp_forma_pagamento_mensal']) == "BDL" ? "selected" : "");?>>Boleto</option>
						<option value="DCC" <?php echo (trim($AR_CADASTRO['tp_forma_pagamento_mensal']) == "DCC" ? "selected" : "");?>>Débito em Conta</option>
						<option value="FOL" <?php echo (trim($AR_CADASTRO['tp_forma_pagamento_mensal']) == "FOL" ? "selected" : "");?>>Folha de Pagamento</option>
					</select>
				</td>				
			</tr>
			<tr id='tr_nr_contrib_mensal'>
				<td>Valor Contribuição:</td>
				<td>
					<input type="text" name="nr_contrib_mensal" id="nr_contrib_mensal" value="<?php echo $AR_CADASTRO['nr_contrib_mensal'];?>" style="width: 200px;">
				</td>				
			</tr>		
		
		
			<tr>
				<td>
					<hr>
				</td>
				<td>
					<hr>
				</td>				
			</tr>			
			
			<tr>
				<td>
					<b>Contribuição Extra-inicial</b>
				</td>
				<td></td>				
			</tr>	
			<tr id='tr_tp_forma_pagamento_extra_inicial'>
				<td>Forma de Pagamento:</td>
				<td>
					<select name="tp_forma_pagamento_extra_inicial" id="tp_forma_pagamento_extra_inicial" onchange="setFormaPagamento();">	
						<option value="">Selecione</option>
						<option value="NAO" <?php echo (trim($AR_CADASTRO['tp_forma_pagamento_extra_inicial']) == "NAO" ? "selected" : "");?>>Não possui</option>
						<option value="BDL" <?php echo (trim($AR_CADASTRO['tp_forma_pagamento_extra_inicial']) == "BDL" ? "selected" : "");?>>Boleto</option>
						<option value="DCC" <?php echo (trim($AR_CADASTRO['tp_forma_pagamento_extra_inicial']) == "DCC" ? "selected" : "");?>>Débito em Conta</option>
						<option value="FOL" <?php echo (trim($AR_CADASTRO['tp_forma_pagamento_extra_inicial']) == "FOL" ? "selected" : "");?>>Folha de Pagamento</option>
					</select>
				</td>				
			</tr>
			<tr id='tr_nr_contrib_extra_inicial'>
				<td>Valor Contribuição:</td>
				<td>
					<input type="text" name="nr_contrib_extra_inicial" id="nr_contrib_extra_inicial" value="<?php echo $AR_CADASTRO['nr_contrib_extra_inicial'];?>" style="width: 200px;">
				</td>				
			</tr>		

			<tr class="obAutorizacaoDebitoConta">
				<td>
					<hr>
				</td>
				<td>
					<hr>
				</td>				
			</tr>				
			
			<tr class="obAutorizacaoDebitoConta">
				<td>
					<b>Autorização Débito em Conta</b>
				</td>
				<td><span style="color: red; font-weight:bold">Cliente com débito em conta, é necessário informar os dados para desconto.</span></td>				
			</tr>	
			
			<tr class="obAutorizacaoDebitoConta" id='tr_debito_conta_agencia'>
				<td>Banco:</td>
				<td>
					041 - Banrisul
				</td>				
			</tr>			
			
			<tr class="obAutorizacaoDebitoConta" id='tr_debito_conta_agencia'>
				<td>Agência:</td>
				<td>
					<input type="text" name="debito_conta_agencia" id="debito_conta_agencia" value="<?php echo $AR_CADASTRO['debito_conta_agencia'];?>" style="width: 100px;">
				</td>				
			</tr>			
			
			<tr class="obAutorizacaoDebitoConta" id='tr_debito_conta_nr_conta'>
				<td>Conta:</td>
				<td>
					<input type="text" name="debito_conta_nr_conta" id="debito_conta_nr_conta" value="<?php echo $AR_CADASTRO['debito_conta_nr_conta'];?>" style="width: 100px;">
				</td>				
			</tr>			
			
			<tr class="obAutorizacaoDebitoConta" id='tr_debito_conta_nome'>
				<td>Nome:</td>
				<td>
					<input type="text" name="debito_conta_nome" id="debito_conta_nome" value="<?php echo $AR_CADASTRO['debito_conta_nome'];?>" style="width: 400px;">
				</td>				
			</tr>
			
			<tr class="obAutorizacaoDebitoConta" id='tr_debito_conta_cpf'>
				<td>CPF:</td>
				<td>
					<input type="text" name="debito_conta_cpf" id="debito_conta_cpf" value="<?php echo $AR_CADASTRO['debito_conta_cpf'];?>" style="width: 120px;">
				</td>				
			</tr>			

			<tr class="obAutorizacaoDebitoConta" id='tr_debito_conta_celular'>
				<td>Celular:</td>
				<td>
					<input type="text" name="debito_conta_celular" id="debito_conta_celular" value="<?php echo $AR_CADASTRO['debito_conta_celular'];?>" style="width: 100px;">
				</td>				
			</tr>
			
			<tr class="obAutorizacaoDebitoConta" id='tr_debito_conta_email'>
				<td>E-mail:</td>
				<td>
					<input type="text" name="debito_conta_email" id="debito_conta_email" value="<?php echo $AR_CADASTRO['debito_conta_email'];?>" style="width: 400px;">
				</td>				
			</tr>				
			
			<tr class="obAutorizacaoFolhaDePagamento">
				<td>
					<hr>
				</td>
				<td>
					<hr>
				</td>				
			</tr>				
			
			<tr class="obAutorizacaoFolhaDePagamento">
				<td>
					<b>Autorização Folha de Pagamento</b>
				</td>
				<td><span style="color: red; font-weight:bold">Cliente com desconto em folha de pagamento, é necessário informar os dados para desconto.</span></td>				
			</tr>	
			
			<tr class="obAutorizacaoFolhaDePagamento" id='tr_folha_pagamento_empresa'>
				<td>Empresa:</td>
				<td>
					<input type="text" name="folha_pagamento_empresa" id="folha_pagamento_empresa" value="<?php echo $AR_CADASTRO['folha_pagamento_empresa'];?>" style="width: 400px;">
				</td>				
			</tr>			
			
			<tr class="obAutorizacaoFolhaDePagamento" id='tr_folha_pagamento_nome'>
				<td>Nome:</td>
				<td>
					<input type="text" name="folha_pagamento_nome" id="folha_pagamento_nome" value="<?php echo $AR_CADASTRO['folha_pagamento_nome'];?>" style="width: 400px;">
				</td>				
			</tr>
			
			<tr class="obAutorizacaoFolhaDePagamento" id='tr_folha_pagamento_cpf'>
				<td>CPF:</td>
				<td>
					<input type="text" name="folha_pagamento_cpf" id="folha_pagamento_cpf" value="<?php echo $AR_CADASTRO['folha_pagamento_cpf'];?>" style="width: 120px;">
				</td>				
			</tr>

			<tr class="obAutorizacaoFolhaDePagamento" id='tr_folha_pagamento_re'>
				<td>RE (EMP/RE/SEQ):</td>
				<td>
					<input type="text" name="folha_pagamento_re" id="folha_pagamento_re" value="<?php echo $AR_CADASTRO['folha_pagamento_re'];?>" style="width: 120px;">
				</td>				
			</tr>				

			<tr class="obAutorizacaoFolhaDePagamento" id='tr_folha_pagamento_celular'>
				<td>Celular:</td>
				<td>
					<input type="text" name="folha_pagamento_celular" id="folha_pagamento_celular" value="<?php echo $AR_CADASTRO['folha_pagamento_celular'];?>" style="width: 100px;">
				</td>				
			</tr>
			
			<tr class="obAutorizacaoFolhaDePagamento" id='tr_folha_pagamento_email'>
				<td>E-mail:</td>
				<td>
					<input type="text" name="folha_pagamento_email" id="folha_pagamento_email" value="<?php echo $AR_CADASTRO['folha_pagamento_email'];?>" style="width: 400px;">
				</td>				
			</tr>			
			
			
			
<?php
	$i = 1;
	$f = 4;
	while($i <= $f)
	{
?>			
			<tr>
				<td>
					<hr>
				</td>
				<td>
					<hr>
				</td>				
			</tr>			
			
			<tr>
				<td>
					<b>Beneficiário <?php echo $i;?></b>
				</td>
				<td></td>				
			</tr>	
			<tr id='tr_<?php echo 'beneficiario_'.$i.'_nome';?>'> 
				<td>Nome:</td>
				<td>
					<input type="text" name="<?php echo 'beneficiario_'.$i.'_nome';?>" id="<?php echo 'beneficiario_'.$i.'_nome';?>" value="<?php echo $AR_CADASTRO['beneficiario_'.$i.'_nome'];?>" style="width: 400px;">
				</td>				
			</tr>	

			<tr id='tr_<?php echo 'beneficiario_'.$i.'dt_nascimento';?>'>
				<td>Dt Nascimento:</td>
				<td>
					<input type="text" name="<?php echo 'beneficiario_'.$i.'_dt_nascimento';?>" id="<?php echo 'beneficiario_'.$i.'_dt_nascimento';?>" value="<?php echo $AR_CADASTRO['beneficiario_'.$i.'_dt_nascimento'];?>" style="width: 120px;">
					<script>
						jQuery(function($){
							$("#<?php echo 'beneficiario_'.$i.'dt_nascimento';?>").mask("99/99/9999");
						});						
					</script>
				</td>				
			</tr>

			<tr id='tr_<?php echo 'beneficiario_'.$i.'_sexo';?>'>
				<td>Sexo:</td>
				<td>
					<select name="<?php echo 'beneficiario_'.$i.'_sexo';?>" id="<?php echo 'beneficiario_'.$i.'_sexo';?>">	
						<option value="">Selecione</option>
						<option value="F" <?php echo (trim($AR_CADASTRO['beneficiario_'.$i.'_sexo']) == "F" ? "selected" : "");?>>Feminino</option>
						<option value="M" <?php echo (trim($AR_CADASTRO['beneficiario_'.$i.'_sexo']) == "M" ? "selected" : "");?>>Masculino</option>
					</select>
				</td>				
			</tr>
			
<?php
		$i++;
	}
?>			


			<tr>
				<td>
					<hr>
				</td>
				<td>
					<hr>
				</td>				
			</tr>			
			
			<tr>
				<td>
					<b>Vendedor</b>
				</td>
				<td></td>				
			</tr>	
			<tr id='tr_vendedor_nome'>
				<td>Nome:</td>
				<td>
					<input type="text" name="vendedor_nome" id="vendedor_nome" value="<?php echo $AR_CADASTRO['vendedor_nome'];?>" style="width: 400px;">
				</td>				
			</tr>

			<tr id='tr_vendedor_nome'>
				<td>Celular:</td>
				<td>
					<input type="text" name="vendedor_celular" id="vendedor_celular" value="<?php echo $AR_CADASTRO['vendedor_celular'];?>" style="width: 100px;">
				</td>				
			</tr>
			
			<tr id='tr_vendedor_nome'>
				<td>E-mail:</td>
				<td>
					<input type="text" name="vendedor_email" id="vendedor_email" value="<?php echo $AR_CADASTRO['vendedor_email'];?>" style="width: 400px;">
				</td>				
			</tr>

			<tr id='tr_dt_recebimento'>
				<td>Dt Recebimento:</td>
				<td>
					<input type="text" name="dt_recebimento" id="dt_recebimento" value="<?php echo $AR_CADASTRO['dt_recebimento'];?>" style="width: 100px;">
				</td>				
			</tr>		

			<tr>
				<td>
					<hr>
				</td>
				<td>
					<hr>
				</td>				
			</tr>			
	
			<tr>
				<td>
					<b>Indicação Interna</b>
				</td>
				<td></td>				
			</tr>	
			<tr id='tr_indicacao_interna_nome'>
				<td>Nome:</td>
				<td>
					<input type="text" name="indicacao_interna_nome" id="indicacao_interna_nome" value="<?php echo $AR_CADASTRO['indicacao_interna_nome'];?>" style="width: 400px;">
				</td>				
			</tr>

			<tr id='tr_indicacao_interna_cpf'>
				<td>CPF:</td>
				<td>
					<input type="text" name="indicacao_interna_cpf" id="indicacao_interna_cpf" value="<?php echo $AR_CADASTRO['indicacao_interna_cpf'];?>" style="width: 100px;">
				</td>				
			</tr>
	
	
			<tr>
				<td>
					<hr>
				</td>
				<td>
					<hr>
				</td>				
			</tr>	

			<tr>
				<td>
					<b>Tratamento de dados LGPD</b>
				</td>
				<td></td>				
			</tr>	
			<tr id='tr_fl_lgpd'>
				<td>Autorizo o compartilhamento dos meus dados pessoais com empresa de prestação de serviços de seguro e produtos agregados:</td>
				<td>
					<select name="fl_lgpd" id="fl_lgpd">	
						<option value="S" <?php echo (trim($AR_CADASTRO['fl_lgpd']) == "S" ? "selected" : "");?>>Sim</option>
						<option value="N" <?php echo (trim($AR_CADASTRO['fl_lgpd']) == "N" ? "selected" : "");?>>Não</option>
					</select>
				</td>				
			</tr>
	
			<tr>
				<td>
					<hr>
				</td>
				<td>
					<hr>
				</td>				
			</tr>				
	
			<tr id='tr_observacoes'>
				<td valign="top">Observações:</td>
				<td>
                    <textarea name="observacoes" id="observacoes" style="width: 400px;" rows="4" ><?php echo $AR_CADASTRO['observacoes'];?></textarea>
				</td>				
			</tr>
			
			
			<tr>
				<td>
					<hr>
				</td>
				<td>
					<hr>
				</td>				
			</tr>				
			
			<tr id='tr_adicionar_familiar' <?php echo (intval($tp_cadastro) != 1 ? 'style="display:none"': "");?>>
				<td valign="top">Familiares:</td>
				<td valign="top" id="obDependente"></td>				
			</tr>			
				
			<tr>
				<td colspan="4">
					<br><br>

					<table border="0">
						<tr>
							<td align="left">
								<input type="submit" value="Salvar" class="botao" style="width: 160px;">
							</td>
							
							<td align="right">
							<?php echo (intval($AR_CADASTRO['cd_cadastro']) > 0 ? '<input type="button" value="Atualizar Dados" class="botao" style="width: 160px;" onclick="atualizarDados();">' : ''); ?>
							</td>
							<td align="right">
							<?php echo ($_SESSION['F_TP_USUARIO'] == "F" ? '<input type="button" value="excluir" class="botao_vermelho" style="width: 160px;" onclick="excluirCadastro();">' : ''); ?>
							</td>
						</tr>
					</table>
					<br>
					<table border="0">
						<tr>
							<td align="left" <?php echo (intval($tp_cadastro) != 1 ? 'style="display:none"': "");?>>
								<input type="button" value="Formulário Preenchido" class="botao" style="width: 160px;" onclick="imprimirFormularioPreenchido();">
							</td>
							
							<td align="left">
								<input type="button" value="Formulário em Branco" class="botao" style="width: 160px;" onclick="imprimirFormularioBranco();">
							</td>
						</tr>
					</table>
					<br>
					<table border="0" <?php echo (((intval($tp_cadastro) != 1) OR (trim($AR_CADASTRO['id_doc_assinatura']) != "")) ? 'style="display:none"': "");?>>
						<tr>
							<td align="left" colspan="2">
								<input id="btEnviarAssinatura" type="button" value="Enviar formulário para assinatura" class="botao_vermelho" style="width: 325px;" onclick="enviarAssinatura();">
							</td>
						</tr>						
					</table>
					<br>
					<table border="0" id="tbEnviarProtocoloInterno" <?php echo (((intval($tp_cadastro) != 1) OR (trim($AR_CADASTRO['id_doc_assinatura']) == "") OR intval($AR_CADASTRO['cd_documento_recebido']) > 0) ? 'style="display:none"': "");?>>
						<tr>
							<td align="left" colspan="2">
								<input id="btEnviarProtocolo" type="button" value="Enviar Protocolo Interno" class="botao" style="width: 325px;" onclick="enviarProtocoloInterno();">
							</td>
						</tr>						
					</table>				
				</td>				
			</tr>			
		</table>
		</fieldset>
		<BR><BR>
		<fieldset style="width: 680px; padding: 10px;">
			<legend style="font-weight: bold;">CONTATO - FUNDAÇÃO CEEE</legend>
			<BR>
			<table id="form_cadastro_tabela" border="0">
				<tr id='tr_email'>
					<td>E-mail 1:</td>
					<td>
						<input type="text" name="contato_email_1" id="contato_email_1" value="<?php echo $AR_CONTATO['email_1'];?>" style="width: 400px;" readonly="">
					</td>				
				</tr>
				<tr id='tr_email'>
					<td>E-mail 2 (profissional):</td>
					<td>
						<input type="text" name="contato_email_2" id="contato_email_2" value="<?php echo $AR_CONTATO['email_2'];?>" style="width: 400px;" readonly="">
					</td>				
				</tr>			
				<tr id='tr_telefone_1'>
					<td>Telefone 1 (telefone):</td>
					<td>
						<input type="text" name="contato_telefone" id="contato_telefone" value="<?php echo $AR_CONTATO['telefone'];?>" style="width: 100px;" readonly="">
					</td>				
				</tr>
				<tr id='tr_telefone_2'>
					<td>Telefone 2 (celular):</td>
					<td>
						<input type="text" name="contato_celular" id="contato_celular" value="<?php echo $AR_CONTATO['celular'];?>" style="width: 100px;" readonly="">
					</td>				
				</tr>		
				<tr id='tr_endereco'>
					<td>Endereço:</td>
						<td>
							<input type="text" name="contato_endereco" id="contato_endereco" value="<?php echo $AR_CONTATO['endereco'];?>" style="width: 400px;" readonly="">
						</td>				
				</tr>
				<tr id='tr_complemento'>
					<td>Complemento:</td>
					<td>
						<input type="text" name="contato_complemento" id="contato_complemento" value="<?php echo $AR_CONTATO['complemento'];?>" style="width: 400px;" readonly="">
					</td>				
				</tr>
				<tr id='tr_bairro'>
					<td>Bairro:</td>
					<td>
						<input type="text" name="contato_bairro" id="contato_bairro" value="<?php echo $AR_CONTATO['bairro'];?>" style="width: 400px;" readonly=""/>
					</td>				
				</tr>	
				<tr id='tr_cep'>
					<td>CEP:</td>
					<td>
						<input type="text" name="contato_cep" id="contato_cep" value="<?php echo $AR_CONTATO['cep'];?>" style="width: 70px;" readonly="">
					</td>				
				</tr>
				<tr id='tr_cidade'>
					<td>Cidade:</td>
					<td>
						<input type="text" name="contato_cidade" id="contato_cidade" value="<?php echo $AR_CONTATO['cidade'];?>" style="width: 400px;" readonly="">
					</td>				
				</tr>	
				<tr id='tr_uf'>
					<td>UF:</td>
					<td>
						<input type="text" name="contato_uf" id="contato_uf" value="<?php echo $AR_CADASTRO['uf'];?>" style="width: 400px;" readonly="">
					</td>				
				</tr>
			</table>
			<!--
- Endereço (endereço e número)
- Complemento
- Bairro
- CEP
- Cidade
- UF
			-->
		</fieldset>	
		<BR><BR>
		<fieldset style="width: 400px; padding: 10px;">
			<legend style="font-weight: bold;">DEPENDENTE - FUNDAÇÃO CEEE</legend>
			<BR>
			<div id="obDependenteParticipante"></div>
		</fieldset>		
		<BR><BR>
		<fieldset style="width: 400px; padding: 10px;">
			<legend style="font-weight: bold;">HISTÓRICO</legend>
			<BR>
			<div id="obAlteracao"></div>
		</fieldset>
	</form>
	</div>
	
	<div id="adicionaDependente" style="text-align:center; background: #FFFFFF; display:none">
		<iframe id="iframeDependente" frameborder="0" style="border: 0px; width:580px; height:230px;" scrolling="no" src=""></iframe>
	</div>
	<BR><BR>
	<BR><BR>
	<BR><BR>
	<BR><BR>
</div>
<script>
	jQuery(function($){
		nr_largura =  screen.width - ((10 * screen.width) / 100);
		$("#form_cadastro").width(nr_largura);
		$("#form_cadastro_tabela").width((nr_largura - 10));
		$("#coluna_cadastro_1").width(210);
		$("#coluna_cadastro_2").width(((nr_largura - 10) - 210));
		$("#ob_cadastro_fundacao").width(nr_largura);		
	
		$("#cd_registro_empregado").numeric();
		$("#seq_dependencia").numeric();
		
		$("#dt_nascimento").mask("99/99/9999");
		$("#dt_expedicao").mask("99/99/9999");
		$("#dt_recebimento").mask("99/99/9999");
		$("#cpf").mask("999.999.999-99");
		$("#representante_legal_cpf").mask("999.999.999-99");
		$("#debito_conta_cpf").mask("999.999.999-99");
		$("#folha_pagamento_cpf").mask("999.999.999-99");
		$("#indicacao_interna_cpf").mask("999.999.999-99");
		$("#indicacao_telefone").mask("(99)99999999?9");
		$("#telefone").mask("(99)99999999?9");
		$("#celular").mask("(99)99999999?9");
		$("#telefone_3").mask("(99)99999999?9");
		$("#telefone_4").mask("(99)99999999?9");
		$("#representante_legal_celular").mask("(99)99999999?9");
		$("#debito_conta_celular").mask("(99)99999999?9");
		$("#folha_pagamento_celular").mask("(99)99999999?9");
		$("#vendedor_celular").mask("(99)99999999?9");
		$("#cep").mask("99999-999");
		
		$('#nr_contrib_primeira').priceFormat({
			prefix             : '',
			clearPrefix        : false,
			suffix             : '',
			clearSufix         : false,
			centsSeparator     : ',',
			thousandsSeparator : '.',
			centsLimit         : 2,
			allowNegative      : true
		}); 

		$('#nr_contrib_mensal').priceFormat({
			prefix             : '',
			clearPrefix        : false,
			suffix             : '',
			clearSufix         : false,
			centsSeparator     : ',',
			thousandsSeparator : '.',
			centsLimit         : 2,
			allowNegative      : true
		});	

		$('#nr_contrib_extra_inicial').priceFormat({
			prefix             : '',
			clearPrefix        : false,
			suffix             : '',
			clearSufix         : false,
			centsSeparator     : ',',
			thousandsSeparator : '.',
			centsLimit         : 2,
			allowNegative      : true
		});			
		
		
		if(($("#cpf").val() == "") || ($("#cd_registro_empregado").val() == ""))
		{
			getParticipanteNome();
		}
		else
		{
			$("#ob_cadastro_fundacao").hide();
		}
		
		setResponsavelLegal();
		setFormaPagamento();
		
		getAssinaturaStatus();
		
		getInfoParticipante();
		getCampanha();
		getCadastroInstituidor();
		
		listaDependente();
		listaAlteracao();		
		listaDependenteParticipante();
		
	});

	
	if( '<?php echo ($acesso_especial)?'sim':'nao'; ?>'=='sim' )
	{
		acesso_especial();
	}	
	<?php
		echo ($_SESSION['F_TP_USUARIO'] != "F" ? '$("#tr_inscrito").hide();' : '');
	?>
</script>
	<?php
		include_once("restrito_janela_troca_senha.php");
	?>
</body>
<?
	include("analytics.php");
?>
</html>
<?php
	function getDelegacia($selecionado)
	{
		global $db;
		$qr_sql = "
					SELECT DISTINCT delegacia AS codigo,
					       delegacia AS descricao
					  FROM familia_previdencia.afceee_cadastro
				  ";
		$ob_resul = pg_query($db, $qr_sql);		
		$ret = "";
		while($ar_reg = pg_fetch_array($ob_resul))
		{
			
			$ret.= '<option value="'.$ar_reg['codigo'].'" '.(trim($selecionado) == $ar_reg['codigo'] ? "selected" : "" ).'>'.$ar_reg['descricao'].'</option>';
		}		
		
		return $ret;
	}
	
	function getSituacao($selecionado)
	{
		global $db;
		$qr_sql = "
					SELECT cd_cadastro_situacao AS codigo, 
					       ds_cadastro_situacao AS descricao
					  FROM familia_previdencia.cadastro_situacao
					 WHERE dt_exclusao IS NULL
					 ORDER BY ds_cadastro_situacao
				  ";
		$ob_resul = pg_query($db, $qr_sql);		
		$ret = "";
		while($ar_reg = pg_fetch_array($ob_resul))
		{
			
			$ret.= '<option value="'.$ar_reg['codigo'].'" '.(trim($selecionado) == $ar_reg['codigo'] ? "selected" : "" ).'>'.$ar_reg['descricao'].'</option>';
		}		
		
		return $ret;
	}	
	
	function getCidade($selecionado)
	{
		global $db;
		$qr_sql = "
					SELECT DISTINCT cidade AS codigo,
					       cidade AS descricao
					  FROM familia_previdencia.cadastro
					 WHERE dt_exclusao IS NULL
					 ORDER BY cidade
				  ";
		$ob_resul = pg_query($db, $qr_sql);		
		$ret = "";
		while($ar_reg = pg_fetch_array($ob_resul))
		{
			
			$ret.= '<option value="'.$ar_reg['codigo'].'" '.(trim($selecionado) == $ar_reg['codigo'] ? "selected" : "" ).'>'.$ar_reg['descricao'].'</option>';
		}		
		
		return $ret;
	}	
	
	function getGrauParentesco($selecionado)
	{
		global $db;
		$qr_sql = "
					SELECT cd_grau_parentesco AS codigo,
					       ds_grau_parentesco AS descricao
					  FROM familia_previdencia.grau_parentesco
                     ORDER BY ds_grau_parentesco					  
				  ";
		$ob_resul = pg_query($db, $qr_sql);		
		$ret = "";
		while($ar_reg = pg_fetch_array($ob_resul))
		{
			
			$ret.= '<option value="'.$ar_reg['codigo'].'" '.(trim($selecionado) == $ar_reg['codigo'] ? "selected" : "" ).'>'.$ar_reg['descricao'].'</option>';
		}		
		
		return $ret;
	}	

	function getEstadoCivil($selecionado)
	{
		global $db;
		$qr_sql = "
					SELECT cd_estado_civil AS codigo,
					       ds_estado_civil AS descricao
					  FROM familia_previdencia.estado_civil
                     ORDER BY ds_estado_civil					  
				  ";
		$ob_resul = pg_query($db, $qr_sql);		
		$ret = "";
		while($ar_reg = pg_fetch_array($ob_resul))
		{
			
			$ret.= '<option value="'.$ar_reg['codigo'].'" '.(trim($selecionado) == $ar_reg['codigo'] ? "selected" : "" ).'>'.$ar_reg['descricao'].'</option>';
		}		
		
		return $ret;
	}

	function getContatoForma($selecionado)
	{
		global $db;
		$qr_sql = "
					SELECT cd_contato_forma AS codigo,
					       ds_contato_forma AS descricao
					  FROM familia_previdencia.contato_forma
                     ORDER BY ds_contato_forma					  
				  ";
		$ob_resul = pg_query($db, $qr_sql);		
		$ret = "";
		while($ar_reg = pg_fetch_array($ob_resul))
		{
			
			$ret.= '<option value="'.$ar_reg['codigo'].'" '.(trim($selecionado) == $ar_reg['codigo'] ? "selected" : "" ).'>'.$ar_reg['descricao'].'</option>';
		}		
		
		return $ret;
	}

	function getContatoTipo($selecionado)
	{
		global $db;
		$qr_sql = "
					SELECT cd_contato_tipo AS codigo,
					       ds_contato_tipo AS descricao
					  FROM familia_previdencia.contato_tipo
                     ORDER BY ds_contato_tipo	  
				  ";
		$ob_resul = pg_query($db, $qr_sql);		
		$ret = "";
		while($ar_reg = pg_fetch_array($ob_resul))
		{
			
			$ret.= '<option value="'.$ar_reg['codigo'].'" '.(trim($selecionado) == $ar_reg['codigo'] ? "selected" : "" ).'>'.$ar_reg['descricao'].'</option>';
		}		
		
		return $ret;
	}	
	
	function getUF($selecionado)
	{
		global $db;
		$qr_sql = "
					SELECT cd_uf AS codigo,
					       cd_uf AS descricao
					  FROM geografico.uf
                     ORDER BY cd_uf	  
				  ";
		$ob_resul = pg_query($db, $qr_sql);		
		$ret = "";
		while($ar_reg = pg_fetch_array($ob_resul))
		{
			
			$ret.= '<option value="'.$ar_reg['codigo'].'" '.(trim($selecionado) == $ar_reg['codigo'] ? "selected" : "" ).'>'.$ar_reg['descricao'].'</option>';
		}		
		
		return $ret;
	}		
?>