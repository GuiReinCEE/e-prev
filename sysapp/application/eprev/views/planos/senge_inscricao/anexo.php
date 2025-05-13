<?php
set_title('Inscrições no SENGE');
$this->load->view('header');
?>
<script>
<?php
    echo form_default_js_submit(Array(''));
?>
    
    function ir_lista()
    {
        location.href='<?php echo site_url("planos/senge_inscricao"); ?>';
    }
	
	function ir_cadastro()
    {
        location.href='<?php echo site_url("planos/senge_inscricao/cadastro/".$row['cd_registro_empregado']); ?>';
    }
	
	function ir_contato()
    {
        location.href='<?php echo site_url("planos/senge_inscricao/contato/".$row['cd_registro_empregado']); ?>';
    }
	
	function ir_documento()
    {
        location.href='<?php echo site_url("planos/senge_inscricao/documento/".$row['cd_registro_empregado']); ?>';
    }
	
	function ir_historico()
    {
        location.href='<?php echo site_url("planos/senge_inscricao/historico/".$row['cd_registro_empregado']); ?>';
    }
	
	function validaArq(enviado, nao_enviado, arquivo)
	{
		$("form").submit();
	}
	
	function excluir_anexo(cd_inscritos_anexo)
	{
		if(confirm('Deseja excluir o anexo?'))
		{
			location.href='<?php echo site_url("planos/senge_inscricao/excluir_anexo/".$row['cd_registro_empregado']); ?>/'+cd_inscritos_anexo;
		}
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
$abas[] = array('aba_nc', 'Cadastro', FALSE, 'ir_cadastro();');
$abas[] = array('aba_lista', 'Contato', FALSE, 'ir_contato();');
$abas[] = array('aba_lista', 'Documentos', FALSE, 'ir_documento();');
$abas[] = array('aba_lista', 'Anexo', TRUE, 'location.reload();');
$abas[] = array('aba_lista', 'Histórico', FALSE, 'ir_historico();');

$body = array();
$head = array( 
	'Dt. Inclusão',
	'Arquivo',
	'Usuário',
	''
);

foreach( $collection as $item )
{
	$body[] = array(
		$item["dt_inclusao"],
		array(anchor(base_url().'up/senge_inscricao/'.$item['arquivo'], $item['arquivo_nome'] , array('target' => "_blank")), "text-align:left;"),
		$item["nome"],
		'<a href="javascript:void(0)" onclick="excluir_anexo('.intval($item['cd_inscritos_anexo']).');" >[excluir]</a>'
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;

echo aba_start( $abas );
    echo form_open('planos/senge_inscricao/salvar_anexo', 'name="filter_bar_form"');
        echo form_start_box( "default_box", "Cadastro" );
            echo form_default_hidden('cd_empresa', '', $row['cd_empresa']);
			echo form_default_hidden('cd_registro_empregado', '', $row['cd_registro_empregado']);
			echo form_default_hidden('seq_dependencia', '', $row['seq_dependencia']);
			echo form_default_row('re', 'RE :', $row['cd_registro_empregado']);
			echo form_default_row('nome', 'Nome :', $row['nome']);
		echo form_end_box("default_box");	
		echo form_start_box( "default_anexo_box", "Anexo" );
			echo form_default_upload_multiplo('arquivo_m', 'Arquivo :*', 'senge_inscricao', 'validaArq');
		echo form_end_box("default_anexo_box");
        echo form_command_bar_detail_start();     
        echo form_command_bar_detail_end();
    echo form_close();
	echo $grid->render();
    echo br();	
	
echo aba_end();

$this->load->view('footer_interna');
?>