<?php
$body=array();
$head = array( 
	'Cód',
	'Arquivo'
);

foreach($ar_anexo as $item)
{
	$body[] = array(
		$item['cd_email_anexo'],
		array(anchor("ecrm/reenvio_email/abrirAnexo/".$item["cd_email_anexo_md5"], $item['arquivo_nome'], array("target" => "_blank")),"text-align:left;")
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
$grid->view_count = false;
echo $grid->render();
?>