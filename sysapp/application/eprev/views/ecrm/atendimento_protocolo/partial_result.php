<?php
$head = array( 
    '<input type="checkbox"  id="checkboxCheckAll" onclick="checkAll();" title="Clique para Marcar ou Desmarcar Todos">',
	'',
	'Participante',
    'Nome',
    'Destino',
	'Tipo',
	'Protocolo',
	'Discriminação',
	'Gerência',
	'Remetente',
	'Dt Remetido',
    'Recebido por',
    'Dt Recebido',
    'Dt Cancelado',
    'Dt. Devolvido'
);

$body = array();

foreach( $collection as $item )
{
	$re = $item['cd_empresa'].'/'.$item['cd_registro_empregado'].'/'.$item['seq_dependencia'];

    $botoes = '';
	
	$campo_check = array(
		'name'  => 'part_'.$item['cd_atendimento_protocolo'],
		'id'    => 'part_'.$item['cd_atendimento_protocolo'],
		'value' => $item['cd_atendimento_protocolo']
	);	
	
    if($item['dt_recebimento'] == '' AND $item['dt_cancelamento'] == "" AND ($item['cd_usuario_inclusao'] == $this->session->userdata('codigo')))
    {
        $botoes .= anchor("ecrm/atendimento_protocolo/detalhe/".$item["cd_atendimento_protocolo"], "[editar]");
    }
    else
    {
        $botoes .= anchor("ecrm/atendimento_protocolo/detalhe/".$item["cd_atendimento_protocolo"], "[visualizar]");
    }	

	$checkbox = '';
	
    if($item['dt_recebimento'] == '' AND $item['dt_cancelamento'] == "" AND ($this->session->userdata('divisao') == "GFC"))
    {
        $botoes .= ' <a href="#" style="color:blue" onclick="receber('.$item['cd_atendimento_protocolo'].');">[receber]</a> ';
		$checkbox = form_checkbox($campo_check);
    }

    if(($item['dt_recebimento'] == '' OR $item['dt_devolucao'] != '') AND $item['dt_cancelamento'] == "" AND (($item['cd_usuario_inclusao'] == $this->session->userdata('codigo')) OR ($this->session->userdata('divisao') == "GFC")))
    {
        $botoes .= ' <a href="#" style="color:red" onclick="cancelar('.$item['cd_atendimento_protocolo'].')">[cancelar]</a> ';
    }

    if($item['dt_recebimento'] != '' AND $item['dt_devolucao'] == '' AND $cd_divisao == 'GFC')
    {
        $botoes .= ' <a href="#" style="color:red" onclick="devolver('.$item['cd_atendimento_protocolo'].')">[devolver]</a> ';
    }


	$body[] = array(
	    $checkbox,
		$botoes,
        $re,
        array($item['nome'],"text-align:left;"),
        array($item['ds_destino'],"text-align:left;"),
        $item['tipo_nome'],
        $item['cd_atendimento'],
        array($item['discriminacao_nome']." ". $item['identificacao'],"text-align:left;"),
        $item['cd_gerencia_origem'],
        $item['nome_gap'],
        $item['dt_inclusao'],
        $item['nome_gad'],
        $item['dt_recebimento'],
        $item['dt_cancelamento'],
        $item['dt_devolucao']
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
$grid->id_tabela = "tabela_1";

echo $grid->render();