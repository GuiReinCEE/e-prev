<?php
set_title('Plano Fiscal - Parecer - Diretoria');
$this->load->view('header');
?>
<script>
<?php
    echo form_default_js_submit(Array('dt_limite_diretoria'));
?>
    function ir_lista()
    {
        location.href='<?php echo site_url("gestao/plano_fiscal_parecer"); ?>';
    }
    
    function ir_cadastro()
    {
        location.href='<?php echo site_url("gestao/plano_fiscal_parecer/cadastro/".$row['cd_plano_fiscal_parecer']); ?>';
    }
    
    function configure_result_table()
    {
        var ob_resul = new SortableTable(document.getElementById("table-1"),
        [
			'CaseInsensitiveString',
            'DateTimeBR',
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
        ob_resul.sort(1, true);
    }
    
    $(function(){
		configure_result_table();
    });
</script>

<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_nc', 'Cadastro', FALSE, 'ir_cadastro();');
$abas[] = array('aba_nc', 'Diretoria', TRUE, 'location.reload();');

$body = array();
$head = array(
    'Diretoria',
	'Dt Assinado',
	'Usuário'
);

foreach ($collection as $item)
{
    $body[] = array(
	    $item["cd_diretoria"],
        $item["dt_inclusao"],
        array($item["nome"],"text-align:left;")
    );
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;

echo aba_start( $abas );
    echo form_open('gestao/plano_fiscal_parecer/salvar_limite_diretoria', 'name="filter_bar_form_cadastro"');
        echo form_start_box( "default_box", "Cadastro" );
            echo form_default_hidden('cd_plano_fiscal_parecer', '', $row['cd_plano_fiscal_parecer']);
			echo form_default_date('dt_limite_diretoria', 'Dt. Limite Diretoria :*', $row['dt_limite_diretoria']);

            if(trim($row['dt_envio_diretoria']) != '')
            {
                echo form_default_row('dt_envio_diretoria', 'Dt. Envio Diretoria :', $row['dt_envio_diretoria']);
				echo form_default_row('usuario_envio_diretoria', 'Usuário Envio Diretoria :', $row['usuario_envio_diretoria']);
            }
        echo form_end_box("default_box");
        
            echo form_command_bar_detail_start(); 
				echo button_save("Encaminhar para Diretoria");
            echo form_command_bar_detail_end();
        
    echo form_close();
    
    echo $grid->render();
    echo br(3);	
	
echo aba_end();

$this->load->view('footer_interna');
?>