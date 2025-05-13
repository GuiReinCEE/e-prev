<?php
$body=array();
$head = array( 
	'Cód.',  
	'Nome', 
	'Dt Libera',
	'Ordem',
	'URL', 
	'Imagem'
);

foreach($collection as $item)
{
	$body[] = array(
	    anchor("ecrm/site_parceiro/detalhe/".$item["cd_site_parceiro"], $item["cd_site_parceiro"]),
	    array(anchor("ecrm/site_parceiro/detalhe/" . $item["cd_site_parceiro"], $item["nome"]),'text-align:left;'),
	    $item["dt_libera"],
	    $item["nr_ordem"],
		array(anchor($item["url"], $item["url"],array('target' => 'blank')),'text-align:left;'),
		(trim($item['img_parceiro']) != "" ? '<img src="../../../../../eletroceee/img/site_parceiro/'.$item['img_parceiro'].'" border="0">' : "")
	);
}
$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>