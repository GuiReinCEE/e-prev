<?php
	$head = array(
		'Cód',
		'Descrição', 
	   	'Periodicidade',
	   	'Dt Referência',
	   	'Dia',
	   	'Dt Inclusão',
	   	'Usuário',
	   	''
	);

	$body = array();

	foreach($collection as $item)
	{
		$body[] = array(
			anchor(site_url("gestao/gestao_aviso/historico/".$item["cd_gestao_aviso"]), $item["cd_gestao_aviso"]),
			array(anchor(site_url("gestao/gestao_aviso/cadastro/".$item["cd_gestao_aviso"]), $item["ds_descricao"]), "text-align:left;"),
			'<span class="label '.$item["cor_periodicidade"].'">'.$item["periodicidade"].'</span>',
			$item["dt_referencia"],
			$item["dia"],
			$item["dt_inclusao"],
			array($item["usuario"], "text-align:left;"),
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