<?php
$body = array();
$head = array(
    'Dt. Inclusão',
	'RE',
	'Nome',
	'Dt. Óbito',
    'Qtd. Dependente',
    'Qtd. Dependente Contatado',
    'Acompanhamento'
);

foreach ($collection as $item)
{	
    $acompanhamento = '';
    
    foreach ($item['acompanhamento'] as $key => $item2)
    {
        $acompanhamento .= $item2['dt_inclusao'].' : '.$item2['ds_contato_dependente_retorno'].(trim($item2['ds_contato_dependente_acompanhamento']) != '' ? br().trim($item2['ds_contato_dependente_acompanhamento']) : "");
        
        if(isset($item['acompanhamento'][$key+1]))
        {
            $acompanhamento .= '<hr>'.br();
        }
    }
    
	$body[] = array(
        $item['dt_inclusao'],
		anchor("ecrm/contato_dependente/cadastro/".$item["cd_contato_dependente"], $item['re']),
        array(anchor("ecrm/contato_dependente/cadastro/".$item["cd_contato_dependente"], $item['nome']), 'text-align:left;'),
		'<label class="label label-important">'.$item['dt_obito'].'</label>',
        $item['qt_dependente'],
        $item['qt_dependente_contatado'],
        array(nl2br($acompanhamento), "text-align:justify;")
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>