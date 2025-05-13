<?php
    set_title('Contribuição - Envio SMS');
    $this->load->view('header');
?>
<script>
    function filtrar()
    {
        $("#result_div").html("<?= loader_html() ?>");
                
        $.post("<?= site_url('planos/contribuicao_relatorio/listar') ?>",
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
            var ob_resul = new SortableTable(document.getElementById("table-1"),[
                null,
                "CaseInsensitiveString",
                "CaseInsensitiveString",
                "RE",
                "CaseInsensitiveString",
                "CaseInsensitiveString",
                "Number",
                "CaseInsensitiveString",
                "DateTimeBR",
                "CaseInsensitiveString",
                "DateTimeBR",
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
            ob_resul.sort(8, true);
        }
    }	

    function ir_gerado()
    {
        location.href = "<?= site_url('planos/contribuicao_relatorio/gerado') ?>";
    }

    function ir_debito_conta()
    {
        location.href = "<?= site_url('planos/contribuicao_relatorio/debito_conta') ?>";
    }

    function check_all()
    {
        var ipts = $("#tabela_contribuicao_relatorio > tbody").find("input:checkbox");
        var check = document.getElementById("check_all");
     
        check.checked ?
            jQuery.each(ipts, function(){
            this.checked = true;
        }) :
            jQuery.each(ipts, function(){
            this.checked = false;
        });
    }
	
    function gerar()
    {
        var ipts = $("#tabela_contribuicao_relatorio > tbody").find("input:checkbox:checked");
        
        var check = [];

        var contribuicao_relatorio_item = '';

        $("#contribuicao_relatorio_item").html(contribuicao_relatorio_item);
    
        ipts.each(function(i, e) {
            contribuicao_relatorio_item = $("#contribuicao_relatorio_item").html();

            $("#contribuicao_relatorio_item").html(contribuicao_relatorio_item + '<input type="hidden" name="contribuicao_relatorio[]" value="'+$(this).val()+'"/>');

            check.push($(this).val());
        });
        
        if(check.length > 0)
        {
            var confirmacao = "Deseja Gerar o arquivo CSV (Total : "+check.length +")?\n\n"+
                "Clique [Ok] para Sim\n\n"+
                "Clique [Cancelar] para Não\n\n"; 

            if(confirm(confirmacao))
            {
                filter_bar_form.method   = "post";
                filter_bar_form.action   = "<?= site_url('planos/contribuicao_relatorio/gerar') ?>";
                filter_bar_form.target   = "_self";
                filter_bar_form.onsubmit = "";
                filter_bar_form.submit();

                setTimeout(filtrar, 300, i);
            }
            else
            {
                $("#contribuicao_relatorio_item").html('');
            }
        }
        else
        {
            alert('Selecione no mínimo um participante.');
            $("#contribuicao_relatorio_item").html('');
        }
    }

    function enviar_email()
    {
        filter_bar_form.method   = "post";
        filter_bar_form.action   = "<?= site_url('planos/contribuicao_relatorio/enviar_email') ?>";
        filter_bar_form.target   = "_self";
        filter_bar_form.onsubmit = "";
        filter_bar_form.submit();
    }
	
    function enviarSMS()
    {
        var ipts = $("#tabela_contribuicao_relatorio > tbody").find("input:checkbox:checked");
        
        var check = [];

        var contribuicao_relatorio_item = '';

        $("#contribuicao_relatorio_item").html(contribuicao_relatorio_item);
    
        ipts.each(function(i, e) {
            contribuicao_relatorio_item = $("#contribuicao_relatorio_item").html();

            $("#contribuicao_relatorio_item").html(contribuicao_relatorio_item + '<input type="hidden" name="contribuicao_relatorio[]" value="'+$(this).val()+'"/>');

            check.push($(this).val());
        });
        
        if(check.length > 0)
        {
            var confirmacao = "Deseja enviar os SMS (Total : "+check.length +")?\n\n"+
                "ESTA AÇÃO É IRREVERSÍVEL\n\n"+
                "Clique [Ok] para Sim\n\n"+
                "Clique [Cancelar] para Não\n\n"; 

            if(confirm(confirmacao))
            {
                filter_bar_form.method   = "post";
                filter_bar_form.action   = "<?= site_url('planos/contribuicao_relatorio/enviarSMS') ?>";
                filter_bar_form.target   = "_self";
                filter_bar_form.onsubmit = "";
                filter_bar_form.submit();

                setTimeout(filtrar, 300, i);
            }
            else
            {
                $("#contribuicao_relatorio_item").html('');
            }
        }
        else
        {
            alert('Selecione no mínimo um participante.');
            $("#contribuicao_relatorio_item").html('');
        }
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

        if($("#nr_ano").val() == '')
        {
          //  $("#nr_ano").val("<?= date('Y') ?>");
        }

        if($("#nr_mes").val() == '')
        {
          //  $("#nr_mes").val("<?= date('m') ?>");
        }

        $("#contribuicao_relatorio_row").hide();

		//filtrar();
    })
</script>
<?php
    $abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');
    $abas[] = array('aba_gerados', 'CSV Gerados', FALSE, 'ir_gerado();');
    $abas[] = array('aba_debito_conta', 'Débito em Conta', FALSE, 'ir_debito_conta();');

    $status_telefone = array(
        array('value' => 'O', 'text' => 'OK'),
        array('value' => 'I', 'text' => 'INCORRETO'),
        array('value' => 'C', 'text' => 'SEM CELULAR')
    );

    $status_geracao = array(
        array('value' => 'S', 'text' => 'Sim'),
        array('value' => 'N', 'text' => 'Não'),
    );

    $config['button'][] = array('Gerar CSV', 'gerar();');
    $config['button'][] = array('Atualizar Telefones', 'atualiza_telefone();');

    echo aba_start($abas);
    	echo form_list_command_bar($config);
    	echo form_start_box_filter('filter_bar', 'Filtros');
            echo form_default_row('contribuicao_relatorio', '', '');
            echo filter_dropdown('cd_contribuicao_relatorio_origem', 'Origem:', $origem, $cd_contribuicao_relatorio_origem);
    		echo filter_plano_ajax('cd_plano', $cd_plano_empresa, $cd_plano, 'Empresa:', 'Plano:');
    		echo filter_integer('nr_mes', 'Mês:', $nr_mes);
    		echo filter_integer('nr_ano', 'Ano:', $nr_ano);
            echo filter_date_interval('dt_referencia_ini', 'dt_referencia_fim', 'Dt. Referência:');
            echo filter_participante(array('cd_empresa', 'cd_registro_empregado', 'seq_dependencia', 'nome'), 'Participante:', '', TRUE, FALSE);
            echo filter_dropdown('fl_telefone', 'Status Telefone:', $status_telefone, $fl_telefone);
            echo filter_dropdown('fl_gerado', 'Gerado:', $status_geracao, $fl_gerado);
            echo filter_dropdown('fl_envio_sms', 'SMS Enviado:', $status_geracao, $fl_sms_enviado);
    	echo form_end_box_filter();
    	echo '<div id="result_div"></div>';
    	echo br(2);
    echo aba_end();
    $this->load->view('footer');
?>