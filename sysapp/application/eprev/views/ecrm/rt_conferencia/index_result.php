<?php
$body = array();
$head = array( 
	"RT - ID",
	"CPF",
	"RE",
	"Nome",
	"Assunto",
	"Subprograma",
	"E-mail 1",
	"E-mail 2",
	"Dt Inclusão",
	"Dt Atualização",
	"Dt Respondido",

);

foreach($collection as $item)
{
	$body[] = array(
		anchor("http://srvrt.eletroceee.com.br/rt/Ticket/Display.html?id=".$item["cd_ticket"], $item["cd_ticket"], array("target"=>"_blank")),
		"<NOBR>".$item["cpf"]."</NOBR>",
		$item["cd_empresa"]."/".$item["cd_registro_empregado"]."/".$item["seq_dependencia"],
		array("<NOBR>".anchor("http://srvrt.eletroceee.com.br/rt/Ticket/Display.html?id=".$item["cd_ticket"],$item["nome"], array("target"=>"_blank"))."</NOBR>", "text-align:left;"),
		array(utf8_decode($item["assunto"]), "text-align:left;"),
		array($item["subprograma"], "text-align:left;"),
		$item["email"],
		$item["email_profissional"],
		$item["dt_inclusao"],
		$item["dt_atualizacao"],
		$item["dt_resolvido"]
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>