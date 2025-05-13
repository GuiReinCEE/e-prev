<?php
set_title('Pauta CCI - Pesquisa Assunto');
$this->load->view('header');
?>
<script>
	function filtrar()
	{
		$("#result_div").html("<?= loader_html() ?>");
				
		$.post("<?= site_url('gestao/pauta_cci/pesquisa_listar') ?>",
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
		    'Number',
		    'DateTimeBR',
		    'DateTimeBR',
		    'CaseInsensitiveString',
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
		ob_resul.sort(0, true);
	}			

	function ir_lista()
	{
		location.href = '<?= site_url('gestao/pauta_cci') ?>';
	}

	$(function(){
		filtrar();
	});

	$(function(){
		configure_result_table();
	})
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_pesquisa', 'Pesquisa', TRUE, "location.reload();");

echo aba_start($abas);
	echo form_list_command_bar(array());
	echo form_start_box_filter(); 
		echo filter_integer('nr_pauta_cci', 'Número da Ata :');
		echo filter_date_interval('dt_pauta_cci_ini', 'dt_pauta_cci_fim', 'Dt. Reunião :');
		echo filter_date_interval('dt_pauta_cci_fim_ini', 'dt_pauta_cci_fim_fim', 'Dt. Reunião Encerramento :');
		echo filter_text('ds_pauta_cci_assunto', 'Assunto : ','' , 'style="width:350px;"');
    echo form_end_box_filter();

	echo '<div id="result_div"></div>';
	echo br(5);
echo aba_end();

$this->load->view('footer');
?>