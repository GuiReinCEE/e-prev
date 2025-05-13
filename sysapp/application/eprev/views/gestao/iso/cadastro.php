<?php
set_title('Pendências das Auditorias ISO');
$this->load->view('header');
?>
<script>
<?php
		echo form_default_js_submit(Array('cd_pendencia_auditoria_iso_tipo', 'nr_contatacao', 'fl_impacto', 'cd_processo', 'cd_responsavel', 'cd_gerencia', 'ds_item'));
?>

function irLista()
{
    location.href='<?php echo site_url("gestao/iso"); ?>';
}

function irAcomp(cd)
{
    location.href='<?php echo site_url("gestao/iso/acompanhamento"); ?>/'+cd;
}

function encerrar(cd_pendencia_auditoria_iso)
{
    if(confirm("ATENÇÃO\n\nDeseja encerrar?\n\n"))
    {
        location.href='<?php echo site_url("gestao/iso/encerrar/"); ?>' + "/" + cd_pendencia_auditoria_iso;
    }
}
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'irLista();');
$abas[] = array('aba_nc', 'Cadastro', TRUE, 'location.reload();');

if($row['cd_pendencia_auditoria_iso'] > 0)
{
   $abas[] = array('aba_nc', 'Acompanhamento', FALSE, 'irAcomp('.$row['cd_pendencia_auditoria_iso'].');');
}

$arr_impacto[] = Array('value' => 'S', 'text' => 'Sim');
$arr_impacto[] = Array('value' => 'N', 'text' => 'Não');

echo aba_start( $abas );
    echo form_open('gestao/iso/salvar', 'name="filter_bar_form"');
        echo form_start_box( "default_box", "Cadastro" );

            echo form_default_hidden('cd_pendencia_auditoria_iso', "Código:", $row, "style='width:100%;border: 0px;' readonly" );
            echo form_default_dropdown('cd_pendencia_auditoria_iso_tipo', 'Auditoria:*', $auditoria, array($row['cd_pendencia_auditoria_iso_tipo']));
            echo form_default_integer('nr_contatacao', 'Constat.*', $row);
            echo form_default_dropdown('fl_impacto', 'Impacto:*', $arr_impacto, array($row['fl_impacto']));
            #echo form_default_dropdown('cd_processo', 'Processo:*', $processo, array($row['cd_processo']));
			echo form_default_processo('cd_processo', 'Processo:*', $row['cd_processo']);
            echo filter_usuario_ajax('cd_responsavel','',$row['cd_responsavel'],'Responsável:*','Gerência:');
            echo form_default_dropdown('cd_gerencia', 'Gerência:*', $gerencia, array(trim($row['cd_gerencia'])));
            echo form_default_textarea('ds_item', "Item:*", $row['ds_item'], "style='width:500px;'");
        echo form_end_box("default_box");

        echo form_command_bar_detail_start();
            if((($row['cd_pendencia_auditoria_iso'] == 0) OR $row['cd_responsavel'] == $this->session->userdata('codigo') OR $this->session->userdata('indic_12') == "*") AND $row['dt_encerrada'] == '')
            {
                echo button_save("Salvar");
                
                if($row['cd_pendencia_auditoria_iso'] > 0)
                {
                    echo button_save("Encerrar", "encerrar(".$row['cd_pendencia_auditoria_iso'].")", "botao_vermelho");
                }
            }
        echo form_command_bar_detail_end();

    echo form_close();

	echo "<BR><BR><BR>";

echo aba_end();

$this->load->view('footer_interna');
?>