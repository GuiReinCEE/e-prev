<?php
set_title('Email Divulgação - Grupo');
$this->load->view('header');
?>
<script>
	function filtrar()
	{
		$("#result_div").html("<?= loader_html() ?>");
				
		$.post("<?= site_url('ecrm/divulgacao_grupo_mkt/listar') ?>",
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
		    "Number",
		    "CaseInsensitiveString", 
		    "CaseInsensitiveString", 
		    "DateTimeBR", 
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
		ob_resul.sort(4, true);
	}

	function novo()
	{
		location.href = "<?= site_url('ecrm/divulgacao_grupo_mkt/cadastro') ?>";
	}

	function excluir(cd_divulgacao_grupo)
    {
    	var confirmacao = "Deseja EXCLUIR o Grupo?\n\n"+
                          "Clique [Ok] para Sim\n\n"+
                          "Clique [Cancelar] para Não\n\n"; 

        if(confirm(confirmacao))
        {
            location.href = "<?= site_url('ecrm/divulgacao_grupo_mkt/excluir_grupo/') ?>/" + cd_divulgacao_grupo;
        }
    }

	$(function(){
		filtrar();
	});

</script>

<?php
	$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

	$config['button'][] = array('Novo Grupo', 'novo();');

	echo aba_start($abas);
		echo form_list_command_bar($config);
		echo form_start_box_filter(); 
			echo filter_dropdown('tp_grupo','Tipo: ',$grupo);
	    echo form_end_box_filter();
		echo '<div id="result_div"></div>';
		echo br(2);
	echo aba_end();

	$this->load->view('footer');
?>

			