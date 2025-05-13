<?php
$head = array(
				'Participante',
				'RE', 
				'CPF',
				'Dt Pagamento',
				'Nr-Ano',
				'Valor',
				''
			 );
$body = array();
foreach ($collection as $item)
{
	
    $body[] = array(
		array($item['nome'], 'text-align:left;'),
		$item['cd_empresa'].'/'.$item['cd_registro_empregado'].'/'.$item['seq_dependencia'],
		$item['cpf'],
		$item['dt_pagamento'],
		$item['nr_tit']."-".$item['nr_ano'],
		array(number_format($item['vl_liquido'],2,",","."), 'text-align:right;'),
		'<a href="javascript: void(0)" onclick="addDocResgate($(this),'.$item['cd_empresa'].','.$item['cd_registro_empregado'].','.$item['seq_dependencia'].','.$item['nr_tit'].','.$item['nr_ano'].",'".number_format($item['vl_liquido'],2,",",".")."'".')">Adicionar</a>'
    );
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
$grid->view_count = false;
$grid->id_tabela = "tb_financeiro_resgate_protocolo";
echo $grid->render();
?>