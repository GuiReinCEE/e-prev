<?php
$body=array();
$head = array( 
	'Cód.',
	'Nome',
	'CPF',
	'Dt Inscrição',
	'Dt Cadastro GAP',
	'Dt Ingresso'
);
foreach( $collection as $item )
{
	$body[] = array(
		$item["cd_interessado"],
		array($item["nome"],"text-align:left;"),
		$item["cpf"],
		'<span class="label">'.$item["dt_inscricao"].'</span>',
		'<span class="label label-warning">'.$item["dt_inclusao_gap"].'</span>',
		'<span class="label label-success">'.$item["dt_ingresso_eletro"].'</span>'
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>