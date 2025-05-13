<?php
$body = array();
$head = array( 
	'Número',
	'Atendente',
	'Descrição',
	'Cenário',
	'Pertinência',
	'Data',
	'Dt Implementação',
	'Dt Prevista'
);

foreach( $collection as $item )
{						
	$body[] = array(
		anchor(site_url('atividade/atividade_solicitacao/index/'.$item['area'].'/'.$item['numero']), $item['numero']),
		$item['atendente'],
		array(anchor(site_url('atividade/atividade_solicitacao/index/'.$item['area'].'/'.$item['numero']),nl2br($item['descricao'])),'text-align: left'),
		"<nobr>".anchor("ecrm/informativo_cenario_legal/legislacao/".$item["cd_edicao"]."/".$item["cd_cenario"], "[Ver o Cenário]",array("target" => "_blank"))."</nobr>",
		array('<span class="label '.$item["cor_status"].'">'.wordwrap($item['pertinencia'], 50, "<BR>", false).'</span>', 'text-align:left;'),
		$item['data'],
		$item['dt_implementacao_norma_legal'],
		$item['dt_prevista_implementacao_norma_legal']
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>