<?php 
	set_title('Documentos Recebidos - Anexo');
	$this->load->view('header'); 
?>
<script>
	<?= form_default_js_submit(array(), 'valida_arquivo(form)') ?>

	function ir_lista()
	{
		location.href = "<?= site_url('cadastro/sg_documento_recebido') ?>";
	}

	function ir_cadastro()
	{
		location.href = "<?= site_url('cadastro/sg_documento_recebido/cadastro/'.$row['ano'].'/'.$row['numero']) ?>";
	}
	
	function excluir(cd_docs_recebidos_anexo)
	{
		var confirmacao = 'Deseja excluir o anexo?\n\n'+
			'Clique [Ok] para Sim\n\n'+
			'Clique [Cancelar] para Não\n\n';
	
		if(confirm(confirmacao))
		{
			location.href = "<?= site_url('cadastro/sg_documento_recebido/excluir_anexo/'.$row['ano'].'/'.$row['numero']) ?>/"+cd_docs_recebidos_anexo;
		}
	}

	function valida_arquivo(form)
    {
        if($('#arquivo').val() == '' && $('#arquivo_nome').val() == '')
        {
            alert('Nenhum arquivo foi anexado.');
            return false;
        }
        else
        {
            if( confirm('Salvar?') )
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
			'DateTimeBR', 
			'CaseInsensitiveString', 
			'CaseInsensitiveString', 
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
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_detalhe', 'Cadastro', FALSE, 'ir_cadastro()');
	$abas[] = array('aba_anexo', 'Anexo', true, 'location.reload();');

	$body = array();
	$head = array(
		'Dt Inclusão',
		'Arquivo',
		'Usuário',
		''
	);

	foreach($collection as $item)
	{
		$excluir = '<a href="javascript:void(0);" onclick="excluir('.$item['cd_docs_recebidos_anexo'].')">[excluir]</a>';
		
	    $body[] = array(
			$item['dt_inclusao'],
			array(anchor(base_url().'up/docs_recebidos/'.$item['arquivo'], $item['arquivo_nome'], array('target' => "_blank")), "text-align:left;"),
			$item['ds_usuario'],
			$excluir
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
	
	echo aba_start($abas);
		echo form_open('cadastro/sg_documento_recebido/salvar_anexo');
			echo form_start_box('default_box', 'Anexo');
				echo form_default_hidden('ano', '', $row['ano']);
				echo form_default_hidden('numero', '', $row['numero']);
				echo form_default_upload_multiplo('arquivo_m', 'Arquivo: (*)', 'docs_recebidos', 'validaArq');
				echo form_default_row('', '', '<i>Adicione o(s) arquivo(s) e depois clique no botão [Anexar]</i>');
			echo form_end_box('default_box');
		echo form_close();
		echo $grid->render();

		echo br(2);

	echo aba_end();
	$this->load->view('footer_interna');
?>