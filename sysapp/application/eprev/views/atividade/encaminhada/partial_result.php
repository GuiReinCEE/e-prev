<?php
$body=array();
$head=array(
	'Ativ.'
	, 'Data'
	, 'Solic/Atend'
	, 'Descrição'
	, 'Ger.'
	, 'Status'
	, 'Tarefas'
	, 'Projeto'
	, 'Tipo'
	, 'Dt. Limite'
	, 'Dt. Teste'
	, 'Dt. Conclusão'
	, 'RE'
);

foreach( $collection as $item )
{
	$link = anchor(site_url('atividade/atividade_solicitacao/index/'.$this->session->userdata('divisao').'/'.$item['numero']), $item["numero"]);
	$link_descricao = anchor(site_url('atividade/atividade_solicitacao/index/'.$this->session->userdata('divisao').'/'.$item['numero']), $item["descricao"]);

	$RE='';
	if(trim($item["cd_empresa"])!='')
	{
		$RE = $item["cd_empresa"]."/".$item["cd_registro_empregado"]."/".$item["cd_sequencia"];
	}

	$sep="";
	$tarefas="";
	if(isset($item['tarefas']))
	{
		foreach( $item['tarefas'] as $tarefa )
		{
			$tarefas .= $sep . anchor("atividade/tarefa/cadastro/".$item["numero"]."/".$tarefa["cd_tarefa"]."/".strtolower($tarefa['fl_tarefa_tipo']), '- '.$tarefa['cd_tarefa']  ) ;
			$sep="<br>";
		}
	}

	$body[] = array(
		 $link
		, $item["dt_cad"]
		, $item["nomesolic"].'<br /><i>'.$item["nomeatend"].'</i>'
		, array( "<div style='width:500px;'>" . $link_descricao . "</div>",'text-align:left')
		, $item["div_solic"]
		, '<span class="'.$item["status_label"].'">'.$item["status"].'</span>'
		, $tarefas
		, $item["projeto_nome"]
		, $item["tipo"]
		, $item["data_limite"]
		, $item["data_limite_teste"]
		, $item["data_conclusao"]
		, $RE
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;

echo $grid->render();
?>