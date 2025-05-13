<?php
$head = array( 
	'Tabela',
	'Dt. de Alteração',
	'Dt. de Confirmação',
	'Usuário Confirmação',
	'Descrição',
	'Descrição',
	''
);

$body = array();

$i = 1;

foreach($collection as $item)
{	
	$config = array(
		'name'   => 'ds_descricao'.$item['cd_alteracao_tabela_oracle'], 
		'id'     => 'ds_descricao'.$item['cd_alteracao_tabela_oracle'],
		'rows'   => '3',
		'cols'   => '30'
	);

	$body[] = array(
		array($item['tabela'], 'text-align:left;'),
		$item['dt_alteracao'],
		$item['dt_inclusao'],
		$item['usuario_inclusao'],
		array(nl2br($item['ds_descricao']), 'text-align:left;'),
		form_textarea($config, $item['ds_descricao']),	
	   	'<span id="ajax_load_'.$item['cd_alteracao_tabela_oracle'].'"></span>'.
	   	'<a id="descricao_editar_'.$item['cd_alteracao_tabela_oracle'].'" href="javascript: void(0)"  onclick="editar_texto('.$item['cd_alteracao_tabela_oracle'].', '.$i.');" title="Editar a descricao">[editar]</a>'.
	   	'<a id="descricao_salvar_'.$item['cd_alteracao_tabela_oracle'].'" href="javascript: void(0)" style="display:none;" onclick="set_descricao('.$item['cd_alteracao_tabela_oracle'].', '.$i.');" title="Salvar a descricao">[salvar]</a>'	
	);

	$i++;
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;

echo $grid->render();
?>

<script>
	$(function(){
		oculta_coluna(5);
	});
</script>