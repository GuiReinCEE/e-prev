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
	$excluir = '<a href="javascript:void(0);" onclick="excluir('.$item['cd_tarefa_anexo'].')">[excluir]</a>';
	
    $body[] = array(
		$item['cd_tarefa_anexo'],
		$item['dt_inclusao'],
		array(anchor(base_url().'up/tarefa_anexo/' . $item['arquivo'], $item['arquivo_nome'] , array('target' => "_blank")), "text-align:left;"),
		$item['nome'],
		 ($fl_excluir ? $excluir : '')
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>