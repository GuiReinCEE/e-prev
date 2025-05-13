<?php
set_title('Cronograma - Cadastro');
$this->load->view('header');
?>
<script>
	<?php
		echo form_default_js_submit(Array('periodo', 'cd_responsavel'));
	?>
	
	function ir_lista()
	{
		location.href='<?php echo site_url("atividade/atividade_cronograma"); ?>';
	}
	
	function cronogramaItem(cd_atividade_cronograma)
	{
		location.href='<?php echo site_url("atividade/atividade_cronograma/cronograma"); ?>' + "/" + cd_atividade_cronograma;
	}
	
	function ir_acompanhamento(cd_atividade_cronograma)
	{
		location.href = '<?php echo site_url("atividade/atividade_cronograma/acompanhamento"); ?>' + "/" + cd_atividade_cronograma;
	}	
	
	function ir_quadro_resumo(cd_atividade_cronograma)
	{
		location.href = '<?php echo site_url("atividade/atividade_cronograma/quadro_resumo"); ?>' + "/" + cd_atividade_cronograma;
	}

</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');
	if(intval($cd_atividade_cronograma) > 0)
	{
		$abas[] = array('aba_cronograma', 'Cronograma', FALSE, "cronogramaItem('".$cd_atividade_cronograma."');");
		$abas[] = array('aba_cronograma', 'Acompanhamento', FALSE, "ir_acompanhamento('".$cd_atividade_cronograma."');");
		$abas[] = array('aba_cronograma', 'Quadro Resumo', FALSE, "ir_quadro_resumo('".$cd_atividade_cronograma."');");
	}
	
	$arr_periodo[] = array('text' => '1� Quadrimestre', 'value' => '1');
	$arr_periodo[] = array('text' => '2� Quadrimestre', 'value' => '2');
	$arr_periodo[] = array('text' => '3� Quadrimestre', 'value' => '3');
	

	$head = array( 
		'Cronograma',
		'Reuni�o',
		'In�cio',
		'Final'
	);
	
	$body[] = array(
		'1� Quadrimestre', 'Mar�o', 'Abril', 'Julho'
	);
	
	$body[] = array(
		'2� Quadrimestre', 'Julho', 'Agosto', 'Novembro'
	);
	
	$body[] = array(
		'3� Quadrimestre', 'Novembro', 'Dezembro', 'Mar�o'
	);
	
	$this->load->helper('grid');
	$grid = new grid();
	$grid->view_count = false;
	$grid->head = $head;
	$grid->body = $body; 

	echo aba_start( $abas );
		echo form_open('atividade/atividade_cronograma/cronograma_salvar');
		echo form_start_box( "default_box", "Cronograma" );
			echo form_default_text('cd_atividade_cronograma', "C�digo: ", $cd_atividade_cronograma, "style='width:100%;border: 0px;' readonly" );
			echo form_default_text('dt_inclusao', "Dt. Inclus�o: ", $row, "style='width:100%;border: 0px;' readonly" );
			echo form_default_text('dt_exclusao', "Dt. Exclus�o: ", $row, "style='width:100%;border: 0px;' readonly" );
			echo form_default_text('dt_encerra', "Dt. Encerrado: ", $row, "style='width:100%;border: 0px;' readonly" );
			echo form_default_dropdown('periodo', 'Per�odo:* ', $arr_periodo, array($row['periodo']));
			
			echo form_default_usuario_ajax(array('cd_divisao','cd_responsavel'),$row['cd_divisao'],$row['cd_responsavel'], 'Respons�vel:*', 'Ger�ncia:*');
		echo form_end_box("default_box");
		echo form_command_bar_detail_start();
			if($row['dt_exclusao'] == "" AND trim($row['dt_encerra']) == "")
			{
				echo button_save("Salvar");		
			}
		echo form_command_bar_detail_end();
		echo form_close();
		echo form_start_box( "quadrimestre_box", "Quadrimestre" );
			echo $grid->render();
		echo form_end_box("quadrimestre_box");
	echo aba_end();
	$this->load->view('footer_interna');
?>