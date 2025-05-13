<?php 
	set_title('Cen�rio Legal - Edi��o');
	$this->load->view('header'); 
?>
<script>
	<?= form_default_js_submit(array('tit_capa', 'texto_capa'))	?>

	function ir_lista()
	{
		location.href = "<?= site_url('ecrm/informativo_cenario_legal') ?>";
	}

	function ir_conteudo()
	{
		location.href = "<?= site_url('ecrm/informativo_cenario_legal/conteudo/'.intval($row['cd_edicao'])) ?>";
	}
		
	function excluir()
	{
		var confirmacao = 'Deseja excluir a Edi��o?\n\n'+
            'Clique [Ok] para Sim\n\n'+
            'Clique [Cancelar] para N�o\n\n';

		if(confirm(confirmacao))
		{
			location.href = "<?= site_url('ecrm/informativo_cenario_legal/excluir/'.intval($row['cd_edicao'])) ?>";
		}
	}	
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_detalhe', 'Cadastro', TRUE, 'location.reload();');
	
	if(intval($row['cd_edicao']) > 0)
	{
		$abas[] = array('aba_conteudo', 'Conte�do', FALSE, 'ir_conteudo();');
	}

	echo aba_start($abas);
		echo form_open('ecrm/informativo_cenario_legal/salvar');
			echo form_start_box('default_box', 'Edi��o Cen�rio Legal');
				echo form_default_hidden('cd_edicao', '', intval($row['cd_edicao']));

				if(intval($row['cd_edicao']) > 0)
				{
					echo form_default_row('', 'Edi��o:', '<span class="label label-inverse">'.intval($row['cd_edicao']).'</span>');
					echo form_default_row('dt_edicao', 'Data:', $row['dt_edicao']);

					if(trim($row['dt_exclusao']) != '')
					{
						echo form_default_row('dt_exclusao', 'Data Exclus�o:', $row['dt_exclusao']); 
					} 
				}

				echo form_default_text('tit_capa', 'T�tulo: (*)', $row, 'style="width:350px;"');
				echo form_default_textarea('texto_capa', 'Texto: (*)', $row, 'style="width:500px;:"');
			echo form_end_box('default_box');
			echo form_command_bar_detail_start();
				if(trim($row['dt_exclusao']) == '')
				{
					echo button_save();
				}

				if((intval($row['cd_edicao']) > 0) AND (trim($row['dt_exclusao']) == ''))
				{
					echo button_save('Excluir', 'excluir();', 'botao_vermelho');
				}
			echo form_command_bar_detail_end();
		echo form_close();
		echo br(2);
	echo aba_end();

	$this->load->view('footer_interna');
?>