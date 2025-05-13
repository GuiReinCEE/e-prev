<?php 
	set_title('Correspondências');
	$this->load->view('header'); 
?>
<script>
	<?= form_default_js_submit(array(
		'data', 
		'divisao', 
		'solicitante_nome', 
		'destinatario_nome', 
		'assunto'  
	)) ?>

	function ir_lista()
	{
		location.href = "<?= site_url('cadastro/sg_correspondencia') ?>";
	}

	function ir_anexo()
	{
		location.href = "<?= site_url('cadastro/sg_correspondencia/anexo/'.intval($row['cd_correspondencia'])) ?>";
	}
	
	function excluir()
	{
		var confirmacao = 'Deseja excluir?\n\n'+
			'Clique [Ok] para Sim\n\n'+
			'Clique [Cancelar] para Não\n\n';
	
		if(confirm(confirmacao))
		{
			location.href = "<?= site_url('cadastro/sg_correspondencia/excluir/'.$row['cd_correspondencia']) ?>";
		}
	}
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_detalhe', 'Cadastro', true, 'location.reload();');

	if(intval($row['cd_correspondencia']) > 0)
	{
		$abas[] = array('aba_anexo', 'Anexo', false, 'ir_anexo();');
	}

	$restrito = array(
		array('value' => 'N', 'text' => 'Não'),
		array('value' => 'S', 'text' => 'Sim')
	);

	echo aba_start($abas);
		echo form_open('cadastro/sg_correspondencia/salvar');
			echo form_start_box('default_box', 'Correspondências');
				echo form_hidden('cd_correspondencia', intval($row['cd_correspondencia']));

				if(intval($row['cd_correspondencia']) > 0)
				{
					echo form_default_row('ano_numero', 'Ano/Número:', '<span class="label label-inverse">'.trim($row['ano_numero']).'</span>');
				}
				
				echo form_default_date('data', 'Data: (*)', $row); 
				echo form_default_dropdown('divisao', 'Gerência: (*)', $gerencia, array($row['divisao']));
				echo form_default_participante(array('solicitante_emp','solicitante_re','solicitante_seq', 'solicitante_nome'), 'RE Solicitante:', $row, TRUE, TRUE);
				echo form_default_text('solicitante_nome', 'Solicitante: (*)', $row, 'style="width:300px;"'); 
				echo form_default_participante(array('assinatura_emp','assinatura_re','assinatura_seq', 'assinatura_nome'), 'RE Assinatura:', $row, TRUE, TRUE);
				echo form_default_text('assinatura_nome', 'Assinatura: (*)', $row, 'style="width:300px;"'); 
				echo form_default_participante(array('cd_empresa','cd_registro_empregado','seq_dependencia', 'destinatario_nome'),'RE Destinatário:', $row, TRUE, TRUE);
				echo form_default_text('destinatario_nome', 'Destinatário:', $row, 'style="width:300px;"'); 
				
				echo form_default_textarea('assunto', 'Assunto: (*)', $row); 

				echo form_default_dropdown('fl_restrito', 'Restrito: (*)', $restrito, array($row['fl_restrito']));
				
			echo form_end_box('default_box');
			echo form_command_bar_detail_start();
				echo button_save();

				if ((intval($row['cd_correspondencia']) > 0) AND ((gerencia_in(array('SG'))) OR (intval($row['cd_usuario_inclusao']) == $this->session->userdata('codigo'))))
				{
					echo button_save('Excluir', 'excluir()', 'botao_vermelho');
				}

			echo form_command_bar_detail_end();
		echo form_close();
		echo br(2);
	echo aba_end();

	$this->load->view('footer_interna');
?>