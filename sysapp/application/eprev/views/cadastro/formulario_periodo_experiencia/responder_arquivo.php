<?php
	set_title('Formulário');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array(), 'valida_formulario(form, false)') ?>

    function valida_formulario(form, valida)
    {        
        if($('#arquivo').val() == '' && $('#arquivo_nome').val() == '')
        {
            alert('Nenhum arquivo foi anexado.');
            return false;
        }
        else
        {
            if(valida)
            {
                $("#fl_encerrar").val("S");

                confirmacao = 'Deseja salvar e encaminhar o formulário para o RH?\n\n'+
                    'Clique [Ok] para Sim\n\n'+
                    'Clique [Cancelar] para Não\n\n';
            }
            else
            {
                var confirmacao = 'Deseja salvar o formulário? \n\n'+
                    '(Para encerrar clique em Salvar e Encaminhar) \n\n'+
                    'Clique [Ok] para Sim\n\n'+
                    'Clique [Cancelar] para Não\n\n';
            }

            if(confirm(confirmacao))
            {
                form.submit();
            } 
        }
    }

    function encaminhar(form)
    {

        valida_formulario(form, true);
    }

    function ir_lista()
    {
        location.href = "<?= site_url('cadastro/formulario_periodo_experiencia/minhas') ?>";
    }
</script>
<?php
    $abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
    $abas[] = array('aba_cadastro', 'Responder', TRUE, 'location.reload();');

    echo aba_start($abas);
        echo form_start_box('default_box', $row['ds_formulario_periodo_experiencia']); 
            echo form_default_row('', 'Colaborador:', $row['ds_usuario_avaliado']);
            //echo form_default_row('', 'Cargo:', $row['ds_cargo']);
            echo form_default_row('', 'Admissão:', $row['dt_admissao']);
            echo form_default_row('', 'Área:', $row['divisao']);
            echo form_default_row('', 'Devolução até:', $row['dt_limite']);
        echo form_end_box('default_box');

        echo br();
        echo '<h3>Baixar formulário, preencher e anexar.</h3>';
        echo br();
        echo form_open('cadastro/formulario_periodo_experiencia/salvar_resposta');
            echo form_start_box('default_box', $row['ds_formulario_periodo_experiencia']);
                echo form_default_row('', 'Formulário:', anchor(base_url('up/formulario_periodo_experiencia/formulario.docx'), '<i>Download</i>'));
                echo form_default_upload_iframe('arquivo', 'formulario_periodo_experiencia', 'Formulário Preenchido:', array($row['arquivo'], $row['arquivo_nome']), 'formulario_periodo_experiencia', true);
            echo form_end_box('default_box');
        
    	
            echo form_default_hidden('cd_formulario_periodo_experiencia_solic', '', $row['cd_formulario_periodo_experiencia_solic']);
            echo form_default_hidden('fl_encerrar', '', 'N');
           
	    	echo form_command_bar_detail_start();
                echo button_save('Salvar');
                echo button_save('Salvar e Encaminhar', 'encaminhar(form)', 'botao_verde');
            echo form_command_bar_detail_end();
    	echo form_close();
        echo br(2);
    echo aba_end();

    $this->load->view('footer_interna');
?>