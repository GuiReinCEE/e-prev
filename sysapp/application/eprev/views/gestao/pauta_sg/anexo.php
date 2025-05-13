<?php
	set_title('Pauta SG - Arquivo');
	$this->load->view("header");
?>
<script>
	<?= form_default_js_submit(array(), 'valida_arquivo(form)') ?>

	function ir_lista()
	{
		location.href = "<?= site_url('gestao/pauta_sg') ?>";
	}

	function ir_cadastro()
	{
		location.href = "<?= site_url('gestao/pauta_sg/cadastro/'.$row['cd_pauta_sg']) ?>";
	}

	function ir_assunto()
	{
		location.href = "<?= site_url('gestao/pauta_sg/assunto/'.$row['cd_pauta_sg']) ?>";
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

    function excluir(cd_pauta_sg_assunto_anexo)
	{
		var confirmacao = 'Deseja excluir o arquivo?\n\n'+
			'Clique [Ok] para Sim\n\n'+
			'Clique [Cancelar] para N�o\n\n';

		if(confirm(confirmacao))
		{ 
			location.href = "<?= site_url('gestao/pauta_sg/anexo_excluir/'.$row['cd_pauta_sg'].'/'.$assunto['cd_pauta_sg_assunto']) ?>/"+cd_pauta_sg_assunto_anexo;
		}
	}

	function validar_documentacao()
	{
		location.href = "<?= site_url('gestao/pauta_sg/set_pydio/'.$row['cd_pauta_sg'].'/'.$assunto['cd_pauta_sg_assunto']) ?>";
	}

	function historico(cd_pauta_sg_assunto_anexo)
	{
		location.href = "<?= site_url('gestao/pauta_sg/historico/'.$row['cd_pauta_sg'].'/'.$assunto['cd_pauta_sg_assunto']) ?>/"+cd_pauta_sg_assunto_anexo;
	}
	
	$(function(){
		configure_result_table();
	});
</script>

<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_cadastro', 'Cadastro', FALSE, 'ir_cadastro();');
	$abas[] = array('aba_assunto', 'Assuntos', FALSE, 'ir_assunto();');
	$abas[] = array('aba_anexo', 'Arquivo', TRUE, 'location.reload();');
	
	$head = array( 
		'Dt Inclus�o',
		'Arquivo',
		'RDS',
		'Usu�rio',
		''
	);

	$body = array();

	foreach($collection as $item)
	{	
	    $body[] = array(
			$item['dt_inclusao'],
			array(anchor(base_url().'up/pauta/'.$item['arquivo'], $item['arquivo_nome'], array('target' => '_blank')), 'text-align:left;'),
			(trim($item['fl_rds']) == 'S' ? $item['nr_ano_numero_rds'] : ''),
			$item['ds_usuario'],
			(trim($row['dt_aprovacao']) == '' ? '<a href="javascript:void(0);"" onclick="excluir('.$item['cd_pauta_sg_assunto_anexo'].')">[excluir]</a> ' : '').
			(trim($item['fl_rds']) == 'S' ? '<a href="javascript:void(0);"" onclick="historico('.$item['cd_pauta_sg_assunto_anexo'].')">[hist�rico]</a> ' : ''),
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo aba_start($abas);
		echo form_start_box('default_box', 'Pauta');
			echo form_default_row('nr_ata', 'N� da Ata:', '<label class="label label-inverse">'.$row['nr_ata'].'</label>');
			echo form_default_row('fl_sumula', 'Colegiado:', '<span class="'.$row["class_sumula"].'">'.$row["fl_sumula"].'</span>');

			if(trim($row['ds_tipo_reuniao']) != '')
			{
				echo form_default_row('ds_tipo_reuniao', 'Tipo Reuni�o:', $row['ds_tipo_reuniao']);
			}

			if(trim($row['dt_aprovacao']) != '')
			{
				echo form_default_row('dt_aprovacao', 'Dt. Encerramento:', $row['dt_aprovacao']);
				echo form_default_row('ds_usuario_responsavel', 'Usu�rio:', $row['ds_usuario_aprovacao']);
			}
		echo form_end_box('default_box');
		echo form_start_box('default_assunto_box', 'Assunto');
			echo form_default_row('cd_gerencia_responsavel', 'Ger�ncia Respons�vel:', $assunto['cd_gerencia_responsavel']);
			echo form_default_row('nome', 'Respons�vel:', $assunto['ds_usuario_responsavel']);

			if(trim($row['fl_sumula']) == 'DE')
			{	
				echo form_default_row('ds_diretoria', 'Diretoria:', $assunto['ds_diretoria']);
			}
			else if(trim($row['fl_sumula']) == 'IN')
			{
				echo form_default_row('ds_diretoria', '�rea de Atua��o:', $assunto['ds_diretoria']);
			}

			echo form_default_textarea('ds_pauta_sg_assunto', 'Assunto:', $assunto, 'style="height:80px;"');
			echo form_default_row('nr_tempo', 'Tempo (min):', $assunto['nr_tempo']);
		echo form_end_box('default_assunto_box');

		if(trim($row['dt_aprovacao']) == '')
		{
			echo form_open('gestao/pauta_sg/anexo_salvar');
				echo form_start_box('default_anexo_box', 'Cadastro - Arquivo');
					echo form_default_hidden('cd_pauta_sg', '', $row);	
					echo form_default_hidden('cd_pauta_sg_assunto', '', $assunto);	
					echo form_default_upload_multiplo('arquivo_m', 'Arquivo: (*)', 'pauta', 'validaArq');
					echo form_default_row('', '', '<i>Adicione o(s) arquivo(s) e depois clique no bot�o [Anexar]</i>');
					echo form_end_box("default_anexo_box");
				echo form_command_bar_detail_start();
					echo button_save('Validar Documenta��o Pydio', 'validar_documentacao()', 'botao_verde');
				echo form_command_bar_detail_end();
			echo form_close();
		}
		echo $grid->render();
		echo br(2);
	echo aba_end();

	$this->load->view("footer_interna");
?>