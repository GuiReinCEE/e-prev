<?php
$body = array();
$head = array(
  'Código',
  'RE',
  'Participante',
  'Dt Solicitação',
  'Opção'
);

foreach ($collection as $item)
{      
	$opcao = '<thead>
			  <TR>
				<th valing="top">Grupo</th>
				<th valing="top">Opção</th>
			  </TR>
			  </thead>
			  <tbody>';
	
	foreach( $item['opcao'] as $item2 )
	{
		$opcao .= '
			
			<TR>
				<TD valing="top">'.$item2['ds_grupo'].'</TD>
				<TD valing="top"><label style="color:'.$item2['cor'].'; font-weight:bold;">'.$item2['ds_opcao'].'</label></TD>
			</TR>'; 
	}
	
	$opcao .= '</tbody>';

    $body[] = array(
		$item['cd_aa_opcao_envio'],
		$item['re'],
		array($item['nome'], 'text-align:left;'),
		$item['dt_solicitacao'],
		(trim($opcao) != "" ? '<table border="0" class="sort-table">'.trim($opcao)."</table>" : "")
    );
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;

echo $grid->render();
?>

