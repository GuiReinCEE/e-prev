<?php
	$body = array();
	$head = array(
		'N�mero',
		'S�mula',
		'Resposta',
		'Dt S�mula',
		'Dt Divulga��o',
		'Total N�o Resp.',
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
				(gerencia_in(array('SG'))) ? $item["nr_sumula_interventor"]." ".anchor("gestao/sumula_interventor/responsabilidade/".$item["cd_sumula_interventor"], "[editar]") : $item["nr_sumula_interventor"]
			),
			array($item["arquivo_nome"]." ".anchor(site_url('gestao/sumula_interventor/sumula_interventor_pdf')."/".$item['cd_sumula_interventor'], "[ver arquivo]" , array('target' => "_blank")), "text-align:left;"),
			anchor("gestao/sumula_interventor/pdf/".$item["cd_sumula_interventor"], "[ver resposta]", array('target' => "_blank")),
			$item["dt_sumula_interventor"],
			$item["dt_divulgacao"],
			array('<span class="label '.(intval($nao_respondidos) > 0 ? 'label-important' : '').'">'.$nao_respondidos.'</span>','tex-align:center;' ,'int'),
			array('<span class="label label-info">'.$item['qt_respondidos'].'</span>','tex-align:center;' ,'int'),
			array('<span class="label '.(intval($item['qt_respondidos_limite']) > 0 ? 'label-important' : '').'">'.$item['qt_respondidos_limite'].'</span>','tex-align:center;' ,'int'),
			array('<span class="label label-success">'.$item['qt_itens'].'</span>','tex-align:center;' ,'int'),
			array('Sem Reflexo: '.intval($item['tl_sem_reflexo']).'<br/>
			A��o Preventiva: '.intval($item['tl_acao_preventiva']).'<br/>
			N�o Conformidade: '.intval($item['tl_nao_conformidade']).'<br/>
			Sem Reflexo - Plano de A��o: '.intval($item['tl_sem_reflexo_plano_de_acao']),'text-align:left'),
			progressbar(intval($percent)),
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
	echo $grid->render();
?>