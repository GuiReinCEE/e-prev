<?php
$body = array();
$head = array( 
	'Versão',
	'Nome Plano',
	'Dt Inicial',
	'Dt Final',
	''
);

foreach( $collection as $item )
{
	$body[] = array(
	    array(anchor(site_url('ecrm/cadastro_plano_certificado/cadastro/'.$item["cd_plano"]."/".$item['versao_certificado']), $item["versao_certificado"]), "text-align:left;"),
	    array($item["nome_certificado"], "text-align:left;"),
	    $item["dt_inicio"],
		$item["dt_final"],
		'<a href="'.site_url("ecrm/cadastro_plano_certificado/imprimir/".$item['cd_plano']."/".$item['versao_certificado']).'" target="_blank">[imprimir verso]</a>'
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>