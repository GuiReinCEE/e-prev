<?php
$head = array(
	'Nъmero'
    , 'Atendente'
    , 'Status'
	, 'Descriзгo'
	, 'Prazo'
    , 'Teste'
    , 'Conclusгo'
);
$body=array();
foreach( $collection as $item )
{

	$body[] = array(
         $item['numero']
        ,$item['guerra']
        ,$item['status_descricao']
        ,array($item['atividade_titulo'],'text-align:left;')
        ,$item['dt_limite']
        ,$item['dt_limite_testes']
        ,$item['dt_fim_real']
	);

}


$this->load->helper('grid');
$grid = new grid();
$grid->id_tabela = 'tb_result_atendimento';
$grid->head = $head;
$grid->body = $body;

echo $grid->render();
?>