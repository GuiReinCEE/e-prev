<?php
$body=array();
$head = array(
    'Dt Acompanhamento',
    'Descrição',
    'Usuário',
	''
);

foreach($collection as $item )
{
    $excluir = '<a href="javascript:void(0);" onclick="excluir('.$item['cd_atas_cci_acompanhamento'].')">[excluir]</a>';
    $editar = '<a href="javascript:void(0);" onclick="editar('.$item['cd_atas_cci_acompanhamento'].');" >[editar]</a>';
    
	$body[] = array(
		$item["dt_inclusao"],
		array(nl2br($item["descricao"]), "text-align:justify;"),
		$item['nome'],
		$editar.' '.$excluir
    );
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;

echo $grid->render();

?>