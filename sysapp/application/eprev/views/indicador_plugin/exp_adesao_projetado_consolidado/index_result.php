<?php
$body = array();
$head = array( 
	'#', $label_0, $label_5, $label_1, $label_2, $label_3, $label_4, ''
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

$contador_ano_atual   = 0;
$contador             = sizeof($collection);
$a_data               = array(0, 0);
$nr_resultado         = 0;
$ultimo_mes          = 0;

foreach($collection as $item)
{
	$a_data = explode('/', $item['mes_ano_referencia']);
	
	if(trim($item['fl_media']) == 'S')
	{
		$link = '';

		$referencia = ' Resultado de '.$item['ano_referencia'];
	}
	else
	{
		$link = anchor('indicador_plugin/exp_adesao_projetado_consolidado/cadastro/'.$item['cd_exp_adesao_projetado_consolidado'], 'editar');

		$referencia = $item['mes_ano_referencia'];
	}
	
	if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) && trim($item['fl_media']) != 'S')
	{
		$contador_ano_atual++;
		$ultimo_mes         = $item['mes_ano_referencia'];
		$nr_resultado       = $item['nr_resultado'];
	}
	
	$body[] = array(
		$contador--,
		$referencia,
		number_format($item['nr_meta_ano'], 0, ',', '.'),
		number_format($item['nr_meta'], 0, ',', '.'),
		(trim($item['nr_resultado']) != '' ? number_format($item['nr_resultado'], 0, ',', '.') : ''),		
		number_format($item['nr_percentual_f'], 2, ',', '.').'%',
		array(nl2br($item['ds_observacao']), 'text-align:left'), 
		$link 
	);
}

if($contador_ano_atual > 0)
{
	$body[] = array(
		0, 
		'<b>Resultado de '.intval($tabela[0]['nr_ano_referencia']).'</b>', 
		'<b>'.number_format($item['nr_meta_ano'], 0, ',', '.').' </b>',
		'<b>'.number_format($item['nr_meta'], 0, ',', '.').' </b>',
		'<b>'.number_format($item['nr_resultado'], 0, ',', '.').'</b>',		
		'<b>'.number_format($item['nr_percentual_f'], 2, ',', '.').'%</b>',
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
<br/>
<div style="text-align:center;"> 
	<?= anchor_popup('indicador/apresentacao/detalhe/'.intval($tabela[0]['cd_indicador_tabela']), '[Visualizar Apresentação]', $ar_janela) ?>
</div>
<br/>
<?= $grid->render(); ?>
