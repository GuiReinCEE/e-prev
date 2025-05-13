<?php
$body=array();
$head = array( 
	'Ano', 'Ger�ncia', 'Colaborador', 'Compet�ncia', 'Resultado esperado', 'A��es de apoio'
);

foreach( $collection as $item )
{
    $body[] = array(
         $item['dt_periodo'],
         $item['divisao'],
         $item['nome'],
         array($item['aspecto'], 'text-align:left'),
         array($item['resultado_esperado'], 'text-align:justify'),
         array($item['acao'], 'text-align:left'),
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>