<?php
set_title('Log PostgreSQL - Detalhe');
$this->load->view('header');
?>

<script>
    function ir_lista()
    {
        location.href='<?php echo site_url("servico/log_postgresql"); ?>';
    } 
</script>
<style>
    code {
    display:block;
    font: 1em 'Courier New', Courier, Fixed, monospace;
    font-size : 100%;
    color: #000;
    background : #fff url(http://www.estudiowas.com.ar/images/preback.jpg) no-repeat left top;
    overflow : auto;
    text-align:left;
    border : 1px solid #5581C0;
    padding : 0px 20px 0 30px;
    margin:1em 0 1em 0;
    line-height:17px;
    font-weight:normal!important;
    }
</style>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_nc', 'Detalhe', TRUE, 'location.reload();');

#print_r($ar_reg); exit;

$texto = "";
foreach($ar_reg as $item)
{
	$texto.= $item['linha']."\n";
}

echo aba_start( $abas );
    echo form_open('servico/tabelas_atualizar/salvar', 'name="filter_bar_form"');
        echo form_start_box( "default_box", "Detalhe" );
            echo form_default_row('','','<pre>'.$texto.'</pre>');
			
			#echo form_default_textarea('comando', 'Log:', print_r($ar_reg,true), 'style="height:900px;"');
        echo form_end_box("default_box");
    echo form_close();
    echo br(3);
echo aba_end();

$this->load->view('footer_interna');
?>