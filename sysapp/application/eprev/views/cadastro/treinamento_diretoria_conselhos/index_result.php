<?php
$head = array( 
	'Número', 
	'Nome',
	'Promotor',
	'Cidade',
	'UF', 
	'Dt Início',
	'Dt Final',
	'Tipo', 
	'Carga Horária(H)',
	'Qt. Colaboradores'
);

$body = array();

foreach($collection as $item)
{
    $body[] = array(
        anchor('cadastro/treinamento_diretoria_conselhos/cadastro/'.$item['cd_treinamento_diretoria_conselhos'], $item['nr_numero']),
		array(anchor('cadastro/treinamento_diretoria_conselhos/cadastro/'.$item['cd_treinamento_diretoria_conselhos'], $item['ds_nome']), 'text-align:left'),
        array($item['ds_promotor'],'text-align:left'),
        array($item['ds_cidade'],'text-align:left'),
        $item['ds_uf'],
        $item['dt_inicio'],
        $item['dt_final'],
        array($item['ds_treinamento_colaborador_tipo'], 'text-align:left'),
        str_replace('.', ',', $item['nr_carga_horaria']),
        $item['tl_colaborador']        
	);
}	

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;

echo $grid->render();
?>