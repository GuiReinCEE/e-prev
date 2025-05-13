<?php
set_title('Reclamatória - Acompanhamento');
$this->load->view('header');
?>
<script>
	function salvar( form )
	{
	
		if( $("#cd_empressa").val()=="" )
		{
			alert("Informe a Descrição.");
			$("#observacao").focus();
			return false;
		}
		else
		{
			form.submit();
		}
		
	}

	function ir_lista()
	{
		location.href='<?php echo site_url("ecrm/atendimento_reclamatoria"); ?>';
	}
	
	function ir_retorno()
	{
		location.href='<?php echo site_url("ecrm/atendimento_reclamatoria/retorno/0/".$cd_atendimento_reclamatoria); ?>';
	}
	
	function ir_detalhe()
	{
		location.href='<?php echo site_url("ecrm/atendimento_reclamatoria/detalhe/".$cd_atendimento_reclamatoria); ?>';
	}	

	function ir_anexo()
	{
		location.href='<?php echo site_url("ecrm/atendimento_reclamatoria/anexo/".$cd_atendimento_reclamatoria); ?>';
	}
	
	function encerrarReclamatoria(cd)
	{
		if(confirm("Deseja encerrar esta reclamatória?"))
		{
			$.post( '<?php echo base_url() . index_page(); ?>/ecrm/atendimento_reclamatoria/encerra'
				,{
					cd_atendimento_reclamatoria : cd
				}
				,
				function(data)
				{
					location.reload();
				}
			);
		}
	}	
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_detalhe', 'Reclamatória', FALSE, 'ir_detalhe();');
	$abas[] = array('aba_acompanhamento', 'Acompanhamento', TRUE, 'location.reload();');
	$abas[] = array('aba_anexo', 'Anexo', FALSE, 'ir_anexo();');
	
	echo aba_start( $abas );

	echo form_open('ecrm/atendimento_reclamatoria/salvarAcompanhamento');
	echo form_start_box( "default_box", "Cadastro" );
	
	echo form_hidden('cd_atendimento_reclamatoria', intval($cd_atendimento_reclamatoria));
	echo form_default_textarea("observacao","Descrição*:",$observacao, "style='width:500px; height: 100px;'");
	echo form_default_text('dt_encerrado', "Dt. Encerrado: ", $dt_encerrado, "style='width:100%;border: 0px;' readonly" );
	echo form_end_box("default_box");
	
	// Barra de comandos ...
	echo form_command_bar_detail_start();
	echo button_save("Salvar");
	
	if($dt_encerrado == "")
	{
		if(intval($cd_atendimento_reclamatoria) > 0)
		{
			if(gerencia_in(array('GAP','GB')))
			{		
				echo form_command_bar_detail_button("Encerrar", "encerrarReclamatoria(".intval($cd_atendimento_reclamatoria).");");
			}
		}	
	}	
	
	echo form_command_bar_detail_end();

	if(intval($cd_atendimento_reclamatoria) > 0)
	{	
			echo form_start_box( "lista_box", "Lista",FALSE );
			
		$body=array();
		$head = array(
			'Cod.',
			'Data',
			'Descrição',
			'Usuário'
		);

		foreach( $collection as $item )
		{
			$body[] = array(
			$item["cd_atendimento_reclamatoria_acompanhamento"],
			$item["dt_inclusao"],
			array($item["observacao"],"text-align:justify;"),
			array($item["usuario"],"text-align:left;")
		);
		}

		$this->load->helper('grid');
		$grid = new grid();
		$grid->head = $head;
		$grid->body = $body;
		echo $grid->render();	
			echo form_end_box("lista_box",FALSE);
	}
		echo aba_end();
	// FECHAR FORM
	echo form_close();
	
	$this->load->view('footer');
?>