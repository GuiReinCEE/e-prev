<?php
$body = array();
$head = array( 
	'#', $label_0, '', $label_7, $label_1, $label_2, $label_3, $label_4, $label_5, $label_6,  ''
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

$ar_ultimo = Array();


$nr_quantidade = 0;
$nr_resgate = 0;
$nr_captacao = 0;
$nr_resultado = 0;
$pr_resultado = 0;
$nr_meta = 0;

foreach($collection as $key => $item)
{
	$a_data     = explode("/", $item['mes_referencia']);
	
	if(trim($item['fl_media']) == 'S')
	{
		$referencia = 'Resultado de '.intval($item['ano_referencia']);

		$link = '';
	}
	else
	{
		$contador_ano_atual++;
		$referencia = $item['mes_ano_referencia'];

		$link = anchor('indicador_plugin/exp_captacao_liquida/cadastro/'.$item['cd_exp_captacao_liquida'], '[editar]');

		$nr_quantidade += $item['nr_quantidade'];
		$nr_resgate += $item['nr_resgate'];
		$nr_captacao += $item['nr_captacao'];
		$nr_meta = $item['nr_meta'];
	}


	$body[] = array(
		$contador--,
		$referencia,
		indicador_status($item["fl_meta"], $item["fl_direcao"]),
		number_format($item['nr_quantidade'], 0, ',', '.'),
		number_format($item['nr_resgate'], 2, ',', '.'),
		number_format($item['nr_captacao'], 2, ',', '.'),
		number_format($item['nr_resultado'], 2, ',', '.'),
		number_format($item['pr_resultado'], 2, ',', '.'),
		number_format($item['nr_meta'], 2, ',', '.'),
		array(nl2br($item['observacao']), 'text-align:justify'), 
		$link 
	);
	
	$ar_ultimo = $item;
}

if ($contador_ano_atual > 0)
{
	$nr_resultado = $nr_captacao - $nr_resgate;
    $pr_resultado = (($nr_resultado / $nr_resgate) * 100);    

	$body[] = array(
		0,
		'<b>Total de '.intval($tabela[0]['nr_ano_referencia']).'</b>',
		indicador_status($ar_ultimo["fl_meta"], $ar_ultimo["fl_direcao"]),
		number_format($nr_quantidade, 0, ',', '.'),
		number_format($nr_resgate, 2, ',', '.'),
		number_format($nr_captacao, 2, ',', '.'),
		number_format($nr_resultado, 2, ',', '.'),
		number_format($pr_resultado, 2, ',', '.'),
		number_format($nr_meta, 2, ',', '.'),
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

<input type="hidden" id="ultimo_mes" name="ultimo_mes" value="<?= $a_data[0] ?>"/>
<input type="hidden" id="contador" name="contador" value="<?= $contador_ano_atual ?>"/>
<div style="text-align:center;"> 
	<?= anchor_popup('indicador/apresentacao/detalhe/'.intval($tabela[0]['cd_indicador_tabela']), '[Visualizar Apresentação]', $ar_janela) ?>
</div>
<br/>
<?= $grid->render() ?>