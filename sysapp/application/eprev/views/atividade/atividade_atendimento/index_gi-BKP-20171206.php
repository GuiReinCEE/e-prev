<?php
set_title('Atividades - Informática - Atendimento');
$this->load->view('header');
?>
<script>
	<?php
		echo form_default_js_submit
			 (
				Array
					(
						'sistema',
						'status_atual',
						'complexidade',
						'fl_balanco_gi'
					),
				'formValidar(form)'
			 );
	?>
	
	function formValidar(form)
	{
        var dt_minima = new Date();
            dt_minima.zeroTime();
			
		var dt_limite_teste = Date.fromString($('#dt_limite_teste').val());
            dt_limite_teste.zeroTime();			
		
		if(($("#status_atual").val() == "ETES") && ($("#dt_limite_teste").val() == ""))
		{
			alert("Informe a Dt Limite para Teste");
			$("#dt_limite_teste").focus();
			return false;
		}
		else if(($("#status_atual").val() == "ETES") && (dt_limite_teste < dt_minima))
		{
			alert("A Dt Limite para Teste deve ser maior ou igual a data de hoje");
			$("#dt_limite_teste").focus();
			return false;
		}		
		else if(($("#status_atual").val() == "ETES") && ($("#fl_teste_relevante").val() == ""))
		{
			alert("Informe se o Teste é relevante");
			$("#fl_teste_relevante").focus();
			return false;
		}	
		else if(($("#status_atual").val() == "ETES") && ($("#cod_testador").val() == ""))
		{
			alert("Informe o Responsável pelo Teste");
			$("#cod_testador").focus();
			return false;
		}		
		else
		{
			if(confirm('Salvar?'))
			{
				form.submit();
			}
			else
			{
				return false;
			}
		}	
	}
	
	function statusCheck()
	{
		if(($("#status_atual").val() == "ETES") && ($("#qt_tarefa_aberta").val() == 0) && ($("#status_anterior").val() != "ETES"))
		{
			$.post('<?php echo site_url('atividade/atividade_atendimento/sugerirDataTeste');?>',{},
			function(data)
			{
				if(confirm("A data sugerida é: " + data.dt_sugerida + "\n\nDeseja utilizar esta data?" ))
				{
					$("#dt_limite_teste").val(data.dt_sugerida);
				}				
			},
			'json');				
		}		
		
		if(($("#status_atual").val() == "ETES") && ($("#qt_tarefa_aberta").val() > 0) && ($("#status_anterior").val() != "ETES"))
		{
			alert("Existe(m) " + $("#qt_tarefa_aberta").val() + " tarefa(s) não concluída(s).\n\nNão é possível mudar o Status.");
			$("#status_atual").val($("#status_anterior").val());
		}		
		else if(($("#status_atual").val() == "CANC") && ($("#qt_tarefa_aberta").val() > 0) && ($("#status_anterior").val() != "CANC"))
		{
			var confirmacao = 'Existe(m) ' + $("#qt_tarefa_aberta").val() + ' tarefa(s) não concluída(s).\n\nDeseja realmente mudar o Status?\n\n'+
							  'Clique [Ok] para Sim\n\n'+
							  'Clique [Cancelar] para Não\n\n';
							  
			if(!confirm(confirmacao))			
			{
				$("#status_atual").val($("#status_anterior").val());
				testeCheck();
			}			
		}	
		else if(($("#status_atual").val() == "AGDF") && ($("#qt_tarefa_aberta").val() > 0) && ($("#status_anterior").val() != "AGDF"))
		{
			var confirmacao = 'Existe(m) ' + $("#qt_tarefa_aberta").val() + ' tarefa(s) não concluída(s).\n\nDeseja realmente mudar o Status?\n\n'+
							  'Clique [Ok] para Sim\n\n'+
							  'Clique [Cancelar] para Não\n\n';
							  
			if(!confirm(confirmacao))			
			{
				$("#status_atual").val($("#status_anterior").val());
				testeCheck();
			}
		}
		else if(($("#status_atual").val() == "SUSP") && ($("#qt_tarefa_aberta").val() > 0) && ($("#status_anterior").val() != "SUSP"))
		{
			var confirmacao = 'Existe(m) ' + $("#qt_tarefa_aberta").val() + ' tarefa(s) não concluída(s).\n\nDeseja realmente mudar o Status?\n\n'+
							  'Clique [Ok] para Sim\n\n'+
							  'Clique [Cancelar] para Não\n\n';
							  
			if(!confirm(confirmacao))			
			{
				$("#status_atual").val($("#status_anterior").val());
				testeCheck();
			}
		}		
		else if(($("#status_atual").val() == "ADIR") && ($("#qt_tarefa_exec").val() > 0) && ($("#status_anterior").val() != "ADIR"))
		{
			var confirmacao = 'Existe(m) ' + $("#qt_tarefa_exec").val() + ' tarefa(s) em manutenção.\n\nDeseja realmente mudar o Status?\n\n'+
							  'Clique [Ok] para Sim\n\n'+
							  'Clique [Cancelar] para Não\n\n';
							  
			if(!confirm(confirmacao))			
			{
				$("#status_atual").val($("#status_anterior").val());
				testeCheck();
			}			
		}	
		else if(($("#status_atual").val() == "AUSR") && ($("#qt_tarefa_aberta").val() > 0) && ($("#status_anterior").val() != "AUSR"))
		{
			var confirmacao = 'Existe(m) ' + $("#qt_tarefa_aberta").val() + ' tarefa(s) não concluída(s).\n\nDeseja realmente mudar o Status?\n\n'+
							  'Clique [Ok] para Sim\n\n'+
							  'Clique [Cancelar] para Não\n\n';
							  
			if(!confirm(confirmacao))			
			{
				$("#status_atual").val($("#status_anterior").val());
				testeCheck();
			}
		}			
	}
	
	function ir_lista()
    {
        location.href='<?php echo site_url("atividade/minhas"); ?>';
    }
	
	function ir_solicitacao()
    {
		location.href='<?php echo site_url('atividade/atividade_solicitacao/index/'.$ar_atividade['cd_gerencia_destino'].'/'.$ar_atividade['numero']);?>';
    }
	
	function ir_historico()
    {
        location.href='<?php echo site_url('atividade/atividade_historico/index/'.$ar_atividade['numero'].'/'.$ar_atividade['cd_gerencia_destino']);?>';
    }
	
	function ir_acompanhamento()
    {
        location.href='<?php echo site_url('atividade/atividade_acompanhamento/index/'.$ar_atividade['numero'].'/'.$ar_atividade['cd_gerencia_destino']);?>';
    }
	
	function ir_anexo()
    {
        location.href='<?php echo site_url('atividade/atividade_anexo/index/'.$ar_atividade['numero'].'/'.$ar_atividade['cd_gerencia_destino']);?>';
    }

    function imprimir()
    {
    	window.open('<?php echo site_url('atividade/atividade_solicitacao/imprimir/'.$ar_atividade['numero'].'/'.$ar_atividade['cd_gerencia_destino']);?>');
    }

	function cronogramaCombo()
	{
		var select = $('#cd_atividade_cronograma');
		if(select.prop) 
		{
			var options = select.prop('options');
		}
		else 
		{
			var options = select.attr('options');
		}
		$('option', select).remove();
		options[options.length] = new Option("Carregando...","");			
			
        $.post('<?php echo site_url('atividade/atividade_atendimento/cronogramaCombo');?>',
        {
            cd_atividade  : $('#numero').val(),
            cod_atendente : $('#cod_atendente').val()
        },
        function(data)
        {
			$('option', select).remove();

			options[options.length] = new Option("Selecione","");
			
			$.each(data, function(i, val) 
			{
				options[options.length] = new Option(val.text, val.value);
			});	
        },
		'json');	
	}
	
	function cronogramaIncluir()
	{
        if($('#cd_atividade_cronograma').val() != "")
		{
			$.post('<?php echo site_url('atividade/atividade_cronograma/salvar_item');?>',
			{
				cd_atividade_cronograma_item : 0,
				cd_atividade_cronograma      : $('#cd_atividade_cronograma').val(),
				cd_atividade                 : $('#numero').val(),
				nr_prioridade_operacional    : "",
				nr_prioridade_gerente        : ""
			},
			function(data)
			{
				cronogramaListar();
			});	
		}
		else
		{
			alert("Informe o cronograma para incluir");
			$('#cd_atividade_cronograma').focus();
		}
	}
	
	function cronogramaExcluir(cd_atividade_cronograma,cd_atividade_cronograma_item)
	{
		var confirmacao = 'Deseja remover do cronograma?\n\n'+
						  'Clique [Ok] para Sim\n\n'+
						  'Clique [Cancelar] para Não\n\n';
						  
		if(confirm(confirmacao))
		{
			$.post('<?php echo site_url('atividade/atividade_cronograma/excluir_item');?>/' + cd_atividade_cronograma + "/" + cd_atividade_cronograma_item,
			{},
			function(data)
			{
				cronogramaListar();
			});	
		}
	}	

	function cronogramaListar()
	{
        if(($('#fl_salvar').val() == "S") && ($('#fl_teste').val() == "N"))
		{
			cronogramaCombo();
		}
		
		$("#ob_cronograma_lista").html("<?php echo loader_html("P"); ?>");

        $.post('<?php echo site_url('atividade/atividade_atendimento/cronogramaListar');?>',
        {
            cd_atividade        : $('#numero').val(),
            cd_gerencia_destino : $('#cd_gerencia_destino').val(),
            fl_salvar           : $('#fl_salvar').val(),
            fl_teste            : $('#fl_teste').val()
        },
        function(data)
        {
            $("#ob_cronograma_lista").html(data);
        });		
	}
	
	function tarefaListar(fl_check_status)
	{
		$("#ob_tarefa").html("<?php echo loader_html("P"); ?>");

        $.post('<?php echo site_url('atividade/atividade_atendimento/tarefaListar');?>',
        {
            cd_atividade        : $('#numero').val(),
			cd_gerencia_destino : $('#cd_gerencia_destino').val()
        },
        function(data)
        {
            $("#ob_tarefa").html(data);
			
			if(fl_check_status)
			{
				statusCheck();
			}
        });		
	}
	
	function testeCheck()
	{
		$("#dt_limite_teste_row").hide();
		$("#fl_teste_relevante_row").hide();
		
		if(($("#status_atual").val() == "ETES") || ($("#status_anterior").val() == "ETES"))
		{
			$("#dt_limite_teste_row").show();
			$("#fl_teste_relevante_row").show();
		}
	}
	
	$(function(){
		$("#default_solucao").hide();
		testeCheck();
		
		if($("#tipo_ativ").val() == "S")
		{
			$("#default_solucao").show();
		}
		
		$('#status_atual').change(function(){ testeCheck(); tarefaListar(true); });
		
		cronogramaListar();
		tarefaListar();
	});
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_lista', 'Solicitação', FALSE, 'ir_solicitacao();');
$abas[] = array('aba_lista', 'Atendimento', TRUE, 'location.reload();');
$abas[] = array('aba_lista', 'Anexo', FALSE, 'ir_anexo();');
$abas[] = array('aba_lista', 'Acompanhamento', FALSE, 'ir_acompanhamento();');
$abas[] = array('aba_lista', 'Histórico', FALSE, 'ir_historico();');

$ar_relevante[] = array('text' => 'Não', 'value' => 'N');
$ar_relevante[] = array('text' => 'Sim', 'value' => 'S');
$ar_balanco_info[] = array('text' => 'Não', 'value' => 'N');
$ar_balanco_info[] = array('text' => 'Sim', 'value' => 'S');

echo aba_start( $abas );
    echo form_open('atividade/atividade_atendimento/salvar');

        echo form_start_box("default_box", "Cadastro");
			echo form_default_hidden('numero', '', intval($ar_atividade['numero']));
			echo form_default_hidden('tipo_ativ', '', trim($ar_atividade['tipo_ativ']));
			echo form_default_hidden('cod_atendente', '', intval($ar_atividade['cod_atendente']));
			echo form_default_hidden('dt_cadastro', '', trim($ar_atividade['dt_cadastro']));
			echo form_default_hidden('dt_env_teste', '', $ar_atividade['dt_env_teste']);
			echo form_default_hidden('cd_gerencia_destino', '', trim($ar_atividade['cd_gerencia_destino']));
			echo form_default_hidden('fl_salvar', '', ($fl_salvar ? "S" : "N"));
			echo form_default_hidden('fl_teste', '', ($fl_teste ? "S" : "N"));
			echo form_default_hidden('qt_tarefa_aberta', '', 0);
			echo form_default_hidden('qt_tarefa_exec', '', 0);
			
			echo form_default_row('nr_prioridade', 'Prioridade:', '<span class="label label-important">'.$ar_atividade['nr_prioridade'].'</span>');
			echo form_default_row('numero_1', 'Número:', '<span class="label">'.trim($ar_atividade['numero']).'</span>');
			echo form_default_row('dt_cad', 'Dt Solicitação:', $ar_atividade['dt_cad']);
			echo form_default_row('dt_env_teste_1', 'Dt Envio Teste:', $ar_atividade['dt_env_teste']);
			echo form_default_row('dt_fim_real_1', 'Dt Fim:', $ar_atividade['dt_fim_real']);
			echo form_default_row('status', 'Status Atual:', '<span class="'.trim($ar_atividade['class_status']).'">'.trim($ar_atividade['status_atividade']).'</span>');
			if(intval($ar_atividade['qt_anexo']) > 0)
			{
				echo form_default_row('', '', '<i>Esta atividade possui anexo(s).</i>');
			}
		echo form_end_box("default_box");	
		
		echo form_start_box("default_solucao", "Banco de Soluções",TRUE,TRUE,$extra='style="display:none;"');
			echo form_default_hidden('cd_atividade_solucao', '', intval($ar_atividade['cd_atividade_solucao']));
			echo form_default_dropdown('cd_solucao_categoria', 'Categoria:', $ar_solucao, $ar_atividade['cd_solucao_categoria']);
			echo form_default_textarea('ds_solucao_assunto', 'Assunto:', $ar_atividade['ds_solucao_assunto'], 'style="width:500px; height:70px;"');		
		echo form_end_box("default_solucao");			
			
		echo form_start_box("default_encaminhamento", "Encaminhamento");	
			echo form_default_hidden('dt_inicio_prev', 'Dt Início Prevista:', $ar_atividade['dt_inicio_prev']); //NAO UTILIZADO
			echo form_default_hidden('dt_fim_prev', 'Dt Término Prevista:', $ar_atividade['dt_fim_prev']); //NAO UTILIZADO	
			
			echo form_default_hidden('dt_inicio_real', 'Dt Início Real:', $ar_atividade['dt_inicio_real']); //NAO UTILIZADO	
			echo form_default_hidden('dt_fim_real_2', 'Dt Fim Real:', $ar_atividade['dt_fim_real']); //NAO UTILIZADO	
		
			echo form_default_dropdown('sistema', 'Projeto:(*)', $ar_sistema, $ar_atividade['sistema']);
			echo form_default_hidden('status_anterior', '', trim($ar_atividade['status_atual']));
			echo form_default_dropdown('status_atual', 'Status:(*)', $ar_status_atual, $ar_atividade['status_atual']);
			
			if(trim($ar_atividade['dt_aguardando_usuario_limite']) != "")
			{
				echo form_default_row('dt_aguardando_usuario_limite', 'Dt Envio p/ Usuário:', $ar_atividade['dt_aguardando_usuario']);
				echo form_default_row('dt_aguardando_usuario_limite', 'Dt Limite p/ Usuário:', '<span class="label label-important">'.$ar_atividade['dt_aguardando_usuario_limite'].'</span>');
			}
			
			echo form_default_date('dt_limite_teste', 'Dt Limite para Teste:', $ar_atividade['dt_limite_teste']);
			echo form_default_dropdown('fl_teste_relevante', 'Teste é Relevante:', $ar_relevante, $ar_atividade['fl_teste_relevante']);
			echo form_default_dropdown('cod_testador', 'Responsável pelo Teste:', $ar_testador, $ar_atividade['cod_testador']);			

			echo form_default_textarea('solucao', 'Descrição da Manutenção:', $ar_atividade['solucao'], 'style="width:500px; height:100px;"');	

			echo form_default_dropdown('complexidade', 'Complexidade:(*)', $ar_complexidade, $ar_atividade['complexidade']);
			echo form_default_dropdown('fl_balanco_gi', 'Balanço Anual - Informática:(*)', $ar_balanco_info, $ar_atividade['fl_balanco_gi']);
		echo form_end_box("default_encaminhamento");	

		echo form_start_box("default_tarefa", "Tarefas");	
			if(($fl_salvar) and (!$fl_teste))
			{
				echo form_default_row('ob_tarefa1', 'Nova:', '
										<a href="'.site_url('atividade/tarefa/cadastro/'.intval($ar_atividade['numero']).'/0/f').'">
											<img src="'.base_url().'img/btn_tarefa_forms.jpg" border="0">
										</a>
										<a href="'.site_url('atividade/tarefa/cadastro/'.intval($ar_atividade['numero']).'/0/r').'">
											<img src="'.base_url().'img/btn_tarefa_reports.jpg" border="0">
										</a>
										<a href="'.site_url('atividade/tarefa/cadastro/'.intval($ar_atividade['numero']).'/0/a').'">
											<img src="'.base_url().'img/btn_tarefa_arquivo.jpg" border="0">
										</a>
										<a href="'.site_url('atividade/tarefa/cadastro/'.intval($ar_atividade['numero']).'/0').'">
											<img src="'.base_url().'img/btn_tarefa_operario.jpg" border="0">
										</a>
									  ');
			}
			echo form_default_row('ob_tarefa2', 'Lista:', '<div id="ob_tarefa"></di>');
		echo form_end_box("default_tarefa");				

		echo form_start_box("default_cronograma", "Cronograma");	
			if(($fl_salvar) and (!$fl_teste))
			{
				echo form_default_dropdown('cd_atividade_cronograma', 'Cronograma:', Array());
				echo form_default_row('ob_cronograma1', '', '<a href="javascript:cronogramaIncluir(); void(0);">[incluir]</a>');
			}
			echo form_default_row('ob_cronograma2', 'Cronograma(s):', '<div id="ob_cronograma_lista"></div>');
		echo form_end_box("default_cronograma");	

		echo form_command_bar_detail_start();  
			if($fl_salvar)
			{		
				echo button_save("Salvar");
				
			}
			echo button_save("Imprimir", 'imprimir()', 'botao_disabled');
		echo form_command_bar_detail_end();
    echo form_close();
	
    echo br(10);	
echo aba_end();

$this->load->view('footer_interna');
?>