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
			array(anchor('servico/meus_treinamentos_diretoria/documento/'.$item['cd_treinamento_diretoria_conselhos_item'], $item['ds_nome']), 'text-align:left'),
	        array($item['ds_promotor'], 'text-align:left'),
	        array($item['ds_cidade'], 'text-align:left'),
	        $item['ds_uf'],
	        $item['dt_inicio'],
	        $item['dt_final'],
	        array($item['ds_treinamento_colaborador_tipo'], 'text-align:left'),
	        str_replace('.', ',', $item['nr_carga_horaria']),
	        (trim($item['arquivo_nome']) != ''
	        	? array(anchor(base_url().'up/certificado_treinamento/'.$item['arquivo'], $item['arquivo_nome'], array('target' => '_blank')), 'text-align:left;')
	        	: ''
	        ),
		);
	}	

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo $grid->render();
?>