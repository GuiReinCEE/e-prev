<?php
	set_title('Informativo do Cenário Legal');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array('titulo', 'cd_secao', 'pertinencia'), 'valida_arquivo(form)')	?>

	function valida_arquivo(form)
    {
        if($('#arquivo').val() == '' && $('#arquivo_nome').val() == '')
        {
            alert('Nenhum arquivo foi anexado.');
            return false;
        }
        else
        {
            if( confirm('Salvar?') )
            {
                form.submit();
            }
        }
    }

	function ir_lista()
	{
		location.href = "<?= site_url('ecrm/informativo_cenario_legal') ?>";
	}
	
	function ir_cadastro()
	{
		location.href = "<?= site_url('ecrm/informativo_cenario_legal/cadastro/'.$row['cd_edicao']) ?>";
	}

	function ir_conteudo()
	{
		location.href = "<?= site_url('ecrm/informativo_cenario_legal/conteudo/'.$row['cd_edicao']) ?>";
	}
	
	function ir_anexo()
	{
		location.href = "<?= site_url('ecrm/informativo_cenario_legal/anexo/'.$row['cd_edicao'].'/'.intval($row['cd_cenario'])) ?>";
	}
	
	function excluir()
	{
		var confirmacao = 'Deseja excluir o conteúdo?\n\n'+
			'Clique [Ok] para Sim\n\n'+
			'Clique [Cancelar] para Não\n\n';

		if(confirm(confirmacao))
		{
			location.href = "<?= site_url('ecrm/informativo_cenario_legal/excluir_conteudo/'.$row['cd_edicao'].'/'.intval($row['cd_cenario'])) ?>";
		}
	}

	function cancelar()
	{
		var confirmacao = 'Deseja cancelar o conteúdo?\n\n'+
			'Clique [Ok] para Sim\n\n'+
			'Clique [Cancelar] para Não\n\n';

		if(confirm(confirmacao))
		{
			location.href = "<?= site_url('ecrm/informativo_cenario_legal/cancelar_conteudo/'.$row['cd_edicao'].'/'.intval($row['cd_cenario'])) ?>";
		}
	}

	function enviar_atividade()
	{
		var confirmacao = 'Deseja enviar as atividades para ás áreas que ainda não receberam?\n\n'+
			'Clique [Ok] para Sim\n\n'+
			'Clique [Cancelar] para Não\n\n';

		if(confirm(confirmacao))
		{
			location.href = "<?= site_url('ecrm/informativo_cenario_legal/enviar_atividade/'.$row['cd_edicao'].'/'.intval($row['cd_cenario'])) ?>";
		}
	}

	function seleciona(confirma)
	{
		if(confirma == 'LGIN')
		{
			$("#cd_cenario_referencia_row").show();
		}
		else
		{
			$("#cd_cenario_referencia_row").hide();
		}
	}

	$(function() {
		seleciona($("#cd_secao").val());
	});
</script>

<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_cadastro', 'Cadastro' , FALSE, 'ir_cadastro();');
	$abas[] = array('aba_conteudo', 'Conteúdo' , FALSE, 'ir_conteudo();');
	$abas[] = array('aba_cadastro_conteudo', 'Cadastro de Conteúdo', TRUE, 'location.reload();');

	if(intval($row['cd_cenario']) > 0)
	{
		$abas[] = array('aba_anexo', 'Anexos' , FALSE, 'ir_anexo();');
	}

	echo aba_start($abas);
		echo form_open('ecrm/informativo_cenario_legal/salvar_conteudo');
			echo form_default_hidden('cd_cenario', '', intval($row['cd_cenario']));
			echo form_default_hidden('cd_edicao', '', intval($row['cd_edicao']));

			echo form_start_box('edicao_box', 'Edição');
				echo form_default_row('', 'Edição:', '<span class="label label-inverse">'.intval($edicao['cd_edicao']).'</span>');
				echo form_default_row('', 'Data:', $edicao['dt_edicao']);
				echo form_default_row('', 'Título:', $edicao['tit_capa']);
			echo form_end_box('edicao_box');

			echo form_start_box('cenario_box', 'Cenário legal');
				if(intval($row['cd_cenario']) > 0)
				{
					echo form_default_row('', 'Item:', '<span class="label label-inverse">'.intval($row['cd_cenario']).'</span>');
					echo form_default_row('', 'Dt Inclusão:', $row['dt_inclusao']);
					echo form_default_row('', 'Usuário:', $row['ds_usuario_inclusao']);
				}

				if(trim($row['dt_cancelamento']) != '')
				{
					echo form_default_row('', 'Dt. Cancelamento:', $row['dt_cancelamento']);
					echo form_default_row('', 'Usuário Cancelamento:', $row['ds_usuario_cancelamento']);
				}
				echo form_default_text('titulo', 'Título: (*)', $row, 'style="width:700px;"');
				echo form_default_textarea('referencia', 'Referência:', $row, 'style="width:500px;"');
				echo form_default_text('fonte', 'Fonte:', $row, 'style="width:350px;"');
				echo form_default_dropdown('cd_secao', 'Seção: (*)', $secao, $row['cd_secao'],'onchange="seleciona(this.value);"');
				echo form_default_dropdown('cd_cenario_referencia', 'Legislação Referência:', $legislacao, intval($row['cd_cenario_referencia']));
				echo form_default_checkbox_group('gerencia', 'Áreas indicadas:', $divisao, $cenario_gerencia, 190);
			echo form_end_box('cenario_box');

			echo form_start_box('pagina_box', 'Página');
			echo form_default_upload_iframe('arquivo', 'cenario', 'Normativo: (*)', array($row['arquivo'], $row['arquivo_nome']), 'cenario', true);
				echo form_default_editor_html('conteudo_pagina', '', $row['conteudo'], 'style="height: 400px;"');
			echo form_end_box('pagina_box');

			echo form_start_box('link_box', 'Links');
				echo form_default_text('link1', 'Link 1:', $row, 'style="width:500px;"');
				echo form_default_text('link2', 'Link 2:', $row, 'style="width:500px;"');
				echo form_default_text('link3', 'Link 3:', $row, 'style="width:500px;"');
				echo form_default_text('link4', 'Link 4:', $row, 'style="width:500px;"');
			echo form_end_box('link_box');

			echo form_start_box('implementacao_box', 'Implementação');
				echo form_default_dropdown('pertinencia', 'Pertinência: (*)', $pertinencia, $row['pertinencia']);
				echo form_default_date('dt_legal', 'Prazo Legal:', $row);
				echo form_default_row('dt_prevista', 'Prazo Previsto:', $row['dt_prevista']);
				echo form_default_row('','','<i>Prazo previsto para a implementação das mudanças</i>');
				echo form_default_row('dt_implementacao', 'Data Implantação:', $row['dt_implementacao']);
				echo form_default_row('','','<i> Data em que as mudanças foram efetivamente implementadas</i>');
			echo form_end_box("implementacao_box");

			echo form_command_bar_detail_start();
				if((trim($row['dt_exclusao']) == '') AND (trim($row['dt_cancelamento']) == ''))
				{
					echo button_save();
				}

				if(trim($edicao['dt_envio_email']) != '' AND intval($row['tl_area_enviar']) > 0)
				{
					echo button_save('Enviar Atividades', 'enviar_atividade()', 'botao_vermelho');
				}

				if((intval($row['cd_cenario']) > 0) AND (trim($row['dt_exclusao']) == '') AND (trim($edicao['dt_envio_email']) == ''))
				{
					echo button_save('Excluir', 'excluir()', 'botao_vermelho');
				}

				if((intval($row['cd_cenario']) > 0) AND (trim($row['dt_cancelamento']) == ''))
				{
					echo button_save('Cancelar', 'cancelar()', 'botao_vermelho');
				}
			echo form_command_bar_detail_end();
		echo form_close();
		echo br(2);
	echo aba_end();
	$this->load->view('footer');
?>