<?php
set_title('Plano de Ação - Anexo');
$this->load->view('header');
?>
<script>
    <?= form_default_js_submit(array(), 'valida_arquivo(form)') ?>
   
    function ir_minhas()
    {
        location.href = "<?= site_url('gestao/plano_acao/minhas') ?>";
    }

    function ir_responder()
    {
    	location.href = "<?= site_url('gestao/plano_acao/responder/'.intval($row['cd_plano_acao']).'/'.intval($itens['cd_plano_acao_item'])) ?>";
    }

    function excluir(cd_plano_acao_item_anexo)
	{
		var confirmacao = 'Deseja excluir o Anexo?\n\n'+
			'Clique [Ok] para Sim\n\n'+
			'Clique [Cancelar] para Não\n\n';

		if(confirm(confirmacao))
		{ 
			location.href = '<?= site_url('gestao/plano_acao/excluir_anexo/'.$row['cd_plano_acao'].'/'.$itens['cd_plano_acao_item']) ?>/' + cd_plano_acao_item_anexo;
		}
	}

	function valida_arquivo(form)
    {
        if(($('#arquivo').val() == '') && ($('#arquivo_nome').val() == ''))
        {
            alert('Nenhum arquivo foi anexado.');
            return false;
        }
        else
        {
            if(confirm('Salvar?'))
            {
                form.submit();
            }
        }
    }

    function validaArq(enviado, nao_enviado, arquivo)
	{
		$("form").submit();
	}	

	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),
		[
			"DateTimeBR", 
			"CaseInsensitiveString", 
			"CaseInsensitiveString", 
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
		ob_resul.sort(0, true);
	}

	$(function(){
		configure_result_table();
	});
</script>
<?php
	$abas[] = array('aba_minhas', 'Lista', FALSE, 'ir_minhas();');
	$abas[] = array('aba_responder', 'Plano de Ação', FALSE, 'ir_responder();');
	$abas[] = array('aba_anexo', 'Anexos', TRUE, 'location.reload();');

	$head = array(
		'Dt Inclusão',
		'Arquivo',
		'Usuário',
		''
	);

	$body = array();

	foreach($collection as $item)
	{
		$link = '';

		if((trim($itens['fl_status']) != 'E') AND (trim($itens['cd_gerencia_responsavel']) == $this->session->userdata('divisao')))
		{
			$link = '<a href="javascript:void(0)" onclick="excluir('.$item['cd_plano_acao_item_anexo'].')">[excluir]</a>';
		}

		$body[] = array(
			$item['dt_inclusao'],
			array(anchor(base_url().'up/plano_acao/'.$item['arquivo'], $item['arquivo_nome'], array('target' => '_blank')), 'text-align:left;'),
			$item['ds_nome'],
			$link
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
	$grid->view_count = false;

	echo aba_start($abas);
	    echo form_start_box('default_box', 'Plano de Ação');
			echo form_default_row('', 'Ano/Número:', '<label class="label label-inverse">'.$row['ds_ano_numero'].'</label>');
			
			if(trim($row['cd_processo']) != '')
			{
				echo form_default_row('cd_processo', 'Processo:', $row['procedimento']);
			}

			if(trim($row['ds_situacao']) != '')
			{
				echo form_default_textarea('ds_situacao', 'Situação:', $row['ds_situacao'], 'style="height:80px;" readonly=""');
			}

			if(trim($row['ds_relatorio_auditoria']) != '')
			{
				echo form_default_textarea('ds_relatorio_auditoria', 'Relatório de Auditoria:', $row['ds_relatorio_auditoria'], 'style="height:80px;" readonly=""');
			}
		echo form_end_box('default_box');

		echo form_start_box('default_box_item', 'Item');
			echo form_default_hidden('cd_plano_acao', '', $row);
        	echo form_default_hidden('cd_plano_acao_item', '', $itens);	
			echo form_default_row('', 'N° Item :', $itens['nr_plano_acao_item']);
			echo form_default_textarea('ds_constatacao', 'Constatação:', $itens['ds_constatacao'], 'style="height:80px;" readonly=""');
			echo form_default_textarea('ds_recomendacao', 'Recomendação:',implode('&#013;',$ds_recomendacao), 'style="height:80px;" readonly=""');
			if(trim($itens['dt_prazo']) != '')
			{
				echo form_default_date('dt_prazo', 'Dt. Prazo: (*)', $itens['dt_prazo']);
			}
		echo form_end_box('default_box_item');

		if((trim($itens['fl_status']) != 'E') AND (trim($itens['cd_gerencia_responsavel']) == $this->session->userdata('divisao')))
		{
			echo form_open('gestao/plano_acao/salvar_anexo');
				echo form_start_box('default_box_anexo', 'Anexo');
					echo form_default_hidden('cd_plano_acao', '', $row);
		            echo form_default_hidden('cd_plano_acao_item', '', $itens);	
					echo form_default_upload_multiplo('arquivo_m', 'Arquivo :*', 'plano_acao', 'validaArq');
					echo form_default_row('', '', '<i>Adicione o(s) arquivo(s) e depois clique no botão [Anexar]</i>');
				echo form_end_box('default_box_anexo');	
			echo form_close();	
	    }

	    echo br();
		echo $grid->render();
		echo br(2);
	echo aba_end();
	$this->load->view('footer_interna');
?>
