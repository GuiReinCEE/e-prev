<?php
$body = array();
$head = array(
	'Ano/Número',
	'Gerência Destino',
	'Grupo Destino',
	'Status',
	'Dt Envio',
	'Usuário Envio',
	'Dt Recebido',
	'Usuário Recebeu',
	'Dt Correspondência',
	'Origem',
	'Tipo',
	'Identificador',
	'RE',
	'Nome',
	'Dt Recusado',
	'Motivo'
);

foreach ($collection as $item)
{

	$body[] = array( 
		anchor('ecrm/correspondencia_recebida/receber/'.$item['cd_correspondencia_recebida'], $item['ano_numero']),
		$item['cd_gerencia_destino'],
		$item['grupo'],
		'<span class="label '.trim($item['class_status']).'">'.trim($item['status']).'</span>',
		$item['dt_envio'],
		array($item['usuario_envio'],'text-align:left;'),
		$item['dt_recebido'],
		array($item['usuario_recebido'],'text-align:left;'),
		$item['dt_correspondencia'],
		array($item['origem'],'text-align:left;'),
		$item['ds_correspondencia_recebida_tipo'],
		array($item['identificador'],'text-align:left;'),
		$item['re'],
		array($item['nome'],'text-align:left;'),
		$item['dt_recusa'],
		(trim($item['dt_recusa']) != '' ? 'Por: '.$item['usuario_recusa'].'<br/> Motivo: '.$item['motivo_recusa'] : '')
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>