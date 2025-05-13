<?php
set_title('Entidades');
$this->load->view('header');
?>
<script>
	<?php
		echo form_default_js_submit(Array('ds_entidade', 'cnpj'));
	?>
	function ir_lista()
	{
		location.href='<?php echo site_url("atividade/entidade"); ?>';
	}
	
	function ir_usuarios()
	{
		location.href='<?php echo site_url("atividade/entidade_usuario"); ?>';
	}

	function excluir(cd_entidade_recolhimento)
	{
		if(confirm("Deseja excluir o código de recolhimento?"))
		{
			location.href='<?= site_url("atividade/entidade/excluir_recolhimento/".intval($row['cd_entidade'])) ?>/'+cd_entidade_recolhimento;
		}
	}

	/*
	function ir_verbas()
	{
		location.href='<?php echo site_url("atividade/entidade/verbas/".intval($row['cd_entidade'])); ?>';
	}
	
	function ir_empresas()
	{
		location.href='<?php echo site_url("atividade/entidade/empresas/".intval($row['cd_entidade'])); ?>';
	}
	*/
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');
$abas[] = array('aba_cadastro', 'Usuários', FALSE, 'ir_usuarios();');
	
echo aba_start( $abas );
	echo form_open('atividade/entidade/salvar');
		echo form_start_box( "default_box", "Entidade" );
			echo form_default_hidden('cd_entidade', "", $row);	
			echo form_default_text('ds_entidade', 'Entidade :*', $row, 'style="width:300px;"');
			echo form_default_cnpj('cnpj', 'CNPJ :*', $row);
			echo form_default_telefone('telefone1', 'Telefone 1 :',$row);
			echo form_default_telefone('telefone2', 'Telefone 2 :',$row);
		echo form_end_box("default_box");
		echo form_command_bar_detail_start();
			if(trim($row['dt_exclusao']) == "")
			{
				echo button_save("Salvar");	
			}
		echo form_command_bar_detail_end();
	echo form_close();

	if(intval($row["cd_entidade"]) > 0)
	{
		$body = array();
		$head = array( 
			'Descrição.',
			'Código de Recolhimento',
			''
		);

		foreach( $collection as $item )
		{
			$body[] = array(
				anchor("atividade/entidade/cadastro/".$row["cd_entidade"]."/".$item["cd_entidade_recolhimento"], $item["cd_recolhimento"]),
				array($item["ds_entidade_recolhimento"], 'text-align:left;'),
				'<a href="javascript:void(0)"; onclick="excluir('.$item["cd_entidade_recolhimento"].')">[excluir]</a>'
			);
		}

		$this->load->helper('grid');
		$grid = new grid();
		$grid->head = $head;
		$grid->body = $body;


		echo form_open('atividade/entidade/salvar_recolhimento');
			echo form_start_box( "default_reco_box", "Recolhimento" );
				echo form_default_hidden('cd_entidade', "", $row);	
				echo form_default_hidden('cd_entidade_recolhimento', "", $recolhimento);	
				echo form_default_text('ds_entidade_recolhimento', 'Descrição :', $recolhimento, 'style="width:300px;"');
				echo form_default_integer('cd_recolhimento', "Código de Recolhimento :", $recolhimento);
			echo form_end_box("default_reco_box");
			echo form_command_bar_detail_start();
				echo button_save("Salvar");	
			echo form_command_bar_detail_end();
		echo form_close();
		echo $grid->render();
	}

echo aba_end();
$this->load->view('footer_interna');
?>