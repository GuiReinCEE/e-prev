<?php
$body = array();
$head = array( 
	'Dt Inclusão',
	'Arquivo',
	'Usuário',
	''
);

foreach( $collection as $item )
{	
    $body[] = array(
		$item['dt_inclusao'],
		array(anchor(base_url().'up/registro_operacional/'.$item['arquivo'], $item['arquivo_nome'] , array('target' => "_blank")), "text-align:left;"),
		$item['nome'],
		'<a href="javascript:void(0);" onclick="excluir_registro_operacional_anexo('.$item['cd_acompanhamento_registro_operacional_anexo'].')">[excluir]</a>'
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>