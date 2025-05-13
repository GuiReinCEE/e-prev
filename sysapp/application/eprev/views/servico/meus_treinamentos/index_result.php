<?php
	$head = array( 
		'Número', 
		'Nome',
		'Promotor',
		'Cidade',
		'UF', 
		'Dt. Início',
		'Dt. Final',
		'Tipo', 
		'Carga Horária(H)',
		'Certificado'
	);

	$body = array();

	foreach($collection as $item)
	{
	    $body[] = array(
	        $item['numero'],
			array(anchor('servico/meus_treinamentos/anexo/'.$item['cd_treinamento_colaborador_item'], $item['nome']), 'text-align:left'),
	        array($item['promotor'], 'text-align:left'),
	        array($item['cidade'], 'text-align:left'),
	        $item['uf'],
	        $item['dt_inicio'],
	        $item['dt_final'],
	        array($item['ds_treinamento_colaborador_tipo'], 'text-align:left'),
	        str_replace('.', ',', $item['carga_horaria']),
	        (trim($item['fl_certificado']) == 'S'
	        	? array(anchor(base_url().'up/certificado_treinamento/'.$item['arquivo'], $item['arquivo_nome'], array('target' => '_blank')), 'text-align:left;')
	        	: (trim($item['fl_certificado']) == 'N' 
	        			? array($item['ds_justificativa'], 'text-align:justify;') 
	        			: array('<span class="label label-important">Pendente</span>', 'text-align:center;')
	        	  )
	        ),
		);
	}	

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo $grid->render();
?>