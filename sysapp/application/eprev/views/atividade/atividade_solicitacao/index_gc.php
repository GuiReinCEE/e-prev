<?php
set_title('Atividades - '.trim($row['cd_gerencia_destino_nova']));
$this->load->view('header');
?>
<script>
<?php
	echo form_default_js_submit
	 (
		Array('cod_solicitante', 'tipo_solicitacao', 'tipo',  'titulo', 'descricao', 'cod_atendente'),
		'formValidar(form)'
	 );	
?>

	function formValidar(form)
	{
		if(($('#fl_abrir_encerrar').val() == "S") && (jQuery.trim($('#cd_gerencia_abrir_ao_encerrar').val()) == ""))
		{
			alert("Informe a Nova atividade");
			$("#cd_gerencia_abrir_ao_encerrar").focus();
			return false;
		}
		else if(($('#fl_abrir_encerrar').val() == "S") && (jQuery.trim($('#cd_tipo_solicitacao_abrir_ao_encerrar').val()) == ""))
		{
			alert("Informe o Tipo da Solicitação da Nova Atividade");
			$("#cd_tipo_solicitacao_abrir_ao_encerrar").focus();
			return false;
		}
		else if(($('#fl_abrir_encerrar').val() == "S") && (jQuery.trim($('#cd_tipo_abrir_ao_encerrar').val()) == ""))
		{
			alert("Informe o Tipo da Nova Atividade");
			$("#cd_tipo_solicitacao_abrir_ao_encerrar").focus();
			return false;
		}		
		else if(($('#fl_abrir_encerrar').val() == "S") && (jQuery.trim($('#cd_usuario_abrir_ao_encerrar').val()) == ""))
		{
			alert("Informe o Atendente da Nova Atividade");
			$("#cd_usuario_abrir_ao_encerrar").focus();
			return false;
		}		
		else if(($('#fl_abrir_encerrar').val() == "S") && (jQuery.trim($('#descricao_abrir_ao_encerrar').val()) == ""))
		{
			alert("Informe a Descrição Nova atividade");
			$("#descricao_abrir_ao_encerrar").focus();
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

	function ir_lista()
    {
        location.href='<?php echo site_url("atividade/minhas"); ?>';
    }
	
	function ir_atendimento()
    {
        location.href='<?php echo site_url('atividade/atividade_atendimento/index/'.$row['numero'].'/'.$row['cd_gerencia_destino']);?>';
    }
	
	function ir_historico()
    {
        location.href='<?php echo site_url('atividade/atividade_historico/index/'.$row['numero'].'/'.$row['cd_gerencia_destino']);?>';
    }
	
	function ir_acompanhamento()
    {
        location.href='<?php echo site_url('atividade/atividade_acompanhamento/index/'.$row['numero'].'/'.$row['cd_gerencia_destino']);?>';
    }
	
	function ir_anexo()
    {
        location.href='<?php echo site_url('atividade/atividade_anexo/index/'.$row['numero'].'/'.$row['cd_gerencia_destino']);?>';
    }

    function imprimir()
    {
    	window.open('<?php echo site_url('atividade/atividade_solicitacao/imprimir/'.$row['numero'].'/'.$row['cd_gerencia_destino']);?>');
    }

	function proximo_dia_util($tipo_solicitacao)
	{
		if($tipo_solicitacao != '') 
		{
			$.post('<?php echo site_url("atividade/atividade_solicitacao/proximo_dia_util"); ?>',
			{
				tipo_solicitacao : $tipo_solicitacao
			},
			function(data)
			{
				if(data)
				{
					if(($("#dt_limite").val() != ""))
					{
						if(confirm("Deseja limpar a data limite?\n\nClique no botão:\n\n[Ok] para limpar\n\n[Cancelar] para manter a data limite (" + $("#dt_limite").val() + ")"))
						{
							$("#dt_limite").val(data.dt_util);
						}
					}
					else
					{
						$("#dt_limite").val(data.dt_util);
					}
				}
			}, 'json');
			
			$.post('<?php echo site_url("atividade/atividade_solicitacao/descricao_atividade"); ?>',
			{
				tipo_solicitacao : $tipo_solicitacao,
				cd_gerencia      : $('#cd_gerencia_destino').val()
			},
			function(data)
			{
				if(data)
				{
					if(data.obs != "")
					{
						$("#descricao").val(data.obs);
					}
				}
			}, 'json');
		}
	}
	
	function part_callb(data)
	{
		$('#nome_participante').val(data.nome);
		$('#cd_plano').val(data.cd_plano);
	}
	
	function setAbrirEncerrar()
	{
		$('#cd_usuario_abrir_ao_encerrar_row').hide();
		$('#cd_gerencia_abrir_ao_encerrar_row').hide();
		$('#cd_tipo_solicitacao_abrir_ao_encerrar_row').hide();
		$('#cd_tipo_abrir_ao_encerrar_row').hide();
		$('#descricao_abrir_ao_encerrar_row').hide();
		
		if($("#numero").val() == 0)
		{
			$('#cd_usuario_abrir_ao_encerrar').val("");
			$('#cd_gerencia_abrir_ao_encerrar').val("");		
			$('#cd_tipo_solicitacao_abrir_ao_encerrar').val("");		
			$('#cd_tipo_abrir_ao_encerrar').val("");		
			$('#descricao_abrir_ao_encerrar').val("");		
		}
		
		if($('#fl_abrir_encerrar').val() == "S")
		{
			$('#cd_usuario_abrir_ao_encerrar_row').show();
			$('#cd_gerencia_abrir_ao_encerrar_row').show();
			$('#cd_tipo_solicitacao_abrir_ao_encerrar_row').show();
			$('#cd_tipo_abrir_ao_encerrar_row').show();
			$('#descricao_abrir_ao_encerrar_row').show();
		}
	}	
	
	function setAbrirEncerrarGerencia()
	{
		$.post("<?= site_url('atividade/atividade_solicitacao/getAtendente') ?>",
		{
			numero      : $('#numero').val(),
			cd_gerencia : $('#cd_gerencia_abrir_ao_encerrar').val()
		},
		function(data)
		{
			var select = $('#cd_usuario_abrir_ao_encerrar'); 
			
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

			$('#cd_usuario_abrir_ao_encerrar').val($('#cd_usuario_abrir_ao_encerrar_old').val());
		}, 'json', 
		true);	

		$.post("<?= site_url('atividade/atividade_solicitacao/getTipoManutencao') ?>",
		{
			cd_gerencia : $('#cd_gerencia_abrir_ao_encerrar').val()
		},
		function(data)
		{
			var select = $('#cd_tipo_solicitacao_abrir_ao_encerrar'); 
			
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

			$('#cd_tipo_solicitacao_abrir_ao_encerrar').val($('#cd_tipo_solicitacao_abrir_ao_encerrar_old').val());
		}, 'json', 
		true);	

		$.post("<?= site_url('atividade/atividade_solicitacao/getTipoAtividade') ?>",
		{
			cd_gerencia : $('#cd_gerencia_abrir_ao_encerrar').val()
		},
		function(data)
		{
			var select = $('#cd_tipo_abrir_ao_encerrar'); 
			
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

			$('#cd_tipo_abrir_ao_encerrar').val($('#cd_tipo_abrir_ao_encerrar_old').val());
		}, 'json', 
		true);			
		
	}	
	
	$(function(){
		consultar_participante_focus__cd_empresa();

		setAbrirEncerrar();	
		
		if($("#numero").val() != 0)
		{
			setAbrirEncerrarGerencia();
		}
		
		$('#fl_abrir_encerrar').change(function(){ setAbrirEncerrar(); });
		$('#cd_gerencia_abrir_ao_encerrar').change(function(){ setAbrirEncerrarGerencia(); });
	});		
</script>

<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_lista', 'Solicitação', TRUE, 'location.reload();');

if(intval($row['numero']) > 0)
{
	$abas[] = array('aba_lista', 'Atendimento', FALSE, 'ir_atendimento();');
	$abas[] = array('aba_lista', 'Anexo', FALSE, 'ir_anexo();');
	$abas[] = array('aba_lista', 'Acompanhamento', FALSE, 'ir_acompanhamento();');
	$abas[] = array('aba_lista', 'Histórico', FALSE, 'ir_historico();');
}

$arr_recorrente[] = array('text' => 'Não', 'value' => 'N');
$arr_recorrente[] = array('text' => 'Sim', 'value' => 'S');

$ar_abrir_ao_encerrar[] = array('text' => 'Não', 'value' => 'N');
$ar_abrir_ao_encerrar[] = array('text' => 'Sim', 'value' => 'S');

$config['callback']     = 'part_callb';
$config['emp']['value'] = $row['cd_empresa'];
$config['re']['value']  = $row['cd_registro_empregado'];
$config['seq']['value'] = $row['seq_dependencia'];
$config['row_id']       = "participante_row";
$config['caption']      = 'Participante :';

$arr_forma_envio[] = array('value' => '1', 'text' => 'Correio');
$arr_forma_envio[] = array('value' => '2', 'text' => 'Central de atendimento');
$arr_forma_envio[] = array('value' => '3', 'text' => 'Email');

echo aba_start( $abas );
    echo form_open('atividade/atividade_solicitacao/salvar', 'name="filter_bar_form"');
        echo form_start_box( "default_box", "Cadastro" );
			echo form_default_hidden('numero', '', $row['numero']);
            echo form_default_hidden('cd_gerencia_destino', '', $row['cd_gerencia_destino']);
            if(intval($row['numero']) > 0)
            {
                echo form_default_row('numero', 'Número :', '<span class="label label-inverse">'.trim($row['numero']).'</span>');
				
				if(intval($row['cd_atividade_origem']) > 0)
				{
					echo form_default_row('', 'Atividade Origem :', anchor(site_url('atividade/atividade_solicitacao/index/'.$row['cd_atividade_origem']), $row['cd_atividade_origem'], array('target' => "_blank")));
				}	

				if(count($ar_atividade_filha) > 0)
				{
					$atv_filha = "";
					foreach($ar_atividade_filha as $ar_atv)
					{
						$atv_filha.= anchor(site_url('atividade/atividade_solicitacao/index/'.$ar_atv['numero']), $ar_atv['numero'], array('target' => "_blank")).br();
					}
					echo form_default_row('', 'Atividade Filha :', $atv_filha);
				}				
				
                echo form_default_row('dt_cad', 'Dt Solicitação :', $row['dt_cad']);
                echo form_default_row('status', 'Status :', '<span class="'.trim($row['class_status']).'">'.trim($row['status_atividade']).'</span>');
				
				if(intval($row['qt_anexo']) > 0)
				{
					echo form_default_row('', '', '<i>Esta atividade possui anexo(s).</i>');
				}
            }
            echo form_default_row('gerencia_destino', 'Gerência de Destino :', $row['gerencia_destino']);
            echo form_default_dropdown('cod_solicitante', 'Solicitante :*', $arr_solicitante, $row['cod_solicitante']);
            echo form_default_dropdown('tipo_solicitacao', 'Tipo da Manutenção :*', $arr_tipo_manutencao, $row['tipo_solicitacao']);
            echo form_default_dropdown('tipo', 'Tipo da Atividade :*', $arr_tipo_atividade, $row['tipo'], 'onchange="proximo_dia_util($(this).val())"');
            echo form_default_text('titulo', 'Título :*', $row['titulo'], 'style="width:450px;"');
            echo form_default_textarea('descricao', 'Descrição da Solicitação :*', $row['descricao'], 'style="width:450px; height:150px;"');
            echo form_default_dropdown('cod_atendente', 'Atendente da Atividade :*', $arr_atendente, $row['cod_atendente']);
            echo form_default_row('', '', '<i>Indique para quem você vai encaminhar esta solicitação.</i>');
            echo form_default_date('dt_limite', 'Dt. Limite :', $row['dt_limite']);
            echo form_default_row('', '', '<i>Data máxima para o atendimento desta solicitação.</i>');
			
			#### INFORMACOES PARA ABRIR UMA NOVA ATIVIDADE APOS ENCERRAR A ATUAL ####
			echo form_default_hidden('cd_tipo_abrir_ao_encerrar_old', '', $row['cd_tipo_abrir_ao_encerrar']);
			echo form_default_hidden('cd_tipo_solicitacao_abrir_ao_encerrar_old', '', $row['cd_tipo_solicitacao_abrir_ao_encerrar']);
			echo form_default_hidden('cd_usuario_abrir_ao_encerrar_old', '', $row['cd_usuario_abrir_ao_encerrar']);
			echo form_default_dropdown('fl_abrir_encerrar', 'Abrir Nova Atividade: *', $ar_abrir_ao_encerrar, $row['fl_abrir_encerrar']);
			echo form_default_row('', '', '<i>Indique se você deseja abrir uma Nova atividade ao encerrar esta atividade.</i>');
			echo form_default_dropdown('cd_gerencia_abrir_ao_encerrar', 'Nova Atividade: *', $ar_gerencia_abrir_ao_encerrar, $row['cd_gerencia_abrir_ao_encerrar']);
			echo form_default_dropdown('cd_tipo_solicitacao_abrir_ao_encerrar', 'Tipo da Solicitação da Nova Atividade: *', array(), $row['cd_tipo_solicitacao_abrir_ao_encerrar']);
			echo form_default_dropdown('cd_tipo_abrir_ao_encerrar', 'Tipo da Nova Atividade: *', array(), $row['cd_tipo_abrir_ao_encerrar']);
			echo form_default_dropdown('cd_usuario_abrir_ao_encerrar', 'Atendente da Nova Atividade: *', array(), $row['cd_usuario_abrir_ao_encerrar']);
			echo form_default_textarea('descricao_abrir_ao_encerrar', 'Descrição Nova Atividade:*', $row['descricao_abrir_ao_encerrar'], 'style="width:450px; height:80px;"');			
			echo form_default_row('', '', '');
			####			
			
        echo form_end_box("default_box");
		echo form_start_box("default_participante_box", "Atendimento ao Participante");
			echo form_default_participante_trigger($config);
			echo form_default_row('', '', '<i>	Patrocinadora/Instituidor do participante em atendimento.</i>');
			echo form_default_text('nome_participante', "Nome :", $row, "style='width:350px; border: 0px;' readonly" );
			if((intval($row['numero']) > 0) AND (trim($row['cd_empresa']) != '') AND (trim($row['cd_registro_empregado']) != '') AND (trim($row['seq_dependencia']) != ''))
			{
				echo form_default_row('endereco', 'Endereço :', $row['endereco'].", ".$row['nr_endereco']."/".$row['complemento_endereco']." - ".$row['bairro']." - ".$row['cep']." - ".$row['cidade']." - ".$row['uf']);
				echo form_default_row('telefone_1', 'Telefone 1 :', $row['ddd']." - ".$row['telefone']);
				echo form_default_row('telefone_2', 'Telefone 2 :', $row['ddd_celular']." - ".$row['celular']);
				echo form_default_row('email', 'Email :', $row['email']." / ".$row['email_profissional']);
			}
			echo form_default_dropdown('cd_plano', 'Plano :', $arr_plano, $row['cd_plano']);
			echo form_default_dropdown('solicitante', 'Solicitante :', $arr_solicitante_participante, $row['solicitante']);
			echo form_default_dropdown('forma', 'Forma de Solicitação :', $arr_forma_solicitacao, $row['forma']);
			echo form_default_dropdown('tp_envio', 'Forma de Envio :', $arr_forma_envio, $row['tp_envio']);
			echo form_default_text('cd_atendimento', 'Protocolo de Atendimento', $row, 'style="width:80px"');
		echo form_end_box("default_participante_box");
        echo form_command_bar_detail_start();    
            if ($fl_salvar)
			{		
				echo button_save("Salvar");
				echo button_save("Imprimir", 'imprimir()', 'botao_disabled');
			}
        echo form_command_bar_detail_end();
    echo form_close();
    echo br(5);	
echo aba_end();

$this->load->view('footer_interna');
?>