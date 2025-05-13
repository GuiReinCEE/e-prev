<?php
    set_title('Cenário legal');
    $this->load->view('header');
?>
<script>
    function filtrar()
    {
        $("#result_div").html("<?= loader_html() ?>");

        $.post("<?= site_url('gestao/controle_cenario/listar') ?>",
        $("#filter_bar_form").serialize(),
        function(data)
        {
            $("#result_div").html(data);
        });
    }

    function lista()
    {
        location.href = "<?= site_url('gestao/controle_cenario/index') ?>";
    }

    function atrasada()
    {
        location.href = "<?= site_url('gestao/controle_cenario/atrasada') ?>";
    }

    $(function(){
        if($("#dt_inclusao_ini").val() == "")
        {
            $("#dt_inclusao_ini").val("01/01/<?= date('Y') ?>");
            $("#dt_inclusao_fim").val("<?= date('d/m/Y') ?>");
        }

        filtrar();
    });
</script>
<?php
    $abas[] = array('aba_lista', 'Lista', FALSE, 'lista();');
    $abas[] = array('aba_sem_data', 'Sem Data Legal', TRUE, 'location.reload();');
    $abas[] = array('aba_atrasada', 'Atrasada', FALSE, 'atrasada()');

    echo aba_start($abas);
        echo form_list_command_bar(array());
        echo form_start_box_filter();
            echo form_default_hidden('ano', '', 9999);
            echo form_default_hidden('mes', '', '');
            echo filter_date_interval('dt_inclusao_ini', 'dt_inclusao_fim', 'Dt. Inclusão:');
        echo form_end_box_filter();
        echo '<div id="result_div"></div>';
        echo br(2);
    echo aba_end();

    $this->load->view('footer');
?>