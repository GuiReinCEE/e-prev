<?php
$body=array();
$head = array( 
	$label_0, $label_1, $label_2, $label_3, $label_4, $label_5, $label_6, $label_7, 
	$label_8, $label_9, $label_10, $label_11, $label_12, '', '', ''
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

foreach( $collection as $item )
{
    $a_data = explode( "/", $item['mes_referencia'] );

    if( $tabela[0]['cd_indicador_tabela']==$item['cd_indicador_tabela'] )
    {
        $contador_ano_atual++;
    }
	
	$body[] = array(
		$item['mes_referencia'],
		number_format($item['nr_tecnica'], 2, ',', '.'),
		number_format($item['nr_matematica'], 2, ',', '.'),
		number_format($item['nr_tec_mat_percentual']/100, 4, ',', '.'),
		number_format($item['nr_meta']/100, 4, ',', '.'),
		number_format($item['nr_peso'], 2, ',', '.'),
		number_format($item['nr_tecnica_acumulado'], 2, ',', '.'),
		number_format($item['nr_matematica_acumulado'], 2, ',', '.'),
		number_format($item['nr_tec_mat_percentual_acumulado']/100, 4, ',', '.'),
		number_format($item['nr_referencia_mes_acumulado'], 2, ',', '.'),
		number_format($item['nr_referencia_mes'], 2, ',', '.'),
		number_format($item['nr_tec_mat_percentual_acumulado_meta'], 2, ',', '.'),
		number_format($item['nr_tec_mat_meta'], 2, ',', '.'),
		number_format($item['nr_media_movel_percentual'], 2, ',', '.').'%',
		number_format($item['nr_media_movel'], 2, ',', '.'),
		($item['fl_editar'] == "S" ? anchor("igp/equilibrio/detalhe/" . $item["cd_equilibrio"], "editar") : "")
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