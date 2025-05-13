<?php
	set_title('Inscrições Eleições');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array('ds_formulario_inscricao_eleicao_acompanhamento'), 'salvar_nao_atendeu(form)'); ?>

	function ir_lista()
	{
		 location.href = "<?= site_url('gestao/formulario_inscricao_eleicao') ?>";
	}

	function ir_cadastro()
	{
		 location.href = "<?= site_url('gestao/formulario_inscricao_eleicao/cadastro/'.$row['cd_formulario_inscricao_eleicao']) ?>";
	}

	function atendeu()
	{
		var confirmacao = 'Deseja confirmar a pendencia como ATENDEU?\n\n'+
			'Clique [Ok] para Sim\n\n'+
			'Clique [Cancelar] para Não\n\n';

		if(confirm(confirmacao))
		{ 
			location.href = "<?= site_url('gestao/formulario_inscricao_eleicao/salvar_atendeu/'.$row['cd_formulario_inscricao_eleicao'].'/'.$row['cd_formulario_inscricao_eleicao_acompanhamento']) ?>";
		}
	}

	function salvar_nao_atendeu(form)
	{
		 var confirmacao = 'Deseja confirmar a pendencia como NÃO ATENDEU e encaminhar para o candidato?\n\n'+
			'Clique [Ok] para Sim\n\n'+
			'Clique [Cancelar] para Não\n\n';

		if(confirm(confirmacao))
		{ 
			form.submit();
		}
	}

	function nao_atendeu()
	{
		location.href = "<?= site_url('gestao/formulario_inscricao_eleicao/arquivo/'.$row['cd_formulario_inscricao_eleicao'].'/'.$row['cd_formulario_inscricao_eleicao_acompanhamento'].'/S') ?>";
	}

	function cancelar()
	{
		location.href = "<?= site_url('gestao/formulario_inscricao_eleicao/arquivo/'.$row['cd_formulario_inscricao_eleicao'].'/'.$row['cd_formulario_inscricao_eleicao_acompanhamento']) ?>";
	}

	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),
		[
		    "CaseInsensitiveString",
		    "CaseInsensitiveString",
		    "DateTimeBR"
		]);

		ob_resul.onsort = function ()
		{
			var rows = ob_resul.tBody.rows;
			var l = rows.length;
			for (var i = 0; i < l; i++)
			{
				removeClassName(rows[i], i % 2 ? "sort-par" : "sort-impar");
				addClassName(rows[i], i % 2 ? "sort-impar" : "sort-par");
			}
		};
		ob_resul.sort(2, true);
	}

	$(function(){
		configure_result_table();

		<? if(trim($fl_nao_atendeu) == 'S'): ?>
			$("#ds_formulario_inscricao_eleicao_acompanhamento").focus();

			alert("Informe o motivo e clique em salvar.");
		<? endif; ?>
	});
</script>
<?php

	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_cadastro', 'Cadastro', FALSE, 'ir_cadastro();');	
	$abas[] = array('aba_cadastro', 'Arquivo', TRUE, 'location.reload();');	

	echo aba_start($abas);
		echo form_open('gestao/formulario_inscricao_eleicao/salvar_nao_atendeu');
			echo form_default_hidden('cd_formulario_inscricao_eleicao', '', $row['cd_formulario_inscricao_eleicao']);
			echo form_default_hidden('cd_formulario_inscricao_eleicao_acompanhamento', '', $row['cd_formulario_inscricao_eleicao_acompanhamento']);
			echo form_start_box('default_box', 'Inscrição');
				echo form_default_row('', 'Cargo:','<span class="'.$row['class_cargo'].'">'.$row['tp_cargo'].'</span>','style="width:400px;"');
				echo form_default_row('', 'Dt. Inclusao:', $row['dt_inclusao'], 'style="width:400px;"');
				echo form_default_row('', 'Código:', '<span class="label label-inverse">'.$row['ds_codigo'].'</span>', 'style="width:400px;"');
				echo form_default_row('', 'Status:','<span class="'.$row['class_status'].'">'.$row['ds_status'].'</span>','style="width:400px;"');
				echo form_default_row('', 'Nome:', $row['ds_nome'], 'style="width:400px;"');
				echo form_default_row('', 'CPF:', $row['ds_cpf'], 'style="width:400px;"');
			echo form_end_box('default_box');

	        echo form_start_box('default_solicitacao_box', 'Solicitação');
				echo form_default_row('', 'Status:', '<span class="'.$row['ds_class_solicitacao'].'">'.$row['ds_solicitacao'].'</span>', 'style="width:400px;"');
				echo form_default_row('','Descrição:', nl2br($row['ds_formulario_inscricao_eleicao_acompanhamento']), 'style="width:400px;"', 'style="width:400px;"');
				echo form_default_row('', 'Dt. Inclusão:',$row['dt_inclusao_acompanhamento'], 'style="width:400px;"');
				echo form_default_row('', 'Usuário:', $row['ds_usuario_inclusao_acompanhamento'], 'style="width:400px;"');
				echo form_default_row('', 'Dt. Encaminhamento:', $row['dt_encaminhado'], 'style="width:400px;"');
				echo form_default_row('', 'Dt. Retorno:', $row['dt_solicitacao_atendida'], 'style="width:400px;"');
			echo form_end_box('default_solicitacao_box');
			
			if(trim($fl_nao_atendeu) == 'S' AND trim($row['dt_encaminhado']) != '' AND trim($row['fl_solicitacao']) == '')
			{
				echo form_start_box('default_nao_atendeu_box', 'Não Atendeu');
					echo form_default_textarea('ds_formulario_inscricao_eleicao_acompanhamento', 'Motivo:(*)');
				echo form_end_box('default_nao_atendeu_box');
			}
		
			echo form_command_bar_detail_start();  

				if(trim($row['dt_encaminhado']) != '' AND trim($row['fl_solicitacao']) != 'S' AND trim($fl_nao_atendeu) != 'S')
				{
					echo button_save('Atendeu', 'atendeu()', 'botao_verde');
					echo button_save('Não Atendeu', 'nao_atendeu()', 'botao_vermelho');
				}

				if(trim($fl_nao_atendeu) == 'S' AND trim($row['dt_encaminhado']) != '' AND trim($row['fl_solicitacao']) == '')
				{
					echo button_save('Salvar');
					echo button_save('Cancelar', 'cancelar()', 'botao_disabled');
				}

	        echo form_command_bar_detail_end();
        echo form_close();
		
		$head = array( 
			'Descrição',
			'Arquivo',
			'Dt. Inclusão'
		);

		$body = array();

		foreach($collection as $item)
		{
			$body[] = array(
				array($item['ds_formulario_inscricao_eleicao_acompanhamento_anexo'],'text-align:left'),
				array(anchor(base_url().'up/formulario_inscricao_eleicao/'.$row['ds_codigo'].'/arquivo/'.$item['arquivo'], $item['arquivo_nome']), 'text-align:left;'),
				$item['dt_inclusao']
			);
		}

		$this->load->helper('grid');
		$grid = new grid();
		$grid->head = $head;
		$grid->body = $body;

		echo $grid->render();

	echo aba_end();

	$this->load->view('footer');
?>