<?php
	set_title('Registro de Pendência de Documento');
	$this->load->view('header');
?>
<script>
	function filtrar()
	{
		$("#result_div").html("<?= loader_html() ?>");
				
		$.post("<?= site_url('ecrm/registro_pendencia_documento/listar') ?>",
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
			"Number",
			"RE",
			"CaseInsensitiveString",
			"Number",
		    "DateTimeBR",
		    "CaseInsensitiveString",
		    "DateTimeBR",
		    "DateTimeBR",
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
				removeClassName(rows[i], i % 2 ? "sort-par" : "sort-impar");
				addClassName(rows[i], i % 2 ? "sort-impar" : "sort-par");
			}
		};
		ob_resul.sort(4, true);
	}

	$(function(){
		if($("#fl_cancelamento").val() == '')
		{
			$("#fl_cancelamento").val("N");
		}

		if($("#fl_confirmacao").val() == '')
		{
			$("#fl_confirmacao").val("N");
		}

		filtrar();
	});
</script>
<?php

	$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

	$drop = array(
		array('value' => 'S', 'text' => 'Sim'),
		array('value' => 'N', 'text' => 'Não')
	);

	echo aba_start($abas);
		echo form_list_command_bar();
		echo form_start_box_filter(); 
			echo filter_participante(array('cd_empresa','cd_registro_empregado','seq_dependencia', 'nome_participante'), 'RE:', array(
                'cd_empresa'            => $cd_empresa, 
                'cd_registro_empregado' => $cd_registro_empregado, 
                'seq_dependencia'       => $seq_dependencia
            ));
			echo filter_date_interval('dt_inclusao_ini', 'dt_inclusao_fim', 'Dt. Encaminhamento:');
			echo filter_dropdown('fl_cancelamento', 'Cancelado:', $drop);
			echo filter_dropdown('fl_confirmacao', 'Confirmado:', $drop);
			echo filter_dropdown('fl_envio_participante', 'Envio ao Part.:', $drop);
		echo form_end_box_filter();
		echo '<div id="result_div"></div>';
		echo br(2);
	echo aba_end();

	$this->load->view('footer');
?>