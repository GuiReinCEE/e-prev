<?php
	$body=array();
	$head = array( 
		'Cуd.','Seзгo','Tнtulo','Ordem','Dt cadastro','Dt alteraзгo','Dt exclusгo'
	);

	foreach( $collection as $item )
	{
		$body[] = array(
				$item["cd_materia"],
				array($item["ds_secao"], 'text-align:left;'),
				array(anchor("ecrm/conteudo_site/detalhe/".$item["cd_site"]."/".$item["cd_versao"]."/".$item["cd_materia"],$item["titulo"]), 'text-align:left;'),
				intval($item["ordem"]),
				$item["dt_inclusao"],
				$item["dt_alteracao"],
				$item["dt_exclusao"]
			);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
	echo $grid->render();
?>