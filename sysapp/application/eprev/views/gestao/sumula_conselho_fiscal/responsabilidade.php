<?php
set_title('Súmulas do Conselho Fiscal - Responsabilidades');
$this->load->view('header');
?>
<script>
<?php
    echo form_default_js_submit(Array('nr_sumula_conselho_fiscal_item', 'cd_diretoria', 'cd_gerencia', 'cd_responsavel', 'cd_substituto', 'cd_usuario_responsavel', 'cd_usuario_substituto'));
?>
    
    function ir_lista()
    {
        location.href='<?php echo site_url("gestao/sumula_conselho_fiscal"); ?>';
    }
    
    function ir_cadastro()
    {
        location.href='<?php echo site_url("gestao/sumula_conselho_fiscal/cadastro/".$cd_sumula_conselho_fiscal); ?>';
    }
	
	function ir_acompanhamento()
	{
		location.href='<?php echo site_url("gestao/sumula_conselho_fiscal/acompanhamento/".$row['cd_sumula_conselho_fiscal']); ?>';
	}
	
	function load()
    {
		$('#result_div').html("<?php echo loader_html(); ?>");

        $.post( '<?php echo site_url('/gestao/sumula_conselho_fiscal/listar_responsabilidade'); ?>',
        {
            cd_sumula_conselho_fiscal : $('#cd_sumula_conselho_fiscal').val(),
            fl_recebido               : $('#fl_recebido').val(),
            cd_gerencia               : $('#cd_gerencia_f').val(),
			cd_diretoria              : $('#cd_diretoria_f').val()
        },
        function(data)
        {
			$('#result_div').html(data);
            configure_result_table();
        });
    }
	
	function listar()
	{
		load();
	}
    
    function configure_result_table()
    {
        var ob_resul = new SortableTable(document.getElementById("table-1"),[
            "Number",
			"CaseInsensitiveString",
            "CaseInsensitiveString",
            "CaseInsensitiveString",
            "CaseInsensitiveString",
            "CaseInsensitiveString",
            "DateTimeBR",
			"DateTimeBR",
            "CaseInsensitiveString",
            "DateTimeBR",
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
        ob_resul.sort(0, false);
    }
	
	function excluir(cd_sumula_conselho_fiscal_item)
    {
        var confirmacao = 'Deseja excluir o item?\n\n'+
                'Clique [Ok] para Sim\n\n'+
                'Clique [Cancelar] para Não\n\n';
        
        if(confirm(confirmacao))
        {
            location.href='<?php echo site_url("gestao/sumula_conselho_fiscal/excluir_sumula/".$cd_sumula_conselho_fiscal); ?>/'+cd_sumula_conselho_fiscal_item;
        }
    }
	
	function enviar_todos()
    {
        var confirmacao = 'Deseja enviar todos os itens?\n\n'+
                'Clique [Ok] para Sim\n\n'+
                'Clique [Cancelar] para Não\n\n';
        
        if(confirm(confirmacao))
        {
            location.href='<?php echo site_url("gestao/sumula_conselho_fiscal/enviar_todos/".$cd_sumula_conselho_fiscal); ?>';
        }
    }
	
    function enviar(cd_sumula_conselho_fiscal_item)
    {
        var confirmacao = 'Deseja enviar o item?\n\n'+
                'Clique [Ok] para Sim\n\n'+
                'Clique [Cancelar] para Não\n\n';
        
        if(confirm(confirmacao))
        {
           location.href='<?php echo site_url("gestao/sumula_conselho_fiscal/enviar/".$cd_sumula_conselho_fiscal); ?>/'+cd_sumula_conselho_fiscal_item;
        }
    }
	
	function email_gerentes()
	{
		location.href='<?php echo site_url("gestao/sumula_conselho_fiscal/email_gerentes/".$cd_sumula_conselho_fiscal); ?>';
	}
    
	function get_usuarios_responsavel(cd_gerencia, cd_usuario)
	{
		$.post("<?= site_url('gestao/sumula/get_usuarios/') ?>",
		{
			cd_gerencia : cd_gerencia,
			cd_usuario  : cd_usuario
		},
		function(data)
		{
			var select = $('#cd_usuario_responsavel'); 
			
			if(select.prop) 
			{
				var options = select.prop('options');
			}
			else
			{
				var options = select.attr('options');
			}
			
			$('option', select).remove();
			
			options[options.length] = new Option('Selecione', '');
			
			$.each(data, function(val, text) {
				options[options.length] = new Option(text.text, text.value);
			});
			
			if(cd_usuario > 0)
			{
				$("#cd_usuario_responsavel").val(cd_usuario);
			}
		}, 'json', true);
	}
	
	function get_usuarios_substituto(cd_gerencia, cd_usuario)
	{
		$.post("<?= site_url('gestao/sumula/get_usuarios/') ?>",
		{
			cd_gerencia : cd_gerencia,
			cd_usuario  : cd_usuario
		},
		function(data)
		{
			var select = $('#cd_usuario_substituto'); 
			
			if(select.prop) 
			{
				var options = select.prop('options');
			}
			else
			{
				var options = select.attr('options');
			}
			
			$('option', select).remove();
			
			options[options.length] = new Option('Selecione', '');
			
			$.each(data, function(val, text) {
				options[options.length] = new Option(text.text, text.value);
			});
			
			if(cd_usuario > 0)
			{
				$("#cd_usuario_substituto").val(cd_usuario);
			}
		}, 'json', true);
	}
	
	function set_gerencia_responsaveis(cd_gerencia)
	{
		if($("#cd_responsavel").val() == "")
		{
			$("#cd_responsavel").val(cd_gerencia);
			get_usuarios_responsavel(cd_gerencia, 0);
		}
		if($("#cd_substituto").val() == "")
		{
			$("#cd_substituto").val(cd_gerencia);
			get_usuarios_substituto(cd_gerencia, 0);
		}
	}

	function enviar_fundacao()
    {
        var confirmacao = 'Deseja enviar email para todos?\n\n'+
                'Clique [Ok] para Sim\n\n'+
                'Clique [Cancelar] para Não\n\n';
        
        if(confirm(confirmacao))
        {
            location.href='<?= site_url("gestao/sumula_conselho_fiscal/enviar_fundacao/".$cd_sumula_conselho_fiscal) ?>';
        }
    }
	
    $(function(){
        load();
        
		<? if(intval($row_item['cd_responsavel']) > 0): ?>
		get_usuarios_responsavel('<?= trim($row_item['cd_divisao_responsavel']) ?>', <?= intval($row_item['cd_responsavel']) ?>);
		<? endif; ?>
		
		<? if(intval($row_item['cd_substituto']) > 0): ?>
		get_usuarios_substituto('<?= trim($row_item['cd_divisao_substituto']) ?>', <?= intval($row_item['cd_substituto']) ?>);
		<? endif; ?>
    });
      
</script>

<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_lista', 'Cadastro', FALSE, 'ir_cadastro();');
$abas[] = array('aba_nc', 'Responsabilidade', TRUE, 'location.reload();');
$abas[] = array('aba_lista', 'Acompanhamento', FALSE, 'ir_acompanhamento();');

$ar_recebido[] = Array('value' => 'S', 'text' => 'Sim');
$ar_recebido[] = Array('value' => 'N', 'text' => 'Não');	

echo aba_start( $abas );
    echo form_open('gestao/sumula_conselho_fiscal/salvar_item', 'name="filter_bar_form"');
        echo form_start_box( "default_box", "Cadastro" );
            echo form_default_hidden('cd_sumula_conselho_fiscal', '', $cd_sumula_conselho_fiscal);
            echo form_default_hidden('cd_sumula_conselho_fiscal_item', '', $cd_sumula_conselho_fiscal_item);
            echo form_default_integer('nr_sumula_conselho_fiscal', 'Súmula do Conselho :', $row['nr_sumula_conselho_fiscal'], "style='width:100%;border: 0px; font-weight:bold;' readonly");
			echo form_default_diretoria('cd_diretoria', 'Diretoria :*', $row_item['cd_diretoria']);
            echo form_default_gerencia('cd_gerencia', 'Gerência :*', $row_item['cd_gerencia'], 'onchange="set_gerencia_responsaveis(this.value)"');
            echo form_default_integer('nr_sumula_conselho_fiscal_item', 'Número do Item da Súmula :*', $row_item['nr_sumula_conselho_fiscal_item']);
            echo form_default_textarea('descricao', 'Descrição :', $row_item['descricao'], "style='height:100px;'");
        echo form_end_box("default_box");
		echo form_start_box( "default_box", "Responsáveis" );
			echo form_default_gerencia('cd_responsavel', 'Gerência  do Responsável:* ', $row_item['cd_divisao_responsavel'], 'onchange="get_usuarios_responsavel(this.value, 0)"');
			echo form_default_dropdown('cd_usuario_responsavel', 'Responsável :* ', $usuarios, $row_item['cd_responsavel']); 
			echo form_default_gerencia('cd_substituto', 'Gerência  do Substituto:* ', $row_item['cd_divisao_substituto'], 'onchange="get_usuarios_substituto(this.value, 0)"');
			echo form_default_dropdown('cd_usuario_substituto', 'Substituto :* ', $usuarios, $row_item['cd_substituto']);
		echo form_end_box("default_box");
        echo form_command_bar_detail_start();         
            if(intval($cd_sumula_conselho_fiscal_item) == 0)
			{
				echo button_save("Adicionar", "salvar(this.form);", 'botao', 'id="btn_ad"');
			}
			else
			{
				echo button_save("Salvar", "salvar(this.form);", 'botao', 'id="btn_ad"');
			}
            if(intval($nao_enviados) > 0)
            {
                echo button_save("Enviar Emails", "enviar_todos()", "botao_vermelho", 'id="enviar_emails"');
            }
			
			elseif(intval($enviados) > 0)
			{
				//echo button_save("Enviar para Gerentes", "email_gerentes()", "botao_disabled", 'id="enviar_gerentes"');
				echo button_save('Enviar Emails para Todos', 'enviar_fundacao()', 'botao_verde');
			}
			
            echo br(2);
            echo '<div id="enviar_info"></div>';

        echo form_command_bar_detail_end();
    echo form_close();
	
	echo br(2);	
    
	echo form_start_box( "default_filtros_box", "Filtros");
		echo form_default_dropdown('fl_recebido', 'Respondido: ', $ar_recebido, array('fl_recebido' => ''),'onchange="listar();"');	
		echo form_default_diretoria('cd_diretoria_f', 'Diretoria :', array('fl_recebido' => ''), 'listar();');	
		echo form_default_dropdown('cd_gerencia_f', 'Gerência: ', $arr_gerencia_cad, array('fl_recebido' => ''),'onchange="listar();"');			       	
	echo form_end_box("default_filtros_box");
	echo br();	
    echo '<div id="result_div"></div>';
    echo br(3);	
echo aba_end();

$this->load->view('footer_interna');
?>