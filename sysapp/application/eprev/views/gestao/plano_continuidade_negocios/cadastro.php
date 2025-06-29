<?php
	set_title('Plano de Continuidade de Neg�cios');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array('dt_referencia'), 'valida_arquivo(form)') ?>

	function ir_lista()
    {
        location.href = "<?= site_url('gestao/plano_continuidade_negocios/index') ?>";
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
        var confirmacao = 'Deseja enviar e-mail com a altera��o do Plano de Continuidade de Neg�cios?\n\n'+
            'Clique [Ok] para Sim\n\n'+
            'Clique [Cancelar] para N�o\n\n';

        if(confirm(confirmacao))
        { 
            location.href = "<?= site_url('gestao/plano_continuidade_negocios/enviar/'.$row['cd_plano_continuidade_negocios']) ?>";
        }
    }
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');

	echo aba_start($abas);
		echo form_open('gestao/plano_continuidade_negocios/salvar');
			echo form_start_box('default_box', 'Cadastro');
				echo form_default_hidden('cd_plano_continuidade_negocios', '', $row['cd_plano_continuidade_negocios']);
                echo form_default_text('nr_versao', 'Vers�o: (*)', $row['nr_versao']);  
				echo form_default_date('dt_referencia', 'Dt. Aprova��o: (*)', $row['dt_referencia']);
		        echo form_default_upload_iframe('arquivo', 'plano_continuidade_negocios', 'Arquivo: (*)', array($row['arquivo'], $row['arquivo_nome']), 'plano_continuidade_negocios', true);
	         	if(trim($row['dt_envio']) != '')
                {
                    echo form_default_row('', 'Dt. Envio:', $row['dt_envio']);
                    echo form_default_row('', 'Usu�rio:', $row['ds_usuario_envio']);
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