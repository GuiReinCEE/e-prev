<?php
	set_title('Cadastro de Documentos - Cadastro');
	$this->load->view('header');
?>
<script>
    <?= form_default_js_submit(array('ds_documento_arquivo', 'arquivo', 'arquivo_nome')); ?>

    function ir_lista()
    {
        location.href = "<?= site_url('servico/documento_arquivo') ?>";
    }
</script>
<?php
    $abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
    $abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');

    echo aba_start($abas);
        echo form_open('servico/documento_arquivo/salvar');
            echo form_start_box('default_box', 'Cadastro');
                echo form_default_hidden('cd_documento_arquivo', '', $row);	
                echo form_default_text('ds_documento_arquivo', 'Descrição: (*)', $row, 'style="width: 300px;"');
				echo form_default_upload_iframe(
                    'arquivo', 
                    'documento_arquivo', 
                    'Anexo: (*)', 
                    array($row['arquivo'], $row['arquivo_nome']),
                    'documento_arquivo'
                );
                if(intval($row['cd_documento_arquivo']) > 0)
                {
                    echo form_default_row('', 'Dt. Alteração:', $row['dt_alteracao']);
                    echo form_default_row('', 'Usuário:', $row['ds_usuario_alteracao']);
                }
            echo form_end_box('default_box');    
            echo form_command_bar_detail_start();
                echo button_save('Salvar');	
            echo form_command_bar_detail_end();    
        echo form_close();
    echo aba_end();
    echo br();

    $this->load->view('footer');
?>