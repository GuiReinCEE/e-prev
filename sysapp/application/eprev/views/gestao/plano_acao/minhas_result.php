<?php
$head = array(
	'Ano/Mês',
	'Processo',
	'Situação',
	'Nº Item',
	'Dt. Prazo',
	'Responsável',
	'Constatação',
	'Recomendação',
	'Ação',
	'Status',
	'Acompanhamento'
);

$body = array();

foreach($collection as $item)
{
	$body[] = array(
		anchor('gestao/plano_acao/responder/'.$item['cd_plano_acao'].'/'.$item['cd_plano_acao_item'],$item['ds_ano_numero']),
		$item['procedimento'],
		array(nl2br($item['ds_situacao']), 'text-align:justify;'),
		$item['nr_plano_acao_item'],
		$item['dt_prazo'],
		$item['cd_gerencia_responsavel'],
		array(nl2br($item['ds_constatacao']), 'text-align:justify;'),
		array(nl2br(implode(br(),$item['ds_recomendacao'])), 'text-align:justify;'),
		array(nl2br($item['ds_acao']), 'text-align:justify;'),
		(trim($item['ds_status']) != '' ? '<label class="label label-'.$item['ds_class'].'">'.$item['ds_status'].'</label>' : '<label class="label label-important">Não Iniciada</label>'),
		array(nl2br($item['ds_acompanhamento']), 'text-align:justify;')
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>