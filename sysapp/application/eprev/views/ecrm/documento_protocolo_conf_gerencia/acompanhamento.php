<?php 
	set_title('Conferência de Documentos - Relatório');
	$this->load->view('header'); 
?>
<script>
	<?= form_default_js_submit(array('ds_acompanhamento')) ?>

	function ir_lista()
	{
		location.href = "<?= site_url('ecrm/documento_protocolo_conf_gerencia/relatorio') ?>";
	}

	function ir_documentos()
	{
		location.href = "<?= site_url('ecrm/documento_protocolo_conf_gerencia/documentos/'.$documento['cd_documento_protocolo_conf_gerencia_item_mes']) ?>";
	}

    function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),
		[
	    	"CaseInsensitiveString",
	    	"DateTimeBR",
	    	"CaseInsensitiveString"
		]);
		ob_resul.onsort = function ()
		{
			var rows = ob_resul.tBody.rows;
			var l = rows.length;
			for (var i = 0; i < l; i++)
			{
				removeClassName(rows[i], i % 2 ? "sort-par" : "sort-impar");
				addClassName(rows[i], i % 2 ? "sort-impar" : "sort-par");
			}
		};
		
		ob_resul.sort(0, false);
	}

	function excluir(cd_documento_protocolo_conf_gerencia_item, cd_documento_protocolo_conf_gerencia_item_acompanhamento)
	{
		var text = "Deseja excluir este acompanhamento?\n\n"+
				   "[OK] para Sim\n\n"+
				   "[Cancelar] para Não\n";

		if(confirm(text))
		{
			location.href = "<?= site_url('ecrm/documento_protocolo_conf_gerencia/excluir_acompanhamento') ?>/" + cd_documento_protocolo_conf_gerencia_item + "/" +cd_documento_protocolo_conf_gerencia_item_acompanhamento;
		}
	}

	$(function(){
		configure_result_table();
	});
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_documentos', 'Documentos', FALSE, 'ir_documentos();');	

	$abas[] = array('aba_acompanhamento', 'Acompanhamento', TRUE, 'location.reload();');

    $head = array( 
        'Descrição',
        'Dt. Inclusão',
        'Usuário',
        ''
    );

    $body = array();

    foreach($collection as $key => $item)
    {
    	$excluir = '';

    	if(trim($item['fl_acompanhamento']) != 'S' AND intval($item['cd_usuario_inclusao']) == intval($cd_usuario))
    	{
    		$excluir = '<a href="javascript:void(0);" onclick="excluir('.$documento['cd_documento_protocolo_conf_gerencia_item'].', '.$item['cd_documento_protocolo_conf_gerencia_item_acompanhamento'].')">[excluir]</a>';
    	}

    	$body[] = array(
           	array(nl2br($item['ds_acompanhamento']), 'text-align:left;'),
           	$item['dt_inclusao'],
           	$item['ds_usuario_inclusao'],
           	$excluir
    	);
    }

    $this->load->helper('grid');
    $grid = new grid();
    $grid->head = $head;
    $grid->body = $body;

	echo aba_start($abas);
		echo form_start_box('default_documento_box', 'Documento');
			echo form_default_row('', 'Protocolo:', $documento['nr_protocolo']);
			echo form_default_row('', 'Envio:', $documento['dt_envio']);
			echo form_default_row('', 'Usuário Envio:', $documento['ds_usuario_envio']);
			echo form_default_row('', 'Recebimento:', $documento['dt_recebimento']);
			echo form_default_row('', 'Usuário Receb.:', $documento['ds_usuario_recebimento']);
			echo form_default_row('', 'Indexação:', $documento['dt_indexacao']);
			echo form_default_row('', 'RE:', $documento['nr_re']);
			echo form_default_row('', 'Participante:', $documento['ds_participante']);
			echo form_default_row('', 'Doc.:', $documento['ds_documento']);
			echo form_default_row('', 'Tipo de Documento:', $documento['cd_tipo_doc']);
			echo form_default_row('', 'Caminho:', $documento['ds_caminho']);
			echo form_default_row('', 'Páginas:', $documento['nr_folha']);
			echo form_default_row('', 'Processo:', $documento['ds_processo']);
			echo form_default_row('', 'Arquivo:', $documento['arquivo']);
		echo form_end_box('default_documento_box');
		echo form_open('ecrm/documento_protocolo_conf_gerencia/salvar_acompanhamento');
			echo form_start_box('default_cadastro_box', 'Cadastro');
				echo form_default_hidden('cd_documento_protocolo_conf_gerencia_item', '', $documento['cd_documento_protocolo_conf_gerencia_item']);
				echo form_default_hidden('cd_documento_protocolo_conf_gerencia_item_mes', '', $documento['cd_documento_protocolo_conf_gerencia_item_mes']);
				echo form_default_hidden('fl_status', '', $documento['fl_status']);
				echo form_default_row('', 'Status: ', '<span class="'.$documento['ds_label_status'].'">'.$documento['ds_status'].'</span>');
				if((trim($documento['fl_status']) != 'A' AND trim($documento['fl_status']) != 'C') OR ($fl_documento AND intval($cd_usuario_inclusao) != intval($cd_usuario)))
				{
					echo form_default_textarea('ds_acompanhamento', 'Descrição: (*)', '');
				}
			echo form_end_box('default_cadastro_box');
		    echo form_command_bar_detail_start();
		    	if((trim($documento['fl_status']) != 'A' AND trim($documento['fl_status']) != 'C') OR ($fl_documento AND intval($cd_usuario_inclusao) != intval($cd_usuario)))
				{
					echo button_save('Salvar');
				}
	    	echo form_command_bar_detail_end();
		echo form_close();
		echo br(2);
		echo $grid->render();
		echo br();
	echo aba_end();
	$this->load->view('footer');
?>