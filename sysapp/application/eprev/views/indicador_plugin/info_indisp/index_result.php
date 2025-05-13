<?php
$head = array( 
	'#', $label_0, '', $label_1, $label_2, $label_3, $label_4, $label_5, $label_6, $label_8, ''
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
		
$contador = count($collection);

$nr_minutos_a 	 = 0;
$nr_minutos_b	 = 0;
$nr_percentual_a = 0;
$nr_percentual_b = 0;
$nr_expediente   = 0;
$nr_meta    	 = 0;

$contador_ano_atual = 0;
$ultimo_mes         = 0;

$body = array();

foreach($collection as $key => $item)
{
	if(trim($item['fl_media']) == 'S')
	{
		$link = '';

		$referencia = 'Resultado de '.$item['ano_referencia'];
	}
	else
	{
		$link = anchor('indicador_plugin/info_indisp/cadastro/'.$item['cd_info_indisp'], '[editar]');

		$referencia = $item['mes_referencia'];
	}
	
	if((intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia'])) && ($item['fl_media'] != 'S'))
	{
		$nr_expediente 	 += $item['nr_expediente'];
		$nr_minutos_a	 += $item['nr_minutos_a'];
		$nr_minutos_b	 += $item['nr_minutos_b'];
		$nr_percentual_a += $item['nr_percentual_a'];
		$nr_percentual_b += $item['nr_percentual_b'];
		$nr_meta    	  = $item['nr_meta'];

		$ultimo_mes = $item['mes_referencia'];

		$contador_ano_atual++;
	}

	$body[] = array(
		$contador--,
		$referencia, 
		indicador_status($item['fl_meta'], $item['fl_direcao']),
		$item['nr_expediente'],
		$item['nr_minutos_a'],
		$item['nr_minutos_b'],
		number_format($item['nr_percentual_a'], 2, ',', '.').'%',
		number_format($item['nr_percentual_b'], 2, ',', '.').'%',
		number_format($item['nr_meta'],2,',','.').'%',
		array(nl2br($item['observacao']), 'text-align:justify'),
		$link 
	);
}
if(intval($contador_ano_atual) > 0)
{
	$media_ano_percentual_a = 0;
	
	if(trim($nr_percentual_a) != '')
	{
		$media_ano_percentual_a = ($nr_percentual_a / $contador_ano_atual);
	}
	
	$media_ano_percentual_b = 0;
	
	if(trim($nr_percentual_b) != '')
	{
		$media_ano_percentual_b = ($nr_percentual_b / $contador_ano_atual);
	}

	$body[] = array(
		0,
		'<b>Resultado de '.intval($tabela[0]['nr_ano_referencia']).'</b>',
		'',
		'<b>'.$nr_expediente.'</b>',
		'<b>'.$nr_minutos_a.'</b>',
		'<b>'.$nr_minutos_b.'</b>',
		'<b>'.number_format($media_ano_percentual_a, 2, ',', '.').' %</b>',
		'<b>'.number_format($media_ano_percentual_b, 2, ',', '.').' %</b>',
		'<b>'.number_format($item['nr_meta'],2,',','.').' %</b>',
		'',
		''
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
?>
<input type="hidden" id="mes_input" name="mes_input" value="<?= $ultimo_mes ?>"/>
<input type="hidden" id="contador_input" name="contador_input" value="<?= $contador_ano_atual ?>"/>

<div style="text-align:center;"> 
	<?= anchor_popup('indicador/apresentacao/detalhe/'.intval($tabela[0]['cd_indicador_tabela']), '[Visualizar Apresentação]', $ar_janela) ?>
</div>

<?= $grid->render() ?>