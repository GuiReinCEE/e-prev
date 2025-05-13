<?php
	set_title('Recursos Humanos - Ocorrências do Ponto');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array('cd_usuario', 'mes_referencia', 'ano_referencia', 'cd_ocorrencia_ponto_tipo', 'nr_quantidade'), 'salvar(form)') ?>

	function salvar(form)
	{
		var mes_referencia = $("#mes_referencia").val();
		var ano_referencia = $("#ano_referencia").val();

		$("#dt_referencia").val("01-"+mes_referencia+"-"+ano_referencia);

		var text = "Salvar?\n\n"+
				   "[OK] para Sim\n\n"+
				   "[Cancelar] para Não";

		if(confirm(text))
		{
			form.submit();
		}
	}

	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),
		[
			"CaseInsensitiveString",
			"CaseInsensitiveString",
			"Number", 
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
		ob_resul.sort(0, false);
	}

	function ir_lista()
	{
		location.href = "<?= site_url('cadastro/rh') ?>";
	}

	function ir_cadastro()
	{
		location.href = "<?= site_url('cadastro/rh/cadastro/'.$usuario['cd_usuario']) ?>";
	}

	function ir_perfil()
	{
		location.href = "<?= site_url('cadastro/avatar/index/'.$usuario['cd_usuario']) ?>";
	}

	function cancelar()
	{
		location.href = "<?= site_url('cadastro/ocorrencia_ponto/index/'.$usuario['cd_usuario']) ?>";
	}

	$(function (){
		configure_result_table();
	});
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista()');
	$abas[] = array('aba_cadastro', 'Cadastro', FALSE, 'ir_cadastro()');
	$abas[] = array('aba_ocorrencias', 'Ocorrências do Ponto', TRUE, 'location.reload()');
	$abas[] = array('aba_perfil', 'Perfil', FALSE, 'ir_perfil()');

	$avatar_arquivo = $usuario['avatar'];
	
	if(trim($avatar_arquivo) == '')
	{
		$avatar_arquivo = $usuario['usuario'].'.png';
	}
	
	if(!file_exists('./up/avatar/'.$avatar_arquivo))
	{
		$avatar_arquivo = 'user.png';
	}

	$head = array( 
		'Mês',
		'Tipo de Ocorrência',
		'Quantidade',
		''
	);

	$body = array();

	foreach ($collection as $key => $item)
	{
		$body[] = array(
			$item['dt_referencia'],
			$item['ds_ocorrencia_ponto_tipo'],
			array($item['nr_quantidade'], 'text-align : center', TRUE),
			anchor('cadastro/ocorrencia_ponto/index/'.$usuario['cd_usuario'].'/'.$item['cd_ocorrencia_ponto'], '[editar]')
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo aba_start($abas);
		echo form_open('cadastro/ocorrencia_ponto/salvar');
			echo form_start_box('default_box', 'Cadastro');
				echo form_default_hidden('cd_usuario', '', $usuario);	
				echo form_default_row('', 'Código:', '<span class="label label-inverse">'.intval($usuario['cd_usuario']).'</span>');
				echo form_default_row('', 'Foto atual:', '<img height="48" width="48" src="'.base_url().'up/avatar/'.$avatar_arquivo.'">');
				echo form_default_row('', 'Usuário:', $usuario['usuario']);
				echo form_default_row('', 'Nome:', $usuario['nome'], "style='width:500px;'");
			echo form_end_box('default_box');
			echo form_start_box('default_box_ocorrencia', 'Cadastro de Ocorrência');
				echo form_default_hidden('cd_ocorrencia_ponto', '', $row['cd_ocorrencia_ponto']);
				echo form_default_mes_ano('mes_referencia', 'ano_referencia', 'Mês: (*)', $row['dt_referencia']);
				echo form_default_hidden('dt_referencia', '', $row['dt_referencia']);
				echo form_default_dropdown_db('cd_ocorrencia_ponto_tipo', 'Tipo de Ocorrência: (*)', array('rh_avaliacao.ocorrencia_ponto_tipo', 'cd_ocorrencia_ponto_tipo', 'ds_ocorrencia_ponto_tipo'), array($row['cd_ocorrencia_ponto_tipo']), '', 'ocorrencia_ponto_tipo', TRUE);
				echo form_default_integer('nr_quantidade', 'Quantidade: (*)', $row['nr_quantidade']);
			echo form_end_box('default_box_ocorrencia');
			echo form_command_bar_detail_start();
				echo button_save('Salvar');	

				if(intval($row['cd_ocorrencia_ponto']) > 0)
				{
					echo button_save('Cancelar', 'cancelar();', 'botao_disabled');	
				}
			echo form_command_bar_detail_end();
		echo form_close();
		echo br(2);
		echo $grid->render();
		echo br();
	echo aba_end();
	$this->load->view('footer');
?>