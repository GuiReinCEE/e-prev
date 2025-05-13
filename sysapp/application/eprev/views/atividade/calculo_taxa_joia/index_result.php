<?php
$body=array();
$head=array(
	'RE',
	'Nome',
	'Ativ.',
	'Data',
	'Solic/Atend',
	'Descrição',
	'Ger.',
	'Status',
	'Dt. Conclusão'
);

foreach( $collection as $item )
{
	$re      = "";
	$sep     = "";
	$tarefas = "";

	$link = anchor(site_url('atividade/atividade_solicitacao/index/'.$this->session->userdata('divisao').'/'.$item['numero']), $item["numero"]);
	$link_descricao = anchor(site_url('atividade/atividade_solicitacao/index/'.$this->session->userdata('divisao').'/'.$item['numero']), $item["descricao"]);
	
	if(intval($item["cd_registro_empregado"]) > 0)
	{
		$re = $item["cd_empresa"]."/".$item["cd_registro_empregado"]."/".$item["cd_sequencia"];
	}
	
	$body[] = array(
		$re,
		array($item["nome_participante"],'text-align:left'), 
		$link,
		$item["dt_cad"],
		$item["nomesolic"].'<br /><i>'.$item["nomeatend"].'</i>',
		array( "<div style='width:500px;'>" . nl2br($link_descricao) . "</div>",'text-align:left'),
		$item["div_solic"],
		'<span class="'.$item["status_label"].'">'.$item["status"].'</span>',
		$item["data_conclusao"]
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;

echo $grid->render();
?>