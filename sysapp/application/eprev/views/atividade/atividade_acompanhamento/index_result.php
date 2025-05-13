<?php
$body=array();
$head = array( 
	'Acompanhamento',
	'Dt Inclusão',
	'Usuário',
	''
);

foreach( $collection as $item )
{
	$fl_excluir = 'N';
	
	if($cd_usuario == $item['cd_usuario_inclusao'] AND $item['fl_excluir'] == 'S')
	{
		$fl_excluir = 'S';
	}
	else
	{
		$fl_excluir == 'N';
	}
	
	$excluir = '<a href="javascript:void(0);" onclick="excluir('.$item['cd_atividade_acompanhamento'].')">[excluir]</a>';
	
    $body[] = array(
		array(nl2br($item['ds_atividade_acompanhamento']), 'text-align:justify;'),
		$item['dt_inclusao'],
		$item['nome'],
		($fl_excluir == 'S' ? $excluir : '')
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>