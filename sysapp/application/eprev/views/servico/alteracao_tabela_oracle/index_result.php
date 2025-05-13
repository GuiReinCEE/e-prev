<?php
$head = array( 
	'Tabela',
	'Dt. De Alteraчуo',
	''
);

$body = array();

foreach($collection as $item)
{	
	$body[] = array(
		array($item['tabela'], 'text-align:left;'),
		$item['dt_alteracao'],
		anchor('servico/alteracao_tabela_oracle/salvar/'.$item['id_alteracao'],'[Confirmar]')  
	);
	
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;

echo $grid->render();
?>