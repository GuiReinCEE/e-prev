<?php
    set_title('Novo Plano - Estrutura de Atividades');
    $this->load->view('header');
?>
<script>
    function filtrar()
    {
        $("#result_div").html("<?= loader_html() ?>");

        $.post("<?= site_url('planos/novo_plano/listar') ?>",
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
        ob_resul.sort(0, false);
    }
					
    function novo()
    {
        location.href = "<?= site_url('planos/novo_plano/cadastro') ?>";
    }

    function editar_ordem(cd_novo_plano_estrutura)
	{
		$("#ordem_valor_"+cd_novo_plano_estrutura).hide(); 
		$("#ordem_editar_"+cd_novo_plano_estrutura).hide(); 

		$("#ordem_salvar_"+cd_novo_plano_estrutura).show(); 
		$("#nr_ordem"+cd_novo_plano_estrutura).show(); 
		$("#nr_ordem"+cd_novo_plano_estrutura).focus();	
	}

    function set_ordem(cd_novo_plano_subprocesso, cd_novo_plano_estrutura)
    {
        $("#ajax_ordem_valor_" + cd_novo_plano_subprocesso).html("<?= loader_html('P') ?>");

        $.post("<?= site_url('planos/novo_plano/set_ordem') ?>",
        {
            cd_novo_plano_subprocesso : cd_novo_plano_subprocesso,
            cd_novo_plano_estrutura : cd_novo_plano_estrutura,
            nr_ordem : $("#nr_ordem" + cd_novo_plano_estrutura).val()	
        },
        function(data)
        {
			$("#ajax_ordem_valor_"+cd_novo_plano_estrutura).empty();
			
			$("#nr_ordem"+cd_novo_plano_estrutura).hide();
			$("#ordem_salvar_"+cd_novo_plano_estrutura).hide(); 
			
            $("#ordem_valor_"+cd_novo_plano_estrutura).html($("#nr_ordem" + cd_novo_plano_estrutura).val()); 
			$("#ordem_valor_"+cd_novo_plano_estrutura).show(); 
			$("#ordem_editar_"+cd_novo_plano_estrutura).show(); 

        });
    }

    $(function(){
        if($("#fl_encerramento").val() == '')
        {
            $("#fl_encerramento").val("N");
        }

        filtrar();
    });
</script>
<?php
    $abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

    $config['button'][] = array('Nova Atividade', 'novo()');

    $encerramento = array(
        array('value' => 'N', 'text' => 'Não'),
        array('value' => 'S', 'text' => 'Sim')
    );

    echo aba_start($abas);
        echo form_list_command_bar($config);
        echo form_start_box_filter(); 
            echo form_default_dropdown('cd_novo_plano_subprocesso', 'Subprocesso:', $subprocesso);
            echo filter_dropdown('fl_encerramento', 'Desativado:', $encerramento);
        echo form_end_box_filter();
        echo '<div id="result_div"></div>';
        echo br();
    echo aba_end();

    $this->load->view('footer');
?>