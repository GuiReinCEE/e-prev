<?php
$body = array();
$head = array(
	'Ano/Número',
	'Gerência Destino',
	'Grupo Destino',
	'Status',
	'Quantidade',
	'Qt Recebido',
	'Qt Recusado',
	'',
	'Dt Cadastro',
	'Dt Envio',
	'Usuário Envio',
	'Dt Recebido',
	'Usuário Recebeu'
);
	
foreach ($collection as $item)
{	
	$ano_numero = $item['ano_numero'];

	if(trim($this->session->userdata('divisao')) == 'GFC')
	{
		if(trim($item['cd_gerencia_destino']) != 'GFC' AND trim($item['dt_recebido']) == '')
		{
			$ano_numero = anchor('ecrm/correspondencia_recebida/cadastro/'.$item['cd_correspondencia_recebida'], $item['ano_numero']);
		}
		else
		{
			$ano_numero = anchor('ecrm/correspondencia_recebida/receber/'.$item['cd_correspondencia_recebida'], $item['ano_numero']);
		}
		/*
		if(trim($item['cd_gerencia_destino']) == 'GGS' AND trim($item['dt_recebido']) == '')
		{
			$ano_numero = anchor('ecrm/correspondencia_recebida/receber/'.$item['cd_correspondencia_recebida'], $item['ano_numero']);
		}
		else
		{
			$ano_numero = anchor('ecrm/correspondencia_recebida/cadastro/'.$item['cd_correspondencia_recebida'], $item['ano_numero']);
		}
		*/
	}
	else
	{
		if(trim($item['usuario_envio']) != '')
		{
			$ano_numero = anchor('ecrm/correspondencia_recebida/receber/'.$item['cd_correspondencia_recebida'], $item['ano_numero']);
		}
	}
	
	$valor = 0;

	if(intval($item["tl_itens"]) > 0)
	{
		$valor = (100/intval($item["tl_itens"])) * intval($item["tl_recebido_recusado"]);
	}
	
	$body[] = array(
		$ano_numero,
		$item['cd_gerencia_destino'],
		$item['grupo'],
		'<span class="label '.trim($item['class_status']).'">'.trim($item['status']).'</span>',
		$item['tl_itens'],
		$item['tl_recebido'],
		$item['tl_recusado'],
		array(progressbar($valor, "pb_".$item['cd_correspondencia_recebida']),"text-align:left;"),
		$item['dt_inclusao'],
		$item['dt_envio'],
		array($item['usuario_envio'],'text-align:left'),
		$item['dt_recebido'],
		array($item['usuario_recebido'],'text-align:left')
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>