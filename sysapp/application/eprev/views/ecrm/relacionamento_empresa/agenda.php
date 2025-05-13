<?php
set_title('Relacionamento - Anexos');
$this->load->view('header');
?>
<script>
<?php
echo form_default_js_submit(array('ds_email_envia', 'dt_inicio', 'hr_inicio', 'dt_final', 'hr_final', 'local', 'ds_empresa_agenda'));
?>

	function ir_lista()
    {
        location.href='<?php echo site_url("ecrm/relacionamento_empresa"); ?>';
    }
	
	function ir_cadastro()
	{
		location.href='<?php echo site_url("ecrm/relacionamento_empresa/cadastro/".$row['cd_empresa']); ?>';
	}
	
	function ir_contato()
	{
		location.href='<?php echo site_url("ecrm/relacionamento_empresa/contato/".intval($row['cd_empresa'])); ?>';
	}
	
	function ir_pessoas()
	{
		location.href='<?php echo site_url(  "ecrm/relacionamento_empresa/pessoas/".intval($row['cd_empresa'])); ?>';
	}
	
	function ir_anexo()
	{
		location.href='<?php echo site_url(  "ecrm/relacionamento_empresa/anexo/".intval($row['cd_empresa'])); ?>';
	}

	function configure_result_table()
    {
        var ob_resul = new SortableTable(document.getElementById("table-1"),
        [
			'DateTimeBR', 
			'DateTimeBR',
			'CaseInsensitiveString', 
			'CaseInsensitiveString', 
			'DateTimeBR',
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
		
	function excluir(cd_empresa_agenda)
	{	
		if(confirm("ATENÇÃO\n\nDeseja excluir a agenda?\n\nClique [Ok] para Sim\nClique [Cancelar] para Não\n\n"))
		{
			location.href='<?php echo site_url("ecrm/relacionamento_empresa/excluir_agenda/".intval($row['cd_empresa']).'/'.intval($row['cd_empresa_agenda'])); ?>';
		}
	}
	
	$(function(){
		configure_result_table();
	});
	
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_empresa', 'Empresa', FALSE, 'ir_cadastro();');
$abas[] = array('aba_contato', 'Contato', FALSE, 'ir_contato();');
$abas[] = array('aba_pessoa', 'Agenda', TRUE, 'location.reload();');
$abas[] = array('aba_pessoa', 'Pessoa', FALSE, 'ir_pessoas();');
$abas[] = array('aba_pessoa', 'Anexo', FALSE, 'ir_anexo();');

$body = array();
$head = array( 
	'Dt Início',
	'Dt Final',
	'Local', 
	'Descrição',
	'Dt Inclusão', 
	'Usuário',
	'Anexos',
	''
);

foreach( $collection as $item )
{
	$body[] = array( 
		anchor("ecrm/relacionamento_empresa/agenda/".$item["cd_empresa"]."/".$item["cd_empresa_agenda"], $item["dt_inicio"]), 
		anchor("ecrm/relacionamento_empresa/agenda/".$item["cd_empresa"]."/".$item["cd_empresa_agenda"], $item["dt_final"]), 
		array($item['local'],"text-align:left;"), 
		array($item['ds_empresa_agenda'],"text-align:left;"), 
		$item['dt_inclusao'],
		array($item['nome'],"text-align:left;"),
		$item['tl_arquivo'],
		(intval($item['cd_usuario_inclusao']) == $this->session->userdata('codigo') ? '<a href="javascript:void(0)" onclick="excluir('.$item["cd_empresa_agenda"].');">[excluir]</a>' : '')
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;

echo aba_start($abas);
	echo form_open('ecrm/relacionamento_empresa/salvar_agenda');
		echo form_start_box("default_box", "Agenda");
			echo form_default_hidden("cd_empresa", "", $row['cd_empresa']);
			echo form_default_hidden("cd_empresa_agenda", "", $row['cd_empresa_agenda']);
			echo form_default_dropdown('ds_email_envia', 'Enviar por:(*)', $email_envia, $row['ds_email_envia']);
			echo form_default_date("dt_inicio", 'Dt Início:(*)', $row);
			echo form_default_time("hr_inicio", 'Hr Início:(*)', $row);
			echo form_default_date("dt_final", 'Dt Final:(*)', $row);
			echo form_default_time("hr_final", 'Hr Final:(*)', $row);
			echo form_default_text('local', 'Local:(*)', $row, 'style="width:300px;"');
			echo form_default_textarea('ds_empresa_agenda', 'Descrição:(*)', $row, 'style="width:500px; height:100px;"');
			echo form_default_text('ds_email_encaminhar', 'E-mail(s):', $row, 'style="width:500px;"');
			echo form_default_row('', '', '<i>E-mails separados por ponto e vírgula (;).</i>');
			echo form_default_upload_multiplo('arquivo_m', 'Anexos:', 'relacionamento_empresa');
			echo form_default_row('', 'Obs.:', '<i>O agendamento será enviado para seu Outlook, alterações no agendamento devem ser realizadas nesta tela.</i>');
		echo form_end_box("default_box");
		echo form_command_bar_detail_start();
			echo button_save();
			if(intval($row['cd_empresa_agenda']) > 0)
			{
				echo button_save('Excluir', 'excluir()', 'botao_vermelho');
			}
		echo form_command_bar_detail_end();
	echo form_close();
	echo $grid->render();
	echo br();
echo aba_end();
$this->load->view('footer_interna');
?>