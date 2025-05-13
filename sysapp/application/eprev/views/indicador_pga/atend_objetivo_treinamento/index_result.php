<?php
$body = array();
$head = array( 
	'#', $label_0, $label_7, $label_1, $label_2, $label_8, $label_9, ''
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

$contador_ano_atual = 0;
$contador           = sizeof($collection);
$ultimo_mes         = 0;

$referencia = '';

$treinamento_realizados_total    = 0;
$obj_atendidos_treinamento_total = 0;
$nr_meta                         = 0;

foreach($collection as $key => $item)
{
	if(trim($item['fl_media']) == 'S')
	{
		$referencia = 'Resultado de '.intval($item['ano_referencia']);

		$link = '';
	}
	else
	{
		$referencia = $item['mes_ano_referencia'];

		$link = anchor('indicador_pga/atend_objetivo_treinamento/cadastro/'.$item['cd_atend_objetivo_treinamento'], '[editar]');
	}

	if((intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia'])) && ($item['fl_media'] != 'S'))
	{
		$contador_ano_atual++;

		$ultimo_mes = $item['mes_referencia'];
		
		$treinamento_realizados_total    += $item['nr_valor_total'];
		$obj_atendidos_treinamento_total += $item['nr_valor_1'];
		$nr_meta                         = $item['nr_meta'];
	}

	$body[] = array(
		$contador--,
		$referencia,
		number_format($item['nr_valor_total'],0),
		number_format($item['nr_valor_1'],0),
		number_format($item['nr_resultado_1'], 2, ',' ,'.').' %',
		number_format($item['nr_meta'], 2, ',' ,'.').' %',
		array(nl2br($item['observacao']), 'text-align:justify'),
		$link 
	);
}

if(intval($contador_ano_atual) > 0)
{
	
	$body[] = array(
		0,
		'<b>Resultado de '.intval($tabela[0]['nr_ano_referencia']).'</b>',
		'<b>'.intval($treinamento_realizados_total).'</b>',
		'<b>'.intval($obj_atendidos_treinamento_total).'</b>',
		'<b>'.($treinamento_realizados_total > 0 ? number_format((($obj_atendidos_treinamento_total/$treinamento_realizados_total) * 100), 2, ',' ,'.') : 0).' %</b>',
		'<b>'.number_format($nr_meta, 2, ',', '.').' %</b>',
		'',
		''
	);

	
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
$grid->view_count = false;
?>

<input type="hidden" id="ultimo_mes" name="ultimo_mes" value="<?= $ultimo_mes ?>"/>
<input type="hidden" id="contador" name="contador" value="<?= $contador_ano_atual ?>"/>
<div style="text-align:center;"> 
	<?= anchor_popup('indicador/apresentacao/detalhe/'.intval($tabela[0]['cd_indicador_tabela']), '[Visualizar Apresentação]', $ar_janela) ?>
</div>
<br/>
<?= $grid->render() ?>