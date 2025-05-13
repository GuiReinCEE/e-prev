<?php
$body = array();
$head = array(
  'Anexo',
  'Dt Inclusão',
  ''
);

foreach ($collection as $item)
{            
    $body[] = array(
	    anchor('http://'.$_SERVER['SERVER_NAME'].'/eletroceee/app/escritorio_juridico/up/'.$item['arquivo'], $item['arquivo_nome'] , array('target' => "_blank")),
		$item['dt_inclusao'],	
		(trim($item['dt_confirma_beneficio']) == '' ? '<a href="javascript:void(0);" onclick="excluir_anexo('.$item['cd_pagamento_cheque_anexo'].')">[excluir]</a>' : '')
    );
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;

echo $grid->render();
?>