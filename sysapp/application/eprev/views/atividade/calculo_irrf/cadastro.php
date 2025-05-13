<?php
set_title('Cálculo IRRF');
$this->load->view('header');
?>
<script>
	$(function(){
		carrega_tributavel($('#cd_calculo_irrf_tipo_aplicacao'));
		carrega_tipo($('#cd_calculo_irrf_correspondente'));
	
		if($('#cd_calculo_irrf').val() > 0)
		{
			carrega_processos($('#cpf'));
		}
	});
	
	function carrega_tipo($t)
	{
		if($t.val() == '3')
		{
			$('#cd_calculo_irrf_tipo_row').show();
			$('#cd_calculo_irrf_tipo').attr("required", "");
			
			$('#dt_fato_gerador_row').hide();
			$('#dt_fato_gerador').val('');
			$('#dt_fato_gerador').removeAttr("required");
		}
		else if ($t.val() == '2')
		{
			$('#dt_fato_gerador_row').show();
			$('#dt_fato_gerador').attr("required", "");
			
			$('#cd_calculo_irrf_tipo_row').hide();
			$('#cd_calculo_irrf_tipo').val('');
			$('#cd_calculo_irrf_tipo').removeAttr("required");
		}
		else
		{
			$('#cd_calculo_irrf_tipo').val('');
			$('#cd_calculo_irrf_tipo').change();
		
			$('#cd_calculo_irrf_tipo_row').hide();
			$('#cd_calculo_irrf_tipo').removeAttr("required");
			
			$('#dt_fato_gerador_row').hide();
			$('#dt_fato_gerador').val('');
			$('#dt_fato_gerador').removeAttr("required");
		}
	}
	
	function carrega_processos($t)
	{
		$.post('<?php echo site_url('atividade/calculo_irrf/carrega_processos') ?>', 
		{
			cpf : $t.val()
		}, 
		function (data){ 
			var select = $('#ano_nr_processo');
					
			if(select.prop) {
				var options = select.prop('options');
			}
			else 
			{
				var options = select.attr('options');
			}
			
			$('option', select).remove();
			
			options[options.length] = new Option('Selecione', '');
			
			$.each(data, function(val, text) {
				options[options.length] = new Option(text.value, text.value);
				
				if($('#cd_calculo_irrf').val() > 0)
				{
					if(text.value == '<?php echo $row['ano_nr_processo']; ?>')
					{
						$('#ano_nr_processo').val(text.value);
					}
				}
			});
		
		}, 'json');
	}
	
	function carrega_tributavel($t)
	{
		if($t.val() == '1')
		{
			$("#vl_bruto_tributavel_row").show();
			$("#vl_isento_tributacao_row").hide();
		}
		else if($t.val() == '2')
		{
			$("#vl_isento_tributacao_row").show();
			$("#vl_bruto_tributavel_row").hide();
		}
		else if(($t.val() == '3') || ($t.val() == '4'))
		{
			$("#vl_bruto_tributavel_row").show();
			$("#vl_isento_tributacao_row").show();
		}
		else
		{
			$("#vl_bruto_tributavel_row").hide();
			$("#vl_isento_tributacao_row").hide();
		}
	}
	
	function ir_lista()
    {
        location.href='<?php echo site_url("atividade/calculo_irrf"); ?>';
    }
	
	function ir_anexo()
    {
        location.href='<?php echo site_url("atividade/calculo_irrf/anexo/".intval($row['cd_calculo_irrf'])); ?>';
    }
	
	function rejeitar()
    {
        location.href='<?php echo site_url("atividade/calculo_irrf/rejeitar/".intval($row['cd_calculo_irrf'])); ?>';
    }

	function liberar()
	{
		if($('#fl_liberar').val() == 'S')
		{
			if(<?php echo intval($row['tl_anexo_gb']); ?> > 0)
			{
				var confirmacao = 'Deseja liberar?\n\n'+
					'Clique [Ok] para Sim\n\n'+
					'Clique [Cancelar] para Não\n\n';
			
				if(confirm(confirmacao))
				{	
					location.href='<?php echo site_url("atividade/calculo_irrf/liberar/".intval($row['cd_calculo_irrf'])); ?>';
				}
			}
			else
			{
				alert('Nenhum arquivo foi anexado.')
			}
		}
		else
		{
			alert('Informe o RE do participante antes de liberar.');
		}
	}
	
	function salvar_re($t, cd_empresa, cd_registro_empregado, seq_dependencia)
	{
		var confirmacao = 'Deseja salvar o RE?\n\n'+
			'Clique [Ok] para Sim\n\n'+
			'Clique [Cancelar] para Não\n\n';
			
		if(confirm(confirmacao))
		{
			location.href='<?php echo site_url("atividade/calculo_irrf/salvar_re/".intval($row['cd_calculo_irrf'])); ?>/'+cd_empresa+'/'+cd_registro_empregado+'/'+seq_dependencia;
		}
		else
		{
			$t.removeAttr('checked');  
		}
	}
</script>

<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_nc', 'Cadastro', TRUE, 'location.reload();');
$abas[] = array('aba_nc', 'Anexos', FALSE, 'ir_anexo();');

$config['callback'] = 'salvar_re';
$config['emp']['value'] = $row['cd_empresa'];
$config['re']['value']  = $row['cd_registro_empregado'];
$config['seq']['value'] = $row['seq_dependencia'];
$config['row_id'] = "participante_row";
	
echo aba_start( $abas );
	echo form_open('atividade/calculo_irrf/salvar', 'name="filter_bar_form"');
		echo form_start_box("default_box", "Cadastro");
			echo form_default_hidden('fl_liberar', '', (trim($row['cd_registro_empregado']) != '' ? 'S' : 'N'));
			echo form_default_hidden('cd_calculo_irrf', '', $row['cd_calculo_irrf']);
			echo form_default_text('nr_ano_numero', "Ano/Número:", $row, "style='font-weight: bold; width:350px; border: 0px;' readonly" );
			echo form_default_cpf('cpf', 'CPF: *', $row, 'onchange="carrega_processos($(this))"');
			echo form_default_text('nome', 'Nome Reclamante:', $row, 'style="width:350px;"');
			echo form_default_dropdown('ano_nr_processo', 'Nr Processo: *');
			echo form_default_dropdown('cd_calculo_irrf_correspondente', 'Nr Correspondente: *', $arr_correspondente, $row['cd_calculo_irrf_correspondente'], 'onclick="carrega_tipo($(this))"');
			echo form_default_dropdown('cd_calculo_irrf_tipo', 'Calcular: *', $arr_tipo, $row['cd_calculo_irrf_tipo']);
			echo form_default_date('dt_fato_gerador', 'Dt. Fato Gerador: *', $row);
			echo form_default_date('dt_pagamento', 'Prazo Limite de Envio: *', $row);
			if((count($arr_beneficiario) > 0) AND (intval($row['cd_calculo_irrf_tipo']) == 2))
			{
				echo form_default_row("beneficiarios", "Beneficiários:", implode("<br/>", $arr_beneficiario));
			}
		echo form_end_box("default_box");
		echo form_start_box("valor_box", "Valor");
			echo form_default_dropdown('cd_calculo_irrf_tipo_aplicacao', 'Tipo Aplicação :', $arr_tipo_aplicacao, $row['cd_calculo_irrf_tipo_aplicacao'], 'onchange="carrega_tributavel($(this))"');
			echo form_default_numeric('vl_bruto_tributavel', 'Valor tributável:', $row);
			echo form_default_numeric('vl_isento_tributacao', 'Valor não tributável:', $row);
			echo form_default_numeric('vl_contribuicao', 'Valor de contribuição:', $row);
			echo form_default_numeric('vl_custeio_administrativo', 'Valor de custeio administrativo:', $row);
			echo form_default_numeric('vl_desconto_pensao_alimenticia', 'Valor de desconto de pensão alimentícia:', $row);
		echo form_end_box("valor_box");
		if(trim($row['dt_confirma']) == '')
		{
			$body = array();
			$head = array(
			  '',
			  'RE',
			  'Nome'
			);
			
			foreach ($arr_re as $item)
			{
				$checked = "";
				
				if(($item["cd_empresa"] == $row["cd_empresa"]) AND ($item["cd_registro_empregado"] == $row["cd_registro_empregado"]) AND ($item["seq_dependencia"] == $row["seq_dependencia"]))
				{
					$checked = "checked=\"\"";
				}
			
				$body[] = array(
					'<input type="radio" '.$checked.' onchange="salvar_re($(this), '.$item["cd_empresa"].', '.$item["cd_registro_empregado"].', '.$item["seq_dependencia"].')" name="re" value="'.$item["cd_empresa"].'/'.$item["cd_registro_empregado"].'/'.$item["seq_dependencia"].'">',
					$item["cd_empresa"]."/".$item["cd_registro_empregado"]."/".$item["seq_dependencia"],
					array($item["nome"], "text-align:left;")
				);
			}			
			
			$this->load->helper('grid');
			$grid = new grid();
			$grid->head = $head;
			$grid->body = $body;

			echo form_start_box("participante_box", "Participante");
				echo $grid->render();
				/*
				echo form_default_participante_trigger($config);
				echo form_default_text('nome_participante', "Nome:", $row, "style='width:350px; border: 0px;' readonly" );
				*/
			echo form_end_box("participante_box");
		} 
		else if(trim($row['dt_confirma']) != '')
		{
			echo form_start_box("participante_box", "Participante");
				echo form_default_text('re', 'Participante:', (trim($row['cd_registro_empregado']) != '' ? $row['cd_empresa'].'/'.$row['cd_registro_empregado'].'/'.$row['seq_dependencia'] : ''), "style='font-weight: bold; width:350px; border: 0px;' readonly");
				echo form_default_text('nome_participante', 'Participante:', $row['nome_participante'], "style='font-weight: bold; width:350px; border: 0px;' readonly");
			echo form_end_box("participante_box");
		}
		echo form_command_bar_detail_start();    
			if(trim($row['dt_confirma']) == '')
			{
				echo button_save('Liberar', 'liberar()', "botao_verde");
				echo button_save('Rejeitar', 'rejeitar()', "botao_vermelho");
			}
        echo form_command_bar_detail_end();
	echo form_close();
	echo br();
echo aba_end();

$this->load->view('footer_interna');
?>