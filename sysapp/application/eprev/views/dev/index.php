<?php
set_title('Menu Manutenção');
$this->load->view('header');
?>

<script>
    <?php
    echo form_default_js_submit(Array('cd_menu_pai', 'ds_menu', 'ds_href'));
    ?>
        
    function filtrar()
    {
        $('#default_box_ordenacao').hide();
        
        if($('#cd_menu_filter').val() != '')
        {
            $('#result_div').html("<?php echo loader_html(); ?>");
            
            $.post('<?php echo site_url('dev/showmenu/index_result'); ?>',
            {
                fl_desativado : $('#fl_desativado').val(),
                cd_menu       : $('#cd_menu_filter').val()
            },
            function(data)
            {
                $('#result_div').html(data);
                
                $('#cd_menu_pai').val($('#cd_menu_filter').val());
            });
        }
        else
        {
            $('#result_div').html("");
            alert('Selecione o menu.');
        }
    }
    
    function carrega(t, cd_menu)
    {
        $('ul#menu a').css('font-weight', 'normal');
        t.css('font-weight', 'bold');
        
        $.post('<?php echo site_url('dev/showmenu/carrega'); ?>',
        {
            cd_menu : cd_menu
        },
        function(data)
        {
			$('#cd_padrao').val($('#cd_menu_filter').val());
            $('#cd_menu_pai').val(data.cd_menu_pai);
            $('#cd_menu').val(data.cd_menu);
            $('#ds_menu').val(data.ds_menu);
            $('#ds_href').val(data.ds_href);
            $('#ds_resumo').val(data.ds_resumo);
            $('#nr_ordem').val(data.nr_ordem);
            $('#save').val(1);
            $('#btn_cancelar').show();
            
            if(data.dt_desativado != '')
            {
                $('#btn_status').hide();
            }
            else
            {
                $('#btn_status').show();
                //$('#btn_status').attr('class', 'botao_vermelho');
                $('#btn_status').val('Desativar');
            }
            
            if(data.sub_menu > 0)
            {
                $('#default_box_ordenacao').show();
                $('#btn_ordenar').show();
                $('#default_box_ordenacao_content').html(data.lista);
            }
            else
            {
                $('#default_box_ordenacao').hide();
                $('#btn_ordenar').hide();
                $('#default_box_ordenacao_content').html('');
            }
                
        },'json');
    }
    
    function cancelar()
    {
        $('#cd_menu_pai').val('');
        $('#cd_menu').val('');
        $('#ds_menu').val('');
        $('#ds_href').val('');
        $('#ds_resumo').val('');
        $('#nr_ordem').val('');
        $('#save').val(0);
        $('ul#menu a').css('font-weight', 'normal');
        $('#btn_cancelar').hide();
        $('#btn_status').hide();
        $('#default_box_ordenacao').hide();
        $('#btn_ordenar').hide();
        $('#default_box_ordenacao_content').html('');
    }
    
    function ordenar()
    {
        var confirmacao = 'Deseja ordenar o menu?\n\n'+
                'Clique [Ok] para Sim\n\n'+
                'Clique [Cancelar] para Não\n\n';
        
        if(confirm(confirmacao))
        {
            var ordenacao = [];
        
            $('#sortable li').each(function(){
            ordenacao.push($(this).attr('order')); 
            });

            $.post('<?php echo site_url('dev/showmenu/ordenacao'); ?>',
            {
                'ordenacao[]' : ordenacao
            }, function(){
                location.href='<?php echo site_url("dev/showmenu/index/"); ?>/'+ $('#cd_menu_filter').val();
            });
        }
    }
    
    function desativar()
    {
        var confirmacao = 'Deseja desativar?\n\n'+
                'Clique [Ok] para Sim\n\n'+
                'Clique [Cancelar] para Não\n\n';
        
        if(confirm(confirmacao))
        {
            location.href='<?php echo site_url("dev/showmenu/desativar/"); ?>/'+ $('#cd_menu').val();
        }
    }
    
    function ir_mapa()
    {
        location.href='<?php echo site_url("dev/showmenu/mapa/"); ?>';
    }
    
    $(function(){
        if($('#cd_menu_filter').val() != '' && $('#cd_menu_filter').val() != '0')
        {
            filtrar();
            
            carrega($('#li_cd_menu_'+$('#cd_menu_filter').val()), $('#cd_menu_filter').val());
        }
    })
</script>

<style>
    .treeview ul li{
        margin: auto;
        padding-left: 20px;
    }
    
    .treeview a{
        font-size: 120%;
    }
    
    #sortable {
        font-size: 120%;
    }
	
	.handle {
		cursor: pointer;
	}
    
</style>
<?php
    $abas[] = array('aba_lista', 'Menu', TRUE, 'location.reload();');
    $abas[] = array('aba_lista', 'Mapa', FALSE, 'ir_mapa();');
    
    $arr_desativado[] = array('text' => 'Sim', 'value' => 'S');
    $arr_desativado[] = array('text' => 'Não', 'value' => 'N');
    
    echo aba_start( $abas );
        echo form_list_command_bar(array());
            echo form_start_box_filter();
                echo filter_dropdown('fl_desativado', 'Menu Desativado: ', $arr_desativado, array('N'));
                echo filter_dropdown('cd_menu_filter', 'Menu: ', $arr_menu, array($cd_menu_pai)); 
            echo form_end_box_filter();
        echo form_command_bar_detail_end();
        echo form_open('dev/showmenu/salvar', 'name="filter_bar_form"');
            echo form_start_box( "default_box", "Cadastro" );
                echo form_default_hidden('save', '', 0);
				echo form_default_hidden('cd_padrao', '', '');
                echo form_default_integer('cd_menu_pai', 'Pai: *');
                echo form_default_integer('cd_menu', 'Código:');
                echo form_default_text('ds_menu', 'Menu: *', '', 'style="width:300px"');
                echo form_default_text('ds_href', 'Link: *', '', 'style="width:300px"');
                echo form_default_hidden('nr_ordem', 'Ordem:');
                echo form_default_textarea('ds_resumo', 'Resumo:', '', 'style="width:300px;height:120px;"');
            echo form_end_box("default_box");
            echo form_command_bar_detail_start();     
                echo button_save("Salvar");
                echo button_save("Cancelar", 'cancelar();', 'botao_disabled', 'style="display:none" id="btn_cancelar"');
                echo button_save("Desativar", 'desativar();', 'botao_vermelho', 'style="display:none" id="btn_status"');
            echo form_command_bar_detail_end();
        echo form_close();
        echo form_start_box( "default_box", "Menu");
        echo  '<label style="color:blue">Azul - Menu</label> |
            <label>Preto - Ítem</label> |
            <label style="color:red">Vermelho - Desativado</label>'.br(2);
            echo '<div id="result_div"></div>';
        echo form_end_box("default_box");
        
        echo form_start_box( "default_box_ordenacao", "Ordenação", true, false, 'style="display:none;"');

        echo form_end_box("default_box");
        echo form_command_bar_detail_start();     
                echo button_save("Ordenar", 'ordenar();', 'botao', 'style="display:none" id="btn_ordenar"');
            echo form_command_bar_detail_end();
        echo br(3);
    echo aba_end();

$this->load->view('footer'); 
?>