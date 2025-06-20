<?php
	set_title('Regulamento');
	$this->load->view('header');
?>
<script>
	function filtrar()
	{
		$("#result_div").html("<?= loader_html() ?>");

		$.post("<?= site_url('gestao/regulamento/listar') ?>",
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
			"CaseInsensitiveString",
			"CaseInsensitiveString",
			"CaseInsensitiveString",
			"CaseInsensitiveString",
			null,
			"DateBR",
			"Number",
			"DateBR",
			"DateBR",
			"CaseInsensitiveString",
			null,
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
		ob_resul.sort(7, true);
	}

	function novo()
	{
		location.href = "<?= site_url('gestao/regulamento/cadastro') ?>";
	}

	$(function() {
        filtrar();
	});
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

	$config['button'][] = array('Novo', 'novo();');

	echo aba_start($abas);
	    echo form_list_command_bar(gerencia_in(array('GC')) ? $config : array());
	    echo form_start_box_filter();
	    	echo filter_dropdown('cd_regulamento_tipo', 'Regulamento:', $regulamento);
			echo filter_date_interval('dt_aprovacao_cd_ini', 'dt_aprovacao_cd_fim', 'Dt. Aprova��o CD:');
			echo filter_date_interval('dt_aprovacao_previc_ini', 'dt_aprovacao_previc_fim', 'Dt. Aprova��o PREVIC:');
	    echo form_end_box_filter();
		echo '<div id="result_div"></div>';
		echo br(2);
	echo aba_end();

	$this->load->view('footer'); 
?>