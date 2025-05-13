<?php
$body = array();
$head = array(
	'Doc',
	'Tipo de documento', 
	'Quantidade'
);

foreach ($collection as $item)
{
    $body[] = array(
		$item['cd_tipo_doc'],
		array(anchor("ecrm/cadastro_protocolo_interno/relatorio/" . $item["cd_tipo_doc"], $item["nome_documento"]), 'text-align:left'),
		array($item["total"],'text-align:right;','int')
    );
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();

?>