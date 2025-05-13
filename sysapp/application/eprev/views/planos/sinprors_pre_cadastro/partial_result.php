<?php

$body = array();
$head = array(
  'Data', 'Nome', 'Email', 'Telefone', 'Matrícula', 'CPF', 'Nascimento', 'Dúvida', 'Enviado', 'Acompanhamento'
);

foreach ($collection as $item)
{

    $enviado = '';
    
    if($item["cd_enviado"] == 'A')
    {
        $enviado = 'Amauri Bueno';
    }
    else if($item["cd_enviado"] == 'M')
    {
        $enviado = 'Mongeral';
    }
        

    $body[] = array(
      $item["dt_inclusao"]
      , array(anchor("planos/sinprors_pre_cadastro/cadastro/" . $item["cd_pre_cadastro"], $item["ds_nome"]), 'text-align:left;')
      , $item["ds_email"]
      , $item["nr_telefone"]
      , $item["nr_matricula"]
      , $item["nr_cpf"]
      , $item["dt_nascimento"]
      , array($item["ds_duvida"], 'text-align:left;')
      , $enviado
      , array($item["observacao"], 'text-align:justify;')
    );
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>