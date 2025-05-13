<?php
$body=array();
$head = array( 
	'Nome',
	'Cуdigo bloqueto',
	'Valor',
	'Dt Emissгo',
	'Dt Vencimento',		
	'Dt Carga',
	'Descriзгo',
	'Arquivo',
	'Bloqueto'
);

foreach( $collection as $item )
{
	$body[] = array(
	array($item["nome"],"text-align:left;"),
	$item["cd_registro_empregado"],
	number_format($item["valor"],2,",","."),
	$item["dt_emissao"],
	$item["dt_vencimento"],
	$item["dt_carga"],
	array(nl2br($item["descricao"]),"text-align:left;"),
	$item["ds_arquivo_nome"],
	anchor("http://www.fundacaoceee.com.br/COBR0020.php?b=".md5($item["cd_registro_empregado"]),"[ver eletro]","target='_blank'").
	anchor("http://www.banrisul.com.br/bbl/link/ativa.asp?CodCedente=".$item["codigo_cedente"]."&NossoNumero=".$item["nosso_numero"]."&SegundaVia=1","[ver banrisul]","target='_blank'")
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>