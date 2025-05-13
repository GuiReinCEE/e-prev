<?php
$body = array();
$head = array( 
	'#', $label_0, $label_1, $label_2, $label_3, $label_4, $label_5, ''
);

if(sizeof($tabela)<=0)
{
	echo "N�o foi identificado per�odo aberto para o Indicador";
}
else
{
	echo br();

	$ar_janela = array(
				  'width'      => '700',
				  'height'     => '500',
				  'scrollbars' => 'yes',
				  'status'     => 'yes',
				  'resizable'  => 'yes',
				  'screenx'    => '0',
				  'screeny'    => '0'
				);
			
	echo anchor_popup("indicador/apresentacao/detalhe/".intval( $tabela[0]['cd_indicador_tabela'] ), 'Visualizar apresenta��o', $ar_janela);

	$contador = sizeof($collection);
	
	foreach( $collection as $item )
	{
		$body[] = array(
			$contador--,
			$item['ano_referencia'],
			$item['nr_participante'],
			$item['nr_satisfeito'],
			$item['nr_avaliacao'],
			(trim($item['nr_satisfacao_percentual'] != '') ? number_format($item['nr_satisfacao_percentual'],2,',','.').' %' : ''),
			array($item['observacao'], 'text-align:"left"'),
			(trim($item['fl_editar']) == 'S' ? anchor("indicador_plugin/atend_satisfacao_seminario_seguridade/cadastro/" . $item["cd_atend_satisfacao_seminario_seguridade"], "[editar]") : '') 
		);
	}
	
	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
	echo $grid->render();
}
?>