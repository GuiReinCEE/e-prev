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
	
	function valida_form_tipo()
	{				
		if( $("#ds_tipo").val()=="" )
		{
			alert( "Informe os campos obrigatórios! \n\n(os campos obrigatórios tem um * logo após a identificação).\n\n[ds_tipo]" );
			$("#ds_tipo").focus();
			return false;
		}
				
		return true;
	}
	
	function listar_tipos()
	{
		$('#result_tipos_div').html("<?php echo loader_html(); ?>");
	
		$.post( '<?php echo site_url('atividade/tarefa/listar_tipos'); ?>',
		{
			cd_atividade : $('#cd_atividade').val(),
			cd_tarefa    : $('#cd_tarefa').val(),
			cd_mandante  : $('#cd_mandante').val()
		},
		function(data)
		{
			$('#result_tipos_div').html(data);
			//configure_result_table();
		});
	}
	
	function adicionar_tipo()
	{
		if(valida_form_tipo())
		{
			$.post( '<?php echo site_url('atividade/tarefa/salvar_tipo'); ?>',
			{
				cd_atividade : $('#cd_atividade').val(),
				cd_tarefa    : $('#cd_tarefa').val(),
				ds_tipo      : $('#ds_tipo').val()
			},
			function()
			{
				$('#ds_tipo').val('');
				listar_tipos();
			});
		}
	}
	
	function valida_form_campo()
	{
		if( $("#campo_nome").val()=="" )
		{
			alert( "Informe os campos obrigatórios! \n\n(os campos obrigatórios tem um * logo após a identificação).\n\n[campo_nome]" );
			$("#campo_nome").focus();
			return false;
		}
				
		return true;
	}
	
	function adicionar_campo(cd_tarefas_layout)
	{
		if(valida_form_campo())
		{
			$.post( '<?php echo site_url('atividade/tarefa/salvar_campo'); ?>',
			{
				cd_atividade         : $('#cd_atividade').val(),
				cd_tarefa            : $('#cd_tarefa').val(),
				cd_tarefas_layout    : cd_tarefas_layout,
				campo_nome           : $('#campo_nome_'+cd_tarefas_layout).val(),
				campo_tamanho        : $('#campo_tamanho_'+cd_tarefas_layout).val(),
				campo_caracteristica : $('#campo_caracteristica_'+cd_tarefas_layout).val(),
				campo_formato        : $('#campo_formato_'+cd_tarefas_layout).val(),
				campo_definicao      : $('#campo_definicao_'+cd_tarefas_layout).val()
			},
			function()
			{
				$('#ds_tipo').val('');
				listar_tipos();
			});
		}
	}
	
	function excluir_tipo(cd_tarefas_layout)
	{
		if( confirm('Deseja excluir o tipo?') )
		{
			$.post( '<?php echo site_url('atividade/tarefa/excluir_tipo'); ?>',
			{
				cd_atividade       : $('#cd_atividade').val(),
			    cd_tarefa          : $('#cd_tarefa').val(),
				cd_tarefas_layout  : cd_tarefas_layout
			},
			function()
			{
				listar_tipos();
			});
		}
	}
	
	function excluir_campo(cd_tarefas_layout_campo)
	{
		if( confirm('Deseja excluir o campo?') )
		{
			$.post( '<?php echo site_url('atividade/tarefa/excluir_campo'); ?>',
			{
				cd_atividade            : $('#cd_atividade').val(),
			    cd_tarefa               : $('#cd_tarefa').val(),
				cd_tarefas_layout_campo : cd_tarefas_layout_campo
			},
			function()
			{
				listar_tipos();
			});
		}
	}
	
	$(function(){
		nivel();
		
		if($('#cd_tarefa').val() != 0)
		{
			listar_tipos();
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
			echo form_default_text("ds_nome_tela", "Nome do Processo:", $row['ds_nome_tela'], 'style="width: 500px;"');
			echo form_default_text("ds_dir", "Diretório:", $row['ds_dir'], 'style="width: 500px;"');
			echo form_default_text("ds_nome_arq", "Nome:", $row['ds_nome_arq'], 'style="width: 500px;"');
			echo form_default_text("ds_delimitador", "Delimitador:", $row['ds_delimitador'], 'style="width: 500px;"');
			echo form_default_dropdown('fl_largura', 'Largura Fixa:', $arr, array($row['fl_largura']));
			echo form_default_textarea("ds_ordem", "Ordenado por:", $row['ds_ordem'],'style="height:100px; width:500px;"');
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
		echo form_start_box( "default_tipo_box", "Tipo" );
			echo form_default_text("ds_tipo", "Tipo:*",'', 'style="width: 500px;"');
		echo form_end_box("default_tipo_box");
		echo form_command_bar_detail_start();  
			if(intval($row['cd_mandante']) == $this->session->userdata('codigo'))
			{
				echo button_save("Adicionar", "adicionar_tipo()");
			}
		echo form_command_bar_detail_end();
		echo '<div id="result_tipos_div"></div>'.br();
	}
echo br(2);

echo aba_end();
$this->load->view('footer_interna');
?>