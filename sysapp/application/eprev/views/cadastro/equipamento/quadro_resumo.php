<?php 
set_title('Quadro Resumo');
$this->load->library('charts');
$this->load->view('header'); 
?>
<script>
function filtrar()
{
	load();
}

function load()
{
	location.href='<?php echo site_url("cadastro/equipamento/quadro_resumo"); ?>/' + $('#cd_divisao').val();
}
</script>
<?php
$abas[] = array('aba_lista', 'Resumo', TRUE, 'location.reload();');
echo aba_start( $abas );
echo form_list_command_bar();
echo form_start_box_filter('filter_bar', 'Filtros');
echo form_default_dropdown('cd_divisao', 'Gerência', $ar_divisao, array($cd_divisao));
echo form_end_box_filter();




#### SITUACAO EQUIPAMENTO ####
$body=array();
$head = array( 
	'Situação',
	'Quantidade'
);

$ar_titulo = Array();
$ar_dado = Array();
$ar_image = Array();
foreach($ar_situacao_equipamento as $item)
{
	$body[] = array(
		array($item["situacao_equipamento"],"text-align:left;"),
		array($item["quantidade"],'text-align:right;','int')
	);
	
	$ar_titulo[] = $item['situacao_equipamento'];
	$ar_dado[] = $item['quantidade'];	
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
$grid->view_count = false;
echo $grid->render();

$ar_image = $this->charts->pieChart(80,$ar_dado,$ar_titulo,'','Situação do equipamento');	
echo '<center><img src="'.$ar_image['name'].'" border="0"></center>';

echo "<BR>";
#### TIPO DE EQUIPAMENTO ####
$body=array();
$head = array( 
	'Tipo (em uso normal)',
	'Quantidade'
);
$ar_titulo = Array();
$ar_dado = Array();
$ar_image = Array();
foreach($ar_tipo_equipamento as $item)
{
	$body[] = array(
		array($item["tipo_equipamento"],"text-align:left;"),
		array($item["quantidade"],'text-align:right;','int')
	);
	$ar_titulo[] = $item['tipo_equipamento'];
	$ar_dado[] = $item['quantidade'];	
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
$grid->view_count = false;
echo $grid->render();

$ar_image = $this->charts->pieChart(80,$ar_dado,$ar_titulo,'','Tipo de equipamento');	
echo '<center><img src="'.$ar_image['name'].'" border="0"></center>';

echo "<BR>";
#### DISCO EQUIPAMENTO ####
$disco_unidade = 'GB';
$body=array();
$head = array( 
	'Espaço em disco (tipo e em uso normal)',
	'Total ('.$disco_unidade.')',
	'Livre ('.$disco_unidade.')',
	'%',
	'Usado ('.$disco_unidade.')',
	'%'
);

$ar_titulo = Array();
$ar_dado = Array();
$ar_image = Array();

$ar_titulo[] = "Livre";
$ar_titulo[] = "Usado";

$ar_dado["espaco_disco_livre"] = 0;
$ar_dado["espaco_disco_usado"] = 0;
foreach($ar_disco_equipamento as $item)
{
	$ar_dado["espaco_disco_livre"]+= $item["espaco_disco_livre"];
	$ar_dado["espaco_disco_usado"]+= $item["espaco_disco_usado"];	
}

foreach($ar_disco_equipamento as $item)
{
	$ar_total = converte_byte($item["espaco_disco_total"],'KB',2);
	$ar_livre = converte_byte($item["espaco_disco_livre"],'KB',2);
	$ar_usado = converte_byte($item["espaco_disco_usado"],'KB',2);
	
	$body[] = array(
		array($item["tipo_equipamento"],"text-align:left;"),
		array(number_format($ar_total[$disco_unidade],2,",","."),'text-align:right;','float'),
		array(number_format($ar_livre[$disco_unidade],2,",","."),'text-align:right;','float'),
		array((round(($item["espaco_disco_livre"] * 100) / $item["espaco_disco_total"])),'text-align:right;'),
		array(number_format($ar_usado[$disco_unidade],2,",","."),'text-align:right;','float'),
		array((round(($item["espaco_disco_usado"] * 100) / $item["espaco_disco_total"])),'text-align:right;')
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
$grid->view_count = false;
echo $grid->render();

$ar_image = $this->charts->pieChart(80,$ar_dado,$ar_titulo,'','Espaço em disco (em uso normal)');	
echo '<center><img src="'.$ar_image['name'].'" border="0"></center>';

echo "<BR>";
#### MEMORIA RAM ####
$body=array();
$head = array( 
	'Mémoria RAM (Estações de trabalho e Notebooks - em uso normal)',
	'Quantidade',
	'%'
);

$qt_memoria_total = 0;
foreach($ar_memoria_equipamento as $item)
{
	$qt_memoria_total += $item["quantidade"];

}
$ar_titulo = Array();
$ar_dado = Array();
$ar_image = Array();
foreach($ar_memoria_equipamento as $item)
{
	$body[] = array(
		array($item["memoria_ram_categoria"],"text-align:left;"),
		array($item["quantidade"],'text-align:right;','int'),
		array((round(($item["quantidade"] * 100) / $qt_memoria_total)),'text-align:right;')
	);
	$ar_titulo[] = $item['memoria_ram_categoria'];
	$ar_dado[] = $item['quantidade'];	
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
$grid->view_count = false;
echo $grid->render();

$ar_image = $this->charts->pieChart(80,$ar_dado,$ar_titulo,'','Memória RAM');	
echo '<center><img src="'.$ar_image['name'].'" border="0"></center>';

echo "<BR>";
#### PROCESSADOR ####
$body=array();
$head = array( 
	'Processador (Estações de trabalho e Notebooks - em uso normal)',
	'Quantidade',
	'%'
);

$qt_processador_total = 0;
foreach($ar_processador_equipamento as $item)
{
	$qt_processador_total += $item["quantidade"];

}
$ar_titulo = Array();
$ar_dado = Array();
$ar_image = Array();
foreach($ar_processador_equipamento as $item)
{
	$body[] = array(
		array($item["processador_categoria"],"text-align:left;"),
		array($item["quantidade"],'text-align:right;','int'),
		array((round(($item["quantidade"] * 100) / $qt_processador_total)),'text-align:right;')
	);
	$ar_titulo[] = $item['processador_categoria'];
	$ar_dado[] = $item['quantidade'];	
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
$grid->view_count = false;
echo $grid->render();

$ar_image = $this->charts->pieChart(80,$ar_dado,$ar_titulo,'','Processador');	
echo '<center><img src="'.$ar_image['name'].'" border="0"></center>';

echo "<BR>";
#### SISTEMA OPERACIONAL ####
$body=array();
$head = array( 
	'Sistema Operacional (Estações de trabalho e Notebooks - em uso normal)',
	'Quantidade',
	'%'
);

$qt_sistema_operacional_total = 0;
foreach($ar_sistema_operacional_equipamento as $item)
{
	$qt_sistema_operacional_total += $item["quantidade"];

}
$ar_titulo = Array();
$ar_dado = Array();
$ar_image = Array();
foreach($ar_sistema_operacional_equipamento as $item)
{
	$body[] = array(
		array($item["sistema_operacional_categoria"],"text-align:left;"),
		array($item["quantidade"],'text-align:right;','int'),
		array((round(($item["quantidade"] * 100) / $qt_sistema_operacional_total)),'text-align:right;')
	);
	$ar_titulo[] = $item['sistema_operacional_categoria'];
	$ar_dado[] = $item['quantidade'];	
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
$grid->view_count = false;
echo $grid->render();

$ar_image = $this->charts->pieChart(80,$ar_dado,$ar_titulo,'','Sistema Operacional');	
echo '<center><img src="'.$ar_image['name'].'" border="0"></center>';

echo "<BR><BR><BR>";
echo aba_end(''); 

$this->load->view('footer_interna');
?>
