<?php
	$body = array();
	$head = array(
		'Número',
		'Súmula',
		'Resposta',
		'Dt Súmula',
		'Dt Divulgação',
		'Total Não Resp.',
		'Total Resp.',
		'Resp Fora Prazo',
		'Total',
		'Qt por Resp',
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
				(gerencia_in(array('GC'))) ? $item["nr_sumula"]." ".anchor("gestao/sumula/responsabilidade/".$item["cd_sumula"], "[editar]") : $item["nr_sumula"]
			),
			array($item["arquivo_nome"]." ".anchor(site_url('gestao/sumula/sumula_pdf')."/".$item['cd_sumula'], "[ver arquivo]" , array('target' => "_blank")), "text-align:left;"),
			anchor("gestao/sumula/pdf/".$item["cd_sumula"], "[ver resposta]", array('target' => "_blank")),
			$item["dt_sumula"],
			$item["dt_divulgacao"],
			array('<span class="label '.(intval($nao_respondidos) > 0 ? 'label-important' : '').'">'.$nao_respondidos.'</span>','tex-align:center;' ,'int'),
			array('<span class="label label-info">'.$item['qt_respondidos'].'</span>','tex-align:center;' ,'int'),
			array('<span class="label '.(intval($item['qt_respondidos_limite']) > 0 ? 'label-important' : '').'">'.$item['qt_respondidos_limite'].'</span>','tex-align:center;' ,'int'),
			array('<span class="label label-success">'.$item['qt_itens'].'</span>','tex-align:center;' ,'int'),
			array('Sem Reflexo: '.intval($item['tl_sem_reflexo']).'<br/>
			Ação Preventiva: '.intval($item['tl_acao_preventiva']).'<br/>
			Não Conformidade: '.intval($item['tl_nao_conformidade']).'<br/>
			Sem Reflexo - Plano de Ação: '.intval($item['tl_sem_reflexo_plano_de_acao']),'text-align:left'),
			progressbar(intval($percent)),
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
	echo $grid->render();
?>