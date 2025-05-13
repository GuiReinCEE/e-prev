<?php
	set_title("Pauta SG - Pesquisa Assunto");
	$this->load->view("header");
?>
<script>
	function filtrar()
	{
		$("#result_div").html("<?= loader_html() ?>");
				
		$.post("<?= site_url('gestao/pauta_sg/pesquisa_listar') ?>",
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
		    "DateTimeBR",
		    "DateTimeBR",
		    "CaseInsensitiveString",
		    "CaseInsensitiveString",
		    "CaseInsensitiveString",
		    "Number",
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
		location.href = "<?= site_url('gestao/pauta_sg') ?>";
	}
	
	$(function(){
		if($("#dt_pauta_sg_ini").val() == '' || $("#dt_pauta_sg_fim").val() == '')
		{
			$("#dt_pauta_sg_ini_dt_pauta_sg_fim_shortcut").val("currentMonth");
			$("#dt_pauta_sg_ini_dt_pauta_sg_fim_shortcut").change();
		}
		
		filtrar();
	});
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_pesquisa', 'Pesquisa', TRUE, 'location.reload();');

	echo aba_start($abas);
		echo form_list_command_bar(array());
		echo form_start_box_filter(); 
		    echo filter_integer('nr_ata', 'Nº da Ata:');
			echo filter_dropdown('fl_sumula', 'Colegiado:', $sumula);
			echo filter_dropdown('fl_tipo_reuniao', 'Tipo Reunião:', $tipo_reuniao);
			echo filter_date_interval('dt_pauta_sg_ini', 'dt_pauta_sg_fim', 'Dt. Reunião:');
			echo filter_date_interval('dt_pauta_sg_fim_ini', 'dt_pauta_sg_fim_fim', 'Dt. Reunião Encerramento:');
			echo filter_text('ds_pauta_sg_assunto', 'Assunto:', '', 'style="width:350px;"');
	    echo form_end_box_filter();
		echo '<div id="result_div"></div>';
		echo br(2);
	echo aba_end();

	$this->load->view('footer');
?>