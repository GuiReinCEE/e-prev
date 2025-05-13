<?php
$body=array();
$head = array(
    'Dt Inclusão',
    'Protocolo',
	'#'
);

foreach($collection as $item )
{
    $body[] = array(
		$item["dt_inclusao"],
		$item["id_doc"],
		anchor("https://www.fcprev.com.br/fundacaofamilia/index.php/assinatura_documento/index/".$item["id_doc"], "[consultar situação]", array('target' => "_blank")),
    );
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->view_count = false;
$grid->body = $body;
echo $grid->render();