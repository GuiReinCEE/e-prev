<?php
	set_title('Registro de Solicitações de Fiscalizações e Auditorias - Origem');
	$this->load->view('header');
?>
<script>
	function filtrar()
	{
		$("#result_div").html("<?= loader_html() ?>");
				
		$.post("<?= site_url('atividade/solic_fiscalizacao_audit_origem/listar') ?>",
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
		ob_resul.sort(0, false);
	}

	function excluir(cd_solic_fiscalizacao_audit_origem)
	{
		var confirmacao = 'Deseja excluir?\n\n'+
            'Clique [Ok] para Sim\n\n'+
            'Clique [Cancelar] para Não\n\n';

        if(confirm(confirmacao))
        { 
            location.href = "<?= site_url('atividade/solic_fiscalizacao_audit_origem/excluir') ?>/"+cd_solic_fiscalizacao_audit_origem;
        }
	}
					
	function novo()
	{
		location.href = "<?= site_url('atividade/solic_fiscalizacao_audit_origem/cadastro') ?>";
	}

	$(function(){
		filtrar();
	});
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

	$config['button'][] = array('Novo', 'novo();');
    
    $especificar = array(
        array('value' => 'S', 'text' => 'Sim'), 
        array('value' => 'N', 'text' => 'Não')
    );  

	echo aba_start($abas);
		echo form_list_command_bar($config);
		echo form_start_box_filter(); 
			echo filter_text('ds_solic_fiscalizacao_audit_origem', 'Origem:', '', 'style="width:350px;"');
			echo filter_dropdown('fl_especificar', 'Especificar:', $especificar);
	    echo form_end_box_filter();
		echo '<div id="result_div"></div>';
		echo br(2);
	echo aba_end();

	$this->load->view('footer');
?>