<?php
$body = array();
$head = array( 
	'#', $label_0, $label_1, /*[INSS]$label_2,*/ $label_3, $label_4, $label_5, $label_6, /*[RAIS]$label_12,*/ $label_13, $label_14, $label_15, $label_16, $label_17, $label_18, $label_19, $label_7, $label_8, $label_9, $label_10, $label_11, ''
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

$obrigacoes_previstas_total = 0;
$obrigacoes_cumpridas_total = 0;

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

		$link = anchor('indicador_plugin/controladoria_obrigacoes_legais/cadastro/'.$item['cd_controladoria_obrigacoes_legais'], '[editar]');
	}

	if((intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia'])) && ($item['fl_media'] != 'S'))
	{
		$contador_ano_atual++;

		$ultimo_mes = $item['mes_referencia'];
		
		$obrigacoes_previstas_total += $item['nr_obr_previstas'];
        $obrigacoes_cumpridas_total += $item['nr_obr_cumpridas'];
		$nr_meta                    = $item['nr_meta'];
	}

	$body[] = array(
		$contador--,
		$referencia,
		$item['fgts'],
		//$item['inss'],
		$item['balancete'],
		$item['demostracoes'],
		$item['dctf'],
		$item['di'],
		//$item['raiz'],
		$item['dirf'],
		$item['caged'],
		$item['nr_tce'],		
		$item['nr_decweb'],
		$item['nr_efd_contribuicoes'],
		$item['nr_e_financeira'],
		$item['nr_efd_reinf'],
		intval($item['nr_obr_previstas']),
		intval($item['nr_obr_cumpridas']),
		number_format($item['nr_resultado'], 2, ',' ,'.').' %',
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
		'',
		'',
		'',
		'',
		'',
		'',
		'',
		'',
		'',
		'',
		'',
		'',
		'<b>'.intval($obrigacoes_previstas_total).'</b>',
		'<b>'.intval($obrigacoes_cumpridas_total).'</b>',
		'<b>'.($obrigacoes_previstas_total > 0 ? number_format((($obrigacoes_cumpridas_total/$obrigacoes_previstas_total) * 100), 2, ',' ,'.') : 0).' %</b>',
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