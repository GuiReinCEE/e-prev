<?php
set_title('Registro de Ações MKT - Anexo');
$this->load->view('header');
?>
<script>
    <?= form_default_js_submit(array(), 'valida_arquivo(form)') ?>
    
    function ir_lista()
    {
        location.href='<?= site_url("ecrm/registro_acao_marketing") ?>';
    }

    function ir_cadastro()
    {
        location.href='<?= site_url("ecrm/registro_acao_marketing/cadastro/".intval($row['cd_registro_acao_marketing'])) ?>';
    }

    function ir_acompanhamento()
    {
        location.href='<?= site_url("ecrm/registro_acao_marketing/acompanhamento/".intval($row['cd_registro_acao_marketing'])) ?>';
    }

    function excluir(cd_registro_acao_marketing_anexo)
    {
        var confirmacao = 'Deseja excluir?\n\n'+
                'Clique [Ok] para Sim\n\n'+
                'Clique [Cancelar] para Não\n\n';

        if(confirm(confirmacao))
        {
           location.href='<?= site_url("ecrm/registro_acao_marketing/excluir_anexo/".intval($row['cd_registro_acao_marketing'])) ?>/'+cd_registro_acao_marketing_anexo;
        }
    }

    function valida_arquivo(form)
    {
        if($('#arquivo_m_count').val()  == 0)
        {
            alert('Nenhum arquivo foi anexado.');
            return false;
        }
        else
        {
            if( confirm('Salvar?') )
            {
                form.submit();
            }
        }
    }

    function validaArq(enviado, nao_enviado, arquivo)
    {
        $("form").submit();
    }

    function configure_result_table()
    {
        var ob_resul = new SortableTable(document.getElementById("table-1"),
        [
            'DateTimeBR',
            'CaseInsensitiveString',
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
        ob_resul.sort(0, true);
    }

    $(function(){
        $("#result_div").html("<?= loader_html() ?>");
    
        $.post("<?= site_url("ecrm/registro_acao_marketing/listar_anexo") ?>",
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
$abas[] = array('aba_nc', 'Cadastro', FALSE, 'ir_cadastro;');
$abas[] = array('aba_lista', 'Acompanhamento', FALSE, 'ir_acompanhamento();');
$abas[] = array('aba_lista', 'Anexo', TRUE, 'location.reload();');

echo aba_start( $abas );
    echo form_start_box("default_box", "Cadastro");
         echo form_default_textarea('ds_registro_acao_marketing', 'Descrição :', $row);
         echo form_default_row('dt_referencia', 'Dt Referência :', $row["dt_referencia"]);
    echo form_end_box("default_box");

    echo form_open('ecrm/registro_acao_marketing/salvar_anexo', 'name="filter_bar_form"');
        echo form_start_box("default_anexo_box", "Anexo");
             echo form_default_hidden('cd_registro_acao_marketing', '', $row['cd_registro_acao_marketing']);
             echo form_default_upload_multiplo('arquivo_m', 'Arquivo :*', 'registro_acao_marketing', 'validaArq');
        echo form_end_box("default_anexo_box");
        echo form_command_bar_detail_start();     
            echo button_save("Salvar");
        echo form_command_bar_detail_end();
    echo form_close();
    echo '<div id="result_div"></div>';
    echo br();  
echo aba_end();

$this->load->view('footer_interna');
?>