<?php
set_title('Tarefa - Definição');
$this->load->view('header');
?>
<script>
<?php
echo form_default_js_submit(array('cd_mandante', 'cd_recurso', 'prioridade', 'fl_checklist', 'resumo', 'descricao'), 'confirma_encaminhamento()');
?>

	function ir_lista()
    {
        location.href='<?php echo site_url("atividade/tarefa"); ?>';
    }
	
	function ir_atividade()
	{
		location.href='<?php echo site_url("atividade/atividade_atendimento/index/".$row['cd_atividade']); ?>';
	}
	
	function imprimir()
	{
		location.href='<?php echo site_url("atividade/tarefa/imprimir/".$row['cd_atividade']."/".$row['cd_tarefa']); ?>';
	}
	
	function ir_execucao()
	{
		location.href='<?php echo site_url("atividade/tarefa_execucao/index/".$row['cd_atividade']."/".$row['cd_tarefa']); ?>';
	}
	
	function ir_anexo()
	{
		location.href='<?php echo site_url("atividade/tarefa_anexo/index/".$row['cd_atividade']."/".$row['cd_tarefa']); ?>';
	}
	
	function ir_checklist()
	{
		location.href='<?php echo site_url("atividade/tarefa_checklist/index/".$row['cd_atividade']."/".$row['cd_tarefa']); ?>';
	}
	
	function ir_historico()
	{
		location.href='<?php echo site_url("atividade/tarefa_historico/index/".$row['cd_atividade']."/".$row['cd_tarefa']); ?>';
	}
	
	function nivel()
	{
		if($('#prioridade').val() == 'S')
		{
			$('#nr_nivel_prioridade_row').show();
			$('#row_nivel_prioridade_row').show();
		}
		else
		{
			$('#nr_nivel_prioridade_row').hide();
			$('#row_nivel_prioridade_row').hide();
			
			$('#nr_nivel_prioridade').val('0');
		}
	}
	
	function encaminhar()
	{
		if( confirm('Deseja encaminhar?') )
		{
			location.href='<?php echo site_url("atividade/tarefa/encaminhar/".$row['cd_atividade']."/".$row['cd_tarefa']); ?>';
		}
	}
	
	function valida_form_lovs()
	{
		if( $("#ds_seq").val()=="" )
		{
			alert( "Informe os campos obrigatórios! \n\n(os campos obrigatórios tem um * logo após a identificação).\n\n[ds_seq]" );
			$("#ds_seq").focus();
			return false;
		}
				
		if( $("#ds_campo_ori").val()=="" )
		{
			alert( "Informe os campos obrigatórios! \n\n(os campos obrigatórios tem um * logo após a identificação).\n\n[ds_campo_ori]" );
			$("#ds_campo_ori").focus();
			return false;
		}
		
		if( $("#ds_campo_des").val()=="" )
		{
			alert( "Informe os campos obrigatórios! \n\n(os campos obrigatórios tem um * logo após a identificação).\n\n[ds_campo_des]" );
			$("#ds_campo_des").focus();
			return false;
		}
		
		return true;
	}
	
	function salvar_lovs()
	{
		if(valida_form_lovs())
		{
			$.post( '<?php echo site_url('atividade/tarefa/salvar_lovs'); ?>',
			{
				cd_atividade : $('#cd_atividade').val(),
				cd_tarefa    : $('#cd_tarefa').val(),
				ds_seq       : $('#ds_seq').val(),
				ds_tabela    : $('#ds_tabela').val(),
				ds_campo_ori : $('#ds_campo_ori').val(),
				ds_campo_des : $('#ds_campo_des').val()
			},
			function()
			{
				listar_lovs();
			});
		}
	}
	
	function listar_lovs()
	{
		$('#result_lovs_div').html("<?php echo loader_html(); ?>");
	
		$.post( '<?php echo site_url('atividade/tarefa/listar_lovs'); ?>',
		{
			cd_atividade : $('#cd_atividade').val(),
			cd_tarefa    : $('#cd_tarefa').val(),
			cd_mandante  : $('#cd_mandante').val()
		},
		function(data)
		{
			$('#result_lovs_div').html(data);
			//configure_result_table();
		});
	}
	
	function excluir_lovs(cd_tarefas_lovs)
	{
		if( confirm('Deseja excluir a lovs?') )
		{
			$.post( '<?php echo site_url('atividade/tarefa/excluir_lovs'); ?>',
			{
				cd_atividade    : $('#cd_atividade').val(),
			    cd_tarefa       : $('#cd_tarefa').val(),
				cd_tarefas_lovs : cd_tarefas_lovs
			},
			function()
			{
				listar_lovs();
			});
		}
	}
	
	function combo_tabela()
	{
		campos_relatorios();
	
		if($('#cd_db').val() != '' && $('#cd_db').val() != 'NOVO')
		{
			$.post( '<?php echo site_url('atividade/tarefa/tabelas'); ?>',
			{
				cd_db : $('#cd_db').val()
			},
			function(data)
			{
				if(data)
				{
					var select = $('#cd_tabela_drop');
					
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
						options[options.length] = new Option(text.ds_tabela, text.ds_tabela);
					});
				}
			}, 'json');
		}
	}
	
	function combo_campo()
	{
		$('#cd_tabela').val($('#cd_tabela_drop').val());
		if($('#cd_tabela').val() != '')
		{
			$.post( '<?php echo site_url('atividade/tarefa/campos'); ?>',
			{
				cd_tabela : $('#cd_tabela').val() ,
				cd_db     : $('#cd_db').val()
			},
			function(data)
			{
				if(data)
				{
					var select = $('#cd_campo_drop');
					
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
						options[options.length] = new Option(text.ds_campo, text.ds_campo);
					});
				}
			}, 'json');
		}	
	}
	
	function campos_relatorios()
	{
		$('#cd_campo').val('');
		$('#cd_tabela').val('');
		$('#cd_campo_drop').val('');
		$('#cd_tabela_drop').val('');
	
		if($('#cd_db').val() != 'NOVO')
		{
			$('#cd_tabela_row').hide();
			$('#cd_tabela_drop_row').show();
			
			$('#cd_campo_row').hide();
			$('#cd_campo_drop_row').show();
		}
		else
		{
			$('#cd_tabela_drop_row').hide();
			$('#cd_tabela_row').show();
			
			$('#cd_campo_drop_row').hide();
			$('#cd_campo_row').show();
		}
	}
	
	function seta_campo()
	{
		$('#cd_campo').val($('#cd_campo_drop').val());
	}
	
	function valida_form_tabela()
	{
		if( $("#cd_db").val()=="" )
		{
			alert( "Informe os campos obrigatórios! \n\n(os campos obrigatórios tem um * logo após a identificação).\n\n[cd_db]" );
			$("#cd_db").focus();
			return false;
		}
				
		if( $("#cd_tabela").val()=="" )
		{
			alert( "Informe os campos obrigatórios! \n\n(os campos obrigatórios tem um * logo após a identificação).\n\n[cd_tabela]" );
			$("#cd_tabela").focus();
			return false;
		}
		
		if( $("#cd_campo").val()=="" )
		{
			alert( "Informe os campos obrigatórios! \n\n(os campos obrigatórios tem um * logo após a identificação).\n\n[cd_campo]" );
			$("#cd_campo").focus();
			return false;
		}
		
		return true;
	}
	function listar_tabelas()
	{
		$('#result_tabelas_div').html("<?php echo loader_html(); ?>");
	
		$.post( '<?php echo site_url('atividade/tarefa/listar_tabelas'); ?>',
		{
			cd_atividade : $('#cd_atividade').val(),
			cd_tarefa    : $('#cd_tarefa').val(),
			cd_mandante  : $('#cd_mandante').val()
		},
		function(data)
		{
			$('#result_tabelas_div').html(data);
			//configure_result_table();
		});
	}
		
	function salvar_tabela()
	{
		if(valida_form_tabela())
		{
			$.post( '<?php echo site_url('atividade/tarefa/salvar_tabela'); ?>',
			{
				cd_atividade : $('#cd_atividade').val(),
				cd_tarefa    : $('#cd_tarefa').val(),
				cd_db        : $('#cd_db').val(),
				cd_tabela    : $('#cd_tabela').val(),
				cd_campo     : $('#cd_campo').val(),
				nr_ordem     : $('#nr_ordem').val()
			},
			function()
			{
				listar_tabelas();
			});
		}
	}
	
	function atualiza_tabela(cd_tarefas_tabelas)
	{
		$.post( '<?php echo site_url('atividade/tarefa/atualiza_tabela'); ?>',
		{
			cd_atividade       : $('#cd_atividade').val(),
			cd_tarefa          : $('#cd_tarefa').val(),
			cd_tarefas_tabelas : cd_tarefas_tabelas,
			ds_campo           : $('#ds_campo_'+cd_tarefas_tabelas).val(),
			fl_campo           : $('#fl_campo_'+cd_tarefas_tabelas).val(),
			ds_vl_dominio      : $('#ds_vl_dominio_'+cd_tarefas_tabelas).val(),
			fl_campo_de        : $('#fl_campo_de_'+cd_tarefas_tabelas).val(),
			ds_label           : $('#ds_label_'+cd_tarefas_tabelas).val(),
			fl_visivel         : $('#fl_visivel_'+cd_tarefas_tabelas).val()
		},
		function()
		{
			listar_tabelas();
		});
	}
	
	function atualiza_ordenacao(cd_tarefas_tabelas)
	{
		if( confirm('Deseja salvar a ordenação?') )
		{
			$.post( '<?php echo site_url('atividade/tarefa/atualiza_ordenacao'); ?>',
			{
				cd_atividade       : $('#cd_atividade').val(),
				cd_tarefa          : $('#cd_tarefa').val(),
				cd_tarefas_tabelas : cd_tarefas_tabelas,
				nr_ordem           : $('#nr_ordem_'+cd_tarefas_tabelas).val()
			},
			function()
			{
				listar_tabelas();
			});
		}
	}
	
	function excluir_tabela(cd_tarefas_tabelas)
	{
		if( confirm('Deseja excluir tabela?') )
		{
			$.post( '<?php echo site_url('atividade/tarefa/excluir_tabela'); ?>',
			{
				cd_atividade       : $('#cd_atividade').val(),
				cd_tarefa          : $('#cd_tarefa').val(),
				cd_tarefas_tabelas : cd_tarefas_tabelas
			},
			function()
			{
				listar_tabelas();
			});
		}
	}
	
	$(function(){
		nivel();
		campos_relatorios();
		
		if($('#cd_tarefa').val() != 0)
		{
			listar_lovs();
			listar_tabelas();
		}
	})
	
	function nao_conforme()
	{
		if( confirm('Tarefa realmente NÃO ATENDEU?') )
		{
			location.href='<?php echo site_url("atividade/tarefa/nao_conforme/".$row['cd_atividade']."/".$row['cd_tarefa']); ?>';
		}
	}
	
	function conforme()
	{
		location.href='<?php echo site_url("atividade/tarefa/conforme/".$row['cd_atividade']."/".$row['cd_tarefa']); ?>';
	}
	
	function excluir_tarefa()
	{
		if( confirm('Deseja excluir a tarefa?') )
		{
			location.href='<?php echo site_url("atividade/tarefa/excluir_tarefa/".$row['cd_atividade']."/".$row['cd_tarefa']); ?>';
		}
	}
	
	function confirma_encaminhamento()
	{
		if($('#cd_tarefa').val() == 0)
		{
			if(confirm("Deseja encaminhar tarefa?\n\n [OK] para SIM\n [Cancelar] para NÃO"))
			{
				$('#fl_encaminhamento').val('S');
			}
			else
			{
				$('#fl_encaminhamento').val('N');
			}
		}
		
		$('form').submit();
	}

</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_cadastro', 'Atividade', FALSE, 'ir_atividade()');
$abas[] = array('aba_lista', 'Definição', TRUE, 'location.reload();');

if(intval($row['cd_tarefa']) > 0)
{

	$abas[] = array('aba_lista', 'Execução', FALSE, 'ir_execucao();');
	if(trim($row['fl_checklist']) == 'S')
	{
		$abas[] = array('aba_lista', 'Checklist', FALSE, 'ir_checklist();');
	}
	$abas[] = array('aba_lista', 'Anexo', FALSE, 'ir_anexo();');
	$abas[] = array('aba_lista', 'Histórico', FALSE, 'ir_historico();');
}

$arr[] = array('value' => 'N', 'text' => 'Não');
$arr[] = array('value' => 'S', 'text' => 'Sim');

$arr_nivel[] = array('value' => '0', 'text' => '0');
$arr_nivel[] = array('value' => '1', 'text' => '1');
$arr_nivel[] = array('value' => '2', 'text' => '2');
$arr_nivel[] = array('value' => '3', 'text' => '3');
$arr_nivel[] = array('value' => '4', 'text' => '4');
$arr_nivel[] = array('value' => '5', 'text' => '5');
$arr_nivel[] = array('value' => '6', 'text' => '6');
$arr_nivel[] = array('value' => '7', 'text' => '7');
$arr_nivel[] = array('value' => '8', 'text' => '8');
$arr_nivel[] = array('value' => '9', 'text' => '9');
$arr_nivel[] = array('value' => '10', 'text' => '10');

$arr_banco[] = array('value' => 'POSTGRESQL', 'text' => 'POSTGRESQL');
$arr_banco[] = array('value' => 'ORACLE', 'text' => 'ORACLE');
$arr_banco[] = array('value' => 'NOVO', 'text' => 'Novo');

echo aba_start($abas);
	if(trim($row['status_atual']) == 'Liberada' AND intval($row['cd_mandante']) == $this->session->userdata('codigo'))
	{
		echo form_start_box( "default_liberacao_box", "Validação da Tarefa" );
			echo form_default_row('row_liberacao', '',  br().button_save("Atendeu", 'conforme()', 'botao_verde').'  '.button_save("Não Atendeu", 'nao_conforme()', 'botao_vermelho'));
		echo form_end_box("default_liberacao_box");
	}
	echo form_open('atividade/tarefa/salvar', 'name="filter_bar_form"');
		echo form_start_box( "default_box", "Cadastro" );
			echo form_default_hidden('cd_atividade', '', $row['cd_atividade']);
			echo form_default_hidden('cd_tarefa', '', $row['cd_tarefa']);
			echo form_default_hidden('fl_tarefa_tipo', '', $row['fl_tarefa_tipo']);
			if(intval($row['cd_tarefa']) > 0)
			{
				echo form_default_text("atividade_os", "Atividade/Tarefa:", $row['cd_atividade'].' / '.$row['cd_tarefa'], 'style="width: 500px; border: 0px;" readonly');
				echo form_default_text("status", "Status:", $row['status_atual'], 'style="width: 500px; border: 0px; font-weight:bold; color:'.trim($row['status_cor']).'" readonly');
				if(trim($row['dt_encaminhamento']) == '')
				{
					echo form_default_row('row_encaminhamento', '', '<b style="color:red;">Tarefa não foi encaminhada.</b>');
				}
				echo form_default_text("dt_inicio_prog", "Dt de Início da Tarefa:", $row['dt_inicio_prog'], 'style="width: 500px; border: 0px; color:gray; font-weight:bold;" readonly');
				echo form_default_text("dt_fim_prog", "Dt de Fim da Tarefa:", $row['dt_fim_prog'], 'style="width: 500px; border: 0px; color:green; font-weight:bold;" readonly');
				echo form_default_text("dt_ok_anal", "Dt Acordo:", $row['dt_ok_anal'], 'style="width: 500px; border: 0px; color:blue; font-weight:bold;" readonly');
			}
			else
			{
				echo form_default_text("atividade_os", "Atividade:", $row['cd_atividade'], 'style="width: 500px; border: 0px;" readonly');
				echo form_default_hidden('fl_encaminhamento');
				//echo form_default_dropdown('fl_encaminhamento', 'Encaminhar ao Salvar:*', $arr);
			}
			echo form_default_dropdown('programa', 'Nome do programa:', $arr_programa, array($row['programa']));
			echo form_default_dropdown('cd_tipo_tarefa', 'Tipo da tarefa:', $arr_tipo_tarefa, array($row['cd_tipo_tarefa']));
			echo form_default_dropdown('cd_mandante', 'Analista:*', $arr_analista, array($row['cd_mandante']));
			echo form_default_dropdown('cd_recurso', 'Programador:*', $arr_programador, array($row['cd_recurso']));
			echo form_default_dropdown('prioridade', 'Prioridade:*', $arr, array($row['prioridade']), 'onchange="nivel()"');
			echo form_default_dropdown('nr_nivel_prioridade', 'Nível de prioridade:', $arr_nivel, array($row['nr_nivel_prioridade']));
			echo form_default_row('row_nivel_prioridade', '', '<i>0 é o menor nível e 10 é o maior nível de prioridade.</i>');
			echo form_default_dropdown('fl_checklist', 'Checklist:*', $arr, array($row['fl_checklist']));
			echo form_default_date('dt_inicio_prev', 'Dt de Início Prevista:', $row['dt_inicio_prev']);
			echo form_default_date('dt_fim_prev', 'Dt de Término Prevista:', $row['dt_fim_prev']);	
			echo form_default_text("resumo", "Resumo:*", $row['resumo'], 'style="width: 500px;"');
			echo form_default_textarea("descricao", "Descrição:*", $row['descricao'],'style="height:100px; width:500px;"');
			echo form_default_textarea("casos_testes", "Funcionalidades/restrições da seleção (regras):", $row['casos_testes'],'style="height:100px; width:500px;"');
			echo form_default_textarea("tabs_envolv", "Funções ou procedimentos a serem utilizados:", $row['tabs_envolv'],'style="height:100px; width:500px;"');
			echo form_default_text("ds_nome_tela", "Nome da Tela (Forms):", $row['ds_nome_tela'], 'style="width: 500px;"');
			echo form_default_textarea("ds_menu", "Menu:", $row['ds_menu'],'style="height:100px; width:500px;"');
		
		echo form_end_box("default_box");
		echo form_command_bar_detail_start();  
			if(trim($row['dt_ok_anal']) == '')
			{
				if((intval($row['cd_tarefa']) == 0) OR (intval($row['cd_tarefa']) > 0 AND intval($row['cd_mandante']) == $this->session->userdata('codigo') AND trim($row['status_atual']) != 'Liberada'))
				{
					echo button_save("Salvar");
				}
				
				if(intval($row['cd_tarefa']) > 0 AND intval($row['cd_mandante']) == $this->session->userdata('codigo'))
				{
					echo button_save("Excluir", 'excluir_tarefa()', 'botao_vermelho');
				}
			}
			if(intval($row['cd_tarefa']) > 0)
			{
				if(intval($row['cd_mandante']) == $this->session->userdata('codigo') AND trim($row['dt_encaminhamento']) == '' AND trim($row['status_atual']) != 'Liberada')
				{
					echo button_save("Encaminhar", 'encaminhar()', 'botao_verde');
				}
				echo button_save("Imprimir", 'imprimir()', 'botao_disabled');
			}
        echo form_command_bar_detail_end();
	echo form_close();	
	
	if(intval($row['cd_tarefa']) > 0)
	{
		echo form_start_box( "default_box_lovs", "Lovs" );
			echo form_default_hidden('cd_atividade', '', $row['cd_atividade']);
			echo form_default_hidden('cd_tarefa', '', $row['cd_tarefa']);
			echo form_default_text("ds_seq", "Seq:", '', 'style="width: 300px;"');
			echo form_default_text("ds_tabela", "Tabela:", '', 'style="width: 300px;"');
			echo form_default_text("ds_campo_ori", "Campo Origem:", '', 'style="width: 300px;"');
			echo form_default_text("ds_campo_des", "Campo Destino:", '', 'style="width: 300px;"');
							
		echo form_end_box("default_box_lovs");
		echo form_command_bar_detail_start(); 
			if(intval($row['cd_mandante']) == $this->session->userdata('codigo'))
			{
				echo button_save("Adicionar", "salvar_lovs()");
			}
		echo form_command_bar_detail_end();	
		echo '<div id="result_lovs_div"></div>'.br();
		
		echo form_start_box( "default_box_tabela", "Detalhes da Tela" );
			echo form_default_dropdown('cd_db', 'Banco:*', $arr_banco, '', 'onchange="combo_tabela()"');
			echo form_default_dropdown('cd_tabela_drop', 'Tabela:*', array(), '', 'onchange="combo_campo()"');
			echo form_default_text('cd_tabela', 'Tabela:*', '', 'style="width: 300px;"');
			echo form_default_dropdown('cd_campo_drop', 'Campo:*', array(), '', 'onchange="seta_campo()"');
			echo form_default_text('cd_campo', 'Campo:*', '', 'style="width: 300px;"');
			echo form_default_integer('nr_ordem', 'Ordem:*', 0);
		echo form_end_box("default_box_tabela");
		echo form_command_bar_detail_start(); 
			if(intval($row['cd_mandante']) == $this->session->userdata('codigo'))
			{
				echo button_save("Adicionar", "salvar_tabela()");
			}
		echo form_command_bar_detail_end();	
		echo '<div id="result_tabelas_div"></div>'.br();
	}
echo br(2);

echo aba_end();
$this->load->view('footer_interna');
?>