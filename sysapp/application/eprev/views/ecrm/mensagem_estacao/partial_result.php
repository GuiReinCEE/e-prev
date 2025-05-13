<?php
$body=array();
$head = array( 
	'Nome',
	'Arquivo',
	'Dt Início',
	'Dt Final',
	'Usuário'
);

foreach( $collection as $item )
{
	$link = anchor( site_url("ecrm/mensagem_estacao/cadastro/" . $item["cd_mensagem_estacao"]), $item["nome"] );

	$arquivo = anchor( site_url("ecrm/mensagem_estacao/cadastro/" . $item["cd_mensagem_estacao"]), '<img border="0" src="'.base_url().'/up/mensagem_estacao/'.$item["arquivo"].'" width="100px" /></a>' );

	$body[] = array(
		array($link,'text-align:left;'), 
		$arquivo, 
		$item["dt_inicio"],
		$item["dt_final"], 		
		$item["usuario"]
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>