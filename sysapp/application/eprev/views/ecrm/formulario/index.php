<?php
set_title('Formulários - Lista');
$this->load->view('header');
?>
<script>

    function filtrar()
    {
        load();
    }

    function load()
    {
        $("#result_div").html("<?php echo loader_html(); ?>");

        $.post( '<?php echo base_url() . index_page(); ?>/ecrm/formulario/listar',
        {
            cd_plano_empresa : $('#cd_plano_empresa').val(),
            cd_plano         : $('#cd_plano').val()
        },
        function(data)
        {
            $("#result_div").html(data);
            configure_result_table();
        });
    }
    
    function novo()
    {
        location.href='<?php echo site_url("ecrm/formulario/cadastro"); ?>';
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
    
    function excluir(cd_formulario)
    {
        if( confirm('Deseja excluir?') )
        {
            location.href='<?php echo site_url("ecrm/formulario/excluir"); ?>/'+ cd_formulario;
        }
    }
</script>
<?php

$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

$config['button'][]=array('Adicionar arquivo', 'novo()');

echo aba_start($abas);

echo form_list_command_bar($config);
echo form_start_box_filter('filter_bar', 'Filtros');
    echo filter_plano_ajax('cd_plano', '', '', 'Empresa:(*)', 'Plano:(*)','I');
echo form_end_box_filter();


?>
<div id="result_div"></div>
<br>
<?php
   echo aba_end();
?>
<script type="text/javascript">
    filtrar();
</script>
<?php
$this->load->view('footer');
?>