<?php
$body=array();
$head=array( 
	'RE',
	'CPF',	
	'Nome',
	''
);

foreach( $collection as $item )
{
	$body[] = array(
		$item["cd_registro_empregado"],
		$item["cpf"],		
		array($item["nome"],"text-align:left;"),
		'<input type="button" value="Excluir" class="btn btn-mini btn-danger" onclick="itemExcluir('.$item["cd_comprovante_irpf_colaborador"].','.$item["cd_registro_empregado"].')">'
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;

echo $grid->render();
?>