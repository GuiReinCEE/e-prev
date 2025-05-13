<style>
.videoWrapper {
	position: relative;
	padding-bottom: 56.25%; /* 16:9 */
	padding-top: 25px;
	height: 0;
}
.videoWrapper iframe {
	border: 0;
	position: absolute;
	top: 0;
	left: 0;
	width: 100%;
	height: 100%;
}
</style>

<div class="videoWrapper">
    <iframe src="//www.e-prev.com.br/_a/ferias/?a=<?php echo $this->session->userdata('divisao');?>" allowfullscreen></iframe>
</div>


<?php
/*
$body = array();
$head = array( 
	'Nome', 'Dt Início', 'Dt Fim'
);

foreach( $collection as $item )
{
	$body[] = array(
		array($item['nome'],'text-align:left;'),
		$item["dt_ferias_ini"],
		$item["dt_ferias_fim"]
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
*/
?>