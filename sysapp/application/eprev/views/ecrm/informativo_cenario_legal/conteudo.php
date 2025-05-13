<?php
	set_title('Informativo do Cenário Legal');
	$this->load->view('header');
?>
<script>
	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),
		[
			"Number",
			"CaseInsensitiveString",
			"CaseInsensitiveString",
			"DateTimeBR",
			"DateTimeBR",
			"CaseInsensitiveString",
			null
		]);
		ob_resul.onsort = function()
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

	function ir_lista()
	{
		location.href = "<?= site_url('ecrm/informativo_cenario_legal') ?>";
	}

	function ir_cadastro()
	{
		location.href = "<?= site_url('ecrm/informativo_cenario_legal/cadastro/'.$row['cd_edicao']) ?>";
	}

	function novo()
	{
		location.href = "<?= site_url('ecrm/informativo_cenario_legal/conteudo_cadastro/'.$row['cd_edicao']) ?>";
	}

	function enviar_email()
	{
		var confirmacao = 'Deseja enviar email cenário legal?\n\n'+
			'Clique [Ok] para Sim\n\n'+
			'Clique [Cancelar] para Não\n\n';

		if(confirm(confirmacao))
		{
			location.href = "<?= site_url('ecrm/informativo_cenario_legal/enviar_email/'.$row['cd_edicao']) ?>";
		}
	}

	$(function(){
		configure_result_table();
	});
</script>

<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_cadastro', 'Cadastro', FALSE, 'ir_cadastro();');
	$abas[] = array('aba_conteudo', 'Conteúdo', TRUE, 'location.reload();');

	$head = array( 
		'Código',
		'Título',
		'Seção',
		'Dt. Cadastro',
		'Dt. Cancelamento',
		'Usu. Cancelamento',
		''
	);

	$body = array();

	foreach($collection as $item)
	{
		$body[] = array(
			$item['cd_cenario'],
			array(anchor(site_url('ecrm/informativo_cenario_legal/conteudo_cadastro/'.$item['cd_edicao'].'/'.$item['cd_cenario']), $item['titulo']), 'text-align:left;'),
			array($item['ds_secao'], 'text-align:left;'),
			$item['dt_inclusao'],
			$item['dt_cancelamento'],
			array($item['ds_usuario_cancelamento'], 'text-align:left;'),
			anchor(site_url('ecrm/informativo_cenario_legal/anexo/'.$item['cd_edicao'].'/'.$item['cd_cenario']), '[anexos]')
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo aba_start($abas);
		echo form_start_box('default_box', $row['tit_capa']);
			echo form_default_row('', 'Edição:', '<span class="label label-inverse">'.intval($row['cd_edicao']).'</span>');
			echo form_default_row('', 'Data:', $row['dt_edicao']);
			echo form_default_row('', 'Título:', $row['tit_capa']);
			echo form_default_textarea('texto_capa', 'Texto:', $row, 'style="width:500px;:"');

			if(trim($row['dt_envio_email']) != '')
			{
				echo form_default_row('', 'Dt. Enviado:', $row['dt_envio_email']);
				echo form_default_row('', 'Usuário:', $row['ds_usuario_envio']);
			}
		echo form_end_box('default_box');	
		echo form_command_bar_detail_start();
			if(trim($row['dt_envio_email']) == '')
			{
				echo button_save('Novo Item Cenário Legal', 'novo()');
				echo button_save('Enviar Email Cenário Legal', 'enviar_email()', 'botao_vermelho');
			}
		echo form_command_bar_detail_end();
		echo br();
		echo $grid->render();
		echo br(2);
	echo aba_end();
	$this->load->view('footer');
?>
