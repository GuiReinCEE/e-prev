<?php
    $head = array( 
        'Regulamento',
		'CNPB',
        'Dt. Inclusão',
        'Fim Alt. Regulamento',
        'Ini. Quadro Comparativo',
        'Fim Quadro Comparativo',
        'Dt. Envio PREVIC',
        'Aprovação PREVIC',
        'Doc. PREVIC',
        ''
    );

    $body = array();

    foreach($collection as $item)
	{
        $link_regulamento = anchor('planos/regulamento_alteracao/pdf/'.$item['cd_regulamento_alteracao'].'/'.(trim($item['dt_alteracao_finalizada']) == '' ? 'S' : 'N'), '[regulamento]', 'target="_blank"');

        $link_quadro_comparativo = '';
        $link_doc_aprovacao = '';

        if(trim($item['dt_fim_quadro_comparativo']) != '')
        {
            $link_quadro_comparativo = anchor('planos/regulamento_alteracao/pdf_quadro_comparativo/'.$item['cd_regulamento_alteracao'], '[quadro comparativo]', 'target="_blank"');
        }

        if(trim($item['arquivo']) != '')
        {
            $link_doc_aprovacao = anchor(base_url().'up/regulamento/'.$item['arquivo'], '[doc aprovação]', 'target = "_blank"');
        }

        $body[] = array(
            array(anchor('planos/regulamento_alteracao/cadastro/'.$item['cd_regulamento_alteracao'], $item['ds_regulamento_tipo']), 'text-align:left;'),
            anchor('planos/regulamento_alteracao/cadastro/'.$item['cd_regulamento_alteracao'], $item['ds_cnpb']),
            $item['dt_inclusao'],
            $item['dt_alteracao_finalizada'],
            $item['dt_inicio_quadro_comparativo'],
            $item['dt_fim_quadro_comparativo'],
            $item['dt_envio_previc'],
            $item['dt_aprovacao_previc'],
            array($item['ds_aprovacao_previc'], 'text-align:left'),
            $link_regulamento.br().$link_quadro_comparativo.br().$link_doc_aprovacao
        );
    }
    $this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo $grid->render();