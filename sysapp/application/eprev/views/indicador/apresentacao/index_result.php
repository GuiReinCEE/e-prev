<?php
$body=array();

$head = array(
	'Grupo',
	'Tipo',
	'Ord.',
	'Indicador',
	'Período',
	'Processo',
	'Controle',
	'Dt Limite Atualizar',
	'Responsável',
	'Dt Atualização',	
	'PE',
	'PODER',
	'',
	'',
	'',
	'Dt Encerrado'
);

$ar_win_apresenta = array(
              'width'      => '700',
              'height'     => '500',
              'scrollbars' => 'yes',
              'status'     => 'yes',
              'resizable'  => 'yes',
              'screenx'    => '0',
              'screeny'    => '0'
            );
$ar_win_ppt = array(
              'width'      => '10',
              'height'     => '10',
              'scrollbars' => 'no',
              'status'     => 'yes',
              'resizable'  => 'no',
              'screenx'    => '0',
              'screeny'    => '0'
            );			
			

foreach( $collection as $item )
{
	$body[] = array(
		$item['ds_indicador_grupo'],
		$item['ds_tipo'],
		intval($item['nr_ordem']), 
		array(anchor_popup('indicador/apresentacao/detalhe/' . $item['cd_indicador_tabela'], $item['ds_indicador'], $ar_win_apresenta),'text-align:left;'),
		$item['nr_ano_referencia'], 
		array($item['ds_processo'],'text-align:left;'),
		$item['ds_indicador_controle'], 
		'<span class="label '.$item['status_atualizar'].'">'.$item["dt_limite_atualizar"].'</span>',
		array("- ".$item['responsavel'].br()."- ".$item['substituto'],'text-align:left;'),
		$item['dt_atualizacao'],	 
		$item['igp'],
		$item['poder'],
		anchor_popup('indicador/apresentacao/detalhe/' . $item['cd_indicador_tabela'], '[Visualizar]', $ar_win_apresenta), 
		#anchor_popup('indicador/grafico_config/geraPPT/' . $item['cd_indicador_tabela'], '[PPT]', $ar_win_ppt), 
		anchor_popup('indicador/grafico_config/geraEXCEL/' . $item['cd_indicador_tabela'], '[EXCEL]', $ar_win_ppt), 
		($this->session->userdata('codigo') == 170 ? $item['cd_indicador'].' - '.$item['cd_indicador_tabela'] : ''),
		$item['dt_exclusao']
	);
}


$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>