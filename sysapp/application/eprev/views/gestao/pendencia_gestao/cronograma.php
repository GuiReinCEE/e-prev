<?php
	set_title('Pendências Gestão - Cronograma');
	$this->load->view('header');
?>

<script>
	<?= form_default_js_submit(array(), 'valida_arquivo(form)') ?>

	function valida_arquivo(form)
    {
        if($('#arquivo').val() == '' && $('#arquivo_nome').val() == '')
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

	function ir_lista()
    {
        location.href = "<?= site_url('gestao/pendencia_gestao') ?>";
    }

    function ir_cadastro()
    {
        location.href = "<?= site_url('gestao/pendencia_gestao/cadastro/'.intval($row['cd_pendencia_gestao'])) ?>";
    }
    
    function ir_acompanhamento()
    {
        location.href = "<?= site_url('gestao/pendencia_gestao/acompanhamento/'.intval($row['cd_pendencia_gestao'])) ?>";
    }
	
    function ir_anexo()
    {
        location.href = "<?= site_url('gestao/pendencia_gestao/anexo/'.intval($row['cd_pendencia_gestao'])) ?>";
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
	$abas[] = array('aba_pendencia', 'Cadastro', FALSE, 'ir_cadastro();');
	$abas[] = array('aba_cronograma', 'Cronograma', TRUE, 'location.reload();');
	$abas[] = array('aba_acompanhamento', 'Acompanhamento', FALSE, 'ir_acompanhamento();');
	$abas[] = array('aba_anexo', 'Anexo', FALSE, 'ir_anexo();');

	$head = array(
	  	'Dt. Inclusão',
		'Cronograma',
	  	'Usuário'
	);

	$body = array();

	foreach($collection as $item)
	{
	    $body[] = array(
	    	$item['dt_inclusao'],
	      	array(anchor(base_url().'up/pendencia_gestao/'.$item['arquivo'], $item['arquivo_nome'], array('target' => '_blank')) ,'text-align:left'),
	      	array($item['ds_usuario'], 'text-align:left')
	    );
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo aba_start($abas);
		echo form_open('gestao/pendencia_gestao/salva_cronograma');
			echo form_start_box('default_box_pendencia', 'Pêndencia');
				echo form_default_hidden('cd_pendencia_gestao', '', $row['cd_pendencia_gestao']);
				echo form_default_row('ds_reuniao_sistema_gestao_tipo', 'Reunião:', $row['ds_reuniao_sistema_gestao_tipo']);

				if(trim($row['dt_reuniao']) != '')
				{
					echo form_default_row('dt_reuniao', 'Dt. Reunião:', $row['dt_reuniao']);
				}
				
				echo form_default_row('dt_prazo', 'Dt. Prazo:', $row['dt_prazo']);
				echo form_default_textarea('ds_item', 'Item:', $row['ds_item'], 'style="height:80px; width:500px;" readonly=""');
				
			echo form_end_box('default_box_pendencia');
			
			if(trim($fl_permissao) == 'S')
			{
				echo form_start_box('default_box', 'Cronograma');
					echo form_default_upload_multiplo('arquivo_m', 'Arquivo: (*)', 'pendencia_gestao', 'validaArq');
					echo form_default_row('', '', '<i>Adicione o(s) arquivo(s) e depois clique no botão [Anexar]</i>');
				echo form_end_box('default_box');
			}
			
		echo form_close();
		echo $grid->render();
 		echo br(2);
    echo aba_end();

    $this->load->view('footer_interna');
?>