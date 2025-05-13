<?php
set_title('Programas e Projetos');
$this->load->view('header');
?>
<script>
	function filtrar()
	{
		$("#result_div").html("<?= loader_html() ?>");
				
		$.post("<?= site_url('gestao/projeto/listar') ?>",
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
		    "DateTimeBR",
		    "CaseInsensitiveString",
		    null,
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
	
	function ir_lista_relatorio()
	{
		location.href = "<?= site_url('gestao/projeto/relatorio') ?>";
	}

	function novo()
	{
		location.href = "<?= site_url('gestao/projeto/cadastro') ?>";
	}
	
	$(function(){
		filtrar();
	});
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');
	$abas[] = array('aba_lista_relatorio', 'Lista Relatório', FALSE, 'ir_lista_relatorio();');

	$config['button'][] = array('Novo Projeto', 'novo();');

	echo aba_start($abas);
		echo form_list_command_bar($config);
		echo form_start_box_filter(); 
			echo filter_date_interval('dt_inclusao_ini', 'dt_inclusao_fim', 'Dt. Inclusão :');
			echo filter_dropdown('cd_gerencia_resposanvel', 'Responsável', $gerencia);
			echo filter_dropdown('cd_projeto', 'Projeto :', $projetos);
	    echo form_end_box_filter();
		echo '<div id="result_div"></div>';
		echo br(2);
	echo aba_end();

	$this->load->view('footer');
?>