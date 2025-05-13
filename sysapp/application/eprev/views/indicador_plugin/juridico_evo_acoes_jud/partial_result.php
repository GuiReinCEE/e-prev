<?php
$body=array();
$head = array( 
	'#', $label_0, $label_1, $label_2, $label_3, $label_5, ''
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

echo '<div style="text-align:center;">'.br().anchor_popup("indicador/apresentacao/detalhe/".intval($tabela[0]['cd_indicador_tabela']), '[Visualizar Apresentação]', $ar_janela)."</div>";

$contador_ano_atual=0;
$contador = sizeof($collection);
$media_ano=array();
$a_data=array(0, 0);
$nr_valor_f1 = 0;
foreach( $collection as $item )
{
	$a_data = explode( "/", $item['mes_referencia'] );

	$nr_meta = $item["nr_meta"];

	$observacao = $item["observacao"];

	$nr_percentual_f = $item['nr_percentual_f'];

	if( $item['fl_media']=='S' )
	{
		$link = '';

		$referencia = " Média de " . $item['ano_referencia'];

		$nr_valor_1 = '';
		$nr_valor_f1 = '';
		$nr_percentual_f = $item['nr_percentual_f'];
	}
	else
	{
		$link = anchor("indicador_plugin/juridico_evo_acoes_jud/detalhe/" . $item["cd_juridico_evo_acoes_jud"], "editar");

		$referencia = $item['ano_referencia'];

		$nr_valor_1 = $item["nr_valor_1"];
		$nr_valor_ant = $nr_valor_f1;
		$nr_valor_f1 += $nr_valor_1;

		if($nr_valor_1 != $nr_valor_f1){
			$nr_percentual_f =((floatval($nr_valor_f1)/floatval($nr_valor_ant)-1)*100);
		}
	}

	if( intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) && $item['fl_media']!='S' )
	{
		$contador_ano_atual++;
		$media_ano[] = $nr_percentual_f;
	}

	$body[] = array(
		 $contador--
		, $referencia
		, ($nr_valor_1!='')?$nr_valor_1:''
		, ($nr_valor_f1!='')?$nr_valor_f1:''
		, ($nr_percentual_f!='')?number_format($nr_percentual_f,2,',','.').' %':'-'
		, array($observacao, 'text-align:"left"')
		, ($item['fl_editar'] == "S" ? $link : "")
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
