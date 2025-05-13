<?php
	set_title('Não Conformidade - AVlidação de Registros');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array('')) ?>

	function ir_lista()
    {
        location.href = "<?= site_url('gestao/rig/index') ?>";
    }

    function confirmar_validacao()
    {
        var confirmacao = 'Deseja enviar e-mail com a alteração do RIG?\n\n'+
            'Clique [Ok] para Sim\n\n'+
            'Clique [Cancelar] para Não\n\n';

        if(confirm(confirmacao))
        { 
            location.href = "<?= site_url('gestao/rig/enviar/'.$row['cd_rig']) ?>";
        }
    }
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');

	echo aba_start($abas);
		echo form_open('gestao/nc_validacao/salvar');
			echo form_start_box('default_box', 'Cadastro');
				echo form_default_hidden('cd_nao_conformidade', '', $row['cd_nao_conformidade']);
				
			echo form_end_box('default_box');	
			echo form_command_bar_detail_start();
				echo button_save('Salvar');       	            
		    echo form_command_bar_detail_end();
		echo form_close();
		echo br(2);
    echo aba_end();

    $this->load->view('footer_interna');
?>