<?php
set_title('Plano Fiscal - Parecer - Cadastro');
$this->load->view('header');
?>
<script>
<?php
    echo form_default_js_submit(Array('nr_ano', 'nr_mes', 'cd_presidente'));
?>
    function ir_lista()
    {
        location.href='<?php echo site_url("gestao/plano_fiscal_parecer"); ?>';
    }
    
    function ir_responsabilidade()
    {
        location.href='<?php echo site_url("gestao/plano_fiscal_parecer/responsabilidade/".$row['cd_plano_fiscal_parecer']); ?>';
    }
	
	function ir_diretoria()
    {
        location.href='<?php echo site_url("gestao/plano_fiscal_parecer/diretoria/".$row['cd_plano_fiscal_parecer']); ?>';
    }
    
    function salvar_item()
    {
        var retorno = true;
        var focus;
        
        if($('#cd_plano_fiscal_parecer_area').val() == '')
        {
            retorno = false;        
            focus = 'cd_plano_fiscal_parecer_area';
        }
		
		if($('#usuario').val() == '')
        {
            retorno = false;        
            focus = 'usuario';
        }
        
        if($('#usuario_gerencia').val() == '')
        {
            retorno = false;
            focus = 'usuario_gerencia';
        }
        
        if($('#responsavel').val() == '')
        {
            retorno = false;        
            focus = 'responsavel';
        }
        
        if($('#responsavel_gerencia').val() == '')
        {
            retorno = false;
            focus = 'responsavel_gerencia';
        }
        
        if($('#descricao').val() == '')
        {
            retorno = false;
            focus = 'descricao';
        }
        
        if($('#nr_item').val() == '')
        {
            retorno = false;
            focus = 'nr_item';
        }
        
        if(retorno)
        {
            $('#result_div').html("<?php echo loader_html(); ?>");
			
			$.post('<?php echo site_url('/gestao/plano_fiscal_parecer/salvar_item');?>',
            {
                cd_plano_fiscal_parecer      : $('#cd_plano_fiscal_parecer').val(),
                cd_plano_fiscal_parecer_item : $('#cd_plano_fiscal_parecer_item').val(),
                usuario                      : $('#usuario').val(),
                usuario_gerencia             : $('#usuario_gerencia').val(),
                descricao                    : $('#descricao').val(),
                nr_item                      : $('#nr_item').val(),
                responsavel                  : $('#responsavel').val(),
                responsavel_gerencia         : $('#responsavel_gerencia').val(),
                parecer                      : $('#parecer').val(),
				retorno                      : $('#retorno').val(),
				dt_limite                    : $('#dt_limite').val(),
				fl_status                    : $('#fl_status').val(),
				fl_copiar_resultado          : $('#fl_copiar_resultado').val(),
				cd_plano_fiscal_parecer_area : $('#cd_plano_fiscal_parecer_area').val()
                
            }, 
			function(data)
			{
                location.reload();
            });
        }
        else
        {
            alert( "Informe os campos obrigatórios! \n\n(os campos obrigatórios tem um * logo após a identificação.)" );
            $("#"+focus).focus();
        }
    }
    
    function configure_result_table()
    {
        var ob_resul = new SortableTable(document.getElementById("table-1"),
        [
            'Number',
            'CaseInsensitiveString',
            'CaseInsensitiveString',
            'CaseInsensitiveString',
            'CaseInsensitiveString',
            'CaseInsensitiveString',
            'DateTimeBR',
            'DateBR',
            'DateTimeBR',
            'CaseInsensitiveString',
            'CaseInsensitiveString',
            'CaseInsensitiveString',
            'CaseInsensitiveString'
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
    
    function editar(cd_plano_fiscal_parecer_item)
    {
        $.post( '<?php echo site_url('/gestao/plano_fiscal_parecer/carrega_item'); ?>',
        {
            cd_plano_fiscal_parecer_item : cd_plano_fiscal_parecer_item
        },
        function(data)
        {
            if(data)
            {
                $('html,body').animate({scrollTop: $("#default_item_box").offset().top},'slow'); 
				
				$('#cd_plano_fiscal_parecer_item').val(data.cd_plano_fiscal_parecer_item);
                $('#nr_item').val(data.nr_item);
                $('#cd_plano_fiscal_parecer_area').val(data.cd_plano_fiscal_parecer_area);
                $('#descricao').val(data.descricao);
                $('#usuario_gerencia').val(data.cd_gerencia);

                $('#usuario_div').empty();
                $('#usuario_default_init').val(data.cd_responsavel);
                load_users___usuario();                

                $('#responsavel_gerencia').val(data.cd_gerencia_gerente);
                
                $('#responsavel_div').empty();
                $('#responsavel_default_init').val(data.cd_gerente);      
                load_users___responsavel();  
                
				$('#parecer_row').show();
				$('#parecer').val(data.parecer);
				$('#retorno').val(data.retorno);
				
				$('#fl_status_row').show();
				
				$('#fl_status').val(data.fl_status);
				$('#fl_copiar_resultado').val(data.fl_copiar_resultado);
				
				$('#dt_limite').val(data.dt_limite);
				if(data.dt_limite != "")
				{
					$('#dt_limite_row').show();
				}
				else
				{
					$('#dt_limite_row').hide();
				}				
      
                $('#add_item').val("Salvar Item");
            }
        }, 'json');
    }
    
    function listar()
    {
        $('#result_div').html("<?php echo loader_html(); ?>");

        $.post( '<?php echo site_url('/gestao/plano_fiscal_parecer/listar_itens');?>',
        {
            cd_plano_fiscal_parecer      : $('#cd_plano_fiscal_parecer').val(),
			fl_respondido                : $('#fl_respondido').val(),
			fl_assinado                  : $('#fl_assinado').val(),
			fl_status_filtro             : $('#fl_status_filtro').val(),
			cd_plano_fiscal_parecer_area : $('#cd_area').val()
        },
        function(data)
        {
            $('#result_div').html(data);
            configure_result_table();
        });
    }
    
    function enviar(cd_plano_fiscal_parecer_item)
    {
        var confirmacao = 'Deseja enviar o item?\n\n'+
                'Clique [Ok] para Sim\n\n'+
                'Clique [Cancelar] para Não\n\n';
        
        if(confirm(confirmacao))
        {
           location.href='<?php echo site_url("gestao/plano_fiscal_parecer/enviar/".$row['cd_plano_fiscal_parecer']); ?>/'+cd_plano_fiscal_parecer_item;
        }
    }
    
    function excluir(cd_plano_fiscal_parecer_item)
    {
        var confirmacao = 'Deseja excluir o item?\n\n'+
                'Clique [Ok] para Sim\n\n'+
                'Clique [Cancelar] para Não\n\n';
        
        if(confirm(confirmacao))
        {
            location.href='<?php echo site_url("gestao/plano_fiscal_parecer/excluir_plano_item/".$row['cd_plano_fiscal_parecer']); ?>/'+cd_plano_fiscal_parecer_item;
        }
    }
    
    function excluir_plano()
    {
        var confirmacao = 'Deseja excluir o plano?\n\n'+
                'Clique [Ok] para Sim\n\n'+
                'Clique [Cancelar] para Não\n\n';
        
        if(confirm(confirmacao))
        {
            location.href='<?php echo site_url("gestao/plano_fiscal_parecer/excluir_plano/".$row['cd_plano_fiscal_parecer']); ?>';
        }
    }
    
    function encerrar()
    {
        var confirmacao = 'Deseja encerrar o plano?\n\n'+
                'Clique [Ok] para Sim\n\n'+
                'Clique [Cancelar] para Não\n\n';
        
        if(confirm(confirmacao))
        {
            location.href='<?php echo site_url("gestao/plano_fiscal_parecer/encerrar/".$row['cd_plano_fiscal_parecer']); ?>';
        }
    }
    
    function enviar_todos()
    {
        var confirmacao = 'Deseja enviar todos os itens?\n\n'+
                'Clique [Ok] para Sim\n\n'+
                'Clique [Cancelar] para Não\n\n';
        
        if(confirm(confirmacao))
        {
            location.href='<?php echo site_url("gestao/plano_fiscal_parecer/enviar/".$row['cd_plano_fiscal_parecer']); ?>';
        }
    }
    
    function imprimir()
    {
        filter_bar_form_cadastro.method = "post";
        filter_bar_form_cadastro.action = '<?php echo site_url('/gestao/plano_fiscal_parecer/imprimir')?>';
        filter_bar_form_cadastro.target = "_self";
        filter_bar_form_cadastro.submit();
    }
    
    function imprimirPDF()
    {
        filter_bar_form_cadastro.method = "post";
        filter_bar_form_cadastro.action = '<?php echo site_url('/gestao/plano_fiscal_parecer/imprimirPDF')?>';
        filter_bar_form_cadastro.target = "_self";
        filter_bar_form_cadastro.submit();
    }
	
	function reabrir(cd_plano_fiscal_parecer_item)
    {
        var confirmacao = 'Deseja reabrir o item?\n\n'+
                'Clique [Ok] para Sim\n\n'+
                'Clique [Cancelar] para Não\n\n';
        
        if(confirm(confirmacao))
        {
            location.href='<?php echo site_url("gestao/plano_fiscal_parecer/reabrir/".$row['cd_plano_fiscal_parecer']); ?>/'+cd_plano_fiscal_parecer_item;
        }
    }

    function prorrogacao()
    {
        var confirmacao = 'Deseja prorrogar os itens sem assinatura?\n\n'+
                'Clique [Ok] para Sim\n\n'+
                'Clique [Cancelar] para Não\n\n';

        if(confirm(confirmacao))
        {
            $.post('<?=site_url('gestao/plano_fiscal_parecer/prorrogacao')?>',
            {
                cd_plano_fiscal_parecer : $('#cd_plano_fiscal_parecer').val(),
                dt_prorrogaca           : $('#dt_prorrogaca').val()
            },
            function(data)
            {
                listar();
                $('#dt_prorrogaca').val("")
            });
        }
    }
    
    $(function(){
		$('#dt_limite_row').hide();
        $('#parecer_row').hide();
        $('#display_none').hide();
		$('#fl_status_row').hide();

        <?php
            if(intval($row['cd_plano_fiscal_parecer']) > 0)
            {
                ?>
                listar();
                <?php
            }
        ?>
    });
</script>

<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_nc', 'Cadastro', TRUE, 'location.reload();');

if((intval($row['cd_plano_fiscal_parecer']) > 0) AND ($total_enviados['tl'] == 0))
{
	$abas[] = array('aba_nc', 'Diretoria', FALSE, 'ir_diretoria();');
}

$ar_copiar_resultado[] = Array('value' => 'S', 'text' => 'Sim');
$ar_copiar_resultado[] = Array('value' => 'N', 'text' => 'Não');

$ar_opt[] = Array('value' => '', 'text' => 'Todos');
$ar_opt[] = Array('value' => 'S', 'text' => 'Sim');
$ar_opt[] = Array('value' => 'N', 'text' => 'Não');		

echo aba_start( $abas );
    echo form_open('gestao/plano_fiscal_parecer/salvar', 'name="filter_bar_form_cadastro"');
        echo form_start_box( "default_box", "Cadastro" );
            echo form_default_hidden('cd_plano_fiscal_parecer', '', $row['cd_plano_fiscal_parecer']);
            echo form_default_mes_ano('nr_mes', 'nr_ano', 'Ano/Mês : *', '01/'.$row['nr_mes'].'/'.$row['nr_ano'] );
            echo form_default_dropdown('cd_presidente', 'Diretor-Presidente :*', $arr_diretoria, array($row['cd_presidente']));
            echo form_default_dropdown('cd_dir_financeiro', 'Diretor Financeiro :', $arr_diretoria, array($row['cd_dir_financeiro']));
            //echo form_default_dropdown('cd_dir_administrativo', 'Diretor Administrativo :', $arr_diretoria, array($row['cd_dir_administrativo']));
            echo form_default_dropdown('cd_dir_seguridade', 'Diretor de Previdência :', $arr_diretoria, array($row['cd_dir_seguridade']));
            
			echo form_default_dropdown('cd_presidente_sub', 'Sub. Diretor-Presidente :', $arr_diretoria, array($row['cd_presidente_sub']));
			echo form_default_dropdown('cd_dir_financeiro_sub', 'Sub. Diretor Financeiro :', $arr_diretoria, array($row['cd_dir_financeiro_sub']));
            //echo form_default_dropdown('cd_dir_administrativo_sub', 'Sub. Diretor Administrativo :', $arr_diretoria, array($row['cd_dir_administrativo_sub']));
            echo form_default_dropdown('cd_dir_seguridade_sub', 'Sub. Diretor de Previdência :', $arr_diretoria, array($row['cd_dir_seguridade_sub']));
            
            if(trim($row['dt_encerra']) != '')
            {
                echo form_default_row('dt_encerra', 'Dt. Encerramento :', $row['dt_encerra']);
                echo form_default_row('usuario_encerrado', 'Usuário Encerramento :', $row['usuario_encerrado']);
            }
        echo form_end_box("default_box");
        if(trim($row['dt_encerra']) == '')
        {
            echo form_command_bar_detail_start();   
				echo button_save("Salvar");
			
                if(intval($row['cd_plano_fiscal_parecer']) > 0)
                {
                    echo button_save("Excluir", "excluir_plano()", "botao_vermelho");
                    echo button_save("Encerrar", "encerrar();", "botao_vermelho");
                }
            echo form_command_bar_detail_end();
        }
    echo form_close();
    if(trim($row['dt_encerra']) == '')
    {
        echo form_start_box( "default_item_box", "Item", true, false, (intval($row['cd_plano_fiscal_parecer']) > 0 ? '' : 'style="display:none"') );
            echo form_default_hidden('cd_plano_fiscal_parecer_item', '', '0');
            echo form_default_integer('nr_item', 'Número :*');
            echo form_default_textarea('descricao', 'Descrição :*', '', "style='height:100px;'");
			echo form_default_dropdown_db('cd_plano_fiscal_parecer_area', 'Gerência: *', array('gestao.plano_fiscal_parecer_area', 'cd_plano_fiscal_parecer_area', 'ds_plano_fiscal_parecer_area'), array(), '', '', TRUE);			
            echo form_default_usuario_ajax('usuario', '', '', "Respondente :* ", "Gerência Respondente:* ");
            echo form_default_usuario_ajax('responsavel', '', '', "Responsavél :* ", "Gerência Responsavél:* ");
            echo form_default_date("dt_limite","Dt Limite:");
            echo form_default_dropdown('fl_status', 'Status:', $arr_status);
            echo form_default_textarea('parecer', 'Parecer :', '', "style='height:100px;'  ");
            echo form_default_textarea('retorno', 'Retorno :', '', 'style="height:100px;"');
            echo form_default_dropdown('fl_copiar_resultado', 'Copiar mês anterior: *', $ar_copiar_resultado, array('fl_copiar_resultado' => 'N'));
        echo form_end_box("default_box");
    }
    echo form_command_bar_detail_start((intval($row['cd_plano_fiscal_parecer']) > 0 ? '' : 'display_none'));     
        if(trim($row['dt_encerra']) == '')
        {
            echo button_save("Adicionar Item", 'salvar_item();', 'botao', "id='add_item'");
            if($total_enviados['tl'] > 0 OR intval($row['cd_plano_fiscal_parecer']) == 0)
            {
                echo button_save("Enviar Emails", "enviar_todos()", "botao_vermelho");
            } 
        }
        echo button_save("Excel", 'imprimir();', 'botao_disabled');
        echo button_save("PDF", 'imprimirPDF();', 'botao_disabled');
    echo form_command_bar_detail_end();

    if(intval($row['cd_plano_fiscal_parecer']) > 0)
    {
        echo form_start_box("default_prorrogacao_box", "Prorrogação de Data Limite", true, false);
            echo form_default_date("dt_prorrogaca","Dt. Prorrogação:");
        echo form_end_box("default_prorrogacao_box");
        echo form_command_bar_detail_start();     
            echo button_save("Prorrogar", "prorrogacao()");
        echo form_command_bar_detail_end();
    }

	echo br(2);	
	echo form_start_box( "default_filtros_box", "Filtros", true, false, (intval($row['cd_plano_fiscal_parecer']) > 0 ? '' : 'style="display:none"') );
		echo form_default_dropdown('fl_respondido', 'Respondido: ', $ar_opt, array('fl_respondido' => ''),'onchange="listar();"');		
		echo form_default_dropdown('fl_assinado', 'Assinado: ', $ar_opt, array('fl_assinado' => ''),'onchange="listar();"');		
		echo form_default_dropdown('cd_area', 'Gerência: ', $arr_area, array('cd_plano_fiscal_parecer_area' => ''),'onchange="listar();"');		
		echo form_default_dropdown('fl_status_filtro', 'Status: ', $arr_status, array('fl_status_filtro' => ''),'onchange="listar();"');		
	echo form_end_box("default_filtros_box");
	echo br();	
    echo '<div id="result_div"></div>';
    echo br(3);	
echo aba_end();

$this->load->view('footer_interna');
?>