<?php
	set_title('Pendências Gestão');
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
	
    function ir_cronograma()
    {
        location.href = "<?= site_url('gestao/pendencia_gestao/cronograma/'.intval($row['cd_pendencia_gestao'])) ?>";
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

	function excluir(cd_atividade_anexo)
	{
		var confirmacao = "Deseja EXCLUIR o Anexo?\n\n"+
                          "Clique [Ok] para Sim\n\n"+
                          "Clique [Cancelar] para Não\n\n"; 

		if(confirm(confirmacao))
		{
			location.href = "<?= site_url('gestao/pendencia_gestao/excluir_anexo/'.intval($row['cd_pendencia_gestao'])) ?>/"+cd_atividade_anexo;
		}
	}

	$(function(){
		configure_result_table();
	});
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_pendencia', 'Cadastro', FALSE, 'ir_cadastro();');
	if($row['fl_cronograma'] == "S")
	{
		$abas[] = array('aba_cronograma', 'Cronograma', FALSE, 'ir_cronograma();');
	}	
	$abas[] = array('aba_acompanhamento', 'Acompanhamento', FALSE, 'ir_acompanhamento();');
	$abas[] = array('aba_anexo', 'Anexo', TRUE, 'location.reload();');

	$head = array(
	  	'Dt. Inclusão',
		'Arquivo',
	  	'Usuário',
	    ''
	);

	$body = array();

	foreach($collection as $item)
	{
	    $body[] = array(
	    	$item['dt_inclusao'],
	      	array(anchor(base_url().'up/pendencia_gestao/'.$item['arquivo'], $item['arquivo_nome'], array('target' => '_blank')) ,'text-align:left'),
	      	array($item['ds_usuario'], 'text-align:left'),
	      	(trim($row['dt_encerrada']) == '' ? '<a href="javascript:void(0)" onclick="excluir('.$item['cd_pendencia_gestao_anexo'].');">[excluir]</a>' : '')
	    );
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo aba_start($abas);
		echo form_open('gestao/pendencia_gestao/salva_anexo');
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
			echo form_start_box('default_box', 'Anexo');
				echo form_default_upload_multiplo('arquivo_m', 'Arquivo: (*)', 'pendencia_gestao', 'validaArq');
				echo form_default_row('', '', '<i>Adicione o(s) arquivo(s) e depois clique no botão [Anexar]</i>');
			echo form_end_box('default_box');
		echo form_close();
		echo $grid->render();
 		echo br(2);
    echo aba_end();

    $this->load->view('footer_interna');
?>