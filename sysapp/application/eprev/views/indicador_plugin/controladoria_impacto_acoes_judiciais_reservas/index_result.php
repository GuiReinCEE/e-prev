<?php
$body = array();
$head = array( 
	'#',$label_0, $label_1, $label_2, $label_3, $label_4, $label_5, ''
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
$media_ano            = array();
$nr_meta              = 0;
$media                = 0;
$nr_meta_media        = 0;
$ultimo_mes			  = 0;

foreach($collection as $item)
{
	$a_data     = explode("/", $item['mes_referencia']);
	$observacao = $item["observacao"];
	
	if(trim($item['fl_media']) == 'S')
	{
		$link = '';

		$referencia = "Resultado de " . $item['ano_referencia'];
	}
	else
	{
		$link = anchor("indicador_plugin/controladoria_impacto_acoes_judiciais_reservas/cadastro/" . $item["cd_controladoria_impacto_acoes_judiciais_reservas"], "editar");

		$referencia = $item['ano_referencia'];
	}
	
	if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) && trim($item['fl_media']) != 'S')
	{
		$contador_ano_atual++;
		$ultimo_mes = $item['mes_referencia'];
	}
	
	$body[] = array(
		$contador--,
		$referencia,
		(trim($item['nr_valor_1']) != '' ? number_format($item['nr_valor_1'], 2, ',', '.') : ''),
		(trim($item['nr_valor_2']) != '' ? number_format($item['nr_valor_2'], 2, ',', '.') : ''),
		(trim($item['nr_resultado']) != '' ? number_format($item['nr_resultado'], 2, ',', '.').' %' : ''),
		number_format($item['nr_meta'], 2, ',', '.').' %',
		array(nl2br($item['observacao']), 'text-align:left'), 
		$link 
	);
}
$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body; ?>
<input type="hidden" id="ultimo_mes" name="ultimo_mes" value="<?= $ultimo_mes ?>"/>
<input type="hidden" id="contador" name="contador" value="<?= $contador_ano_atual ?>"/>
<br/>
<div style="text-align:center;"> 
	<?= anchor_popup('indicador/apresentacao/detalhe/'.intval($tabela[0]['cd_indicador_tabela']), '[Visualizar Apresentação]', $ar_janela) ?>
</div>
<br/>
<?= $grid->render(); ?>