<?php
	set_title('Contrato Digital');
	$this->load->view('header');
?>
<script>
	function filtrar()
	{
		$("#result_div").html("<?= loader_html() ?>");
				
		$.post("<?= site_url('ecrm/contrato_digital/listar') ?>",
		$("#filter_bar_form").serialize(),
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
			'Number',
			'RE',
		    "CaseInsensitiveString", 
		    "CaseInsensitiveString", 
		    "DateTimeBR",
		    "DateTimeBR",
		    "DateTimeBR",
		    "DateTimeBR",
		    "DateTimeBR",
			'Number'
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

	$(function(){

		if($("#fl_pendente").val() == "")
		{
			$("#fl_pendente").val("<?= $fl_pendente ?>");
			$("#fl_pendente").change();	
		}

		if($("#cd_empresa").val() == "")
		{
			$("#cd_empresa").val("<?= $cd_empresa ?>");
		}

		if($("#cd_registro_empregado").val() == "")
		{
			$("#cd_registro_empregado").val("<?= $cd_registro_empregado ?>");
		}

		if($("#seq_dependencia").val() == "")
		{
			$("#seq_dependencia").val("<?= $seq_dependencia ?>");
		}

			
		filtrar();
	});

</script>

<?php
	$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

	echo aba_start($abas);
		echo form_start_box_filter('filter_bar', 'Filtros', TRUE);
			echo filter_cpf('cpf', 'CPF:');

			echo form_default_row('','','');
			echo filter_participante(array('cd_empresa','cd_registro_empregado','seq_dependencia', 'nome'), "Participante:", array(), TRUE, TRUE);
			echo filter_text('nome', 'Nome:');
			
			echo form_default_row('','','');	
			echo filter_dropdown('fl_pendente', 'Pendente:', $ar_pendente);
			echo filter_dropdown('fl_pendente_participante', 'Pendente Participante:', $ar_pendente_participante);
			echo filter_dropdown('fl_concluido', 'Concluído (Assinado):', $ar_concluido);
			echo filter_dropdown('fl_encerrado', 'Encerrado (Cancelado/Finalizado):', $ar_encerrado);
			
			echo form_default_row('','','');
			echo filter_date_interval('dt_inclusao_ini', 'dt_inclusao_fim', 'Dt Inclusão:');
			echo filter_date_interval('dt_limite_ini', 'dt_limite_fim', 'Dt Limite:');
			echo filter_date_interval('dt_concluido_ini', 'dt_concluido_fim', 'Dt Concluído:');
			echo filter_date_interval('dt_cancelado_ini', 'dt_cancelado_fim', 'Dt Cancelado:');
			echo filter_date_interval('dt_finalizado_ini', 'dt_finalizado_fim', 'Dt Finalizado:');
			
		echo form_end_box_filter();
		echo '<div id="result_div" style="text-align: center;"></div>';
		echo br(5);
	echo aba_end();

$this->load->view('footer');
?>