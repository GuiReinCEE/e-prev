<?php
$body=array();
$head = array( 
	'Cód.',
	'RE',
	'Nome',
	'Empresa/Instituição',
	'Cargo/Função',
	'Dt Inscrição',
	'Presente',
	'Certificado',
	'Dt Certificado'
);



foreach( $collection as $item )
{
	$body[] = array(
		anchor("ecrm/seminario_economico/detalhe/".$item["cd_inscricao"], $item["cd_inscricao"]),
		$item["cd_empresa"]."/".$item["cd_registro_empregado"]."/".$item["seq_dependencia"],
		array(anchor("ecrm/seminario_economico/detalhe/".$item["cd_inscricao"],$item["nome"],'id="seminario_nome_'.$item["cd_inscricao"].'"'),"text-align:left;"),
		array($item["empresa"],"text-align:left;"),
		array($item["cargo"],"text-align:left;"),
		$item["dt_inclusao"],
		$item["fl_presente"],
		($item["fl_presente"] == "S" ? 
				anchor(str_replace("[CD_INSCRICAO]",$item["cd_inscricao_md5"],$item["certificado"]),"[Ver]","title='Versão para impressão do certificado' target='blank'")
				.($item["email"] != "" ? " ". '<a href="Javascript: enviarCertificado('.$item["cd_inscricao"].');" title="Enviar certificado por email">[Enviar]</a>' : "")
		: ""),
		$item["dt_envio_certificado"]
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
