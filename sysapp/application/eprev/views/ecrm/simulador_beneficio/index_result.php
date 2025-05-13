<?php
$body = array();
$head = array(
	'Plano',
	''
);
	
$id = 0;
foreach ($collection as $item)
{	
	$id_form = 'form_simulador_'.$id;
	$body[] = array(
		array($item['plano'],"text-align:left;"),
		array(
		'
			<table border="0" align="left" class="sort-table">
				<tbody>
				<tr>
					<td valing="top" '.($item['fl_participante'] == true ? "" : ' style="display:none;"').'>
		'
		.(($item['fl_participante'] == true)
			?
			form_default_participante(array('cd_empresa_'.$id,'cd_registro_empregado_'.$id,'seq_dependencia_'.$id, 'nome_'.$id),'', false, true, true, '', false).br(1)
			.form_input(array('name' => 'nome_'.$id, 'id' => 'nome_'.$id), '', 'style="width:300px;"')
			:"")
		.'			
					</td>
					<td valing="top">
						<input type="button" value="Simular" class="botao_verde" onclick="formParticipante('.$id.');">
		'
			.form_open($item["url"], array('id' => $id_form, 'target' => '_blank'))
				.form_input(array('type' => 'hidden', 'name' => "fl_participante_".$id, 'id' => "fl_participante_".$id, 'value' => ($item['fl_participante'] ? "S" : "N")))
				.form_input(array('type' => 'hidden', 'name' => "EMP", 'id' => "EMP"))
				.form_input(array('type' => 'hidden', 'name' => "RE", 'id' => "RE"))
				.form_input(array('type' => 'hidden', 'name' => "SEQ", 'id' => "SEQ"))
			.form_close()			
		.'
					</td>
				</tr>
				</tbody>
			</table>
		 '
		,"text-align:left;")
		
	);
	$id++;
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>