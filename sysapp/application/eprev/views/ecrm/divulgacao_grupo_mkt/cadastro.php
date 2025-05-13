<?php
    set_title('Email Divulgação - Grupo');
    $this->load->view('header');
?>
<script>
    <?= form_default_js_submit(array('ds_divulgacao_grupo','tp_grupo')) ?>
    
    function ir_lista()
    {
        location.href = "<?= site_url('ecrm/divulgacao_grupo_mkt') ?>";
    }

    function ir_configuracao()
    {
        location.href = "<?= site_url('ecrm/divulgacao_grupo_mkt/configuracao/'.$row['cd_divulgacao_grupo']) ?>";
    }

    function ir_importacao()
    {
        location.href = "<?= site_url('ecrm/divulgacao_grupo_mkt/importacao/'.$row['cd_divulgacao_grupo']) ?>";
    }

    function ir_importacao_re()
    {
        location.href = "<?= site_url('ecrm/divulgacao_grupo_mkt/importacao_participante/'.$row['cd_divulgacao_grupo']) ?>";
    }

    function excluir()
    {
        var confirmacao = "Deseja EXCLUIR o Grupo?\n\n"+
                          "Clique [Ok] para Sim\n\n"+
                          "Clique [Cancelar] para Não\n\n"; 

        if(confirm(confirmacao))
        {
            location.href = "<?= site_url('ecrm/divulgacao_grupo_mkt/excluir/'.$row['cd_divulgacao_grupo']) ?>";
        }
    }
</script>
<?php
    $abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
    $abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');

    if(trim($row['tp_grupo']) == 'C')
    {
        $abas[] = array('aba_configuracao', 'Configuração', FALSE, 'ir_configuracao();');
    }
    elseif (trim($row['tp_grupo']) == 'I') 
    {
        $abas[] = array('aba_importacao', 'Importação - Email', FALSE, 'ir_importacao();');
    }
    elseif (trim($row['tp_grupo']) == 'P') 
    {
        $abas[] = array('aba_importacao', 'Importação - RE', FALSE, 'ir_importacao_re();');
    }

    echo aba_start($abas);
        echo form_open('ecrm/divulgacao_grupo_mkt/salvar');
            echo form_start_box('default_box', 'Cadastro');
                echo form_default_hidden('cd_divulgacao_grupo', '', $row['cd_divulgacao_grupo']);
                echo form_default_textarea('ds_divulgacao_grupo', 'Descrição: (*)', $row['ds_divulgacao_grupo'], 'style="height:80px"');
                if(intval($row['cd_divulgacao_grupo']) == 0)
                {
                    echo form_default_dropdown('tp_grupo', 'Tipo: (*)', $grupo, $row['tp_grupo']);
                }
                else
                {
                    echo form_default_row('', 'Tipo:', $row['ds_grupo']);
                }     
            echo form_end_box('default_box');
            echo form_command_bar_detail_start();   
                echo button_save('Salvar');
                
                if(intval($row['cd_divulgacao_grupo']) > 0)
                {
                    echo button_save('Excluir', 'excluir();', 'botao_vermelho');
                }
            echo form_command_bar_detail_end();
        echo form_close();
        echo br(2);
    echo aba_end();

    $this->load->view('footer_interna');
?>