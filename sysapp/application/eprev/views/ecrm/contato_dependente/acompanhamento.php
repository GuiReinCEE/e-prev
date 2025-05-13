<?php
set_title('Contato Dependentes - Acompanhamento');
$this->load->view('header');
?>
<script>
<?php
    echo form_default_js_submit(Array('cd_contato_dependente_retorno'));
?>
    function ir_lista()
    {
        location.href='<?php echo site_url("ecrm/contato_dependente"); ?>';
    }
    
    function ir_cadastro()
    {
        location.href='<?php echo site_url("ecrm/contato_dependente/cadastro/".intval($row['cd_contato_dependente'])); ?>';
    }
    
    $(function(){
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
        ob_resul.sort(2, true);
    });
</script>

<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_nc', 'Cadastro', FALSE, 'ir_cadastro();');
$abas[] = array('aba_nc', 'Acompanhamento', TRUE, 'location.reload();');

$body = array();
$head = array(
    'Acompanhamento',
    'Retorno',
    'Dt. Inclusão',
    'Usuário'
);

foreach ($collection as $item)
{
    $body[] = array(
        array(nl2br($item["ds_contato_dependente_acompanhamento"]), "text-align:justify;"),
         array($item["ds_contato_dependente_retorno"], "text-align:left;"),
        $item["dt_inclusao"],
        array($item["nome"], "text-align:left;")
      );
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
$grid->view_count = false;

$config  = array('projetos.contato_dependente_retorno', 'cd_contato_dependente_retorno', 'ds_contato_dependente_retorno');

echo aba_start( $abas );
    echo form_open('ecrm/contato_dependente/salvar_acompanhamento');
         echo form_start_box("default_box", "Participante");
            echo form_default_hidden('cd_contato_dependente', '', $row['cd_contato_dependente']);
            echo form_default_text('re', 'RE :', $row, 'style="font-weight:bold; width: 400px; border: 0px;" readonly');
            echo form_default_row('', 'Nome :', '<span class="label label-info">'.$row["nome"].'</span>');
			echo form_default_row('', 'Dt. Óbito :', '<label class="label label-important">'.$row['dt_obito'].'</label>');
            echo form_default_text('telfone', "Telefone :", $row['telefone'], "style='width:100%;border: 0px;' readonly");
            echo form_default_text('celular', "Celular :", $row['celular'], "style='width:100%;border: 0px;' readonly");
            echo form_default_text('email', "Email :", $row['email'], "style='width:100%;border: 0px;' readonly");
            echo form_default_text('email_profissional', "Email Profissional :", $row['email_profissional'], "style='width:100%;border: 0px;' readonly");
            echo form_default_text('endereco', "Endereço :", $row['endereco'], "style='width:100%;border: 0px;' readonly");
            echo form_default_text('bairro', "Bairro :", $row['bairro'], "style='width:100%;border: 0px;' readonly");
            echo form_default_text('cep', "CEP :", $row['cep'], "style='width:100%;border: 0px;' readonly");
            echo form_default_text('cidade', "Cidade - UF :", $row['cidade'], "style='width:100%;border: 0px;' readonly");
         echo form_end_box("default_box");
		 
         echo form_start_box("default_dependente_box", "Dependente");
            echo form_default_hidden('cd_empresa', '', $row_dependente['cd_empresa']);
            echo form_default_hidden('cd_registro_empregado', '', $row_dependente['cd_registro_empregado']);
            echo form_default_hidden('seq_dependencia', '', $row_dependente['seq_dependencia']);
            echo form_default_text('re', 'RE :', $row_dependente, 'style="font-weight:bold; width: 400px; border: 0px;" readonly');
            echo form_default_row('', 'Nome :', '<span class="label label-success">'.$row_dependente["nome"].'</span>');
		
            echo form_default_text('dep_telefone', "Telefone :", $row_dependente['telefone'], "style='width:100%;border: 0px;' readonly");
            echo form_default_text('dep_celular', "Celular :", $row_dependente['celular'], "style='width:100%;border: 0px;' readonly");
            echo form_default_text('dep_email', "Email :", $row_dependente['email'], "style='width:100%;border: 0px;' readonly");
            echo form_default_text('dep_email_profissional', "Email Profissional :", $row_dependente['email_profissional'], "style='width:100%;border: 0px;' readonly");
            echo form_default_text('dep_endereco', "Endereço :", $row_dependente['endereco'], "style='width:100%;border: 0px;' readonly");
            echo form_default_text('dep_bairro', "Bairro :", $row_dependente['bairro'], "style='width:100%;border: 0px;' readonly");
            echo form_default_text('dep_cep', "CEP :", $row_dependente['cep'], "style='width:100%;border: 0px;' readonly");
            echo form_default_text('dep_cidade', "Cidade - UF :", $row_dependente['cidade'], "style='width:100%;border: 0px;' readonly");
			
         echo form_end_box("default_dependente_box");
		 
         echo form_start_box("default_acompanhamento_box", "Registrar Acompanhamento");
            echo form_default_textarea('ds_contato_dependente_acompanhamento', 'Descrição :', '', 'style="width:500px; height:100px;"');
            echo form_default_dropdown_db('cd_contato_dependente_retorno', 'Retorno :*', $config, '', '', '', TRUE);	
         echo form_end_box("default_acompanhamento_box");
         echo form_command_bar_detail_start();    
            echo button_save("Salvar");
        echo form_command_bar_detail_end();
		
		
         echo form_start_box("default_acompanhamento_lista_box", "Acompanhamento(s)");
			echo $grid->render();
         echo form_end_box("default_acompanhamento_lista_box");		
        
    echo form_close();
    echo br(5);
echo aba_end();

$this->load->view('footer_interna');
?>