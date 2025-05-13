<?php
$body=array();
$head = array( 
	'Código',
	'Dt Inclusão',
	'Arquivo',
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
	
	$excluir = '<a href="javascript:void(0);" onclick="excluir('.$item['cd_atividade_anexo'].')">[excluir]</a>';
	
    $body[] = array(
		$item['cd_atividade_anexo'],
		$item['dt_inclusao'],
		array(anchor(base_url().'up/atividade_anexo/' . $item['arquivo'], $item['arquivo_nome'] , array('target' => "_blank")), "text-align:left;"),
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