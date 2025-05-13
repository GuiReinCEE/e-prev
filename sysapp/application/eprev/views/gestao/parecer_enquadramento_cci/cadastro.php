<?php
set_title('Parecer Enquadramento');
$this->load->view('header');
?>
<script>
	<?php
		echo form_default_js_submit(
									Array('descricao','dt_limite'),
									'_salvar(form)'
									);
	?>
	function _salvar(form)
	{
		if(validaDtLimite("dt_limite"))
		{
			form.submit();
		}
	}	
	
	function validaDtLimite(id)
	{
        var dt_minima = new Date();
            dt_minima.addDays(+10);
            dt_minima.zeroTime();
			
		var dt_limite = Date.fromString($("#" + id).val());
			dt_limite.zeroTime();  
		
		if(dt_limite < dt_minima)
		{
			dt_minima.format == 'dd/mm/yyyy';
			alert("A Dt Limite mínima é hoje + 10 dias (" + dt_minima.asString() + ")");
			return false;
		}
		else
		{
			return true;
		}
	}
	
	function ir_lista()
	{
		location.href='<?php echo site_url("gestao/parecer_enquadramento_cci"); ?>';
	}
	
	function ir_anexo()
	{
		location.href='<?php echo site_url("gestao/parecer_enquadramento_cci/anexo/".$row['cd_parecer_enquadramento_cci']); ?>';
	}
	
	function enviar()
	{
		if(validaDtLimite("dt_limite_bd"))
		{
			var confirmacao = 'Deseja enviar para GC?\n\n'+
				'Clique [Ok] para Sim\n\n'+
				'Clique [Cancelar] para Não\n\n';
			
			if(confirm(confirmacao))
			{
				location.href='<?php echo site_url("gestao/parecer_enquadramento_cci/enviar/".$row['cd_parecer_enquadramento_cci']); ?>';
			}
		}
		else 
		{
			alert("Informe uma nova Dt Limite e clique no botão [Salvar] antes de Enviar para GC");
		}
	}
	
	function encerrar()
	{
		
		var confirmacao = 'Deseja encerrar o parecer?\n\n'+
			'Clique [Ok] para Sim\n\n'+
			'Clique [Cancelar] para Não\n\n';
		
		if(confirm(confirmacao))
		{
			location.href='<?php echo site_url("gestao/parecer_enquadramento_cci/encerrar/".$row['cd_parecer_enquadramento_cci']); ?>';
		}
	}

	function cancelar()
	{
		var confirmacao = 'Deseja Cancelar o parecer?\n\n'+
			'Clique [Ok] para Sim\n\n'+
			'Clique [Cancelar] para Não\n\n';
		
		if(confirm(confirmacao))
		{
			location.href='<?php echo site_url("gestao/parecer_enquadramento_cci/cancelar/".$row['cd_parecer_enquadramento_cci']); ?>';
		}
	}

	function salvar_prorrogacao()
	{
		if($("#dt_limite_prorrogacao").val() == '')
		{
			alert("Informe a data de prorrogação");
		}
		else
		{
			$("#form_prorrogacao").submit();
		}
	}
	
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');

if(intval($row['cd_parecer_enquadramento_cci']) > 0)
{
	$abas[] = array('aba_anexo', 'Anexo', FALSE, 'ir_anexo();');
}

echo aba_start($abas);
	echo form_open('gestao/parecer_enquadramento_cci/salvar');
		echo form_start_box( "default_box", "Cadastro" );
			echo form_default_hidden('cd_parecer_enquadramento_cci', "", $row);	

			if(intval($row['cd_parecer_enquadramento_cci']) > 0)
			{
				echo form_default_row('nr_ano_numero', 'Ano/Número :', $row['nr_ano_numero']);
				echo form_default_row('dt_inclusao', 'Dt Cadastro :', $row['dt_inclusao']);
				echo form_default_row('usuario_cadastro', 'Usuário Cadastro:', $row['usuario_cadastro']);
				
				if(trim($row['dt_envio']) != '')
				{
					echo form_default_row('dt_envio', 'Dt Envio :', $row['dt_envio']);
					echo form_default_row('usuario_envio', 'Usuário Envio :', $row['usuario_envio']);
				}
				
				if(trim($row['dt_encerrado']) != '')
				{
					echo form_default_row('dt_encerrado', 'Dt Encerrado :', $row['dt_encerrado']);
					echo form_default_row('usuario_encerrado', 'Usuário Encerrado :', $row['usuario_encerrado']);
				}
			}			
			echo form_default_textarea('descricao', 'Descrição : *', $row);
			echo form_default_date('dt_limite', 'Dt Limite : *', $row);			
			echo form_default_hidden('dt_limite_bd', 'Dt Limite : *', $row["dt_limite"]);

			if(intval($row['cd_parecer_enquadramento_cci']) > 0)
			{
				if(trim($row['dt_cancelamento_fl']) != '')
				{
					echo form_default_row('dt_cancelamento', 'Dt Cancelado :', $row['dt_cancelamento']);
					echo form_default_row('cd_usuario_cancelamento', 'Usuário de Cancelamento :', $row['ds_usuario_cancelamento']);
					echo form_default_textarea('ds_justificativa_cancelamento', 'Justificativa : ', $row['ds_justificativa_cancelamento'],'style="height:100px;"');
				}
			}
		echo form_end_box("default_box");
		echo form_command_bar_detail_start();
			if(trim($row['dt_envio']) == '')
			{
				echo button_save("Salvar");	
			}
			
			if((intval($row['cd_parecer_enquadramento_cci']) > 0))
			{
				if(trim($row['dt_envio']) == '')
				{
					echo button_save("Enviar para GC", 'enviar()', 'botao_verde');	
				}
				
				if(trim($row['dt_cancelamento']) == '')
				{
					if((trim($row['dt_envio'])) != '' AND (trim($row['dt_encerrado']) == '') AND ((gerencia_in(array('GC')))))
					{
						echo button_save("Encerrar", 'encerrar()', 'botao_vermelho');	
					}
				}		

				if(trim($row['dt_encerrado']) == '')
				{
					if($row['dt_cancelamento_fl'] == '')
					{
						echo button_save("Cancelar", 'cancelar();', 'botao_vermelho');
					}
				}	
			}	
		echo form_command_bar_detail_end();
	echo form_close();
	if((intval($row['cd_parecer_enquadramento_cci']) > 0))
	{
		if(trim($row['dt_envio']) != '')
		{
			echo form_open('gestao/parecer_enquadramento_cci/salvar_prorrogacao', 'id="form_prorrogacao"');
				echo form_start_box('default_box', 'Prorrogação');
					echo form_default_hidden('cd_parecer_enquadramento_cci', '', $row);
					echo form_default_date('dt_limite_prorrogacao', 'Dt Prorrogação : *', $row);	
				echo form_end_box('default_box');
				echo form_command_bar_detail_start();
					if((trim($row['dt_encerrado']) == '') AND (trim($row['dt_encerrado']) == '') AND ((gerencia_in(array('GC')))))
					{
						echo button_save('Salvar', 'salvar_prorrogacao()');	
					}
				echo form_command_bar_detail_end();
			echo form_close();
		}
	}	
echo aba_end();
$this->load->view('footer_interna');
?>