<?php
	set_title('Controle de RDS');
	$this->load->view('header');
?>

<script>
	function filtrar()
	{
		$("#result_div").html("<?=loader_html()?>");
		
	    $.post("<?= site_url('gestao/controle_rds/listar') ?>",
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
			"DateBR",
			"CaseInsensitiveString",
			"CaseInsensitiveString",
			"Number",
			"DateBR",
			"DateTimeBR",
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
	    location.href = "<?= site_url('gestao/controle_rds/cadastro') ?>";
	}

	$(function(){
		if($("#dt_rds_ini").val() == "" || $("#dt_rds_fim").val() == "")
		{
			$("#dt_rds_ini_dt_rds_fim_shortcut").val("currentYear");
			$("#dt_rds_ini_dt_rds_fim_shortcut").change();
		}

		filtrar();
	});

</script>
<?php
	$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

	$config['button'][] = array('Nova RDS', 'novo()');

	echo aba_start($abas);
	    echo form_list_command_bar((gerencia_in(array('GC')) ? $config : array()));
	    echo form_start_box_filter();
			echo filter_integer_ano('nr_ano', 'nr_rds', 'Ano/Número:');
			echo filter_date_interval('dt_rds_ini', 'dt_rds_fim', 'Dt. RDS:');
			echo filter_text('ds_controle_rds', 'Descrição:', '', 'style="width:300px;"');
			echo filter_integer('nr_ata', 'Ata:');
			echo filter_date_interval('dt_reuniao_ini', 'dt_reuniao_fim', 'Dt. Reunião:');
	    echo form_end_box_filter();
		echo '<div id="result_div"></div>';
		echo br(4);
	echo aba_end();

	$this->load->view('footer'); 
?>