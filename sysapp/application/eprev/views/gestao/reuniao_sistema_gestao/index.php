<?php
set_title('Reunião Sistema de Gestão');
$this->load->view('header');
?>
<script>
	function filtrar()
	{
		$("#result_div").html("<?= loader_html() ?>");
				
		$.post("<?= site_url('gestao/reuniao_sistema_gestao/listar') ?>",
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
		    "DateBR",
		    "CaseInsensitiveString",
		    null,
		    "DateTimeBR",
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
					
	function novo()
	{
		location.href = "<?= site_url('gestao/reuniao_sistema_gestao/cadastro') ?>";
	}

	$(function(){
		filtrar();
	});
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

	$config['button'][] = array('Nova Reunião', 'novo();');

	echo aba_start($abas);
		echo form_list_command_bar(($fl_permissao ? $config : array()));
		echo form_start_box_filter(); 
			echo filter_dropdown('cd_reuniao_sistema_gestao_tipo', 'Tipo:', $tipo);
			echo filter_date_interval('dt_ini', 'dt_fim', 'Dt. Reunião:');
	    echo form_end_box_filter();
		echo '<div id="result_div"></div>';
		echo br(2);
	echo aba_end();

	$this->load->view('footer');
?>