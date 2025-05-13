<?php

$body = array();
$head = array(
	'Cуdigo',
	'Descriзгo',
	'Descartar',
	'Gerкncia',
	'Dt Cadastro',
	'Usuбrio'
);

foreach ($collection as $item)
{ 
    $body[] = array(
		anchor('ecrm/documento_protocolo_descarte/cadastro/'.$item['cd_documento'].'/'.$item['cd_divisao'], $item['cd_documento']),
		array(anchor('ecrm/documento_protocolo_descarte/cadastro/'.$item['cd_documento'].'/'.$item['cd_divisao'], $item['nome_documento']),'text-align:left'),
		array($item['descarte'],'text-align:center; font-weight:bold; color: '.(trim($item['descarte']) == 'Sim' ? 'red' : 'green')),
		$item['cd_divisao'],
		$item['dt_inclusao'],
		$item['nome']
    );
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>