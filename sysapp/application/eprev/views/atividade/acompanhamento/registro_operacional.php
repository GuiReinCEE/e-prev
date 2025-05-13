<?php
set_title('Acompanhamento de Projetos - Registro Operacional');
$this->load->view('header');
?>
<script>
	<?php
		echo form_default_js_submit(Array('cd_acomp', 'ds_nome'));
	?>
	
	function ir_lista()
	{
		location.href='<?php echo site_url("atividade/acompanhamento"); ?>';
	}
	
	function ir_cadastro()
	{
		location.href='<?php echo site_url("atividade/acompanhamento/cadastro/".intval($row['cd_acomp'])); ?>';
	}	
	
	function ir_etapa()
	{
		location.href='<?php echo site_url("atividade/acompanhamento/etapa/".intval($row['cd_acomp'])); ?>';
	}

	function ir_previsao()
	{
		location.href='<?php echo site_url("atividade/acompanhamento/previsao/".intval($row['cd_acomp'])); ?>';
	}	

	function ir_lista_reunicao()
	{
		location.href='<?php echo site_url("atividade/acompanhamento/reuniao/".intval($row['cd_acomp'])); ?>';
	}
	
	function finalizar()
	{
		if(confirm('Deseja realmente finalizar o registro operacional?\n\n'))
		{
			location.href='<?php echo site_url("atividade/acompanhamento/finalizar_registro_operacional/".intval($row['cd_acomp'])."/".intval($row_registro['cd_acompanhamento_registro_operacional'])); ?>';
		}
	}
	
	function reiniciar()
	{
		if(confirm('Deseja realmente reiniciar o registro operacional?\n\n'))
		{
			location.href='<?php echo site_url("atividade/acompanhamento/reiniciar_registro_operacional/".intval($row['cd_acomp'])."/".intval($row_registro['cd_acompanhamento_registro_operacional'])); ?>';
		}
	}
	
	function imprimir()
    {
        window.open('<?php echo site_url("atividade/registro_operacional/imprimir/".intval($row_registro['cd_acompanhamento_registro_operacional'])); ?>');
    }
	
	function load()
	{
		$('#result_div').html("<?php echo loader_html(); ?>");

        $.post( '<?php echo site_url('atividade/acompanhamento/listar_registro_operacional_anexo'); ?>',
		{
			cd_acompanhamento_registro_operacional : $('#cd_acompanhamento_registro_operacional').val()
		},
        function(data)
        {
			$('#result_div').html(data);
            configure_result_table();
        });
	}
	
	function configure_result_table()
    {
        var ob_resul = new SortableTable(document.getElementById("table-1"),
        [
			'DateTimeBR', 
			'CaseInsensitiveString', 
			'CaseInsensitiveString', 
			null
        ]);
        ob_resul.onsort = function ()
        {
            var rows = ob_resul.tBody.rows;
            var l = rows.length;
            for (var i = 0; i < l; i++)
            {
                removeClassName( rows[i], i % 2 ? "sort-par" : "sort-impar" );
                addClassName( rows[i], i % 2 ? "sort-impar" : "sort-par" );
            }
        };
        ob_resul.sort(0, true);
    }
	
	function excluir_registro_operacional_anexo(cd_acompanhamento_registro_operacional_anexo)
	{
		if(confirm("ATENÇÃO\n\nDeseja excluir o anexo?\n\n"))
		{
			$.post( '<?php echo site_url('atividade/acompanhamento/excluir_registro_operacional_anexo'); ?>',
			{
				cd_acompanhamento_registro_operacional_anexo : cd_acompanhamento_registro_operacional_anexo
			},
			function(data)
			{
				load();
			});
		}
	}
	
	$(function(){
		if($('#cd_acompanhamento_registro_operacional').val() > 0)
		{
			load();
		}
	});
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_cadastro', 'Acompanhamento', FALSE, 'ir_cadastro();');
$abas[] = array('aba_reuniao', 'Reuniões', FALSE, 'ir_lista_reunicao();');
$abas[] = array('aba_etapas', 'Etapas', FALSE, 'ir_etapa();');
$abas[] = array('aba_reuniao', 'Registro Operacional', TRUE, 'location.reload();');
$abas[] = array('aba_previsao', 'Previsão', FALSE, 'ir_previsao();');	
	
$status = "Projeto em andamento";
$cor_status = "blue";

if (trim($row['dt_encerramento']) != '') 
{
	$status = 'Projeto encerrado em: '. $row['dt_encerramento'];
	$cor_status = "red";
}	

if (trim($row['dt_cancelamento']) != '') 
{
	$status = 'Projeto cancelado em: '. $row['dt_cancelamento'];
	$cor_status = "red";
}
	
echo aba_start( $abas );
	echo form_open('atividade/acompanhamento/salvar_registro_operacional');
		echo form_start_box( "default_box", "Acompanhamento" );
			echo form_default_hidden('cd_acomp', '', $row);
			echo form_default_hidden('cd_acompanhamento_registro_operacional', '', $row_registro);
			echo form_default_text('cd_acomp_h', "Código :", intval($row['cd_acomp']), "style='width:100%;border: 0px;' readonly" );
			echo form_default_text('ds_projeto', "Projeto :", $row, "style='width:100%;border: 0px;' readonly" );
			echo form_default_text('status', "Status :", $status, "style='color: ".$cor_status."; font-weight:bold; width:400px;border: 0px;' readonly" );
		echo form_end_box("default_box");
		echo form_start_box( "default_registro_box", "Registro Operacional" );
			if(! $fl_analista)
			{
				echo form_default_text('ds_nome', 'Nome Processo :*', $row_registro['ds_nome'], 'style="width:400px;"');
				echo form_default_textarea('ds_processo_faz', '1) O que o processo faz : ', $row_registro, 'style="height:100px;"');
				echo form_default_textarea('ds_processo_executado', '2) De que maneira é executado o processo : ', $row_registro, 'style="height:100px;"');
				echo form_default_textarea('ds_calculo', '3) Cálculos : ', $row_registro, 'style="height:100px;"');
				echo form_default_textarea('ds_responsaveis', '4) Responsáveis : ', $row_registro, 'style="height:100px;"');
				echo form_default_textarea('ds_requesito', '5) O que é necessário para que este processo possa ocontecer : ', $row_registro, 'style="height:100px;"');
				echo form_default_textarea('ds_necessario', '6) Este processo é necessário para qual(is) outro(s) processo(s) : ', $row_registro, 'style="height:100px;"');
				echo form_default_textarea('ds_integridade', '7) Integração com outros sistemas : ', $row_registro, 'style="height:100px;"');
				echo form_default_textarea('ds_resultado', '8) Resultados : ', $row_registro, 'style="height:100px;"');
				echo form_default_textarea('ds_local', '9) Telas / Relatórios / Planilhas : ', $row_registro, 'style="height:100px;"');
			}
			else
			{
				echo form_default_hidden('ds_processo_faz', '', $row_registro);
				echo form_default_hidden('ds_processo_executado', '', $row_registro);
				echo form_default_hidden('ds_calculo', '', $row_registro);
				echo form_default_hidden('ds_responsaveis', '', $row_registro);
				echo form_default_hidden('ds_requesito', '', $row_registro);
				echo form_default_hidden('ds_necessario', '', $row_registro);
				echo form_default_hidden('ds_integridade', '', $row_registro);
				echo form_default_hidden('ds_resultado', '', $row_registro);
				echo form_default_hidden('ds_local', '', $row_registro);
				
				echo form_default_text('ds_nome', 'Nome Processo :', $row_registro['ds_nome'], "style='width:100%;border: 0px;' readonly");
				echo form_default_row('ds_processo_faz_r', '1) O que o processo faz : ', $row_registro['ds_processo_faz']);
				echo form_default_textarea('ds_processo_faz_complemento', 'Complemento do Analista : ', $row_registro, 'style="height:100px;"');
				echo form_default_row('ds_processo_executado_r', '2) De que maneira é executado o processo : ', $row_registro['ds_processo_executado']);
				echo form_default_textarea('ds_processo_executado_complemento', 'Complemento do Analista : ', $row_registro, 'style="height:100px;"');
				echo form_default_row('ds_calculo_r', '3) Cálculos : ', $row_registro['ds_calculo']);
				echo form_default_textarea('ds_calculo_complemento', 'Complemento do Analista : ', $row_registro, 'style="height:100px;"');
				echo form_default_row('ds_responsaveis_r', '4) Responsáveis : ', $row_registro['ds_responsaveis']);
				echo form_default_row('ds_requesito_r', '5) O que é necessário para que este processo possa ocontecer : ', $row_registro['ds_requesito']);
				echo form_default_textarea('ds_requesito_complemento', 'Complemento do Analista : ', $row_registro, 'style="height:100px;"');
				echo form_default_row('ds_necessario_r', '6) Este processo é necessário para qual(is) outro(s) processo(s) : ', $row_registro['ds_necessario']);
				echo form_default_textarea('ds_necessario_complemento', 'Complemento do Analista : ', $row_registro, 'style="height:100px;"');
				echo form_default_row('ds_integridade_r', '7) Integração com outros sistemas : ', $row_registro['ds_integridade']);
				echo form_default_textarea('ds_integridade_complemento', 'Complemento do Analista : ', $row_registro, 'style="height:100px;"');
				echo form_default_row('ds_resultado_r', '8) Resultados : ', $row_registro['ds_resultado']);
				echo form_default_textarea('ds_resultado_complemento', 'Complemento do Analista : ', $row_registro, 'style="height:100px;"');
				echo form_default_row('ds_local_r', '9) Telas / Relatórios / Planilhas : ', $row_registro['ds_local']);
			}
		echo form_end_box("default_registro_box");
		echo form_command_bar_detail_start();
			
			if ((trim($row['dt_encerramento']) == '') and (trim($row['dt_cancelamento']) == ''))
			{
				echo ($fl_salvar ? button_save("Salvar") : '');
				echo ($fl_finalizar ? button_save("Finalizar", 'finalizar()', 'botao_verde') : '');
				echo ($fl_reiniciar ? button_save("Reinicar", 'reiniciar()', 'botao_vermelho') : '');
			}

			if(intval($row_registro['cd_acompanhamento_registro_operacional']) > 0)
			{
				echo button_save("Imprimir", 'imprimir();', 'botao_disabled');
			}

		echo form_command_bar_detail_end();	
	echo form_close();		
	if(intval($row_registro['cd_acompanhamento_registro_operacional']) > 0)
	{
		if ((trim($row['dt_encerramento']) == '') and (trim($row['dt_cancelamento']) == ''))
		{
			echo form_open('atividade/acompanhamento/salvar_registro_operacional_anexo');
				echo form_start_box( "default_anexos_box", "Anexos" );
					echo form_default_hidden('cd_acomp', '', $row);
					echo form_default_hidden('cd_acompanhamento_registro_operacional', '', $row_registro);
					echo form_default_upload_iframe('arquivo', 'registro_operacional', 'Arquivo :*', '', 'registro_operacional', false, '$("form").submit();');
				echo form_end_box("default_anexos_box");
			echo form_close();	
		}
		echo br();
		
		echo'<div id="result_div"></div>';
	}

	echo br();
echo aba_end();

$this->load->view('footer_interna');
?>