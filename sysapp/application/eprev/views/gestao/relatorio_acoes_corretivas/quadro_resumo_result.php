<?php
echo form_start_box( "default_box", "Quadro Resumo", true, false, "style='text-align:center;'" );
$head = array(
	'Nуo Conformidade'
    , 'Quantidade'
);

$body=array();

$row = $conformidade[0];
$body[] = array(
    array('Nуo Implementada com Prazo Futuro','style="text-align:left;"')
    ,$row['qt_nao_implementada_prazo']
);

$body[] = array(
     array('Nуo Implementada com Prazo Vencido','style="text-align:left;"')
    ,$row['qt_nao_implementada_fora']
);

$body[] = array(
     array('Implementada no Prazo','style="text-align:left;"')
    ,$row['qt_implementada_prazo']
);

$body[] = array(
     array('Implementada Fora do Prazo','style="text-align:left;"')
    ,$row['qt_implementada_fora']
);

$body[] = array(
     array('Total Aberta','style="text-align:left;"')
    ,$row['qt_aberta']
);


$this->load->helper('grid');
$grid = new grid();
$grid->id_tabela = 'tb_quadro_resumo';
$grid->head = $head;
$grid->body = $body;
echo $grid->render();

$head=array();

$body=array();

$head = array(
	'Aчуo Corretiva '
    , 'Quantidade'
);

$row = $corretiva[0];
$body[] = array(
    array('Nуo Apresentada com Prazo Futuro','style="text-align:left;"')
    ,$row['qt_ac_nao_apresentada_prazo']
);

$body[] = array(
     array('Nуo Apresentada com Prazo Vencido','style="text-align:left;"')
    ,$row['qt_ac_nao_apresentada_fora']
);

$body[] = array(
     array('Apresentada no Prazo','style="text-align:left;"')
    ,$row['qt_ac_apresentada_prazo']
);

$body[] = array(
     array('Apresentada Fora do Prazo','style="text-align:left;"')
    ,$row['qt_ac_apresentada_fora']
);

$body[] = array(
     array('Total','style="text-align:left;"')
    ,$row['qt_ac_total']
);

$this->load->helper('grid');
$grid = new grid();
$grid->id_tabela = 'tb_quadro_resumo';
$grid->head = $head;
$grid->body = $body;
echo $grid->render();

echo form_end_box("default_box");


?>