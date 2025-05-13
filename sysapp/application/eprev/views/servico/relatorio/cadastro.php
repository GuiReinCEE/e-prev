<?php
set_title('Meus Relatórios');
$this->load->view('header');
?>
<script>
<?php
    echo form_default_js_submit(Array('titulo'));
?>
    
    function ir_lista()
    {
        location.href='<?php echo site_url("servico/relatorio/relatorio_dinamico"); ?>';
    }
	
	function adicionar_coluna()
	{
		if($("#numero_colunas").val() < 8)
		{
			if($('#nome_coluna').val() == '' || $('#alinhamento').val() == '' || $('#largura_coluna').val() == '')
			{
				alert("Preencha todos os campos.");
			}
			else
			{
				$.post('<?php echo site_url('/servico/relatorio/salvar_coluna'); ?>',
				{
					cd_relatorio : $('#cd_relatorio').val(),
					cd_coluna    : $('#cd_coluna').val(),
					nome_coluna  : $('#nome_coluna').val(),
					alinhamento  : $('#alinhamento').val(),
					largura      : $('#largura_coluna').val()
				},
				function(data)
				{
					listar_colunas()
					
					$('#cd_coluna').val('');
					$('#nome_coluna').val('');
					$('#alinhamento').val('');
					$('#largura_coluna').val('');
				});
			}
		}
		else
		{
			alert("O maxímo de colunas(8) foi atingido.");
		}
	}
	
	function listar_colunas()
	{
		$('#result_div').html("<?php echo loader_html(); ?>");
		$.post('<?php echo site_url('/servico/relatorio/listar_colunas'); ?>',
		{
			cd_relatorio : $('#cd_relatorio').val()
		},
		function(data)
		{
			$('#result_div').html(data);
		});
	}
	
	function excluir_coluna(cd_coluna)
	{
		if(confirm("Deseja excluir a coluna?"))
		{
			$.post('<?php echo site_url('/servico/relatorio/excluir_coluna/'); ?>',
			{
				cd_coluna    : cd_coluna,
				cd_relatorio : $('#cd_relatorio').val()
			},
			function(data)
			{
				listar_colunas();
			});
		}
	}
	
	function excluir_relatorio()
	{
		if(confirm('Deseja excluir?'))
		{
			location.href="<?php echo site_url("servico/relatorio/excluir/".intval($row['cd_relatorio'])); ?>";
		}
	}

	
	$(function(){
		if($("#cd_relatorio").val() > 0)
		{
			listar_colunas();
		}
	});
</script>

<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_nc', 'Cadastro', TRUE, 'location.reload();');

$arr_orientacao[] = array('value' => 'L', 'text' => 'Esquerda');
$arr_orientacao[] = array('value' => 'C', 'text' => 'Centralizado');
$arr_orientacao[] = array('value' => 'R', 'text' => 'Direita');

$arr_mostrar[] = array('value' => 'S', 'text' => 'Sim');
$arr_mostrar[] = array('value' => 'N', 'text' => 'Não');

echo aba_start( $abas );
    echo form_open('servico/relatorio/salvar', 'name="filter_bar_form"');
        echo form_start_box("default_box", "Cadastro");
			echo form_default_hidden('cd_relatorio', '', $row['cd_relatorio']);
			echo form_default_text('titulo', 'Título do Relatório :*', $row, 'style="width:400px;"');
			echo form_default_editor_code('qr_sql', 'SQL:(*)', $row, "style='width:800px; height: 300px;'");			
			
			echo form_default_row('query', 'Comando de seleção(select...) :', $row["query"], 'style="height:100px;"');
			echo form_default_row('clausula_where', 'Critérios(where...) :', $row["clausula_where"], 'style="height:60px;"');
			echo form_default_row('ordem', 'Ordem :', $row["ordem"], 'style="height:60px;"');
			echo form_default_row('grupo', 'Agrupamento :', $row["grupo"], 'style="height:60px;"');
			echo form_default_row('esquema_tabela', 'Esquema/Tabela :', $row["esquema_tabela"], 'style="height:60px;"');
			#echo form_default_dropdown('esquema_tabela', 'Esquema/Tabela :', $arr_esquema_tabela, array($row['esquema_tabela']));
			
			
			echo filter_usuario_ajax('cd_proprietario', $row['divisao'], $row['cd_proprietario'], 'Proprietário :', 'Gerência :');
			echo form_default_dropdown('restricao_acesso', 'Restrição de Acesso :', $arr_restricao, array($row['restricao_acesso']));
			echo form_default_dropdown('cd_projeto', 'Projeto/Sistema :', $arr_sistema, array($row['cd_projeto']));
			echo form_default_text('especie', 'Espécie :', $row, 'style="width:80px;"');
        echo form_end_box("default_box");
		
		echo form_start_box("default_layout_box", "Layout");
			echo form_default_dropdown('tipo', 'Tipo :', $arr_tipo, array($row['tipo']));
			echo form_default_dropdown('fonte', 'Fonte :', $arr_fonte, array($row['fonte']));
			echo form_default_text('tam_fonte_titulo', 'Tamanho da Fonte do Título :', $row, 'style="width:80px;"');
			echo form_default_text('tam_fonte', 'Tamanho da Fonte :', $row, 'style="width:80px;"');
			echo form_default_text('pos_x', 'Margem Esquerda(mm) :', $row, 'style="width:80px;"');
			echo form_default_text('largura', 'Largura(mm) :', $row, 'style="width:80px;"');
			echo form_default_dropdown('orientacao', 'Orientação :', $arr_orientacao, array($row['orientacao']));
			echo form_default_dropdown('mostrar_cabecalho', 'Mostrar Cabeçalho :', $arr_mostrar, array($row['mostrar_cabecalho']));
			echo form_default_dropdown('mostrar_linhas', 'Mostrar Linhas :', $arr_mostrar, array($row['mostrar_linhas']));
        echo form_end_box("default_layout_box");
		
        echo form_command_bar_detail_start();     
            echo button_save("Salvar");
			if(intval($row['cd_relatorio']) > 0)
			{
				echo button_save("Excluir", 'excluir_relatorio();', 'botao_vermelho');
			}
        echo form_command_bar_detail_end();
    echo form_close();
	
	if(intval($row['cd_relatorio']) > 0)
	{
		echo form_start_box("default_cabecalho_box", "Colunas e Cabeçalho");
			echo form_default_hidden('numero_colunas', '', 0);
			echo form_default_text('cd_coluna', 'Nº da Coluna :*', '', 'style="width:400px;"');
			echo form_default_text('nome_coluna', 'Coluna :*', '', 'style="width:400px;"');
			echo form_default_dropdown('alinhamento', 'Alinhamento :*', $arr_orientacao, array());
			echo form_default_text('largura_coluna', 'Largura(mm) :*', '', 'style="width:80px;"');
		echo form_end_box("default_cabecalho_box");
		echo form_command_bar_detail_start();     
            echo button_save("Adicionar", "adicionar_coluna();");
        echo form_command_bar_detail_end();
		echo '<div id="result_div"></div>';
	}
    echo br(10);	
echo aba_end();

$this->load->view('footer_interna');
?>