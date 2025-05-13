<?php
$this->load->helper('grid');
echo "<BR>";
    $ar_janela = array(
                  'width'      => '700',
                  'height'     => '500',
                  'scrollbars' => 'yes',
                  'status'     => 'yes',
                  'resizable'  => 'yes',
                  'screenx'    => '0',
                  'screeny'    => '0'
                );
echo anchor_popup("indicador/apresentacao/detalhe/".intval( $tabela[0]['cd_indicador_tabela'] ), 'Visualizar apresentação', $ar_janela);
echo "<BR>";
echo "<BR>";
echo h1('RESULTADO ACUMULADO');
$body=array();
$head = array('Mês','Rentab CI','Recl.','Líq c/erro fl','Calc Inic','Custo','Equilibrio','Partic','Var orç','Treinam','Banco','% sat','Aval','Sat Part','Total');
foreach( $collection as $item )
{
	$body[] = array(
		$item["mes_referencia"]
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
		, number_format( ( 
			  round( floatval( $item["acu_rentabilidade_ci"]), 2)
			+ round( floatval($item["acu_recl"]), 2)
			+ round( floatval($item["acu_liq_erro"]), 2)
			+ round( floatval($item["acu_calc_ini"]), 2)
			+ round( floatval($item["acu_custo"]), 2)
			+ round( floatval($item["acu_equilibrio"]), 2)
			+ round( floatval($item["acu_participante"]), 2)
			+ round( floatval($item["acu_var_orc"]), 2)
			+ round( floatval($item["acu_treinamento"]), 2)
			+ round( floatval($item["acu_informatica"]), 2)
			+ round( floatval($item["acu_sat_colab"]), 2)
			+ round( floatval($item["acu_aval"]), 2)
			+ round( floatval($item["acu_sat_part"]), 2)
		), 2, ',', '' )
	);
}
$grid = new grid();
$grid->id_tabela = 'table-1';
$grid->head = $head;
$grid->body = $body;
echo $grid->render();

echo h1('RESULTADO DO MÊS');
$body=array();
$head = array('Mês','Rentab CI','Recl.','Líq c/erro fl','Calc Inic','Custo','Equilibrio','Partic','Var orç','Treinam','Banco','% sat','Aval','Sat Part','Total');
foreach( $collection as $item )
{
	$body[] = array(
		$item["mes_referencia"]
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
		, number_format( (
			floatval($item["mes_rentabilidade_ci"]) + floatval($item["mes_recl"]) + floatval($item["mes_liq_erro"]) + floatval($item["mes_calc_ini"]) + 
			floatval($item["mes_custo"]) + floatval($item["mes_equilibrio"]) + floatval($item["mes_participante"]) + floatval($item["mes_var_orc"]) + 
			floatval($item["mes_treinamento"]) + floatval($item["mes_informatica"]) + floatval($item["mes_sat_colab"]) + floatval($item["mes_aval"]) + 
			floatval($item["mes_sat_part"])
		), 2, ',', '' )
	);
}
$grid = new grid();
$grid->id_tabela = 'table-2';
$grid->head = $head;
$grid->body = $body;
echo $grid->render();

echo h1('RESULTADO DA MÉDIA MÓVEL');
$body=array();
$head = array('Mês','Rentab CI','Recl.','Líq c/erro fl','Calc Inic','Custo','Equilibrio','Partic','Var orç','Treinam','Banco','% sat','Aval','Sat Part','Total');
foreach( $collection as $item )
{
	$body[] = array(
		$item["mes_referencia"]
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
		, number_format( (
			floatval($item["mm_rentabilidade_ci"]) + floatval($item["mm_recl"]) + floatval($item["mm_liq_erro"]) + floatval($item["mm_calc_ini"]) + 
			floatval($item["mm_custo"]) + floatval($item["mm_equilibrio"]) + floatval($item["mm_participante"]) + floatval($item["mm_var_orc"]) + 
			floatval($item["mm_treinamento"]) + floatval($item["mm_informatica"]) + floatval($item["mm_sat_colab"]) + floatval($item["mm_aval"]) + 
			floatval($item["mm_sat_part"])
		), 2, ',', '' )
	);
}
$grid = new grid();
$grid->id_tabela = 'table-4';
$grid->head = $head;
$grid->body = $body;
echo $grid->render();

echo h1('META');
$body=array();
$head = array('Mês','Meta', '');
foreach( $collection as $item )
{
	$body[] = array(
		$item["mes_referencia"]
		, number_format( $item["nr_meta"], 2, ',', '' )
		, anchor('','editar')
	);
}
$grid = new grid();
$grid->id_tabela = 'table-3';
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>