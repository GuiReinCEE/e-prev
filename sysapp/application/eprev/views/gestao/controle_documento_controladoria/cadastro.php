<?php
	set_title('Controle Documentos - Cadastro');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array(), 'valida_arquivo(form)') ?>

	function ir_lista()
	{
		location.href = "<?= site_url('gestao/controle_documento_controladoria') ?>";
	}

	function valida_arquivo(form)
    {
        if(($("#arquivo").val() == "") && ($('#arquivo_nome').val() == ''))
        {
            alert("Nenhum arquivo foi anexado.");
            return false;
        }
        else
        {
            if(confirm("Salvar?"))
            {
                form.submit();
            }
        }
    }

    function validaArq(enviado, nao_enviado, arquivo)
	{
		$("form").submit();
	}	

	function excluir(cd_controle_documento_controladoria)
	{
		var confirmacao = 'Deseja excluir o Documento?\n\n'+
			'Clique [Ok] para Sim\n\n'+
			'Clique [Cancelar] para Não\n\n';

		if(confirm(confirmacao))
		{ 
			location.href = "<?= site_url('gestao/controle_documento_controladoria/excluir') ?>/" + cd_controle_documento_controladoria;
		}
	}

	function enviar_email(cd_controle_documento_controladoria)
	{
		var confirmacao = 'Deseja enviar o E-mail?\n\n'+
			'Clique [Ok] para Sim\n\n'+
			'Clique [Cancelar] para Não\n\n';

		if(confirm(confirmacao))
		{ 
			location.href = "<?= site_url('gestao/controle_documento_controladoria/enviar_email') ?>/" + cd_controle_documento_controladoria;
		}
	}

	function configure_result_table()
    {
        var ob_resul = new SortableTable(document.getElementById("table-1"),
        [
			"CaseInsensitiveString",
			"DateTimeBR",
			"DateTimeBR",
			"CaseInsensitiveString",
			"DateTimeBR", 
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
        ob_resul.sort(1, true);
    }

    
    $(function(){
		configure_result_table();      
	});
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');

	$head = array( 
		'Arquivo',
		'Dt. Atualização',
		'Dt. Referencia',
		'Descrição',
		'Dt. Envio',
		''
	);

	$body = array();

	foreach($collection as $item )
	{	
	    $body[] = array(
			array(anchor(base_url().'up/controle_documento_controladoria/'.$item['arquivo'], $item['arquivo_nome'], array('target' => '_blank')), 'text-align:left;'),
			$item['dt_inclusao'],
			$item['dt_referencia'],
			array(nl2br($item['ds_controle_documento_controladoria']), 'text-align:left;'),
			$item['dt_envio'],
			(trim($item['dt_envio']) == '' ? '<a href="javascript:void(0);" onclick="excluir('.$item['cd_controle_documento_controladoria'].')">[excluir]</a> <a href="javascript:void(0);" onclick="enviar_email('.$item['cd_controle_documento_controladoria'].')">[enviar e-mail]</a>' : '')
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo aba_start($abas);
		echo form_open('gestao/controle_documento_controladoria/salvar');
			echo form_start_box('default_box', 'Cadastro');
				echo form_default_hidden('cd_controle_documento_controladoria_tipo', '', $row['cd_controle_documento_controladoria_tipo'] );	
				echo form_default_row('ds_controle_documento_controladoria_tipo', 'Tipo Documento:', $row['ds_controle_documento_controladoria_tipo'] );
				echo form_default_upload_iframe('arquivo', 'controle_documento_controladoria', 'Arquivo: (*)', array(), 'controle_documento_controladoria', true);
				echo form_default_date('dt_referencia', 'Dt. Referência:');
				echo form_default_textarea('ds_controle_documento_controladoria', 'Descrição:', '', 'style="height:100px;"');
			echo form_end_box('default_box');
			echo form_command_bar_detail_start();
					echo button_save('Salvar');	
			echo form_command_bar_detail_end();
		echo form_close();
		echo $grid->render();
		echo br();
	echo aba_end();

	$this->load->view('footer');
?>