<?php
	set_title('Reclamações e Sugestão - Cadastro');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array('nome', 'cd_plano', 'cd_reclamacao_origem', 'tipo', 'cd_reclamacao_programa', 'descricao'), 'valida_inf(form)'); ?>
	
	function ir_lista()
	{
		location.href = "<?= site_url('ecrm/reclamacao') ?>";
	}

	function ir_anexo()
	{
		location.href = "<?= site_url('ecrm/reclamacao/anexo/'.$row['numero'].'/'.$row['ano'].'/'.$row['tipo']) ?>";
	}
	
	function ir_atendimento()
	{
		location.href = "<?= site_url('ecrm/reclamacao/atendimento/'.$row['numero'].'/'.$row['ano'].'/'.$row['tipo']) ?>";
	}	

	function ir_prorrogacao()
	{
		location.href = "<?= site_url('ecrm/reclamacao/prorrogacao/'.$row['numero'].'/'.$row['ano'].'/'.$row['tipo']) ?>";
	}

	function ir_reencaminhamento()
	{
		location.href = "<?= site_url('ecrm/reclamacao/reencaminhamento/'.$row['numero'].'/'.$row['ano'].'/'.$row['tipo']) ?>";
	}
	
	function ir_acao()
	{
		location.href = "<?= site_url('ecrm/reclamacao/acao/'.$row['numero'].'/'.$row['ano'].'/'.$row['tipo']) ?>";
	}

	function ir_acompanhamento()
	{
		location.href = "<?= site_url('ecrm/reclamacao/acompanhamento/'.$row['numero'].'/'.$row['ano'].'/'.$row['tipo']) ?>";
	}

	function ir_retorno()
	{
		location.href = "<?= site_url('ecrm/reclamacao/retorno/'.$row['numero'].'/'.$row['ano'].'/'.$row['tipo']) ?>";
	}

	function ir_validacao()
	{
		location.href = "<?= site_url('ecrm/reclamacao/validacao_comite/'.$row['numero'].'/'.$row['ano'].'/'.$row['tipo']) ?>";
	}			
	
	function ir_parecer_final()
	{
		location.href = "<?= site_url('ecrm/reclamacao/parecer_comite_avaliacao/'.$row['numero'].'/'.$row['ano'].'/'.$row['tipo']) ?>";
	}
	
	function cancelar_reclamacao()
	{
		var confirmacao = 'Deseja cancelar a Reclamação?\n\n'+
                'Clique [Ok] para Sim\n\n'+
                'Clique [Cancelar] para Não\n\n';
        
        if(confirm(confirmacao))
		{
			location.href = "<?= site_url('ecrm/reclamacao/cancelar_reclamacao/'.$row['numero'].'/'.$row['ano'].'/'.$row['tipo']) ?>";
		}
	}
	
	function valida_inf(form)
	{
		if($('#cd_registro_empregado').val() == 0)
		{
			if(($("#email_novo").val() != "") || ($("#telefone_1").val() != "") || ($("#telefone_2").val() != ""))
			{
				if(confirm("Deseja Salvar?"))
				{
					$('form').submit();
				}
			}
			else
			{
				alert("Informe o email ou um dos telefone.");
				return false;
			}
		}
		else
		{
			if(confirm("Deseja Salvar?"))
			{
				form.submit();
			}
		}
	}

	function get_usuarios()
	{
		var cd_divisao = $("#cd_divisao").val();
		
		$.post("<?= site_url('ecrm/reclamacao/get_usuarios/') ?>",
		{
			cd_divisao : cd_divisao
		},
		function(data)
		{ 
			var select = $('#cd_usuario_responsavel'); 
			
			if(select.prop) 
			{
				var options = select.prop('options');
			}
			else
			{
				var options = select.attr('options');
			}
			   
			$('option', select).remove();
			   
			options[options.length] = new Option('Selecione', '');
			   
			$.each(data, function(val, text) {
				options[options.length] = new Option(text.text, text.value);
			});
		}, 'json');
	}
	
	function listar_reclamacao_anterior()
	{
		$("#result_div").html("<?= loader_html() ?>");

		$.post("<?= site_url('ecrm/reclamacao/listar_reclamacao_anterior') ?>",
		{
			cd_reclamacao          : $("#cd_reclamacao").val(),
			numero                 : <?= $row['numero'] ?>,
			ano                    : <?= $row['ano'] ?>,
			tipo                   : "<?= $row['tipo'] ?>",
			cd_empresa             : $("#cd_empresa").val(),
			cd_registro_empregado  : $("#cd_registro_empregado").val(),
			seq_dependencia        : $("#seq_dependencia").val()
		},
		function(data)
		{
			$("#result_div").html(data);
			configure_result_table();
		});
	}	
	
	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),
		[
			"CaseInsensitiveString",
			"CaseInsensitiveString",
			"DateTimeBR",
			"CaseInsensitiveString",
			"DateBR",
			"DateBR",
			"DateTimeBR",
			"DateTimeBR",
			"CaseInsensitiveString"
		]);
		ob_resul.onsort = function ()
		{
			var rows = ob_resul.tBody.rows;
			var l = rows.length;
			for (var i = 0; i < l; i++)
			{
				removeClassName( rows[i], i % 2 ? "sort-par" : "sort-impar" );
				addClassName( rows[i], i % 2 ? "sort-impar" : "sort-par" );
			}
		};
		ob_resul.sort(0, true);
	}

	function carregar_dados_participante(data)
    {
		$('#nome').val(data.nome);
		$('#cd_plano').val(data.cd_plano);
		
		if(data.email != '')
		{
			$('#email_novo').val(data.email);
		}
		else 
		{
			$('#email_novo').val(data.email_profissional);
		}
		
		$('#telefone_1').val('('+data.ddd.substr(1,2)+') '+data.telefone);
		$('#telefone_2').val('('+data.ddd_celular.substr(1,2)+') '+data.celular);
		
		if(($("#cd_empresa").val() != "") && (parseInt($("#cd_registro_empregado").val()) > 0) && ($("#seq_dependencia").val() != ""))
		{
			listar_reclamacao_anterior();
		}				
	}

    function excluir()
    {
    	var confirmacao = 'Deseja excluir esta reclamação?\n\n'+
                          'Clique [Ok] para Sim\n\n'+
                          'Clique [Cancelar] para Não\n\n';

        if(confirm(confirmacao))
		{
			location.href = "<?= site_url('ecrm/reclamacao/excluir/'.$row['numero'].'/'.$row['ano'].'/'.$row['tipo']) ?>";
		}
    }

    function get_ferias()
	{
		var cd_usuario = $("#cd_usuario_responsavel").val();

		ajax_ferias(cd_usuario);
	}

	function ajax_ferias(cd_usuario)
	{
		$.post("<?= site_url('ecrm/reclamacao/get_ferias') ?>",
		{
			cd_usuario : cd_usuario
		},
		function(data)
		{ 
			if(data.dt_ferias_ini !== undefined && data.dt_ferias_fim !== undefined)
			{
				alert("Atenção! \n\nUsuário de férias de "+data.dt_ferias_ini+" até "+data.dt_ferias_fim);
			}
		}, 'json');
	}

	function salvar_atendimento(form)
	{
		if($("#cd_usuario_responsavel").val() == "")
		{
			alert("Informe os campos obrigatórios! \n\n(os campos obrigatórios tem um * logo após a identificação).\n\n");
			return false;
		}
		else
		{
			if(confirm("Salvar o Atendente?\n\n"))
			{
				form.submit();
			}	
			return true;
		}
	}
	
	$(function() 
	{		
		$("#msg_anterior_row").hide();

		if(($("#cd_empresa").val() != "") && (parseInt($("#cd_registro_empregado").val()) > 0) && ($("#seq_dependencia").val() != ""))
		{
			consultar_participante__cd_empresa();
			listar_reclamacao_anterior();
		}

		default_conceito_box_box_recolher();
	});
</script>
<style>
    #conceito_item {
        white-space:normal !important;
    }
</style>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_reclamacao', 'Cadastro', TRUE, 'location.reload();');

	if(trim($row['cd_reclamacao']) != '')
	{
		/*
		if($permissao['fl_aba_atendimento'])
		{	
			$abas[] = array('aba_atendimento', 'Atendimento', FALSE, 'ir_atendimento();');
		}
		*/

		if($permissao['fl_aba_prorrogacao'])
		{	
			$abas[] = array('aba_reencaminahemnto', 'Reencaminhamento', FALSE, 'ir_reencaminhamento();');
			$abas[] = array('aba_prorrogacao', 'Prorrogação', FALSE, 'ir_prorrogacao();');
		}

		$abas[] = array('aba_acompanhamento', 'Acompanhamento', FALSE, 'ir_acompanhamento();');
		$abas[] = array('aba_anexo', 'Anexo', FALSE, 'ir_anexo();');

		if($permissao['fl_aba_acao'])
		{
			$abas[] = array('aba_acao', 'Ação', FALSE, 'ir_acao();');
		}

		if($permissao['fl_aba_retorno'])
		{
			$abas[] = array('aba_retorno', 'Retorno', FALSE, 'ir_retorno();');
		}

		if($permissao['fl_aba_comite'])
		{
			$abas[] = array('aba_validacao_comite', 'Validação Comitê', FALSE, 'ir_validacao();');
		}

		if($permissao['fl_aba_parecer_final'])
		{
			$abas[] = array('aba_parecer_final', 'Parecer Final', FALSE, 'ir_parecer_final();');
		}	
	}

	$c['emp']['id']    = 'cd_empresa';
	$c['re']['id']     = 'cd_registro_empregado';
	$c['seq']['id']    = 'seq_dependencia';
	$c['emp']['value'] = $row['cd_empresa'];
	$c['re']['value']  = $row['cd_registro_empregado'];
	$c['seq']['value'] = $row['seq_dependencia'];
	$c['caption']      = 'Participante: (*)';
	$c['callback']     = 'carregar_dados_participante';

	$tipo = array(
		array('text' => 'Reclamação', 'value' => 'R'),
		array('text' => 'Sugestão', 'value' => 'S')
	);
	
	echo aba_start($abas);
		echo form_start_box('default_conceito_box', 'Conceito da Tela');
			echo form_default_row('conceito', 'Cadastro:', 'Em descrição registrar a reclamação de forma clara e objetiva com dados necessários para uma  análise.');
			echo form_default_row('conceito', 'Atendimento:', 'Indicar o responsável pelo tratamento da reclamação.');
		echo form_end_box('default_conceito_box');

		echo form_open('ecrm/reclamacao/salvar_reclamacao');
			echo form_start_box('default_box', 'Cadastro');
				echo form_default_hidden('cd_atendimento', '', $row['cd_atendimento']);
				echo form_default_hidden('cd_reclamacao', '', $row['cd_reclamacao']);
				echo form_default_hidden('numero', '', $row['numero']);
				echo form_default_hidden('ano', '', $row['ano']);

				if(trim($row['cd_reclamacao']) != '')
				{
					echo form_default_row('numero', 'Número:', $row['numero']);
					echo form_default_row('ano', 'Ano:', $row['ano']);
					echo form_default_row('dt_inclusao', 'Dt. Cadastro:', $row['dt_inclusao']);
					echo form_default_row('ds_usuario_inclusao', 'Usuário:', $row['ds_usuario_inclusao']);
					
					if(trim($row['dt_cancela']) != '')
					{
						echo form_default_row('dt_cancela', 'Dt. Cancelado:', $row['dt_inclusao']);
						echo form_default_row('ds_usuario_cancela', 'Usuário:', $row['ds_usuario_cancela']);
					}

					if(trim($row['dt_exclusao']) != '')
					{
						echo form_default_row('dt_exclusao', 'Dt. Exclusão:', $row['dt_exclusao']);
					}

					if(intval($row['cd_usuario_responsavel']) > 0)
					{
						echo form_default_row('dt_prazo_acao', 'Dt. Prazo Ação:', '<span class="label label-inverse">'.$row['dt_prazo_acao'].'</span>');
						
						if(trim($row['dt_prorrogacao_acao']) != '')
						{
							echo form_default_row('dt_prorrogacao_acao', 'Dt. Prorrogação Ação:', '<span class="label label-info">'.$row['dt_prorrogacao_acao'].'</span>');
						}

						echo form_default_row('dt_prazo', 'Dt. Prazo Classificação:', '<span class="label label-inverse">'.$row['dt_prazo'].'</span>');
						
						if(trim($row['dt_prorrogacao']) != '')
						{
							echo form_default_row('dt_prorrogacao', 'Dt. Prorrogação Classificação:', '<span class="label label-info">'.$row['dt_prorrogacao'].'</span>');
						}
					}
				}
					
				echo form_default_participante_trigger($c);
				echo form_default_row('msg_anterior', '', '<span id="msg_anterior" style="font-size: 140%; color:red; font-weight:bold;"></span><BR><BR>');	
				echo form_default_text('nome', 'Nome: (*)', $row, 'style="width:500px;"');
				echo form_default_dropdown('cd_plano', 'Plano: (*)', $planos, $row['cd_plano'], 'style="width:500px;"');
				echo form_default_text('email_novo', 'Email:', $row['email_novo'], 'style="width:500px;"');
				echo form_default_telefone('telefone_1', 'Telefone 1:', $row, 'style="width:500px;"');
				echo form_default_telefone('telefone_2', 'Telefone 2:', $row, 'style="width:500px;"');
				
				if(trim($row['cd_reclamacao']) != '')
				{
					echo form_default_row('email', 'Email:', $row['email']);
					echo form_default_row('email_profissional', 'Email Profissional:', $row['email_profissional']);
					echo form_default_row('telefone', 'Telefone:', $row['telefone']);
					echo form_default_row('ramal', 'Ramal:', $row['ramal']);
					echo form_default_row('celular', 'Celular:', $row['celular']);
					echo form_default_row('logradouro', 'Logradouro:', $row['logradouro']);
					echo form_default_row('bairro', 'Bairro:', $row['bairro']);
					echo form_default_row('cidade', 'Cidade:', $row['cidade']);
					echo form_default_row('uf', 'UF:', $row['uf']);
					echo form_default_row('cep', 'CEP:', $row['cep']);
				}
				
				echo form_default_dropdown('cd_reclamacao_origem', 'Origem: (*)', $origem, $row['cd_reclamacao_origem'], 'style="width:500px;"');
				
				if(trim($row['cd_reclamacao']) != '')
				{
					echo form_default_hidden('tipo', '', $row['tipo']);
					echo form_default_row('ds_tipo', 'Tipo:', $row['ds_tipo']);
				}
				else
				{
					echo form_default_dropdown('tipo', 'Tipo: (*)', $tipo, $row['tipo'], 'style="width:500px;"');
				}
				
				echo form_default_dropdown('cd_reclamacao_programa', 'Programa: (*)', $programa, $row['cd_reclamacao_programa'], 'style="width:500px;"');

				echo form_default_dropdown('cd_reclamacao_assunto', 'Assunto: (*)', $assunto, $row['cd_reclamacao_assunto'], 'style="width:500px;"');			

				echo form_default_textarea('descricao', 'Descricao: (*)', $row, 'style="width:500px; height: 70px;"');

			echo form_end_box('default_box');
			echo form_command_bar_detail_start();
				if(trim($row['cd_reclamacao']) == '' OR $permissao['fl_cadastro'])
				{
                    echo button_save('Salvar');

                    if(trim($row['cd_reclamacao']) != '' AND intval($cd_usuario) == 146) #VDORNELLES
                    {
                        echo button_save('Excluir', 'excluir()', 'botao_vermelho');
                    }

					if(trim($row['cd_reclamacao']) != '' AND gerencia_in(array('GRSC')))
					{
						echo button_save('Cancelar', 'cancelar_reclamacao();', 'botao_disabled');
					}			
				}
			echo form_command_bar_detail_end();
		echo form_close();
		echo br();

		if(trim($row['cd_reclamacao']) != '')
		{
			echo form_open('ecrm/reclamacao/salvar_atendimento');
				echo form_start_box('default_atendimento_box', 'Atendimento');
					echo form_default_hidden('cd_atendimento', '', $row['cd_atendimento']);
					echo form_default_hidden('cd_reclamacao', '', $row['cd_reclamacao']);
					echo form_default_hidden('numero', '', $row['numero']);
					echo form_default_hidden('ano', '', $row['ano']);
					echo form_default_hidden('tipo', '', $row['tipo']);

					if(trim($atendimento['dt_atualizacao']) != '')
					{
						echo form_default_row('dt_inclusao', 'Dt. Encaminhado:', $atendimento['dt_inclusao']);
						echo form_default_row('ds_usuario_inclusao', 'Usuário:', $atendimento['ds_usuario_inclusao']);
					}

					if(trim($atendimento['dt_atualizacao']) != '')
					{
						echo form_default_row('dt_atualizacao', 'Dt. Atualização:', $atendimento['dt_atualizacao']);
						echo form_default_row('ds_usuario_atualizacao', 'Usuário:', $atendimento['ds_usuario_atualizacao']);		
					}

					echo form_default_gerencia('cd_divisao', 'Gerência: (*)', $atendimento['cd_divisao'], 'onchange="get_usuarios()"');
					echo form_default_dropdown('cd_usuario_responsavel', 'Responsável: (*)', $usuarios, $atendimento['cd_usuario_responsavel'], 'onchange="get_ferias()"'); 

				echo form_end_box('default_atendimento_box');

				echo form_command_bar_detail_start();
					if(trim($atendimento['dt_inclusao']) == '')
					{		
						echo button_save('Salvar', 'salvar_atendimento(this.form);');
					}
				echo form_command_bar_detail_end();
			echo form_close();
			echo br();
		}

		echo '<div id="result_div"></div>';
		echo br(2);
	echo aba_end();
	$this->load->view('footer_interna');
?>