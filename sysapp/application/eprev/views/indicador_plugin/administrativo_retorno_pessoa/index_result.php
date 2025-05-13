<?php
$body = array();
$head = array( 
	"#",$label_0, $label_1, $label_2, $label_3, $label_4, $label_5, $label_6, $label_7, $label_8, ''
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

$nr_pessoa_total    = 0;
$nr_receita_total   = 0;
$nr_despesa_total   = 0;
$nr_diferenca_total = 0;
$nr_resultado_total = 0;
$nr_meta_total      = 0;

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

		$link = anchor('indicador_plugin/administrativo_retorno_pessoa/cadastro/'.$item['cd_administrativo_retorno_pessoa'], '[editar]');
	}

	if((intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia'])) && (trim($item['fl_media']) != 'S'))
	{
		$contador_ano_atual++;

		$ultimo_mes = $item['mes_referencia'];

		$nr_pessoa_total    += $item['nr_pessoa'];
		$nr_receita_total   += $item['nr_receita'];
		$nr_despesa_total   += $item['nr_despesa'];
		$nr_diferenca_total += $item['nr_diferenca'];
		$nr_resultado_total += $item['nr_resultado'];
		$nr_meta_total      += $item['nr_meta'];
	}

	$body[] = array(
		$contador--,
		$referencia,
		number_format($item['nr_pessoa'], 2, ',', '.'),
		number_format($item['nr_receita'], 2, ',', '.'),
		number_format($item['nr_despesa'], 2, ',', '.'),
		number_format($item['nr_diferenca'], 2, ',', '.'),
		number_format($item['nr_resultado'], 2, ',', '.'),
		number_format($item['nr_meta'], 2, ',', '.'),
		number_format($item['nr_resultado_percentual'], 2, ',', '.')." %",
		array(nl2br($item['ds_observacao']), 'text-align:left'),
		$link 
	);
}

if($contador_ano_atual > 0)
{
	$nr_resultado_percentual = 0;

	if($nr_resultado_total > 0)
	{
		$nr_resultado_percentual = ($nr_resultado_total / $nr_meta_total) * 100;
	}

	$body[] = array(
		0,
		'<b>Média de '.intval($tabela[0]['nr_ano_referencia']).'</b>',
		'<b>'.number_format($nr_pessoa_total/$contador_ano_atual, 2, ',', '.').'</b>',
		'<b>'.number_format($nr_receita_total/$contador_ano_atual, 2, ',', '.').'</b>',
		'<b>'.number_format($nr_despesa_total/$contador_ano_atual, 2, ',', '.').'</b>',
		'<b>'.number_format($nr_diferenca_total/$contador_ano_atual, 2, ',', '.').'</b>',
		'<b>'.number_format($nr_resultado_total/$contador_ano_atual, 2, ',', '.').'</b>',
		'<b>'.number_format($nr_meta_total/$contador_ano_atual, 2, ',', '.').'</b>',
		'<b>'.number_format($nr_resultado_percentual, 2, ',', '.').' %</b>',
		'',
		''
	);

	$body[] = array(
		0,
		'<b>Acumulado de '.intval($tabela[0]['nr_ano_referencia']).'</b>',
		'<b>'.number_format($nr_pessoa_total/$contador_ano_atual, 2, ',', '.').'</b>',
		'<b>'.number_format($nr_receita_total, 2, ',', '.').'</b>',
		'<b>'.number_format($nr_despesa_total, 2, ',', '.').'</b>',
		'<b>'.number_format($nr_diferenca_total, 2, ',', '.').'</b>',
		'<b>'.number_format($nr_resultado_total, 2, ',', '.').'</b>',
		'<b>'.number_format($nr_meta_total, 2, ',', '.').'</b>',
		'<b>'.number_format($nr_resultado_percentual, 2, ',', '.').' %</b>',
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