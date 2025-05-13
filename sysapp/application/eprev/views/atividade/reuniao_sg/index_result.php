<?php
$body=array();
$head = array( 
    'Nº da Reunião',
    'Instituição',
    'Pauta',
    'Dt Solicitação',
    'Solicitante',
    'Situação',
    'Arquivo',
    'Dt Reunião',
    'Secretaria',
    'Parecer',
    'Dt Encerrado '
);

foreach( $collection as $item )
{
    $situacao = "";
    switch ($item["fl_confirma"]) {
            case "S":
                    $situacao = array("Confirmada","text-align:center; font-weight:bold; color:blue;");
                    break;
            case "N":
                    $situacao = array("Não Confirmada","text-align:center; font-weight:bold; color:red;");
                    break;
            case "A":
                    $situacao = array("Aguardando Confirmação","text-align:center; font-weight:bold; color:green;");
                    break;
    }

    $body[] = array(
        anchor("atividade/reuniao_sg/detalhe/".$item["cd_reuniao_sg"], $item["cd_reuniao_sg"]),
        array(anchor("atividade/reuniao_sg/detalhe/".$item["cd_reuniao_sg"],$item["ds_reuniao_sg_instituicao"]),"text-align:left;"),
        array("<div style='width:500px;'>" .anchor("atividade/reuniao_sg/detalhe/".$item["cd_reuniao_sg"],$item["pauta"]). "</div>","text-align:justify;"),
        $item["dt_inclusao"],
        array($item["usuario_cadastro"],"text-align:left;"),
        $situacao,
       (trim($item['arquivo']) != "" ? '<a href="' . base_url() . 'up/reuniao_sg/' . $item['arquivo'] . '" target="_blank">' . $item['arquivo_nome'] . '</a>' : ''),
        array($item["dt_reuniao"],"text-align:center; font-weight:bold; color:blue;"),
        array($item["usuario_confirma"],"text-align:left;"),
        ($item["parecer"] == 'S' ? array("Sim","text-align:center; font-weight:bold; color:blue;") : array("Não","text-align:center; font-weight:bold; color:red;")),
        $item["dt_encerrado"]
    );
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>