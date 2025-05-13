<?php
set_title('Relatório Auditoria');
$this->load->view('header');
?>
<script>
    <?php
		echo form_default_js_submit(Array('relato', 'cd_processo', 'tipo'));
    ?>
    function ir_lista()
    {
        location.href='<?php echo site_url("gestao/relatorio_auditoria"); ?>';
    }
    
    function ir_cadastro(cd_relatorio_auditoria)
    {
        location.href='<?php echo site_url("gestao/relatorio_auditoria/cadastro"); ?>/'+cd_relatorio_auditoria;
    }
    
    function ir_equipe(cd_relatorio_auditoria)
    {
        location.href='<?php echo site_url("gestao/relatorio_auditoria/equipe"); ?>/'+cd_relatorio_auditoria;
    }   
	
	function ir_acompanhamento(cd_relatorio_auditoria)
    {
        location.href='<?php echo site_url("gestao/relatorio_auditoria/acompanhamento"); ?>/'+cd_relatorio_auditoria;
    }
	
	function ir_anexo(cd_relatorio_auditoria)
    {
        location.href='<?php echo site_url("gestao/relatorio_auditoria/anexo"); ?>/'+cd_relatorio_auditoria;
    }
    
    function excluir(cd_relatorio_auditoria_constatacao, cd_relatorio_auditoria )
    {
        if(confirm("ATENÇÃO\n\nDeseja excluir?\n\n"))
        {
            location.href='<?php echo site_url("gestao/relatorio_auditoria/excluir_constatacao/"); ?>' + "/" + cd_relatorio_auditoria_constatacao+ "/"+cd_relatorio_auditoria
        }
    }

    function concluir_constatacao()
    {
        if(confirm("ATENÇÃO\n\nDeseja concluir?\n\n"))
        {
            location.href = "<?= site_url('gestao/relatorio_auditoria/concluir_constatacao/'.$relatorio['cd_relatorio_auditoria']) ?>";
        }
    }
    
    function alterar(cd_relatorio_auditoria_constatacao)
    {
        $.post( '<?php echo base_url() . index_page(); ?>/gestao/relatorio_auditoria/carrega_constatacao',
        {
            cd_relatorio_auditoria_constatacao: cd_relatorio_auditoria_constatacao
        },
        function(data)
        {
            if(data)
            {
                $('#cd_relatorio_auditoria_constatacao').val(cd_relatorio_auditoria_constatacao);
                $('#relato').val(data.relato);
                $("#cd_processo option[value='"+data.cd_processo+"']").attr('selected', 'selected');
                $('#evidencias').val(data.evidencias);
                $("#tipo option[value='"+data.tipo+"']").attr('selected', 'selected');
                
               // impacto();
                
               // $("#fl_impacto option[value='"+data.fl_impacto+"']").attr('selected', 'selected');

                if(data.tipo == "N")
                {
                    $("#nr_ano_nc_nr_nc_row").show();
                    $('#nr_ano_nc').val(data.nr_ano_nc);
                    $('#nr_nc').val(data.nr_nc);
                }
                else
                {
                    $("#nr_ano_nc_nr_nc_row").hide();
                    $('#nr_ano_nc').val('');
                    $('#nr_nc').val('');
                }
                
            }
        },'json');
    }
    
    function impacto()
    {
        if($('#tipo').val() == 'O')
        {
            $('#fl_impacto_row').show();
        }
        else
        {
            $('#fl_impacto_row').hide();
        }
    }
	
    function melhoria()
    {
        if($('#tipo').val() == 'M')
        {
            $('#evidencias_row').hide();
        }
        else
        {
            $('#evidencias_row').show();
        }
    }	
   
    function gera_pdf(cd_relatorio_auditoria)
	{
        location.href='<?php echo base_url() . index_page(); ?>/gestao/relatorio_auditoria/gera_pdf/'+cd_relatorio_auditoria;
	}
	
	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),
		[
			'CaseInsensitiveString',
			'CaseInsensitiveString',
			'CaseInsensitiveString',
			'CaseInsensitiveString',
            'CaseInsensitiveString',
			'DateBR',
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
		ob_resul.sort(1, true);
	}
    
    $(document).ready(function(){
        impacto();
		melhoria();
		
		configure_result_table();
        
        $('#tipo').change(function(){
            impacto();
			melhoria();
        });

        $("#nr_ano_nc_nr_nc_row").hide();
    });
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_lista', 'Cadastro', FALSE, 'ir_cadastro('.$cd_relatorio_auditoria.');');
$abas[] = array('aba_lista', 'Equipe', FALSE, 'ir_equipe('.$cd_relatorio_auditoria.');');
$abas[] = array('aba_nc', 'Constatação', TRUE, 'location.reload();');
$abas[] = array('aba_lista', 'Registros Gerais', FALSE, 'ir_acompanhamento('.$cd_relatorio_auditoria.');');
$abas[] = array('aba_lista', 'Anexo', FALSE, 'ir_anexo('.$cd_relatorio_auditoria.');');

$ar_tipo[] = array('value' => 'N', 'text' => 'Não Conformidade');
$ar_tipo[] = array('value' => 'O', 'text' => 'Observação');
$ar_tipo[] = array('value' => 'M', 'text' => 'Oportunidade de Melhoria');

$ar_impacto[] = array('value' => 'S', 'text' => 'Sim');
$ar_impacto[] = array('value' => 'N', 'text' => 'Não');

$body=array();
$head = array(
    'Relato'
    , 'Processo'
    , 'Evidências'
    , 'Tipo'

    , 'NC'
    , 'Dt Alteração'
    , 'Usuário'
    , ''
);

foreach( $collection as $item )
{
    $body[] = array(
        array($item["relato"],'style="text-align:justify;"'),
        array($item["procedimento"],'style="text-align:left;"'),
        array($item["evidencias"],'style="text-align:justify;"'),
        '<span class="label '.$item["tipo_label"].'">'.$item["tipo"].'</span>',
        $item["ds_nao_conformidade"],
        $item["dt_alteracao"],
        $item["usuario_alteracao"],
        ($fl_permissao ? '<a href="javascript:void(0);" onclick="alterar('.$item["cd_relatorio_auditoria_constatacao"].')">[Editar]</a>  <a href="javascript:void(0);" onclick="excluir('.$item["cd_relatorio_auditoria_constatacao"].','.$cd_relatorio_auditoria.')">[Excluir]</a>' : '')
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;

echo aba_start( $abas );
    echo form_open('gestao/relatorio_auditoria/salvar_constatacao', 'name="filter_bar_form"');
        echo form_start_box( "default_box", "Constatação" );
            echo form_default_hidden('cd_relatorio_auditoria', "Código:", $cd_relatorio_auditoria, "style='width:100%;border: 0px;' readonly" );
            echo form_default_hidden('cd_relatorio_auditoria_constatacao', "Código:", '0', "style='width:100%;border: 0px;' readonly" );

			echo form_default_dropdown('tipo', 'Tipo:*', $ar_tipo);			
            echo form_default_textarea('relato', "Relato:*", '', "style='width:500px;'");
			echo form_default_processo('cd_processo', 'Processo:*');
            echo form_default_textarea('evidencias', "Evidencias (risco):*", '', "style='width:500px;'");
           // echo form_default_dropdown('fl_impacto', 'Impacto significativo:', $ar_impacto); 

            echo form_default_integer_ano('nr_ano_nc', 'nr_nc', 'Não Conformidade (Ano/Número):');

            if(trim($relatorio['dt_constatacao']) != '')
            {
                echo form_default_row('', 'Dt. Concluido Constatação:', $relatorio['dt_constatacao']);     
                echo form_default_row('', 'Usuário:', $relatorio['ds_usuario_constatacao']);     
            }

        echo form_end_box("default_box");
        echo form_command_bar_detail_start(); 
            if($fl_permissao)
            {
                echo button_save("Salvar");
            }

            echo button_save("Imprimir PDF", "gera_pdf(".$cd_relatorio_auditoria.")", "botao_disabled");

            if(($fl_permissao) AND (trim($relatorio['dt_constatacao']) == ''))
            {
                echo button_save('Concluir Constatação', 'concluir_constatacao();', 'botao_vermelho');
            }
            
        echo form_command_bar_detail_end();
        
    echo form_close();

    echo $grid->render();

	echo "<BR><BR><BR>";
echo aba_end();

$this->load->view('footer_interna');
?>