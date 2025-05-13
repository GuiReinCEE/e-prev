<?php
$body = array();

$head = array( 
	'Gerъncia', 
	'Nome', 
	'Escolaridade',
	'Classe - Faixa',
	'Admissуo', 
	'Promoчуo', 
	'Tipo'
);
 	 	
foreach ($collection as $item)
{
    $body[] = array( 
		$item['divisao'],
		array(anchor( "cadastro/matriz/cadastro_colaborador/".$item["codigo"], $item["nome"]),'text-align:left'),
		array($item['nome_escolaridade'],'text-align:left'),
		array($item['classe_nome_familia'],'text-align:left; font-weight:bold; color:'.$item['cor_classe']),
		$item['dt_admissao'],
		$item['dt_promocao'],
		array($item['tipo_promocao'],'text-align:center; font-weight:bold; color:'.$item['cor_tipo_promocao']),
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>