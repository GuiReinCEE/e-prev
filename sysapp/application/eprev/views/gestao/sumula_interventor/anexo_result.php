<?php
$body=array();
$head = array( 
	'Código',
	'Dt Inclusão',
	'Arquivo',
	'Usuário',
	''
);

foreach( $collection as $item )
{
    $body[] = array(
		$item['cd_sumula_interventor_item_anexo'],
		$item['dt_inclusao'],
		array(anchor(base_url().'up/sumula_interventor/'.$item['arquivo'], $item['arquivo_nome'] , array('target' => "_blank")), "text-align:left;"),
		$item['nome'],
		'<a href="javascript:void(0);" onclick="excluir_anexo('.$item['cd_sumula_interventor_item_anexo'].')" class="fnc_excluir">[excluir]</a>'
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
echo '
	<script>
		$(function(){
			if($("#dt_resposta").val() != "")
			{
				$(".fnc_excluir").hide();
			};
		});
	</script>';
?>