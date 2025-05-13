<?php
	set_title('Reclamações - Assunto');
	$this->load->view('header');
?>
<script>
	function filtrar()
	{
		$("#result_div").html("<?= loader_html() ?>");
				
		$.post("<?= site_url('ecrm/reclamacao_assunto/listar') ?>",
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
		    "DateTimeBR" 
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
		ob_resul.sort(1, true);
	}

	function novo()
	{
		location.href = "<?= site_url('ecrm/reclamacao_assunto/cadastro') ?>";
	}

	function excluir(cd_divulgacao_grupo)
    {
    	var confirmacao = "Deseja EXCLUIR o Assunto?\n\n"+
                          "Clique [Ok] para Sim\n\n"+
                          "Clique [Cancelar] para Não\n\n"; 

        if(confirm(confirmacao))
        {
            location.href = "<?= site_url('ecrm/reclamacao_assunto/excluir') ?>/" + cd_divulgacao_grupo;
        }
    }

	$(function(){
		filtrar();
	});

</script>

<?php
	$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

	$config['button'][] = array('Novo Assunto', 'novo();');

	echo aba_start($abas);
		echo form_list_command_bar($config);
		echo form_start_box_filter(); 
	    echo form_end_box_filter();
		echo '<div id="result_div"></div>';
		echo br(2);
	echo aba_end();

	$this->load->view('footer');
?>