<?php
set_title('Reuniões SG - Lista');
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

        $.post( '<?php echo base_url() . index_page(); ?>/atividade/reuniao_sg/listar'
        ,{
            dt_inclusao_ini : $('#dt_inclusao_ini').val(),
            dt_inclusao_fim : $('#dt_inclusao_fim').val(),	
            dt_reuniao_ini  : $('#dt_reuniao_ini').val(),
            dt_reuniao_fim  : $('#dt_reuniao_fim').val(),
            fl_parecer      : $('#fl_parecer').val(),
            fl_encerrado    : $('#fl_encerrado').val(),
            cd_usuario      : $('#cd_usuario').val()
        }
        ,
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
            'Number',  
            'CaseInsensitiveString', 
            'CaseInsensitiveString', 
            'DateTimeBR', 
            'CaseInsensitiveString', 
            'CaseInsensitiveString', 
            'CaseInsensitiveString',
            'DateBR', 
            'CaseInsensitiveString', 
            'CaseInsensitiveString', 
            'DateTimeBR'
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
        location.href='<?php echo site_url("atividade/reuniao_sg/detalhe/0"); ?>';
    }
    
    function ir_relatorio()
    {
        location.href='<?php echo site_url("atividade/reuniao_sg/relatorio"); ?>';
    }
</script>
<?php
$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');
$abas[] = array('aba_lista', 'Relatório', FALSE, 'ir_relatorio();');

$drop[] = array('value' => 'S', 'text' => 'Sim');
$drop[] = array('value' => 'N', 'text' => 'Não');

$drop2[] = array('value' => 'S', 'text' => 'Não');
$drop2[] = array('value' => 'N', 'text' => 'Sim');

echo aba_start($abas);

$config['button'][] = array('Solicitar Reunião', 'novo()');
echo form_list_command_bar($config);
echo form_start_box_filter('filter_bar', 'Filtros');
echo filter_date_interval('dt_inclusao_ini', 'dt_inclusao_fim', 'Dt de cadastro:');
echo filter_date_interval('dt_reuniao_ini', 'dt_reuniao_fim', 'Dt da reunião:');
echo filter_dropdown('fl_parecer', 'Parecer:', $drop2);
echo filter_dropdown('fl_encerrado', 'Parecer encerrado:', $drop);
echo filter_dropdown('cd_usuario', 'Usuário solicitante:', $solicitante);
echo form_end_box_filter();
?>
<div id="result_div"><br><br><span style='color:green;'><b>Realize um filtro para exibir a lista</b></span></div>
<br>
<?php
echo aba_end('');
?>
<script type="text/javascript">
    filtrar();
</script>
<?php
$this->load->view('footer');
?>