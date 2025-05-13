<?php
set_title("Controle Carro");
$this->load->view("header");
?>
<script>
	function filtrar()
	{
		$("#result_div").html("<?= loader_html() ?>");
				
		$.post("<?= site_url('ecrm/controle_carro/listar') ?>",
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
		    "CaseInsensitiveString",
		    "DateTimeBR",
		    "DateTimeBR",
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
				removeClassName( rows[i], i % 2 ? "sort-par" : "sort-impar" );
				addClassName( rows[i], i % 2 ? "sort-impar" : "sort-par" );
			}
		};
		ob_resul.sort(3, true);
	}
					
	function novo()
	{
		location.href = "<?= site_url('ecrm/controle_carro/cadastro') ?>";
	}
	
	function excluir(cd_controle_carro)
	{	
		var confirmacao = "Deseja excluir o controle?\n\n"+
			"Clique [Ok] para Sim\n\n"+
			"Clique [Cancelar] para Não\n\n";

		if(confirm(confirmacao))
		{ 
			location.href = "<?= site_url('ecrm/controle_carro/excluir') ?>/" + cd_controle_carro;
		}
	}

	$(function(){
		filtrar();
	});
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

	$config['button'][] = array('Novo Itinerário', 'novo();');

	echo aba_start($abas);
		echo form_list_command_bar($config);
		echo form_start_box_filter(); 
			echo filter_date_interval('dt_saida_ini', 'dt_saida_fim', 'Dt. Saída:');
			echo filter_date_interval('dt_retorno_ini', 'dt_retorno_fim', 'Dt. Retorno:');
			echo filter_dropdown('cd_controle_carro_destino', 'Destino:', $destino);
			echo filter_dropdown('cd_controle_carro_motivo', 'Motivo:', $motivo);
			echo filter_dropdown('cd_controle_carro_motorista', 'Motorista:', $motorista);
	    echo form_end_box_filter();
		echo '<div id="result_div"></div>';
		echo br(2);
	echo aba_end();
	$this->load->view('footer');
?>