<?php
	set_title('Inscrições Eleições');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array('tp_formulario_inscricao_eleicao_acompanhamento', 'ds_formulario_inscricao_eleicao_acompanhamento')); ?>

	function ir_lista()
	{
		 location.href = "<?= site_url('gestao/formulario_inscricao_eleicao') ?>";
	}

	function ir_anexos()
	{
		 location.href = "<?= site_url('gestao/formulario_inscricao_eleicao/anexos/'.$row['cd_formulario_inscricao_eleicao']) ?>";
	}

	function aprovar()
	{
		var confirmacao = 'Deseja homologar a inscrição?\n\n'+
			'Clique [Ok] para Sim\n\n'+
			'Clique [Cancelar] para Não\n\n';

		if(confirm(confirmacao))
		{ 
			location.href = "<?= site_url('gestao/formulario_inscricao_eleicao/aprovar_inscricao/'.$row['cd_formulario_inscricao_eleicao']) ?>";
		}
	}

	function cancelar()
	{
		var confirmacao = 'Deseja cancelar a inscrição?\n\n'+
			'Clique [Ok] para Sim\n\n'+
			'Clique [Cancelar] para Não\n\n';

		if(confirm(confirmacao))
		{ 
			location.href = "<?= site_url('gestao/formulario_inscricao_eleicao/cancelar_inscricao/'.$row['cd_formulario_inscricao_eleicao']) ?>";
		}
	}

	function reprovar()
	{
		location.href = "<?= site_url('gestao/formulario_inscricao_eleicao/cadastro/'.$row['cd_formulario_inscricao_eleicao'].'/S') ?>";
	}

	function acompanhamento_anexo(cd_formulario_inscricao_eleicao_acompanhamento)
	{
		 location.href = "<?= site_url('gestao/formulario_inscricao_eleicao/acompanhamento_anexo/'.$row['cd_formulario_inscricao_eleicao']) ?>/" + cd_formulario_inscricao_eleicao_acompanhamento;
	}

	function encaminhar()
	{
		var confirmacao = 'Deseja encaminhar as pendências de inscrição para o candidato?\n\n'+
			'Clique [Ok] para Sim\n\n'+
			'Clique [Cancelar] para Não\n\n';

		if(confirm(confirmacao))
		{ 
			location.href = "<?= site_url('gestao/formulario_inscricao_eleicao/encaminhar_pendencia/'.$row['cd_formulario_inscricao_eleicao']) ?>";
		}
	}

	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),
		[
		    "CaseInsensitiveString",
		    "CaseInsensitiveString",
		    "DateTimeBR",
		    "CaseInsensitiveString",
		    "CaseInsensitiveString",
		    "DateTimeBR",
		    "DateTimeBR",
		    "CaseInsensitiveString",
		    ""
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

		<? if(trim($fl_impugnacao) == 'S'): ?>
			$("#tp_formulario_inscricao_eleicao_acompanhamento").val("I");
			$("#ds_formulario_inscricao_eleicao_acompanhamento").focus();

			alert("Informe a descrição da impugnação do candidato e clique em salvar.");
		<? endif; ?>
	});
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');
	$abas[] = array('aba_anexos', 'Anexos', FALSE, 'ir_anexos();');	

	$this->load->helper('grid');
	$grid = new grid();

	$head = array( 
		'Tipo',
		'Descrição',
		'Dt. Inclusão',
		'Usuário',
		'Qt. Anexo',
		'Dt. Encaminhamento',
		'Dt. Retorno',
		'Status',
		''
	);

	$body = array();

	echo aba_start($abas);
		echo form_start_box('default_box', 'Cadastro');
			echo form_default_row('', 'Cargo:','<span class="'.$row['class_cargo'].'">'.$row['tp_cargo'].'</span>','style="width:400px;"');

			if($row['tp_cargo'] == 'CAP')
			{
				echo form_default_row('', 'Patrocinador:', $row['ds_patrocinador']);
			}

			echo form_default_row('', 'Dt. Inclusao:', $row['dt_inclusao'], 'style="width:400px;"');
			echo form_default_row('', 'Código:', '<span class="label label-inverse">'.$row['ds_codigo'].'</span>');		
			echo form_default_row('', 'Status:','<span class="'.$row['class_status'].'">'.$row['ds_status'].'</span>','style="width:400px;"');
			echo form_default_row('', 'Nome:', $row['ds_nome'], 'style="width:400px;"');
			echo form_default_row('', 'CPF:', $row['ds_cpf'], 'style="width:400px;"');
			echo form_default_row('', 'Nome na cédula eleitoral:', $row['ds_vinculacao'], 'style="width:400px;"');
			echo form_default_row('', 'Telefone 1:', $row['ds_telefone_1'], 'style="width:400px;"');
			echo form_default_row('', 'Telefone 2:', $row['ds_telefone_2'], 'style="width:400px;"');
			echo form_default_row('', 'E-Mail 1:', $row['ds_email_1']);
			echo form_default_row('', 'E-Mail 2:', $row['ds_email_2']);
			echo form_default_row('', 'Representante:', $row['ds_representante']);
			
			if(trim($row['fl_representante']) == 'S')	
			{
				echo form_default_row('', 'Nome Representante:', $row['ds_nome_representante'], 'style="width:400px;"');
				echo form_default_row('', 'CPF Representante:', $row['ds_cpf_representante']);
				echo form_default_row('', 'Telefone Representante:',$row['ds_telefone_representante']);
				echo form_default_row('', 'E-Mail Representante:', $row['ds_email_representante']);
			}

			if(trim($row['dt_cancelamento']) != '')
			{	
				echo form_default_row('', 'Dt. Cancelamento:', $row['dt_cancelamento']);
			}		

		   	if(trim($row['dt_aprovacao']) != '')
			{
				echo form_default_row('', 'Dt. Aprovação:', $row['dt_aprovacao']);
			}

			if(trim($row['ds_usuario_aprovacao']) != '')
			{
				echo form_default_row('', 'Usuario Aprovação:', $row['ds_usuario_aprovacao']);
			}

			echo form_default_textarea('', 'Qualificação Pessoal:', $row['ds_qualificacao'], 'disabled="" style="height:80px;"');

			echo form_default_row('', 'RG/CNH:', anchor(base_url().'up/formulario_inscricao_eleicao/'.$row['ds_codigo'].'/'.$row['arquivo_identidade'], '[arquivo]', array('target' => '_blank')));
			echo form_default_row('', 'Declaração de Atividades Exercidas:', anchor(base_url().'up/formulario_inscricao_eleicao/'.$row['ds_codigo'].'/'.$row['declaracao_atividade'],'[arquivo]', array('target' => '_blank')));		
			echo form_default_row('', 'Certidões Negativas:', anchor(base_url().'up/formulario_inscricao_eleicao/'.$row['ds_codigo'].'/'.$row['certidao_negativa'], '[arquivo]', array('target' => '_blank')));	

			if($row['tp_cargo'] == 'DE')
			{
				echo form_default_row('', 'Comprovante de Residência:', anchor(base_url().'up/formulario_inscricao_eleicao/'.$row['ds_codigo'].'/'.$row['comprovante_residencia'], '[arquivo]', array('target' => '_blank')));	
				echo form_default_row('', 'Comprovante Nivel Superior:', anchor(base_url().'up/formulario_inscricao_eleicao/'.$row['ds_codigo'].'/'.$row['comprovante_nivel_superior'], '[arquivo]', array('target' => '_blank')));	
			}

			if($row['tp_cargo'] == 'CAP')
			{
				echo form_default_row('', 'Certidão Comitê Ética:', anchor(base_url().'up/formulario_inscricao_eleicao/'.$row['ds_codigo'].'/'.$row['certidao_comite'], '[arquivo]', array('target' => '_blank')));
			}

		echo form_end_box('default_box');
		echo form_command_bar_detail_start();  
			if(trim($fl_impugnacao) == 'N')
			{
				if(trim($row['tp_status']) == 'AN' OR trim($row['tp_status']) == 'IN')
				{
					echo button_save('Homologar Inscrição', 'aprovar()', 'botao_verde');
					echo button_save('Cancelar Inscrição', 'cancelar()', 'botao_vermelho');
				}

				if(trim($row['tp_status']) == 'AN' OR trim($row['tp_status']) == 'AP')
				{
					echo button_save('Impugnar Inscrição', 'reprovar()', 'botao_vermelho');
				}
			}
        echo form_command_bar_detail_end();

		if(trim($row['dt_cancelamento']) == '')
		{
			echo form_open('gestao/formulario_inscricao_eleicao/salvar_acompanhamento');
				echo form_start_box('default_box', 'Registros');
					echo form_default_hidden('cd_formulario_inscricao_eleicao','',$row['cd_formulario_inscricao_eleicao']);
					echo form_default_dropdown('tp_formulario_inscricao_eleicao_acompanhamento', 'Tipo:(*)', $tipo_registro);
					echo form_default_textarea('ds_formulario_inscricao_eleicao_acompanhamento', 'Descrição:(*)','');
				echo form_end_box('default_box');
				echo form_command_bar_detail_start();  
					echo button_save('Salvar');
					if(trim($fl_impugnacao) == 'S')
					{
						echo button_save('Cancelar', 'cancelar()', 'botao_disabled');
					}

					if(trim($row['dt_encaminha_pendencia']) == '' AND intval($row['qt_pendencia']) > 0)
					{
						echo button_save('Encaminhar', 'encaminhar()', 'botao_verde');
					}
	            echo form_command_bar_detail_end();
			echo form_close();
		}

		foreach($collection as $item)
		{
			$body[] = array(
				array($item['ds_tp_acompanhamento'], 'text-align:left'),
				array(nl2br($item['ds_formulario_inscricao_eleicao_acompanhamento']),'text-align:justify'),
				$item['dt_inclusao'],
				$item['ds_usuario_inclusao'],
				$item['qt_anexo'],
				$item['dt_encaminhado'],
				$item['dt_solicitacao_atendida'],
				(trim($item['tp_formulario_inscricao_eleicao_acompanhamento']) == 'S' ? '<span class="'.$item['ds_class_solicitacao'].'">'.$item['ds_solicitacao'].'</span>' : ''),
				(trim($item['tp_formulario_inscricao_eleicao_acompanhamento']) == 'S' ? '<a href="javascript:void(0);" onclick="acompanhamento_anexo('.$item['cd_formulario_inscricao_eleicao_acompanhamento'].')">[arquivo]</a>' : '')
			);
		}

		$grid->head = $head;
		$grid->body = $body;
		echo $grid->render();

	echo aba_end();

	$this->load->view('footer');
?>