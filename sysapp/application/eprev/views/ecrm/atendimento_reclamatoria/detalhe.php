<?php
set_title('Reclamatória - Cadastro');
$this->load->view('header');
?>
<script>
	function salvar( form )
	{
	
		if( $("#cd_empressa").val()=="" )
		{
			alert( "Informe a Empresa." );
			$("#cd_empressa").focus();
			return false;
		}
		else if( $("#cd_registro_empregado").val()=="" )
		{
			alert( "Informe o RE." );
			$("#cd_registro_empregado").focus();
			return false;
		}
		else if( $("#seq_dependencia").val()=="" )
		{
			alert( "Informe a Sequência." );
			$("#seq_dependencia").focus();
			return false;
		}		
		else
		{
			form.submit();
		}
		
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

	function ir_lista()
	{
		location.href='<?php echo site_url("ecrm/atendimento_reclamatoria"); ?>';
	}
	
	function ir_retorno()
	{
		location.href='<?php echo site_url("ecrm/atendimento_reclamatoria/retorno/0/".$cd_atendimento_reclamatoria); ?>';
	}
	
	function ir_acompanhamento()
	{
		location.href='<?php echo site_url("ecrm/atendimento_reclamatoria/acompanhamento/".$cd_atendimento_reclamatoria); ?>';
	}

	function ir_anexo()
	{
		location.href='<?php echo site_url("ecrm/atendimento_reclamatoria/anexo/".$cd_atendimento_reclamatoria); ?>';
	}

	function pesquisar()
	{
		window.open("<?= site_url('ecrm/atendimento_reclamatoria/index/'.intval($cd_empresa).'/'.intval($cd_registro_empregado).'/'.intval($seq_dependencia)) ?>")
	}
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_detalhe', 'Reclamatória', TRUE, 'location.reload();');
	if(intval($cd_atendimento_reclamatoria) > 0)
	{	
		$abas[] = array('aba_acompanhamento', 'Acompanhamento', FALSE, 'ir_acompanhamento();');
		$abas[] = array('aba_anexo', 'Anexo', FALSE, 'ir_anexo();');
	}
	
	echo aba_start( $abas );

	echo form_open('ecrm/atendimento_reclamatoria/salvar');
	echo form_start_box( "default_box", "Cadastro" );
	
	echo form_hidden('cd_atendimento_reclamatoria', intval($cd_atendimento_reclamatoria));
	
	$participante['cd_empresa']            = intval($cd_empresa);
	$participante['cd_registro_empregado'] = intval($cd_registro_empregado);
	$participante['seq_dependencia']       = intval($seq_dependencia);
	$conf = array('cd_empresa','cd_registro_empregado','seq_dependencia', 'nome');
	echo form_default_participante( $conf, "Participante:*", $participante, FALSE, TRUE );	
	
	echo form_default_textarea("observacao","Observação:",$observacao);
	echo form_default_integer("cd_atendimento", "Atendimento:", $cd_atendimento); 
	echo form_default_text('dt_encerrado', "Dt. Encerrado: ", $dt_encerrado, "style='width:100%;border: 0px;' readonly" );
	echo form_end_box("default_box");
	
	// Barra de comandos ...
	echo form_command_bar_detail_start();

		
	if($dt_encerrado == "")
	{
		echo button_save("Salvar");

		if(intval($cd_atendimento_reclamatoria) > 0)
		{
			echo form_command_bar_detail_button("Registrar retorno", "ir_retorno();");
			echo form_command_bar_detail_button("Registrar acompanhamento", "ir_acompanhamento();");
			if(gerencia_in(array('GAP','GB')))
			{
				echo form_command_bar_detail_button("Encerrar", "encerrarReclamatoria(".intval($cd_atendimento_reclamatoria).");");
			}
		}	
	}
	if(intval($cd_registro_empregado) > 0)
	{	
		echo button_save("Pesquisar", "pesquisar()", "botao_verde");
	}
	echo form_command_bar_detail_end();
	


	if((intval($cd_atendimento_reclamatoria) > 0) or (($cd_empresa != "") and (intval($cd_registro_empregado) > 0) and ($seq_dependencia != "")))
	{
		echo "
				<script>
					consultar_participante__cd_empresa();
				</script>
			 ";
	}

	if(intval($cd_atendimento_reclamatoria) > 0)
	{	
	echo form_start_box( "lista_box", "Retorno",FALSE );
	
$body=array();
$head = array(
	'Cod.',
	'Data',
	'Observação',
	'Usuário'
);

foreach( $collection as $item )
{
	$body[] = array(
	anchor("ecrm/atendimento_reclamatoria/retorno/".$item["cd_atendimento_reclamatoria_retorno"], $item["cd_atendimento_reclamatoria_retorno"]),
	anchor("ecrm/atendimento_reclamatoria/retorno/".$item["cd_atendimento_reclamatoria_retorno"], $item["dt_inclusao"]),
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