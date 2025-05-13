<?php
$head = array( 
	'Código',
	'Dt Email',
	'Dt Envio',
	'Assunto',
	'Para',
	'Cc',
	'Cco',
	'Evento',
	'Divulgação'
);

$body = array();

foreach($collection as $item)
{
	$body[] = array(
		array(anchor('ecrm/reenvio_email/index/'.$item['cd_email'], $item['cd_email'], 'target="_blank"'),'text-align:left;'),
		$item['dt_email'],
		$item['dt_envio'],	
		array($item['assunto'],'text-align:left;'),
		array($item['para'],'text-align:left;'),
		array($item['cc'],'text-align:left;'),
		array($item['cco'],'text-align:left;'),
		array($item['evento'],'text-align:left;'),
		array($item['divulgacao'],'text-align:left;')		
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;

echo $grid->render();
?>