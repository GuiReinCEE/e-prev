<?php
$body=array();
$head=array( 
	$label_0, $label_1, $label_2, $label_3, $label_4, $label_5, $label_6, $label_7, 
	$label_8, $label_9, $label_10,''
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
echo '<div style="text-align:center;">'.br().anchor_popup("indicador/apresentacao/detalhe/".intval( $grafico[0]['cd_indicador_tabela'] ), '[Visualizar Apresentação]', $ar_janela)."</div>";


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
		array( number_format($item['nr_satisfacao'],2,',','.').'%','text-align:right;' ),
		array( number_format($item['nr_meta'], 0,',','.' ).'%', 'text-align:right;' ),
		array( number_format($item['nr_peso'],1,',','.'),'text-align:right;' ),
		array( number_format($item['nr_resultado_mes'],2,',','.').'%','text-align:right;' ),
		array( number_format($item['nr_referencia_mes'],2,',','.'),'text-align:right;' ),
		array( number_format($item['nr_resultado_acumulado'],2,',','.').'%','text-align:right;' ),
		array( number_format($item['nr_resultado_meta_acumulado'],2,',','.').'%','text-align:right;' ),
		array( number_format($item['nr_referencia_mes_acumulado'],2,',','.'),'text-align:right;' ),
		array( number_format($item['nr_media_movel_percentual'], 2,',','.' ).'%', 'text-align:right;' ),
		array( number_format($item['nr_media_movel'], 2,',','.' ), 'text-align:right;' ),
		($item['fl_editar'] == "S" ? anchor("igp/satisfacao_colab/detalhe/" . $item["cd_satisfacao_colab"], "editar") : "")
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