<?php
set_title('USERLOCK - F�rias - Bloqueio');
$this->load->view('header');
?>

<script>
    $(function(){
        //filtrar();
    });
    
    function acesso()
    {
        location.href='<?php echo site_url("servico/userlock/acesso/"); ?>';
    }
    
    function filtrar()
    {
        if(($("#validar_login_usuario").val() != "") && ($("#validar_login_senha").val() != ""))
		{
			$('#result_div').html("<?php echo loader_html(); ?>");

			$.post('<?php echo site_url("servico/userlock/ferias_listar"); ?>',
			{
				validar_login_usuario : $('#validar_login_usuario').val(),
				validar_login_senha : $('#validar_login_senha').val()
			},
			function(data)
			{
				$('#result_div').html(data);
				configure_result_table();
			});
		}
		else
		{
			alert("Informe o usu�rio e senha");
		}
    }
    
    function configure_result_table()
    {
        var ob_resul = new SortableTable(document.getElementById("table-1"),
        [
            'CaseInsensitiveString',
            'CaseInsensitiveString',
            'CaseInsensitiveString',
            'DateTimeBR',
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
        ob_resul.sort(3, false);
    }
</script>

<?php
$abas[] = array('aba_lista', 'F�rias - Bloqueio', TRUE, 'location.reload();');
$abas[] = array('aba_lista', 'Acesso - Grupo', FALSE, 'acesso();');

echo aba_start( $abas );
    echo form_list_command_bar();
    echo form_start_box_filter();
		echo form_default_text('validar_login_usuario', "Usu�rio:(*)", $this->session->userdata('usuario'), 'readonly');
		echo form_default_password('validar_login_senha', "Senha:(*)","");		
    echo form_end_box_filter();
    echo '<div id="result_div"></div>'.br();
echo aba_end();
$this->load->view('footer'); 
?>