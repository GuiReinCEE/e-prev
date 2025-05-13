<?php
	set_title('Pauta SG Anual');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array('nr_ano', 'fl_colegiado')) ?>

	function ir_lista()
    {
        location.href = "<?= site_url('gestao/pauta_sg_anual/index') ?>";
    }

    function ir_assunto()
    {
        location.href = "<?= site_url('gestao/pauta_sg_anual/assunto/'.$row['cd_pauta_sg_anual']) ?>";
    }

    function enviar_todos()
    {
        var confirmacao = 'Deseja enviar para os responsáveis?\n\n'+
            'Clique [Ok] para Sim\n\n'+
            'Clique [Cancelar] para Não\n\n';

        if(confirm(confirmacao))
        { 
            location.href = "<?= site_url('gestao/pauta_sg_anual/enviar/'.$row['cd_pauta_sg_anual']) ?>";
        }
    }
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');

    if(intval($row['cd_pauta_sg_anual']) > 0)
    {
        $abas[] = array('aba_assunto', 'Assuntos', FALSE, 'ir_assunto();');
    }

	echo aba_start($abas);
		echo form_open('gestao/pauta_sg_anual/salvar');
			echo form_start_box('default_box', 'Cadastro');
                echo form_default_hidden('cd_pauta_sg_anual', '', $row['cd_pauta_sg_anual']); 
                if(intval($row['cd_pauta_sg_anual']) == 0)
                {
                    echo form_default_dropdown('fl_colegiado', 'Colegiado: (*)', $colegiado, $row['fl_colegiado']);
                    echo form_default_text('nr_ano', 'Ano: (*)', $row['nr_ano']);  
                    echo form_default_date('dt_limite', 'Dt. Limite:', $row['dt_limite']);
                }
                else
                {
                    echo form_default_row('ds_colegiado', 'Colegiado:', '<span class="'.$row['ds_class_colegiado'].'">'.$row['ds_colegiado'].'</span>');
                    echo form_default_row('nr_ano', 'Ano:', '<span class="label label-inverse">'.$row['nr_ano'].'</span>');
                    echo form_default_row('dt_limite', 'Dt. Limite:', $row['dt_limite']);
                    echo form_default_date('dt_confirmacao', 'Dt. Confirmação do Colegiado:', $row['dt_confirmacao']);

                    if(trim($row['dt_envio_responsavel']) != '')
                    {
                        echo form_default_row('dt_envio_responsavel', 'Dt. Envio Responsável:', $row['dt_envio_responsavel']);
                        echo form_default_row('ds_usuario_envio_resposanvel', 'Usuário Envio:', $row['ds_usuario_envio_resposanvel']);
                    }
                }
			echo form_end_box('default_box');
			echo form_command_bar_detail_start();
                echo button_save('Salvar'); 

                if(intval($row['cd_pauta_sg_anual']) > 0 AND trim($row['dt_envio_responsavel']) == '')
                {
                    echo button_save('Enviar Emails', 'enviar_todos();', 'botao_vermelho');
                }
		    echo form_command_bar_detail_end();
		echo form_close();        
		echo br(2);
    echo aba_end();

    $this->load->view('footer_interna');
?>