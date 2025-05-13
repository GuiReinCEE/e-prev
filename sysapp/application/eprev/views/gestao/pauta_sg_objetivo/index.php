<?php
	set_title('Pauta SG - Objetivo');
	$this->load->view('header');
?>
<script>
	function filtrar()
	{
		$("#result_div").html("<?= loader_html() ?>");
				
		$.post("<?= site_url('gestao/pauta_sg_objetivo/listar') ?>",
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

	function excluir(cd_pauta_sg_objetivo)
	{
		var confirmacao = 'Deseja excluir o objetivo?\n\n'+
            'Clique [Ok] para Sim\n\n'+
            'Clique [Cancelar] para Não\n\n';

        if(confirm(confirmacao))
        { 
            location.href = "<?= site_url('gestao/pauta_sg_objetivo/excluir') ?>/"+cd_pauta_sg_objetivo;
        }
	}
					
	function novo()
	{
		location.href = "<?= site_url('gestao/pauta_sg_objetivo/cadastro') ?>";
	}

	$(function(){
		filtrar();
	});
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

	$config['button'][] = array('Novo Objetivo', 'novo();');
    
    $tipo = array(
        array('value' => 'S', 'text' => 'Sim'), 
        array('value' => 'N', 'text' => 'Não')
    );  

	echo aba_start($abas);
		echo form_list_command_bar($config);
		echo form_start_box_filter(); 
			echo filter_text('ds_pauta_sg_objetivo', 'Objetivo:', '', 'style="width:300px;"');
			echo filter_dropdown('fl_anexo_obrigatorio', 'Anexo obrigatório:', $tipo);
	    echo form_end_box_filter();
		echo '<div id="result_div"></div>';
		echo br(2);
	echo aba_end();

	$this->load->view('footer');
?>