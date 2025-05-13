<?php
set_title('Cadastro de Projetos');
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

        $.post( '<?php echo base_url() . index_page(); ?>/cadastro/projeto/listar', '',
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
            'CaseInsensitiveString','CaseInsensitiveString','CaseInsensitiveString','DateBR'
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
        location.href='<?php echo site_url("cadastro/projeto/detalhe"); ?>';
    }

    function ir_aba_sistema()
    {
        location.href='<?php echo site_url("cadastro/sistema"); ?>';
    }

    $(function (){
        filtrar();
    })
    
</script>

<?php
$abas[] = array('aba_lista', 'Sistemas', FALSE, 'ir_aba_sistema();');
$abas[] = array('aba_lista', 'Projetos', TRUE, 'location.reload();');

$config['filter'] = FALSE;
$config['button'][] = array('Novo Projeto', 'novo()');

echo aba_start($abas);

    echo form_list_command_bar($config);
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