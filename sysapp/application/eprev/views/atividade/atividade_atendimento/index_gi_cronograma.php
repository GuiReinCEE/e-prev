<?php
if(count($collection) > 0)
{
	$body = array();
	$head = array('Descrição','');

	foreach($collection as $item)
	{
		$body[] = array(
					array($item['descricao'],"text-align:left;"), 
					( 
						(($fl_salvar == 'S') AND ($fl_teste == 'N') AND (trim($item['dt_encerra']) == ''))
						? 
						'<a href="javascript:cronogramaExcluir('.intval($item['cd_atividade_cronograma']).','.intval($item['cd_atividade_cronograma_item']).'); void(0);">[excluir]</a>'
						:
						""
					)
				);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head       = $head;
	$grid->body       = $body;
	$grid->view_count = false;

	echo $grid->render();
}
?>