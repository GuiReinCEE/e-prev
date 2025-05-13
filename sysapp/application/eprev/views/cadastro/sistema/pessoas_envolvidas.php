<?php
set_title('Cadastro de Sistemas');
$this->load->view('header');
?>
<script>
<?php
    echo form_default_js_submit(array("usuario"));
?>
    
    function ir_lista()
    {
        location.href='<?php echo site_url("cadastro/sistema"); ?>';
    }
    
    function ir_cadastro()
    {
        location.href='<?php echo site_url("cadastro/sistema/detalhe/".$codigo); ?>';
    }
    
    function filtrar()
    {
        load();
    }
    
    function excluir_envolvido(cd_envolvido)
    {
        var confirmacao = 'Deseja excluir o envolvido?\n\n'+
                'Clique [Ok] para Sim\n\n'+
                'Clique [Cancelar] para Não\n\n';
        
        if(confirm(confirmacao))
        {
           location.href='<?php echo site_url("cadastro/sistema/excluir_envolvido/".$codigo); ?>/'+ cd_envolvido;
        }

    }
    
    function configure_result_table()
    {
        var ob_resul = new SortableTable(document.getElementById("table-1"),
        [
            'CaseInsensitiveString',
            null
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
    
    
    function load()
    {
        document.getElementById("result_div").innerHTML = "<?php echo loader_html(); ?>";

        $.post( '<?php echo base_url() . index_page(); ?>/cadastro/sistema/listar_envolvidos', 
        {
            codigo : $('#codigo').val()
        },
        function(data)
        {
            document.getElementById("result_div").innerHTML = data;
            configure_result_table();
        });
    }
    
    $(function(){
        filtrar();
    })
    
</script>


<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_detalhe', 'Cadastro', false, 'ir_cadastro();');
$abas[] = array('aba_detalhe', 'Pessoas Envolvidas', true, 'location.reload();');

echo aba_start($abas);
     echo form_open('cadastro/sistema/salvar_envolvido');
        echo form_start_box("default_box", "Cadastro");
            echo form_default_hidden('codigo', '', $codigo);
            echo form_default_usuario_ajax('usuario', '', '', "Usuário :* ", "Gerência :* ");
        echo form_end_box("default_box");
        
        echo form_command_bar_detail_start();
            echo button_save();
        echo form_command_bar_detail_end();
        
        echo form_start_box("default_box", "Pessoas Envolvidas");
            echo '<div id="result_div"></div>';
        echo form_end_box("default_box");
    echo form_close();
echo aba_end();

$this->load->view('footer_interna');
?>