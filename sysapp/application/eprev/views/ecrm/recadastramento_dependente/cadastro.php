<?php
set_title('Recadastramento de Dependente - Cadastro');
$this->load->view('header');
?>
<script>
    <?= form_default_js_submit(array()) ?>
   
    function ir_lista()
    {
        location.href = "<?= site_url('ecrm/recadastramento_dependente') ?>";
    }

    function cancelar(t)
    {
        var retorno = true;

        if($('#ds_justificativa').val() == '')
        {
            alert('Informe a justificativa do cancelamento.');
            
            retorno = false;
        }

        if(retorno)
        {
            var confirmacao = 'Deseja cancelar a solicitação de recadastramento?\n\n'+
                'Clique [Ok] para Sim\n\n'+
                'Clique [Cancelar] para Não\n\n';

            if(confirm(confirmacao))
            {
                t.submit();
            }
        }
    }

    function confirmar(tipo)
    {
        var confirmacao = 'Deseja confirmar a solicitação de recadastramento?\n\n'+
            'Clique [Ok] para Sim\n\n'+
            'Clique [Cancelar] para Não\n\n';

        if(confirm(confirmacao))
        { 
            location.href = '<?= site_url('ecrm/recadastramento_dependente/confirmar/'.$row['cd_recadastramento_dependente']) ?>/'+tipo;
        }
    }

    function confirmar_endereco()
    {
        var confirmacao = 'Deseja confirmar a atualização dos dados de contato no ELETRO?\n\n'+
            'Clique [Ok] para Sim\n\n'+
            'Clique [Cancelar] para Não\n\n';

        if(confirm(confirmacao))
        { 
            location.href = '<?= site_url('ecrm/recadastramento_dependente/confirmar_endereco/'.$row['cd_recadastramento_dependente']) ?>';
        }
    }

    function configure_result_table()
    {
        var ob_resul = new SortableTable(document.getElementById("table-1"),
        [
            'CaseInsensitiveString',
            'DateBr',
            'CaseInsensitiveString',
            'CaseInsensitiveString',
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
        ob_resul.sort(0, false);

         var ob_resul2 = new SortableTable(document.getElementById("table-2"),
        [
            'CaseInsensitiveString',
            'DateBr',
            'CaseInsensitiveString',
            'CaseInsensitiveString',
            'CaseInsensitiveString',
            null
        ]);
        ob_resul.onsort = function ()
        {
            var rows = ob_resul2.tBody.rows;
            var l = rows.length;
            for (var i = 0; i < l; i++)
            {
                removeClassName( rows[i], i % 2 ? "sort-par" : "sort-impar" );
                addClassName( rows[i], i % 2 ? "sort-impar" : "sort-par" );
            }
        };
        ob_resul2.sort(0, false);
    }

    $(function(){
        configure_result_table();
        default_contato_atual_box_box_recolher();
        default_bancario_atual_box_box_recolher();
    });
</script>
<?php
    $abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
    $abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');

    $this->load->helper('grid');
    $grid = new grid();

    $head = array(

        'Nome',
        'Dt. Nascimento',
        'Grau Parentesco',
        'Sexo',
        'Inválido',
        'Arquivo'
    );

    $body = array();

    foreach($collection as $item)
    {
        $arquivo = '';

        if(intval($item['cd_recadastramento_dependente_grau']) == 1)
        {
            if(trim($item['documento_identificacao']) != '' AND trim($item['certidao_casamento']) != '')
            {    
                $arquivo = anchor(base_url().'up/recadastramento_dependente/'.$item['certidao_casamento'],'certidao_casamento.pdf') .'<br>'. anchor(base_url().'up/recadastramento_dependente/'.$item['documento_identificacao'],'documento_identificacao.pdf', array('target' => "_blank")); 
            }
            else if($item['certidao_casamento'] != '' AND trim($item['documento_identificacao']) == '')
            {
                $arquivo = anchor(base_url().'up/recadastramento_dependente/'.$item['certidao_casamento'],'certidao_casamento.pdf', array('target' => "_blank"));
            }  
            else if(trim($item['documento_identificacao']) != '' AND trim($item['certidao_casamento']) == '') 
            {
                 $arquivo =anchor(base_url().'up/recadastramento_dependente/'.$item['documento_identificacao'],'documento_identificacao.pdf', array('target' => "_blank"));
            }  
        }        
        else if(intval($item['cd_recadastramento_dependente_grau']) == 2)
        {
            if(trim($item['documento_identificacao']) != '' AND trim($item['declaracao_convivencia']) != '')
            {    
                $arquivo = anchor(base_url().'up/recadastramento_dependente/'.$item['declaracao_convivencia'],'declaracao_convivencia.pdf') .'<br>'. anchor(base_url().'up/recadastramento_dependente/'.$item['documento_identificacao'],'documento_identificacao.pdf', array('target' => "_blank"));
            }
            else if ($item['declaracao_convivencia'] != '' AND trim($item['documento_identificacao']) == '')
            {
                $arquivo = anchor(base_url().'up/recadastramento_dependente/'.$item['declaracao_convivencia'], 'declaracao_convivencia.pdf', array('target' => "_blank"));
            }
            else if(trim($item['documento_identificacao']) != '' AND trim($item['declaracao_convivencia']) == '')
            {
                $arquivo = anchor(base_url().'up/recadastramento_dependente/'.$item['documento_identificacao'],'documento_identificacao.pdf', array('target' => "_blank"));
            }
        }      
        else if((intval($item['cd_recadastramento_dependente_grau']) == 3) OR (intval($item['cd_recadastramento_dependente_grau']) == 4))
        {
            if($item['certidao_nascimento'] != ''  AND  trim($item['documento_identificacao']) != '')
            {   
                $arquivo = anchor(base_url().'up/recadastramento_dependente/'.$item['certidao_nascimento'], 'certidao_nascimento.pdf', array('target' => "_blank")).'<br>'. anchor(base_url().'up/recadastramento_dependente/'.$item['documento_identificacao'],'documento_identificacao.pdf', array('target' => "_blank"));
            }
            else if($item['certidao_nascimento'] != ''  AND  trim($item['documento_identificacao']) == '')
            {
                $arquivo = anchor(base_url().'up/recadastramento_dependente/'.$item['certidao_nascimento'], 'certidao_nascimento.pdf', array('target' => "_blank"));
            }
            else if(trim($item['documento_identificacao']) != '' AND trim($item['certidao_nascimento']) == '')
            {
                $arquivo =  anchor(base_url().'up/recadastramento_dependente/'.$item['documento_identificacao'],'documento_identificacao.pdf', array('target' => "_blank"));
            }    
        } 

        $body[] = array(
            array($item['ds_nome'], 'text-align:left;'),
            $item['dt_nascimento'],
            array($item['ds_recadastramento_dependente_grau'], 'text-align:left;'),
            array($item['ds_sexo'], 'text-align:left;'),
            array($item['ds_invalido'], 'text-align:left;'), 
            array($arquivo, 'text-align:left;')
        );
    }
    $grid->head       = $head;
    $grid->body       = $body;
    $grid->id_tabela  = 'table-1';
    $grid->view_count = false;
    $tabela_1         = $grid->render();

    $head2 = array(
        'RE',
        'Nome',
        'Dt. Nascimento',
        'Grau Parentesco',
        'Sexo',
        'Opção',
        'Arquivo'
    );

    $body2 = array();

    foreach($dependente as $item)
    {
        $body2[] = array(
            $item['cd_empresa'].'/'.$item['cd_registro_empregado'].'/'.$item['seq_dependencia'],
            array($item['nome'], 'text-align:left;'),
            $item['dt_nascimento'],
            array($item['descricao_grau_parentesco'], 'text-align:left;'),
            array($item['ds_sexo'], 'text-align:left;'),
            '<label class="label label-'.(trim($item['fl_opcao']) == 'M' ? 'info' : 'important').'">'.$item['ds_opcao'].'</label>' .'</label>',
            (trim($item['arquivo_dependente']) != '' ? anchor(base_url().'up/recadastramento_dependente/'.$item['arquivo_dependente'],'arquivo.pdf') : '')
        );
    }

    $grid->head       = $head2;
    $grid->body       = $body2;
    $grid->view_count = false;
    $grid->id_tabela  = "table-2";
    $tabela_2         = $grid->render();

    echo aba_start($abas);
        echo form_start_box('default_box', 'Participante');			
    		
            echo form_default_row('', 'RE:', $row['cd_empresa'].'/'.$row['cd_registro_empregado'].'/'.$row['seq_dependencia']);
            echo form_default_row('', 'Nome:', $row['nome'] );
            echo form_default_row('id_doc','Protocolo Assinatura:', '<span class="label label-success">'.$row['id_doc'].'</span> '.(trim($row['id_doc']) != '' ? anchor('https://www.fcprev.com.br/fundacaofamilia/index.php/assinatura_documento/index/'.$row['id_doc'], '[ver situação]', array('target' => '_blank')) : ""));
            echo form_default_row('', 'Status Assinatura:', '<label class="label label-inverse">'.$row['ds_status_assinatura'].'</label>' );
            if(trim($row['dt_confirmacao']) != '') 
            {
                echo form_default_row('', 'Dt. Confirmação:', $row['dt_confirmacao']);
                echo form_default_row('', 'Usuário:', $row['ds_usuario_confirmacao']);
            }

            if(trim($row['dt_confirmacao_endereco']) != '') 
            {
                echo form_default_row('', 'Dt. Confirmação Endereço:', $row['dt_confirmacao_endereco']);
                echo form_default_row('', 'Usuário:', $row['ds_usuario_confirmacao_endereco']);
            }
           
        echo form_end_box('default_box');
        echo form_command_bar_detail_start();
            if((trim($row['dt_confirmacao']) == '') AND (trim($row['dt_cancelamento']) == '') AND (trim($row['fl_confirmar']) == 'S') AND $fl_permissao)
            {
                echo button_save('Confirmar Solicitação', 'confirmar(0)', 'botao_verde');
                echo button_save('Enviar e-mail', 'confirmar(1)', 'botao_verde');
            }       
        echo form_command_bar_detail_end();

        echo form_start_box('default_contato_atual_box', 'Contato - ATUAL');
            echo form_default_row('', 'CEP:', $participante['cep'].'-'.$participante['complemento_cep']);
            echo form_default_row('', 'Logradouro:', $participante['endereco']);
            echo form_default_row('', 'Número:', $participante['nr_endereco']);
            echo form_default_row('', 'Complemento:', $participante['complemento_endereco']);
            echo form_default_row('', 'Bairro:', $participante['bairro']);
            echo form_default_row('', 'Cidade:', $participante['cidade']);
            echo form_default_row('', 'UF:', $participante['unidade_federativa']);
            echo form_default_row('', '(DDD) Telefone:', (trim($participante['telefone']) != '' ? '('.$participante['ddd'].') '.$participante['telefone'] : ''));
            echo form_default_row('', 'Ramal:', $participante['ramal']);
            echo form_default_row('', '(DDD) Celular:', (trim($participante['celular']) != '' ? '('.$participante['ddd_celular'].') '.$participante['celular'] : ''));
            echo form_default_row('', 'E-mail:', $participante['email']);
            echo form_default_row('', 'E-mail Profissional:', $participante['email_profissional']);
        echo form_end_box('default_contato_box');
        echo form_command_bar_detail_start();
        /*
            if((trim($row['dt_confirmacao_endereco']) == '') AND (trim($row['dt_cancelamento'])== ''))
            {
                echo button_save('Confirmar Dados de Contato', 'confirmar_endereco()', 'botao_verde');
            }
        */
        echo form_command_bar_detail_end();
        echo form_start_box('default_bancario_atual_box', 'Dados Bancários - ATUAL');
            echo form_default_row('', 'Banco:', $participante['banco']);
            echo form_default_row('', 'Agência:', $participante['cd_agencia']);
            echo form_default_row('', 'Conta:', $participante['conta_folha']);
            echo form_default_row('', 'Nome Correntista:', $participante['nome']);
        echo form_end_box('default_bancario_box');

        echo form_start_box('default_contato_box', 'Contato - RECADASTRO');
            echo form_default_row('', 'CEP:', $row['cep'].'-'.$row['complemento_cep']);
            echo form_default_row('', 'Logradouro:', $row['endereco']);
            echo form_default_row('', 'Número:', $row['nr_endereco']);
            echo form_default_row('', 'Complemento:', $row['complemento_endereco']);
            echo form_default_row('', 'Bairro:', $row['bairro']);
            echo form_default_row('', 'Cidade:', $row['cidade']);
            echo form_default_row('', 'UF:', $row['unidade_federativa']);
            echo form_default_row('', '(DDD) Telefone:', (trim($row['telefone']) != '' ? '('.$row['ddd'].') '.$row['telefone'] : ''));
            echo form_default_row('', 'Ramal:', $row['ramal']);
            echo form_default_row('', '(DDD) Celular:', (trim($row['celular']) != '' ? '('.$row['ddd_celular'].') '.$row['celular'] : ''));
            echo form_default_row('', 'E-mail:', $row['email']);
            echo form_default_row('', 'E-mail Profissional:', $row['email_profissional']);
        echo form_end_box('default_contato_box');
        echo form_command_bar_detail_start();
        /*
            if((trim($row['dt_confirmacao_endereco']) == '') AND (trim($row['dt_cancelamento'])== ''))
            {
                echo button_save('Confirmar Dados de Contato', 'confirmar_endereco()', 'botao_verde');
            }
        */
        echo form_command_bar_detail_end();
        echo form_start_box('default_bancario_box', 'Dados Bancários - RECADASTRO');
            echo form_default_row('', 'Banco:', $row['banco']);
            echo form_default_row('', 'Agência:', $row['agencia']);
            echo form_default_row('', 'Conta:', $row['conta']);
            echo form_default_row('', 'Nome Correntista:', $row['nome_correntista']);
        echo form_end_box('default_bancario_box');

        if(trim($row['dt_confirmacao']) == '')
        {
            echo form_open('ecrm/recadastramento_dependente/cancelar');
                echo form_start_box('default_cancelamento_box', 'Cancelar Solicitação'); 
                    echo form_default_hidden('cd_recadastramento_dependente', '', $row['cd_recadastramento_dependente']);
                    if(trim($row['dt_cancelamento']) != '')
                    {
                        echo form_default_row('', 'Dt. Cancelamento:', $row['dt_cancelamento']);
                    }

                    if(trim($row['ds_usuario_cancelamento']) != '')
                    {
                        echo form_default_row('', 'Usuário:', $row['ds_usuario_cancelamento']);
                    }

                    if(!(trim($row['dt_cancelamento']) != '' AND trim($row['ds_usuario_cancelamento']) == ''))
                    {
                        echo form_default_textarea('ds_justificativa', 'Justificativa: (*)', $row['ds_justificativa'], 'style="height:80px;"');
                    }
                echo form_end_box('default_cancelamento_box');
                echo form_command_bar_detail_start();
                    if((trim($row['dt_confirmacao']) == '') AND (trim($row['dt_cancelamento'])== '') AND $fl_permissao)
                    {
                        echo button_save('Cancelar Solicitação', 'cancelar(this.form)', 'botao_disabled');
                    }       
                echo form_command_bar_detail_end();
            echo form_close();  
        }
        echo br(); 
        echo form_start_box('default_box', 'Alteração Dependente');            
            echo $tabela_2;    
        echo form_end_box('default_box');
        echo form_start_box('default_box', 'Confirmação Dependente');            
            echo $tabela_1;    
        echo form_end_box('default_box');  
    echo br(2);
    echo aba_end();
    $this->load->view('footer_interna');
?>