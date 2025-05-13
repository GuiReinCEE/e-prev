<?php
	$head = array( 
		'Ano/Número', 
		'Data', 
		'Solicitante', 
		'Assinatura',
		//'RE',
		'Destinatário',
		'Assunto'
	);

	$body = array();

	foreach($collection as $item)
	{
		$body[] = array( 
		    anchor('cadastro/sg_correspondencia/cadastro/'.$item['cd_correspondencia'], $item['ano_numero']), 
			$item['data'],
			array($item['solicitante'], 'text-align:left'), 
			array($item['assinatura'], 'text-align:left'), 
			//$item['destinatario_emp'].'/'.$item['destinatario_re'].'/'.$item['destinatario_seq'],
			array($item['destinatario_nome'], 'text-align:left'),
			array(nl2br($item['assunto']), 'text-align:justify')
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo $grid->render();
?>
