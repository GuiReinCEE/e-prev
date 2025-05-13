<?php
set_title('Cadastros, Contratos, Avaliação');
$this->load->view('header');
?>
<script>
	function ir_lista()
	{
		location.href='<?php echo site_url("cadastro/contrato_avaliacao"); ?>';
	}
	
	function ir_avaliacao()
	{
		location.href='<?php echo site_url("cadastro/contrato_avaliacao/avaliacao/".$cd_contrato_avaliacao); ?>';
	}

</script>

<?php
$abas[] = array( 'aba_lista', 'Lista', FALSE, 'ir_lista()');
$abas[] = array( 'aba_avaliacao', 'Avaliação', FALSE, 'ir_avaliacao()');
$abas[] = array( 'aba_resultado', 'Resultado', TRUE, 'location.reload();');

$body = array();
$head = array( 
	'Gerência', 
	'Nome', 
	'Avaliou',
	'Dt Avaliado'
);

foreach( $avaliacao as $item )
{
	$body[] = array( 
		$item["cd_divisao"], 
		array($item["nome"], 'text-align:left'),
		array($item["avaliou"], 'text-align:center; font-weight:bold; '.(trim($item["avaliou"]) == 'Sim' ? 'color:blue;' : 'color:red;')),
		$item["dt_resposta"]
	);
}

$this->load->helper('grid');
$grid_1 = new grid();
$grid_1->head = $head;
$grid_1->body = $body;

$body = array();
$head = array( 
	array('', 'width:10px;'),
	array('', 'width:10px;'),
	array('', 'width:10px;'),
	array('', ''),
	array('Quantidade', 'text-align:right;')
);

$old['cd_divisao'] = '';
$new['cd_divisao'] = '';
$old['ds_contrato_formulario_pergunta'] = '';
$new['ds_contrato_formulario_pergunta'] = '';
$old['ds_contrato_formulario_grupo'] = '';
$new['ds_contrato_formulario_grupo'] = '';

foreach($respostas as $item)
{
	$new['ds_contrato_formulario_grupo'] = '';
	if( $item["ds_contrato_formulario_grupo"]!=$old['ds_contrato_formulario_grupo'] ) 
	{
		$new['ds_contrato_formulario_grupo']="<tr bgcolor='#eeeeee'><td colspan='5'><big><b>".$item["ds_contrato_formulario_grupo"]."</b></big></td></tr>";
		$old['cd_divisao']="";
		$old['ds_contrato_formulario_pergunta']="";
	}
	$old['ds_contrato_formulario_grupo'] = $item["ds_contrato_formulario_grupo"];
	
	$new['ds_contrato_formulario_pergunta']='';
	if( $item["ds_contrato_formulario_pergunta"]!=$old['ds_contrato_formulario_pergunta'] ) 
	{
		$new['ds_contrato_formulario_pergunta']="<tr bgcolor='#f0e8ba'><td></td><td colspan='4'>".$item["ds_contrato_formulario_pergunta"]."</td></tr>";
		$old['cd_divisao']="";
	}
	$old['ds_contrato_formulario_pergunta']=$item["ds_contrato_formulario_pergunta"];

	$new['cd_divisao']='';
	if( $item["cd_divisao"]!=$old['cd_divisao'] )
	{
		$new['cd_divisao']=$item["cd_divisao"];
		$new['cd_divisao']="<tr bgcolor='#FFFFCC'><td></td><td></td><td colspan='3'>".$item["cd_divisao"]."</td></tr>";
	}
	$old['cd_divisao']=$item["cd_divisao"];

	$resposta = "<tr bgcolor='#ffffff'><td colspan='3'></td><td>".$item["ds_resposta"]."</td><td align='right'>".$item["total"]."</td></tr>";

	$body[] = array( 
		$new['ds_contrato_formulario_grupo'], 
		$new['ds_contrato_formulario_pergunta'], 
		$new['cd_divisao'], 
		$resposta 
	);
}

$this->load->helper('grid');
$grid_2 = new grid();
$grid_2->head = $head;
$grid_2->body_template = $body;

echo aba_start( $abas );
	echo form_start_box("contrato_box","Contrato");
		echo form_default_row('ds_contrato', 'Contrato', $row['ds_contrato']);
	echo form_end_box("contrato_box");

	echo form_start_box("final_box","Resultado Final");

		echo "<span style='color:blue;font-size:20px;'>Índice de aprovação " . $resultado_final . "%</span>";
	echo form_end_box("final_box");
	echo form_start_box("avaliador_box", "Controle Avaliação");
		echo $grid_1->render();
	echo form_end_box("controle_box");
	echo form_start_box("resposta_box","Resposta Avaliação");
		echo $grid_2->render();
	echo form_end_box("resposta_box");
	
	echo br();

echo aba_end(); 
$this->load->view('footer');
?>
