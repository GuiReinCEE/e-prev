<?php
set_title('Cronograma - Quadro Resumo');
$this->load->view('header');
?>
<script>
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
	
	function ir_acompanhamento(cd_atividade_cronograma)
	{
		location.href = '<?php echo site_url("atividade/atividade_cronograma/acompanhamento"); ?>' + "/" + cd_atividade_cronograma;
	}
	
	function ir_concluidas_fora(cd_atividade_cronograma)
	{
		location.href = '<?php echo site_url("atividade/atividade_cronograma/concluidas_fora"); ?>' + "/" + cd_atividade_cronograma;
	}


</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	//$abas[] = array('aba_cadastro', 'Cadastro', FALSE, "ir_cadastro('".$cd_atividade_cronograma."');");
	$abas[] = array('aba_cronograma', 'Cronograma', FALSE, "ir_cronograma('".$cd_atividade_cronograma."');");
	$abas[] = array('aba_cronograma', 'Acompanhamento', FALSE, "ir_acompanhamento('".$cd_atividade_cronograma."');");
	$abas[] = array('aba_cronograma', 'Quadro Resumo', TRUE, "location.reload();");
	$abas[] = array('aba_cronograma', 'Conclu�das Fora', FALSE, "ir_concluidas_fora('".$cd_atividade_cronograma."');");
	
	$body=array();
	$head = array( 
		'Ger�ncia',
		'Qt Atividade',
		'Qt Atividade Priorizada',
		'Qt Ativ. Conclu�da',
		'Qt Ativ. Conc. Fora'
	);
	
	foreach( $collection as $item )
	{
		$body[] = array(
			$item["cd_gerencia"],
			array($item["qt_atividade"],'text-align:right;','int'),
			array($item["qt_atividade_prio"],'text-align:right;','int'),
			array($item["qt_atividade_conc"],'text-align:right;','int'),
			array($item["qt_atividade_conc_fora"],'text-align:right;','int')
		);
	}
	
	$this->load->helper('grid');
	$grid = new grid();
	$grid->sums = array(1,2,3,4);
	$grid->view_count = false;
	$grid->head = $head;
	$grid->body = $body;
	
		
	echo aba_start( $abas );
		echo form_start_box( "legenda_box", "Legenda" );
			echo form_default_row('','Qt Atividade: ','Quantidade de atividades dentro do cronograma.');
			echo form_default_row('','Qt Atividade Priorizada: ','Quantidade de atividades dentro do cronograma priorizada pelo Gerente.');
			echo form_default_row('','Qt Atividade Conclu�da: ','Quantidade de atividades dentro do cronograma priorizada pelo Gerente conclu�da/teste dentro do per�odo.');
			echo form_default_row('','Qt Atividade Conclu�da Fora: ','Quantidade de atividades que n�o est�o no cronograma ou n�o priorizada pelo Gerente conclu�da dentro do per�odo.');
		echo form_end_box("legenda_box");
		echo br();
		echo form_start_box( "default_box", "Quadro Resumo" );
			echo $grid->render();
		echo form_end_box("default_box");
	echo aba_end();
	$this->load->view('footer_interna');
?>