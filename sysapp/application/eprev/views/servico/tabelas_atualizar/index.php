<?php
set_title('Tabelas a Atualizar');
$this->load->view('header');
?>

<script>
    $(function(){
        filtrar();
    });
    
    function novo()
    {
        location.href='<?php echo site_url("servico/tabelas_atualizar/cadastro/"); ?>';
    }
    
    function filtrar()
    {
        $('#result_div').html("<?php echo loader_html(); ?>");

        $.post('<?php echo site_url('servico/tabelas_atualizar/listar'); ?>',
        {
            periodicidade : $('#periodicidade').val(),
            tipo_bd       : $('#tipo_bd').val(),
            dt_inicio_ini : $('#dt_inicio_ini').val(),
            dt_inicio_fim : $('#dt_inicio_fim').val()
        },
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
            'CaseInsensitiveString',
            'DateTimeBR',
            'DateTimeBR',
            'TimeBR',
            'Number',
            'Number',
            'Number',
            'CaseInsensitiveString',
            'CaseInsensitiveString',
			'Number'
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
        ob_resul.sort(2, true);
    }
</script>

<?php
$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

$arr_periodicidade[] = array('value' => 'D', 'text' => 'Diário');            
$arr_periodicidade[] = array('value' => 'S', 'text' => 'Sincronizado');    
$arr_periodicidade[] = array('value' => 'M', 'text' => 'Mensal');    
$arr_periodicidade[] = array('value' => 'E', 'text' => 'Eventual');    
$arr_periodicidade[] = array('value' => 'I', 'text' => 'Inativa');    

$arr_banco[] = array('value' => 'O', 'text' => 'ORACLE');
$arr_banco[] = array('value' => 'R', 'text' => 'RT - EMAILS');
$arr_banco[] = array('value' => 'U', 'text' => 'URA - TOI');
$arr_banco[] = array('value' => 'T', 'text' => 'URA - TELEDATA');
$arr_banco[] = array('value' => 'X', 'text' => 'URA - XCALLY');

$config['button'][]=array('Novo', 'novo()');

echo aba_start( $abas );
    echo form_list_command_bar($config);
    echo form_start_box_filter();
        echo filter_dropdown('periodicidade', 'Periodicidade:', $arr_periodicidade, array('D'));   
        echo filter_dropdown('tipo_bd', 'Banco:', $arr_banco);
		echo filter_date_interval('dt_inicio_ini', 'dt_inicio_fim', 'Dt. Início:');		
    echo form_end_box_filter();
    echo '<div id="result_div"></div>'.br();
echo aba_end();
$this->load->view('footer'); 
?>