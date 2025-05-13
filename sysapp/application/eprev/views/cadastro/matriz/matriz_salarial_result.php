<?php
$body = array();
$head = array( 
	'Classe', 
	'A',
	'B',
	'C',
	'D', 
	'E', 
	'F', 
	'G', 
	'H', 
	'I', 
	'J', 
	'K', 
	'L'
);

for($i=0; $i < count($collection); $i++)
{
    $body[$i][] = anchor( "cadastro/matriz/cadastro_matriz/".$collection[$i]["value"], $collection[$i]["text"]);
        
    for($j=0; $j < count($collection[$i]['matriz']); $j++)
    {
        $body[$i][] = number_format($collection[$i]['matriz'][$j]["valor_inicial"],2,",",".").' / '.number_format($collection[$i]['matriz'][$j]["valor_final"],2,",",".");
    }
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>