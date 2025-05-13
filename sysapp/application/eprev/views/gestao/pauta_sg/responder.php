<?php
	set_title('Pauta SG - Responder');
	$this->load->view('header');
?>
<script>
    <?= form_default_js_submit(array('nr_tempo', 
                                     'cd_pauta_sg_objetivo', 
                                     'cd_pauta_sg_justificativa', 
                                     'fl_aplica_detalhamento', 
                                     'fl_aplica_historico', 
                                     'fl_aplica_situacao', 
                                     'fl_aplica_recomendacao', 
                                     'fl_aplica_rds'), 'valida_form(form)') ?>

	function valida_form(form)
	{
		if($("#fl_aplica_rds").val() == "S")
		{
			if($("#fl_rds_restrita").val() == "")
			{
				alert("Informe os campos obrigatórios (fl_rds_restrita)! \n\n(os campos obrigatórios tem um * logo após a identificação.)");
				return false;
			}
			else if($("#nr_ano_rds").val() == "")
			{
				alert("Informe os campos obrigatórios  (nr_ano_rds)! \n\n(os campos obrigatórios tem um * logo após a identificação.)");
				return false;
			}
			else if($("#nr_rds").val() == "")
			{
				alert("Informe os campos obrigatórios (nr_rds)! \n\n(os campos obrigatórios tem um * logo após a identificação.)");
				return false;
			}
			else if($("#arquivo").val() == "" && $("#arquivo_nome").val() == "")
			{	
				alert("Informe os campos obrigatórios (arquivo - arquivo_nome)! \n\n(os campos obrigatórios tem um * logo após a identificação.)");
				return false;
			}
			else
			{
				if($("#fl_ordem_fornecimento").val() == "S")
				{
					if($("#nr_ordem_fornecimento").val() == "")
					{
						alert("Informe os campos obrigatórios! \n\n(os campos obrigatórios tem um * logo após a identificação.)");
						return false;
					}
					else if($("#arquivo_ordem").val() == "" && $("#arquivo_ordem_nome").val() == "")
					{	
						alert("Informe os campos obrigatórios! \n\n(os campos obrigatórios tem um * logo após a identificação.)");
						return false;
					}
					else if($("#arquivo_quadro").val() == "" && $("#arquivo_quadro_nome").val() == "")
					{	
						alert("Informe os campos obrigatórios! \n\n(os campos obrigatórios tem um * logo após a identificação.)");
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
				else 
				{
					if(confirm("Salvar?"))
		            {
		                form.submit();
		            }
				}
			}
		}
		else
		{
			if(confirm("Salvar?"))
            {
                form.submit();
            }
		}
	}

	function ir_lista()
	{
		location.href = "<?= site_url('gestao/pauta_sg/minhas') ?>";
	}

	function ir_anexo()
	{
		location.href = "<?= site_url('gestao/pauta_sg/responder_anexo/'.$row['cd_pauta_sg'].'/'.$assunto['cd_pauta_sg_assunto']) ?>";
	}

	function apresentacao()
	{
		window.open("<?= site_url('gestao/pauta_sg/apresentacao/'.$row['cd_pauta_sg'].'/'.$assunto['cd_pauta_sg_assunto']) ?>");
	}

	function encerrar()
	{
		if($("#fl_anexo_obrigatorio").val() == 'S' && $("#tl_arquivo").val() == 0)
		{
			alert('Esse Objetivo requer documento suporte.');
			ir_anexo();
		}
		else
		{
			var confirmacao;

			if($("#tl_arquivo").val() == 0)
			{
				confirmacao = 'ATENÇÃO : Nenhum documento suporte foi anexado!\n\n'+
				    'Deseja encerrar o assunto da pauta mesmo assim?\n\n'+
					'Clique [Ok] para Sim\n\n'+
					'Clique [Cancelar] para Não\n\n';
			}
			else
			{
				confirmacao = 'Deseja encerrar o assunto da pauta?\n\n'+
					'Clique [Ok] para Sim\n\n'+
					'Clique [Cancelar] para Não\n\n';
			}

			if(confirm(confirmacao))
			{ 
				location.href = "<?= site_url('gestao/pauta_sg/encerrar_assunto/'.$row['cd_pauta_sg'].'/'.$assunto['cd_pauta_sg_assunto']) ?>";
			}
		}
	}

	function se_aplica_detalhamento($aplica)
	{
		if($aplica == 'S')
		{
			$("#ds_detalhamento_row").show();
		}
		else
		{
			$("#ds_detalhamento_row").hide();
			$("#ds_detalhamento").val("");
		}
	}

	function se_aplica_historico($aplica)
	{
		if($aplica == 'S')
		{
			$("#ds_historico_row").show();
		}
		else
		{
			$("#ds_historico_row").hide();
			$("#ds_historico").val("");
		}
	}

	function se_aplica_situacao($aplica)
	{
		if($aplica == 'S')
		{
			$("#ds_situacao_row").show();
		}
		else
		{
			$("#ds_situacao_row").hide();
			$("#ds_situacao").val("");
		}
	}

	function se_aplica_recomendacao($aplica)
	{
		if($aplica == 'S')
		{
			$("#ds_recomendacao_row").show();
		}
		else
		{
			$("#ds_recomendacao_row").hide();
			$("#ds_recomendacao").val("");
		}
	}

	$(function(){
		se_aplica_detalhamento($("#fl_aplica_detalhamento").val());
		se_aplica_historico($("#fl_aplica_historico").val());
		se_aplica_situacao($("#fl_aplica_situacao").val());
		se_aplica_recomendacao($("#fl_aplica_recomendacao").val());
		//se_aplica_rds($("#fl_aplica_rds").val());
		
		<?php if(trim($assunto['fl_ordem_fornecimento'] != 'S')): ?>
            $("#default_servico_box").hide();
        <?php endif ?>

        <?php if(trim($assunto['fl_aplica_rds'] != 'S')): ?>
            $("#default_rds_box").hide();
        <?php endif ?>
	});
</script>

<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_responder', 'Responder', TRUE, 'location.reload();');
	$abas[] = array('aba_anexo', 'Anexos', FALSE, 'ir_anexo();');

	if(trim($row['fl_sumula']) == 'DE')
	{	
		$descricao_ds_recomendacao = 'Recomendação da Gerência';
	}
	else
	{
		$descricao_ds_recomendacao = 'Recomendação da Diretoria Executiva';
	}

	$drop = array(
		array('value' => 'N', 'text' => 'Não'),
		array('value' => 'S', 'text' => 'Sim')
	);

	echo aba_start($abas);
		echo form_start_box('default_box', 'Pauta');
			echo form_default_row('nr_ata', 'Nº da Ata:', '<label class="label label-inverse">'.$row['nr_ata'].'</label>');
			echo form_default_row('fl_sumula', 'Colegiado:', '<span class="'.$row["class_sumula"].'">'.$row["fl_sumula"].'</span>');

			if(trim($row['ds_tipo_reuniao']) != '')
			{
				echo form_default_row('ds_tipo_reuniao', 'Tipo Reunião:', $row['ds_tipo_reuniao']);
			}

			echo form_default_row('dt_reuniao', 'Dt. Reunião:', $row['dt_pauta'].' '.$row['hr_pauta']);

			if(trim($row['dt_pauta_sg_fim']) != '')
			{	
				echo form_default_row('dt_reuniao_fim', 'Dt. Reunião Encerramento:', $row['dt_pauta_sg_fim'].' '.$row['hr_pauta_sg_fim']);
			}

			echo form_default_row('dt_limite', 'Dt. Limite:', '<span class="label label-warning">'.$row['dt_limite'].'</span>');
			
		echo form_end_box('default_box');
		echo form_start_box('default_assunto_box', 'Assunto');
			echo form_default_row('cd_gerencia_responsavel', 'Gerência Responsável:', $assunto['cd_gerencia_responsavel']);
			echo form_default_row('ds_usuario_responsavel', 'Responsável:', $assunto['ds_usuario_responsavel']);
			echo form_default_row('cd_gerencia_substituto', 'Gerência Substituto:', $assunto['cd_gerencia_substituto']);
			echo form_default_row('ds_usuario_substituto', 'Substituto:', $assunto['ds_usuario_substituto']);
			
			if(trim($row['fl_sumula']) == 'DE')
			{	
				echo form_default_row('ds_diretoria', 'Diretoria:', $assunto['ds_diretoria']);
			}

			echo form_default_textarea('ds_pauta_sg_assunto', 'Assunto:', $assunto, 'style="height:80px;"');
		echo form_end_box('default_assunto_box');
        echo form_open('gestao/pauta_sg/responder_salvar');
			echo form_start_box('default_responder_box', 'Assunto - Responder');
				echo form_default_hidden('cd_pauta_sg', '', $row);	
				echo form_default_hidden('cd_pauta_sg_assunto_anexo', '', $assunto);
				echo form_default_hidden('cd_pauta_sg_assunto_ordem_anexo', '', $assunto);
				echo form_default_hidden('cd_pauta_sg_assunto_quadro_anexo', '', $assunto);	
				echo form_default_hidden('cd_pauta_sg_assunto', '', $assunto);	
				echo form_default_hidden('tl_arquivo', '', $assunto['tl_arquivo']);	
				echo form_default_hidden('fl_anexo_obrigatorio', '', $assunto['fl_anexo_obrigatorio']);	
				echo form_default_row('qt_anexo', 'Qt. Anexos:', '<span class="badge badge-inverse">'.$assunto['tl_arquivo'].'</span>');
				echo form_default_integer('nr_tempo', 'Tempo (min): (*)', $assunto);
				echo form_default_dropdown('cd_pauta_sg_objetivo', 'Objetivo: (*)', $objetivo, $assunto['cd_pauta_sg_objetivo']);
				echo form_default_dropdown('cd_pauta_sg_justificativa', 'Justificativa: (*)', $justificativa, $assunto['cd_pauta_sg_justificativa']);
				echo form_default_dropdown('fl_aplica_detalhamento', 'Se Aplica Detalhamento: (*)', $drop, $assunto['fl_aplica_detalhamento'], 'onchange="se_aplica_detalhamento($(this).val())"');
				echo form_default_textarea('ds_detalhamento', 'Detalhamento', $assunto, 'style="height:80px;"');
				echo form_default_dropdown('fl_aplica_historico', 'Se Aplica Histórico: (*)', $drop, $assunto['fl_aplica_historico'], 'onchange="se_aplica_historico($(this).val())"');
				echo form_default_textarea('ds_historico', 'Histórico', $assunto, 'style="height:80px;"');
				echo form_default_dropdown('fl_aplica_situacao', 'Se Aplica Situação Atual: (*)', $drop, $assunto['fl_aplica_situacao'], 'onchange="se_aplica_situacao($(this).val())"');
				echo form_default_textarea('ds_situacao', 'Situação Atual', $assunto, 'style="height:80px;"');
				echo form_default_dropdown('fl_aplica_recomendacao', 'Se Aplica '. $descricao_ds_recomendacao.': (*)', $drop, $assunto['fl_aplica_recomendacao'], 'onchange="se_aplica_recomendacao($(this).val())"');
				echo form_default_textarea('ds_recomendacao', $descricao_ds_recomendacao, $assunto, 'style="height:80px;"');
            echo form_end_box('default_responder_box'); 
            echo form_start_box('default_servico_box', 'Aprovação de Contratação de Serviço');
               	echo form_default_hidden('fl_ordem_fornecimento', '', $assunto['fl_ordem_fornecimento']);
               	echo form_default_integer('nr_ordem_fornecimento', 'Nº de Ordem de Fornecimento: (*)', $assunto['nr_ordem_fornecimento']);
               	echo form_default_upload_iframe('arquivo_ordem','pauta','Ordem de Fornecimento: (*)',array($assunto['arquivo_ordem'],$assunto['arquivo_ordem_nome']),'pauta', (trim($assunto['dt_encerramento']) != '' ? false : true));
               	echo form_default_upload_iframe('arquivo_quadro','pauta','Quadro Comparativo: (*)',array($assunto['arquivo_quadro'],$assunto['arquivo_quadro_nome']),'pauta', (trim($assunto['dt_encerramento']) != '' ? false : true));
            echo form_end_box('default_servico_box');
            echo form_start_box('default_rds_box', 'Relatório Descritivo de Situação - RDS');
                echo form_default_hidden('fl_aplica_rds', '', $assunto['fl_aplica_rds']);
                echo form_default_row('', 'Número/Ano RDS:', $assunto['nr_ano_numero_rds']);
                echo form_default_hidden('nr_rds', '', $assunto);	
                echo form_default_hidden('nr_ano_rds', '', $assunto);	            
                echo form_default_upload_iframe('arquivo', 'pauta', 'RDS: (*)', array($assunto['arquivo'], $assunto['arquivo_nome']), 'pauta', (trim($assunto['dt_encerramento']) != '' ? false : true));
                echo form_default_dropdown('fl_rds_restrita', 'Acesso Restrito: (*)', $drop, $assunto['fl_rds_restrita']);            
            echo form_end_box('default_rds_box');   
			echo form_command_bar_detail_start();
				echo button_save('Salvar');	
				echo button_save('Visualizar Apresentação', 'apresentacao()', 'botao_verde');
				if(trim($assunto['fl_encerrar']) == 'S' AND trim($assunto['dt_encerramento']) == '' AND ((trim($assunto['fl_aplica_rds']) == 'S' AND trim($assunto['arquivo_nome']) != '') OR trim($assunto['fl_aplica_rds']) == 'N'))
				{
					echo button_save('Encerrar', 'encerrar()', 'botao_vermelho');
				}
			echo form_command_bar_detail_end();
		echo form_close();
		echo br(2);
	echo aba_end();
	$this->load->view('footer');
?>