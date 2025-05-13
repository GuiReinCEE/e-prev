<?php
$body=array();
$head = array( 
	'M�s',  
	'Ingresso',
	'Desligamento',
	'L�quido',
	'Acumulado',
	'% Crescimento',
	'Dig. Ingresso', 
	'Dig. Desligamento',
	'Dig. L�quido',
	'Dig. Acumulado',
	'Dig. % Crescimento'
);

$qt_total = (count($acumulado) > 0 ? ($acumulado[0]["qt_ingresso"] - $acumulado[0]["qt_desligamento"]) : 0);
$qt_total_digita = (count($acumulado) > 0 ? ($acumulado[0]["qt_digita_ingresso"] - $acumulado[0]["qt_digita_desligamento"]) : 0);

$body[] = array(
	0,
	array((count($acumulado) > 0 ? $acumulado[0]["qt_ingresso"] : 0),'text-align:center;','int'),
	array((count($acumulado) > 0 ? $acumulado[0]["qt_desligamento"] : 0),'text-align:center;','int'),
	array($qt_total,'text-align:center;','int'),
	$qt_total, 
	"0,00",
	array((count($acumulado) > 0 ? $acumulado[0]["qt_digita_ingresso"] : 0),'text-align:center;','int'),
	array((count($acumulado) > 0 ? $acumulado[0]["qt_digita_desligamento"] : 0),'text-align:center;','int'),
	array($qt_total_digita,'text-align:center;','int'),
	$qt_total_digita,
	"0,00"
);


foreach($collection as $item)
{
	$qt_total_mes = ($item["qt_ingresso"] - $item["qt_desligamento"]);
	$qt_total+= ($item["qt_ingresso"] - $item["qt_desligamento"]);
	$perc = ($qt_total > 0 ? ($qt_total_mes * 100) / $qt_total : 0);

	$qt_total_mes_digita = ($item["qt_digita_ingresso"] - $item["qt_digita_desligamento"]);
	$qt_total_digita+= ($item["qt_digita_ingresso"] - $item["qt_digita_desligamento"]);
	$perc_digita = ($qt_total_digita > 0 ? ($qt_total_mes_digita * 100) / $qt_total_digita : 0);
	
	$body[] = array(
	    $item["mes"],
	    array($item["qt_ingresso"],'text-align:center;','int'),
	    array($item["qt_desligamento"],'text-align:center;','int'),
	    
		array($qt_total_mes,'text-align:center;','int'),
		
		$qt_total, 
		
		($perc == 100 ? "0,00" : number_format($perc,2,',','.')),
		
	    array($item["qt_digita_ingresso"],'text-align:center;','int'),
	    array($item["qt_digita_desligamento"],'text-align:center;','int'),
		
		array(($item["qt_digita_ingresso"] - $item["qt_digita_desligamento"]),'text-align:center;','int'),
		
		$qt_total_digita,
		
		($perc_digita == 100 ? "0,00" : number_format($perc_digita,2,',','.'))
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();


echo "
<div style='text-align:left;'>
	LEGENDA:
	<BR>
	<B>Ingresso</B> => refer�ncia data de ingresso
	<BR>
	<B>Desligamento</B> => refer�ncia data de desligamento 
	<BR>
	<B>L�quido</B> => refer�ncia data de desligamento e ingresso
	<BR>
	<B>Acumulado</B> => refer�ncia data de desligamento e ingresso
	<BR>
	<B>% Crescimento</B> => refer�ncia data de desligamento e ingresso
	<BR>
	<B>Dig. Ingresso</B> => refer�ncia data de digita��o do ingresso
	<BR>
	<B>Dig. Desligamento</B> => refer�ncia data de digita��o do desligamento
	<BR>
	<B>Dig. L�quido</B> => refer�ncia data de digita��o do desligamento e de digita��o do ingresso
	<BR>
	<B>Dig. Acumulado</B> => refer�ncia data de digita��o do desligamento e de digita��o do ingresso
	<BR>
	<B>Dig. % Crescimento</B> => refer�ncia data de digita��o do desligamento e de digita��o do ingresso
</div>	
";


?>