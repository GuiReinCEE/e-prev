<?php
set_title('Pré-venda - Agenda');
$this->load->view('header');
?>
<script>
<?php
echo form_default_js_submit(
	array(
		'cd_pre_venda_agenda_tipo',
		'dt_pre_venda_agenda_data',
		array('dt_pre_venda_agenda_hora', 'hora'),
		'observacao'
	)
);
?>
</script>
<?
echo form_open('ecrm/prevenda/agenda_salvar');
echo form_hidden('cd_pre_venda_agenda', intval($record['cd_pre_venda_agenda']));
echo form_hidden('cd_pre_venda', intval($record['cd_pre_venda']));
	
	$link_lista = site_url( 'ecrm/prevenda' );	
	$link_cadastro = site_url("ecrm/prevenda/abrir/") . '/' . $record["cd_pre_venda"];
	$link_contato = site_url("ecrm/prevenda/contato/") . '/' . $record["cd_pre_venda"];
	$link_agenda = site_url("ecrm/prevenda/agenda/") . '/' . $record["cd_pre_venda"];

	$abas[0] = array('aba_lista', 'Lista', false, "redir('', '$link_lista')");
	$abas[1] = array('aba_cadastro', 'Cadastro', false, "redir('', '$link_cadastro')");
	$abas[2] = array('aba_contato', 'contato', false, "redir('', '$link_contato');");
	$abas[3] = array('aba_agenda', 'agenda', true, "redir('', '$link_agenda')");
	
	$link_relatorio = site_url( 'ecrm/prevenda/relatorio' );
	$abas[4] = array('aba_relatorio', 'Relatório', false, "redir('', '$link_relatorio')");

    echo aba_start( $abas );

    echo form_start_box("cadastro", "Cadastro");
    if($ar_participante['seq_dependencia']=='') $ar_participante['seq_dependencia'] = 0;
    echo form_default_participante(array('cd_empresa','cd_registro_empregado','seq_dependencia', 'nome'), "Participante:", $ar_participante, TRUE, FALSE);
    echo form_default_text("nome", "Nome:", $ar_participante, "style='border: 0px; width:300px' readonly");		
	
	echo form_default_dropdown('cd_pre_venda_agenda_tipo', 'Tipo:(*)', $ar_agenda_tipo, Array($record['cd_pre_venda_agenda_tipo']));
    echo form_default_date("dt_pre_venda_agenda_data", "Data:(*)", $record);
    echo form_default_time("dt_pre_venda_agenda_hora", "Hora:(*)", $record);
    echo form_default_textarea("observacao", "Observação:(*)", $record,'style="height: 80px;"');
    echo form_end_box("cadastro");
    
    echo form_command_bar_detail_start();
		echo button_save("Salvar");
    echo form_command_bar_detail_end();
	
	echo form_start_box("lista", "Agendamentos");
?>
<table width="100%" class="sort-table" id="table-1" align="center" cellspacing="2" cellpadding="2">
    <thead>
	<tr>
		<td><b>Data</b></td>
		<td><b>Tipo</b></td>
		<td><b>Observação</b></td>
		<td></td>
		<td></td>
	</tr>
    </thead>
	<tbody>
		<? foreach($collection as $item) : ?>
		<tr onmouseover="sortSetClassOver(this);" onmouseout="sortSetClassOut(this);">
			<td align="center"><?php echo $item['dt_pre_venda_agenda']; ?></td>
			<td align="center"><?php echo $item['ds_pre_venda_agenda_tipo']; ?></td>
			<td align="left"><?php echo nl2br($item['observacao']); ?></td>
			<td align="center">
				<?php
				if( $item['dt_pre_venda_agenda_enviado']=="" )
				{
					echo form_list_button_edit( "ecrm/prevenda/agenda", $record["cd_pre_venda"].'/'.$item['cd_pre_venda_agenda'] );
				}
				?>
			</td>
			<td align="center">
				<?php
				if( $item['dt_pre_venda_agenda_enviado']=="" )
				{
					echo button_delete("ecrm/prevenda/agenda_excluir", $item['cd_pre_venda_agenda']); 
				}
				?>
			</td>
		</tr>
		<? endforeach; ?>
	</tbody>
</table>
<script>
	var ob_resul = new SortableTable(document.getElementById("table-1"),["DateTimeBR", "CaseInsensitiveString","CaseInsensitiveString", null]);
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
</script>
<?
	echo form_end_box("lista");
	echo form_close();
	
	echo br(5);
echo aba_end( 'cadastro');
$this->load->view('footer');
?>