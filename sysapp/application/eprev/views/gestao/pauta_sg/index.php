<?php
	set_title('Pauta SG');
	$this->load->view('header');
?>
<script>
	function filtrar()
	{
		$("#result_div").html("<?= loader_html() ?>");
				
		$.post("<?= site_url('gestao/pauta_sg/listar') ?>",
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
		    "CaseInsensitiveString",
		    "CaseInsensitiveString",
		    "CaseInsensitiveString",
		    "DateTimeBR",
		    "DateTimeBR",
		    "DateTimeBR",
		    "CaseInsensitiveString",
		    "Number",
		    "Number",
		    "Number",
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
					
	function novo()
	{
		location.href = "<?= site_url('gestao/pauta_sg/cadastro') ?>";
	}

	function ir_pesquisa()
	{
		location.href = "<?= site_url('gestao/pauta_sg/pesquisa') ?>";
	}
	
	$(function(){
		filtrar();
	});
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');
	$abas[] = array('aba_pesquisa', 'Pesquisa', FALSE, 'ir_pesquisa();');

	$config['button'][] = array('Nova Pauta', 'novo();');

	$aprovado = array(
		array('value' => 'S', 'text' => 'Sim'), 
		array('value' => 'N', 'text' => 'Não')
	);  

	echo aba_start($abas);
		echo form_list_command_bar($config);
		echo form_start_box_filter(); 
			echo filter_integer('nr_ata', 'Nº da Ata:');
			echo filter_dropdown('fl_sumula', 'Colegiado:', $sumula);
			echo filter_dropdown('fl_tipo_reuniao', 'Tipo Reunião:', $tipo_reuniao);
			echo filter_date_interval('dt_pauta_sg_ini', 'dt_pauta_sg_fim', 'Dt. Reunião:');
			echo filter_date_interval('dt_pauta_sg_fim_ini', 'dt_pauta_sg_fim_fim', 'Dt. Reunião Encerramento:');
		    echo filter_dropdown('fl_aprovado', 'Pauta Aprovada:', $aprovado);		
	    echo form_end_box_filter();
		echo '<div id="result_div"></div>';
		echo br(2);
	echo aba_end();

	$this->load->view('footer');
?>