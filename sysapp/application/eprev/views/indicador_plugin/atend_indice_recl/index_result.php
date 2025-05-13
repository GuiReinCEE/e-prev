<?php
$body=array();
$head = array( 
	'#', $label_0, $label_1, $label_2, $label_3, $label_4, $label_6, $label_8, $label_14, $label_16, ''
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
$media_ano_reclamacoes= array();
$media_reclamacoes    = 0;
$media                = 0;
$ultimo_mes           = 0;
$nr_total_reclamacoes = 0;
$nr_procede           = 0;
$nr_nao_procede       = 0;
$nr_abertas           = 0;

foreach($collection as $item)
{
	$a_data = explode('/', $item['mes_referencia']);
	
	if(trim($item['fl_media']) == 'S')
	{
		$link = '';

		$referencia = 'Resultado de ' . $item['ano_referencia'];
	}
	else
	{
		$link = anchor('indicador_plugin/atend_indice_recl/cadastro/' . $item['cd_atend_indice_recl'], 'editar');

		$referencia = $item['mes_referencia'];
	}
	
	if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) && trim($item['fl_media']) != 'S')
	{
		$contador_ano_atual++;
		$ultimo_mes              = $item['mes_referencia'];
		$media_ano_reclamacoes[] = $item['nr_percentual_reclamacoes'];
		$media_ano[]             = $item['nr_total_reclamacoes'];	
		$nr_total_reclamacoes    += $item['nr_total_reclamacoes'];
		$nr_procede              += $item['nr_procede'];
		$nr_nao_procede          += $item['nr_nao_procede'];
		$nr_abertas              += $item['nr_abertas'];
	}
		
	$body[] = array(
		$contador--,
		$referencia,
		(trim($item['nr_total_participantes'])    != '' ? intval($item['nr_total_participantes']) : ''),
		(trim($item['nr_total_reclamacoes'])      != '' ? intval($item['nr_total_reclamacoes'])   : ''),
        (trim($item['nr_procede'])                != '' ? intval($item['nr_procede'])             : ''),
		(trim($item['nr_nao_procede'])            != '' ? intval($item['nr_nao_procede'])         : ''),
        (trim($item['nr_abertas'])                != '' ? intval($item['nr_abertas'])             : ''),
		(trim($item['nr_percentual_reclamacoes']) != '' ? number_format($item['nr_percentual_reclamacoes'], 2, ',', '.').' %' : ''),
		number_format($item['nr_meta'], 2, ',', '.').' %',
		array(nl2br($item['observacao']), 'text-align:justify'), 
		$link 
	);
}

if(sizeof($media_ano) >0)
{	
	foreach($media_ano_reclamacoes as $valor)
	{
		$media_reclamacoes += $valor;
	}

	$media = ($media_reclamacoes / sizeof($media_ano_reclamacoes));
	
	$body[] = array(
		0, 
		'<b>Resultado de '.intval($tabela[0]['nr_ano_referencia']).'</b>', 
		$item['nr_total_participantes'], 
		$nr_total_reclamacoes, 
		$nr_procede, 
		$nr_nao_procede, 
        $nr_abertas, 
		'<big><b>'.app_decimal_para_php(number_format($media , 2, ',', '.')).' %</b></big>',
		number_format($item['nr_meta'], 2, ',', '.').' %',
		'', 
		''
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body; ?>
<input type='hidden' id='ultimo_mes' name='ultimo_mes' value='<?= $ultimo_mes ?>'/>
<input type='hidden' id='contador' name='contador' value='<?= $contador_ano_atual ?>'/>
<br/>
<div style='text-align:center;'> 
	<?= anchor_popup('indicador/apresentacao/detalhe/'.intval($tabela[0]['cd_indicador_tabela']), '[Visualizar Apresentação]', $ar_janela)?>
</div>
<br/>
<?= $grid->render(); ?>