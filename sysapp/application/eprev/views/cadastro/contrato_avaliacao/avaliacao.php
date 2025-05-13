<?php
set_title('Cadastros, Contratos, Avaliação');
$this->load->view('header');
?>
<script>
	<?php
		echo form_default_js_submit(array('cd_contrato', 'cd_contrato_formulario', 'dt_inicio_avaliacao', 'dt_fim_avaliacao', 'dt_limite_avaliacao'));
	?>
	
	function ir_lista()
	{
		location.href = '<?php echo site_url("cadastro/contrato_avaliacao"); ?>';
	}
	
	function ir_resultado()
	{
		location.href = '<?php echo site_url("cadastro/contrato_avaliacao/resultado/".$row["cd_contrato_avaliacao"]); ?>';
	}
	
	function excluir()
	{
		if(confirm("Excluir?"))
		{
			location.href='<?php echo site_url("cadastro/contrato_avaliacao/excluir/".$row["cd_contrato_avaliacao"]); ?>';
		}
	}	
	
	function excluir_avaliador(cd_contrato_avaliacao_item)
	{
		if(confirm("Excluir o avaliador?"))
		{
			$.post('<?php echo site_url('/cadastro/contrato_avaliacao/excluir_avaliador');?>', 
			{ 
				cd_contrato_avaliacao_item : cd_contrato_avaliacao_item
			}, 
			function()
			{
				load();
			});
		}
	}
	
	function load()
	{
		$('#result_div').html("<?php echo loader_html(); ?>");

		$.post('<?php echo site_url('/cadastro/contrato_avaliacao/listar_grupos');?>', 
		{ 
			cd_contrato_formulario : $('#cd_contrato_formulario').val(),
			cd_contrato_avaliacao  : $('#cd_contrato_avaliacao').val()
		}, 
		function(data)
		{
			$('#result_div').html(data);
		});
	}
	
	function salvar_avaliador()
	{
		var bol = true;
	
		if( $("#cd_usuario_avaliador_gerencia").val()=="" )
		{
			alert( "Informe os campos obrigatórios! \n\n(os campos obrigatórios tem um * logo após a identificação).\n\n[cd_usuario_avaliador_gerencia]" );
			$("#cd_usuario_avaliador_gerencia").focus();
			return false;
		}
		
		if( $("#cd_usuario_avaliador").val()=="" )
		{
			alert( "Informe os campos obrigatórios! \n\n(os campos obrigatórios tem um * logo após a identificação).\n\n[cd_usuario_avaliador]" );
			$("#cd_usuario_avaliador").focus();
			return false;
		}
			
				
		if( $("#cd_contrato_formulario_grupo").val()=="" )
		{
			alert( "Informe os campos obrigatórios! \n\n(os campos obrigatórios tem um * logo após a identificação).\n\n[cd_contrato_formulario_grupo]" );
			$("#cd_contrato_formulario_grupo").focus();
			return false;
		}
		
		if(bol)
		{
			$.post('<?php echo site_url('/cadastro/contrato_avaliacao/salvar_avaliador');?>', 
			{ 
				cd_contrato_avaliacao        : $('#cd_contrato_avaliacao').val(),
				cd_divisao                   : $('#cd_usuario_avaliador_gerencia').val(),
				cd_usuario_avaliador         : $('#cd_usuario_avaliador').val(),
				cd_contrato_formulario_grupo : $('#cd_contrato_formulario_grupo').val()
			}, 
			function()
			{
				load();
				limpa_campos_avaliador();
			});
		}
	}
	
	function limpa_campos_avaliador()
	{
		$('#cd_usuario_avaliador_gerencia').val('');
		$('#cd_usuario_avaliador').val('');
		$('#cd_contrato_formulario_grupo').val('');
	}
	
	function enviar_email()
	{
		if(confirm('Enviar emails?'))
		{
			location.href = '<?php echo site_url("cadastro/contrato_avaliacao/enviar_email/".$row["cd_contrato_avaliacao"]); ?>';
		}
	}
	
	function reabrir()
	{
		if(confirm('Reabrir?'))
		{
			location.href = '<?php echo site_url("cadastro/contrato_avaliacao/reabrir/".$row["cd_contrato_avaliacao"]); ?>';
		}
	}
	
	$(function(){
		if($('#cd_contrato_avaliacao').val() > 0)
		{
			load();
		}
	});
</script>

<?php
$abas[] = array( 'aba_lista', 'Lista', FALSE, 'ir_lista()' );
$abas[] = array( 'aba_avaliacao', 'Avaliação', TRUE, 'location.reload();' );

if(intval($row["cd_contrato_avaliacao"]) > 0)
{
	$abas[] = array( 'aba_resultado', 'Resultado', FALSE, 'ir_resultado()' );
}

echo aba_start( $abas );
	echo form_open('cadastro/contrato_avaliacao/salvar');
		echo form_start_box("contrato_box", "Cadastro");
			echo form_hidden("cd_contrato_avaliacao", $row['cd_contrato_avaliacao'] );
			echo form_hidden("dt_envio_email", $row['dt_envio_email'] );
			echo form_default_dropdown_db("cd_contrato", "Contrato:*", array("projetos.contrato","cd_contrato","ds_empresa || ' - ' || ds_servico"), array($row['cd_contrato']));
			echo form_default_dropdown_db("cd_contrato_formulario", "Formulário:*", array("projetos.contrato_formulario","cd_contrato_formulario","ds_contrato_formulario"), array($row['cd_contrato_formulario']));
			echo form_default_date("dt_inicio_avaliacao", "Dt Início da Avaliação:*", $row);
			echo form_default_date("dt_fim_avaliacao", "Dt Final da Avaliação:*", $row);
			echo form_default_date("dt_limite_avaliacao", "Dt Limite da Avaliação:*", $row);
		echo form_end_box("contrato_box");
		echo form_command_bar_detail_start("salvar_avaliacao_box");
			if($row['dt_envio_email'] == '')
			{
				echo button_save("Salvar as configurações da avaliação");
				
				if(intval($row["cd_contrato_avaliacao"]) > 0)
				{
					echo button_save('Excluir', 'excluir()', 'botao_vermelho');
					echo button_save("Enviar email", "enviar_email();", 'botao_verde', 'id="btn_enviar"');
				}
			}
			else
			{
				echo button_save("Reabrir", "reabrir();", 'botao_vermelho');
			}

		echo form_command_bar_detail_end();
	echo form_close();
	
		if(intval($row["cd_contrato_avaliacao"]) > 0)
		{
			echo form_start_box("avaliador_box", "Avaliador");
				echo form_default_usuario_ajax("cd_usuario_avaliador", '', '', 'Usuário:*', 'Gerência:*');
				echo form_default_dropdown('cd_contrato_formulario_grupo', 'Grupo:*', $arr_grupos);
			echo form_end_box("avaliador_box");
			echo form_command_bar_detail_start("salvar_avaliador_box");
			if($row['dt_envio_email'] == '')
			{
				echo button_save("Adicionar", 'salvar_avaliador()');
			}
			echo form_command_bar_detail_end();
			echo form_start_box("grupo_box", "Grupos");
				echo '<div id="result_div"></div>';
			echo form_end_box("grupo_box");
		}
	echo br();

echo aba_end(); 
$this->load->view('footer');
?>
