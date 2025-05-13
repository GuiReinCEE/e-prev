<?php
    set_title('Sócio Instituidor');
    $this->load->view('header');
?>
<script>
	function filtrar()
    {
        $("#result_div").html("<?= loader_html() ?>");

        $.post("<?= site_url('ecrm/socio_instituidor/listar') ?>",
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
            "CaseInsensitiveString",
            "CaseInsensitiveString",
            "DateTimeBR",
            "DateTimeBR",
            "DateTimeBR",
            "CaseInsensitiveString",
            "CaseInsensitiveString",
            "CaseInsensitiveString",
            "CaseInsensitiveString",
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
        ob_resul.sort(0, true);
    }

    function novo()
    {
        location.href = "<?= site_url('ecrm/socio_instituidor/cadastro'); ?>";
    }
		
    function ir_email()
    {
        location.href = "<?= site_url('ecrm/socio_instituidor/email'); ?>";
    }		
	
	$(function(){
		filtrar();
	});
</script>
<?php
    $abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');
    $abas[] = array('aba_email', 'Email', FALSE, 'ir_email();');

    $socio = array(
        array('value' => '0', 'text' => 'Não informado'),
        array('value' => '1', 'text' => 'Sócio'),
        array('value' => '2', 'text' => 'Não Sócio'),
        array('value' => '3', 'text' => 'Vinculado'),
        array('value' => '4', 'text' => 'Não Vinculado')
    );
    
    $config['button'][] = array('Novo', 'novo()');

    echo aba_start($abas);
    	echo form_list_command_bar($config);
    	echo form_start_box_filter();
    		echo filter_dropdown('cd_empresa', 'Instituidor:', $empresa, array($cd_empresa));
    		echo filter_dropdown('id_situacao', 'Situação:', $socio);
            echo filter_dropdown('cd_socio_instituidor_categoria', 'Categoria:', $categoria);
            echo filter_dropdown('cd_gerencia_indicacao', 'Indicação Interna', $gerencia);
    		echo filter_cpf('cpf', 'CPF:');
            echo filter_cpf('cpf_participante', 'CPF Participante:');
    		echo form_default_date_interval('dt_inclusao_ini', 'dt_inclusao_fim', 'Dt Cadastro:');
    		echo form_default_date_interval('dt_validacao_ini', 'dt_validacao_fim', 'Dt Validação:', $dt_ult_validacao, date('d/m/Y'));
    	echo form_end_box_filter();
    	echo '<div id="result_div"></div>';
    	echo br(2);
    echo aba_end();
    $this->load->view('footer');
?>