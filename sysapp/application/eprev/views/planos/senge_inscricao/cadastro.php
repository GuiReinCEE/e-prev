<?php
set_title('Inscrições no SENGE');
$this->load->view('header');
?>
<script>
<?php
    echo form_default_js_submit(Array('nome', 'cpf'));
?>
    
    function ir_lista()
    {
        location.href='<?php echo site_url("planos/senge_inscricao"); ?>';
    }
	
	function ir_contato()
    {
        location.href='<?php echo site_url("planos/senge_inscricao/contato/".$row['cd_registro_empregado']); ?>';
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
	
	function imprimir_pedido_inscricao()
	{
		alert('<?php echo $mensagem; ?>');
		
		window.open('<?php echo site_url("planos/senge_inscricao/pedido_inscricao/".$row['cd_registro_empregado']); ?>');
	}
	
	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),
		[
			'CaseInsensitiveString',
			'Number'
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
	
	$(function(){
		configure_result_table();
	});
    
</script>

<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_nc', 'Cadastro', TRUE, 'location.reload();');
$abas[] = array('aba_lista', 'Contato', FALSE, 'ir_contato();');
$abas[] = array('aba_lista', 'Documentos', FALSE, 'ir_documento();');
$abas[] = array('aba_lista', 'Anexo', FALSE, 'ir_anexo();');
$abas[] = array('aba_lista', 'Histórico', FALSE, 'ir_historico();');

$arr_sexo[] = array('value' => 'M', 'text' => 'Masculino');
$arr_sexo[] = array('value' => 'F', 'text' => 'Feminino');

$arr_desconto[] = array('value' => '2', 'text' => 'NÃO opto pela tabela regressiva');
$arr_desconto[] = array('value' => '1', 'text' => 'Opto pela tabela regressiva');

$body = array();
$head = array( 
	'Nome',
	'Percentual'
);

foreach( $arr_peculio as $item )
{
	$body[] = array(
		array($item["nome"], "text-align:left;"),
		 number_format($item['percentual'],2,',','').'%'
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;

	
echo aba_start( $abas );
    echo form_open('planos/senge_inscricao/salvar', 'name="filter_bar_form"');
        echo form_start_box( "default_box", "Cadastro" );
            echo form_default_hidden('cd_empresa', '', $row['cd_empresa']);
			echo form_default_hidden('cd_registro_empregado', '', $row['cd_registro_empregado']);
			echo form_default_hidden('seq_dependencia', '', $row['seq_dependencia']);
			if(trim($row['dt_documentacao_confirmada']) != "")
			{
				echo form_default_row('', '', '<b style="color:blue; font-size:200%">A documentação já está confirmada, verifique os dados através do Atendimento ao Participante.</b>');
			}
			
			if(trim($row['cd_registro_patroc']) != '')
			{
				echo form_default_row('', '', '<b style="color:green; font-size:200%">Ingresso autorizado pelo Senge.</b>');
			}
			elseif(trim($row['usuario_alteracao']) != '')
			{
				echo form_default_row('', '', '<b style="color:red; font-size:200%">Ingresso negado pelo Senge.</b>');
			}
			else
			{
				echo form_default_row('', '', '<b style="color:black; font-size:200%">Ainda sem manifestação do Senge.</b>');
			}
			
            echo form_default_row('re', 'RE :', $row['cd_registro_empregado']);
			echo form_default_text('nome', 'Nome :*', $row, 'style="width:400px;"');
			echo form_default_cpf('cpf', "CPF :*", $row);
			echo form_default_integer('rg', "RG :", $row);
			echo form_default_text('emissor', 'Emissor :', $row, 'style="width:100px;"');
			echo form_default_date('dt_emissao', 'Dt Emissão :', $row);
			echo form_default_text('crea', 'CREA :', $row, 'style="width:250px;"');
			echo form_default_dropdown('sexo', 'Sexo :', $arr_sexo, array($row['sexo']));
			echo form_default_date('dt_nascimento', 'Dt Nascimento :', $row);
			echo form_default_dropdown('cd_estado_civil', 'Estado Civil :', $arr_estado_civil, array($row['cd_estado_civil']));
			echo form_default_dropdown('cd_grau_instrucao', 'Grau de Instrução :', $arr_grau_instrucao, array($row['cd_grau_instrucao']));
			echo form_default_text('nome_mae', 'Nome Mãe :', $row, 'style="width:400px;"');
			echo form_default_text('nome_pai', 'Nome Pai :', $row, 'style="width:400px;"');
        echo form_end_box("default_box");
		echo form_start_box( "default_banco_box", "Informações Bancárias" );
			echo form_default_dropdown('cd_instituicao', 'Banco :', $arr_banco, array($row['cd_instituicao']));
			echo form_default_dropdown('cd_agencia', 'Agência :', $arr_agencia, array($row['cd_agencia']));
			echo form_default_text('conta_bco', 'Conta :', $row, 'style="width:250px;"');
		echo form_end_box("default_banco_box");
		echo form_start_box( "default_outras_box", "Outras Informações" );
			echo form_default_row('nome_titular', 'Nome Titular :', $row['nome_titular']);
			echo form_default_row('matricula_titular', 'Matrícula Titular :', $row['matricula_titular']);
			echo form_default_row('', '', '<i>(somente se dependente)</i>');
			echo form_default_row('cd_registro_patroc', 'Matrícula :', $row['cd_registro_patroc']);
			echo form_default_row('seq_registro_patroc', 'Sequência :', $row['seq_registro_patroc']);
			echo form_default_row('dt_adesao_instituidor', 'Dt Adesão ao Senge :', $row['dt_adesao_instituidor']);
			echo form_default_row('dt_alteracao', 'Dt Negação ao Senge :', $row['dt_alteracao']);
			echo form_default_row('usuario_alteracao', 'Responsável Senge :', $row['usuario_alteracao']);
			echo form_default_row('', '', '<i>Quem negou o pedido de inscrição</i>');
			echo form_default_dropdown('opt_irpf', 'Opção de Desconto de IR :', $arr_desconto, array($row['opt_irpf']));
		echo form_end_box("default_outras_box");
		echo form_start_box( "default_peculio_box", "Beneficiários Pecúlio" );
			echo $grid->render();
		echo form_end_box("default_peculio_box");
        echo form_command_bar_detail_start();     
			if(trim($row['dt_documentacao_confirmada']) == "")
			{
				echo button_save("Salvar");
			}
			echo button_save("Imprimir Pedido de Inscrição", 'imprimir_pedido_inscricao();', 'botao_disabled');
        echo form_command_bar_detail_end();
    echo form_close();
	
    echo br();	
echo aba_end();

$this->load->view('footer_interna');
?>