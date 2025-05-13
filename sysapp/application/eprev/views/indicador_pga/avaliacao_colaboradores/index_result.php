<?php
$body=array();
$head = array( 
	"#",$label_0, $label_1, $label_2, $label_3, $label_4, /*$label_7,*/ $label_6, ''
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

$contador_ano_atual = 0;
$contador = sizeof($collection);
$total_1 = 0;
$total_2 = 0;
$ultima_meta = 0;
$ultima_meta_2 = 0;

$a_data = array(0, 0);

foreach( $collection as $item )
{
	$a_data = explode( "/", $item['mes_referencia'] );
	$observacao = $item["observacao"];

	if(trim($item['fl_media']) == 'S')
	{
		$link = '';
	
		$referencia = " Total de " . $item['ano_referencia'];
	}
	else
	{
		$link = anchor("indicador_pga/avaliacao_colaboradores/cadastro/".$item["cd_avaliacao_colaboradores"], "editar");

		$referencia = $item['ano_referencia'];
	}
	
	$nr_valor_1   = $item['nr_valor_1'];
	$nr_valor_2   = $item['nr_valor_2'];
	$nr_resultado = $item['nr_resultado'];
	$nr_meta      = $item['nr_meta'];
	$nr_meta_2    = $item['nr_meta_2'];

	if( intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) && $item['fl_media']!='S' )
	{
		$contador_ano_atual++;		
	}

	$body[] = array(
		$contador--,
		$referencia,
		number_format($nr_valor_1,2,',','.'),
		number_format($nr_valor_2,0),
		number_format($nr_resultado,2,',','.'),
		number_format($nr_meta,2,',','.'),
		//number_format($nr_meta_2,2,',','.'),
		array($observacao, 'text-align:"left"'),
		$link 
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
