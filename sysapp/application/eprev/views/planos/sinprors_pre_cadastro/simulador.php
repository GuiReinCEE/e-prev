<?php
set_title('Pré-cadastro SINPRORS');
$this->load->view('header');
?>
<script>
    function filtrar()
    {
        load();
    }

    function load()
    {
        document.getElementById("result_div").innerHTML = "<?php echo loader_html(); ?>";

        $.post( '<?php echo base_url() . index_page(); ?>/planos/sinprors_pre_cadastro/simulador_listar',
        {
            dt_inclusao_inicial       : $('#dt_inclusao_inicial').val(),
            dt_inclusao_final         : $('#dt_inclusao_final').val(),
            ds_nome                   : $('#ds_nome').val(),
            nr_cpf                    : $('#nr_cpf').val(),
            cd_enviado                : $('#cd_enviado').val(),
            dt_acompanhamento_inicial : $('#dt_acompanhamento_inicial').val(),
            dt_acompanhamento_final   : $('#dt_acompanhamento_final').val()
        } ,
        function(data)
        {
            document.getElementById("result_div").innerHTML = data;
            configure_result_table();
        }
    );
    }

    function configure_result_table()
    {
        var ob_resul = new SortableTable(document.getElementById("table-1"),
        [
            'DateTimeBR','CaseInsensitiveString','CaseInsensitiveString','CaseInsensitiveString','Number','Number','DateBR','CaseInsensitiveString'
        ]);
        ob_resul.onsort = function()
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

    function ir_lista()
    {
        //alert('<?php echo site_url("planos/sinprors_pre_cadastro/index"); ?>');
        location.href='<?php echo site_url("planos/sinprors_pre_cadastro/index"); ?>';
    }
</script>

<?php
$abas[] = array('aba_lista', 'Pré-cadastro', FALSE, 'ir_lista();');
$abas[] = array('aba_simulador', 'Simulador', TRUE, 'location.reload();');

$arr_enviado[] = Array('value' => 'A', 'text' => 'Amauri Bueno');
$arr_enviado[] = Array('value' => 'M', 'text' => 'Mongeral');

echo aba_start($abas);

echo form_list_command_bar();

echo form_start_box_filter();
    echo filter_date_interval('dt_inclusao_inicial', 'dt_inclusao_final', 'Dt Cadastro :');
    echo filter_text('ds_nome', 'Nome :');
    echo filter_cpf('nr_cpf', 'CPF :');
    echo filter_dropdown('cd_enviado', 'Enviado:', $arr_enviado);
    echo filter_date_interval('dt_acompanhamento_inicial', 'dt_acompanhamento_final', 'Dt Acompanhamento :');
echo form_end_box_filter();

?>

<div id="result_div"><br><br><span style='color:green;'><b>Realize um filtro para exibir a lista</b></span></div>
<br />

<?php
echo aba_end('');
?>

<script type="text/javascript">
    filtrar();
</script>

<?php
$this->load->view('footer');
?>