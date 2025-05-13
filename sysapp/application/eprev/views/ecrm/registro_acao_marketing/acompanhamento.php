<?php
set_title('Registro de Ações MKT - Acompanhamento');
$this->load->view('header');
?>
<script>
    <?= form_default_js_submit(array('ds_registro_acao_marketing_acompanhamento')) ?>
    
    function ir_lista()
    {
        location.href='<?= site_url("ecrm/registro_acao_marketing") ?>';
    }

    function ir_cadastro()
    {
        location.href='<?= site_url("ecrm/registro_acao_marketing/cadastro/".intval($row['cd_registro_acao_marketing'])) ?>';
    }

    function ir_anexo()
    {
        location.href='<?= site_url("ecrm/registro_acao_marketing/anexo/".intval($row['cd_registro_acao_marketing'])) ?>';
    }

    function configure_result_table()
    {
        var ob_resul = new SortableTable(document.getElementById("table-1"),
        [
            'DateTimeBR',
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
        ob_resul.sort(0, true);
    }

    $(function(){
        $("#result_div").html("<?= loader_html() ?>");
    
        $.post("<?= site_url("ecrm/registro_acao_marketing/listar_acompanhamento") ?>",
        {
            cd_registro_acao_marketing : $("#cd_registro_acao_marketing").val()
        },
        function(data)
        {
            $("#result_div").html(data);
            configure_result_table();
        });
    });
</script>

<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_nc', 'Cadastro', FALSE, 'ir_cadastro();');
$abas[] = array('aba_lista', 'Acompanhamento', TRUE, 'location.reload();');
$abas[] = array('aba_lista', 'Anexo', FALSE, 'ir_anexo();');

echo aba_start( $abas );
    echo form_start_box("default_box", "Cadastro");
         echo form_default_text('ds_registro_acao_marketing', 'Descrição :', $row);
         echo form_default_row('dt_referencia', 'Dt Referência :', $row["dt_referencia"]);
    echo form_end_box("default_box");

    echo form_open('ecrm/registro_acao_marketing/salvar_acompanhamento', 'name="filter_bar_form"');
        echo form_start_box("default_acompanhamento_box", "Acompanhamento");
             echo form_default_hidden('cd_registro_acao_marketing', '', $row['cd_registro_acao_marketing']);
             echo form_default_textarea('ds_registro_acao_marketing_acompanhamento', 'Descrição :*', '', 'style="height:100px;"');
             echo form_default_upload_multiplo('arquivo_m', 'Arquivo :', 'registro_acao_marketing');
        echo form_end_box("default_acompanhamento_box");
        echo form_command_bar_detail_start();     
            echo button_save("Salvar");
        echo form_command_bar_detail_end();
    echo form_close();
    echo '<div id="result_div"></div>';
    echo br();	
echo aba_end();

$this->load->view('footer_interna');
?>