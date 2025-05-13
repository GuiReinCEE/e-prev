<?php
$body = array();

$head = array( 
	"Data", 
	"Contato", 
	"Usuário", 
	"" 
);

foreach( $collection as $item )
{
	$body[] = array( 
		$item["dt_contato"], 
		array($item["ds_contato"], 'text-align:left;'), 
		array($item["nome_usuario"], 'text-align:left;'), 
		'<a href="javascript:void(0)" onclick="excluir_contato('.$item["cd_pessoa_contato"].')">[excluir]</a>'
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
$grid->view_count=FALSE;
echo $grid->render();
?>