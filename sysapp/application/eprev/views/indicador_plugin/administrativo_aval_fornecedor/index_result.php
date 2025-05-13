<?php
$body = array();
$head = array( 
	'#', $label_0, $label_1, $label_2, $label_3, ''
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
$media_ano          = array();
$ultimo_mes         = 0;

$nr_meta = 0;

foreach($collection as $item)
{
	if(trim($item['fl_media']) == 'S')
	{
		$referencia = 'Resultado de '.intval($item['ano_referencia']);

		$link = '';
	}
	else
	{
		$referencia = $item['mes_ano_referencia'];

		$link = anchor('indicador_plugin/administrativo_aval_fornecedor/cadastro/'.$item['cd_administrativo_aval_fornecedor'], '[editar]');
	}

	if((intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia'])) && ($item['fl_media'] != 'S'))
	{
		$contador_ano_atual++;

		$ultimo_mes = $item['mes_referencia'];

		$media_ano[] = $item['nr_percentual_f'];

		$nr_meta = $item['nr_meta'];
	}
	
	$body[] = array(
		$contador--,
		$referencia,
		number_format($item['nr_percentual_f'], 2, ',', '.').' %',
		number_format($item['nr_meta'], 2, ',', '.').'%',
		array(nl2br($item['observacao']), 'text-align:"left"'), 
		$link 
	);
}

if(sizeof($media_ano) >0)
{
	$media = 0;

	foreach($media_ano as $valor)
	{
		$media += $valor;
	}
	
	$body[] = array(
		0, 
		'<b>Resultado de '.intval($tabela[0]['nr_ano_referencia']).'</b>', 
		'<b>'.number_format(($media / sizeof($media_ano)), 2, ',', '.' ).'%'.'</b>', 
		'<b>'.number_format($nr_meta, 2, ',', '.' ).'%'.'</b>', 
		'', 
		''
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
?>

<input type="hidden" id="ultimo_mes" name="ultimo_mes" value="<?= $ultimo_mes ?>"/>
<input type="hidden" id="contador" name="contador" value="<?= $contador_ano_atual ?>"/>
<div style="text-align:center;"> 
	<?= anchor_popup('indicador/apresentacao/detalhe/'.intval($tabela[0]['cd_indicador_tabela']), '[Visualizar Apresentação]', $ar_janela) ?>
</div>
<br/>
<?= $grid->render() ?>