<?php
$body = array();
$head = array( 
	"#",$label_0, $label_1, $label_2, $label_3, $label_4, $label_5, $label_6, $label_7, $label_8, $label_9, ''
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

$nr_recurso_garantidor = 0;
$nr_limite             = 0;
$nr_deducao_limite     = 0;
$nr_limite_efetivo     = 0;
$nr_custeio_adm        = 0;
$nr_efetivo_custeio    = 0;
$nr_efetivo_garantidor = 0;
$nr_custeio_recurso    = 0;

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

		$link = anchor('indicador_pga/limite_taxa_adm/cadastro/'.$item['cd_limite_taxa_adm'], '[editar]');
	}

	if((intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia'])) && (trim($item['fl_media']) != 'S'))
	{
		$contador_ano_atual++;

		$ultimo_mes = $item['mes_referencia'];

		$nr_recurso_garantidor = $item['nr_recurso_garantidor'];
		$nr_limite             = $item['nr_limite'];
		$nr_deducao_limite     = $item['nr_deducao_limite'];
		$nr_limite_efetivo     = $item['nr_limite_efetivo'];
		$nr_custeio_adm        = $item['nr_custeio_adm'];
		$nr_efetivo_custeio    = $item['nr_efetivo_custeio'];
		$nr_efetivo_garantidor = $item['nr_efetivo_garantidor'];
		$nr_custeio_recurso    = $item['nr_custeio_recurso'];
	}

	$body[] = array(
		$contador--,
		$referencia,
		number_format($item['nr_recurso_garantidor'], 2, ',', '.'),
		number_format($item['nr_limite'], 2, ',', '.'),
		number_format($item['nr_deducao_limite'], 2, ',', '.'),
		number_format($item['nr_limite_efetivo'], 2, ',', '.'),
		number_format($item['nr_custeio_adm'], 2, ',', '.'),
		number_format($item['nr_efetivo_custeio'], 2, ',', '.'),
		number_format($item['nr_efetivo_garantidor'], 2, ',', '.').' %',
		number_format($item['nr_custeio_recurso'], 2, ',', '.').' %',
		array(nl2br($item['ds_observacao']), 'text-align:left'),
		$link 
	);
}

if($contador_ano_atual > 0)
{
	$body[] = array(
		0,
		'<b>Resultado de '.intval($tabela[0]['nr_ano_referencia']).'</b>',
		'<b>'.number_format($nr_recurso_garantidor, 2, ',', '.').'</b>',
		'<b>'.number_format($nr_limite, 2, ',', '.').'</b>',
		'<b>'.number_format($nr_deducao_limite, 2, ',', '.').'</b>',
		'<b>'.number_format($nr_limite_efetivo, 2, ',', '.').'</b>',
		'<b>'.number_format($nr_custeio_adm, 2, ',', '.').'</b>',
		'<b>'.number_format($nr_efetivo_custeio, 2, ',', '.').'</b>',
		'<b>'.number_format($nr_efetivo_garantidor, 2, ',', '.').' %</b>',
		'<b>'.number_format($nr_custeio_recurso, 2, ',', '.').' %</b>',
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