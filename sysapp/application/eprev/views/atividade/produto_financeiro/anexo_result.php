<?php
$body=array();
$head = array( 
	'Arquivo',
    'Dt Inclusao',
	''
);

if(((intval($row['cd_produto_financeiro']) > 0) AND (($this->session->userdata('codigo') == $row['cd_usuario_inclusao']) OR ($this->session->userdata('codigo') == $row['cd_usuario_responsavel']) 
	OR ($this->session->userdata('codigo') == $row['cd_usuario_revisor'])  OR (($this->session->userdata('divisao') == 'GIN') AND ($this->session->userdata('tipo') == 'G')))) OR (intval($row['cd_produto_financeiro']) == 0))
{
	$bool = true;
}
else
{
	$bool = false;
}

foreach( $collection as $item )
{
	$excluir = '<a href="javascript:void(0);" onclick="excluir('.$item['cd_produto_financeiro_anexo'].')">[excluir]</a>';
	
    $body[] = array(
		array(anchor(base_url().'up/produto_financeiro/' . $item['arquivo'], $item['arquivo_nome'] , array('target' => "_blank")), "text-align:left;"),
		$item['dt_inclusao'],
		($bool ? $excluir : '')
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>