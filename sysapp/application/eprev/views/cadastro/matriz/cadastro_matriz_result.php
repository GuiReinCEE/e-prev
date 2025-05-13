<?php
$body = array();
$head = array(
    'Faixa',
    'Valor Inicial',
	'Valor Final',
    ''
);

foreach( $collection as $item )
{
    $body[] = array(
        $item["faixa"],
        '<input type="text" id="vl_ini_'.$item["faixa"].'" value="'.$item['valor_inicial'].'"/>',
        '<input type="text" id="vl_fim_'.$item["faixa"].'" value="'.$item['valor_final'].'"/>',
        button_save('Salvar', 'salvar("'.$item["faixa"].'")')
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;

echo $grid->render();

?>