<?php
	set_title('Cadastro de Documentos');
	$this->load->view('header');
?>
<script>
    function filtrar()
	{
		$("#result_div").html("<?= loader_html() ?>");
			
		$.post("<?= site_url('servico/documento_arquivo/listar') ?>",
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
		    "DateTimeBR",
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
				removeClassName(rows[i], i % 2 ? "sort-par" : "sort-impar");
				addClassName(rows[i], i % 2 ? "sort-impar" : "sort-par");
			}
		};
		ob_resul.sort(1, true);
	}

    function ir_cadastro()
    {
        location.href = "<?= site_url('servico/documento_arquivo/cadastro') ?>";
    }

    function excluir(cd_documento_arquivo)
    {
        var confirmacao = "Deseja excluir este documento?\n\n"+
                          "[OK] para Sim\n\n"+
                          "[Cancelar] para Não\n\n";

        if(confirm(confirmacao))
        {
            location.href = "<?= site_url('servico/documento_arquivo/excluir') ?>/"+cd_documento_arquivo;
        }
    }
    
    $(function (){
        filtrar();
    });
</script>
<?php
    $abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

    $config['button'][] = array('Novo Documento', 'ir_cadastro();');

    echo aba_start($abas);
    echo form_list_command_bar($config);
    echo form_start_box_filter('filter_bar', 'Filtros', FALSE);
    echo form_end_box_filter();
    echo '<div id="result_div"></div>';
    echo br(2);
    echo aba_end();
    echo br();
    $this->load->view('footer');
?>