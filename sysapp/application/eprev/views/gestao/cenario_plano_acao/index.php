<?php
	set_title('Cenário Plano de Ação');
	$this->load->view('header');
?>
<script>
	function filtrar()
	{
		$("#result_div").html("<?= loader_html() ?>");
				
		$.post("<?= site_url('gestao/cenario_plano_acao/listar') ?>",
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
			"CaseInsensitiveString",
			"CaseInsensitiveString",
			"DateBR",
			"DateBR",
			"DateBR",
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
					
	$(function(){
		filtrar();
	});
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

	echo aba_start($abas);
		echo form_list_command_bar();
		echo form_start_box_filter(); 
			echo filter_dropdown('cd_cenario', 'Cenário:', $titulo);
			echo filter_dropdown('cd_gerencia_responsavel', 'Gerencia Responsavel:', $gerencia);
			echo filter_date_interval('dt_prazo_previsto_ini', 'dt_prazo_previsto_fim', 'Dt. Prazo Previsto:');
	    	echo filter_date_interval('dt_verificacao_eficacia_ini', 'dt_verificacao_eficacia_fim', 'Dt. Verificação Eficácia:');
	    	echo filter_date_interval('dt_validacao_eficacia_ini', 'dt_validacao_eficacia_fim', 'Dt. Validação Eficácia:');
	    echo form_end_box_filter(); 
		echo '<div id="result_div"></div>';
		echo br(2);
	echo aba_end();

	$this->load->view('footer');
?>