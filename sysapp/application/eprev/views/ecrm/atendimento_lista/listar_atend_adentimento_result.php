<?php
$body=array();

$head = array(
	'Nome Tela ', 'Hora acesso', 'Programa'
);

foreach($atendimento as $item)
{
	$body[] = array(
        array($item["tela"],'text-align:left;'),
        array($item["hr_hora"],'text-align:left;'),
        array($item["tp_tela"],'text-align:left;')
	);
}

echo form_start_box( "default_box", "Informações do atendimento" );
$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
echo form_end_box("default_box");

?>