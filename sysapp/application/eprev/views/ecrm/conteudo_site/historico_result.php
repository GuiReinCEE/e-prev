<?php
$body = array();
$head = array('C�d', '' , 'Dt Atualiza��o', 'Usu�rio Atualiza��o');

foreach($collection as $item)
{
	$body[] = array(
		$item["cd_conteudo_site_jn"],
		anchor("https://www.fundacaoceee.com.br/inicio.php?cd_secao=".$item["cd_secao"]."&cd_artigo=".$item["cd_materia"]."&jn=".$item["cd_jn"], '[Ver]', array('target' => 'blank')),
		$item["dt_jn"],
		$item["usuario_atualizacao"]
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>