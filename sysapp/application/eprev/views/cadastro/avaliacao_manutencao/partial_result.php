<?php
$body = array();
$head = array(
    'Cód', 
    'Gerência', 
	'Nome do Avaliado',
	'Nome do Avaliador',
	'#',
	'Período',
	'Resultado', 
	'Tipo', 
	'Status', 
	''
);

foreach( $collection as $item )
{
    $link = '';
    $fl_avaliador = false;
	
	if($item['status'] == 'A' OR $item['status'] == 'F'  OR $item['status'] == 'E')
    {
        $link .= "<a href='javascript:void(0);' onclick='reabrir(".$item['cd_avaliacao_capa'].")'>[reabrir]</a> ";
		$fl_avaliador = true;
    }
	
    if($item['status'] == 'E' OR $item['status'] == 'S' )
    {
        $link .= "<a href='javascript:void(0);' onclick='encerrar(".$item['cd_avaliacao_capa'].")'>[encerrar]</a> ";
    }
	
    if($item['status'] != 'C')
    {
        $link .= "<a href='javascript:void(0);' onclick='excluir(".$item['cd_avaliacao_capa'].")'>[excluir]</a>";
    }
	

	#### EDITA SUPERIOR (AVALIADOR) ####
	$input_ordem  = "";
	$editar_ordem = "";
	$salvar_ordem = "";
	if($fl_avaliador)
	{
		$ar_avaliador = Array();
		$ar_avaliador[$item['cd_usuario_avaliador']] = $item['nome_avaliador'];
		foreach($ar_superior as $i_sup)
		{
			if($item['cd_usuario_avaliador'] != $i_sup["value"])
			{
				$ar_avaliador[$i_sup["value"]] = $i_sup["text"];
			}
		}		

		$input_ordem = form_dropdown('cd_avaliador_'.$item["cd_avaliacao_capa"], $ar_avaliador, array($item['cd_usuario_avaliador']), 'style="width: 200px;"');
		$editar_ordem = '<a href="javascript: void(0)" id="cd_avaliador_editar_'.$item['cd_avaliacao_capa'].'" onclick="editar_superior($(this));" title="Editar">[editar]</a>';
		$salvar_ordem = '<BR><a href="javascript: void(0)" id="cd_avaliador_salvar_'.$item['cd_avaliacao_capa'].'" onclick="salvar_superior('.$item['cd_avaliacao_capa'].', $(this));" title="Salvar">[salvar]</a> <a href="javascript: void(0)" id="cd_avaliador_cancelar_'.$item['cd_avaliacao_capa'].'" onclick="cancelar_superior($(this));" title="Cancelar">[cancelar]</a>';	
	}
	
	$body[] = array(
		$item['cd_avaliacao_capa'],
		$item['divisao'],
        array($item['nome_avaliado'],'text-align:left;'),
		array($item['nome_avaliador'].' '.$editar_ordem,'text-align:left;'),
		($fl_avaliador ? array('<span id="cd_avaliador_ajax_'.$item['cd_avaliacao_capa'].'"></span> '.$input_ordem.$salvar_ordem, 'text-align:left;') : ""),
        $item['dt_periodo'],
		number_format($item['media_parcial'],2,",","."),
		'<span class="'.$item["cor_tipo_promocao_label"].'">'.$item["tipo_promocao"].'</span>',
		'<span class="'.$item["cor_ds_status_label"].'">'.$item["ds_status"].'</span>',
        (trim($item['dt_publicacao']) != '' ? $item['dt_publicacao'] : $link)
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
$grid->col_oculta = array(4);
echo $grid->render();
?>