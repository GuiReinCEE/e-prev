<?php
    $head = array( 
        'Número',
        'Gerência',
        'Assunto',
        'Data'
    );

    $body = array();

    foreach($collection as $item)
	{
		if($fl_permissao)
		{
			$nr_controle_rds_solicitacao = anchor('gestao/controle_rds_solicitacao/cadastro/'.$item['cd_controle_rds_solicitacao'], $item['nr_controle_rds_solicitacao']);
		}
		else
		{
			$nr_controle_rds_solicitacao = $item['nr_controle_rds_solicitacao'];
		}

        $body[] = array(
            $nr_controle_rds_solicitacao,
            $item['cd_gerencia'],
            array($item['ds_controle_rds_solicitacao'], 'text-align:left;'),
            $item['dt_controle_rds_solicitacao']
        );
    }

    $this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo $grid->render();