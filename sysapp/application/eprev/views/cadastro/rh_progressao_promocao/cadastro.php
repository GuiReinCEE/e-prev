<?php
	set_title('Sistema de Avaliação - Progressão/Promoção');
	$this->load->view('header');
?>
<script>
    <?= form_default_js_submit(array('cd_cargo_area_atuacao', 'cd_classe', 'dt_progressao_promocao')); ?>

    function ir_lista()
	{
		location.href = "<?= site_url('cadastro/rh_progressao_promocao') ?>";
	}

    function set_classe()
    {
        $.post("<?= site_url('cadastro/rh_progressao_promocao/classe') ?>",
        {
            cd_cargo_area_atuacao : $("#cd_cargo_area_atuacao").val()
        },
        function(data)
        {
            var classe = $("#cd_classe");

            if(classe.prop) 
            {
                var classe_opt = classe.prop("options");
            }
            else
            {
                var classe_opt = classe.attr("options");
            }

            $("option", classe).remove();

            classe_opt[classe_opt.length] = new Option("Selecione", "");

            $.each(data.classe, function(val, text) {
                classe_opt[classe_opt.length] = new Option(text.text, text.value);
            });

            $("#cd_classe").val(data.cd_classe);
            $("#cd_classe").change();
        }, "json", true);
    }

    function set_classe_padrao()
    {
        $.post("<?= site_url('cadastro/rh_progressao_promocao/classe_padrao') ?>",
        {
            cd_classe : $("#cd_classe").val()
        },
        function(data)
        {
            var classe_padrao = $("#cd_classe_padrao");

            if(classe_padrao.prop) 
            {
                var classe_padrao_opt = classe_padrao.prop("options");
            }
            else
            {
                var classe_padrao_opt = classe_padrao.attr("options");
            }

            $("option", classe_padrao).remove();

            classe_padrao_opt[classe_padrao_opt.length] = new Option("Selecione", "");

            $.each(data, function(val, text) {
                classe_padrao_opt[classe_padrao_opt.length] = new Option(text.text, text.value);
            });
        }, "json", true);
    }

    function configure_result_table()
    {
        var ob_resul = new SortableTable(document.getElementById("table-1"),
        [
            "CaseInsensitiveString",
            "CaseInsensitiveString",
            "CaseInsensitiveString",
            "DateBR",
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
        ob_resul.sort(3, true);
    }
    
    $(function(){
        configure_result_table();
    });
</script>
<?php
    $abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
    $abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');

    $head = array(
        'Cargo/Área de Atuação',
        'Classe',
        'Padrão',
        'Dt. Progressão/Promoção',
        ''
    );

    $body = array();

    foreach($collection as $item)
    {        
        $body[] = array(
            array($item['ds_cargo_area_atuacao'], 'text-align:left'),
            $item['ds_classe'],
            $item['ds_padrao'],
            $item['dt_progressao_promocao'],
            anchor('cadastro/rh_progressao_promocao/cadastro/'.$row['cd_usuario'].'/'.$item['cd_progressao_promocao'], '[editar]')
        );
    }

    $this->load->helper('grid');
    $grid = new grid();
    $grid->head = $head;
    $grid->body = $body;

    $avatar_arquivo = $row['avatar'];
    
    if(trim($avatar_arquivo) == '')
    {
        $avatar_arquivo = $row['usuario'].'.png';
    }
    
    if(!file_exists('./up/avatar/'.$avatar_arquivo))
    {
        $avatar_arquivo = 'user.png';
    }

    echo aba_start($abas); 
        echo form_open('cadastro/rh_progressao_promocao/salvar');
            echo form_start_box('default_usuario_box', 'Colaborador');
                echo form_default_hidden('cd_progressao_promocao', '', $row);  
                echo form_default_hidden('cd_usuario', '', $row);  
                echo form_default_row('', 'Código:', '<span class="label label-inverse">'.intval($row['cd_usuario']).'</span>');
                echo form_default_row('', 'Foto atual:', '<a href="'.site_url('cadastro/avatar/index/'.intval($row['cd_usuario'])).'" title="Clique aqui para ajustar a foto"><img height="48" width="48" src="'.base_url().'up/avatar/'.$avatar_arquivo.'"></a>');
                echo form_default_row('', 'Nome:', $row['ds_nome']);
                echo form_default_row('', 'Dt. Admissão:', $row['dt_admissao']);
            echo form_end_box('default_usuario_box');
            echo form_start_box('default_box', 'Progressão/Promoção');
                echo form_default_dropdown('cd_cargo_area_atuacao', 'Cargo/Área de Atuação: (*)', $area_atuacao, $row['cd_cargo_area_atuacao'], 'onchange="set_classe();"');
                echo form_default_dropdown('cd_classe', 'Classe: (*)', $classe, $row['cd_classe'], 'onchange="set_classe_padrao();"');
                echo form_default_dropdown('cd_classe_padrao', 'Padrão:', $classe_padrao, $row['cd_classe_padrao']);
                echo form_default_date('dt_progressao_promocao', 'Dt. Progressão/Promoção: (*)', $row['dt_progressao_promocao']);
            echo form_end_box('default_box');
            echo form_command_bar_detail_start();
				echo button_save('Salvar');	
			echo form_command_bar_detail_end();
        echo form_close();
        echo br();
        echo $grid->render();
        echo br(2);
	echo aba_end();

    $this->load->view('footer_interna');
?>