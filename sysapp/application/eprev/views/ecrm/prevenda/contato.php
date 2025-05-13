<?php
set_title('Pré-venda - Contato');
$this->load->view('header');
?>
<script>
function salvar(ob)
{
	if( $('#dt_pre_venda_contato_data').val()=="" )
	{
		alert( "Informe a Data!" );
		$('#dt_pre_venda_contato_data').focus();
		return false;
	}
	if( $('#dt_pre_venda_contato_hora').val()=="" )
	{
		alert( "Informe a Hora!" );
		$('#dt_pre_venda_contato_hora').focus();
		return false;
	}

	if($('#preenchida').val()=='')
	{
		alert('Informe o preenchimento!');
		$('#preenchida').focus();
		return false;
	}
	
	if( $('#preenchida').val()=='S' && $('#dt_envio_inscricao').val()=='' )
	{
		alert('Informe a data de envio!');
		$('#dt_envio_inscricao').focus();
		return false;
	}

	if( $('#dt_envio_inscricao').val()!='' && !data_valida($('#dt_envio_inscricao').val()) )
	{
		alert('Informe uma data válida para data de envio!');
		$('#dt_envio_inscricao').focus();
		return false;
	}
	
	if( $('#preenchida').val()=='N' && $('#cd_pre_venda_motivo').val()=='' )
	{
		alert('Informe o motivo!');
		$('#cd_pre_venda_motivo').focus();
		return false;
	}
	
	if( $('#cd_pre_venda_local').val()=="" )
	{
		alert( "Informe o local!" );
		$('#cd_pre_venda_local').focus();
		return false;
	}	

	if(confirm("Salvar?"))
	{
		ob.submit();
	}
}

function verifica_preenchida()
{
	if( $('#preenchida').val()=='S' )
	{
		$('#cd_pre_venda_motivo_tr').hide();
		$('#dt_envio_inscricao_tr').show();
		
		$('#cd_pre_venda_motivo').val('');
	}
	if( $('#preenchida').val()=='N' )
	{
		$('#cd_pre_venda_motivo_tr').show();
		$('#dt_envio_inscricao_tr').hide();
		
		$('#dt_envio_inscricao').val('');
	}
	if( $('#preenchida').val()=='' )
	{
		$('#cd_pre_venda_motivo_tr').hide();
		$('#dt_envio_inscricao_tr').hide();
		
		$('#cd_pre_venda_motivo').val('');
		$('#dt_envio_inscricao').val('');
	}
}

function protocolo_interno(cd_pre_venda)
{
	if(confirm("Deseja criar o protocolo?"))
	{
		location.href='<?php echo site_url('ecrm/prevenda/protocolo_interno_contato');?>/' +cd_pre_venda;
	}
}

function configure_result_table()
{
	var ob_resul = new SortableTable(document.getElementById("tb_lista_contatos"),
		[
			"DateTimeBR", 
			"CaseInsensitiveString", 
			"CaseInsensitiveString", 
			"CaseInsensitiveString", 
			"DateBR", 
			"CaseInsensitiveString", 
			"CaseInsensitiveString"
		]);
	ob_resul.onsort = function ()
	{
		var rows = ob_resul.tBody.rows;
		var l = rows.length;
		for (var i = 0; i < l; i++)
		{
			removeClassName( rows[i], i % 2 ? "sort-par" : "sort-impar" );
			addClassName( rows[i], i % 2 ? "sort-impar" : "sort-par" );
		}
	};
	ob_resul.sort(0, false);
}

	function novo()
	{
		location.href = '<?php echo site_url('ecrm/prevenda/abrir'); ?>';
	}

$(function(){
	$('#preenchida').change(
		function()
		{
			verifica_preenchida();
		}
	);
	
	// Carregamento da opção de preenchimento no onload
	verifica_preenchida();
	
	configure_result_table();
});
</script>
<?php
echo form_open('ecrm/prevenda/contato_salvar');
echo form_hidden('cd_pre_venda_contato', intval($record['cd_pre_venda_contato']));
echo form_hidden('cd_pre_venda', intval($record['cd_pre_venda']));

	$link_lista = site_url( 'ecrm/prevenda' );
	$link_cadastro = site_url("ecrm/prevenda/abrir/") . '/' . $record["cd_pre_venda"];
	$link_contato = site_url("ecrm/prevenda/contato/") . '/' . $record["cd_pre_venda"];
	$link_agenda = site_url("ecrm/prevenda/agenda/") . '/' . $record["cd_pre_venda"];

	$abas[0] = array('aba_lista', 'Lista', false, "redir('', '$link_lista')");
	$abas[1] = array('aba_cadastro', 'Cadastro', false, "redir('', '$link_cadastro')");
	$abas[2] = array('aba_contato', 'Contato', true, "redir('', '$link_contato');");
	$abas[3] = array('aba_agenda', 'agenda', false, "redir('', '$link_agenda')");
	$link_relatorio = site_url( 'ecrm/prevenda/relatorio' );
	$abas[4] = array('aba_relatorio', 'Relatório', false, "redir('', '$link_relatorio')");

    echo aba_start( $abas );

    echo form_start_box("cadastro", "Cadastro");

    if($ar_participante['seq_dependencia']=='') $ar_participante['seq_dependencia'] = 0;
    echo form_default_participante(array('cd_empresa','cd_registro_empregado','seq_dependencia', 'nome'), "Participante:", $ar_participante, TRUE, FALSE);
    echo form_default_text("nome", "Nome:", $ar_participante, "style='border: 0px; width:300px' readonly");	
	echo form_default_cpf("cpf", "CPF:", $ar_participante, "style='border: 0px; width:300px' readonly");	
	
    echo form_default_date("dt_pre_venda_contato_data", "Data:", $record);
    echo form_default_hidden("dt_pre_venda_contato_hora", "Hora:", ($record['dt_pre_venda_contato_hora'] == "" ? "00:00" : $record));
	
    $sel = array('');
    if(trim($record['dt_envio_inscricao'])!='')
    {
    	$sel = array('S');
    }
    elseif(intval($record['cd_pre_venda_motivo'])>0)
    {
    	$sel = array('N');
    }	
	
	echo form_default_dropdown("preenchida", "Inscrição preenchida:", array( array('value'=>'S', 'text'=>'Sim'), array('value'=>'N', 'text'=>'Não') ), $sel);
    echo form_default_date("dt_envio_inscricao", "Data de envio GAP:", $record, "dt_envio_inscricao_tr");

    echo form_default_dropdown_db( "cd_pre_venda_motivo", "Motivo:"
	    , array( 'projetos.pre_venda_motivo', 'cd_pre_venda_motivo', 'ds_pre_venda_motivo' )
	    , array($record['cd_pre_venda_motivo'])
	    , ""
	    , "cd_pre_venda_motivo_tr"
	    , TRUE
    );	
	
	
    echo form_default_dropdown_db( "cd_pre_venda_local", "Local:"
	    , array( 'projetos.pre_venda_local', 'cd_pre_venda_local', 'ds_pre_venda_local' )
	    , array($record['cd_pre_venda_local'])
	    , "", "", TRUE
    );	

	echo form_default_dropdown("cd_evento_institucional", "Evento:", $arr_evento, array($record['cd_evento_institucional']));
	
    echo form_default_textarea("observacao", "Observação", $record, "style='width: 400px; height: 50px;'");
    echo form_end_box("cadastro");
    
    echo form_command_bar_detail_start();
    echo button_save("Salvar", "salvar(this.form)");
	echo button_save("Novo", "novo();","botao_amarelo");		

	if(intval($ar_participante['qt_protocolo']) > 0)
	{
		#echo button_save("Protocolo Interno", "protocolo_interno(".$ar_participante['cd_pre_venda'].")", "botao_amarelo");
	}
    $link_voltar = site_url("ecrm/prevenda");
	
	echo form_command_bar_detail_end();

	echo form_start_box("lista", "Contatos");
	$body=array();
	$head = array( 
		'Data',
		'Contato',
		'Local',
		'Evento',
		'Data Envio GAP',
		'Motivo',
		'Observação',
		'',''
	);	
	foreach( $collection as $item )
	{
		$body[] = array(
			$item['dt_pre_venda_contato'],
			array($item['ds_usuario_contato'],"text-align:left;"),
			array($item['ds_pre_venda_local'],"text-align:left;"),
			array($item['ds_evento'],"text-align:left;"),
			$item['dt_envio_inscricao'],
			$item['ds_pre_venda_motivo'],
			array(nl2br($item['observacao']),"text-align:left;"),
			form_list_button_edit( "ecrm/prevenda/contato", $record["cd_pre_venda"].'/'.$item['cd_pre_venda_contato']),
			button_delete("ecrm/prevenda/contato_excluir", $item['cd_pre_venda_contato'])
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->id_tabela = "tb_lista_contatos";
	$grid->body = $body;
	echo $grid->render();	
	echo form_end_box("lista");
	echo form_close();

	if(count($collection_protocolo_interno) > 0)
	{
		$body = array();
		$head = array(
			'Protocolo', 
			'Envio', 
			'Redirecionamento', 
			'Destino', 
			'Encerrado por', 
			'Recebimento', 
			'Participante', 
			'', 
			'Doc', 
			'Tipo de documento', 
			'Folhas', 
			'Arquivo', 
			'Obs do Recebimento'
		);

		foreach ($collection_protocolo_interno as $item)
		{
		    $arquivo = '';
		    if ($item['arquivo'] != '')
		    {
		        $arquivo = anchor(base_url() . 'up/documento_recebido/' . $item['arquivo'], $item['arquivo_nome'], array('target' => '_blank'));
		    }

		    $body[] = array(
		      anchor("ecrm/cadastro_protocolo_interno/detalhe/" . $item["cd_documento_recebido"], $item["nr_documento_recebido"], array('target' => '_blank'))
		      , $item['dt_envio']
		      , $item['dt_redirecionamento']
		      , ( ($item['nome_grupo'] != '') ? $item['nome_grupo'] : $item['divisao_usuario_destino'] . '-' . $item['guerra_usuario_destino'] )
		      , $item['usuario_encerrado']
		      , $item['dt_ok']
		      , $item['cd_empresa'] . '/' . $item['cd_registro_empregado'] . '/' . $item['seq_dependencia']
		      , $item['nome_participante']
		      , $item['cd_tipo_doc']
		      , $item['nome_documento']
		      , $item['nr_folha']
		      , $arquivo
		      , $item['ds_observacao_recebimento']
		    );
		}

		$this->load->helper('grid');
		$grid = new grid();
		$grid->head = $head;
		$grid->body = $body;
		echo form_start_box('protocolo_interno', 'Protocolo Interno');
		echo $grid->render();
		echo form_end_box('protocolo_interno');
	}

echo aba_end( 'cadastro');
$this->load->view('footer');
?>