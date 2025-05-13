<?php
    set_title('Documentos Recebidos - Lista');
    $this->load->view('header');
?>
<script>
    function filtrar()
    {
        $("#result_div").html("<?= loader_html() ?>");
        
        $.post("<?= site_url('ecrm/documento_recebido/listar') ?>",
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
			"RE",
			"CaseInsensitiveString",
			"CaseInsensitiveString",
			"DateTimeBR",
            "CaseInsensitiveString",
            "CaseInsensitiveString",
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
		ob_resul.sort(3, true);
	}

    $(function (){
        filtrar();
    });
</script>
<?php
    $abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

    echo aba_start($abas);
        echo form_list_command_bar(array());
        echo form_start_box_filter();
            echo filter_participante(array('cd_empresa','cd_registro_empregado','seq_dependencia', 'nome_participante'), 'RE:', array(
                'cd_empresa'            => $cd_empresa, 
                'cd_registro_empregado' => $cd_registro_empregado, 
                'seq_dependencia'       => $seq_dependencia
            ));
            echo filter_date_interval('dt_encaminhamento_ini', 'dt_encaminhamento_fim', 'Dt. Encaminhamento:');
            echo filter_dropdown('cd_documento_recebido_tipo', 'Origem:', $origem);
        echo form_end_box_filter();
    	echo '<div id="result_div"></div>';
        echo br();
    echo aba_end();

    $this->load->view('footer'); 
?>