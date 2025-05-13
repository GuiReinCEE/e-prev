<?php
	set_title('Reclamações e Sugestões - Ação');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array('descricao')) ?>
	
	function ir_lista()
	{
		location.href = "<?= site_url('ecrm/reclamacao') ?>";
	}
	
	function ir_cadastro()
	{
		location.href = "<?= site_url('ecrm/reclamacao/cadastro/'.$acao['numero'].'/'.$acao['ano'].'/'.$acao['tipo']) ?>";
	}

	function ir_atendimento()
	{
		location.href = "<?= site_url('ecrm/reclamacao/atendimento/'.$acao['numero'].'/'.$acao['ano'].'/'.$acao['tipo']) ?>";
	}

	function ir_prorrogacao()
	{
		location.href = "<?= site_url('ecrm/reclamacao/prorrogacao/'.$acao['numero'].'/'.$acao['ano'].'/'.$acao['tipo']) ?>";
	}	

	function ir_reencaminhamento()
	{
		location.href = "<?= site_url('ecrm/reclamacao/reencaminhamento/'.$acao['numero'].'/'.$acao['ano'].'/'.$acao['tipo']) ?>";
	}
	
	function ir_acompanhamento()
	{
		location.href = "<?= site_url('ecrm/reclamacao/acompanhamento/'.$acao['numero'].'/'.$acao['ano'].'/'.$acao['tipo']) ?>";
	}

	function ir_anexo()
	{
		location.href = "<?= site_url('ecrm/reclamacao/anexo/'.$acao['numero'].'/'.$acao['ano'].'/'.$acao['tipo']) ?>";
	}

	function ir_retorno()
	{
		location.href = "<?= site_url('ecrm/reclamacao/retorno/'.$row['numero'].'/'.$row['ano'].'/'.$row['tipo']) ?>";
	}

	function ir_validacao()
	{
		location.href = "<?= site_url('ecrm/reclamacao/validacao_comite/'.$acao['numero'].'/'.$acao['ano'].'/'.$acao['tipo']) ?>";
	}	

	function ir_parecer_final()
	{
		location.href = "<?= site_url('ecrm/reclamacao/parecer_comite_avaliacao/'.$acao['numero'].'/'.$acao['ano'].'/'.$acao['tipo']) ?>";
	}
	
	function seleciona_reclamacao_retorno_classificacao_pai(cd_reclamacao_retorno_classificacao_pai)
	{
		$.post("<?= site_url('ecrm/reclamacao/get_reclamacao_retorno_classificacao_tipo') ?>", 
		{
			cd_reclamacao_retorno_classificacao_pai : cd_reclamacao_retorno_classificacao_pai
		}, 
		function(data)
		{ 
			if(data.length > 0)
			{
				$("#cd_reclamacao_retorno_classificacao_row").show();
			}
			else
			{
				$("#cd_reclamacao_retorno_classificacao_row").hide();
				$("#cd_reclamacao_retorno_classificacao").val('');
				$('#nr_nc').val("");
				$('#nr_ano_nc').val("");
				$('#nr_ano_nc_nr_nc_row').hide();
				$('#ds_justificativa').val("");
				$('#ds_justificativa_row').hide();
			}
			var select = $('#cd_reclamacao_retorno_classificacao'); 
			
			if(select.prop) 
			{
				var options = select.prop('options');
			}
			else
			{
				var options = select.attr('options');
			}
			   
			$('option', select).remove();
			   
			options[options.length] = new Option('Selecione', '');
			
			$.each(data, function(val, text) {
				options[options.length] = new Option(text.text, text.value);
			});
		}, 'json');
	}
	
	function seleciona_reclamacao_retorno_classificacao_filho(cd_reclamacao_retorno_classificacao)
	{
		if(cd_reclamacao_retorno_classificacao == 6)
		{
			$('#nr_ano_nc_nr_nc_row').show();
			$('#ds_justificativa').val("");
			$('#ds_justificativa_row').hide();
		}
		else if(cd_reclamacao_retorno_classificacao == 8)
		{
			$('#ds_justificativa_row').show();
			$('#nr_nc').val("");
			$('#nr_ano_nc').val("");
			$('#nr_ano_nc_nr_nc_row').hide();
		}
	}
	
	function valida_encerrar(form)
	{
		if(($("#cd_reclamacao_retorno_classificacao_pai").val() == "") && ($("#tipo").val() == "R"))
		{
			alert("Para encerrar é necessário informar a Classificação.");
			$("#cd_reclamacao_retorno_classificacao_pai").focus();
			return false;
		}	
		else if(($("#cd_reclamacao_retorno_classificacao_pai").val() == 1) && ($("#tipo").val() == "R") && ($("#cd_reclamacao_retorno_classificacao").val() == ""))
		{
			alert("Para encerrar é necessário informar o tipo da Classificação");
			$("#cd_reclamacao_retorno_classificacao").focus();
			return false;
		}			
		else if(($("#cd_reclamacao_retorno_classificacao_pai").val() == 1) && ($("#tipo").val() == "R") && ($("#cd_reclamacao_retorno_classificacao").val() == 6) && (($("#nr_ano_nc").val() == "") || (($("#nr_nc").val() == ""))))
		{
			alert("Para encerrar é necessário informar a Não Conformidade");
			$("#nr_ano_nc").focus();
			return false;
		}			
		else if(($("#cd_reclamacao_retorno_classificacao_pai").val() == 1) && ($("#tipo").val() == "R") && ($("#cd_reclamacao_retorno_classificacao").val() == 8) && ($("#ds_justificativa").val() == ""))
		{
			alert("Para encerrar é necessário informar a Justificativa");
			$("#ds_justificativa").focus();
			return false;
		}
		else
		{
			<?php if(trim($acao['tipo']) == 'S'): ?>
				confirma = "ATENÇÃO\n\nDeseja encerrar?\n\nClique [Ok] para Sim\nClique [Cancelar] para Não\n\n";
			<?php else: ?>
				confirma = "ATENÇÃO\n\nDeseja classificar e encerrar?\n\nClique [Ok] para Sim\nClique [Cancelar] para Não\n\n";
			<?php endif; ?>

			if(confirm(confirma))
			{
				form.submit();
			}
			return true;
		}
	}

	function valida_retorno(form)
	{
		if($("#fl_concorda").val() == '')
		{
			alert("Informe se CONCORDA ou NÃO CONCORDA com o parecer do Comitê.");
			$("#fl_concorda").focus();
			return false;
		}
		else if(($("#fl_concorda").val() == 'N') && ($("#ds_justificativa_concorda").val() == ""))
		{
			alert("Informe a Justificativa.");
			$("#ds_justificativa_concorda").focus();
			return false;
		}			
		else
		{
			confirma = "ATENÇÃO\n\nDeseja salvar o retorno?\n\nClique [Ok] para Sim\nClique [Cancelar] para Não\n\n";

			if(confirm(confirma))
			{
				form.submit();
			}

			return true;
		}
	}
	
	function seleciona_concorda(concorda)
	{
		if(concorda == "N")
		{
			$("#ds_justificativa_concorda_row").show();
		}
		else
		{
			$("#ds_justificativa_concorda_row").hide();
			$("#ds_justificativa_concorda").val("");
		}
	}
	
	$(function() {
		<?php if(count($retorno_classificacao_filho) > 0): ?>
			$("#cd_reclamacao_retorno_classificacao_row").show();

			//NÃO CONFORMIDADE
			if($("#cd_reclamacao_retorno_classificacao").val() == 6)
			{
				$('#nr_ano_nc_nr_nc_row').show();
				$('#ds_justificativa').val("");
				$('#ds_justificativa_row').hide();
			}
			//JUSTIFICATIVA
			else if($("#cd_reclamacao_retorno_classificacao").val() == 8)
			{
				$('#ds_justificativa_row').show();
				$('#nr_nc').val("");
				$('#nr_ano_nc').val("");
				$('#nr_ano_nc_nr_nc_row').hide();
			}
		<?php else: ?>
			$("#cd_reclamacao_retorno_classificacao_row").hide();
			$('#nr_nc').val("");
			$('#nr_ano_nc').val("");
			$('#nr_ano_nc_nr_nc_row').hide();
			$('#ds_justificativa').val("");
			$('#ds_justificativa_row').hide();
		<?php endif; ?>

		seleciona_concorda($("#fl_concorda").val());

		<?php if(trim($acao['tipo']) == 'S'): ?>
			$("#encerramento_box").hide();
		<?php endif; ?>

		default_conceito_box_box_recolher();
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

	if($permissao['fl_aba_atendimento'])
	{	
	//	$abas[] = array('aba_atendimento', 'Atendimento', FALSE, 'ir_atendimento();');
	}

	if($permissao['fl_aba_prorrogacao'])
	{	
		$abas[] = array('aba_reencaminahemnto', 'Reencaminhamento', FALSE, 'ir_reencaminhamento();');
		$abas[] = array('aba_prorrogacao', 'Prorrogação', FALSE, 'ir_prorrogacao();');
	}

	$abas[] = array('aba_acompanhamento', 'Acompanhamento', FALSE, 'ir_acompanhamento();');
	$abas[] = array('aba_anexo', 'Anexo', FALSE, 'ir_anexo();');
	$abas[] = array('aba_acao', 'Ação', TRUE, 'location.reload();');

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

	echo aba_start($abas);
		echo form_start_box('default_conceito_box', 'Coneceito da Tela');
			echo form_default_row('conceito', 'Ação:', 'Registro  da solução para a reclamação/sugestão.');
		echo form_end_box('default_conceito_box');
		echo form_start_box('default_reclamacao_box', 'Reclamação');
			echo form_default_row('numero', 'Número:', $reclamacao['cd_reclamacao']);

			if(intval($reclamacao['cd_usuario_responsavel']) > 0)
			{
				echo form_default_row('dt_prazo_acao', 'Dt. Prazo Ação:', '<span class="label label-inverse">'.$reclamacao['dt_prazo_acao'].'</span>');
				
				if(trim($reclamacao['dt_prorrogacao_acao']) != '')
				{
					echo form_default_row('dt_prorrogacao_acao', 'Dt. Prorrogação Ação:', '<span class="label label-info">'.$reclamacao['dt_prorrogacao_acao'].'</span>');
				}

				echo form_default_row('dt_prazo', 'Dt. Prazo Classificação:', '<span class="label label-inverse">'.$reclamacao['dt_prazo'].'</span>');
				
				if(trim($reclamacao['dt_prorrogacao']) != '')
				{
					echo form_default_row('dt_prorrogacao', 'Dt. Prorrogação Classificação:', '<span class="label label-info">'.$reclamacao['dt_prorrogacao'].'</span>');
				}
			}
			
		echo form_end_box('default_reclamacao_box');
		echo form_open('ecrm/reclamacao/salvar_acao');
			echo form_start_box('default_box', 'Cadastro');
				echo form_default_hidden('numero', '', $acao['numero']);
				echo form_default_hidden('ano', '', $acao['ano']);
				echo form_default_hidden('tipo', '', $acao['tipo']);
				echo form_default_hidden('cd_reclamacao_andamento', '', $acao['cd_reclamacao_andamento']);

				if(trim($acao['dt_inclusao']) != '')
				{			
					echo form_default_row('dt_inclusao', 'Dt. Cadastro:', $acao['dt_inclusao']);
					echo form_default_row('ds_usuario_inclusao', 'Usuário:', $acao['ds_usuario_inclusao']);
				}

				if(trim($row['dt_encerramento']) != '')
				{
					echo form_default_row('dt_encerramento', 'Dt. Encerramento:', $row['dt_encerramento']);
				}

				echo form_default_textarea('descricao', 'Descricao: (*)', $acao, 'style="width:500px; height: 100px;"');
			echo form_end_box('default_box');

			echo form_command_bar_detail_start();
				if($permissao['fl_acao_responsavel'] AND trim($row['dt_concorda']) == '' AND trim($acao['tipo']) == 'R')
				{		
					echo button_save('Salvar');
				}

				if($permissao['fl_acao_responsavel'] AND trim($acao['tipo']) == 'S' AND trim($row['dt_encerramento']) == '')
				{
					echo button_save('Encerrar', 'valida_encerrar(this.form);');
				}
			echo form_command_bar_detail_end();
		echo form_close();

		if(trim($acao['dt_inclusao']) != '')
		{
			$link_classificacao = 'ecrm/reclamacao/salvar_classificacao';

			if(trim($row['dt_concorda']) != '')
			{
				$link_classificacao = 'ecrm/reclamacao/salvar_reclassificacao';
			}

			echo form_open($link_classificacao, 'id="salvar_classificacao"');
				echo form_start_box('encerramento_box', 'Classificação');
					echo form_default_hidden('numero', '', $acao['numero']);
					echo form_default_hidden('ano', '', $acao['ano']);
					echo form_default_hidden('tipo', '', $acao['tipo']);
					echo form_default_hidden('cd_reclamacao_comite', '', $classificacao['cd_reclamacao_comite']);
					echo form_default_hidden('cd_reclamacao_andamento', '', $classificacao['cd_reclamacao_andamento']);
					echo form_default_dropdown('cd_reclamacao_retorno_classificacao_pai', 'Classificação: (*)', $retorno_classificacao, array($classificacao['cd_reclamacao_retorno_classificacao_pai']), 'onchange="seleciona_reclamacao_retorno_classificacao_pai(this.value);"');
					echo form_default_dropdown('cd_reclamacao_retorno_classificacao', 'Classificação Tipo: (*)', $retorno_classificacao_filho, array($classificacao['cd_reclamacao_retorno_classificacao']), 'onchange="seleciona_reclamacao_retorno_classificacao_filho(this.value);"');
					echo form_default_integer_ano('nr_ano_nc', 'nr_nc', 'Não Conformidade (Ano/Número): (*)', $classificacao['nr_ano_nc'], $classificacao['nr_nc']);		
					echo form_default_textarea('ds_justificativa', 'Justificativa: (*)', $classificacao['ds_justificativa'], 'style="width:500px; height: 100px;"');

					/*
					if((gerencia_in(array('GCM'))) AND (trim($classificacao['dt_inclusao']) == ''))
					{
						echo form_default_dropdown('fl_encaminhar_comite', 'Encaminhar Comitê: (*)', $fl_encaminhar_comite, 'S');
					}
					else
					{
						echo form_default_hidden('fl_encaminhar_comite', '', 'S');
					}
					*/
					
					echo form_default_hidden('fl_encaminhar_comite', '', 'S');	

					if(intval($classificacao['cd_reclamacao_andamento']) > 0)
					{
						echo form_default_row('dt_inclusao', 'Dt. Classificação:', $classificacao['dt_inclusao']);
						echo form_default_row('ds_usuario_inclusao', 'Usuário:', $classificacao['ds_usuario_inclusao']);
					}
					
				echo form_end_box('encerramento_box');
				echo form_command_bar_detail_start();
					if($permissao['fl_acao_responsavel'] AND trim($acao['tipo']) == 'R' AND trim($row['dt_encerramento']) == '' AND (trim($classificacao['dt_inclusao']) == '') AND (intval($atendimento['cd_usuario_responsavel']) > 0))
					{		
						echo button_save('Encerrar', 'valida_encerrar(this.form);');
					}
					else if(intval($atendimento['cd_usuario_responsavel']) == 0)
					{
						echo '<span class="label label-important">Favor cadastrar o responsável pelo tratamento desta reclamação.</span>';
					}
				echo form_command_bar_detail_end();	
			echo form_close();
			
			if((intval($classificacao['cd_reclamacao_andamento']) > 0 OR trim($row['fl_concorda'] != '')) AND (count($validacao_comite) > 0))
			{
				if(
					((count($parecer_final) > 0 AND trim($parecer_final['dt_parecer_final']) != '' AND trim($row['fl_retorno']) == 'S') 
					OR 
					(trim($row['fl_retorno']) == 'S' AND count($parecer_final) == 0))
					AND 
					trim($row['fl_concorda']) != 'S')
				{
					echo form_open('ecrm/reclamacao/salvar_retorno_responsavel');
						echo form_start_box('default_box_responsavel', 'Retorno');
							echo form_default_hidden('numero', '', $row['numero']);
							echo form_default_hidden('ano', '', $row['ano']);
							echo form_default_hidden('tipo', '', $row['tipo']);
							echo form_default_dropdown('fl_concorda', 'Concorda: (*)', $fl_concorda, $row['fl_concorda'], 'onchange="seleciona_concorda(this.value);"');
							echo form_default_textarea('ds_justificativa_concorda', 'Justificativa: (*)', $row['ds_justificativa_concorda']);
						echo form_end_box('default_box_responsavel');
						echo form_command_bar_detail_start();
							if($permissao['fl_responsavel'] AND trim($row['dt_encerramento']) == '' AND trim($row['dt_concorda']) == '') 
							{
								echo button_save('Salvar', 'valida_retorno(this.form);');
							}
						echo form_command_bar_detail_end();	
					echo form_close();
				}
			}
				$head = array('Nome', 'Status', 'Justificativa', 'Dt. Parecer');	

				$body = array();	
			
				foreach($validacao_comite as $item)
				{
					$class = 'label';

					if(trim($item['fl_confirma']) == 'N')
					{
						$class = 'label label-warning';
					}
					elseif(trim($item['fl_confirma']) == 'S')
					{
						$class = 'label label-success';
					}
					
					$body[] = array(
						array($item['ds_usuario_comite'], 'text-align:left;'),
						'<span class="'.$class.'">'.$item['ds_confirma'].'</span>',
						array(nl2br($item['ds_justificativa_confirma']), 'text-align:justify;'),
						$item['dt_confirma']
					);
				}		

				if(count($parecer_final) > 0 AND trim($parecer_final['dt_parecer_final']) != '')
				{
					$class = 'label';

					if(trim($parecer_final['fl_retorno']) == 'S')
					{
						$class = 'label label-warning';
					}
					elseif(trim($parecer_final['fl_retorno']) == 'N')
					{
						$class = 'label label-success';
					}

					$body[] = array(
						array('<b>'.$parecer_final['ds_usuario_comite'].'</b>', 'text-align:left;'),
						'<span class="'.$class.'">'.$parecer_final['ds_confirma'].'</span>',
						array(nl2br($parecer_final['ds_justificativa_confirma']), 'text-align:justify;'),
						'<b>'.$parecer_final['dt_parecer_final'].'</b>'
					);
				}	
				
				$this->load->helper('grid');
				$grid = new grid();
				$grid->head = $head;
				$grid->body = $body;
				$grid->view_count = false;

				echo form_start_box('validar_box', 'Validação Comitê');
					echo $grid->render();
				echo form_end_box('validar_box');
			
		}
		echo br(2);
	echo aba_end();

	$this->load->view('footer_interna');
?>