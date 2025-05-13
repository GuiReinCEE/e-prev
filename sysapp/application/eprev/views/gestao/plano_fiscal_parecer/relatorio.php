<?php
set_title('Plano Fiscal - Parecer - Relatório');
$this->load->view('header');
?>
<script>
    function filtrar()
    {
        $('#result_div').html("<?php echo loader_html(); ?>");

        $.post('<?php echo site_url('gestao/plano_fiscal_parecer/listar_relatorio')?>', $('#filter_bar_form').serialize(),
        function(data)
        {
            $('#result_div').html(data);
            configure_result_table();
        });
    }

    function configure_result_table()
    {
        var ob_resul = new SortableTable(document.getElementById("table-1"),
        [
           'CaseInsensitiveString',
            'Number',
            null,
            'CaseInsensitiveString',
            'CaseInsensitiveString',
            'CaseInsensitiveString',
            'CaseInsensitiveString',
            'DateTimeBR',
            'DateTimeBR',
            'DateBR',
            'DateTimeBR',
            'DateTimeBR',
            'CaseInsensitiveString',
            'CaseInsensitiveString'
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

    
    function ir_lista()
    {
        location.href='<?php echo site_url("gestao/plano_fiscal_parecer"); ?>';
    }

    $(function(){
        filtrar();
     });
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_lista', 'Relatório', TRUE, 'location.reload();');

$ar_opt[] = Array('value' => 'S', 'text' => 'Sim');
$ar_opt[] = Array('value' => 'N', 'text' => 'Não');		

echo aba_start( $abas );
    echo form_list_command_bar(array());
    echo form_start_box_filter();
		echo filter_integer_ano('nr_ano', 'nr_mes', 'Ano/Mês :', date('Y'));
        echo filter_integer('nr_item', 'Item :');
        echo filter_usuario_ajax('usuario', '', '', "Respondente :", "Gerência :");
        echo filter_usuario_ajax('responsavel', '', '', "Responsavél :", "Gerência :");
        echo filter_date_interval('dt_envio_ini', 'dt_envio_fim', 'Dt Envio :');
        echo filter_date_interval('dt_encaminhamento_ini', 'dt_encaminhamento_fim', 'Dt Enc. :');
        echo filter_date_interval('dt_limite_ini', 'dt_limite_fim', 'Dt Limite :');
        echo filter_date_interval('dt_resposta_ini', 'dt_resposta_fim', 'Dt Resposta :');
        echo filter_date_interval('dt_assinatura_ini', 'dt_assinatura_fim', 'Dt Assinatura :');
        echo filter_dropdown('fl_assinado', 'Assinado :', $ar_opt);	
        echo filter_dropdown('fl_status', 'Status :', $arr_status);
    echo form_end_box_filter();
    echo '<div id="result_div"></div>';
    echo br(2);
echo aba_end();

$this->load->view('footer'); ?>.