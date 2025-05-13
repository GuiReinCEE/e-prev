<?php
$body = array();
$head = array( 
	'Código',
	'Dt Inclusão',
	'Arquivo',
	'Usuário',
	''
);

foreach( $collection as $item )
{	
    $body[] = array(
		$item['cd_relatorio_auditoria_contabil_anexo'],
		$item['dt_inclusao'],
		array(anchor(base_url().'up/relatorio_auditoria_contabil/'.$item['arquivo'], $item['arquivo_nome'], array('target' => "_blank")), "text-align:left;"),
		$item['nome'],
		(trim($item['dt_encaminhamento']) == '' ? '<a href="javascript:void(0);" onclick="excluir('.$item['cd_relatorio_auditoria_contabil_anexo'].')">[excluir]</a>' : '')
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;

echo $grid->render();
?>