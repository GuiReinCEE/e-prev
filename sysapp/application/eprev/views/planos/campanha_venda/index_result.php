<?php
$body = array();
$head = array( 
	'Cуd',
	'Empresa',
	'Descriзгo',
	'',
	'Dt Inнcio',
	'Dt Fim',			
	'Dt Cadastro',			
	'Dt Ingresso',			
	'Qt CPF',
	
	'Qt Contato',
	'Qt С Encontrado',
	'Qt Em Negociaзгo',
	'Qt Proposta',
	'Qt Inscrito',
	'Qt Ingresso'
);

foreach($collection as $item)
{
	$url = "";
	if(in_array(intval($item["cd_empresa"]), array(8, 10, 11, 12, 14, 19, 20, 24, 25, 26, 27, 28, 29, 30, 31)))
	{
		$url = anchor("planos/campanha_venda/familia", "[pъblico]");
	}
	
	$body[] = array(
		anchor("planos/campanha_venda/cadastro/".$item["cd_campanha_venda"], $item["cd_campanha_venda"]),
		array(anchor("planos/campanha_venda/cadastro/".$item["cd_campanha_venda"], $item["empresa"]), 'text-align:left;'),
		array(anchor("planos/campanha_venda/cadastro/".$item["cd_campanha_venda"], $item["ds_campanha_venda"]), 'text-align:left;'),
		$url,
		$item['dt_inicio'],
		$item['dt_final'],						
		$item['dt_cadastro'],					
		$item['dt_ingresso'],					
		array($item['qt_cpf'],'text-align:center;','int'),

		array($item['qt_contato'],'text-align:center;','int'),
		array($item['qt_nao_encontrado'],'text-align:center;','int'),
		array($item['qt_em_negociacao'],'text-align:center;','int'),
		array($item['qt_proposta'],'text-align:center;','int'),
		array($item['qt_inscrito'],'text-align:center;','int'),												
		array($item['qt_ingresso'],'text-align:center;','int')
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>