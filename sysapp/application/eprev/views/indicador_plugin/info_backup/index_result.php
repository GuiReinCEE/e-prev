<?php
$head = array( 
	'#', $label_0, '', $label_1, $label_2, $label_3, $label_4, $label_6, ''
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

$contador = count($collection);

$nr_percentual = 0;
$nr_meta       = 0;

$contador_ano_atual = 0;
$ultimo_mes         = 0;

$body = array();

foreach($collection as $key => $item)
{
	if(trim($item['fl_media']) == 'S')
	{
		$link = '';

		$referencia = 'Resultado de '.$item['ano_referencia'];
	}
	else
	{
		$link = anchor('indicador_plugin/info_backup/cadastro/'.$item['cd_info_backup'], '[editar]'); 

		$referencia = $item['mes_referencia'];
	}
	
	if((intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia'])) && ($item['fl_media'] != 'S'))
	{
		$nr_percentual += $item['nr_percentual'];
		$nr_meta       = $item['nr_meta'];

		$ultimo_mes = $item['mes_referencia'];

		$contador_ano_atual++;
	}

	$body[] = array(
		$contador--,
		$referencia,
		indicador_status($item['fl_meta'], $item['fl_direcao']),
		(trim($item['nr_soma']) != '' ? number_format($item['nr_soma'], 2, ',', '.') : ''),
		(trim($item['nr_processo']) != '' ? intval($item['nr_processo']) : ''),
		number_format($item['nr_percentual'], 2, ',', '.').' %',
		number_format($item['nr_meta'], 2, ',', '.').' %',
		array(nl2br($item['observacao']), 'text-align:justify'),
		$link 
	);
}

if(intval($contador_ano_atual) > 0)
{
	$nr_resultado = 0;

	if(intval($nr_percentual) > 0)
	{
		$nr_resultado = ($nr_percentual / $contador_ano_atual);
	}

	$body[] = array(
		 0,
		 '<b>Resultado de '.intval($tabela[0]['nr_ano_referencia']).'</b>',
		 '',
		 '',
		 '',
		 '<b>'.number_format($nr_resultado,2,',','.').'%'.'</b>',
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