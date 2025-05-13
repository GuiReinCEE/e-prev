<?php
    $head = array(
        'Ano/mês',
        'Auditoria',
        'Escopo',
    	'Processo Auditados',
    	#'Auditor Lider',
        'Empresa',
    	'Equipe',
        'Dt Inclusão',
        ''
    );

    $body = array();

    foreach($collection as $item)
    {
        $processos="";

    	foreach($ar_processo[$item["cd_relatorio_auditoria"]] as $item2)
    	{
    		$processos.=nl2br($item2['procedimento']).br(2);
    	}
        
        $equipe="";

    	foreach($ar_equipe[$item["cd_relatorio_auditoria"]] as $item3)
    	{
    		$equipe.=nl2br($item3['nome']).br(2);
    	}
        
        $body[] = array(
            anchor("gestao/relatorio_auditoria/cadastro/".$item["cd_relatorio_auditoria"], $item['ano_mes']),
            anchor("gestao/relatorio_auditoria/cadastro/".$item["cd_relatorio_auditoria"], $item['ds_tipo']),
            array(anchor("gestao/relatorio_auditoria/cadastro/".$item["cd_relatorio_auditoria"], $item['escopo']),'text-align:justify;'),
            array($processos,'text-align:left;'),
           # $item["auditor_lider"],
            array($item['ds_empresa'],'text-align:left;'),
            array($equipe,'text-align:left;'),
            $item['dt_inclusao'],
            ($fl_permissao ? '<a href="javascript:void(0);" onclick="excluir('.$item["cd_relatorio_auditoria"].')">[Excluir]</a>' : '').
            '<a href="javascript:void(0);" onclick="gera_pdf('.$item["cd_relatorio_auditoria"].')">[Imprimir]</a>'
    	);

    }

    $this->load->helper('grid');
    $grid = new grid();
    $grid->head = $head;
    $grid->body = $body;

    echo $grid->render();
?>