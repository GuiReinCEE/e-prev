<?php
set_title('Atividades');
$this->load->view('header');

$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

$body = array();
$head = array(
  'Clique num dos itens abaixo para abrir uma nova atividade'
);

foreach($ar_gerencia_abrir_ao_encerrar as $ar_item)
{
	$body[] = array(array('<a href="'.site_url('atividade/atividade_solicitacao/index/'.$ar_item["value"].'/0/'.$cd_empresa.'/'.$cd_registro_empregado.'/'.$seq_dependencia.'/'.$cd_atendimento.'/'.$forma_atendimento).'">'.$ar_item["text"].'</a>', 'text-align:left'));
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
$grid->view_count = false;

echo aba_start( $abas );
	echo $grid->render();
	echo br(3);
echo aba_end();
$this->load->view('footer'); 
?>