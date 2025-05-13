<?php
$body = array();
$head = array( 
	'',
    'NC',
    'SAP',
    'Retorno',
	'Número',
	'RE',
	'Nome',
	'Descrição',
	'Dt Cadastro',
	'Classificação',
	'Registrada por',
	'Dt Encaminhado',
	'Gerência',
	'Responsável',
	'Dt Prazo',
	'Dt Prorrogação',
	'Dt Encerrado',
	'Dt Cancelado'
	
);

$fl_reclamacao_analise_item = FALSE;

foreach( $collection as $item )
{
	$campo_check = array(
		'name'        => 'cd_reclamacao_'.$item['ano'].'_'.$item['numero'].'_'.$item['tipo'].'_'.intval($item['cd_reclamacao_analise_item']),
		'id'          => 'cd_reclamacao_'.$item['ano'].'_'.$item['numero'].'_'.$item['tipo'].'_'.intval($item['cd_reclamacao_analise_item']),
		'value'       => $item['ano'].'_'.$item['numero'].'_'.$item['tipo'].'_'.intval($item['cd_reclamacao_analise_item']),
		'checked'     => (intval($item['cd_reclamacao_analise_item']) > 0 ? TRUE : FALSE),
		'onclick'     => 'salvar_reclamacao(this)'
	);	
	
	if(intval($item['cd_reclamacao_analise_item']) > 0)
	{
		$fl_reclamacao_analise_item = TRUE;
	}

	$body[] = array(
		(trim($item['dt_envio']) == '' ? form_checkbox($campo_check) : ""),
        '<span class="label label-important">'.$item['nc_ano_numero'].'</span>',
        '<span class="label label-success">'.$item['ano_numero_sap'].'</span>',
        array($item["ds_retorno"],"text-align:justify;"),
		$item["cd_reclamacao"],
		$item["cd_empresa"]."/".$item["cd_registro_empregado"]."/".$item["seq_dependencia"],
		array($item["nome"],"text-align:left;"),
		array("<div style='width: 300px;'>".nl2br($item["descricao"])."</div>","text-align:left;"),
		$item["dt_inclusao"],
		'<span class="label '.trim($item['cor']).'">'.$item['ds_reclamacao_retorno_classificacao'].'</span>',
		array($item["ds_usuario_reclamacao"],"text-align:left;"),
		$item["dt_encaminhado"],
		$item["cd_divisao"],
		array($item["ds_usuario_responsavel"],"text-align:left;"),
		$item["dt_prazo"],
		$item["dt_prorrogacao"],
		$item["dt_retorno"],
		$item["dt_cancela"]
	);
}

echo '
<script>
	$(function(){
		'.($fl_reclamacao_analise_item ? '$("#btn_enviar").show();' : '$("#btn_enviar").hide();').'
	});
</script>';

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>