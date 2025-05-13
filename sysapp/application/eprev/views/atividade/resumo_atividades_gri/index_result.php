<?php
$head = array( 
	'Mês/Ano'
    , 'Abertas'
    , 'Solicitadas'
	, 'Atendidas no prazo'
	, 'Atendidas fora do prazo'
);

$ano_abertos = 0;
$ano_solicitadas = 0;
$ano_atendidas_no_prazo = 0;
$ano_atendidas_fora_prazo = 0;
$total_atendidas = 0;
$total_programas = 0;
$total_dias = 0;
$body=array();
$ano_ant = $ano-1;
$atendidas_fora_prazo_anterior = $ano_anterior['solicitadas_anterior']-$ano_anterior['atendidas_no_prazo_anterior'];
echo form_start_box( "default_box", "Atividades", true, false, "style='text-align:center;'" );
$body[] = array(
    '<b>Até 12/'.$ano_ant.'</b>'
    ,'<b>'.$ano_anterior['abertas_anterior'].'</b>'
    ,'<b>'.$ano_anterior['solicitadas_anterior'].'</b>'
    ,'<b>'.$ano_anterior['atendidas_no_prazo_anterior'].'</b>'
    ,'<b>'.$atendidas_fora_prazo_anterior.'</b>'
);

foreach( $atividade as $item )
{

	$body[] = array(
        '<a href="#" onclick="carregar_atividades('.$item['mes'].', '.$item['ano'].');">'.$item['mes'].'/'.$item['ano'].'</a>'
        ,$item['abertas']
        ,$item['solicitadas']
        ,$item['atendidas_no_prazo']
        ,$item['atendidas_fora_prazo']
	);

    $ano_abertos += $item['abertas'];
    $ano_solicitadas += $item['solicitadas'];
    $ano_atendidas_no_prazo += $item['atendidas_no_prazo'];
    $ano_atendidas_fora_prazo += $item['atendidas_fora_prazo'];
}

$body[] = array(
        '<b>Total de '. $ano.'</b>'
        ,'<b>'.$ano_abertos.'</b>'
        ,'<b>'.$ano_solicitadas.'</b>'
        ,'<b>'.$ano_atendidas_no_prazo.'</b>'
        ,'<b>'.$ano_atendidas_fora_prazo.'</b>'
	);

$acum_abertos = $ano_abertos + $ano_anterior['abertas_anterior'];
$acum_solicitados = $ano_solicitadas + $ano_anterior['solicitadas_anterior'];
$acum_atendidas_no_prazo = $ano_atendidas_no_prazo + $ano_anterior['atendidas_no_prazo_anterior'];
$acum_atendidas_fora_prazo = $ano_atendidas_fora_prazo + $atendidas_fora_prazo_anterior;

$body[] = array(
        '<b>Total acumulado até '. $ano.'</b>'
        ,'<b>'.$acum_abertos.'</b>'
        ,'<b>'.$acum_solicitados.'</b>'
        ,'<b>'.$acum_atendidas_no_prazo.'</b>'
        ,'<b>'.$acum_atendidas_fora_prazo.'</b>'
	);

$this->load->helper('grid');
$grid = new grid();
$grid->id_tabela = 'tb_atividades';
$grid->head = $head;
$grid->body = $body;

echo $grid->render();
echo '<div id="result_atividade"></div>';
echo form_end_box("default_box");

$body=array();
$head=array();

$head = array(
	'Mês'
    , 'Gerência'
    , 'Quantidade'
	, 'Percentual'
);

foreach( $atendimento as $item )
{

	$body[] = array(
        $item['mes']
        ,$item['divisao']
        ,array($item["total_mes_divisao"],'text-align:center;','int')
        ,number_format($item['percentual'],2, ',', '.')
	);

}
echo form_start_box( "default_box", "Atividades Atendidas por Mês x Gerência", true, false, "style='text-align:center;'" );

$this->load->helper('grid');
$grid = new grid();
$grid->id_tabela = 'tb_atendimento';
$grid->head = $head;
$grid->body = $body;

echo $grid->render();
echo form_end_box("default_box");

$body=array();
$head=array();

$head = array(
	'Programa'
    , 'Atividades'
    , 'Dias'
);

foreach( $programa as $item )
{

	$body[] = array(
         $item['programa']
        ,array(number_format($item["quantidade"],2, ',', '.'),'text-align:center;','float')
        ,array(number_format($item["dias"],2, ',', '.'),'text-align:center;','float')
	);
}

echo form_start_box( "default_box", "Atividades Atendidas por Programa", true, false, "style='text-align:center;'" );

$this->load->helper('grid');
$grid = new grid();
$grid->id_tabela = 'tb_programas';
$grid->head = $head;
$grid->body = $body;

echo $grid->render();
echo form_end_box("default_box");