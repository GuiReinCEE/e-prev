<?php
$body=array();
$head = array(
    '<input type="checkbox"  id="checkboxCheckAll" onclick="checkAll();" title="Clique para Marcar ou Desmarcar Todos">',
    'Item Súmula',
    'Descrição',
    'Gerência',
    'Resp.',
    'Subst.',
    'Dt Envio',
    'Dt Limite',
    'Resposta',
    'Dt Reposta',
    'Resposta por',
    ''
);

foreach($collection as $item )
{
    $editar = anchor('gestao/sumula_interventor/responsabilidade/'.$item['cd_sumula_interventor'].'/'.$item['cd_sumula_interventor_item'], '[editar]');
    $excluir = '<a href="javascript:void(0);" onclick="excluir('.$item['cd_sumula_interventor_item'].')">[excluir]</a>';
    $enviar = '<a href="javascript:void(0);" onclick="enviar('.$item['cd_sumula_interventor_item'].')">[enviar]</a>';
    
    $resposta = '';
    
    if($item['cd_resposta'] == 'AP')
    {
        $resposta = 'Ação Preventiva';
    }
    else if($item['cd_resposta'] == 'NC')
    {
        $resposta = 'Não Conforminadade';
    }
    else if($item['cd_resposta'] == 'SR')
    {
        $resposta = 'Sem Reflexo';
    }
    else if($item['cd_resposta'] == 'SP')
    {
        $resposta = 'Sem Reflexo - Plano de Ação';
    }

    if($item['complemento'] != '')
    {
        $resposta .= ': '.$item['complemento'];
    }

    $checkbox = array(
      'name'  => 'check_'.$item['cd_sumula_interventor_item'],
      'id'    => 'check_'.$item['cd_sumula_interventor_item'],
      'value' => $item['cd_sumula_interventor_item'],
    );
    
    $body[] = array(
      form_checkbox($checkbox),
      $item['nr_sumula_interventor_item'],
      array(nl2br($item["descricao"]), "text-align:justify;"),
      array($item["gerencia"], "text-align:left;"),
      array($item["responsavel"], "text-align:left;"),
      array($item["substituto"], "text-align:left;"),
      $item['dt_envio'],
      $item['dt_limite'],
      array(nl2br($resposta), "text-align:justify;"),
      $item['dt_resposta'],
      array($item["nome"], "text-align:left;"),
      $editar.' '.(trim($item["dt_envio"]) == '' ? $excluir.' '.$enviar : '')
    );
}

$ar_window = Array(1,7);

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
$grid->col_window = $ar_window;
echo $grid->render();

?>