<?php
$body=array();

$head = array(
	'Código ', 'Encaminhamento', 'Status'
);

foreach($encaminhamento as $item)
{
	$body[] = array(
        array(anchor("ecrm/encaminhamento/detalhe/".$item['cd_atendimento']."/".$item['cd_encaminhamento'], $item['cd_encaminhamento']),'style=font-weight:bold'),
        array($item["texto_encaminhamento"],'text-align:left;'),
        $item['fl_encaminhamento']
	);
}


echo form_start_box( "default_box", "Encaminhamento" );
$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
echo form_end_box("default_box");

?>