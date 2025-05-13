<?php
$body = Array();
$head = array( 
	'Cód',
	'Agrupamento',
	'Questão',
	'R01',
	'R02',
	'R03',
	'R04',
	'R05',
	'R06',
	'R07',
	'R08',
	'R09',
	'R10',
	'R11',
	'R12',
	'R13',
	'R14',
	'R15'
);

foreach($collection as $item )
{
	$body[] = array( 
		$item["cd_pergunta"],
		array($item["nr_ordem_agrupamento"]." - ".$item["ds_agrupamento"], 'text-align:left'),
		array(anchor('ecrm/operacional_enquete/questao/'.$item["cd_enquete"].'/'.$item["cd_pergunta"], $item["ds_pergunta"]), 'text-align:left'),
		'<span class="label '.(trim($item["r1"])  == "S" ? "label-inverse" : "").'">'.$item["r1"].'</span>',
		'<span class="label '.(trim($item["r2"])  == "S" ? "label-inverse" : "").'">'.$item["r2"].'</span>',
		'<span class="label '.(trim($item["r3"])  == "S" ? "label-inverse" : "").'">'.$item["r3"].'</span>',
		'<span class="label '.(trim($item["r4"])  == "S" ? "label-inverse" : "").'">'.$item["r4"].'</span>',
		'<span class="label '.(trim($item["r5"])  == "S" ? "label-inverse" : "").'">'.$item["r5"].'</span>',
		'<span class="label '.(trim($item["r6"])  == "S" ? "label-inverse" : "").'">'.$item["r6"].'</span>',
		'<span class="label '.(trim($item["r7"])  == "S" ? "label-inverse" : "").'">'.$item["r7"].'</span>',
		'<span class="label '.(trim($item["r8"])  == "S" ? "label-inverse" : "").'">'.$item["r8"].'</span>',
		'<span class="label '.(trim($item["r9"])  == "S" ? "label-inverse" : "").'">'.$item["r9"].'</span>',
		'<span class="label '.(trim($item["r10"]) == "S" ? "label-inverse" : "").'">'.$item["r10"].'</span>',
		'<span class="label '.(trim($item["r11"]) == "S" ? "label-inverse" : "").'">'.$item["r11"].'</span>',
		'<span class="label '.(trim($item["r12"]) == "S" ? "label-inverse" : "").'">'.$item["r12"].'</span>',
		'<span class="label '.(trim($item["r13"]) == "S" ? "label-inverse" : "").'">'.$item["r13"].'</span>',
		'<span class="label '.(trim($item["r14"]) == "S" ? "label-inverse" : "").'">'.$item["r14"].'</span>',
		'<span class="label '.(trim($item["r15"]) == "S" ? "label-inverse" : "").'">'.$item["r15"].'</span>'
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->id_tabela  = "tbQuestao";
$grid->view_count = false;
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>
