<?php
$body = array();
$head = array( 
	"#",$label_0, $label_1, $label_2, $label_3, ''
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

$nr_media = 0;
$nr_meta  = 0;

$referencia = '';

foreach($collection as $key => $item)
{
	/*if(trim($item['fl_media']) == 'S')
	{
		$referencia = 'Resultado de '.intval($item['ano_referencia']);

		$link = '';
	}
	else
	{
		$referencia = $item['mes_ano_referencia'];

		$link = anchor('indicador_pga/avaliacao_prestadores_servico/cadastro/'.$item['cd_avaliacao_prestadores_servico'], '[editar]');
    }*/

	if((intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia'])) && (trim($item['fl_media']) != 'S'))
	{
		$contador_ano_atual++;

		$ultimo_mes = $item['mes_referencia'];

		$nr_media = $item['nr_media'];
		$nr_meta  = $item['nr_meta'];
	}

	$body[] = array(
		$contador--,
		$item['ano_referencia'],
		number_format($item['nr_media'], 2, ',', '.').' %',
		number_format($item['nr_meta'], 2, ',', '.').' %',
		array(nl2br($item['ds_observacao']), 'text-align:left'),
		anchor('indicador_pga/avaliacao_prestadores_servico/cadastro/'.$item['cd_avaliacao_prestadores_servico'], '[editar]')
	);
}

/*if($contador_ano_atual > 0)
{
	$body[] = array(
		0,
		'<b>Resultado de '.intval($tabela[0]['nr_ano_referencia']).'</b>',
		'<b>'.number_format($nr_media, 2, ',', '.').' %</b>',
		'<b>'.number_format($nr_meta, 2, ',', '.').' %</b>',
		'',
		''
	);
}*/

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