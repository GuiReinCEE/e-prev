<?php
$head = array( 
	'Tipo Documento',
	'Arquivo',
	'Dt. Atualizaчуo',
	'Dt. Divulgaчуo',
	'Dt. Referъncia',
	'Descriчуo',
	''
);

$body = array();

foreach($collection as $item)
{
	$body[] = array(
		array($item['ds_controle_documento_controladoria_tipo'],'text-align:left;'),
		array(anchor(base_url().'up/controle_documento_controladoria/' . $item['arquivo'], $item['arquivo_nome'] , array('target' => '_blank')), "text-align:left;"),
		$item['dt_inclusao'],
		$item['dt_envio'],
		$item['dt_referencia'],
		array(nl2br($item['ds_controle_documento_controladoria']),'text-align:left;'),
		(intval($item['qt_doc']) > 1 ? anchor('gestao/controle_documento_controladoria/documentos/' . $item['cd_controle_documento_controladoria_tipo'], '[anteriores]') : '')
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>