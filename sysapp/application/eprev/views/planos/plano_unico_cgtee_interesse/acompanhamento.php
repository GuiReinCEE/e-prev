<?php
set_title('Plano Único CGTEE - Pré-Cadastro');
$this->load->view('header');
?>
<script>
	<?php echo form_default_js_submit(Array('descricao')); ?>
	function ir_lista()
	{
		location.href='<?php echo site_url("planos/plano_unico_cgtee_interesse"); ?>';
	}
	
	function excluir(cd_plano_unico_cgtee_interesse_acompanhamento)
	{
		if(confirm("Deseja excluir o acompanhamento"))
		{
			location.href='<?php echo site_url("planos/plano_unico_cgtee_interesse/excluir_acompanhamento/".intval($row['cd_plano_unico_cgtee_interesse'])); ?>/'+cd_plano_unico_cgtee_interesse_acompanhamento;
		}
	}
	
	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),
		[
			"DateTimeBR",
			"CaseInsensitiveString",
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
		ob_resul.sort(0, true);
	}

	$(function(){
		configure_result_table();
	});
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_cadastro', 'Acompanhamento', TRUE, 'location.reload();');

$body=array();
$head = array( 
	'Dt. Cadastro',
	'Acompanhamento',
	'Usuário'
);

foreach( $collection as $item )
{
	$body[] = array(
		$item['dt_inclusao'],
		array($item['descricao'], 'text-alig:justify;'),
		array($item['nome'], 'text:left;')
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;

echo aba_start( $abas );
	echo form_open('planos/plano_unico_cgtee_interesse/salvar_acompanhamento');
	echo form_start_box( "default_box", "Pré-Cadastro" );
		echo form_default_hidden('cd_plano_unico_cgtee_interesse', "", $row['cd_plano_unico_cgtee_interesse']);		
		echo form_default_row('nome', 'Nome :', $row['nome']);
		echo form_default_row('email', 'Email :', $row['email']);
		echo form_default_row('telefone_1', 'Telefone :', $row['telefone_1']);
		echo form_default_row('telefone_2', 'Telefone :', $row['telefone_2']);
		echo form_default_row('dt_inclusao', 'Dt Inclusão :', $row['dt_inclusao']);
		echo form_default_textarea('descricao_contato', 'Contato :', $row['descricao'], 'readonly="" style="height:100px;"');
	echo form_end_box("default_box");
	echo form_start_box( "default_acompanhamento_box", "Acompanhamento" );
		echo form_default_textarea('descricao', 'Descrição :', '');
	echo form_end_box("default_acompanhamento_box");
	echo form_command_bar_detail_start();
		echo button_save("Salvar");
	echo form_command_bar_detail_end();
	echo form_close();
	echo $grid->render();
	echo br(2);
echo aba_end();
$this->load->view('footer_interna');
?>