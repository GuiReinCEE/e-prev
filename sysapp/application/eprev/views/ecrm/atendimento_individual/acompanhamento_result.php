<?php
$body=array();
$head = array( 
	'Descrição',
	'Dt Inclusão',
	'Usuário',
	''
);

foreach( $collection as $item )
{	
    $body[] = array(
		array($item['ds_atendimento_individual_acompanhamento'], 'text-align:justify'),
		$item['dt_inclusao'],
		$item['nome'],
		'<a href="javascript:void(0);" onclick="excluir_acompahamento('.$item['cd_atendimento_individual_acompanhamento'].')">[excluir]</a>'
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>