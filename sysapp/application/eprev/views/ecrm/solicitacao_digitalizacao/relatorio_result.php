<?php
$this->load->helper('grid');

$head = array( 
	'Mês',
	array('Nr. Solicitações', 'width:200px;')
);

$body = array();

$total = 0;

foreach($collection_mes as $item)
{
	$total += intval($item['soma']);

	$body[] = array(
		array(mes_extenso(intval($item['mes'])), 'text-align:left'),
		'<label class="badge badge-info">'.$item['soma'].'</label>'
	);		
}

$body[] = array(
	array('<label class="label label-inverse">TOTAL</label>', 'text-align:left'),
	'<label class="badge badge-inverse">'.$total.'</label>'
);	

$grid_mes = new grid();
$grid_mes->head = $head;
$grid_mes->body = $body;
$grid_mes->view_count = false;

$head = array( 
	'Gerência',
	array('Nr. Solicitações', 'width:200px;')
);

$body = array();

$total = 0;

foreach($collection_gerencia as $item)
{
	$total += intval($item['soma']);

	$body[] = array(
		array($item['gerencia'], 'text-align:left'),
		'<label class="badge badge-info">'.$item['soma'].'</label>'
	);		
}

$body[] = array(
	array('<label class="label label-inverse">TOTAL</label>', 'text-align:left'),
	'<label class="badge badge-inverse">'.$total.'</label>'
);	

$grid_gerencia = new grid();
$grid_gerencia->head = $head;
$grid_gerencia->body = $body;
$grid_gerencia->view_count = false;

$head = array( 
	'Usuário',
	array('Nr. Solicitações', 'width:200px;')
);

$body = array();

$total = 0;

foreach($collection_usuario as $item)
{
	$total += intval($item['soma']);

	$body[] = array(
		array($item['cd_usuario_responsavel'], 'text-align:left'),
		'<label class="badge badge-info">'.$item['soma'].'</label>'
	);		
}

$body[] = array(
	array('<label class="label label-inverse">TOTAL</label>', 'text-align:left'),
	'<label class="badge badge-inverse">'.$total.'</label>'
);	

$grid_usuario = new grid();
$grid_usuario->head = $head;
$grid_usuario->body = $body;
$grid_usuario->view_count = false;

echo br();
echo $grid_mes->render();
echo $grid_gerencia->render();
echo $grid_usuario->render();
?>
