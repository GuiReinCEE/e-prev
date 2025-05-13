<?php
	set_title('Documentos Recebidos');
	$this->load->view('header');
?>
<script>
	function filtrar()
	{
		$("#result_div").html("<?= loader_html() ?>");

		$.post("<?= site_url('cadastro/sg_documento_recebido/listar') ?>",
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

	function novo()
	{
		location.href = "<?= site_url('cadastro/sg_documento_recebido/cadastro') ?>";
	}

	$(function(){
		filtrar();
	});
</script>

<?php
	$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

	$config['button'][] = array('Novo Documento', 'novo()');

	echo aba_start($abas);
		echo form_list_command_bar($config);
		echo form_start_box_filter('filter_bar', 'Filtros');
			echo filter_integer_ano('ano', 'numero', 'Ano/N�mero:', date('Y'));
			echo filter_date_interval('data_ini', 'data_fim', 'Data:');
			echo filter_text('remetente', 'Remetente:', '', 'style="width:300px;"');
			echo filter_text('destino', 'Destino:', '', 'style="width:300px;"');
		echo form_end_box_filter();
		echo '<div id="result_div"></div>';
		echo br();
	echo aba_end(); 
	$this->load->view('footer');
?>