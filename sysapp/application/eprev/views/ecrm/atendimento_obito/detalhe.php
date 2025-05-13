<?php
set_title('Atendimento Óbito - Cadastro');
$this->load->view('header');
?>
<script>
<?php
echo form_default_js_submit(Array('acompanhamento'));
?>
	
    function ir_lista()
    {
        location.href='<?php echo site_url("ecrm/atendimento_obito"); ?>';
    }

    function encerrar(cd_atendimento_obito)
    {
        if(confirm("ATENÇÃO\n\nDeseja Encerrar?\n\n"))
        {
            location.href='<?php echo site_url("ecrm/atendimento_obito/encerrar"); ?>' + "/" + cd_atendimento_obito;
        }
    }	
	
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');

echo aba_start($abas);

echo form_open('ecrm/atendimento_obito/salvar');
echo form_start_box("default_box", "Participante");
echo form_default_hidden('cd_atendimento_obito', "Código: ", $cd_atendimento_obito);

$ar_part['cd_empresa'] = $row['cd_empresa'];
$ar_part['cd_registro_empregado'] = $row['cd_registro_empregado'];
$ar_part['seq_dependencia'] = $row['seq_dependencia'];
echo form_default_participante(array('cd_empresa', 'cd_registro_empregado', 'seq_dependencia', 'nome'), "Participante:", $ar_part, TRUE, FALSE);
echo form_default_text('nome', 'Nome:', $row, 'style="font-weight:bold; width: 400px; border: 0px;" readonly');

echo form_default_text('dt_obito', "Dt Óbito: ", $row, "style='color: red; font-weight:bold; width:100%;border: 0px;' readonly");
echo form_default_text('dt_dig_obito', "Dt Dig. Óbito: ", $row, "style='color: green; font-weight:bold; width:100%;border: 0px;' readonly");
echo form_default_text('telfone', "Telefone: ", (trim($row['telefone']) != '' ? '('.$row['ddd'].') '.$row['telefone'] : ''), "style='width:100%;border: 0px;' readonly");
echo form_default_text('celular', "Celular: ", (trim($row['celular']) != '' ? '('.$row['ddd'].') '.$row['celular'] : ''), "style='width:100%;border: 0px;' readonly");
echo form_default_text('email', "Email: ", $row['email'], "style='width:100%;border: 0px;' readonly");
echo form_default_text('email_profissional', "Email Profissional: ", $row['email_profissional'], "style='width:100%;border: 0px;' readonly");
echo form_default_text('endereco', "Endereço: ", $row['endereco'] .' , '. $row['nr_endereco'] .' '. $row['complemento_endereco'], "style='width:100%;border: 0px;' readonly");
echo form_default_text('bairro', "Bairro: ", $row['bairro'], "style='width:100%;border: 0px;' readonly");
echo form_default_text('cep', "CEP: ", $row['cep'].'-'.$row['complemento_cep'], "style='width:100%;border: 0px;' readonly");
echo form_default_text('cidade', "Cidade - UF: ", $row['cidade'].' - '.$row['unidade_federativa'], "style='width:100%;border: 0px;' readonly");

if ($row['dt_encerrado'] != "")
{
    echo form_default_text('dt_encerrado', "Dt Encerrado: ", $row, "style='color: blue; font-weight:bold; width:100%;border: 0px;' readonly");
}
echo form_end_box("default_box");

echo form_start_box("default_box_depende", "Dependentes");
$body = array();
$head = array(
  'RE',
  'Nome',
  'Dt Nascimento',
  'Sexo',
  'Grau Parentesco',
  'Contato',
  'Mot. Deslg.'
);

foreach ($ar_dependente as $item)
{
    $motivo = '';
    
    if(trim($item['descricao_motivo_desligamento']) != '')
    {
        $motivo = $item['cd_motivo_desligamento'] . ' - '.$item['descricao_motivo_desligamento'];
    }
    
    $contato = "Telefone: ". (trim($item['telefone']) != '' ? '('.$item['ddd'].') '.$item['telefone'] : '').br();
    $contato .= "Celular: ". (trim($item['celular']) != '' ? '('.$item['ddd'].') '.$item['celular'] : '').br();
    $contato .= "Email: ". $item['email'].br();
    $contato .= "Email Profissional: ". $item['email_profissional'].br();
    $contato .= "Endereço: ". $item['endereco'] .' , '. $item['nr_endereco'] .' '. $item['complemento_endereco'].br();
    $contato .= "Bairro: ". $item['bairro'].br();
    $contato .= "CEP: ". $row['cep'].'-'.$row['complemento_cep'].br();
    $contato .= "Cidade - UF: ". $row['cidade'].' - '.$row['unidade_federativa'].br();
    
    $body[] = array(
      $item["cd_empresa"] . "/" . $item["cd_registro_empregado"] . "/" . $item["seq_dependencia"],
      array($item["nome"], "text-align:left;"),
      $item["dt_nascimento"],
      $item["sexo"],
      array($item["descricao_grau_parentesco"], "text-align:left;"),
      array($contato, "text-align:left;"),
      (trim($item['cd_motivo_desligamento']) != '0'  ? $motivo : '')
    );
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
echo form_end_box("default_box_depende");

echo form_start_box("acompanha_box", "Acompanhamento");
echo form_default_textarea('acompanhamento', "Descrição:(*)", "", "style='width:400px; height: 70px;'");
echo form_end_box("acompanha_box");

echo form_command_bar_detail_start();
if ($row['dt_exclusao'] == "")
{
    echo button_save("Salvar");
    if ($row['dt_encerrado'] == "")
    {
        echo button_save("Encerrar", "encerrar(" . $cd_atendimento_obito . ")", "botao_vermelho");
    }
}
echo form_command_bar_detail_end();
echo form_close();


echo form_start_box("default_box_lista", "Acompanhamentos");
$body = array();
$head = array(
  'Data',
  'Cadastrado',
  'Descrição'
);

foreach ($ar_acompanhamento as $item)
{
    $body[] = array(
      $item["dt_inclusao"],
      array($item["ds_usuario_inclusao"], "text-align:left;"),
      $item["acompanhamento"]
    );
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
echo form_end_box("default_box_lista");


echo aba_end();
$this->load->view('footer_interna');
?>