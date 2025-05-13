<?php
	set_title('Atendimento Retenção');
	$this->load->view('header');
?>
<script>
	<?php 
		if(intval($row['cd_atendimento_retencao']) == 0)
		{
			echo form_default_js_submit(array('cd_atendimento_retencao', 'cd_empresa', 'cd_registro_empregado', 'seq_dependencia'), 'valida_salvar(form)');
		}
		else
		{
			echo form_default_js_submit(array('ds_atendimento_retencao_acompanhamento'));
		}
	?>

	function valida_salvar(form)
	{
		var fl_enviar = true;
		var qt_anterior = $("#qt_anterior").val();

		if(qt_anterior > 0)
		{
			var confirmacao = "Já foi registrado uma retenção nesse mês para esse participante.\n\n"+
			              "Reseja registrar nova Rentenção?\n\n"+
                          "Clique [Ok] para Sim\n\n"+
                          "Clique [Cancelar] para Não\n\n"; 

			fl_enviar = confirm(confirmacao);
		}

		if(fl_enviar)
		{
			form.submit();
		}
	}

	function ir_lista()
    {
        location.href = "<?= site_url('ecrm/atendimento_retencao') ?>";
    }

    function configure_result_table_acom()
	{
		var ob_resul = new SortableTable(document.getElementById("table-2"),
		[
			"DateTimeBR",
		    "CaseInsensitiveString",
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

	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),
		[
			"Number",
			"Number",
		    "CaseInsensitiveString",
			"RE",
		    "CaseInsensitiveString",
		    "DateTimeBR",
		    "CaseInsensitiveString",
		    null
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

    function callback_buscar_retencao_anterior()
    {
    	$.post("<?= site_url('ecrm/atendimento_retencao/listar_retencao_anterior') ?>",
		{
			cd_atendimento_retencao : $("#cd_atendimento_retencao").val(),
			cd_empresa              : $("#cd_empresa").val(),
			cd_registro_empregado   : $("#cd_registro_empregado").val(),
			seq_dependencia         : $("#seq_dependencia").val()
		},
		function(data)
		{
			$("#result_div").html(data);
			configure_result_table();
		});	
    }

    $(function(){
    	if($("#cd_atendimento_retencao").val() > 0)
    	{
    		callback_buscar_retencao_anterior();
    		configure_result_table_acom();
    	}
    });
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');

	$participante = array('cd_empresa' => $row['cd_empresa'], 'cd_registro_empregado' => $row['cd_registro_empregado'], 'seq_dependencia' => $row['seq_dependencia']);

	$retido = array(
		array('text' => 'Não', 'value' => 'N'),
		array('text' => 'Sim', 'value' => 'S')
	);

	echo aba_start($abas);
		echo form_open('ecrm/atendimento_retencao/salvar');
			echo form_start_box('default_box', 'Cadastro');
				echo form_default_hidden('cd_atendimento_retencao', '', $row['cd_atendimento_retencao']);
				echo form_default_hidden('cd_atendimento', '', $row['cd_atendimento']);
			
				if(intval($row['cd_atendimento_retencao']) == 0)
				{
					echo form_default_participante(
						array('cd_empresa', 'cd_registro_empregado', 'seq_dependencia', 'nome_participante'), 
						'Participante:', 
						$participante, FALSE, TRUE, 'callback_buscar_retencao_anterior();');
					echo form_default_textarea('ds_descricao', 'Descrição:', $row['ds_descricao'], 'style="height:100px;"');

					echo form_default_dropdown('fl_retido', 'Retenção ocorreu:', $retido, $row['fl_retido']);	
				}
				else
				{
					echo form_default_hidden('cd_empresa', '', $row['cd_empresa']);
					echo form_default_hidden('cd_registro_empregado', '', $row['cd_registro_empregado']);
					echo form_default_hidden('seq_dependencia', '', $row['seq_dependencia']);

					echo form_default_row('re', 'RE:', $row['cd_empresa'].'/'.$row['cd_registro_empregado'].'/'.$row['seq_dependencia']);
					echo form_default_row('nome', 'Nome:', $row['nome']);

					//echo form_default_textarea('ds_atendimento_retencao_acompanhamento', 'Acompanhamento:(*)', '', 'style="height:100px;"');	
				}
			echo form_end_box('default_box');
			echo form_command_bar_detail_start();
				if(intval($row['cd_atendimento_retencao']) == 0)
				{
					echo button_save('Salvar');   
				}          
		    echo form_command_bar_detail_end();
		echo form_close();
		echo br();

		if(intval($row['cd_atendimento_retencao']) > 0)
		{
			$head = array(
				'Dt. Inclusão',
				'Acompanhamento',
				'Usuário'
			);

			$body = array();

			foreach ($collection as $item)
			{	
				$body[] = array(
					$item['dt_inclusao'],
					array(nl2br($item['ds_atendimento_retencao_acompanhamento']), 'text-align: justify;'),
					array($item['ds_usuario_inclusao'], 'text-align: left;')
				); 
			}

			$this->load->helper('grid');
			$grid = new grid();
			$grid->id_tabela = 'table-2';
			$grid->view_count = false;
			$grid->head = $head;
			$grid->body = $body;

			echo form_start_box('default_acompanhamento_box', 'Acompanhamento');
				echo $grid->render();
			echo form_end_box('default_acompanhamento_box');
		}

		echo form_start_box('default_anterior_box', 'Anteriores');
			echo '<div id="result_div"></div>';
		echo form_end_box('default_anterior_box');
		echo br(2);
    echo aba_end();

    $this->load->view('footer_interna');
?>