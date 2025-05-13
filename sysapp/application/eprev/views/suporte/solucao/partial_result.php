<?php
$body = array();
$head = array(
	'Atividade',
	'Dt Cadastro',
	'Dt Conclusão',
	'Categoria',
	'Assunto',
	'Descrição',
	'Solução'
);

foreach ($collection as $item)
{
	$body[] = array(
		anchor(site_url('atividade/atividade_solicitacao/index/GI/'.$item['numero']), $item['numero'], array("target"=>"_blank")),
		$item['dt_cad'],
		$item['dt_fim_real'],
		$item['categoria'],
		$item['assunto'],
		array(str_replace("\n", "<br />", $item['atividade']),'text-align:justify'),
		array(str_replace("\n", "<br />", $item['solucao'] ),'text-align:justify')
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>