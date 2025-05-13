<?php
	$head = array(
		'Cód',
		'Descrição', 
	   	'Dt Referência',
	   	'Dt Inclusão',
	   	'Usuário',
	   	'Responsáveis',
	   	'Dt. Prazo',
	   	'Dt. Verificado',
	   	'Usuário Verificado',
	   	'Acompanhamento',
	   	''
	);

	$body = array();

	foreach($collection as $item)
	{
		$body[] = array(
			anchor(site_url("gestao/gestao_aviso/historico/".$item["cd_gestao_aviso"].'/S'), $item["cd_gestao_aviso"]),
			array(anchor(site_url("gestao/gestao_aviso/aviso_diretoria_cadastro/".$item["cd_gestao_aviso"]), $item["ds_descricao"]), "text-align:left;"),
			$item["dt_referencia"],
			$item["dt_inclusao"],
			array($item["usuario_inclusao"], "text-align:left;"),
			array(implode(br(),$item['usuario']), 'text-align:left;'),
			$item['dt_referencia'],
			$item['dt_verificacao'],
			array($item['ds_usuario_verificado'], 'text-align:left;'),
			array(nl2br($item["ds_acompanhamento"]), "text-align:justify;"),
			anchor(site_url("gestao/gestao_aviso/aviso_diretoria_cadastro/".$item["cd_gestao_aviso"]), '[editar]').' '.
			anchor(site_url("gestao/gestao_aviso/historico/".$item["cd_gestao_aviso"].'/S'), '[histórico]').' '.
			(intval($item['cd_usuario_inclusao']) == $this->session->userdata('codigo')
				? '<a href="javascript: excluirItem('.$item["cd_gestao_aviso"].')">[excluir]</a>'
				: '')
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
	echo $grid->render();
?>