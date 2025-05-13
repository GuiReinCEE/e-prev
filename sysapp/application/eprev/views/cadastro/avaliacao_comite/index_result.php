<?php
$body = array();
$head = array( 
    'Cód.',
	'Nome',
	'Tipo',
	'Status',
	'Comitê',
	''
);
	 	 
foreach( $collection as $item )
{
	$comite              = '';
	$cd_avaliacao_comite = 0;
	
	foreach($item['arr_comite'] as $item2)
	{
		if(trim($item2['fl_responsavel']) == "S")
		{
			$cd_avaliacao_comite = $item2['cd_avaliacao_comite'];
			
			$comite .= '<span class="label label-info">'.$item2['nome'].'</span>'.br();
        }                         
		else
		{
			$comite .= $item2['nome'].br();
		}
	}
	
	if(trim($item['avaliador']) != '')
	{
		if(trim($item['avaliador_responsavel_comite']) == "S")
		{
			$comite .= '<span class="label label-info">'.$item['avaliador'].' (superior)</span>';
		}
		else
		{
			$comite .= $item['avaliador'].' (superior)';
		}
	}
	
	$encaminhar   = '';
	$enviar_email = '';
	
	if((trim($item['tl_responsavel']) > 0) AND (trim($item['fl_status']) == 'E'))
	{
		$encaminhar = (trim($item['tl_responsavel']) > 0 ? '<a href="javascript:void(0);" onclick="encaminhar('.intval($item['cd_avaliacao_capa']).')">[encaminhar]</a>' : '');
	}
	
	if(trim($item['fl_status']) == 'S')
	{
		$enviar_email = '<a href="javascript:void(0);" onclick="enviar_email('.intval($item['cd_avaliacao_capa']).')">[enviar email comitê]</a>';
	}
	
	$body[] = array(
		$item['cd_avaliacao_capa'],
		array(anchor("cadastro/avaliacao_comite/cadastro/".$item["cd_avaliacao_capa"], $item["nome"]), 'text-align:left'),
		'<span class="'.trim($item['cor_tipo_promocao']).'">'.$item['tipo_promocao'].'</span>',
		'<span class="'.trim($item['cor_status']).'">'.$item['status'].'</span>',
		array($comite, 'text-align:left'),
		$encaminhar.' '.$enviar_email
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;

echo $grid->render();
?>