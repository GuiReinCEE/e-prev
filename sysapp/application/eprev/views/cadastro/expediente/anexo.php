<?php
	set_title('Comitê de Ética - Expediente - Anexos');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array(), 'valida_arquivo(form)') ?>

	function ir_lista()
	{
		location.href = "<?= site_url('cadastro/expediente') ?>";
	}
	
	function ir_cadastro()
	{
		location.href = "<?= site_url('cadastro/expediente/cadastro/'.$expediente['cd_expediente']) ?>";
	}	
	
	function ir_andamento()
	{
		location.href = "<?= site_url('cadastro/expediente/andamento/'.$expediente['cd_expediente']) ?>";
	}
	
	function valida_arquivo(form)
    {
        if($("#arquivo").val() == "" && $("#arquivo_nome").val() == "")
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
			"Number",
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
        ob_resul.sort(1, true);
    }
	
	$(function(){
		configure_result_table();
	});
	
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_cadastro', 'Cadastro', FALSE, 'ir_cadastro()');
	$abas[] = array('aba_andamento', 'Andamento', FALSE, 'ir_andamento();');
	$abas[] = array('aba_anexo', 'Anexo', TRUE, 'location.reload();');

	$head = array( 
		'Código',
		'Dt Inclusão',
		'Arquivo',
		'Usuário'
	);

	$body = array();

	foreach($collection as $item)
	{
	    $body[] = array(
			$item['cd_expediente_anexo'],
			$item['dt_inclusao'],
			array(anchor($item['url'].$item['ds_arquivo'], $item['ds_arquivo_nome'] , array('target' => "_blank")), "text-align:left;"),
			$item['ds_usuario']
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
	
	echo aba_start($abas);
		echo form_open('cadastro/expediente/anexo_salvar');
			echo form_start_box('default_box', 'Expediente');	
				echo form_default_hidden('cd_expediente', '', $expediente['cd_expediente']);
				echo form_default_row('', 'Cód Expediente:', '<span class="label label-inverse">'.$expediente['nr_expediente'].'</span>');
				echo form_default_row('', 'Dt Registro:', '<span class="label">'.$expediente['dt_inclusao'].'</span>');
				echo form_default_row('', 'Dt Atualização:', '<span class="label">'.$expediente['dt_alteracao'].'</span>');
				echo form_default_row('', 'Dt Envio Comitê:', '<span class="label">'.$expediente['dt_envio_comite'].'</span>');
				echo form_default_row('', 'Dt Conclusão:', '<span class="label label-success">'.$expediente['dt_conclusao'].'</span>');
				echo form_default_row('', 'Status:', '<span class="label label-warning">'.$expediente['ds_expediente_status'].'</span>');
				echo form_default_row('', 'Descrição:', nl2br($expediente['ds_descricao']));		
			echo form_end_box('default_box');
			
			echo form_start_box('anexo_box', 'Anexo');	
				echo form_default_row('', '', '');
				echo form_default_upload_multiplo('arquivo_m', 'Arquivo: (*)', 'expediente', 'validaArq');
				echo form_default_row('', '', '<i>Adicione o(s) arquivo(s) e depois clique no botão [Anexar]</i>');
			echo form_end_box('anexo_box');
		echo form_close();

		echo $grid->render();
		echo br(2);
	echo aba_end();

	$this->load->view('footer_interna');
?>