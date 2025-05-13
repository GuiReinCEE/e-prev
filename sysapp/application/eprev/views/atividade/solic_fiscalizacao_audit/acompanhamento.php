<?php
	set_title('Registro de Solicitações, Fiscalizações e Auditorias');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array('ds_solic_fiscalizacao_audit_acompanhamento')) ?>

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

    function ir_documentacao()
    {
    	location.href = "<?= site_url('atividade/solic_fiscalizacao_audit/documentacao/'.intval($row['cd_solic_fiscalizacao_audit'])) ?>";
    }

    function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),
		[
			"DateTimeBR",
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
		ob_resul.sort(0, true);
	}

	$(function(){
		configure_result_table();
	});
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_cadastro', 'Cadastro', FALSE, 'ir_cadastro();');

	if(trim($row['dt_envio']) != '')
	{
		$abas[] = array('aba_prorrogacao', 'Prorrogação de Prazo', FALSE, 'ir_prorrogacao();');
	}
	
	$abas[] = array('aba_acompanhamento', 'Acompanhamento', TRUE, 'location.reload();');

	if(trim($row['dt_envio']) != '')
	{
		$abas[] = array('aba_documentacao', 'Documentação/Informação', FALSE, 'ir_documentacao();');
	}

	$head = array(
		'Dt. Inclusao',
		'Descrição',
		'Usuário'
	);

	$body = array();

	foreach ($collection as $item)
	{
	  	$body[] = array(
	  		$item['dt_inclusao'],
	  		array(nl2br($item['ds_solic_fiscalizacao_audit_acompanhamento']), 'text-align:justify'),
	  		array($item['ds_usuario_inclusao'], 'text-align:left')
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

			if(trim($row['dt_envio']) != '')
			{
				echo form_default_row('', 'Dt. Envio:', $row['dt_envio']);
				echo form_default_row('', 'Usuário:', $row['ds_usuario_envio']);
			}	

			if(trim($row['dt_envio_solicitacao_documento']) != '')
			{
				echo form_default_row('', 'Dt. Envio Solicitação:', $row['dt_envio_solicitacao_documento']);
				echo form_default_row('', 'Usuário:', $row['ds_usuario_envio_solicitacao_documento']);
			}
		echo form_end_box('default_solicitacao_box');
		echo form_open('atividade/solic_fiscalizacao_audit/salvar_acompanahmento');
			echo form_start_box('default_acompanhamento_box', 'Acompanhamento');
				echo form_default_hidden('cd_solic_fiscalizacao_audit', '', $row['cd_solic_fiscalizacao_audit']);
				echo form_default_textarea('ds_solic_fiscalizacao_audit_acompanhamento', 'Descrição: (*)', '', 'style="height:80px;"');
			echo form_end_box('default_acompanhamento_box');
			echo form_command_bar_detail_start();
				echo button_save('Salvar');  
			echo form_command_bar_detail_end();
		echo form_close();
		echo br();
		echo $grid->render();
		echo br(2);
    echo aba_end();

    $this->load->view('footer_interna');

?>