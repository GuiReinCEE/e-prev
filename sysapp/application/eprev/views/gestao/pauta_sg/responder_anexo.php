<?php
	set_title('Pauta SG - Responder - Anexo');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array(), 'valida_arquivo(form)') ?>

	function ir_lista()
	{
		location.href = "<?= site_url('gestao/pauta_sg/minhas') ?>";
	}

	function ir_responder()
	{
		location.href = "<?= site_url('gestao/pauta_sg/responder/'.$row['cd_pauta_sg'].'/'.$assunto['cd_pauta_sg_assunto']) ?>";
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

    function excluir(cd_pauta_sg_assunto_anexo)
	{
		var confirmacao = 'Deseja excluir o arquivo?\n\n'+
			'Clique [Ok] para Sim\n\n'+
			'Clique [Cancelar] para Não\n\n';

		if(confirm(confirmacao))
		{ 
			location.href = "<?= site_url('gestao/pauta_sg/responder_anexo_excluir/'.$row['cd_pauta_sg'].'/'.$assunto['cd_pauta_sg_assunto']) ?>/"+cd_pauta_sg_assunto_anexo;
		}
	}
	
	$(function(){
		configure_result_table();
	});
</script>

<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_responder', 'Responder', FALSE, 'ir_responder();');
	$abas[] = array('aba_anexo', 'Arquivo', TRUE, 'location.reload();');

	$head = array( 
		'Dt Inclusão',
		'Arquivo',
		'Usuário',
		''
	);

	$body = array();

	foreach($collection as $item)
	{	
	    $body[] = array(
			$item['dt_inclusao'],
			array(anchor(base_url().'up/pauta/'.$item['arquivo'], $item['arquivo_nome'], array('target' => '_blank')), 'text-align:left;'),
			array($item['ds_usuario'], 'text-align:left;'),
			(trim($row['dt_aprovacao']) == '' ? '<a href="javascript:void(0);" onclick="excluir('.$item['cd_pauta_sg_assunto_anexo'].')">[excluir]</a>' : '')
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo aba_start($abas);
		echo form_start_box('default_box', 'Pauta');
			echo form_default_row('nr_ata', 'Nº da Ata:', '<label class="label label-inverse">'.$row['nr_ata'].'</label>');
			echo form_default_row('fl_sumula', 'Colegiado:', '<span class="'.$row["class_sumula"].'">'.$row["fl_sumula"].'</span>');

			if(trim($row['ds_tipo_reuniao']) != '')
			{
				echo form_default_row('ds_tipo_reuniao', 'Tipo Reunião:', $row['ds_tipo_reuniao']);
			}

			echo form_default_row('dt_reuniao', 'Dt. Reunião:', $row['dt_pauta'].' '.$row['hr_pauta']);

			if(trim($row['dt_pauta_sg_fim']) != '')
			{	
				echo form_default_row('dt_reuniao_fim', 'Dt. Reunião Encerramento:', $row['dt_pauta_sg_fim'].' '.$row['hr_pauta_sg_fim']);
			}

			echo form_default_row('dt_limite', 'Dt. Limite:', '<span class="label label-warning">'.$row['dt_limite'].'</span>');
		echo form_end_box('default_box');
		
		echo form_start_box('default_box', 'Assunto');
			echo form_default_row('cd_gerencia_responsavel', 'Gerência Responsável:', $assunto['cd_gerencia_responsavel']);
			echo form_default_row('ds_usuario_responsavel', 'Responsável:', $assunto['ds_usuario_responsavel']);
			echo form_default_row('cd_gerencia_substituto', 'Gerência Substituto:', $assunto['cd_gerencia_substituto']);
			echo form_default_row('ds_usuario_substituto', 'Substituto:', $assunto['ds_usuario_substituto']);

			if(trim($row['fl_sumula']) == 'DE')
			{	
				echo form_default_row('ds_diretoria', 'Diretoria:', $assunto['ds_diretoria']);
			}

			echo form_default_textarea('ds_pauta_sg_assunto', 'Assunto:', $assunto, 'style="height:80px;"');
		echo form_end_box('default_box');

		if(trim($row['dt_aprovacao']) == '')
		{
			echo form_open('gestao/pauta_sg/responder_anexo_salvar');
				echo form_start_box('default_anexo_box', 'Cadastro - Arquivo');
					echo form_default_hidden('cd_pauta_sg', '', $row);	
					echo form_default_hidden('cd_pauta_sg_assunto', '', $assunto);	
					echo form_default_upload_multiplo('arquivo_m', 'Arquivo: (*)', 'pauta', 'validaArq');
					echo form_default_row('', '', '<i>Adicione o(s) arquivo(s) e depois clique no botão [Anexar]</i>');
				echo form_end_box('default_anexo_box');
				echo form_command_bar_detail_start();
				echo form_command_bar_detail_end();
			echo form_close();
		}

		echo $grid->render();
		echo br(2);
	echo aba_end();

	$this->load->view('footer_interna');
?>