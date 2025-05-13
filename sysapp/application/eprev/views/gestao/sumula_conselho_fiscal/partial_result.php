<?php
	$body = array();
	$head = array(
		'Número',
		'Súmula do Conselho',
		'Resposta',
		'Dt Súmula',
		'Dt Divulgação',
		'Total Não Resp.',
		'Total Resp.',
		'Resp Fora Prazo',
		'Total',
		''
	);

	foreach ($collection as $item)
	{
		$percent = 0;
	
		if(intval($item['qt_itens']) > 0)
		{
			$percent = (intval($item['qt_respondidos']) * 100) / intval($item['qt_itens']);
		}
		
		$nao_respondidos = intval($item['qt_itens']) - intval($item['qt_respondidos']);
		
		$body[] = array(
			(
				(gerencia_in(array('GC'))) ? $item["nr_sumula_conselho_fiscal"]." ".anchor("gestao/sumula_conselho_fiscal/responsabilidade/".$item["cd_sumula_conselho_fiscal"], "[editar]") : $item["nr_sumula_conselho_fiscal"]
			),
			array($item["arquivo_nome"]." ".anchor(base_url().'up/sumula_conselho_fiscal/' . $item['arquivo'], "[ver arquivo]" , array('target' => "_blank")), "text-align:left;"),
			anchor("gestao/sumula_conselho_fiscal/pdf/".$item["cd_sumula_conselho_fiscal"], "[ver resposta]", array('target' => "_blank")),
			$item["dt_sumula_conselho_fiscal"],
			$item["dt_divulgacao"],
			array('<span class="label '.(intval($nao_respondidos) > 0 ? 'label-important' : '').'">'.$nao_respondidos.'</span>','tex-align:center;' ,'int'),
			array('<span class="label label-info">'.$item['qt_respondidos'].'</span>','tex-align:center;' ,'int'),
			array('<span class="label '.(intval($item['qt_respondidos_limite']) > 0 ? 'label-important' : '').'">'.$item['qt_respondidos_limite'].'</span>','tex-align:center;' ,'int'),
			array('<span class="label label-success">'.$item['qt_itens'].'</span>','tex-align:center;' ,'int'),
			progressbar(intval($percent)),
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
	echo $grid->render();
?>