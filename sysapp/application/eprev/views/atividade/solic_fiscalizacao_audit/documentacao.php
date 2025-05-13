<?php
	set_title('Registro de Solicitações, Fiscalizações e Auditorias');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array(
		'nr_item',
		'ds_solic_fiscalizacao_audit_documentacao',
		'cd_gerencia',
		'dt_prazo_retorno',
		'dt_prazo_atendimento'
	)) ?>

	function ir_lista()
    {
        location.href = "<?= site_url('atividade/solic_fiscalizacao_audit') ?>";
    }

    function ir_cadastro()
    {
    	location.href = "<?= site_url('atividade/solic_fiscalizacao_audit/cadastro/'.intval($row['cd_solic_fiscalizacao_audit'])) ?>";
    }

    function ir_prorrogacao()
    {
    	location.href = "<?= site_url('atividade/solic_fiscalizacao_audit/prorrogacao/'.intval($row['cd_solic_fiscalizacao_audit'])) ?>";
    }

    function ir_acompanhamento()
    {
    	location.href = "<?= site_url('atividade/solic_fiscalizacao_audit/acompanhamento/'.intval($row['cd_solic_fiscalizacao_audit'])) ?>";
    }

    function cancelar()
    {
    	location.href = "<?= site_url('atividade/solic_fiscalizacao_audit/documentacao/'.intval($row['cd_solic_fiscalizacao_audit'])) ?>";
    }

    function excluir()
    {
    	var confirmacao = 'Deseja excluir o item?\n\n'+
            'Clique [Ok] para Sim\n\n'+
            'Clique [Cancelar] para Não\n\n';

       	if(confirm(confirmacao))
	    {
        	location.href = "<?= site_url('atividade/solic_fiscalizacao_audit/excluir_documentacao/'.intval($row['cd_solic_fiscalizacao_audit']).'/'.intval($documentacao['cd_solic_fiscalizacao_audit_documentacao'])) ?>";
        }
    }

    function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),
		[
			null,
			"CaseInsensitiveString",
			"CaseInsensitiveString",
			null,
			"DateBR",
			"DateBR",
			"DateTimeBR",
			null,
			null,
			null
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
		ob_resul.sort(1, false);
	}

	function get_usuario(cd_gerencia)
	{
		$.post("<?= site_url('atividade/solic_fiscalizacao_audit/get_usuario') ?>",
		{
			cd_gerencia : cd_gerencia
		},
		function(data)
		{
			$("#usuario_table").html("");

			var tabela = "";

			$.each(data, function(val, text) {
                tabela = tabela + '<tr><td><input name="usuario[]" value="'+text.value+'" id="usuario" type="checkbox"></td><td><label class="label-padrao-form">'+text.text+'</label> </td></tr>';
            });

			$("#usuario_table").html(tabela);

			console.log(data);
		}, "json");
	}

	function enviar_solicitacao()
	{
		var ipts  = $("#table-1 > tbody").find("input:checkbox:checked");
		
		var solic_fiscalizacao_audit_documentacao = [];
	
		ipts.each(function() {
			solic_fiscalizacao_audit_documentacao.push($(this).val());
		});

		if(solic_fiscalizacao_audit_documentacao.length > 0)
		{
			var confirmacao = 'Deseja encaminhar o e-mail com as solicitações de documento?\n\n'+
	            'Clique [Ok] para Sim\n\n'+
	            'Clique [Cancelar] para Não\n\n';

	        if(confirm(confirmacao))
	        { 
	        	$("#solic_fiscalizacao_audit_documentacao").val(solic_fiscalizacao_audit_documentacao);

	            form_documentacao.method = "post";
		        form_documentacao.action = "<?= site_url('atividade/solic_fiscalizacao_audit/enviar_solicitacao') ?>";
		        form_documentacao.target = "_self";
		        form_documentacao.submit();
	        }
		}
		else
		{
			alert('Selecione no mínimo uma solicitação');
		}
		
	}

	function atendeu(cd_solic_fiscalizacao_audit_documentacao)
	{
		var confirmacao = 'Solicitação foi atendida?\n\n'+
		    'Deseja concluir item?\n\n'+
            'Clique [Ok] para Sim\n\n'+
            'Clique [Cancelar] para Não\n\n';

        if(confirm(confirmacao))
        { 
            location.href = "<?= site_url('atividade/solic_fiscalizacao_audit/atendeu/'.$row['cd_solic_fiscalizacao_audit']) ?>/"+cd_solic_fiscalizacao_audit_documentacao;
        }
	}

    function nao_atendeu(cd_solic_fiscalizacao_audit_documentacao)
    {
        location.href = "<?= site_url('atividade/solic_fiscalizacao_audit/atendimento/'.$row['cd_solic_fiscalizacao_audit']) ?>/"+cd_solic_fiscalizacao_audit_documentacao;
    }

    function encerra_documentacao()
    {
        var confirmacao = 'Deseja encerrar esta documentação e encaminhar para a GRC?\n\n'+
                          'Clique [Ok] para Sim\n\n'+
                          'Clique [Cancelar] para Não\n\n';
        
        if(confirm(confirmacao))
        {
            location.href = "<?= site_url('atividade/solic_fiscalizacao_audit/encerra_documentacao/'.$row['cd_solic_fiscalizacao_audit']) ?>";
        }
    }

    function reabrir_documentacao()
    {
        var confirmacao = 'Deseja reabrir esta documentação?\n\n'+
                          'Clique [Ok] para Sim\n\n'+
                          'Clique [Cancelar] para Não\n\n';
        
        if(confirm(confirmacao))
        {
            location.href = "<?= site_url('atividade/solic_fiscalizacao_audit/reabrir_documentacao/'.$row['cd_solic_fiscalizacao_audit']) ?>";
        }
    }

    function reabrir_atendimento(cd_solic_fiscalizacao_audit_documentacao)
    {
    	var confirmacao = 'Deseja reabrir esse item?\n\n'+
                          'Clique [Ok] para Sim\n\n'+
                          'Clique [Cancelar] para Não\n\n';
        
        if(confirm(confirmacao))
        {
            location.href = "<?= site_url('atividade/solic_fiscalizacao_audit/reabrir_atendimento/'.$row['cd_solic_fiscalizacao_audit']) ?>/"+cd_solic_fiscalizacao_audit_documentacao;
        }
    }

    function checkAll()
    {
        var ipts = $("#table-1 > tbody").find("input:checkbox");
        var check = document.getElementById("checkboxCheckAll");

        jQuery.each(ipts, function(){
            this.checked = check.checked ? true : false;
        });
    }

	$(function(){
		configure_result_table();
	});
</script>
<style>
    #artigo_item {
        white-space:normal !important;
    }
</style>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_cadastro', 'Cadastro', FALSE, 'ir_cadastro();');
	$abas[] = array('aba_prorrogacao', 'Prorrogação de Prazo', FALSE, 'ir_prorrogacao();');
	$abas[] = array('aba_acompanhamento', 'Acompanhamento', FALSE, 'ir_acompanhamento();');
	$abas[] = array('aba_documentacao', 'Documentação/Informação', TRUE, 'location.reload();');

	$head = array(
		'<input type="checkbox" id="checkboxCheckAll" onclick="checkAll();" title="Clique para Marcar ou Desmarcar Todos">',
		'Item',
		'Descrição Resumida',
		'Responsável',
		'Val. Competência',
		'Dt. Retorno Solicitante',
		'Dt. Atendimento Resp.',
        'Atendeu',
        'Motivo',
		'Dt. Atendimento',
		'Qt. Doc.',
		''
	);

	$body = array();

	foreach ($collection as $item)
	{
		$link = nl2br($item['ds_solic_fiscalizacao_audit_documentacao']);

		if(trim($row['cd_gerencia']) == trim($this->session->userdata('divisao')) AND trim($item['dt_atendimento']) == '')
		{
			$link = anchor('atividade/solic_fiscalizacao_audit/documentacao/'.$row['cd_solic_fiscalizacao_audit'].'/'.$item['cd_solic_fiscalizacao_audit_documentacao'], nl2br($item['ds_solic_fiscalizacao_audit_documentacao']));
		}

		$atendeu = '';

		if(trim($row['cd_gerencia']) == trim($this->session->userdata('divisao')) AND trim($item['dt_atendimento_responsavel']) != '' AND trim($item['dt_atendimento']) == '')
		{
			$atendeu = '<a href="javascript:void(0)" style="color:green;" onclick="atendeu('.$item['cd_solic_fiscalizacao_audit_documentacao'].')">[SIM]</a> '.
		    	'<a href="javascript:void(0)" style="color:red;" onclick="nao_atendeu('.$item['cd_solic_fiscalizacao_audit_documentacao'].')">[NÃO]</a> ';
		}
		else if(trim($item['fl_atendeu']) == 'S')
		{
			$atendeu = '<span class="label label-success">'.$item['ds_atendeu'].'</span>';
		}
		else if(trim($item['fl_atendeu']) == 'N')
		{
			$atendeu = '<span class="label label-important">'.$item['ds_atendeu'].'</span>';
		}


		if(
			trim($row['cd_gerencia']) == trim($this->session->userdata('divisao')) 
			AND 
			!$fl_atendimento 
			AND 
			(intval($row['tl_documento_encerramento']) > 0 OR intval($row['tl_documento']) == 0)
			AND 
			trim($item['dt_atendimento']) != ''
		)
		{
			$atendeu .= br().'<a href="javascript:void(0)" onclick="reabrir_atendimento('.$item['cd_solic_fiscalizacao_audit_documentacao'].')">[REABRIR]</a>';
		}

		$fl_encaminhar = FALSE;

		if((trim($item['fl_atendeu']) != 'N') AND trim($item['dt_atendimento_responsavel']) == '')
		{
			$fl_encaminhar = TRUE;
		}

		$check = array(
			'name'    => 'cd_solic_fiscalizacao_audit_documentacao_'.$item['cd_solic_fiscalizacao_audit_documentacao'],
			'id'      => 'cd_solic_fiscalizacao_audit_documentacao_'.$item['cd_solic_fiscalizacao_audit_documentacao'],
			'value'   => $item['cd_solic_fiscalizacao_audit_documentacao'],
			'checked' => (trim($item['dt_envio_solicitacao']) == '' ? TRUE : FALSE)
		);

	  	$body[] = array(
	  		($fl_encaminhar ? form_checkbox($check) : ""),
		    $item['nr_item'],
		    array($link, 'text-align:left;'),
		    implode(br(), $item['responsavel']),
		    $item['ds_verificação_gerencia'].(count($item['gerencia_apoio']) > 0 ? br().'Apoio: '.implode(', ', $item['gerencia_apoio']) : ''),
		    $item['dt_prazo_retorno'],
		    $item['dt_atendimento_responsavel'],
            $atendeu,
            array(nl2br($item['ds_motivo_atendeu']), 'test-align:justify'),
		    $item['dt_atendimento'],
		    '<span class="badge badge-success">'.$item['qt_doc'].'</span>',
		    anchor('atividade/solic_fiscalizacao_audit/documentos/'.$row['cd_solic_fiscalizacao_audit'].'/'.$item['cd_solic_fiscalizacao_audit_documentacao'], '[documentos]').' '.
		    anchor('atividade/solic_fiscalizacao_audit/zip/'.$row['cd_solic_fiscalizacao_audit'].'/'.$item['cd_solic_fiscalizacao_audit_documentacao'], '[zip]', 'target="_blank"').' '.
		    anchor('atividade/solic_fiscalizacao_audit/zip_multiplo/'.$row['cd_solic_fiscalizacao_audit'].'/'.$item['cd_solic_fiscalizacao_audit_documentacao'], '[zip multiplo]', 'target="_blank"') 

		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo aba_start($abas);
		echo form_start_box('default_solicitacao_box', 'Solicitação');
			echo form_default_row('', 'Ano/Nº:', '<span class="label label-inverse">'.$row['ds_ano_numero'].'</i>');
			echo form_default_row('', 'Origem:', $row['ds_solic_fiscalizacao_audit_origem'].(trim($row['ds_origem']) != '' ? ' ('.$row['ds_origem'].')' : ''));
			echo form_default_row('', 'Data Recebimento:', $row['dt_recebimento']);
			echo form_default_row('', 'Tipo:', $row['ds_solic_fiscalizacao_audit_tipo'].(trim($row['ds_tipo']) != '' ? ' ('.$row['ds_tipo'].')' : ''));
			echo form_default_row('', 'Área Consolidadora:', $row['cd_gerencia']);
			
			if(count($row['gestao']) > 0)
			{
				echo form_default_row('', 'Gestão:', implode(', ', $row['gestao']));
			}

			echo form_default_row('', 'Documento:', $row['ds_documento']);
			echo form_default_row('', 'Teor:', $row['ds_teor']);
			echo form_default_row('', 'Dt. Prazo:', $row['dt_prazo']);
			echo form_default_row('', 'Dt. Inclusão:', $row['dt_inclusao']);
			echo form_default_row('', 'Usuário:', $row['ds_usuario_inclusao']);
			echo form_default_row('', 'Dt. Envio:', $row['dt_envio']);
			echo form_default_row('', 'Usuário:', $row['ds_usuario_envio']);

			if(trim($row['dt_envio_solicitacao_documento']) != '')
			{
				echo form_default_row('', 'Dt. Envio Solicitação:', $row['dt_envio_solicitacao_documento']);
				echo form_default_row('', 'Usuário:', $row['ds_usuario_envio_solicitacao_documento']);
			}
			
		echo form_end_box('default_solicitacao_box');

		if($fl_atendimento)
	    {
	        echo form_command_bar_detail_start();
	            echo button_save('Encerrar Documentação', 'encerra_documentacao('.$documentacao['cd_solic_fiscalizacao_audit_documentacao'].')', 'botao_verde');
	        echo form_command_bar_detail_end();
	    }
	    else if(trim($row['dt_envio_atendimento']) == '' AND trim($row['cd_gerencia']) == trim($this->session->userdata('divisao')))
	    {
	    	echo form_command_bar_detail_start();
	            echo button_save('Reabrir Documentação', 'reabrir_documentacao('.$documentacao['cd_solic_fiscalizacao_audit_documentacao'].')', 'botao_vermelho');
	        echo form_command_bar_detail_end();
	    }

		if(intval($row['tl_documento_encerramento']) > 0 OR trim($row['dt_envio_atendimento']) == '')
		{
			echo form_open('atividade/solic_fiscalizacao_audit/salvar_documentacao', 'id="form_documentacao"');
				echo form_start_box('default_documentacao_box', 'Documentação');
					echo form_default_hidden('solic_fiscalizacao_audit_documentacao', '', '');
					echo form_default_hidden('cd_solic_fiscalizacao_audit', '', $row['cd_solic_fiscalizacao_audit']);
					echo form_default_hidden('cd_solic_fiscalizacao_audit_documentacao', '', $documentacao['cd_solic_fiscalizacao_audit_documentacao']);
					echo form_default_text('nr_item', 'Nº Item: (*)', $documentacao);
					echo form_default_text('ds_solic_fiscalizacao_audit_documentacao', 'Descrição Resumida: (*)', $documentacao, 'style="width:500px;"');
					echo form_default_dropdown('cd_gerencia', 'Gerência Responsável: (*)', $gerencia, $documentacao['cd_gerencia'], 'onchange="get_usuario($(this).val());"');
					echo form_default_checkbox_group('usuario', 'Responsável:', $usuario, $usuario_responsavel, 150, 350);
					echo form_default_date('dt_prazo_retorno', 'Prazo Retorno Solicitante: (*)', $documentacao);
				echo form_end_box('default_documentacao_box');
				echo form_command_bar_detail_start();
					echo button_save('Salvar');  

					if(intval($documentacao['cd_solic_fiscalizacao_audit_documentacao']) > 0 AND trim($row['cd_gerencia']) == trim($this->session->userdata('divisao')) AND trim($documentacao['dt_atendimento']) == '')
					{
						echo button_save('Excluir', 'excluir()', 'botao_vermelho');
					}
					
					if(intval($documentacao['cd_solic_fiscalizacao_audit_documentacao']) > 0)
					{
						echo button_save('Cancelar', 'cancelar()', 'botao_disabled');
					}

					if(!$fl_atendimento AND count($collection) > 0)
					{
						echo button_save('Enviar Solicitação', 'enviar_solicitacao();', 'botao_verde');
					}

		    	echo form_command_bar_detail_end();
			echo form_close();
		}
		echo br();
		echo $grid->render();

	echo br(2);
    echo aba_end();

    $this->load->view('footer_interna');
?>