<?php
$body=array();
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
	'Qt. Colaboradores',
	'Qt. Avaliações',
	'Qt. Aval. Finalizadas',
	'Subsidio FCEEE',
    'Bem-Estar'

);

foreach( $collection as $item )
{
    $body[] = array(
        anchor('cadastro/treinamento_colaborador/cadastro/'.$item['numero'], $item['numero']),
		array(anchor('cadastro/treinamento_colaborador/cadastro/'.$item['numero'], $item['nome']),'text-align:left'),
        array($item['promotor'],'text-align:left'),
        array($item['cidade'],'text-align:left'),
        $item['uf'],
        $item['dt_inicio'],
        $item['dt_final'],
        array($item['ds_treinamento_colaborador_tipo'], 'text-align:left'),
        str_replace('.', ',', $item['carga_horaria']),
        $item['tl_colaborador'],
        array(
        	(intval($item['tl_avaliacao_c']) > 0 ? 'Colaborador : '. $item['tl_avaliacao_c'] : '').
        	(intval($item['tl_avaliacao_g']) > 0 ? br().'Gestor : '. $item['tl_avaliacao_g'] : ''),
        'text-align:left'),
		array(
        	(intval($item['tl_avaliacao_c']) > 0 ? 'Colaborador : '. $item['tl_avaliacao_finalizada_c'] : '').
        	(intval($item['tl_avaliacao_g']) > 0 ? br().'Gestor : '. $item['tl_avaliacao_finalizada_g'] : ''),
        'text-align:left'),
        $item['ds_subsidio_fundacao'],
        $item['fl_bem_estar']
        
	);
}	

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>