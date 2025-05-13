<?php
$body=array();
$head = array( 
	'Versão', 'Site', '', '', 'Url'
);

foreach( $collection as $item )
{
	$body[] = array(
			$item["cd_site"] . " - " . $item["cd_versao"],
			array($item["tit_capa"], 'text-align:left;'),
			anchor("ecrm/site/detalhe/".$item["cd_site"]."/".$item["cd_versao"], '[Editar home]'),
			anchor("ecrm/conteudo_site/index/".$item["cd_site"]."/".$item["cd_versao"],'[Editar páginas]'),
			array($item["endereco"], 'text-align:left;')
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
