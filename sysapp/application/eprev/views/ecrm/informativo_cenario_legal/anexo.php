<?php
	set_title('Informativo do Cen�rio Legal');
	$this->load->view('header');
	?>
<script>
	<?= form_default_js_submit(array(), 'valida_arquivo(form)') ?>

	function ir_lista()
	{
		location.href = "<?= site_url('ecrm/informativo_cenario_legal') ?>";
	}

	function ir_cadastro()
	{
		location.href = "<?= site_url('ecrm/informativo_cenario_legal/cadastro/'.$edicao['cd_edicao']) ?>";
	}
	
	function ir_conteudo()
	{
		location.href = "<?= site_url('ecrm/informativo_cenario_legal/conteudo/'.$edicao['cd_edicao']) ?>";
	}
	
	function ir_cadastro_conteudo()
	{
		location.href = "<?= site_url('ecrm/informativo_cenario_legal/conteudo_cadastro/'.$edicao['cd_edicao'].'/'.$cenario['cd_cenario']) ?>";
	}
	
	function valida_arquivo(form)
    {
        if(($("#arquivo").val() == "") && ($("#arquivo_nome").val() == ""))
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
	
	function configure_result_table()
    {
        var ob_resul = new SortableTable(document.getElementById("table-1"),
        [
			'Number',
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
        ob_resul.sort(1, true);
    }

    function enviar_email()
    {
    	var confirmacao = 'Deseja enviar o e-mail para as �reas envolvidas?\n\n'+
			'Clique [Ok] para Sim\n\n'+
			'Clique [Cancelar] para N�o\n\n';

		if(confirm(confirmacao))
		{
    		location.href = "<?= site_url('ecrm/informativo_cenario_legal/email_anexo/'.$edicao['cd_edicao'].'/'.$cenario['cd_cenario']) ?>";
    	}
    }
	
	function excluir(cd_cenario_anexo)
	{
		var confirmacao = 'Deseja excluir o anexo?\n\n'+
			'Clique [Ok] para Sim\n\n'+
			'Clique [Cancelar] para N�o\n\n';

		if(confirm(confirmacao))
		{
			location.href = "<?= site_url('ecrm/informativo_cenario_legal/excluir_anexo/'.$edicao['cd_edicao'].'/'.$cenario['cd_cenario']) ?>/"+cd_cenario_anexo;
		}
	}
	
	$(function(){
		configure_result_table();
	});
	
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_cadastro', 'Cadastro' , FALSE, 'ir_cadastro();');
	$abas[] = array('aba_conteudo', 'Conte�do' , FALSE, 'ir_conteudo();');
	$abas[] = array('aba_cadastro_conteudo', 'Cadastro de Conte�do', FALSE, 'ir_cadastro_conteudo();');
	$abas[] = array('aba_anexo', 'Anexos' , TRUE, 'location.reload();');

	$head = array( 
		'C�digo',
		'Dt Inclus�o',
		'Arquivo',
		'Usu�rio',
		''
	);

	$body = array();

	foreach($collection as $item)
	{	
	    $body[] = array(
			$item['cd_cenario_anexo'],
			$item['dt_inclusao'],
			array(anchor(base_url().'up/cenario/'.$item['arquivo'], $item['arquivo_nome'], array('target' => "_blank")), 'text-align:left;'),
			$item['ds_usuario_inclusao'],
			(trim($edicao['dt_envio_email']) == '' ? '<a href="javascript:void(0);" onclick="excluir('.$item['cd_cenario_anexo'].')">[excluir]</a>' : '')
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
	

	echo aba_start($abas);
		echo form_start_box('edicao_box', 'Edi��o');
			echo form_default_row('', 'Edi��o:', '<span class="label label-inverse">'.intval($edicao['cd_edicao']).'</span>');
			echo form_default_row('', 'Data:', $edicao['dt_edicao']);
			echo form_default_row('', 'T�tulo:', $edicao['tit_capa']);
		echo form_end_box('edicao_box');

		echo form_start_box('cenario_box', 'Cen�rio');
			echo form_default_row('', 'Item:', '<span class="label label-inverse">'.intval($cenario['cd_cenario']).'</span>');
			echo form_default_row('', 'T�tulo:', $cenario['titulo']);
		echo form_end_box('cenario_box');

		echo form_open('ecrm/informativo_cenario_legal/salvar_anexo');
			echo form_start_box('default_box', 'Anexo');
				echo form_default_hidden('cd_edicao', '', $edicao['cd_edicao']);
				echo form_default_hidden('cd_cenario', '', $cenario['cd_cenario']);
                echo form_default_upload_multiplo('arquivo_m', 'Arquivo: (*)', 'cenario', 'validaArq');
				echo form_default_row('', '', '<i>Selecione o arquivo e depois clique no bot�o [Anexar arquivo]</i>');
				echo form_default_row('', '', '<i>Tamanho m�ximo por arquivo anexo � de 10 Mb</i>');
			echo form_end_box("default_box");
		echo form_close();
		echo form_command_bar_detail_start();
			if(count($collection) > 0)
			{
				echo button_save('Enviar E-mail', 'enviar_email()');
			}
		echo form_command_bar_detail_end();
		echo $grid->render();
		echo br(2);
	echo aba_end();
	$this->load->view('footer_interna');
?>