<?php
$body=array();
$head = array(
    'Item Súmula',
    'Descrição',
	  'Diretoria',
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

$fl_enviar_tudo = true;

foreach($collection as $item )
{
    $fl_enviar = true;

    if((trim($item["ds_diretoria"]) == "") OR (trim($item["gerencia"]) == "") OR (trim($item["responsavel"]) == "") OR (trim($item["substituto"]) == ""))
    {
      $fl_enviar      = false;
      $fl_enviar_tudo = false;
    }

    $editar = anchor('gestao/sumula_conselho/responsabilidade/'.$item['cd_sumula_conselho'].'/'.$item['cd_sumula_conselho_item'], '[editar]');
    $excluir = '<a href="javascript:void(0);" onclick="excluir('.$item['cd_sumula_conselho_item'].')">[excluir]</a>';
    $enviar = '<a href="javascript:void(0);" onclick="enviar('.$item['cd_sumula_conselho_item'].')">[enviar]</a>';
    
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
    
    $body[] = array(
      $item["nr_sumula_conselho_item"],
      array(nl2br($item["descricao"]), "text-align:justify;"),
	    array($item["ds_diretoria"], "text-align:left;"),
      array($item["gerencia"], "text-align:left;"),
      array($item["responsavel"], "text-align:left;"),
      array($item["substituto"], "text-align:left;"),
      $item['dt_envio'],
      $item['dt_limite'],
      array(nl2br($resposta), "text-align:justify;"),
      $item['dt_resposta'],
      array($item["nome"], "text-align:left;"),
      $editar.' '.(trim($item["dt_envio"]) == '' ? $excluir.' '.($fl_enviar ? $enviar : "") : '')
    );
}

$ar_window = Array(1,8);

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
$grid->col_window = $ar_window;
echo $grid->render();

if(! $fl_enviar_tudo)
{
?>
  <script>
    $(function(){
      $("#enviar_gerentes").hide();
      $("#enviar_emails").hide();

      $("#enviar_info").html('<span class="label label-warning">Informe a Diretoria, Gerência, Resp. e Subst. dos itens  </span>');
    });
  </script>
<?php
}

?>