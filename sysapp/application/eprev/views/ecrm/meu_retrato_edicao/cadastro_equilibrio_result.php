<?php
	$head = array(
		'Cód',
		'Ano',
		'Provisões Matemáticas',
		'Patrimônio de Cobertura',
		''
	);

	$body = array();

	foreach ($collection as $item)
	{	
		$body[] = array(
			$item['cd_edicao_equilibrio'],
			$item['nr_ano'],
			number_format($item['vl_provisao'],2,',','.'),
			number_format($item['vl_cobertura'],2,',','.'),
			'<a href="#" onclick="equilibrio_del('.$item['cd_edicao_equilibrio'].')">[excluir]</a>'
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->view_count = false;
	$grid->head = $head;
	$grid->body = $body;
	echo $grid->render();
?>