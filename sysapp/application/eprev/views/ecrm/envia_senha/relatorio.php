<?php
set_title('Enviar Senha');
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

        $.post( '<?php echo base_url() . index_page(); ?>/ecrm/envia_senha/listar',{
            cd_empresa            : $('#cd_empresa').val(),
            cd_registro_empregado : $('#cd_registro_empregado').val(),
            seq_dependencia       : $('#seq_dependencia').val(),
            dt_email_ini          : $('#dt_email_ini').val(),
            dt_email_fim          : $('#dt_email_fim').val(),
            dt_envio_ini          : $('#dt_envio_ini').val(),
            dt_envio_fim          : $('#dt_envio_fim').val()			
        },
        function(data)
        {
            document.getElementById("result_div").innerHTML = data;
            configure_result_table();
        });
    }

    function configure_result_table()
    {
        var ob_resul = new SortableTable(document.getElementById("table-1"),
        [
            'Number',
            'RE',
            'DateTimeBR',
            'DateTimeBR',
            'CaseInsensitiveString',
            'CaseInsensitiveString',
            'CaseInsensitiveString',
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
        ob_resul.sort(3, true);
    }
    
    function ir_envia_senha()
    {
        location.href='<?php echo site_url('ecrm/envia_senha') ?>';
    }

</script>
<?php
$abas[] = array('aba_lista', 'Enviar senha', false, 'ir_envia_senha();');
$abas[] = array('aba_lista', 'Relatório', TRUE, 'location.reload();');
    echo aba_start( $abas );

    echo form_list_command_bar();

    echo form_start_box_filter('filter_bar', 'Filtros');

        $participante['cd_empresa']            = '';
        $participante['cd_registro_empregado'] = '';
        $participante['seq_dependencia']       = '';
        $conf = array('cd_empresa','cd_registro_empregado','seq_dependencia', 'nome');
        echo filter_participante( $conf, "Participante:", $participante, TRUE, FALSE );	
        echo filter_date_interval('dt_email_ini', 'dt_email_fim', 'Período do email:',calcular_data('','2 month'), date('d/m/Y'));
        echo filter_date_interval('dt_envio_ini', 'dt_envio_fim', 'Período do envio:');

    echo form_end_box_filter();
?>

<div id="result_div"></div>
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