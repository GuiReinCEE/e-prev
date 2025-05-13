<?php
$body=array();
$head = array(
    'Empresa'
    , 'Participante'
	, 'Nome'
    , 'Dt Cadastro'
	, 'Dt Ingresso'
	, 'Dt Desligamento'
    , 'Dt Cancela Insc.'
    , 'Forma de Pagamento'
);

foreach( $collection as $item )
{
    $body[] = array(
		$item['ds_empresa'],
        $item['cd_empresa'].'/'.$item['cd_registro_empregado'].'/'.$item['seq_dependencia'],
        array($item['nome'], 'text-align:left'),
        $item['dt_inclusao'],
        $item['dt_ingresso_eletro'],
        $item['dt_desligamento_eletro'],
        $item['dt_cancela_inscricao'],
        $item['forma_pagamento']
	);
}


$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();