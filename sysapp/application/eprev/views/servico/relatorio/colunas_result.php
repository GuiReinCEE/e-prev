<?php
$body=array();
$head = array( 
	'Nº Coluna',
	'Coluna',
	'Alinhamento',
	'Largura(mm)',
	''
);
$i = 0;
foreach( $collection as $item )
{
	$body[] = array(
		$item["cd_coluna"],
		array($item["nome_coluna"],'text-align:left;'),
		$item["alinhamento"],
		$item["largura"],
		'<a href="javascript:void(0);" onclick="excluir_coluna('.intval($item["cd_coluna"]).');">[excluir]</a>'
	);
	
	$i++;
}

echo '
<script>
	$(function(){
		$("#numero_colunas").val("'.$i.'");
	});
</script>';
$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>