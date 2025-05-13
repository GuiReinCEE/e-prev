<?php
$body=array();
$head = array( 
	'Código','Nome','Data do contato','Respondido','Data da resposta'
);

foreach( $collection as $item )
{
	if( $item['fl_respondido']!='S' ) { $color = ' style="color:red;"'; } else { $color = ' style="color:;"'; }
	//$color = ' style="color:red;"';

	if( trim($item['nome'])=='' ){ $nome="[Não informado]"; } else { $nome = trim($item['nome']); }

	if( $item["fl_respondido"]=='S' ) { $respondido='Sim'; } else { $respondido="<div".$color.">Não</div>"; }
	
    $link=anchor('ecrm/operacional_contato/detalhe/'.$item["codigo"], "<div".$color.">".$nome."</div>" );
	$body[] = array(
		 $item["codigo"]
		, array($link, "text-align:left;"), $item["data"]
		, $respondido
		, $item["dt_resposta"]
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
