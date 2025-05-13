<?php
$body = array();
$head = array( 
	'Código',
	'Evento',
	'Sistema',
	'Tipo'
);

foreach( $collection as $item )
{
	$link = anchor(site_url('gestao/evento/cadastro/'.$item["cd_evento"]), $item["nome"]); 

	$tipo = '';
	
	if(trim($item["tipo"]) == 'T')
	{
		$tipo = 'Temporal';
	}
	else if(trim($item["tipo"]) == 'E')
	{
		$tipo = 'Externo';
	}
	
	$body[] = array(
		$item["cd_evento"], 
		array($link, "text-align:left"), 
		$item["nome_projeto"], 
		$tipo
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>