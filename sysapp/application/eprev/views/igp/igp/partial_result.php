<?php
$this->load->helper('grid');

$ar_janela = array(
			  'width'      => '700',
			  'height'     => '500',
			  'scrollbars' => 'yes',
			  'status'     => 'yes',
			  'resizable'  => 'yes',
			  'screenx'    => '0',
			  'screeny'    => '0'
			);
echo '<div style="text-align:center;">'.br().anchor_popup("indicador/apresentacao/detalhe/".intval( $tabela[0]['cd_indicador_tabela'] ), '[Visualizar Apresentação]', $ar_janela)."</div>";


#### ACUMULADO ###
echo form_start_box("default_acumulado", "RESULTADO ACUMULADO", FALSE);
$body=array();
$head = array('Mês','Total','Meta','Rentab CI','Recl.','Líq c/erro fl','Calc Inic','Custo','Equilibrio','Partic','Var orç','Treinam','Indisp. TI','% sat','Aval','Sat Part');
foreach( $collection as $item )
{
	$body[] = array(
		array($item["mes_referencia"], "text-align:center; font-weight:bold;")
		, array(number_format($item["acu_total"],2,',',''), "text-align:center; font-weight:bold;")
		, number_format( $item["nr_meta"], 2, ',', '')
		, number_format( $item["acu_rentabilidade_ci"], 2, ',', '' )
		, number_format( $item["acu_recl"], 2, ',', '' )
		, number_format( $item["acu_liq_erro"], 2, ',', '' )
		, number_format( $item["acu_calc_ini"], 2, ',', '' )
		, number_format( $item["acu_custo"], 2, ',', '' )
		, number_format( $item["acu_equilibrio"], 2, ',', '' )
		, number_format( $item["acu_participante"], 2, ',', '' )
		, number_format( $item["acu_var_orc"], 2, ',', '' )
		, number_format( $item["acu_treinamento"], 2, ',', '' )
		, number_format( $item["acu_informatica"], 2, ',', '' )
		, number_format( $item["acu_sat_colab"], 2, ',', '' )
		, number_format( $item["acu_aval"], 2, ',', '' )
		, number_format( $item["acu_sat_part"], 2, ',', '' )
		
		
	);
}
$grid = new grid();
$grid->id_tabela = 'table-1';
$grid->head = $head;
$grid->body = $body;
$grid->view_count = false;
echo $grid->render();
echo form_end_box("default_acumulado");

echo br();

#### DO MÊS ####
echo form_start_box("default_mes", "RESULTADO DO MÊS", FALSE);
$body=array();
$head = array('Mês','Total','Rentab CI','Recl.','Líq c/erro fl','Calc Inic','Custo','Equilibrio','Partic','Var orç','Treinam','Indisp. TI','% sat','Aval','Sat Part');
foreach( $collection as $item )
{
	$body[] = array(
		array($item["mes_referencia"], "text-align:center; font-weight:bold;")
		, array(number_format($item["mes_total"],2,',',''), "text-align:center; font-weight:bold;")
		, number_format( $item["mes_rentabilidade_ci"], 2, ',', '' )
		, number_format( $item["mes_recl"], 2, ',', '' )
		, number_format( $item["mes_liq_erro"], 2, ',', '' )
		, number_format( $item["mes_calc_ini"], 2, ',', '' )
		, number_format( $item["mes_custo"], 2, ',', '' )
		, number_format( $item["mes_equilibrio"], 2, ',', '' )
		, number_format( $item["mes_participante"], 2, ',', '' )
		, number_format( $item["mes_var_orc"], 2, ',', '' )
		, number_format( $item["mes_treinamento"], 2, ',', '' )
		, number_format( $item["mes_informatica"], 2, ',', '' )
		, number_format( $item["mes_sat_colab"], 2, ',', '' )
		, number_format( $item["mes_aval"], 2, ',', '' )
		, number_format( $item["mes_sat_part"], 2, ',', '' )
		
	);
}
$grid = new grid();
$grid->id_tabela = 'table-2';
$grid->head = $head;
$grid->body = $body;
$grid->view_count = false;
echo $grid->render();
echo form_end_box("default_mes");

echo br();

#### DO MÉDIA MÓVEL ####
echo form_start_box("default_media", "RESULTADO DA MÉDIA MÓVEL", FALSE);
$body=array();
$head = array('Mês','Total','Rentab CI','Recl.','Líq c/erro fl','Calc Inic','Custo','Equilibrio','Partic','Var orç','Treinam','Indisp. TI','% sat','Aval','Sat Part');
foreach( $collection as $item )
{
	$body[] = array(
		array($item["mes_referencia"], "text-align:center; font-weight:bold;")
		, array(number_format($item["mm_total"],2,',',''), "text-align:center; font-weight:bold;")
		, number_format( $item["mm_rentabilidade_ci"], 2, ',', '' )
		, number_format( $item["mm_recl"], 2, ',', '' )
		, number_format( $item["mm_liq_erro"], 2, ',', '' )
		, number_format( $item["mm_calc_ini"], 2, ',', '' )
		, number_format( $item["mm_custo"], 2, ',', '' )
		, number_format( $item["mm_equilibrio"], 2, ',', '' )
		, number_format( $item["mm_participante"], 2, ',', '' )
		, number_format( $item["mm_var_orc"], 2, ',', '' )
		, number_format( $item["mm_treinamento"], 2, ',', '' )
		, number_format( $item["mm_informatica"], 2, ',', '' )
		, number_format( $item["mm_sat_colab"], 2, ',', '' )
		, number_format( $item["mm_aval"], 2, ',', '' )
		, number_format( $item["mm_sat_part"], 2, ',', '' )
		
	);
}
$grid = new grid();
$grid->id_tabela = 'table-3';
$grid->head = $head;
$grid->body = $body;
$grid->view_count = false;
echo $grid->render();
echo form_end_box("default_media");


echo br();

#### HISTÓRICO ####
echo form_start_box("default_hist", "HISTÓRICO", FALSE);
$body=array();
$head = array('Ano','Resultado Acumulado');
foreach($ar_anual as $item )
{
	$body[] = array(
		array($item["nr_ano"], "text-align:center; font-weight:bold;")
		, array(number_format($item["resultado"],2,',',''), "text-align:center; font-weight:bold;")
	);
}
$grid = new grid();
$grid->id_tabela = 'table-4';
$grid->head = $head;
$grid->body = $body;
$grid->view_count = false;
$grid->width = 300;
#$grid->align = "left";
echo $grid->render();
echo form_end_box("default_hist");

?>