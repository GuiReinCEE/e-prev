<?php
set_title('Indisponibilidade de Sistemas');
$this->load->view('header');
?>
<script>
	function filtrar()
	{
		$('#result_div').html("<?= loader_html() ?>");

		$.post('<?= site_url('servico/indisp_sistemas/listar') ?>',
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
			'Number',
			'DateTimeBR'
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
		location.href = "<?= site_url('servico/indisp_sistemas/cadastro') ?>";
	}
</script>
<?php
$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

$config['button'][] = array('Novo Mês', 'novo();');

echo aba_start($abas);
	echo form_list_command_bar($config);
	echo form_start_box_filter(); 
		//echo filter_date_interval('dt_indisp_sistemas_renovacao_ini', 'dt_indisp_sistemas_renovacao_fim', 'Dt. Expiração: ');
		//echo filter_dropdown('cd_indisp_sistemas_tipo', 'Tipo Controle:', $tipo_indisp_sistemas);
	echo form_end_box_filter();
	echo '<div id="result_div"></div>';
	echo br(2);
echo aba_end(); 
$this->load->view('footer');
?>