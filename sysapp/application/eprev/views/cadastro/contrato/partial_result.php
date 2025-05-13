<?php
$body = array();
$head = array(
	'Cód',
	'Cód Eletro',
	'Empresa',
	'Serviço',
	'Status',
	'Ren. Automática',
	'Avaliar',
	'Qt Avaliador',
	'Valor',
	'Pagamento',
	'Dt Início',
	'Dt Encerramento',
	'Dt Reajuste',
	'Gerência'
);

foreach ($collection as $item)
{	
	$link = anchor('cadastro/contrato/cadastro/'.$item['cd_contrato'], $item['ds_empresa']);
	
	$body[] = array(
		$item['cd_contrato'],
		$item['cd_contrato_eletro'],
		array($link, 'text-align:left;'),
		array($item['ds_servico'], 'text-align:left;'),
		$item['status_contrato'],
		$item['id_renovacao_automatica'],
		$item['fl_avaliar'],
		$item['qt_avaliador'],
		array($item['ds_valor'], 'text-align:right;'),
		$item['ds_contrato_pagamento'],
		$item['dt_inicio'],
		$item['dt_encerramento'],
		$item['dt_reajuste'],
		$item['cd_divisao']
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>