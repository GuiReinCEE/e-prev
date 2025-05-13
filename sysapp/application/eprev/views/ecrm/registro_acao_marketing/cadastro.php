<?php
set_title('Registro de Ações MKT - Cadastro');
$this->load->view('header');
?>
<script>
    <?= form_default_js_submit(array('ds_registro_acao_marketing', 'dt_referencia')) ?>
    
    function ir_lista()
    {
        location.href='<?= site_url("ecrm/registro_acao_marketing") ?>';
    }

    function ir_acompanhamento()
    {
        location.href='<?= site_url("ecrm/registro_acao_marketing/acompanhamento/".intval($row['cd_registro_acao_marketing'])) ?>';
    }

    function ir_anexo()
    {
        location.href='<?= site_url("ecrm/registro_acao_marketing/anexo/".intval($row['cd_registro_acao_marketing'])) ?>';
    }

    function excluir()
    {
        var confirmacao = 'Deseja excluir?\n\n'+
                'Clique [Ok] para Sim\n\n'+
                'Clique [Cancelar] para Não\n\n';

        if(confirm(confirmacao))
        {
           location.href='<?= site_url("ecrm/registro_acao_marketing/excluir/".intval($row['cd_registro_acao_marketing'])) ?>';
        }
    }
</script>

<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_nc', 'Cadastro', TRUE, 'location.reload();');

if(intval($row['cd_registro_acao_marketing']) > 0)
{
   $abas[] = array('aba_lista', 'Acompanhamento', FALSE, 'ir_acompanhamento();');
   $abas[] = array('aba_lista', 'Anexo', FALSE, 'ir_anexo();');
}

echo aba_start( $abas );
    echo form_open('ecrm/registro_acao_marketing/salvar', 'name="filter_bar_form"');
        echo form_start_box("default_box", "Cadastro");
             echo form_default_hidden('cd_registro_acao_marketing', '', $row['cd_registro_acao_marketing']);
             echo form_default_text('ds_registro_acao_marketing', 'Descrição :*', $row);
             echo form_default_date('dt_referencia', 'Dt Referência :*', $row);
        echo form_end_box("default_box");
        echo form_command_bar_detail_start();     
            echo button_save("Salvar");
            
            if(intval($row['cd_registro_acao_marketing']) > 0)
            {
                echo button_save("Excluir", "excluir()", "botao_vermelho");
            }
        echo form_command_bar_detail_end();
    echo form_close();
    echo br();	
echo aba_end();

$this->load->view('footer_interna');
?>