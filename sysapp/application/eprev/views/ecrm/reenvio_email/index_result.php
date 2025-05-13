<?php
$body=array();
$head = array( 
	'URL',
	'Acesso (Data - IP)'
);

foreach($ar_link as $item)
{
	$link_log = "";
	
	if(count($ar_link_log[$item["cd_email_link"]]) > 0)
	{
		foreach($ar_link_log[$item["cd_email_link"]] as $ar_log)
		{
			$link_log.= $ar_log['dt_acesso']." [".$ar_log['ip']."]<BR>";
		}
	}
	$body[] = array(
		array(anchor($item["link"], $item["link"]),"text-align:left;"),
		$link_log
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>