<?php
set_title('Entidades');
$this->load->view('header');
?>
<script>
	function ir_lista()
	{
		location.href='<?php echo site_url("atividade/entidade"); ?>';
	}
	
	function ir_usuarios()
	{
		location.href='<?php echo site_url("atividade/entidade_usuario"); ?>';
	}
	
	function ir_cadastro()
	{
		location.href='<?php echo site_url("atividade/entidade/cadastro/".intval($row['cd_entidade'])); ?>';
	}
	
	function ir_verbas()
	{
		location.href='<?php echo site_url("atividade/entidade/verbas/".intval($row['cd_entidade'])); ?>';
	}
	
	function salvar_patrocinadora(t)
	{
		var cd_empresa = t.val();

		if(t.attr('checked') == true)
		{
			$.post('<?php echo site_url('atividade/entidade/salvar_patrocinadora');?>',
			{
				cd_empresa   : cd_empresa,
				cd_entidade  : $('#cd_entidade').val()
			},function(data){});
		}
		else
		{
			$.post('<?php echo site_url('atividade/entidade/excluir_patrocinadora');?>',
			{
				cd_empresa   : cd_empresa,
				cd_entidade  : $('#cd_entidade').val()
			},function(data){});
		}		
	}
	
	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),[
			null,
			"Number",
			"CaseInsensitiveString"
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
		ob_resul.sort(1, false);
	}
	
	$(function(){
		configure_result_table();
	});
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_cadastro', 'Cadastro', FALSE, 'ir_cadastro();');
$abas[] = array('aba_cadastro', 'Verbas', FALSE, 'ir_verbas();');
$abas[] = array('aba_cadastro', 'Empresas', TRUE, 'location.reload();');
$abas[] = array('aba_cadastro', 'Usuários', FALSE, 'ir_usuarios();');

$body = array();
$head = array( 
	'',
	'Cód. Empresa',
	'Empresa'
);

foreach( $collection as $item )
{
	$campo_check = array(
		'name'    => 'cd_empresa_'.$item['cd_empresa'],
		'id'      => 'cd_empresa_'.$item['cd_empresa'],
		'value'   => intval($item['cd_empresa']),
		'checked' => (intval($item['cd_entidade_empresas']) > 0 ? TRUE : FALSE),
		'onclick' => 'salvar_patrocinadora($(this))'
	);	

	$body[] = array(
		form_checkbox($campo_check),
		$item['cd_empresa'],
		array($item['nome_empresa'], 'text-align:left;')
		
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
	
echo aba_start( $abas );
	echo form_start_box( "default_box", "Entidade" );
		echo form_default_hidden('cd_entidade', "", $row);	
		echo form_default_row('ds_entidade', 'Entidade :', $row['ds_entidade']);
		echo form_default_row('cnpj', 'CNPJ :', $row['cnpj']);
		echo form_default_row('telefone_1', 'Telefone 1 :',$row['telefone_1']);
		echo form_default_row('telefone_2', 'Telefone 2 :',$row['telefone_2']);
	echo form_end_box("default_box");
	echo $grid->render();
echo aba_end();
$this->load->view('footer_interna');
?>