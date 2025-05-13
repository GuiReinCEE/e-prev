<?php
set_title('Menu Manutenção');
$this->load->view('header');
?>
<script>
    function ir_menu()
    {
        location.href='<?php echo site_url("dev/showmenu/index/"); ?>';
    }
</script>
<style>
    #tb_mapa_menu td {
        padding-right: 5px;
		vertical-align:text-top;
    }
	    
    #tb_mapa_menu ul li{
        margin: auto;
        padding-left: 15px;
    }
	
	#tb_mapa_menu {
        font-size: 95%;
    }
	
</style>
<?php

$abas[] = array('aba_lista', 'Menu', False, 'ir_menu();');
$abas[] = array('aba_lista', 'Mapa', TRUE, 'location.reload();');

echo aba_start( $abas );
    ?>
    <table id="tb_mapa_menu">
        <tr>
            <td>
                <?php echo $menu_atividade; ?>
            </td>
            <td>
                <?php echo $menu_cadastro; ?>
            </td>
			<td>
                <?php echo $menu_ecrm; ?>
            </td>
			<td>
                <?php echo $menu_gestao; ?>
            </td>
			<td>
                <?php echo $menu_intranet; ?>
            </td>
			<td>
                <?php echo $menu_planos; ?>
            </td>
			<td>
                <?php echo $menu_servicos; ?>
            </td>
        </tr>
    </table>
    <?php
    echo br(3);
echo aba_end();

$this->load->view('footer'); 
?>