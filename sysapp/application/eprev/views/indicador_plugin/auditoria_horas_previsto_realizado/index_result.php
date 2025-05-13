<?php
$head = array( 
	'#', $label_0, $label_1, $label_2, $label_3, $label_4, $label_5, $label_6, ''
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

$a_data                    = array(0, 0);
$contador_ano_atual        = 0;
$contador                  = sizeof($collection);
$horas_previsto_realizado  = array();
$media_ano                 = array();


$ultimo_mes                = 0;

$nr_meta = 0;

$body = array();

foreach($collection as $item)
{
	$a_data     = explode("/", $item['mes_referencia']);
	
	if(trim($item['fl_media']) == 'S')
	{
		$referencia = 'Resultado de '.intval($item['ano_referencia']);

		$link = '';
	}
	else
	{
		$referencia = $item['mes_ano_referencia'];

		$link = anchor('indicador_plugin/auditoria_horas_previsto_realizado/cadastro/'.$item['cd_auditoria_horas_previsto_realizado'], '[editar]');
	}

	if((intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia'])) && ($item['fl_media'] != 'S'))
	{
		$contador_ano_atual++;

		$ultimo_mes = $item['mes_referencia'];

		$media_ano[] = $item['nr_previstas_realizadas'];
	}

	$body[] = array(
		$contador--,
		$item['ds_evento'],
		$item['nr_horas_previstas'],
		$item['nr_horas_realizadas'],
		number_format($item['nr_previstas_realizadas'], 2, ',', '.' ).'%',
		number_format($item['nr_percentual_acima_meta'], 2, ',', '.' ).'%',
		number_format($item['nr_meta'], 2, ',', '.' ), 
		array(nl2br($item['observacao']), "text-align:left;"),
		$link
	);
}


$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;

echo "<input type='hidden' id='mes_input' name='mes_input' value='".$a_data[0]."' />";
echo "<input type='hidden' id='contador_input' name='contador_input' value='".$contador_ano_atual."' />";
?>
<?= $grid->render() ?>