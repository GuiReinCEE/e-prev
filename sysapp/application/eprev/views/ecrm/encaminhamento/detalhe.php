<?php
set_title('Encaminhamentos -> Cadastro');
$this->load->view('header');
?>
<script type="text/javascript">
	<?= form_default_js_submit(array('cd_atendimento_encaminhamento_tipo')) ?>

	function buscarEmprestimo(id_confere_emprestimo)
	{
		if($("#cd_contrato_emprestimo").val() == "")
		{
			alert("Informe Nº Contrato Empréstimo");
			$("#cd_contrato_emprestimo").focus();
		}
		else
		{		
			$("#emprestimo_div_item").html("<?php echo loader_html(); ?>");

			$.post('<?php echo site_url("ecrm/encaminhamento/emprestimo"); ?>',
			{
				cd_contrato_emprestimo : $("#cd_contrato_emprestimo_"+id_confere_emprestimo).val(),
				cd_empresa             : $("#cd_empresa").val(),
				cd_registro_empregado  : $("#cd_registro_empregado").val(),
				seq_dependencia        : $("#seq_dependencia").val()
			},
			function(data)
			{
				$("#emprestimo_div_"+id_confere_emprestimo+"_item").html(data);
			});
		}
	}

    function emprestimoSalvar(form)
    {
		if($("#cd_contrato_emprestimo").val() == "")
		{
			alert("Informe Nº Contrato Empréstimo");
			$("#cd_contrato_emprestimo").focus();
		}
		else
		{
			var confirmacao = 'Confirma a CONFERÊNCIA da concessão do EMPRÉSTIMO?\n\n'+
							  'Clique [Ok] para Sim\n\n'+
							  'Clique [Cancelar] para Não\n\n';	

			if(confirm(confirmacao))
			{
				form.submit();
				/*
				location.href='<?php echo site_url("ecrm/encaminhamento/emprestimoSalvar"); ?>'+'/'+ $("#cd_atendimento").val()+'/'+$("#cd_encaminhamento").val()+'/'+$("#cd_contrato_emprestimo_"+$("#id_confere_emprestimo").val()).val()+'/'+$("#id_confere_emprestimo").val();
				*/

			}
        }
    }

    function ir_lista()
	{
		location.href='<?php echo site_url("ecrm/encaminhamento"); ?>';
	}

    function executarComando(v)
    {
        alert(v);
        bConfirm = true;
        if(v=="cancelar")
        {
            bConfirm = confirm("Atenção:\n\nClique em OK para confirmar o cancelamento?");
        }
        if( bConfirm )
        {
            f = document.frmOnly;
            f.tipo_operacao.value = v;
            f.submit();
        }
    }

    function encerrar()
    {
        location.href='<?php echo site_url("ecrm/encaminhamento/encerra_atendimento"); ?>'+'/'+ $("#cd_atendimento").val()+'/'+$("#cd_encaminhamento").val();
    }

    function cancelar()
    {

        location.href='<?php echo site_url("ecrm/encaminhamento/cancelamento"); ?>'+'/'+ $("#cd_atendimento").val()+'/'+$("#cd_encaminhamento").val();
    }

	$(function(){
		if($("#cd_contrato_emprestimo_1").val() != "")
		{
			buscarEmprestimo(1);
		}
		
		if($("#cd_contrato_emprestimo_2").val() != "")
		{
			buscarEmprestimo(2);
		}
		
	});		
</script>

<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_jogo', 'Cadastro', TRUE, 'location.reload();');
echo aba_start( $abas );

echo form_start_box( "default_box", "ATENDIMENTO", true, false);
    echo form_default_hidden('cd_empresa', "cd_empresa: ", $atendimento['cd_empresa']);
    echo form_default_hidden('cd_registro_empregado', "cd_registro_empregado: ", $atendimento['cd_registro_empregado']);
    echo form_default_hidden('seq_dependencia', "seq_dependencia: ", $atendimento['seq_dependencia']);
    echo form_default_text('', "Nrº do Atendimento: ", $atendimento['cd_atendimento'], "style='width:100%;border: 0px;' readonly" );
    echo form_default_text('ds_tipo', "Tipo: ", $atendimento['tp_atendimento'], "style='width:100%;border: 0px;' readonly" );
    echo form_default_text('dt_atendimento', "Dt atendimento: ", $atendimento['dt_atendimento'], "style='width:100%;border: 0px;' readonly" );
    echo form_default_text('ds_emp', "Emp/re/seq: ", $atendimento['cd_empresa']. " / " . $atendimento['cd_registro_empregado'] . " / ". $atendimento['seq_dependencia'], "style='width:100%;border: 0px;' readonly" );
    echo form_default_text('ds_nome', "Nome participante: ", $atendimento['nome_participante'], "style='width:500px;border: 0px;' readonly" );
    echo form_default_text('ds_atendente', "Atendente: ", $atendimento['atendente'], "style='width:100%;border: 0px;' readonly" );
    echo form_default_text('ds_obs', "Observações: ", $atendimento['tp_atendimento'], "style='width:100%;border: 0px;' readonly" );
    echo form_default_row('', 'Observação: ', $atendimento['obs']);
echo form_end_box("default_box");

	echo form_open('ecrm/encaminhamento/salvar_tipo');
		echo form_start_box( "default_box", "ENCAMINHAMENTO", true, false);
			echo form_default_hidden('cd_atendimento', '', $atendimento['cd_atendimento'] );
			echo form_default_hidden('cd_encaminhamento', '', $encaminhamento['cd_encaminhamento']);
		    echo form_default_text('', "Encaminhamento nº: ", $encaminhamento['cd_encaminhamento'], "style='width:100%;border: 0px;' readonly"  );
		    if((trim($encaminhamento['dt_encaminhamento']) == '')  and (trim($encaminhamento['dt_cancelado']) == ''))
			{
		    	echo form_default_dropdown('cd_atendimento_encaminhamento_tipo', 'Tipo Encaminhamento: (*)', $tipo, $encaminhamento['cd_atendimento_encaminhamento_tipo']);
			}
			else
			{
				echo form_default_text('ds_atendimento_encaminhamento_tipo', "Tipo Encaminhamento: ", $encaminhamento['ds_atendimento_encaminhamento_tipo'], "style='width:100%;border: 0px;' readonly"  );
			}
		    echo form_default_text('ds_situacao', "Situação: ", $encaminhamento['fl_atendimento'], "style='width:100%;border: 0px;' readonly" );
		    echo form_default_text('ds_solicitado', "Solicitado por: ", $encaminhamento['solicitante'], "style='width:100%;border: 0px;' readonly" );
		    echo form_default_text('dt_solicitacao', "Dt solicitação: ", $encaminhamento['dt_solicitacao'], "style='width:100%;border: 0px;' readonly" );
		    echo form_default_text('ds_encaminhado', "Processado por: ", $encaminhamento['atendente'], "style='width:100%;border: 0px;' readonly" );
		    echo form_default_text('dt_encaminhamento', "Dt encaminhamento: ", $encaminhamento['dt_encaminhamento'], "style='width:100%;border: 0px;' readonly" );
		    echo form_default_row('', 'Observação: ', "<span style='font-size: 160%; color: green; font-weight: bold;'>".$encaminhamento['texto_encaminhamento']."</span>");
		echo form_end_box("default_box");

		if((trim($encaminhamento['dt_encaminhamento']) == '')  and (trim($encaminhamento['dt_cancelado']) == ''))
		{
		    echo form_command_bar_detail_start();
		    	echo button_save('Alterar Tipo de Encaminhamento');
		        echo button_save("Encerrar este atendimento", "encerrar()", "botao_verde");
		        echo button_save("Cancelar este atendimento", "cancelar();","botao_vermelho");
		    echo form_command_bar_detail_end();
		}
	echo form_close();

	echo form_open('ecrm/encaminhamento/emprestimoSalvar');
		echo form_default_hidden('cd_atendimento', '', $atendimento['cd_atendimento'] );
		echo form_default_hidden('cd_encaminhamento', '', $encaminhamento['cd_encaminhamento']);
		if((trim($encaminhamento['dt_encaminhamento']) != "") and (intval($encaminhamento['cd_atendimento_encaminhamento_tipo']) == 3))
		{
			echo form_start_box("default_box", "EMPRÉSTIMO", true, false);
				
				echo form_default_hidden('id_confere_emprestimo', "id_confere_emprestimo: ", $encaminhamento['id_confere_emprestimo']);
				
				echo form_default_textarea('ds_observacao', "Observações:", $encaminhamento['ds_observacao'], 'style="height:80px;"');
				
				echo form_default_integer('usuario_contrato_emprestimo_1', "Usuário Conferente:", $encaminhamento['usuario_contrato_emprestimo_1'], "style='width:500px;border: 0px;' readonly" );
				echo form_default_integer('dt_contrato_emprestimo_1', "Dt Conferência:", $encaminhamento['dt_contrato_emprestimo_1'], "style='width:500px;border: 0px;' readonly" );
				if (intval($encaminhamento['cd_contrato_emprestimo_1']) > 0)
				{
					echo form_default_integer('cd_contrato_emprestimo_1', "Nº Contrato de Empréstimo:", $encaminhamento['cd_contrato_emprestimo_1'], "style='width:100%;border: 0px;' readonly" );
				}
				else
				{
					echo form_default_integer('cd_contrato_emprestimo_1', "Nº Contrato Empréstimo:", $encaminhamento['cd_contrato_emprestimo_1']);
					echo form_default_row('', '', '<input type="button" class="botao" value="Buscar" onclick="buscarEmprestimo(1);">');
				}
				echo form_default_row('emprestimo_div_1', 'Dados do Empréstimo:', '');

				if(trim($encaminhamento['usuario_contrato_emprestimo_2']) != '')
				{
					echo form_default_row('', '', '');
					echo form_default_row('', '', '');
					
					echo form_default_integer('usuario_contrato_emprestimo_2', "Usuário Conferência 2:", $encaminhamento['usuario_contrato_emprestimo_2'], "style='width:500px;border: 0px;' readonly" );
					echo form_default_integer('dt_contrato_emprestimo_2', "Dt Conferência 2:", $encaminhamento['dt_contrato_emprestimo_2'], "style='width:500px;border: 0px;' readonly" );		
					if ((intval($encaminhamento['cd_contrato_emprestimo_2']) > 0) or (intval($encaminhamento['cd_contrato_emprestimo_1']) == 0))
					{
						echo form_default_integer('cd_contrato_emprestimo_2', "Nº Contrato de Empréstimo:", $encaminhamento['cd_contrato_emprestimo_2'], "style='width:100%;border: 0px;' readonly" );
					}
					else
					{
						echo form_default_integer('cd_contrato_emprestimo_2', "Nº Contrato Empréstimo:", $encaminhamento['cd_contrato_emprestimo_2']);
						echo form_default_row('', '', '<input type="button" class="botao" value="Buscar" onclick="buscarEmprestimo(2);">');
					}		
					echo form_default_row('emprestimo_div_2', 'Dados do Empréstimo:', '');
				}		
			echo form_end_box("default_box");
			if((trim($encaminhamento['dt_encaminhamento']) != "") and (in_array(intval($encaminhamento['id_confere_emprestimo']), array(1))))
			{
				echo form_command_bar_detail_start();
					echo button_save("Confirma Conferência", "emprestimoSalvar(this.form);", "botao_amarelo");
				echo form_command_bar_detail_end();
			}	

		}
	echo form_close();
	if(trim($encaminhamento['dt_cancelado']) != '')
	{
		echo form_start_box( "default_box_cancelamento", "CANCELAMENTO", true, false);
			echo form_default_text('dt_cancelamento', "Dt. Cancelamento: ", $encaminhamento['dt_cancelado'], "style='width:100%;border: 0px;' readonly" );
			echo form_default_textarea('ds_observacao_cancelamento', 'Observação:',  $encaminhamento['ds_observacao_cancelamento'], 'style="width: 500px; height: 80px;"');
		echo form_end_box("default_box_cancelamento");
	}

echo form_start_box( "default_box", "Informações do atendimento", true, false );
    $body=array();
    $head = array(
        'Nome Tela',
        'Hora acesso',
        'Programa'
    );

    foreach($info_atendimento as $item )
    {
        $body[] = array(
            $item["tela"],
            $item["hr_hora"],
            $item["tp_tela"]
        );
    }

    $this->load->helper('grid');
    $grid = new grid();
    $grid->head = $head;
    $grid->body = $body;
    echo $grid->render();
echo form_end_box("default_box");
echo aba_end();

?>
