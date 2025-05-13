<?php
$body=array();
$head = array( 
	'Código',
	'Nome',
	'Envio'
);

foreach( $collection as $item )
{
	$body[] = array(
		$item["cd_prevenir_formulario"],
		array(anchor(site_url('ecrm/prevenir_formulario/formulario/'.$item["cd_prevenir_formulario"]), (gerencia_in(array('GRI', 'AAA', 'DE')) ? $item['ds_nome'] : md5($item["ds_nome"]))), "text-align:left;" ),
		$item["dt_envio"]
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>