<?php
#echo "<PRE>".print_r($ar_eleicao,true)."</PRE>";


$body = array();
$head = array(
	'Ano',
	'Descrição',
	'Status',
	'',
	'Kits recebidos',
	'Kits inválidos',
	'Votos válidos',
	'Dt Abertura',
	'Dt Encerramento'
);

foreach ($ar_eleicao as $item)
{
    $link = "";
	$cor = "black";
	$status = $item["status"];
	if($item["situacao"] == "")
	{
		$cor = "orange";
	}
	elseif($item["situacao"] == "G")
	{
		$cor = "red";
		$status = "Aguardando Apuração";
		$link = anchor("gestao/eleicoes_apuracao/apuracao/".$item["id_eleicao"], "[Apuração]");
	}
	elseif($item["situacao"] == "A")
	{
		$cor = "blue";
		$link = anchor("gestao/eleicoes_apuracao/apuracao/".$item["id_eleicao"], "[Apuração]");
	}
	elseif($item["situacao"] == "F")
	{
		$cor = "gray";
	}
	
	$body[] = array(
		$item["ano_eleicao"],
		array($item['nome'], 'text-align:left;'),
		'<span style="color:'.$cor.';font-weight:bold;">'.$status.'</span>',		
		$link,
		number_format($item["num_votos"],0,",","."),
		number_format($item["invalidados"],0,",","."),
		number_format($item["votos_apurados"],0,",","."),
		$item["dt_hr_abertura"],
		$item["dt_hr_fechamento"]
    );
}
$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;

echo $grid->render();
?>