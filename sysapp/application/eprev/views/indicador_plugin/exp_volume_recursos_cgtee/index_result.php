<?php
$body = array();
$head = array( 
	'#', $label_0, '', $label_1, $label_2, $label_3, $label_4, $label_5,  ''
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

$nr_contratado_total = 0;
$nr_meta_total       = 0;

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

		$link = anchor('indicador_plugin/exp_volume_recursos_cgtee/cadastro/'.$item['cd_exp_volume_recursos_cgtee'], '[editar]');
	}

	if((intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia'])) && ($item['fl_media'] != 'S'))
	{
		$contador_ano_atual++;

		$ultimo_mes = $item['mes_referencia'];
		
		$nr_contratado_total += $item['nr_contratado'];
		$nr_meta_total       += $item['nr_meta'];
	}

	$body[] = array(
		$contador--,
		$referencia,
		indicador_status($item["fl_meta"], $item["fl_direcao"]),
		number_format($item['nr_contratado'], 2, ',', '.'),
		($contador_ano_atual > 0 ? number_format($nr_contratado_total, 2, ',', '.') : ''),
		number_format($item['nr_meta'], 2, ',', '.'),
		($contador_ano_atual > 0 ? number_format($nr_meta_total, 2, ',', '.') : ''),
		array(nl2br($item['observacao']), 'text-align:justify'), 
		$link 
	);
}

if($contador_ano_atual > 0)
{
	$body[] = array(
		0, 
		'<b>Total de '.intval($tabela[0]['nr_ano_referencia']).'</b>', 
		'', 
		'<b>'.number_format($nr_contratado_total, 2, ',', '.').'</b>',
		'',
		'<b>'.number_format($nr_meta_total, 2, ',', '.').'</b>',
		'',
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