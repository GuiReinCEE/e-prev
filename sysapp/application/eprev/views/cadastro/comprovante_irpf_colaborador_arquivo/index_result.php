<?php
$body=array();
$head=array( 
	'Cód',
	'Arquivo',
	'Qt Comprovantes',
	'Ano Exercício',
	'Ano Calendário',
	'Dt Carga',
	'Usuário Carga',
	'Dt Liberação',
	'Usuário Liberação',
	''
);

foreach( $collection as $item )
{
	$body[] = array(
		anchor("cadastro/comprovante_irpf_colaborador_arquivo/item/".$item["cd_comprovante_irpf_colaborador"], $item["cd_comprovante_irpf_colaborador"]),
		array(anchor("cadastro/comprovante_irpf_colaborador_arquivo/item/" . $item["cd_comprovante_irpf_colaborador"], $item["ds_arquivo_nome"]),"text-align:left;") ,
		$item["qt_comprovante"],
		$item["nr_ano_exercicio"],
		'<span class="label label-success">'.$item["nr_ano_calendario"]."</span>",
		$item["dt_carga"],
		$item["usuario_carga"],
		(trim($item["dt_liberacao"]) == "" ? '<input type="button" value="Liberar Acesso" class="btn btn-mini btn-warning" onclick="liberar(\''.$item["cd_comprovante_irpf_colaborador"].'\')">' : $item["dt_liberacao"]),
		$item["usuario_liberacao"],
		'<input type="button" value="Excluir" class="btn btn-mini btn-danger" onclick="excluir(\''.$item["cd_comprovante_irpf_colaborador"].'\')">'
		
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;

echo $grid->render();
?>