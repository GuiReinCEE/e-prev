<?php
	set_title('Avaliação - Comparativo - Responsabilidades');
	$this->load->view('header');
?>
<script>
	function ir_lista()
	{
		location.href='<?php echo base_url_eprev()."avaliacao.php"; ?>';
	}
	
	function ir_resultado()
	{
		location.href='<?php echo base_url_eprev()."avaliacao.php?tipo=F&cd_capa=".intval($capa['cd_avaliacao_capa']); ?>';
	}	
</script>
<style>
	#avaliacao_identifica_box_title {
		font-size: 110%;
	}
	
</style>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_resultado', 'Resultado', FALSE, 'ir_resultado();');
$abas[] = array('aba_competencia', 'Responsabilidades', TRUE, 'location.reload();');
echo aba_start( $abas );


	$head[] = 'Competência';
	$head[] = 'Avaliado'; 
	$head[] = 'Superior'; 
	

	$sum_avaliado  = 0;
	$sum_superior  = 0;
	$qt_reg = 0;

	foreach($avaliado as $item)
	{
		$body[] = Array (
					array($item['nome_responsabilidade'],'text-align:left;'),
					number_format($item['grau'],2,",","."),
					array(number_format($superior[$item['cd_responsabilidade']],2,",","."), 'text-align:center;'.($item['grau'] != $superior[$item['cd_responsabilidade']] ? "font-weight:bold; color:green;" : ""))
				);
		
		
		$sum_avaliado+= floatval($item['grau']);
		$sum_superior+= floatval($superior[$item['cd_responsabilidade']]);
		$qt_reg++;
	}

	$reg = Array();
	$reg[] = array("Grau Obtido",'font-size: 110%; font-weight: bold; text-align:left;');
	$reg[] = array(number_format(round($sum_avaliado / $qt_reg,2),2,",","."),'font-size: 110%; font-weight: bold; text-align:center;');
	$reg[] = array(number_format(round($sum_superior / $qt_reg,2),2,",","."),'font-size: 110%; font-weight: bold; text-align:center;');
	$body[] = $reg;

	
	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
	$grid->view_count=false;
	$grid->hide_sum=true;
	$lista=$grid->render();

	
	echo form_start_box('avaliacao_identifica_box',$capa['nome_avaliado'])
			.form_default_row('','Ano:',$capa['dt_periodo'])
			.form_default_row('','Avaliador:',$capa['nome_avaliador'])
			
		.form_end_box('avaliacao_identifica_box');

	echo 
	form_start_box('grau_box','Responsabilidades ', false)
		.$lista
	.form_end_box('grau_box',false);

	echo br(6);
echo aba_end(''); 

$this->load->view('footer');
?>