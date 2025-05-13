<?php
$body=array();
$head = array( 
	'Código',
    'Produto',
	'Status',
	'Dt Atualização',
	'Dt Recebido',
    'Dt Conclusão'
);

foreach( $collection as $item )
{
	$etapa = '';

	foreach( $item['etapas'] as $item2 )
	{
		$etapa .= '
					<TR>
						<TD valing="top">'.$item2['nr_ordem'].'</TD>
						<TD valing="top">'.$item2['ds_produto_financeiro_etapa'].':</TD>
						<TD valing="top">'.progressbar(intval($item2['nr_concluido'])).'</TD>
						<TD valing="top">'.(trim($item2['observacao']) != "" ? '<a href="javascript:void(0)" onclick="$(\'#obs_etapa_'.$item2['cd_produto_financeiro_etapa_status'].'\').toggle();">[observação]</a>' : "&nbsp;").'</TD>
						<TD valing="top" id="obs_etapa_'.$item2['cd_produto_financeiro_etapa_status'].'" style="display:none;" align="left">'.(trim($item2['observacao']) != "" ? trim($item2['observacao']) : "&nbsp;").'</TD>
					</TR>
				  '; 
	}
	
    $body[] = array(
		anchor("atividade/produto_financeiro/cadastro/" . $item['cd_produto_financeiro'], $item['cd_produto_financeiro']),
		
		array(
			anchor("atividade/produto_financeiro/cadastro/" . $item["cd_produto_financeiro"], $item["ds_produto"]).
			br(2).
		'<div>
		
		Entidade/Fornecedor : '.trim($item['ds_reuniao_sg_instituicao']).'<br/>
		Responsável         : '.trim($item['responsavel']).'<br/>
		Revisor             : '.trim($item['revisor']).'
		
		</div>'			
		
		,"text-align:left;"),
		'
		<table border="0" class="sort-table">
			<TR>
				<TD>Total</TD>
				<TD>'.progressbar(intval($item['nr_concluido'])).'</TD>
			</TR>
			<TR>
				<TD colspan="2">
				<div><a href="javascript:void(0)" onclick="$(\'#etapa_'.$item['cd_produto_financeiro'].'\').toggle();" >[etapas]</a></div>
				<div id="etapa_'.$item['cd_produto_financeiro'].'" style="display:none;">
				'
				.(trim($etapa) != "" ? '<table border="0" class="sort-table">'.trim($etapa)."</table>" : "").
				'
				</div>
			</TR>
		</table>
		',
		$item['dt_atualizacao'],
		$item['dt_recebido'],
		$item['dt_conclusao']		
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>
