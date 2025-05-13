<?php
    set_title('Operacionalização de Nova Patrocinadora - Minhas');
    $this->load->view('header');
?>
<script>
    function filtrar()
    {
        $("#result_div").html("<?= loader_html() ?>");

        $.post("<?= site_url('planos/nova_patrocinadora/minhas_listar') ?>",
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
            "Number",
            "CaseInsensitiveString",
            "CaseInsensitiveString",
            "CaseInsensitiveString",
            "CaseInsensitiveString",
            "DateTimeBR",
            "DateBR",
            "DateTimeBR",
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
        ob_resul.sort(8, false);
    }				

    $(function(){

        if($("#fl_encerramento").val() == "")
        {
            $("#fl_encerramento").val("N");
        }

		filtrar();        
    });
</script>
<?php   
    $abas[] = array('aba_minhas', 'Lista', TRUE, 'location.reload();');

    $encerrado = array(
        array('value' => 'N', 'text' => 'Não'), 
        array('value' => 'S', 'text' => 'Sim')
    );

    echo aba_start($abas);
    echo form_list_command_bar(array());
    	echo form_start_box_filter(); 
            echo filter_dropdown('cd_nova_patrocinadora', 'Patrocinadora:', $patrocinadora);
            echo filter_dropdown('fl_encerramento', 'Encerrado:',  $encerrado);
            echo filter_date_interval('dt_prazo_ini', 'dt_prazo_fim', 'Dt. Prazo:');
        echo form_end_box_filter();
		echo '<div id="result_div"></div>';
		echo br(2);
	echo aba_end();

    $this->load->view('footer');
?>