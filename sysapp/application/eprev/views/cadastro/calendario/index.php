<?php
set_title('Cadastro de Calendário');
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

        $.post( '<?php echo base_url() . index_page(); ?>/cadastro/calendario/listar', 
        {
            ano           : $('#ano').val(),
            tp_calendario : $('#tp_calendario').val()
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
            'DateBR', 'CaseInsensitiveString','CaseInsensitiveString','CaseInsensitiveString'
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
        location.href='<?php echo site_url("cadastro/calendario/cadastro"); ?>';
    }
    
    $(function (){
        if($('#ano').val() != '')
        {
            filtrar();
        }
        else
        {
            alert('Informe o ano');
            $('#result_div').html("<br><br><span style='color:green;'><b>Realize um filtro para exibir a lista</b></span>");
        }
        
    })
    
</script>

<?php
$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

$config['button'][] = array('Nova Data', 'novo()');

$arr_tipo[] = array('value' => 'E', 'text' => 'Evento');
$arr_tipo[] = array('value' => 'C', 'text' => 'Feriado FCEEE');
$arr_tipo[] = array('value' => 'F', 'text' => 'Feriado');
$arr_tipo[] = array('value' => 'T', 'text' => 'Feriado  FCEEE  Meio Turno');
$arr_tipo[] = array('value' => 'P', 'text' => 'Pagamento Colaboradores');

$arr_tipo[] = array('value' => 'DE', 'text' => 'Reunião Diretoria Executiva');
$arr_tipo[] = array('value' => 'CF', 'text' => 'Reunião Conselho Fiscal');
$arr_tipo[] = array('value' => 'CD', 'text' => 'Reunião Conselho Deliberativo');

$arr_tipo[] = array('value' => 'EN', 'text' => 'Evento Endomarketing');

echo aba_start($abas);
    echo form_list_command_bar($config);
    
    echo form_start_box_filter();
        echo filter_integer('ano', 'Ano:', date('Y'));
        echo filter_dropdown('tp_calendario', 'Tipo :', $arr_tipo);
    echo form_end_box_filter();
    ?>

    <div id="result_div">
        <br><br>
        <span style='color:green;'>
            <b>Realize um filtro para exibir a lista</b>
        </span>
    </div>

    <?php
echo br();
echo aba_end('');

$this->load->view('footer');
?>