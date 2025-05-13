<?php
$head = array( 
	'Tipo de Reunião',
	'Processos'
);

$body=array();

foreach($collection as $item)
{
	$body[] = array(
		array(nl2br(anchor('gestao/reuniao_sistema_gestao_tipo/cadastro/'.$item['cd_reuniao_sistema_gestao_tipo'],($item['ds_reuniao_sistema_gestao_tipo']))),'text-align:left;'),
		array((count($item['processo']) > 0 ? implode(br(), $item['processo']) : ''), 'text-align:left')
	);
}
$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>