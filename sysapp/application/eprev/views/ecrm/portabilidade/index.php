<?php
	set_title('Portabilidade');
	$this->load->view('header');
?>
<script>
	function filtrar()
	{
		$("#result_div").html("<?= loader_html() ?>");

		$.post("<?= site_url('ecrm/portabilidade/listar') ?>",
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
			'DateTimeBR',
			"RE",
			"CaseInsensitiveString",
			null,
			'DateTimeBR',
			null,
			'DateBR'

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

	function novo()
	{
		location.href = "<?= site_url('ecrm/portabilidade/cadastro') ?>";
	}

	$(function() {
        filtrar();
	});
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

	$config['button'][] = array('Nova', 'novo();');

	echo aba_start($abas);
	    echo form_list_command_bar($config);
	    echo form_start_box_filter();
			echo filter_date_interval('dt_inclusao_ini', 'dt_inclusao_fim' , 'Dt. Cadastro:');
			echo filter_date_interval('dt_acompanhamento_ini', 'dt_acompanhamento_fim' , 'Dt. Último Acompanhamento:');
			echo filter_dropdown('cd_portabilidade_status', 'Status:', $status);
			echo filter_participante(array('cd_empresa','cd_registro_empregado','seq_dependencia', 'nome_participante'), 'RE:');
	    echo form_end_box_filter();
		echo '<div id="result_div"></div>';
		echo br(2);
	echo aba_end();

	$this->load->view('footer'); 
?>