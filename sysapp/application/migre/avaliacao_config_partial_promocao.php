<?
header("Content-Type: text/html; charset=iso-8859-1");
include_once('inc/sessao.php');
include_once('inc/conexao.php');
include_once('inc/ePrev.Service.Projetos.php');

class controle_projetos_avaliacao_config_partial_promocao
{
    private $service;
    public $command;
    public $capas;

    function __construct($db)
    {
        $this->service = new service_projetos( $db );
        $this->requestParams();

        if($this->command=="load_filtro_usuario")
        {
            $this->lista_usuario_render();
        }
        if($this->command=="adicionar_usuario_ao_comite")
        {
            $this->adicionar_usuario_ao_comite();
        }
        if($this->command=="remover_usuario_do_comite")
        {
            $this->remover_usuario_do_comite();
        }
        if($this->command=="encaminhar_comite")
        {
            $this->encaminhar_comite();
        }
        if($this->command=="definir_responsavel")
        {
            $this->ajax_definir_responsavel();
        }
    }

    function __destruct()
    {
    }

    private function requestParams()
    {
        if(isset($_POST["ajax_command_hidden"]))
        {
            $this->command = $_POST["ajax_command_hidden"];
        }
    }

    public function load()
    {
        $this->capas = $this->service->avaliacao_capa__fetch_para_promocao();
    }

    private function lista_usuario_render()
    {
        // Itens para lista
        $filtro = $_POST['filtro_nome_usuario_text'];

        $usuarios = $this->service->usuario_controledi__fetch_by_name( $filtro );

        $service = null;
        echo( '                         <table bgcolor="white" width="100%" class="tb_lista_resultado">' . "\n"  );
        echo( '                            <tr>' . "\n"  );
        echo( '                                <th><b>Código</b></th>' . "\n"  );
        echo( '                                <th><b>Nome</b></th>' . "\n"  );
        echo( '                                <th></th>' . "\n"  );
        echo( '                            </tr>' . "\n"  );
        $bgcolor = '#f4f4f4';
        foreach( $usuarios as $usuario ) 
        {
            $bgcolor = ($bgcolor=="#ffffff")?"#f4f4f4":"#ffffff";
            echo( '                         <tr bgcolor="'.$bgcolor.'">' . "\n" );
            echo( '                             <td class="td_border" align="center">'. $usuario->get_codigo() . '</td>' . "\n"  );    
            echo( '                             <td class="td_border" align="center">' . addslashes(  str_replace("\"", "", $usuario->get_nome())  ) . '</td>' . "\n"  );
            echo( '                             <td class="td_border" align="center"><a href="javascript:void(0)" onclick="esta.adicionar_usuario_Click(this)" registroId="' . $usuario->get_codigo() . '">Adicionar</a></td>' . "\n"  );
            echo( '                         </tr>' . "\n"  );
        }
        echo( '                         </table>' );
        $result = null;
    }

    private function adicionar_usuario_ao_comite()
    {
        $comite = new entity_projetos_avaliacao_comite();
        $comite->set_cd_avaliacao_capa( (int)$_POST['cd_avaliacao_capa_hidden'] );
        $comite->set_cd_usuario_avaliador( (int)$_POST['cd_usuario_avaliador_hidden'] );
        $comite->set_fl_responsavel( 'N' );
        $srv = new service_projetos($this->db);
        $ret = $this->service->avaliacao_capa__insert_integrante_comite( $comite );
    }

    private function remover_usuario_do_comite()
    {
        $ret = $this->service->avaliacao_capa__delete_integrante_comite( $_POST['cd_avaliacao_comite_hidden'] );
    }
    
    private function encaminhar_comite()
    {
        $ret = $this->service->avaliacao_capa__encaminhar_ao_comite( $_POST['cd_avaliacao_capa_hidden'] );
    }
    
    private function ajax_definir_responsavel()
    {
        $origem = $_POST['origem_hidden'];
        $cd_avaliacao_comite = $_POST['cd_avaliacao_comite_hidden'];
        $cd_avaliacao_capa = $_POST['cd_avaliacao_capa_hidden'];
        $ret = $this->service->avaliacao_capa__definir_responsavel_comite( $cd_avaliacao_comite, $origem, $cd_avaliacao_capa );
        
        if($ret) echo 'true'; 
        else     echo 'false';
    }
}

$esta = new controle_projetos_avaliacao_config_partial_promocao($db);

if($esta->command!='')
{
   exit();
}

$esta->load();
?>
<div id="message_panel"></div>
<CENTER>
<table style="width:100%">
    <tr>
    <td>
        <table align="center" border='0' style='width:95%'>
            <tr><td>

                <table cellpadding="0" cellspacing="0" border="0" align='center'>
                <tr>
                    <td>

                        <table width="100%" cellpadding="0" cellpadding="0">
                        <tr>
                            <th bgcolor="#dae9f7" style="display:none;">
                                Filtros
                            </th>
                        </tr>
                        <tr id="tr_filtro_form" style="display:none;">
                            <td>
                                <table cellpadding="0" cellpadding="0" width="100%">
                                <tr>
                                    <td>
                                        <table class="tb_cadastro_saida" style="width:100%" cellpadding="0" cellpadding="0">
                                        <tr>
                                            <th>EMP/RE/SEQ:</th>
                                            <td>
                                                <input id="filtro_empresa_text" name="FiltroEmpresaText" style="width:50px" title="Código da Empresa" value="" />
                                            </td>
                                        </tr>
                                        </table>
                                    </td>
                                    <td align="center" valign="center"><a 
                                        href="javascript:void(0)"><input type="hidden" 
                                                                         name="filtrar_hidden" 
                                                                         id="filtrar_hidden"><img id="filtrar_image" 
                                                                           src="img/btn_atualizar.jpg" 
                                                                           border="0" 
                                                                           onclick="thisPage.filtrar_Click(this);" 
                                                                           contentPartial="div_content"
                                                                           /></a></td>
                                </tr>
                                </table>
                            </td>
                        </tr>
                        </table>

                    </td>
                </tr>
				
                <tr>
                    <td>
						 <table align='center' class='tb_lista_resultado' border='0'>
							<tr>
								<th align='center' colspan="2">
									Filtros
								</th>
							</tr>
							<tr>	
								<td>
									Gerência:
								</td>
								<td>
									<select name="cd_divisao" id="cd_divisao" onchange="filtro_gerencia(this.value);">
									<option value="">Selecione</option>
									<?php
										$qr_sql = "
											SELECT codigo, 
											       nome 
											  FROM projetos.divisoes 
											 WHERE tipo = 'DIV'
											 ORDER BY nome ASC";
											 
										$ob_resultado = pg_query($db, $qr_sql);
										
										while($a_reg = pg_fetch_array($ob_resultado))
										{
											echo '<option value="'.trim($a_reg['codigo']).'" '.(trim($_REQUEST['cd_divisao']) == trim($a_reg['codigo']) ? "selected": '').'>'.trim($a_reg['nome']).'</option>';
										}
									?>
									</select>
								</td>
							</tr>
						</table>
						<br/>
                        <table align='center' class='tb_lista_resultado' border='0'>
                            <tr>
                                <th align='center'>
                                    Nome
                                </th>
                                <th align='center'>
                                    Tipo
                                </th>
                                <th align='center'>
                                    Comitê
                                </th>
                                <th align='center'>Encaminhar</th>
                            </tr>

                            <input type='hidden' name='origem_hidden' id='origem_hidden' value=''>
                            <input type='hidden' name='cd_avaliacao_capa_hidden' id='cd_avaliacao_capa_hidden' value=''>
                            <input type='hidden' name='cd_usuario_avaliador_hidden' id='cd_usuario_avaliador_hidden' value=''>
                            <input type='hidden' name='cd_avaliacao_comite_hidden' id='cd_avaliacao_comite_hidden' value=''>

                            <? $bgcolor="#ffffff"; 
                            foreach( $esta->capas as $capa ) : ?>
								<?php
									
									$fl_gerencia = true;
									if(trim($_REQUEST['cd_divisao']) != "")
									{
									    if($capa->avaliado)
										{											
											$qr_sql = "
														SELECT COUNT(*) AS fl_gerencia
														  FROM projetos.usuarios_controledi uc
														 WHERE uc.codigo  = ".($capa->avaliado->get_codigo())."
														   AND uc.divisao = '".trim($_REQUEST['cd_divisao'])."'
													  ";
											$ob_res = pg_query($db,$qr_sql);
											$ar_reg = pg_fetch_array($ob_res);
											if(intval($ar_reg['fl_gerencia']) == 0)
											{
												$fl_gerencia = false;
											}
										}
									}									
									
									
									if($fl_gerencia) :
								
								?>

                                <? if($capa!=null): ?>
									
                                    <tr bgcolor="<?php if($bgcolor!="#ffffff") $bgcolor="#ffffff"; else $bgcolor="#f4f4f4"; echo($bgcolor); ?>">
                                        <td><?= $capa->avaliado->get_nome(); ?></td>
                                        <td><? if($capa->get_tipo_promocao()=='H') echo('Horizontal'); else echo('Vertical'); ?></td>
                                        <td align='left'>

                                            <? if( $capa->comite[0]!=null ) : ?>
                                                <table>
                                                <? 
                                                $cd_avaliacao_comite = 0;
                                                foreach( $capa->comite as $integrante ) : ?>
                                                    <tr>
                                                        <?
                                                        if($integrante->get_fl_responsavel()=="S"):
                                                            $cd_avaliacao_comite = $integrante->get_cd_avaliacao_comite();
                                                        endif;
                                                        ?>
                                                        <td><a href="javascript:void(0)" 
                                                                        onclick="esta.deletar_integrante_Click(this);" 
                                                                        registroId="<?= $integrante->get_cd_avaliacao_comite() ?>"
                                                                        ><img src="img/btn_deletar.gif" border="0" title="Excluir integrante deste comitê" /></a></td>
                                                        <td><input 
                                                            type='radio'
                                                            name='responsavel_avaliacao_<?=$capa->get_cd_avaliacao_capa();?>_radio'
                                                            onclick='esta.responsavel_Click(this, "comite", <?php echo $capa->get_cd_avaliacao_capa(); ?>);'
                                                            registroId='<?=$integrante->get_cd_avaliacao_comite();?>'
                                                            responsavelId='cd_responsavel_<?=$capa->get_cd_avaliacao_capa();?>_hidden'
                                                            <?if($integrante->get_fl_responsavel()=="S") echo 'checked';?>
                                                            title='Definir o responsável pelo comitê'
                                                            /></td>
                                                        <td>- <?= $integrante->avaliador->get_nome(); ?></td>
                                                    </tr>
                                                <? endforeach; ?>
                                                </table>
                                            <? endif; ?>
                                            
                                            <?php if($capa->get_avaliador_responsavel_comite()=="S") $cd_avaliacao_comite = $capa->get_cd_avaliacao_capa(); ?>
                                            
                                            - <input 
                                                     type='radio'
                                                     name='responsavel_avaliacao_<?=$capa->get_cd_avaliacao_capa();?>_radio'
                                                     onclick='esta.responsavel_Click(this, "superior", <?php echo $capa->get_cd_avaliacao_capa(); ?>);'
                                                     registroId='<?=$capa->avaliador->get_codigo();?>'
                                                     responsavelId='cd_responsavel_<?=$capa->get_cd_avaliacao_capa();?>_hidden'
                                                     <?if($capa->get_avaliador_responsavel_comite()=="S") echo 'checked';?>
                                                     title='Definir o responsável pelo comitê'
                                                     /><?= $capa->avaliador->get_nome(); ?> (superior)<br /><br />
                                            - <a 
                                                id="nomear_comite_link" 
                                                href="javascript:void(0);" 
                                                onclick="esta.consultar_usuario_Click( this );"
                                                registroId = <?= $capa->get_cd_avaliacao_capa(); ?> 
                                                extra="show_panel"><u>Clique aqui para nomear o comitê</u></a>
                                                
											<input id='cd_responsavel_<?=$capa->get_cd_avaliacao_capa();?>_hidden' 
                                                    name='cd_responsavel_<?=$capa->get_cd_avaliacao_capa();?>_hidden' 
                                                    type='hidden' 
                                                    value='<?=$cd_avaliacao_comite;?>'
                                                    />
                                        </td>
                                        <td align='center' style='width:10px'>
                                            <a href="javascript:void(0);"
                                                onClick="esta.encaminhar_Click(this);" 
                                                registroId="<?= $capa->get_cd_avaliacao_capa(); ?>"
                                                responsavelId='cd_responsavel_<?=$capa->get_cd_avaliacao_capa();?>_hidden'
                                                ><img 
                                                    src="img/btn_confirmar.gif"
                                                    border="0"
                                                    alt='Encaminhar ao comitê'
                                                    title='Encaminhar ao comitê a avaliação!'
                                                    
                                                    /></a>
                                        </td>
                                    </tr>
                                
                                <?endif;?>
                                <?endif;?>
                                
                            <? endforeach; ?>
                        </table>
                    </td>
                </tr>

                </table>
            </td></tr>
        </table>
    </td>
    </tr>
</table>
</CENTER>

    <div    id="lista_usuario_div" 
            style="
                display:none;
                margin-top: 30px;
                margin-left: 0px;
                left: 200;
                top: 0;
                position: absolute;
            "
    >
        <table bgcolor="#dae9f7" border="1" bordercolor="#000000" cellpadding="1" cellspacing="0">
        <tr><td>
            <table class="tb_lista_resultado">
            <tr>
                <td>Filtro:<br><input type="text"
                    name="filtro_nome_usuario_text"
                    id="filtro_nome_usuario_text"
                    style="width:300px"
                    maxlenght="255"
                    /><input type="button" 
                    name="consultar_usuario_button" 
                    id="consultar_usuario_button" 
                    value="Consultar"
                    title="Clique aqui para filtrar a lista de usuários"
                    urlPartial="avaliacao_config_partial_promocao.php"
                    onclick="esta.consultar_usuario_Click(this);"
                    registroId=''
                    extra=""
                    />
                <input type="button" 
                    name="fechar_button" 
                    id="fechar_button" 
                    value="Fechar"
                    title="Clique aqui para fechar"
                    onclick="Effect.Fade('lista_usuario_div');esta.aba_promocao_Click();"
                    /></td>
            </tr>
            <tr>
            <td align="center">
                
            </td>
            </tr>
            <tr>
                <td>
                    <div    id="lista_usuario_grid_div" 
                            style="
                                height:300px;
                                overflow: auto;
                            "
                    ></div>
                </td>
            </tr>
            </table>
        </td></tr></table>

    </div>