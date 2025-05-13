<?php
$body = array();
$head = array( 
	'#', $label_0, $label_1, $label_2, $label_3, ''
);

$ar_janela = array(
	'width'      => '700',
	'height'     => '500',
	'scrollbars' => 'yes',
	'status'     => 'yes',
	'resizable'  => 'yes',
	'screenx'    => '0',
	'screeny'    => '0'
);

$contador = sizeof($collection);

foreach($collection as $key => $item)
{
	$body[] = array(
		$contador--,
		$item['ano_referencia'],
		(trim($item['nr_resultado']) != '' ? ($item['nr_resultado'] == 1 ? 'Sim' : 'Não') : ''),
		(trim($item['nr_meta']) != '' ? ($item['nr_meta'] == 1 ? 'Sim' : 'Não') : ''),
		array(nl2br($item['observacao']), 'text-align:justify'),
		anchor('indicador_plugin/controladoria_certificado_iso/cadastro/'.$item['cd_controladoria_certificado_iso'],  '[editar]')
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
$grid->view_count = false;
?>

<div style="text-align:center;"> 
	<?= anchor_popup('indicador/apresentacao/detalhe/'.intval($tabela[0]['cd_indicador_tabela']), '[Visualizar Apresentação]', $ar_janela) ?>
</div>
<br/>
<?= $grid->render() ?>