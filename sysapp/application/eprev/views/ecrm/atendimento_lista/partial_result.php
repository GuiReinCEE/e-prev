<?php
$body=array();
$head = array( 
	'Cód', 'Grav', 'De','Até','Tempo','Tipo','Programa', 'Atendente','RE','Nome','Cidade','UF','Obs'
);

foreach( $collection as $item )
{
	$obs='';
	if(($item['tipo_atendimento_indicado'] == 'R') or ($item['qt_reclamacao'] > 0) or ($item['qt_reclamacao_novo'] > 0))
	{
		$obs.='<img src="'.base_url().'img/atendimento/img_reclamacao.png" border="0">';
	}
	if((trim($item['obs']) != '') or ($item['qt_obs'] > 0))
	{
		$obs.='<img src="'.base_url().'img/atendimento/img_observacao.png" border="0">';
	}
	if($item['qt_elogio'] > 0)
	{
		$obs.='<img src="'.base_url().'img/atendimento/img_elogio.png" border="0">';
	}
	if($item['qt_encaminhamento'] > 0)
	{
		$obs.='<img src="'.base_url().'img/atendimento/img_encaminhamento.png" border="0">';
	}
	if($item['qt_retorno'] > 0)
	{
		$obs.='<img src="'.base_url().'img/atendimento/img_retorno.png" border="0">';
	}	
	
	$gravacao='';
	if(trim($item['nm_arquivo']) != "")
	{
		if($item['tp_arquivo'] == "XCALLY")
		{
			$gravacao='<a href="'.site_url("ecrm/atendimento_lista/gravacaoXcally/".$item['cd_atendimento']).'" title="Clique para ouvir a Gravação" target="_blank"><img src="'.base_url().'img/atendimento/img_gravacao.png" border="0"></a>';
		}
		else
		{
			$gravacao='<a href="'.pasta_gravacao().trim($item['nm_arquivo']).'" title="Clique para ouvir a Gravação" target="_blank"><img src="'.base_url().'img/atendimento/img_gravacao.png" border="0"></a>';
		}
	
		#$gravacao.= '<audio preload="none" controls="true" src="'.pasta_gravacao().trim($item['nm_arquivo']).'" type="audio/wav" style="width: 260px;"></audio>';
	}

	// https://www.e-prev.com.br/controle_projetos/lst_atendimento.php?at=750236
	//$link=anchor("ecrm/atendimento_lista/detalhe/" . $item["cd_atendimento"], $item["cd_atendimento"]);
    //
	$link=anchor("ecrm/atendimento_lista/atendimento/".$item['cd_atendimento'], $item['cd_atendimento']);

	$body[] = array(
	 $link
	, $gravacao
	, $item["dt_atendimento"]
	, $item["hr_fim"]
	, $item["hr_tempo"]
	, $item["tp_atendimento"]
	, $item["ds_programa"]
	, $item["atendente"]
	, $item["cd_empresa"]."/".$item["cd_registro_empregado"]."/".$item["seq_dependencia"]
	, array($item["nome"], "text-align:left;")
	, array($item["cidade"], "text-align:left;")
	, $item["uf"]
	, array($obs, 'text-align:left;')
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>