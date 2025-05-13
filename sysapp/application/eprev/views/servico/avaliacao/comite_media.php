<?php
	set_title('Avaliação - Comparativo - Competências Institucionais');
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
$abas[] = array('aba_media', 'Média do Comitê', TRUE, 'location.reload();');
echo aba_start( $abas );

	// *** integrantes
	$integrantes = '';
	foreach($comite_componentes as $item)
	{ 
		$integrantes.= $item['nome_avaliador'].br(); 
	}

	$head[] = 'Competência';
	$head[] = 'Avaliado'; 
	$head[] = 'Superior'; 
	
	$ar_soma = Array();
	foreach($comite_avaliador as $item)
	{
		$head[] = "Comitê";//$item['seq'];
		$ar_soma[$item['cd_avaliacao']] = 0;
	}
	
	$head[] = 'Média Comitê';
	
	#print_r($head);
	
	#print_r($comite_avaliador);
	#print_r($comite);
	#echo "<PRE>".print_r($comite_avaliador, true)."</PRE>";	
	
	$sum_comite_media  = 0;
	$sum_avaliado  = 0;
	$sum_superior  = 0;
	$qt_reg = 0;
	$nr_fim   = count($comite_media);
	$nr_conta = 0;
	while($nr_conta < $nr_fim)
	{
		$nr_nota_superior = $superior[$comite_media[$nr_conta]['cd_comp_inst']];
	
		$reg = Array();
		$reg[] = array($comite_media[$nr_conta]['nome_comp_inst'],'text-align:left;');
		$reg[] = number_format($avaliado[$comite_media[$nr_conta]['cd_comp_inst']],2,",",".");
		$reg[] = number_format($superior[$comite_media[$nr_conta]['cd_comp_inst']],2,",",".");
		
		foreach($comite_avaliador as $i)
		{
			$reg[] = number_format($comite[$i['cd_avaliacao']][$comite_media[$nr_conta]['cd_comp_inst']],2,",",".");
			$ar_soma[$i['cd_avaliacao']] += $comite[$i['cd_avaliacao']][$comite_media[$nr_conta]['cd_comp_inst']];
		}		

		$reg[] = array(number_format($comite_media[$nr_conta]['grau'],2,",",".") , 'text-align:center;'.($nr_nota_superior != $comite_media[$nr_conta]['grau'] ? "font-weight:bold; color:green;" : ""));
		$body[] = $reg;

						
		$sum_comite_media+= floatval($comite_media[$nr_conta]['grau']);
		$sum_avaliado+= floatval($avaliado[$comite_media[$nr_conta]['cd_comp_inst']]);
		$sum_superior+= floatval($superior[$comite_media[$nr_conta]['cd_comp_inst']]);
		$qt_reg++;
		$nr_conta++;
	}
	
	$reg = Array();
	$reg[] = array("Grau Obtido",'font-size: 110%; font-weight: bold; text-align:left;');
	$reg[] = array(number_format(round($sum_avaliado / $qt_reg,2),2,",","."),'font-size: 110%; font-weight: bold; text-align:center;');
	$reg[] = array(number_format(round($sum_superior / $qt_reg,2),2,",","."),'font-size: 110%; font-weight: bold; text-align:center;');
	
	foreach($comite_avaliador as $i)
	{
		$reg[] = array(number_format(round($ar_soma[$i['cd_avaliacao']] / $qt_reg,2),2,",","."),'font-size: 110%; font-weight: bold; text-align:center;');
	}	
	
	$reg[] = array(number_format(round($sum_comite_media / $qt_reg,2),2,",","."),'font-size: 115%; font-weight: bold; text-align:center;') ;
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
			.form_default_row('','Comitê:',$integrantes)
			
		.form_end_box('avaliacao_identifica_box');

	echo 
	form_start_box('grau_box','Competências Institucionais ', false)
		.$lista
	.form_end_box('grau_box',false);

	echo br(6);
echo aba_end(''); 

$this->load->view('footer');
?>