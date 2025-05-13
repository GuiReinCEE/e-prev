<?php
$body = array();
$head = array( 
	'#', $label_0, $label_1, $label_2, $label_3, $label_4, ''
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

$nr_valor_1  = 0;
$nr_validada = 0;
$nr_meta     = 0;

$referencia = '';

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

		$link = anchor('indicador_plugin/administrativo_acoes_preventivas/cadastro/'.$item['cd_administrativo_acoes_preventivas'], '[editar]');
	}

	if((intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia'])) && ($item['fl_media'] != 'S'))
	{
		$contador_ano_atual++;

		$ultimo_mes = $item['mes_referencia'];

		$nr_valor_1  = $item['nr_valor_1'];
		$nr_validada = $item['nr_validada'];
		$nr_meta     = $item['nr_meta'];
	}

	$body[] = array(
		$contador--,
		$referencia,
		intval($item['nr_valor_1']),
		intval($item['nr_validada']),
		intval($item['nr_meta']),
		array(nl2br($item['observacao']), 'text-align:justify'),
		$link 
	);
}

if(intval($contador_ano_atual) > 0)
{
	$body[] = array(
		0,
		'<b>Resultado de '.intval($tabela[0]['nr_ano_referencia']).'</b>',
		intval($nr_valor_1),
		intval($nr_validada),
		'<b>'.intval($nr_meta).'</b>',
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