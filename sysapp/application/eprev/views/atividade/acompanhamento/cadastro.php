<?php
set_title('Acompanhamento de Projetos - Cadastro');
$this->load->view('header');
?>
<script>
	function envia_email(cd_acomp)
	{
		if(confirm('Confirma o reenvio do EMAIL para TODOS?'))
		{			
			$.post('<?php echo site_url('/atividade/acompanhamento/envia_email'); ?>',
			{
				cd_acomp : $('#cd_acomp').val()
			},
			function(data)
			{
				if(data)
				{
					alert('Total de email enviados: '+data.tl);
				}
			}, 'json');
			
		}
	}
	
	function encerra(cd_acomp)
	{
		if(confirm('Confirma a operação ENCERRAR o acompanhamento?'))
		{
			location.href='<?php echo site_url("atividade/acompanhamento/encerra"); ?>/' + cd_acomp;
		}
	}	
	
	function cancela(cd_acomp)
	{
		if(confirm('Confirma a operação CANCELAR o acompanhamento?'))
		{
			location.href='<?php echo site_url("atividade/acompanhamento/cancela"); ?>/' + cd_acomp;
		}
	}

	function salvar( form )
	{
		if( $("#cd_projeto").val()=="" )
		{
			alert( "Informe os campos obrigatórios! \n\n(os campos obrigatórios tem um * logo após a identificação.)" );
			$("#cd_projeto").focus();
			return false;
		}
		
		var fl_marcado = false;
		$("input[type='checkbox'][id='arr_analista']").each( 
			function() 
			{ 
				if (this.checked) 
				{ 
					fl_marcado = true;
				} 
			}
		);				
				
		if(!fl_marcado)
		{
			alert("Informe os campos obrigatórios! \n\n(os campos obrigatórios tem um * logo após a identificação.)");
			return false;
		}
				
		if( confirm('Salvar?') )
		{
			form.submit();
		}
	}
	
	function ir_lista()
	{
		location.href='<?php echo site_url("atividade/acompanhamento"); ?>';
	}
	
	function ir_reuniao()
	{
		location.href='<?php echo site_url("atividade/acompanhamento/reuniao/".intval($row['cd_acomp'])); ?>';
	}

	function ir_etapa()
	{
		location.href='<?php echo site_url("atividade/acompanhamento/etapa/".intval($row['cd_acomp'])); ?>';
	}

	function ir_previsao()
	{
		location.href='<?php echo site_url("atividade/acompanhamento/previsao/".intval($row['cd_acomp'])); ?>';
	}	
</script>

<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_cadastro', 'Acompanhamento', TRUE, 'location.reload();');

if(intval($row['cd_acomp']) > 0)
{
	$abas[] = array('aba_reuniao', 'Reuniões', FALSE, 'ir_reuniao();');
	$abas[] = array('aba_etapas', 'Etapas', FALSE, 'ir_etapa();');
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
}
	
echo aba_start( $abas );
	echo form_open('atividade/acompanhamento/salvar');
		echo form_start_box( "default_box", "Cadastro" );
			echo form_default_hidden('cd_acomp', '', $row);
			if(intval($row['cd_acomp']) > 0)
			{
				echo form_default_text('cd_acomp_h', "Código :", intval($row['cd_acomp']), "style='width:100%;border: 0px;' readonly" );
				echo form_default_text('dt_acomp', "Dt. Inclusão :", $row, "style='width:100%;border: 0px;' readonly" );
				echo form_default_text('status', "Status :", $status, "style='color: ".$cor_status."; font-weight:bold; width:100%;border: 0px;' readonly" );			
			}		
			echo form_default_dropdown('cd_projeto', 'Projeto :*', $arr_projeto, Array($row['cd_projeto']), "style='width:100%;'");
			echo form_default_checkbox_group('arr_analista', 'Analistas Responsáveis :*', $arr_analista, $arr_analista_checked, 300);
			
			if(intval($row['cd_acomp']) > 0)
			{			
				echo form_default_row('', 'Dt. último email :', '<div class="label-padrao-form">'.$row['dt_email'].'  (Todo dia 10 de cada mês é enviado email para TODOS)</div>');
			}
		echo form_end_box("default_box");
		echo form_command_bar_detail_start();
			if ((trim($row['dt_encerramento']) == '') and (trim($row['dt_cancelamento']) == ''))
			{
				echo button_save("Salvar");
				
				if(intval($row['cd_acomp']) > 0)
				{
					echo button_save("Reenviar Email","envia_email(".intval($row['cd_acomp']).")","botao_disabled");
					echo button_save("Encerrar","encerra(".intval($row['cd_acomp']).")","botao_disabled");
					echo button_save("Cancelar","cancela(".intval($row['cd_acomp']).")","botao_vermelho");
				}
			}
		echo form_command_bar_detail_end();
	echo form_close();		
	echo br();
echo aba_end();
$this->load->view('footer_interna');
?>