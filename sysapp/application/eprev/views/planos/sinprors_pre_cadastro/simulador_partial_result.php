<?php
$body=array();
$head = array( 
	'Data','Nome','Email','Telefone','Matrícula','CPF','Nascimento','Dúvida'
);

foreach( $collection as $item )
{
	$body[] = array(
    $item["dt_inclusao"]
  , array( $item["ds_nome"], 'text-align:left;' )
  , $item["ds_email"]
  , $item["nr_telefone"]
  , $item["nr_matricula"]
  , $item["nr_cpf"]
  , $item["dt_nascimento"]
  , array($item["ds_duvida"], 'text-align:left;')
);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>