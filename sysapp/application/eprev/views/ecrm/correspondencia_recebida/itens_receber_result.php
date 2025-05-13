<?php
$body = array();
$head = array(
	'Dt Correspondência ',
	'Origem',
	'Tipo',
	'Identificador',
	'RE',
	'Nome',
	'',
	''
);
	
foreach ($collection as $item)
{		
	$id = "re_".$item['cd_correspondencia_recebida_item'];
	
	$arr_nome = array('id' => 'nome_participante_'.$item['cd_correspondencia_recebida_item'], 'name' => 'nome_participante_'.$item['cd_correspondencia_recebida_item']);

	$solic_fiscalizacao_audit = '';
	
	if(trim($item['dt_recebido']) != '')
	{
		$receber = $item['dt_recebido'];

		if(gerencia_in(array('GRC')))
		{
			$solic_fiscalizacao_audit = anchor('atividade/solic_fiscalizacao_audit/cadastro/0/'.$item['cd_correspondencia_recebida_item'], '[Solicitação de Fiscalização]', array('target' => '_blank'));
		}
	}
	else if(trim($item['dt_recusa']) != '')
	{
		$receber = '<span style="color:red; font-weight:bold;">Recusado por '.trim($item['nome_recusa']).' em '.trim($item['dt_recusa']).'</span>';
		
		if(trim($item['dt_recusa_ok']) == '' AND $this->session->userdata('divisao') == 'GGS')
		{
			$receber .= '<br/><a href="javascript:void(0)" onclick="recusar_ok('.$item['cd_correspondencia_recebida_item'].')">[aceitar]</a>';
		}
		else if(trim($item['dt_recusa_ok']) != '')
		{
			$receber .= '<br/><span style="color:green; font-weight:bold;">(Dt ok: '.trim($item['dt_recusa_ok']).')</span>';
		}
	}
	else
	{
		$receber = '<a href="javascript:void(0)" onclick="receber('.$item['cd_correspondencia_recebida_item'].')" style="color:green">[receber]</a>
		 <a href="javascript:void(0)" onclick="recusar('.$item['cd_correspondencia_recebida_item'].')" style="color:red">[recusar]</a>';
	}
	
	$participante_re[$id.'_cd_empresa']            = $item['cd_empresa'];
	$participante_re[$id.'_cd_registro_empregado'] = $item['cd_registro_empregado'];
	$participante_re[$id.'_seq_dependencia']       = $item['seq_dependencia'];
	
	$check = array(
		'name'  => 'item[]',
		'id'    => 'item[]',
		'value' => $item['cd_correspondencia_recebida_item']
	);

	$body[] = array(
		$item['dt_correspondencia'],
		array($item['origem'],'text-align:left'),
		$item['ds_correspondencia_recebida_tipo'],
		array($item['identificador'],'text-align:left'),
		'<span id="campo_'.$id.'" style="display:none">'.
			form_default_participante(array($id.'_cd_empresa',$id.'_cd_registro_empregado',$id.'_seq_dependencia', $id.'_nome_participante'),'', $participante_re, true, true, 'carregar_dados_participante('.$item['cd_correspondencia_recebida_item'].',emp,re,seq,data);', false).
		 '</span>'.
		 '<span id="span_re_'.$item['cd_correspondencia_recebida_item'].'">'.
			(intval($item['cd_registro_empregado']) != 0 ? $item['cd_empresa'].'/'.$item['cd_registro_empregado'].'/'.$item['seq_dependencia'].' ' : '').
			'<a href="javascript:void(0);" onclick="informar_re('.$item['cd_correspondencia_recebida_item'].');" title="Informar RE">[informar]</a>
		  </span>'.
                 (trim($item['cd_registro_empregado']) != '' ? '<a href="javascript:void(0)" onclick="limpar_re('.$item['cd_correspondencia_recebida_item'].')">[limpar re]</a>' : ''),
		array(((intval($item['cd_registro_empregado']) == 0) ? '' : $item['nome']),'text-align:left'), 
		$receber,
		(((trim($item['dt_recusa']) != "") AND (trim($item['dt_origem']) == "")) ? form_checkbox($check) : "").$solic_fiscalizacao_audit
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render().($this->session->userdata('divisao') == 'GGS' ? '<center>'.button_save('Nova Correspondência', 'nova_correspondeica()', 'botao', 'id="btn_salvar"').'</center>' : '') ;
?>