<?php
$body=array();
$head = array( 
	'Dt Upload',
	'Nome',
	'Status',
	'Qt Linha',
	'Qt Bloqueto',
	'Vl Total',
	'Dt Carga',
	'Dt Banrisul',
	'Dt Agenda Envio E-mail',
	'Dt Agenda Bloqueio',
	'Dt Bloqueio',
	'Dados do Envio',
	'Arquivo'
);

foreach( $collection as $item )
{
	$body[] = array(
	$item["dt_upload"],
	array($item["ds_arquivo_nome"],"text-align:left;"),
	($item["status"] == "OK" ? $item["status"]: array($item["status"],"text-align:left;color:red;")),
	$item["qt_linha"],
	$item["qt_registro"],
	number_format($item["vl_total"],2,",","."),
	'<span class="label">'.$item["dt_carga"].'</span>',
	'<span class="label label-inverse">'.$item["dt_envio_banco"].'</span>',
	'<span class="label label-warning">'.$item["dt_envio_participantes"].'</span>',
	'<span class="label label-info">'.$item["dt_bloqueio"].'</span>',
	
	($item["dt_exclusao"] != "" ? '<span class="label label-important">'.$item["dt_exclusao"].'</span>'  : '<input type="button" value="Bloquear Acesso" class="botao_vermelho" onclick="deletaArquivo(\''.$item["cd_arquivo"].'\')">'),
	
	((($item["dt_exclusao"] != "") or ($item["dt_envio_email"] != ""))  
		? 
			(($item["dt_envio_email"] != "")
				?
					"	
					<table align='center' border='0' class='sort-table'>
						<tbody>
						<tr>
							<td>Dt Envio:</td><td align='center'>".'<span class="label label-success">'.$item["dt_envio_email"].'</span>'."</td>
						</tr>
						<tr>
							<td>Qt Email Enviado:</td><td align='center'>".$item["qt_email"]."</td>
						<tr>
							<td>Qt Sem Email:</td><td align='center'>".$item["qt_sem_email"]."</td>
						</tr>
						<tr>
							<td>Qt Total:</td><td align='center'>".($item["qt_email"] + $item["qt_sem_email"])."</td>
						</tr>						
						</tbody>
					</table>
					"
				: ""
			)
		: 
		""
		#'<input type="button" value="Enviar Emails" class="botao_verde" onclick="enviaEmail(\''.$item["cd_arquivo"].'\')">'
		),
	
	
	anchor(base_url()."up/bloqueto/".$item["ds_arquivo_fisico"],"[ver arquivo]","target='_blank'")
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>