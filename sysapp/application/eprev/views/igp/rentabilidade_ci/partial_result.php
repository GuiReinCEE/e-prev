<?php
$body=array();
$head=array( 
	$label_0, $label_1, $label_2, $label_3, $label_4, $label_5, $label_6, $label_7, 
	$label_8, $label_9, $label_12, $label_13, $label_14, $label_15, $label_16, 
	$label_17, $label_18, $label_19, $label_20, ''
);

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


$a_data             = array(0, 0);
$contador_ano_atual = 0;

foreach($collection as $item)
{
    $a_data = explode( "/", $item['mes_referencia'] );

    if( $tabela[0]['cd_indicador_tabela']==$item['cd_indicador_tabela'] )
    {
        $contador_ano_atual++;
    }

	$body[] = array(
		$item['mes_referencia'],
		number_format($item['nr_rentabilidade'],4,',','.'),
		number_format($item['nr_benchmark'],4,',','.'),
		number_format($item['nr_diferenca'],4,',','.'),
		number_format($item['nr_rentabilidade_fator'],4,',','.'),
		number_format($item['nr_benchmark_fator'],4,',','.'),
		number_format($item['nr_rentabilidade_indice'],4,',','.'),
		number_format($item['nr_benchmark_indice'],4,',','.'),
		number_format($item['nr_rentabilidade_variacao'],4,',','.'),
		number_format($item['nr_benchmark_variacao'],4,',','.'),
		number_format($item['nr_poder'],4,',','.'),
		number_format($item['nr_peso_igp'],2,',','.'),
		number_format($item['nr_igp_mes'],4,',','.'),
		number_format($item['nr_igp_acumulado'],4,',','.'),
		number_format($item['nr_igp_media'],4,',','.'),
		number_format($item['nr_diferenca_acumulado'],4,',','.'),
		number_format($item['nr_referencia_mes'],2,',','.'),
		number_format($item['nr_referencia_acumulado'],2,',','.'),
		number_format($item['nr_referencia_media'],4,',','.'),
		($item['fl_editar'] == "S" ? anchor("igp/rentabilidade_ci/detalhe/" . $item["cd_rentabilidade_ci"], "editar") : "")
	);
}


echo "<input type='hidden' id='mes_input' name='mes_input' value='".$a_data[0]."' />";
echo "<input type='hidden' id='contador_input' name='contador_input' value='".$contador_ano_atual."' />";
$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>