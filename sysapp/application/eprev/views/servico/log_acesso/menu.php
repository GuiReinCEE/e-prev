<?php
	set_title('Logs de Acessos');
	$this->load->view('header');
?>
<script>
	function ir_acesso()
	{
		location.href = "<?= site_url('servico/log_acesso') ?>";
	}

	function filtrar()
	{
		var nr_ano = $("#nr_ano").val();

		if(nr_ano != '' || dt_acesso_fim != '')
		{
			$("#result_div").html("<?= loader_html() ?>");
					
			$.post("<?= site_url('servico/log_acesso/listar_menu') ?>",
			$("#filter_bar_form").serialize(),
			function(data)
			{
				$("#result_div").html(data);
				configure_result_table();
			});	
		}
		else
		{
			alert("Informe o ano.");
		}
	}

	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),
		[
			"Number",
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
	
	$(function(){
		filtrar();
	});
</script>
<?php
	$abas[] = array('aba_acesso', 'Por Acesso', FALSE, 'ir_acesso();');
	$abas[] = array('aba_menu', 'Por Menu', TRUE, 'location.reload();');

	echo aba_start($abas);
		echo form_list_command_bar();
		echo form_start_box_filter();
			echo filter_integer('nr_ano', 'Ano:', date('Y')); 
			echo filter_mes('nr_mes', 'Mês:');
	    echo form_end_box_filter();
		echo '<div id="result_div"></div>';
		echo br(2);
	echo aba_end();

	$this->load->view('footer');
?>