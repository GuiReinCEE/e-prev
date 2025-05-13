<?php
$body=array();
$head=array(
	'Gerência',
	'Avaliado',
	'Período',
	'Tipo de Avaliação',
	'Comitê' 	
);

 	 	 	
foreach( $collection as $item )
{	
	//$comite = '';
	$table = '';
	if(count($item['comite']) > 0)
	{
		$table .= '
			<table id="table-comite" class="sort-table" cellspacing="2" cellpadding="2">
				<tbody>
					<tr>
						<td>
							<b>Integrante</B>
						</td>
						<td>
							<b>Avaliou</b>
						</td>
					<tr>
				';
		foreach($item['comite'] as $item2)
		{
			$table .= '
				<tr onmouseout="sortSetClassOut(this);" onmouseover="sortSetClassOver(this);">
					<td>
						'.$item2['nome'].'
					</td>
					<td align="center">
						'.(($item2['ja_avaliou'] > 0) ? '<font style="color:green; font-weight:bold;">Sim</font>' : '<font style="color:red; font-weight:bold;">Não</font>').'
					</td>
				</tr>';
		}
		$table .= '
				</tbody>
			</table>';
	
	}

	$body[] = array(
		$item['divisao'],
        array($item['avaliado'],'text-align:left;'),
		$item['periodo'],
		array($item['tipo_promocao'],'text-align:center; font-weight:bold; color:'.$item['tipo_promocao_color'].';'),
		array($table, 'text-align:left;')
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>