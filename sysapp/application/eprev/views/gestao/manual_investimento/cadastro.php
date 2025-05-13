<?php
	set_title('Manual de Investimentos');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array('dt_referencia'), 'valida_arquivo(form)') ?>

	function ir_lista()
    {
        location.href = "<?= site_url('gestao/manual_investimento/index') ?>";
    }

    function valida_arquivo(form)
    {
        if($("#arquivo").val() == "" && $("#arquivo_nome").val() == "")
        {
            alert("Nenhum arquivo foi anexado.");
            return false;
        }
        else
        {
            if(confirm("Salvar?"))
            {
                form.submit();
            }
        }
    }

    function enviar()
    {
        var confirmacao = 'Deseja enviar e-mail com a alteração do Manual de Empréstimo?\n\n'+
            'Clique [Ok] para Sim\n\n'+
            'Clique [Cancelar] para Não\n\n';

        if(confirm(confirmacao))
        { 
            location.href = "<?= site_url('gestao/manual_investimento/enviar/'.$row['cd_manual_investimento']) ?>";
        }
    }
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');

	echo aba_start($abas);
		echo form_open('gestao/manual_investimento/salvar');
			echo form_start_box('default_box', 'Cadastro');
				echo form_default_hidden('cd_manual_investimento', '', $row['cd_manual_investimento']);
				echo form_default_date('dt_referencia', 'Dt. Aprovação: (*)', $row['dt_referencia']);
		        echo form_default_upload_iframe('arquivo', 'manual_investimento', 'Arquivo: (*)', array($row['arquivo'], $row['arquivo_nome']), 'manual_investimento', true);
	         	if(trim($row['dt_envio']) != '')
                {
                    echo form_default_row('', 'Dt. Envio:', $row['dt_envio']);
                    echo form_default_row('', 'Usuário:', $row['ds_usuario_envio']);
                }
			echo form_end_box('default_box');	
			echo form_command_bar_detail_start();
				echo button_save('Salvar');    
                if(trim($row['dt_envio']) == '')
                {
                    echo button_save('Enviar E-mail', 'enviar()', 'botao_verde'); 
                }    	            
		    echo form_command_bar_detail_end();
		echo form_close();
		echo br(2);
    echo aba_end();

    $this->load->view('footer_interna');
?>