<?php
	set_title('Inscrições Eleições');
	$this->load->view('header');
?>
<script>

	<?= form_default_js_submit(array(), 'valida_arquivo(form)'); ?>	

	function ir_lista()
	{
		 location.href = "<?= site_url('gestao/formulario_inscricao_eleicao') ?>";
	}

	function ir_cadastro()
	{
		 location.href = "<?= site_url('gestao/formulario_inscricao_eleicao/cadastro/'.$row['cd_formulario_inscricao_eleicao']) ?>";
	}

	function excluir_anexo(cd_formulario_inscricao_eleicao_anexo)
	{
		var confirmacao = 'Deseja excluir o anexo?\n\n'+
			'Clique [Ok] para Sim\n\n'+
			'Clique [Cancelar] para Não\n\n';

		if(confirm(confirmacao))
		{ 
		 	location.href = "<?= site_url('gestao/formulario_inscricao_eleicao/excluir_anexo/'.$row['cd_formulario_inscricao_eleicao'])?>" +'/'+ cd_formulario_inscricao_eleicao_anexo;
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
		    "CaseInsensitiveString",
		    "DateTimeBR",
		    "CaseInsensitiveString",
		    "",
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
		ob_resul.sort(1, true);
	}
 
$(function()
{	
	configure_result_table();

});
		
</script>



<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_cadastro', 'Cadastro', FALSE, 'ir_cadastro();');
	$abas[] = array('aba_anexos', 'Anexos', TRUE, 'location.reload();');

	$this->load->helper('grid');
	$grid = new grid();

	$head = array( 
		'Arquivo',
		'Dt. Inclusão',
		'Usuário Inclusão',
		''
	);

	$body = array();

	echo aba_start($abas);
		echo form_open('gestao/formulario_inscricao_eleicao/salvar_anexos');
			echo form_start_box('default_box', 'Anexos');
				echo form_default_hidden('cd_formulario_inscricao_eleicao','',$row['cd_formulario_inscricao_eleicao']);
				echo form_default_upload_multiplo('arquivo_m', 'Arquivo :*', 'formulario_inscricao_eleicao/'.$row['ds_codigo'], 'validaArq');
				echo form_default_row('', '', '<i>Adicione o(s) arquivo(s) e depois clique no botão [Anexar]</i>');
			echo form_end_box('default_box');
		echo form_close();

		foreach($collection as $item)
		{
			$body[] = array(
				array(anchor(base_url().'up/formulario_inscricao_eleicao/'.$row['ds_codigo'].'/'.$item['arquivo'], $item['arquivo_nome']), 'text-align:left;'),
				$item['dt_inclusao'],
				$item['ds_usuario_inclusao'],
				'<a href="javascript:void(0);" onclick="excluir_anexo('.$item['cd_formulario_inscricao_eleicao_anexo'].')">[excluir]</a>'
			);
		}

		$grid->head = $head;
		$grid->body = $body;
		echo $grid->render();

	echo aba_end();




?>
