<?php
	set_title('Gerência - Unidade');
	$this->load->view('header');
?>
<script>
	 function ir_mapa()
    {
        location.href = "<?= site_url('servico/gerencia_unidade/mapa')?>";
    }

	function filtrar()
	{
		$("#result_div").html("<?= loader_html() ?>");
				
		$.post("<?= site_url('servico/gerencia_unidade/listar') ?>",
		$("#filter_bar_form").serialize(),
		function(data)
		{
			$("#result_div").html(data);
			configure_result_table();
		});	
	}

	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById('table-1'),
		[
		    'CaseInsensitiveString',
		    'CaseInsensitiveString',
		    null,
		    'CaseInsensitiveString',
		    'CaseInsensitiveString',
		    'CaseInsensitiveString',
		    'CaseInsensitiveString',
		    'DateBr'
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
		ob_resul.sort(0, false);
	}

	function novo()
    {
        location.href = "<?= site_url('servico/gerencia_unidade/cadastro') ?>";
    }

	$(function(){

		if($('#fl_tipo').val() == '')
		{
			$('#fl_tipo').val('DIV');
		}

		filtrar();
	});
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');
	$abas[] = array('aba_mapa', 'Mapa', FALSE, 'ir_mapa();');

	$config['button'][] = array('Nova Gerência', 'novo();');

	echo aba_start($abas);
		echo form_list_command_bar($config);
		echo form_start_box_filter(); 
			echo filter_dropdown('fl_tipo', 'Tipo:', $tipo);
			echo filter_dropdown('fl_area', 'Diretoria:', $diretoria);
		echo form_end_box_filter();
		echo '<div id="result_div"></div>';
		echo br(2);
	echo aba_end();

	$this->load->view('footer');
?>