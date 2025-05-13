<?php
$this->load->helper('grid');

$body=array();
$head = array(
	'Código'
	, 'Nome'
	, 'Trajeto de Vinda'
	, 'Trajeto de Retorno'
    , 'Vagas'
	, 'Caroneiro'
	, ''
);

foreach( $caronas as $item )
{
    $link = '';

    if($this->session->userdata('codigo') != $item['cd_usuario_inclusao'] AND $item['nr_vaga'] > count($arr_caroneiros[$item['cd_carona']]) AND $tl_caronas == 0)
    {
        $link = '<a onclick="entrar_carona('.$item['cd_carona'].')" href="javascript:void(0)">[ENTRAR]</a>';
    }
    
    $caroneiros = '';

    foreach($arr_caroneiros[$item['cd_carona']] as $item2)
    {
        $caroneiros .= $item2['nome'].'<br/>';

        if($item2['cd_usuario_inclusao'] == $this->session->userdata('codigo'))
        {
            $link = '<a onclick="sair_carona('.$item2['cd_carona_caroneiro'].')" href="javascript:void(0)">[SAIR]</a>';
        }
    }

    $cd_carona = $item['cd_carona'];
    $nome = $item['nome'];

    if($this->session->userdata('codigo') == $item['cd_usuario_inclusao'])
    {
        $cd_carona = anchor("servico/carona/cadastro/" . $item['cd_carona'], $item['cd_carona']);
        $nome = anchor("servico/carona/cadastro/" . $item['cd_carona'], $item['nome']);
    }

    $body[] = array(
        $cd_carona,
        array($nome, 'text-align:"left";'),
        array($item['trajeto_vinda'], 'text-align:"left";'),
        array($item['trajeto_retorno'], 'text-align:"left";'),
        $item['nr_vaga'],
        array($caroneiros, 'text-align:"left";'),
        $link,
    );
}


$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>