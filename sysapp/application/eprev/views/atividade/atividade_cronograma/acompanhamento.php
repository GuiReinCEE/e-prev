<?php
set_title('Cronograma - Acompanhamento');
$this->load->view('header');
?>
<script>
	<?php
		echo form_default_js_submit(Array('descricao'));
	?>

	function ir_lista()
	{
		location.href='<?php echo site_url("atividade/atividade_cronograma"); ?>';
	}
	
	function ir_cadastro(cd_atividade_cronograma)
	{
		location.href='<?php echo site_url("atividade/atividade_cronograma/cadastro"); ?>' + "/" + cd_atividade_cronograma;
	}
	
	function ir_cronograma(cd_atividade_cronograma)
	{
		location.href = '<?php echo site_url("atividade/atividade_cronograma/cronograma"); ?>' + "/" + cd_atividade_cronograma;
	}
	
	function ir_quadro_resumo(cd_atividade_cronograma)
	{
		location.href = '<?php echo site_url("atividade/atividade_cronograma/quadro_resumo"); ?>' + "/" + cd_atividade_cronograma;
	}
	
	function ir_concluidas_fora(cd_atividade_cronograma)
	{
		location.href = '<?php echo site_url("atividade/atividade_cronograma/concluidas_fora"); ?>' + "/" + cd_atividade_cronograma;
	}

	
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	if($fl_responsavel)
	{
		//$abas[] = array('aba_cadastro', 'Cadastro', FALSE, "ir_cadastro('".$cd_atividade_cronograma."');");
	}	
	$abas[] = array('aba_cronograma', 'Cronograma', FALSE, "ir_cronograma('".$cd_atividade_cronograma."');");
	$abas[] = array('aba_cronograma', 'Acompanhamento', TRUE, "location.reload();");
	$abas[] = array('aba_cronograma', 'Quadro Resumo', FALSE, "ir_quadro_resumo('".$cd_atividade_cronograma."');");
	$abas[] = array('aba_cronograma', 'Concluídas Fora', FALSE, "ir_concluidas_fora('".$cd_atividade_cronograma."');");

	$body=array();
	$head = array( 
		'Descrição',
		'Dt Inclusão',
		'Nome'
		
	);

	foreach( $collection as $item )
	{
		$body[] = array(
			array($item["descricao"],"text-align:left;"),
			$item['dt_inclusao'],
			$item['nome']
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
	
	echo aba_start( $abas );
		echo form_start_box( "default_box", "Cronograma" );
			echo form_default_text('descricao', "Descrição: ", $row, "style='width:300%;border: 0px;' readonly" );
			echo form_default_text('periodo', "Período: ", $row['dt_inicio'] .' á '. $row['dt_final'], "style='width:300%;border: 0px;' readonly" );
			echo form_default_text('nome', "Responsável: ", $row, "style='width:300%;border: 0px;' readonly" );
		echo form_end_box("default_box");
		echo form_open('atividade/atividade_cronograma/salvar_acompanhamento');
			echo form_start_box( "default_box", "Cronograma" );
			    echo form_default_hidden('cd_atividade_cronograma', "",$cd_atividade_cronograma);
				echo form_default_textarea('descricao', "Descrição:* ");
			echo form_end_box("default_box");
			echo form_command_bar_detail_start();
				echo button_save("Salvar");
			echo form_command_bar_detail_end();
		echo form_close();
		echo $grid->render();
	echo aba_end();
	$this->load->view('footer_interna');
?>