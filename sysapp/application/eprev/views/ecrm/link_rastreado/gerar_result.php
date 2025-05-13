<?php
$body=array();
$head = array( 
	'Cуdigo',
	'Descriзгo',
	'',
	'Url Rastreada',
	'Url',
	'RE',
	'Participante',
	'Dt Inclusгo'
);

foreach( $collection as $item )
{
	$re = '';
	
	if(trim($item['cd_empresa']) != '' AND trim($item['cd_registro_empregado']) != '' AND trim($item['seq_dependencia']) != '')
	{
		$re = $item['cd_empresa'].'/'.$item['cd_registro_empregado'].'/'.$item['seq_dependencia'];
	}
				
	$body[] = array(
		$item["cd_link"],
		array($item["ds_divulgacao_link"],'text-align:left'),
		anchor( site_url('ecrm/link_rastreado/index/'. $item['cd_link']), '[relatуrio]'),
		array(anchor( $item["ds_url"], $item["ds_url"], array('target' => '_blank')) ,'text-align:left'),
		array(anchor( $item["link"], $item["link"], array('target' => '_blank')) ,'text-align:left'),
		$re,
		array($item['nome'],'text-align:left'),
		$item['dt_inclusao']
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>