<?php
set_title('Inscrições no SENGE');
$this->load->view('header');
?>
<script>
<?php
    echo form_default_js_submit(Array('endereco', 'cpf'));
?>
    
    function ir_lista()
    {
        location.href='<?php echo site_url("planos/senge_inscricao"); ?>';
    }
	
	function ir_cadastro()
    {
        location.href='<?php echo site_url("planos/senge_inscricao/cadastro/".$row['cd_registro_empregado']); ?>';
    }
	
	function ir_documento()
    {
        location.href='<?php echo site_url("planos/senge_inscricao/documento/".$row['cd_registro_empregado']); ?>';
    }
	
	function ir_anexo()
    {
        location.href='<?php echo site_url("planos/senge_inscricao/anexo/".$row['cd_registro_empregado']); ?>';
    }
	
	function ir_historico()
    {
        location.href='<?php echo site_url("planos/senge_inscricao/historico/".$row['cd_registro_empregado']); ?>';
    }
	
	function gerar_email_senha()
	{
		if(confirm("Deseja Gerar Email de Senha?"))
		{
			location.href='<?php echo site_url("planos/senge_inscricao/email_senha/".$row['cd_registro_empregado']); ?>';
		}
	}
	
	function gerar_email_confirmacao()
	{
		if(confirm("Deseja Gerar Email de Confirmação de Inscrição?"))
		{
			location.href='<?php echo site_url("planos/senge_inscricao/email_confirmacao/".$row['cd_registro_empregado']); ?>';
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
$abas[] = array('aba_lista', 'Contato', TRUE, 'location.reload();');
$abas[] = array('aba_lista', 'Documentos', FALSE, 'ir_documento();');
$abas[] = array('aba_lista', 'Anexo', FALSE, 'ir_anexo();');
$abas[] = array('aba_lista', 'Histórico', FALSE, 'ir_historico();');

$body = array();
$head = array( 
	'Data',
	'Assunto',
	'Email',
	''
);

foreach( $collection as $item )
{
	$body[] = array(
		$item["dt_envio"],
		array($item["assunto"],'text-alig:left;'),
		$item["para"],
		''
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;

echo aba_start( $abas );
    echo form_open('planos/senge_inscricao/salvar_contato', 'name="filter_bar_form"');
        echo form_start_box( "default_box", "Cadastro" );
            echo form_default_hidden('cd_empresa', '', $row['cd_empresa']);
			echo form_default_hidden('cd_registro_empregado', '', $row['cd_registro_empregado']);
			echo form_default_hidden('seq_dependencia', '', $row['seq_dependencia']);
			echo form_default_row('re', 'RE :', $row['cd_registro_empregado']);
			echo form_default_row('nome', 'Nome :', $row['nome']);
		echo form_end_box("default_box");	
		echo form_start_box( "default_endereco_box", "Endereçamento" );
			echo form_default_textarea('endereco', 'Endereço :', $row, 'style="height:50px;"');
			echo form_default_text('bairro', 'Bairro :', $row,'style="width:400px;"');
			echo form_default_dropdown('uf', 'Estado :', $arr_estado, array($row['uf']));
			echo form_default_dropdown('cidade', 'Cidade :', $arr_cidade, array($row['cidade']));
			echo form_default_cep('cep', 'CEP :', $row['cep'].'-'.$row['complemento_cep']);
        echo form_end_box("default_endereco_box");
		echo form_start_box( "default_contato_box", "Contato" );
		    echo form_default_telefone('telefone', 'Telefone :', '('.$row['ddd'].') '.$row['telefone']);
			echo form_default_integer('ramal', 'Ramal :', $row);
			echo form_default_telefone('celular', 'Celular :', '('.$row['ddd_cel'].') '.$row['celular']);
			echo form_default_telefone('fax', 'Fax :', '('.$row['ddd_fax'].') '.$row['fax']);
			echo form_default_text('email', 'Email :', $row,'style="width:400px;"');
		echo form_end_box("default_contato_box");
        echo form_command_bar_detail_start();    
			if(trim($row['dt_documentacao_confirmada']) == "")
			{
				echo button_save("Salvar");
				echo button_save("Gerar Email de Senha", 'gerar_email_senha();', 'botao_vermelho');
				echo button_save("Gerar Email de Confirmação de Inscrição", 'gerar_email_confirmacao();', 'botao_vermelho');
			}
        echo form_command_bar_detail_end();
    echo form_close();
	echo $grid->render();
    echo br();	
	
echo aba_end();

$this->load->view('footer_interna');
?>