<BR>
<span id="status_envio" class="label label-important"></span>
<br><br>
<span class="label label-info">Para consultar os Participantes utilize a tela do Eletro COBP0402</span>
<br><br>
<br>
<input type="hidden" id="fl_gerado_email" name="fl_gerado_email" value="<? echo $fl_gerado_email; ?>">
<input type="hidden" id="fl_envia_email" name="fl_envia_email" value="<? echo $fl_envia_email; ?>">
<input type="hidden" id="qt_registro" name="qt_registro" value="<? echo $qt_registro; ?>">
<?php
echo button_save("Gerar emails","gerarEmail()","botao",'id="btGerarEmail"');
echo button_save("Enviar emails","enviaEmail()","botao_vermelho",'id="btEnviaEmail"');
echo button_save("Gerar Protocolo Digitalização","gerar_protocolo()","botao_disabled",'id="btGerarProtocolo"');


$body = array();
$head = array(
  'EMP/RE/SEQ',
  'Nome',
  'Dt Gerado',
  'Email Enviado',
  ''
);

foreach ($collection as $item)
{
    $body[] = array(
		$item["cd_empresa"] . "/" . $item["cd_registro_empregado"] . "/" . $item["seq_dependencia"],
		array($item["nome"], "text-align:left;"),
		$item["dt_controle"],
		((trim($item["dt_exclusao"]) != "") ? '<span class="label">Excluído</span>' : ($item["fl_email_enviado"] == "S" ? '<span class="label label-success">Sim</span>' : '<span class="label label-warning">Não</span>')),
		(((intval($fl_gerado_email) > 0) and (intval($fl_envia_email) == 0) and (trim($item["dt_exclusao"]) == ""))   ? '<a href="javascript: void(0);" onclick="excluirAviso('.$item["cd_empresa"].",".$item["cd_registro_empregado"].",".$item["seq_dependencia"].",".$item["nr_ano_competencia"].",".$item["nr_mes_competencia"].')">[excluir]</a>' : $item["dt_exclusao"])
    );
}



$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
