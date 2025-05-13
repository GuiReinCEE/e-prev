<?php
$body = array();
$head = array( 
	'#', $label_0, $label_1, $label_2, $label_3, $label_6, $label_7, $label_8, $label_4, $label_5, ''
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

$nr_pessoal_total         = 0;
$nr_telefonico_total      = 0;
$nr_email_total		      = 0;
$nr_total_total		      = 0;
$nr_correspondencia_total = 0;
$nr_virtual_total         = 0;
$nr_whatsapp_total         = 0;

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

		$link = anchor('indicador_plugin/atend_numero_atendimentos/cadastro/'.$item['cd_atend_numero_atendimentos'], '[editar]');
	}

	if((intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia'])) && ($item['fl_media'] != 'S'))
	{
		$contador_ano_atual++;

		$ultimo_mes = $item['mes_referencia'];

		$nr_pessoal_total         += $item['nr_pessoal'];
		$nr_telefonico_total      += $item['nr_telefonico'];
		$nr_email_total		      += $item['nr_email'];
		$nr_correspondencia_total += $item['nr_correspondencia'];
		$nr_virtual_total         += $item['nr_virtual'];
		$nr_whatsapp_total        += $item['nr_whatsapp'];
		$nr_total_total		      += $item['nr_total'];
	}

	$body[] = array(
		$contador--,
		$referencia,
		number_format($item['nr_pessoal'], 0, ',' ,'.'),
		number_format($item['nr_telefonico'], 0, ',' ,'.'),
		number_format($item['nr_email'], 0, ',' ,'.'),
		number_format($item['nr_correspondencia'], 0, ',' ,'.'),
		number_format($item['nr_virtual'], 0, ',' ,'.'),
		number_format($item['nr_whatsapp'], 0, ',' ,'.'),
		number_format($item['nr_total'], 0, ',' ,'.'),
		array(nl2br($item['observacao']), 'text-align:justify'),
		$link 
	);
}

if(intval($contador_ano_atual) > 0)
{
	$body[] = array(
		0,
		'<b>Resultado de '.intval($tabela[0]['nr_ano_referencia']).'</b>',
		'<b>'.number_format($nr_pessoal_total, 0, ',', '.').'</b>',
		'<b>'.number_format($nr_telefonico_total, 0, ',', '.').'</b>',
		'<b>'.number_format($nr_email_total, 0, ',', '.').'</b>',
		'<b>'.number_format($nr_correspondencia_total, 0, ',', '.').'</b>',
		'<b>'.number_format($nr_virtual_total, 0, ',', '.').'</b>',
		'<b>'.number_format($nr_whatsapp_total, 0, ',', '.').'</b>',
		'<b>'.number_format($nr_total_total, 0, ',', '.').'</b>',
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