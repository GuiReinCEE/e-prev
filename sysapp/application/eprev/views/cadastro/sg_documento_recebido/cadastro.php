<?php 
	set_title('Documentos Recebidos');
	$this->load->view('header'); 
?>
<script>
	<?= form_default_js_submit(array(
		'data', 
		'hora', 
		'remetente', 
		'destino_nome', 
		'assunto'
	)) ?>

	function ir_lista()
	{
		location.href = "<?= site_url('cadastro/sg_documento_recebido') ?>";
	}

	function ir_anexo()
	{
		location.href = "<?= site_url('cadastro/sg_documento_recebido/anexo/'.$row['ano'].'/'.$row['numero']) ?>";
	}
	
	function excluir()
	{
		var confirmacao = 'Deseja excluir?\n\n'+
			'Clique [Ok] para Sim\n\n'+
			'Clique [Cancelar] para Não\n\n';
	
		if(confirm(confirmacao))
		{
			location.href = "<?= site_url('cadastro/sg_documento_recebido/excluir/'.$row['ano'].'/'.$row['numero']) ?>";
		}
	}
	
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_detalhe', 'Cadastro', true, 'location.reload();');

	if(trim($row['ano_numero']) != '')
	{
		$abas[] = array('aba_anexo', 'Anexo', false, 'ir_anexo();');
	}

	$restrito = array(
		array('value' => 'N', 'text' => 'Não'),
		array('value' => 'S', 'text' => 'Sim')
	);

	echo aba_start($abas);
		echo form_open('cadastro/sg_documento_recebido/salvar');
			echo form_start_box('default_box', 'Correspondências');
				echo form_hidden('ano', intval($row['ano']));
				echo form_hidden('numero', intval($row['numero']));

				if(trim($row['ano_numero']) != '')
				{
					echo form_default_text('ano_numero', 'Ano/Número:', $row['ano_numero'], 'style="font-weight: bold;width:500px;border: 0px;" readonly' );		
				}

				echo form_default_date('data', 'Data: (*)', $row); 
				echo form_default_time('hora', 'Hora: (*)', $row); 
				echo form_default_text('remetente', 'Remetente: (*)', $row, 'style="width:300px;"');
				echo form_default_participante(array('destino_emp','destino_re','destino_seq', 'destino_nome'), 'RE Destino:', $row, TRUE, TRUE);
				echo form_default_text('destino_nome', 'Destino: (*)', $row, 'style="width:300px;"'); 

				echo form_default_textarea('assunto', 'Assunto: (*)', $row); 

				echo form_default_dropdown('fl_restrito', 'Restrito: (*)', $restrito, array($row['fl_restrito']));
				
			echo form_end_box('default_box');
			echo form_command_bar_detail_start();
				echo button_save();
				if(intval($row['ano_numero']) != '')
				{
					echo button_save('Excluir', 'excluir()', 'botao_vermelho');
				}
			echo form_command_bar_detail_end();
		echo form_close();
		echo br();
	echo aba_end();

	$this->load->view('footer_interna');
?>