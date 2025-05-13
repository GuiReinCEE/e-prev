<?php
$body = array();
$head = array(
  'Ano/Nъmero',
  'Status',
  'CPF',
  'RE',
  'Nome Reclamante',
  'Nr Processo',
  'Dt Solicitaзгo',
  'Solicitado'
  
);

foreach ($collection as $item)
{            
    $body[] = array(
		anchor(site_url('atividade/calculo_irrf/cadastro/'.$item['cd_calculo_irrf']), $item['nr_ano_numero']),
		array($item['status'], 'text-align:center; font-weight:bold; color: '.$item['cor'].';'),
		$item['cpf'],
		(trim($item['cd_registro_empregado']) != '' ? $item['cd_empresa'].'/'.$item['cd_registro_empregado'].'/'.$item['seq_dependencia'] : ''),
		array($item['nome'], 'text-align:left;'),
		$item['nr_processo'],
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