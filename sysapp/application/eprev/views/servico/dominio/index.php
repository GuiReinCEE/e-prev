<?php
set_title('Controles TI');
$this->load->view('header');
?>
<script>
	function filtrar()
	{
		$('#result_div').html("<?= loader_html() ?>");

		$.post('<?= site_url('/servico/dominio/listar') ?>',
		$("#filter_bar_form").serialize(),	
		function(data)
		{
			$('#result_div').html(data);
			configure_result_table();
		});
	}

	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),
		[
			'CaseInsensitiveString',
			'CaseInsensitiveString',
			'DateBR',
			'CaseInsensitiveString'
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
		ob_resul.sort(2, false);
	}

	$(function(){
		filtrar();
	});

	function novo()
	{
		location.href = "<?= site_url('servico/dominio/cadastro') ?>";
	}
</script>
<?php
$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

$config['button'][] = array('Novo Controle TI', 'novo();');

echo aba_start($abas);
	echo form_list_command_bar($config);
	echo form_start_box_filter(); 
		echo filter_date_interval('dt_dominio_renovacao_ini', 'dt_dominio_renovacao_fim', 'Dt. Expiração: ');
		echo filter_dropdown('cd_dominio_tipo', 'Tipo Controle:', $tipo_dominio);
	echo form_end_box_filter();
	echo '<div id="result_div"></div>';
	echo br(2);
echo aba_end(); 
$this->load->view('footer');
?>