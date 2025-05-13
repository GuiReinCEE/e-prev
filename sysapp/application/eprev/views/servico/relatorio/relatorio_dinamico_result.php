<?php
$body=array();
$head = array( 
	'Título',
	'Dt Inclusão',
	'Gerência',
	'',
	'',
	'',
	''
);

foreach( $collection as $item )
{
	$body[] = array(
		array(anchor("servico/relatorio/cadastro/".$item["cd_relatorio"], $item["titulo"]), "text-align:left;"),
		$item["dt_criacao"],
		$item["divisao"],
		anchor("servico/relatorio/gerar_pdf/".$item["cd_relatorio"], 'PDF'),
		anchor("servico/relatorio/gerar_documento/".$item["cd_relatorio"]."/txt", 'TXT'),
		anchor("servico/relatorio/gerar_documento/".$item["cd_relatorio"]."/csv", 'EXCEL'),
		'<a href="javascript:void(0);" onclick="excluir_relatorio('.$item['cd_relatorio'].')">[excluir]</a>'
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>