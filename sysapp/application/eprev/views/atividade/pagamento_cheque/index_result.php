<?php
$body = array();
$head = array(
  'Ano/Nъmero',
  'Status',
  'CPF',
  'RE',
  'Nome Reclamante',
  'Nr Processo',
  'Dt Depуsito',
  'Valor das Custas',
  'Dt Solicitaзгo',
  'Solicitado'
  
);

foreach ($collection as $item)
{            
    $body[] = array(
		anchor(site_url('atividade/pagamento_cheque/cadastro/'.$item['cd_pagamento_cheque']), $item['nr_ano_numero']),
		array($item['status'], 'text-align:center; font-weight:bold; color: '.$item['cor'].';'),
		$item['cpf'],
		(trim($item['cd_registro_empregado']) != '' ? $item['cd_empresa'].'/'.$item['cd_registro_empregado'].'/'.$item['seq_dependencia'] : ''),
		array($item['nome'], 'text-align:left;'),
		$item['nr_processo'],
		$item['dt_deposito'],
		'R$ '.number_format($item['vl_custo'],2,',','.'),
		$item['dt_envio'],
		array($item['solicitado'], 'text-align:left;')
    );
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;

echo $grid->render();
?>