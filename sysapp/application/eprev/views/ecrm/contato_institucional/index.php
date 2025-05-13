<?php
set_title('Contato Institucional');
$this->load->view('header');
?>
<script>
    $(function(){
		filtrar();
		
		$('#nr_etiqueta_ini_row').hide();
		
		$('#fl_gerar').change(function(){
			if($(this).val() == 'E')
			{
				$('#nr_etiqueta_ini_row').show();
			}
			else
			{
				$('#nr_etiqueta_ini_row').hide();
			}
		});
    });
    
    function novo()
    {
        location.href='<?php echo site_url("ecrm/contato_institucional/cadastro/"); ?>';
    }

    
    function filtrar()
    {
		if($('#fl_gerar').val() == '')
		{
			$('#fl_gerar').val('T');
		}
		
		if($('#fl_gerar').val() == 'T')
		{
			$('#result_div').html("<?php echo loader_html(); ?>");

			$.post('<?php echo site_url('ecrm/contato_institucional/listar'); ?>',
			{
					cd_contato_institucional_tipo : $('#cd_contato_institucional_tipo').val(),
					cd_contato_institucional_empresa  : $('#cd_contato_institucional_empresa').val(),
					cd_contato_institucional_cargo  : $('#cd_contato_institucional_cargo').val(),
					nome : $('#nome').val(),
					sec_nome : $('#sec_nome').val()
			},
			function(data)
			{
				$('#result_div').html(data);
				configure_result_table();
			});
		}
		else
		{	
			var msg = 'Defina o tamanha do papel como CARTA.\n\n'+
                'No Dimensionamento de Páginas informe NENHUM.\n\n';
				
			alert(msg);		
			etiquetas();
		}
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
	
	function etiquetas()
	{
		filter_bar_form.method = "post";
		filter_bar_form.action = '<?php echo base_url() . index_page(); ?>/ecrm/contato_institucional/etiquetas';
		filter_bar_form.target = "_blank";
		filter_bar_form.submit();
	}
</script>

<?php
$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

$config['button'][]=array('Novo Contato', 'novo()');

$arr_gera[] = array('text' => 'Tela', 'value' => 'T');
$arr_gera[] = array('text' => 'Etiqueta', 'value' => 'E');

echo aba_start( $abas );
    echo form_list_command_bar($config);
    echo form_start_box_filter();
        echo filter_dropdown('cd_contato_institucional_tipo', 'Tipo:', $arr_tipo);
        echo filter_dropdown('cd_contato_institucional_empresa', 'Empresa:', $arr_empresa);
        echo filter_dropdown('cd_contato_institucional_cargo', 'Cargo:', $arr_cargo);
        echo filter_text('nome', 'Nome:', '', 'style="width:300px"');
        echo filter_text('sec_nome', 'Secretária:', '', 'style="width:300px"');    
		echo filter_dropdown('fl_gerar', 'Gerar:', $arr_gera);		
		echo filter_integer('nr_etiqueta_ini', 'Iniciar a partir da etiqueta:');
    echo form_end_box_filter();
echo aba_end();
echo '<div id="result_div"></div>'.br();
$this->load->view('footer'); 
?>