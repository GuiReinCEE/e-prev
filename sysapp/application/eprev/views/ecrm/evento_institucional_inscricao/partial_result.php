<?php
$body=array();
$head = array( 
	'Cód.','Inscrito','EMP/RE/SEQ','Empresa', 'CPF', 'Telefone', 'Cargo','Data','Origem','Evento',
	'Tipo','Identificação','Confirmado','Presente','Certificado','Alterar','E-mail'
);

foreach( $collection as $item )
{
    $link=anchor( site_url() . "/ecrm/evento_institucional_inscricao/detalhe/" . $item["cd_eventos_institucionais_inscricao"], $item["inscrito"]);

	$tipo=($item['tipo']=='I')?'Inscrito':'Acompanhante';
	
	$nr_conta = 0;
	$nr_fim = count($ar_tp_inscrito);
	$ob_select = "<select ".(gerencia_in(array('SG','GRI')) ? '' : 'disabled')." id='tp_inscrito_".$item["cd_eventos_institucionais_inscricao"]."' name='tp_inscrito_".$item["cd_eventos_institucionais_inscricao"]."' onchange='setIdentificacao(this.value,".$item["cd_eventos_institucionais_inscricao"].")'>";
	

	while ($nr_conta < $nr_fim)
	{
		$ob_select.= "<option  value='".$nr_conta."' ".($nr_conta == intval($item['tp_inscrito']) ? "selected" : "").">".$ar_tp_inscrito[$nr_conta]." </option>";
		$nr_conta++;
		
	}
	$ob_select.= "</select>";	
	


	$body[] = array(
		 $item["cd_eventos_institucionais_inscricao"]
		, array($link,'text-align:left;')
		, $item["cd_empresa"]."/".$item["cd_registro_empregado"]."/".$item["seq_dependencia"]
		, array($item['empresa'],'text-align:left;')
		, $item['cpf']
		, $item['telefone']
		, array($item['cargo'],'text-align:left;')
		, $item["dt_cadastro"]
		, $item["cadastro_por"]
		, array($item["evento"],'text-align:left;')
		, $tipo
		, '<span id="lb_tp_inscrito_'.$item["cd_eventos_institucionais_inscricao"].'">'.$ar_tp_inscrito[intval($item['tp_inscrito'])]."</span>"
		, $item["dt_confirma"]
		, 

		"<select ".(gerencia_in(array('SG','GRI','GAP')) ? '' : 'disabled')." name='fl_presente_".$item["cd_eventos_institucionais_inscricao"]."' onchange='setPresente(this.value,".$item["cd_eventos_institucionais_inscricao"].")'>
			<option value='N' ".($item['fl_presente'] == "S" ? "selected" : "").">Não</option>
			<option value='S' ".($item['fl_presente'] == "S" ? "selected" : "").">Sim</option>
		</select>"		

		, "<nobr>".
		($item['fl_presente'] == "S" ? anchor("http://www.fundacaoceee.com.br/evento_certificado.php?i=".$item["cd_eventos_institucionais_inscricao_md5"], "[Imprimir]" ,array('target' => '_blank')) : "")
		."<BR>".
		($item['fl_presente'] == "S" ? '<a href="#" onclick="emailCertificado('.$item["cd_eventos_institucionais_inscricao"].')">[Enviar Email]</a>' : "")
		."</nobr>"
		,$ob_select
		,$item['email']
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>