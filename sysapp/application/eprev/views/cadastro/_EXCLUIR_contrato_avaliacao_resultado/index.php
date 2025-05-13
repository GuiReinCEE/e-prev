<?php
set_title('Cadastro, avaliação de contrato, resultado');
$this->load->view('header');
?>
<script>
function abrir_avaliacao()
{
	location.href='<?php echo site_url("cadastro/contrato_avaliacao/avaliacao/") . "/" . md5($cd_avaliacao); ?>';
}

function configure_result_table()
{
	var ob_resul = new SortableTable(document.getElementById("table-1"),
	[
		'Number','CaseInsensitiveString','CaseInsensitiveString'
	]);
	ob_resul.onsort = function ()
	{
		var rows = ob_resul.tBody.rows;
		var l = rows.length;
		for (var i = 0; i < l; i++)
		{
			removeClassName( rows[i], i % 2 ? "sort-par" : "sort-impar" );
			addClassName( rows[i], i % 2 ? "sort-impar" : "sort-par" );
		}
	};
	ob_resul.sort(0, true);
}


function lista()
{
	alert( 'Em desenvolvimento' );
}

function abrir_lista()
{
	location.href='<?php echo site_url("cadastro/contrato_avaliacao"); ?>';
}

</script>

<?php

$abas[] = array( 'aba_lista', 'Lista', FALSE, 'abrir_lista()' );
$abas[] = array( 'aba_avaliacao', 'Avaliação', FALSE, 'abrir_avaliacao()' );
$abas[] = array( 'aba_resultado', 'Resultado', TRUE, '' );
echo aba_start( $abas );

$body=array();
$head = array( 
	'Código', 'Nome', 'Avaliou'
);

foreach( $collection as $item )
{
	$body[] = array( $item["cd_divisao"], $item["nome"], $item["fl_avaliou"] );
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
$grid->view_count=FALSE;
echo form_start_box("controle_box","<span style='color:red'>CONTROLE AVALIAÇÃO</span>", FALSE);
echo $grid->render();
echo form_end_box("controle_box", FALSE);

// ------------

$body=array();
$head = array( 
	array('', 'width:10px;'),array('', 'width:10px;'),array('', 'width:10px;'),array('', ''),array('Quantidade', 'text-align:right;')
);

$old['cd_divisao']='';$new['cd_divisao']='';
$old['ds_contrato_formulario_pergunta']='';$new['ds_contrato_formulario_pergunta']='';
$old['ds_contrato_formulario_grupo']='';$new['ds_contrato_formulario_grupo']='';
$body_template=array();
foreach( $collection_resposta as $item )
{
	
	// GRUPO
	$new['ds_contrato_formulario_grupo']='';
	if( $item["ds_contrato_formulario_grupo"]!=$old['ds_contrato_formulario_grupo'] ) 
	{
		$new['ds_contrato_formulario_grupo']="<tr bgcolor='#eeeeee'><td colspan='5'><big><b>".$item["ds_contrato_formulario_grupo"]."</b></big></td></tr>";
		$old['cd_divisao']="";
		$old['ds_contrato_formulario_pergunta']="";
	}
	$old['ds_contrato_formulario_grupo'] = $item["ds_contrato_formulario_grupo"];
	
	// PERGUNTA
	$new['ds_contrato_formulario_pergunta']='';
	if( $item["ds_contrato_formulario_pergunta"]!=$old['ds_contrato_formulario_pergunta'] ) 
	{
		$new['ds_contrato_formulario_pergunta']="<tr bgcolor='#f0e8ba'><td></td><td colspan='4'>".$item["ds_contrato_formulario_pergunta"]."</td></tr>";
		$old['cd_divisao']="";
	}
	$old['ds_contrato_formulario_pergunta']=$item["ds_contrato_formulario_pergunta"];

	// DIVISÃO
	$new['cd_divisao']='';
	if( $item["cd_divisao"]!=$old['cd_divisao'] )
	{
		$new['cd_divisao']=$item["cd_divisao"];
		$new['cd_divisao']="<tr bgcolor='#FFFFCC'><td></td><td></td><td colspan='3'>".$item["cd_divisao"]."</td></tr>";
	}
	$old['cd_divisao']=$item["cd_divisao"];

	// RESPOSTA
	$resposta = "<tr bgcolor='#ffffff'><td colspan='3'></td><td>".$item["ds_resposta"]."</td><td align='right'>".$item["quantos"]."</td></tr>";

	$body_template[] = array( $new['ds_contrato_formulario_grupo'], $new['ds_contrato_formulario_pergunta'], $new['cd_divisao'], $resposta );
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->view_count=FALSE;
$grid->body_template = $body_template;

echo form_start_box("resposta_box","<span style='color:red'>RESPOSTA AVALIAÇÃO</span>", FALSE);
echo $grid->render();
echo form_end_box("resposta_box", FALSE);

// ------------

echo form_start_box("final_box","<span style='color:red'>RESULTADO FINAL</span>", FALSE);
echo "<span style='color:blue;font-size:20px;'>Índice de aprovação " . $resultado_final . "%</span>";
echo form_end_box("final_box", FALSE);
?>

<br />

<?php
echo aba_end(''); 
?>

<script type="text/javascript">
	// filtrar();
</script>

<?php
$this->load->view('footer');
?>