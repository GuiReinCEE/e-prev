<?php
$body=array();
$head=array( 
	'Protocolo', 'Participante', 'Nome', 'Data de cadastro', 'Assunto', 'Forma de envio', 'Usuário'
);
if( gerencia_in( array('GB') ) ){
	$head[]='';
}

foreach( $collection as $item )
{
	$link=anchor("ecrm/protocolo_beneficio/detalhe/" . $item["cd_protocolo_beneficio"], $item["nr_ano"] . "/" . substr('0000'.$item["nr_protocolo"],-4,4)  );

	if( $item["cd_empresa"]!='' )
	{
		$participante = $item["cd_empresa"] . "/" . $item["cd_registro_empregado"] . "/" . $item["seq_dependencia"];
	}
	else
	{
		$participante = "";
	}

	$body[] = array(
	 $link
	, $participante
	, $item["nome"]
	, $item["dt_inclusao"]
	, $item["ds_protocolo_beneficio_assunto"]
	, $item["ds_protocolo_beneficio_forma_envio"]
	, $item["nome_usuario_inclusao"]
	);

	if( gerencia_in( array('GB') ) )
	{
		$body[sizeof($body)-1][]="<input type='button' onclick='excluir( \"".md5($item["cd_protocolo_beneficio"])."\" );' class='botao' value='excluir' />";
	}
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
