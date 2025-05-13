<?php
set_title('Contato Dependentes - Cadastro');
$this->load->view('header');
?>
<script>
<?php
    echo form_default_js_submit(Array('cd_empresa', 'cd_registro_empregado', 'seq_dependencia'));
?>
    function ir_lista()
    {
        location.href='<?php echo site_url("ecrm/contato_dependente"); ?>';
    }
    
    function novo(cd_empresa, cd_registro_empregado, seq_dependencia)
    {
        location.href='<?php echo site_url("ecrm/contato_dependente/acompanhamento/".intval($row['cd_contato_dependente'])); ?>/'+ cd_empresa +'/'+ cd_registro_empregado +'/'+ seq_dependencia;
    }
    
	
    function part_callb(data)
	{
		var dt_obito = data.dt_obito;
		if(dt_obito != null)
		{
			dt_obito = (data.dt_obito.substring(0,10));
			if(dt_obito != "")
			{
				Date.format = 'yyyy-mm-dd';
				var dt_obito_data = Date.fromString(dt_obito);
				Date.format = 'dd/mm/yyyy';
				dt_obito = dt_obito_data.asString();
			}
		}
		
		$('#nome').html(data.nome);
        $('#email').val(data.email);
        $('#dt_obito').html(dt_obito);
        $('#email_profissional').val(data.email_profissional);
        $('#endereco').val(data.logradouro);
        $('#bairro').val(data.bairro);
        $('#cep').val(data.cep + '-' + data.complemento_cep);
        $('#cidade').val(data.cidade + ', ' + data.unidade_federativa);
        
        if((data.telefone != '') && (data.telefone != '0'))
        {
            $('#telfone').val('('+data.ddd+') '+data.telefone);
        }       
        
        if((data.celular != '') && (data.celular != '0'))
        {
            $('#celular').val('('+data.ddd+') '+data.celular);
        }  
		
		$("#default_dependente_box").show();
		$("#obMensagemContato_row").hide();
		$("#btSalvarContato").show();
	}
    
	function checkContatoDependente()
	{
		$.post('<?php echo site_url('ecrm/contato_dependente/checkContatoDependente');?>',
		$("#frmSalvarContato").serialize(),
		function(data)
		{
			if(data.cd_contato_dependente > 0)
			{
				location.href = '<?php echo site_url('ecrm/contato_dependente/cadastro');?>/' + data.cd_contato_dependente;
			}
			else
			{
				consultar_participante__cd_empresa();
			}
		},'json');	
	}
	
    $(function(){
		$("#obMensagemContato_row").hide();
		
        if($('#cd_contato_dependente').val() > 0)
        {
            $("#default_dependente_box").show();
			var ob_resul = new SortableTable(document.getElementById("table-1"),
            [
                null,
				'RE',
                'CaseInsensitiveString',
                'DateBR',
                'Number',
                'CaseInsensitiveString',
                'DateBR',
                'DateTimeBR',
                'DateTimeBR',
                'CaseInsensitiveString',
                null,
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
            ob_resul.sort(1, false);
        }   

		if(($('#cd_contato_dependente').val() == 0) && ($('#cd_empresa').val() != '') && ($('#cd_registro_empregado').val() != '') && ($('#seq_dependencia').val() != ''))
		{
			$("#default_dependente_box").hide();
			$("#btSalvarContato").hide();
			$("#obMensagemContato_row").show();
			checkContatoDependente();
		}
    });
</script>

<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_nc', 'Cadastro', TRUE, 'location.reload();');

$arr_part['cd_empresa']            = $row['cd_empresa'];
$arr_part['cd_registro_empregado'] = $row['cd_registro_empregado'];
$arr_part['seq_dependencia']       = $row['seq_dependencia'];

$config['callback']     = 'part_callb';
$config['emp']['value'] = $row['cd_empresa'];
$config['re']['value']  = $row['cd_registro_empregado'];
$config['seq']['value'] = $row['seq_dependencia'];
$config['row_id']       = "participante_row";

echo aba_start( $abas );
    echo form_open('ecrm/contato_dependente/salvar', array("id" => "frmSalvarContato","method"=>"post"));
        echo form_start_box("default_box", "Participante");			
			echo form_default_hidden('cd_contato_dependente', '', $row['cd_contato_dependente']);
			
			echo form_default_row("obMensagemContato", "", loader_html("P").' <span class="label label-important">Aguarde, buscando informações...</span>');
			
            if(intval($row['cd_contato_dependente']) == 0)
            {
                echo form_default_participante_trigger($config);
            }
            else
            {
                echo form_default_text('re', 'RE :', $row, 'style="font-weight:bold; width: 400px; border: 0px;" readonly');
            }
            
            #echo form_default_text('nome', 'Nome :', $row, 'style="font-weight:bold; width: 400px; border: 0px;" readonly');
			
			echo form_default_row('', 'Nome :', '<span class="label label-info" id="nome">'.$row["nome"].'</span>');
			
			echo form_default_row('', 'Dt. Óbito :', '<span class="label label-important" id="dt_obito">'.$row['dt_obito'].'</span>');
            echo form_default_text('telfone', "Telefone :", $row['telefone'], "style='width:100%;border: 0px;' readonly");
            echo form_default_text('celular', "Celular :", $row['celular'], "style='width:100%;border: 0px;' readonly");
            echo form_default_text('email', "Email :", $row['email'], "style='width:100%;border: 0px;' readonly");
            echo form_default_text('email_profissional', "Email Profissional :", $row['email_profissional'], "style='width:100%;border: 0px;' readonly");
            echo form_default_text('endereco', "Endereço :", $row['endereco'], "style='width:100%;border: 0px;' readonly");
            echo form_default_text('bairro', "Bairro :", $row['bairro'], "style='width:100%;border: 0px;' readonly");
            echo form_default_text('cep', "CEP :", $row['cep'], "style='width:100%;border: 0px;' readonly");
            echo form_default_text('cidade', "Cidade - UF :", $row['cidade'], "style='width:100%;border: 0px;' readonly");
        echo form_end_box("default_box");
        echo form_command_bar_detail_start();    
            if(intval($row['cd_contato_dependente']) == 0)
            {
                echo button_save("Iniciar","salvar(this.form);","botao",'id="btSalvarContato"');
            }
        echo form_command_bar_detail_end();
    echo form_close();
	

        $body = array();
        $head = array(
            '#',
            'RE',
            'Nome',
            'Dt Nascimento',
            'Idade',
            'Sexo',
            'Pensionista',
            'Dt. DIB',
            'Dt. Habilita',
            'Dt. Folha',
            'Grau Parentesco',
            'Contato',
            'Acompanhamento'
        );
        
        foreach ($collection as $item)
        {
            $contato = "Telefone: ". (trim($item['telefone']) != '' ? '('.$item['ddd'].') '.$item['telefone'] : '').br();
            $contato .= "Celular: ". (trim($item['celular']) != '' ? '('.$item['ddd'].') '.$item['celular'] : '').br();
            $contato .= "Email: ". $item['email'].br();
            $contato .= "Email Profissional: ". $item['email_profissional'].br();
            $contato .= "Endereço: ". $item['endereco'] .' , '. $item['nr_endereco'] .' '. $item['complemento_endereco'].br();
            $contato .= "Bairro: ". $item['bairro'].br();
            $contato .= "CEP: ". $row['cep'].br();
            $contato .= "Cidade - UF: ". $row['cidade'].br();
            
            $acompanhamento = '';
            
            foreach($item['acompanhamento'] as $item2)
            {
                $acompanhamento .= $item2['dt_inclusao'].' : '.$item2['ds_contato_dependente_retorno'].(trim($item2['ds_contato_dependente_acompanhamento']) != '' ? br().trim($item2['ds_contato_dependente_acompanhamento']) : "").br();
            }
            
            $body[] = array(
                ((intval($row['cd_contato_dependente']) > 0) ? '<a href="javascript:void(0);" onclick="novo('.$item["cd_empresa"].', '.$item["cd_registro_empregado"].', '.$item["seq_dependencia"].');">[Acompanhamento]</a>'  : ""),
				$item["cd_empresa"] . "/" . $item["cd_registro_empregado"] . "/" . $item["seq_dependencia"],
                
				((intval($row['cd_contato_dependente']) > 0) ? array('<a href="javascript:void(0);" onclick="novo('.$item["cd_empresa"].', '.$item["cd_registro_empregado"].', '.$item["seq_dependencia"].');">'.$item["nome"].'</a>' , "text-align:left;") : array($item["nome"], "text-align:left;font-weight:bold;")),
                
				$item["dt_nascimento"],
				$item["nr_idade"],
                $item["sexo"],
                '<span class="'.trim($item['class_pensionista']).'">'.$item["pensionista"].'</span>',
                $item["dt_dib"],
                $item["dt_habilita"],
                $item["dt_folha"],
                array($item["descricao_grau_parentesco"], "text-align:left;"),
                array($contato, "text-align:left;"),
                array(nl2br($acompanhamento), "text-align:justify;")
              );
        }
        
        $this->load->helper('grid');
        $grid = new grid();
        $grid->head = $head;
        $grid->body = $body;
        $grid->view_count = false;
        
        echo form_start_box("default_dependente_box", "Dependentes");
            echo $grid->render();
        echo form_end_box("default_dependente_box");

    echo br(5);
echo aba_end();

$this->load->view('footer_interna');
?>