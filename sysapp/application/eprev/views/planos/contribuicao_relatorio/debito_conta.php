<?php
    set_title('Contribuição - Débito em Conta');
    $this->load->view('header');
?>
<script>
    function filtrar()
    {
        $("#result_div").html("<?= loader_html() ?>");
                
        $.post("<?= site_url('planos/contribuicao_relatorio/listar_debito_conta') ?>",
        $("#filter_bar_form").serialize(),
        function(data)
        {
            $("#result_div").html(data);
            configure_result_table();
        }); 
    }
	
    function configure_result_table()
    {
        if(document.getElementById("table-1"))
        {
            var ob_resul = new SortableTable(document.getElementById("table-1"),
            [
                "CaseInsensitiveString",
                "CaseInsensitiveString",
                "RE",
                "CaseInsensitiveString",
                "CaseInsensitiveString",
                "Number",
                "CaseInsensitiveString",
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
            ob_resul.sort(7, true);
        }
    }	

    function ir_gerado()
    {
        location.href = "<?= site_url('planos/contribuicao_relatorio/gerado') ?>";
    }

    function ir_lista()
    {
        location.href = "<?= site_url('planos/contribuicao_relatorio') ?>";
    }

    function atualiza_telefone()
    {
        var confirmacao = "Deseja Atualizar os Telefones INCORRETOS?\n\n"+
                "Clique [Ok] para Sim\n\n"+
                "Clique [Cancelar] para Não\n\n"; 

        if(confirm(confirmacao))
        {
            location.href = "<?= site_url('planos/contribuicao_relatorio/atualiza_telefone') ?>";
        }
    }

    $(function(){
        $("#contribuicao_relatorio_row").hide();

		filtrar();
    })
</script>
<?php
    $abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
    $abas[] = array('aba_gerados', 'CSV Gerados', FALSE, 'ir_gerado();');
    $abas[] = array('aba_debito_conta', 'Débito em Conta', TRUE, 'ir_debito_conta();');

    $status_telefone = array(
        array('value' => 'O', 'text' => 'OK'),
        array('value' => 'I', 'text' => 'INCORRETO'),
        array('value' => 'C', 'text' => 'SEM CELULAR')
    );

    echo aba_start($abas);
    	echo form_list_command_bar(array());
    	echo form_start_box_filter('filter_bar', 'Filtros');
    		echo filter_plano_ajax('cd_plano', '', '', 'Empresa:', 'Plano:');
    		echo filter_integer('nr_mes', 'Mês:');
    		echo filter_integer('nr_ano', 'Ano:');
            echo filter_dropdown('fl_telefone', 'Status Telefone:', $status_telefone);
    	echo form_end_box_filter();
    	echo '<div id="result_div"></div>';
    	echo br(2);
    echo aba_end();
    $this->load->view('footer');
?>