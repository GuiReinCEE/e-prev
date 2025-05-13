<?php
$body = array();
$head = array( 
	'#',$label_0, $label_1, $label_2, $label_3, $label_4, $label_6, ''
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
$nr_percentual_f      = 0;
$ultimo_mes           = 0;
$link = '';
$referencia = '';
foreach($collection as $item)
{
	$link = anchor("indicador_plugin/administrativo_aval_desempenho/cadastro/" . $item["cd_administrativo_aval_desempenho"], "editar");	

	$referencia = $item['periodo_ini']."/".$item['periodo_fim'];	
	
	if($item['nr_percentual_f'] == '')
	{
		if(intval($item['nr_valor_1']) > 0 )
		{
			$nr_percentual_f = ($item['nr_valor_2']/$item['nr_valor_1']);
		}						
	}
	else
	{
		$nr_percentual_f = $item['nr_percentual_f'];
	}
	
	$body[] = array(
		$contador--,
		$referencia,
		(trim($item['nr_valor_1']) != '' ? number_format($item['nr_valor_1'], 2, ',', '.') : ''),
		(trim($item['nr_valor_2']) != '' ? number_format($item['nr_valor_2'], 0, ',', '.') : ''),
		(trim($nr_percentual_f) != '' ? number_format($nr_percentual_f, 2, ',', '.') : ''),
		number_format($item['nr_meta'], 2, ',', '.'),
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