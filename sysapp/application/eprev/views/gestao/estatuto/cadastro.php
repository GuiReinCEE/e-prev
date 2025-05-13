<?php
	set_title('Estatuto');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array('dt_aprovacao_cd', 'nr_ata_cd', 'dt_envio_spc'), 'valida_arquivo(form)') ?>

	function ir_lista()
    {
        location.href = "<?= site_url('gestao/estatuto/index') ?>";
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
        var confirmacao = 'Deseja enviar e-mail com a alteração do estatuto?\n\n'+
            'Clique [Ok] para Sim\n\n'+
            'Clique [Cancelar] para Não\n\n';

        if(confirm(confirmacao))
        { 
            location.href = "<?= site_url('gestao/estatuto/enviar/'.$row['cd_estatuto']) ?>";
        }
    }
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');

	echo aba_start($abas);
		echo form_open('gestao/estatuto/salvar');
			echo form_start_box('default_box', 'Cadastro');
				echo form_default_hidden('cd_estatuto', '', $row['cd_estatuto']);
		        echo form_default_upload_iframe('arquivo', 'estatuto', 'Arquivo: (*)', array($row['arquivo'], $row['arquivo_nome']), 'estatuto', true);
                echo form_default_date('dt_aprovacao_cd', 'Dt. Aprovação CD: (*)', $row['dt_aprovacao_cd']);
                echo form_default_integer('nr_ata_cd', 'Nº Ata: (*)', $row['nr_ata_cd']);
                echo form_default_date('dt_envio_spc', 'Dt. Envio PREVIC: (*)', $row['dt_envio_spc']);
                echo form_default_date('dt_aprovacao_spc', 'Dt. Aprovação PREVIC:', $row['dt_aprovacao_spc']);
                echo form_default_text('ds_aprovacao_spc', 'Documento PREVIC:', $row['ds_aprovacao_spc'], 'style="width:350px;"');
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