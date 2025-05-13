<?php
    set_title('Reclamações - Assunto');
    $this->load->view('header');
?>
<script>
    <?= form_default_js_submit(array('ds_reclamacao_assunto')) ?>
    
    function ir_lista()
    {
        location.href = "<?= site_url('ecrm/reclamacao_assunto') ?>";
    }

    function excluir()
    {
        var confirmacao = "Deseja EXCLUIR o Assunto?\n\n"+
                          "Clique [Ok] para Sim\n\n"+
                          "Clique [Cancelar] para Não\n\n"; 

        if(confirm(confirmacao))
        {
            location.href = "<?= site_url('ecrm/reclamacao_assunto/excluir/'.$row['cd_reclamacao_assunto']) ?>";
        }
    }
</script>

<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
    $abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');

	echo aba_start($abas);
        echo form_open('ecrm/reclamacao_assunto/salvar');
            echo form_start_box('default_box', 'Cadastro');
                echo form_default_hidden('cd_reclamacao_assunto', '', $row['cd_reclamacao_assunto']);
                echo form_default_text('ds_reclamacao_assunto', 'Descrição: (*)', $row['ds_reclamacao_assunto'], 'style="width:500px"');
            echo form_end_box('default_box');
            echo form_command_bar_detail_start();   
                echo button_save('Salvar');
                
                if(intval($row['cd_reclamacao_assunto']) > 0)
                {
                    echo button_save('Excluir', 'excluir();', 'botao_vermelho');
                }
            echo form_command_bar_detail_end();
        echo form_close();
        echo br(2);
    echo aba_end();

    $this->load->view('footer_interna');
?>