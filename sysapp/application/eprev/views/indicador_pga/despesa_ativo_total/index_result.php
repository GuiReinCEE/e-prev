<?php
$body = array();
$head = array( 
	"#",$label_0, $label_1, $label_2, $label_3, $label_4, $label_6, $label_5, ''
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

$nr_valor_1_total = 0;
$nr_valor_2_total = 0;
$nr_meta          = 0;
$nr_meta_max      = 0;

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

		$link = anchor('indicador_pga/despesa_ativo_total/cadastro/'.$item['cd_despesa_ativo_total'], '[editar]');
	}

	if((intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia'])) && (trim($item['fl_media']) != 'S'))
	{
		$contador_ano_atual++;

		$ultimo_mes = $item['mes_referencia'];

		$nr_valor_1_total = $item['nr_valor_1'];
		$nr_valor_2_total = $item['nr_valor_2'];
		$nr_meta          = $item['nr_meta'];
		$nr_meta_max      = $item['nr_meta_max'];
	}

	$body[] = array(
		$contador--,
		$referencia,
		number_format($item['nr_valor_1'], 2, ',', '.'),
		number_format($item['nr_valor_2'], 2, ',', '.'),
		number_format($item['nr_resultado'], 2, ',', '.').'%',
		number_format($item['nr_meta'], 2, ',', '.').'%',
		number_format($item['nr_meta_max'], 2, ',', '.').'%',
		array(nl2br($item['observacao']), 'text-align:left'),
		$link 
	);
}

if($contador_ano_atual > 0)
{
	$nr_resultado = 0;

	if(floatval($nr_valor_2_total) > 0)
	{
		$nr_resultado = (floatval($nr_valor_1_total) / floatval($nr_valor_2_total)) * 100;
	}

	$body[] = array(
		0,
		'<b>Resultado de '.intval($tabela[0]['nr_ano_referencia']).'</b>',
		'<b>'.number_format($nr_valor_1_total, 2, ',', '.').'</b>',
		'<b>'.number_format($nr_valor_2_total, 2, ',', '.').'</b>',
		'<b>'.number_format($nr_resultado, 2, ',', '.').'%</b>',
		'<b>'.number_format($nr_meta, 2, ',', '.').'%</b>',
		'<b>'.number_format($nr_meta_max, 2, ',', '.').'%</b>',
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
<br/>
<div style="text-align:center;"> 
	<?= anchor_popup('indicador/apresentacao/detalhe/'.intval($tabela[0]['cd_indicador_tabela']), '[Visualizar Apresentação]', $ar_janela) ?>
</div>
<br/>
<?= $grid->render() ?>