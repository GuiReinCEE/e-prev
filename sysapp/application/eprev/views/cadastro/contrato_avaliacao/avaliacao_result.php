<?php
echo '
	<script>
		$(function(){
			if($("#dt_envio_email").val() != "")
			{
				$(".a_excluir_avaliador").hide();
			}';
if(!$fl_mudar_formulario)
{
	echo '$("#cd_contrato_formulario").attr("disabled", "true");';
	
	echo '
		if($("#dt_envio_email").val() == "")
		{
			$("#btn_enviar").show()
		};';
}
else
{
	echo '$("#btn_enviar").hide();';
}
echo '
		});
	</script>';
	
$body = array();
$head = array( 
	'Grupo', 
	'Avaliador'
);

foreach($grupos as $item)
{
	$table = '';
	if(count($item['avaliadores']) > 0)
	{
		$table .= '
			<table id="table-comite" class="sort-table" cellspacing="2" cellpadding="2">
				<tbody>
					<tr>
						<td>
							<b>Gerência</b>
						</td>
						<td>
							<b>Usuário</b>
						</td>
						<td></td>
					<tr>
				';
		foreach($item['avaliadores'] as $item2)
		{
			$table .= '
				<tr onmouseout="sortSetClassOut(this);" onmouseover="sortSetClassOver(this);">
					<td>
						'.$item2['divisao'].'
					</td>
					<td>
						'.$item2['nome'].'
					</td>
					<td align="center">
						<a href="javascript:void(0)" onclick="excluir_avaliador('.$item2['cd_contrato_avaliacao_item'].')" class="a_excluir_avaliador">[excluir]</a>
					</td>
				</tr>';
		}
		$table .= '
				</tbody>
			</table>';
	}
	$body[] = array(
		array(form_hidden("cd_contrato_formulario_grupo_". $item["cd_contrato_formulario_grupo"], $item["cd_contrato_formulario_grupo"]).$item["ds_contrato_formulario_grupo"], 'text-align:left;'), 
		array($table, 'text-align:left;')
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>