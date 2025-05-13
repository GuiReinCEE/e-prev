<?php
$body = array();
$head = array( 
	'<input type="checkbox"  id="checkboxCheckAll" onclick="checkAll();" title="Clique para Marcar ou Desmarcar Todos">',
	'Nome',
	'CPF',
	'Idade',
	'Qt Dependente',
	'Vl Renda (R$)',
	'Endereço',
	'Bairro',
	'Cidade',
	'CEP',
	'UP',
	'Telefone',
	'Email',
	'Delegacia',
	'Origem'
);

$i = 0;

foreach( $collection as $item )
{
	$campo_check = array(
		'name'    => 'campanha_'.$i,
		'id'      => 'campanha_'.$i,
		'value'   => $item['cd_campanha_venda_item'],
		'checked' => (intval($item['cd_campanha_venda_new']) == intval($item['cd_campanha_venda']) ? true : false), 
		'onchange' => 'salvar_item($(this), \''.$item['cpf'].'\', \''.$item['origem'].'\', \''.intval($item['cd_origem']).'\', \''.intval($item['cd_campanha_venda_new']).'\')',
		'contador' => $i
	);	

	$body[] = array(
		'<input type="hidden" id="cpf_'.$i.'" value="'.$item['cpf'].'">'.
		'<input type="hidden" id="ds_origem_'.$i.'" value="'.$item['origem'].'">'.
		'<input type="hidden" id="cd_origem_'.$i.'" value="'.$item['cd_origem'].'">'.
		'<input type="hidden" id="cd_campanha_venda_new_'.$i.'" value="'.intval($item['cd_campanha_venda_new']).'">'.
		(((trim($item['fl_edita_campanha']) > 0) AND (trim($row['dt_fechamento'])  == ''))  ? form_checkbox($campo_check) : (intval($item['cd_campanha_venda_new']) == intval($item['cd_campanha_venda']) ? '<span class="label label-inverse">Sim</span>' : '<span class="label">Não</span>')),
		array($item['nome'], 'text-align:left;'),
		$item["cpf"],
		$item["nr_idade"],
		$item["qt_dependente"],
		array(number_format($item["vl_renda"],2,",","."), 'text-align:right;'),
		array($item["endereco"], 'text-align:left;'),
		array($item["bairro"], 'text-align:left;'),
		array($item["cidade"], 'text-align:left;'),
		$item["cep"],
		$item["uf"],
		$item["telefone_1"].(trim($item['telefone_2']) != '' ? br().$item['telefone_2'] : ''),
		$item["email_1"].(trim($item['email_2']) != '' ? br().$item['email_2'] : ''),
		array($item["delegacia"],  'text-align:left;'),
		array($item["origem"],  'text-align:left;')
	);
	
	$i++;
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;

if(trim($row['dt_fechamento']) == '')
{
	echo br(2).button_save("Fechar Público Campanha", 'fechar('.intval($row['cd_campanha_venda']).');', 'botao_vermelho');
}

echo $grid->render();

?>