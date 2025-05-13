<?php
$body=array();
$head = array( 
	'#',
	'RE',
	'Nome',
	'Email',
	'Dt Inscrição',
	'Empresa',
	'Cargo',
	'Cidade',
	'UF',
	'Presente',
	'Certificado',
	'Certificado'
);

foreach( $collection as $item )
{
	$cb_presente = '
						<select name="fl_presente_'.$item["cd_dialogo_inscricao"].'" id="fl_presente_'.$item["cd_dialogo_inscricao"].'" onchange="setPresente(this.value,'.$item["cd_dialogo_inscricao"].');">	
							<option value="" '.($item['fl_presente'] == "" ? "selected" : "").'></option>
							<option value="S" '.($item['fl_presente'] == "S" ? "selected" : "").'>Sim</option>
							<option value="N" '.($item['fl_presente'] == "N" ? "selected" : "").'>Não</option>
						</select>	
	               ';
				   
	$body[] = array(
	anchor("ecrm/dialogo_inscricao/cadastro/".$item["cd_dialogo_inscricao"], $item["cd_dialogo_inscricao"]),
	$item["cd_empresa"]."/".$item["cd_registro_empregado"]."/".$item["seq_dependencia"],
	array(anchor("ecrm/dialogo_inscricao/cadastro/".$item["cd_dialogo_inscricao"], $item["nome"]),"text-align:left;"),
	$item["email"],
	$item["dt_inclusao"],
	array($item["empresa"],"text-align:left;"),
	array($item["cargo"],"text-align:left;"),
	array($item["cidade"],"text-align:left;"),
	array($item["uf"],"text-align:left;"),
	$cb_presente,
	anchor($item["link_certificado"], "[Imprimir]", Array('title'=>'Clique para imprimir','target'=>'_blank')),
	($item["fl_email"] == "S" ?	$item["dt_envio_certificado"].'<BR><input type="button" value="Enviar Email" class="botao" onclick="enviaCertificado(\''.$item["cd_certificado"].'\');">' : "")
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
