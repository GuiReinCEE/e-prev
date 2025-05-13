<?php
$body=array();

$head = array(
	'Cуdigo ', 'Reclamaзгo'
);

foreach($reclamacao as $item)
{
	$body[] = array(
        $item["cd_reclamacao"],
        array($item["texto_reclamacao"],'text-align:left;'),
	);
}


echo form_start_box( "default_box", "Reclamaзгo" );
$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
echo form_end_box("default_box");

?>