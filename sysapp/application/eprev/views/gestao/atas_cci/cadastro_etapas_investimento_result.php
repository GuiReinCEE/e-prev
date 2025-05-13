<?php
$body=array();
$head = array(
	'',
    'Dt Limite',
    'Etapa'
);

$i = 0;

foreach($collection as $item )
{
	$campo_check = array(
		'name'    => 'etapa_'.$i,
		'id'      => 'etapa_'.$i,
		'value'   => $item['cd_atas_cci_etapas_investimento'],
		'checked' => (trim($item['checked']) == 'S' ? TRUE : FALSE),
		'onclick' => 'this_checked('.$i.');'
	);	

	$body[] = array(
	    form_checkbox($campo_check),
		$item["dt_limite"],
		array(nl2br($item["ds_etapa"]), "text-align:justify;")
    );
	
	$i++;
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
$grid->view_count = false;
$grid->view_data = false;

echo $grid->render();
echo '
<script>
	function this_checked(i)
	{
		checked_etapa(i, '.$i.');
	}
</script>';
?>