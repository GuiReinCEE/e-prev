<?php
set_title('Eventos Institucionais');
$this->load->view('header');
?>
<script>
<?php
echo form_default_js_submit(Array('nome', 'selecionado', 'desclassificado', 'cd_eventos_institucionais'));
?>
</script>
<script>
    function lista()
    {
        location.href='<?php echo site_url("ecrm/evento_institucional_inscricao"); ?>';
    }
    
    function carregar_dados_participante(data)
    {
        $('#nome').val(data.nome);
        
		$('#cpf').val(data.cpf);

        $('#telefone').val(data.ddd + ' ' + data.telefone);

        $('#email').val(data.email);

        $('#endereco').val(data.logradouro);

        $('#cidade').val(data.cidade);

        $('#uf').val(data.unidade_federativa);

        $('#cep').val(data.cep+'-'+data.complemento_cep);

		if($('#obs').html() == "")
		{
			$('#obs').val("\rDt Nascimento: " + data.dt_nascimento + " (" + data.nr_idade + " anos)");
		}

        if(data.ds_empresa_nova != null)
        {
            $('#empresa').val(data.ds_empresa + " ("+ data.ds_empresa_nova +")");
        }
        else 
        {
            $('#empresa').val(data.ds_empresa);
        }
        
		carrega_participacoes();
    }
	
	function carrega_participacoes()
	{
        $.post( '<?php echo site_url("ecrm/evento_institucional_inscricao/participacoes"); ?>/',
        {
            cd_empresa            : $('#cd_empresa').val(),
            cd_registro_empregado : $('#cd_registro_empregado').val(),
            seq_dependencia       : $('#seq_dependencia').val()
        },
        function(data)
        {
            $('#div_partic').html(data);
        });	
	}

    function del(cd_eventos_institucionais_inscricao)
    {
        if(confirm("ATENÇÃO\n\nDeseja excluir?\n\n"))
        {
            location.href="<?php echo site_url("ecrm/evento_institucional_inscricao/delete"); ?>/"+ cd_eventos_institucionais_inscricao;
        }
    }

    function carrega_evento()
    {
        var cd_eventos_institucionais = $('#cd_eventos_institucionais').val();

        if(cd_eventos_institucionais == 0)
        {
            $('#evento').html('');
        }
        else
        {
            $.post( '<?php echo site_url("ecrm/evento_institucional_inscricao/evento"); ?>/'
            ,{
                cd_eventos_institucionais: cd_eventos_institucionais
            }
            ,
            function(data)
            {
                $('#evento').html(data);
            }
        );
        }
    }

    $(document).ready(function(){
        $('#cd_eventos_institucionais').change(function(){
            carrega_evento();
        });
		
		if($('#cd_eventos_institucionais').val() > 0)
        {
			carrega_participacoes();
		}
    });
    
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'lista();');
$abas[] = array('aba_lista', 'Cadastro', TRUE, 'location.reload();');

$ar_tipo[] = array('value' => 'I', 'text' => 'Inscrito');
$ar_tipo[] = array('value' => 'A', 'text' => 'Acompanhante');

$ar_selecionado[] = array('value' => 'S', 'text' => 'Sim');
$ar_selecionado[] = array('value' => 'N', 'text' => 'Não');

echo aba_start($abas);

if (count($eventos) == 0)
{
    echo "NÃO HÁ EVENTO ABERTO";
}
else
{
    echo form_open('ecrm/evento_institucional_inscricao/salvar');
    echo form_default_hidden('cd_eventos_institucionais_inscricao', '', $row['cd_eventos_institucionais_inscricao']);
    echo form_start_box("evento_box", "EVENTO");
    echo form_default_dropdown('cd_eventos_institucionais', 'Evento:*', $eventos, array($row['cd_eventos_institucionais']));
    echo form_default_row('', '', '<span id="evento"></span>');
    echo form_end_box("evento_box");

    echo form_start_box("inscrito_box", "INSCRITO");
    echo form_default_dropdown('tipo', 'Tipo:', $ar_tipo, array($row['tipo']));
    echo form_default_dropdown('identificacao', 'Identificação:', $ar_identificacao, array($row['identificacao']));
    $c['emp']['id'] = 'cd_empresa';
    $c['re']['id'] = 'cd_registro_empregado';
    $c['seq']['id'] = 'seq_dependencia';
    $c['emp']['value'] = $row['cd_empresa'];
    $c['re']['value'] = $row['cd_registro_empregado'];
    $c['seq']['value'] = $row['seq_dependencia'];
    $c['caption'] = 'Participante:*';
    $c['callback'] = 'carregar_dados_participante';
    echo form_default_participante_trigger($c);
    echo form_default_text("empresa", "Empresa:", $row['empresa'], "style='width:500px;'");
    echo form_default_text("nome", "Nome:", $row['nome'], "style='width:500px;'");
    echo form_default_cpf("cpf", "CPF:", $row['cpf']);
    echo form_default_text("telefone", "Telefone:", $row['telefone'], "style='width:500px;'");
    echo form_default_text("email", "Email:", $row['email'], "style='width:500px;'");
    echo form_default_text("endereco", "Endereço:", $row['endereco'], "style='width:500px;'");
    echo form_default_text("cidade", "Cidade:", $row['cidade'], "style='width:500px;'");
    echo form_default_text("uf", "UF:", $row['uf'], "style='width:500px;'");
    echo form_default_text("cep", "CEP:", $row['cep'], "style='width:500px;'");
    echo form_default_textarea('obs', "Observação:", $row['obs'], "style='width:500px; height: 80px;'");
    echo form_end_box("inscrito_box");

    echo form_start_box("participacoes_box", "PARTICIPAÇÕES");
    #echo form_default_row('div_partic','', '');
    echo form_default_row('','', '<div id="div_partic" style="width: 700px;"></div>');
    #echo '<div id="div_partic"></div>';
    echo form_end_box("participacoes_box");
    
    echo form_start_box("anexos_box", "ANEXOS");
    if (intval($row['cd_eventos_institucionais_inscricao']) > 0)
    {
        if (sizeof($anexo) > 0)
        {
            $nr_conta = 1;
            foreach ($anexo as $anexos)
            {
                echo form_default_row('', 'Arquivo ' . $nr_conta . ': ', "<a href='http://www.e-prev.com.br/upload/evento_institucional_" . $row['cd_eventos_institucionais'] . "/" . $anexos['ds_arq_fisico'] . "' target='_blank'>" . $anexos['ds_arquivo'] . "</a>");
                $nr_conta++;
            }
        }
        else
        {
            echo form_default_row('', '', "Não foi enviado nenhum arquivo anexo.");
        }
    }
    echo form_end_box("anexos_box");
    
    echo form_start_box("classificacao_box", "CLASSIFICAÇÃO");
    echo form_default_dropdown('selecionado', 'Selecionado:', $ar_selecionado, array($row['selecionado']));
    echo form_default_dropdown('desclassificado', 'Desclassificado:', $ar_selecionado, array($row['desclassificado']));
    echo form_default_text("motivo", "Motivo:", $row['motivo'], "style='width:500px;'");
    echo form_default_row('', '', 'Você pode indicar "seleção" e "desclassificação" do participante no concurso, para desclassificação informe o motivo');
    echo form_end_box("classificacao_box");

    echo form_command_bar_detail_start();

		echo button_save("Salvar");

		if (intval($row['cd_eventos_institucionais_inscricao']) > 0) 
		{
			echo button_save("Excluir", 'del(' . $row['cd_eventos_institucionais_inscricao'] . ')', 'botao_vermelho');
		}

    echo form_command_bar_detail_end();
}
echo br(2);
echo aba_end();

echo form_close();
if (intval($row['cd_eventos_institucionais_inscricao']) > 0)
{
    ?>
    <script>
        carrega_evento();
    </script>
    <?php
}
$this->load->view('footer_interna');
?>

