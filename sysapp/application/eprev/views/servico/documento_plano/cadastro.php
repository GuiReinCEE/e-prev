<?php
set_title('Documentos Site');
$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array('cd_documento_plano_tipo'), 'valida_arquivo(form);') ?>

	function ir_lista()
	{
		location.href = '<?= site_url('servico/documento_plano') ?>';
	}

	function excluir(cd_documento_plano_arquivo)
	{
		var confirmacao = 'Deseja excluir o arquivo?\n\n'+
			'Clique [Ok] para Sim\n\n'+
			'Clique [Cancelar] para Não\n\n';

		if(confirm(confirmacao))
		{ 
			location.href = "<?= site_url('servico/documento_plano/excluir/'.$row['cd_documento_plano']) ?>/"+cd_documento_plano_arquivo;
		}
	}

	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),
		[
			"CaseInsensitiveString",
			"DateTimeBR",
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
		ob_resul.sort(1, true);
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

    function todos(cd_documento_plano_tipo)
	{
		location.href = "<?= site_url('servico/documento_plano/todos/'.$row['cd_documento_plano']) ?>/"+cd_documento_plano_tipo;
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
		'Dt Inclusão',
		'Usuário',
		''
	);

	$body = array();

	foreach($collection as $item)
	{	
	    $body[] = array(
			array(anchor(base_url().'up/documento_plano/'.$item['arquivo_nome'], $item['ds_documento_plano_tipo'], array('target' => '_blank')), "text-align:left;"),
			$item['dt_inclusao'],
			array($item['ds_usuario_inclusao'], "text-align:left;"),
			(intval($item['tl_documento']) > 1 ? '<a href="javascript:void(0);" onclick="todos('.$item['cd_documento_plano_tipo'].')">[ver todos]</a> ' : '').
			'<a href="javascript:void(0);" onclick="excluir('.$item['cd_documento_plano_arquivo'].')">[excluir]</a>' 
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo aba_start($abas);
		echo form_open('servico/documento_plano/salvar');
			echo form_start_box('default_box', 'Documento Site');
				echo form_default_hidden('cd_documento_plano', '', $row['cd_documento_plano']);
				echo form_default_row('ds_documento_plano', 'Documento Site:', $row['ds_documento_plano']);
			echo form_end_box('default_box');

			echo form_start_box('default_cad_box', 'Cadastro');
				echo form_default_dropdown('cd_documento_plano_tipo', 'Tipo Documento: (*)', $tipo_documento);
				echo form_default_upload_iframe('arquivo', 'documento_plano', 'Arquivo: (*)', array(), 'documento_plano', true);
			echo form_end_box('default_cad_box');

			echo form_command_bar_detail_start();
				echo button_save('Salvar');	
			echo form_command_bar_detail_end();
		echo form_close();
		echo $grid->render();
		echo br(2);
	echo aba_end();

	$this->load->view('footer');
?>