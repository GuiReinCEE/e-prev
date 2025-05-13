<?php
$body=array();

$head = array(
	'Nъmero ', 'Descriзгo', 'Responsбvel', 'Aзгo', 'Retorno'
);

foreach($sugestao as $item)
{
	$body[] = array(
        array(anchor("ecrm/reclamacao/cadastro/".$item['numero']."/".$item['ano']."/".$item['tipo'], $item['cd_reclamacao']),'style=font-weight:bold'),
        array($item["descricao"],'text-align:left;'),
        array($item["ds_usuario_responsavel"],'text-align:left;'),
        array($item["ds_acao"],'text-align:left;'),
        array($item["dt_retorno"],'text-align:left;'),
	);
}

echo form_start_box( "default_box", "Reclamaзгo Sugestгo" );
$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
echo form_end_box("default_box");

?>