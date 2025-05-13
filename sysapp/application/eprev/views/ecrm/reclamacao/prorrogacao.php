<?php
	set_title('Reclamações e Sugestões - Prorrogação');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array('tp_prorrogacao', 'ds_justificativa_prorrogacao'), 'valida_prorrogacao()'); ?>
	
	function ir_lista()
	{
		location.href = "<?= site_url('ecrm/reclamacao') ?>";
	}

	function ir_anexo()
	{
		location.href = "<?= site_url('ecrm/reclamacao/anexo/'.$row['numero'].'/'.$row['ano'].'/'.$row['tipo']) ?>";
	}
	
	function ir_cadastro()
	{
		location.href = "<?= site_url('ecrm/reclamacao/cadastro/'.$row['numero'].'/'.$row['ano'].'/'.$row['tipo']) ?>";
	}

	function ir_reencaminhamento()
	{
		location.href = "<?= site_url('ecrm/reclamacao/reencaminhamento/'.$row['numero'].'/'.$row['ano'].'/'.$row['tipo']) ?>";
	}

	function ir_acao()
	{
		location.href = "<?= site_url('ecrm/reclamacao/acao/'.$row['numero'].'/'.$row['ano'].'/'.$row['tipo']) ?>";
	}

	function ir_acompanhamento()
	{
		location.href = "<?= site_url('ecrm/reclamacao/acompanhamento/'.$row['numero'].'/'.$row['ano'].'/'.$row['tipo']) ?>";
	}

	function ir_retorno()
	{
		location.href = "<?= site_url('ecrm/reclamacao/retorno/'.$row['numero'].'/'.$row['ano'].'/'.$row['tipo']) ?>";
	}

	function ir_atendimento()
	{
		location.href = "<?= site_url('ecrm/reclamacao/atendimento/'.$row['numero'].'/'.$row['ano'].'/'.$row['tipo']) ?>";
	}

	function ir_validacao()
	{
		location.href = "<?= site_url('ecrm/reclamacao/validacao_comite/'.$row['numero'].'/'.$row['ano'].'/'.$row['tipo']) ?>";
	}	

	function ir_parecer_final()
	{
		location.href = "<?= site_url('ecrm/reclamacao/parecer_comite_avaliacao/'.$row['numero'].'/'.$row['ano'].'/'.$row['tipo']) ?>";
	}

	function set_prorrogacao()
	{
		if($("#tp_prorrogacao").val() != '')
		{
			$("#ds_justificativa_prorrogacao_row").show();

			$("#dt_prorrogacao_acao_row").show();
			$("#dt_prorrogacao_row").show();


			if($("#tp_prorrogacao").val() == '1')
			{
				$("#dt_prorrogacao_acao").val($("#dt_prorrogacao_acao_default").val());
				$("#dt_prorrogacao").val($("#dt_prorrogacao_default").val());

				$("#dt_prorrogacao_acao").attr('disabled','disabled');
				$("#dt_prorrogacao").attr('disabled','disabled');

				$("#ds_definicao_justificativa_row").hide();

				$("#arquivo_row").hide();
				$("#ds_definicao_anexo_row").hide();
			}
			else if($("#tp_prorrogacao").val() == '2') 
			{
				$("#dt_prorrogacao_acao").val($("#dt_prorrogacao_acao_default").val());
				$("#dt_prorrogacao").val('');

				$("#dt_prorrogacao_acao").attr('disabled','disabled');
				$("#dt_prorrogacao").removeAttr('disabled');

				$("#ds_definicao_justificativa_row").show();

				$("#arquivo_row").show();
				$("#ds_definicao_anexo_row").show();
			}
			else if($("#tp_prorrogacao").val() == '3') 
			{
				$("#dt_prorrogacao_acao").val('');
				$("#dt_prorrogacao").val('');

				$("#dt_prorrogacao_acao").removeAttr('disabled');
				$("#dt_prorrogacao").removeAttr('disabled');

				$("#ds_definicao_justificativa_row").show();

				$("#arquivo_row").show();
				$("#ds_definicao_anexo_row").show();
			}
		}
		else
		{
			$("#ds_justificativa_prorrogacao_row").hide();
			$("#ds_definicao_justificativa_row").hide();

			$("#dt_prorrogacao_acao_row").hide();
			$("#dt_prorrogacao_row").hide();

			$("#arquivo_row").hide();
			$("#ds_definicao_anexo_row").hide();
		}
	}

	function valida_prorrogacao(form)
	{
		var save = true;

		if($("#tp_prorrogacao").val() == '1')
		{
			save = true;
		}
		else if($("#tp_prorrogacao").val() == '2') 
		{
			if($("#dt_prorrogacao").val() == "")
			{
				alert("Informe a data da prorrogação da classificação.");
				save = false;

				return false;
			}

			if($("#arquivo").val() == "" || $("#arquivo_nome").val() == "")
			{
				alert("Anexe o cronograma de execução.");
				save = false;

				return false;
			}
		}
		else if($("#tp_prorrogacao").val() == '3') 
		{
			if($("#dt_prorrogacao_acao").val() == "")
			{
				alert("Informe a data da prorrogação da ação.");
				save = false;

				return false;
			}

			if($("#dt_prorrogacao").val() == "")
			{
				alert("Informe a data da prorrogação da classificação.");
				save = false;

				return false;
			}

			if($("#arquivo").val() == "" || $("#arquivo_nome").val() == "")
			{
				alert("Anexe o cronograma de execução.");
				save = false;

				return false;
			}
		}

		if(save)
		{
			if(confirm("Deseja Salvar?"))
			{
				$("form").submit();
			}
		}
	}

	$(function(){
		default_conceito_box_box_recolher();

		set_prorrogacao();
	});
</script>
<style>
    #conceito_item {
        white-space:normal !important;
    }
</style>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_reclamacao', 'Cadastro', FALSE, 'ir_cadastro();');
	//$abas[] = array('aba_atendimento', 'Atendimento', FALSE, 'ir_atendimento();');
	$abas[] = array('aba_reencaminahemnto', 'Reencaminhamento', FALSE, 'ir_reencaminhamento();');
	$abas[] = array('aba_prorrogacao', 'Prorrogação', TRUE, 'location.reload();');
	$abas[] = array('aba_acompanhamento', 'Acompanhamento', FALSE, 'ir_acompanhamento();');
	$abas[] = array('aba_anexo', 'Anexo', FALSE, 'ir_anexo();');

	if($permissao['fl_aba_acao'])
	{
		$abas[] = array('aba_acao', 'Ação', FALSE, 'ir_acao();');
	}

	if($permissao['fl_aba_retorno'])
	{
		$abas[] = array('aba_retorno', 'Retorno', FALSE, 'ir_retorno();');
	}

	if($permissao['fl_aba_comite'])
	{
		$abas[] = array('aba_validacao_comite', 'Validação Comitê', FALSE, 'ir_validacao();');
	}

	if($permissao['fl_aba_parecer_final'])
	{
		$abas[] = array('aba_parecer_final', 'Parecer Final', FALSE, 'ir_parecer_final();');
	}

	$prorrogar = array(
		array('value' => '1', 'text' => 'Por igual período'),
		array('value' => '2', 'text' => 'Ação por igual período e a Classificação por outro período'),
		array('value' => '3', 'text' => 'Ação e Classificação por outro período')
	);

	echo aba_start($abas);
		echo form_start_box('default_conceito_box', 'Coneceito da Tela');
			echo form_default_row('conceito', 'Prorrogação:', 'Após analisar a reclamação e verificar que o prazo de 5 dias úteis não é suficiente, poderá prorrogar, desde que haja uma justificativa fundamentada para o novo prazo estabelecido, sendo necessário anexar o cronograma de execução.');
		echo form_end_box('default_conceito_box');

		echo form_start_box('default_reclamacao_box', 'Reclamação');
			echo form_default_row('numero', 'Número:', $reclamacao['cd_reclamacao']);

			if(intval($reclamacao['cd_usuario_responsavel']) > 0)
			{
				echo form_default_row('dt_prazo_acao', 'Dt. Prazo Ação:', '<span class="label label-inverse">'.$reclamacao['dt_prazo_acao'].'</span>');
				echo form_default_row('dt_prazo', 'Dt. Prazo Classificação:', '<span class="label label-inverse">'.$reclamacao['dt_prazo'].'</span>');			
			}
			
		echo form_end_box('default_reclamacao_box');
		
		echo form_open('ecrm/reclamacao/salvar_atendimento_prorrogacao');
			echo form_start_box('default_box_prorrogar', 'Prorrogar');
				echo form_default_hidden('numero', '', $row['numero']);
				echo form_default_hidden('ano', '', $row['ano']);
				echo form_default_hidden('tipo', '', $row['tipo']);
				echo form_default_hidden('cd_operacao', '', $row['cd_operacao']);

				echo form_default_hidden('dt_prorrogacao_acao_default', '', $dt_prorrogacao_acao_default);
				echo form_default_hidden('dt_prorrogacao_default', '', $dt_prorrogacao_default);

				echo form_default_dropdown('tp_prorrogacao', 'Prorrogar: (*)', $prorrogar, array($row['tp_prorrogacao']), 'onchange="set_prorrogacao()"'. (intval($row['tp_prorrogacao']) > 0 ? 'disabled=""' : ''));
				
				if(intval($row['tp_prorrogacao']) == 0)
				{
					echo form_default_date('dt_prorrogacao_acao', 'Dt. Prorrogação Ação: (*)', $row);
					echo form_default_date('dt_prorrogacao', 'Dt. Prorrogação Classificação: (*)', $row);
				}
				else
				{
					echo form_default_row('', 'Dt. Prorrogação Ação:', '<span class="label label-info">'.$row['dt_prorrogacao_acao'].'</span>');
					echo form_default_row('', 'Dt. Prorrogação Classificação:', '<span class="label label-info">'.$row['dt_prorrogacao'].'</span>');
				}
				
				echo form_default_textarea('ds_justificativa_prorrogacao', 'Justificativa: (*)', $row, 'style="height:80px;"');
				echo form_default_row('ds_definicao_justificativa', '', '<i>Fundamentar a necessidade de um prazo maior</i>');
				echo form_default_upload_iframe('arquivo', 'reclamacao', 'Arquivo: (*)', array($row['arquivo'], $row['arquivo_nome']), 'reclamacao', false);
				echo form_default_row('ds_definicao_anexo', '', '<i>Deverá anexar o cronograma de execução de acordo com o prazo proposto para o encerramento</i>');
			echo form_end_box('default_box_prorrogar');
			echo form_command_bar_detail_start();
				if($permissao['fl_acao'] AND intval($row['tp_prorrogacao']) == 0)
				{	
					echo button_save('Salvar');
				}
			echo form_command_bar_detail_end();
		echo form_close();
		echo br(2);
	echo aba_end();
	$this->load->view('footer_interna');
?>