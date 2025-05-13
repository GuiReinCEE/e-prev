<? header("Content-Type: text/html; charset=iso-8859-1"); ?>
<?
include_once('inc/sessao.php');
include_once('inc/conexao.php');

include_once('inc/ePrev.Service.Projetos.php');

class avaliacao_partial_avaliacao_comite
{   #begin_class
    private $db;
    private $command;
    private $id;
    private $cd_usuario_logado;
    private $usuario_avaliado;
    private $usuario_avaliador;
    private $row;
    private $avaliacao=null;

    private $grau_ci;
    private $val_ci;
    private $grau_esc;
    private $val_esc;
    private $grau_ce;
    private $val_ce;
    private $grau_resp;
    private $val_resp;

    // collections
    private $result_comp_inst;
    private $result_escolaridade;
    private $result_comp_espec;
    private $result_resp;

    private $capas;
    public $capa;

    function avaliacao_partial_avaliacao_comite( $_db )
    {
        $this->db = $_db;
        $this->avaliacao = new entity_projetos_avaliacao_extended();
        $this->requestParams();
        if ($this->command=="view") 
        {
            $this->load();
        }
        if ($this->command=="edit") 
        {
            $this->load();
        }
    }

    function __destruct()
    {
        $this->db = null;
    }
    
    public function get_grau_ci()
    {
        return $this->grau_ci;
    }
    public function get_val_ci()
    {
        return $this->val_ci;
    }
    public function get_grau_esc()
    {
        return $this->grau_esc;
    }
    public function get_val_esc()
    {
        return $this->val_esc;
    }
    public function get_grau_ce()
    {
        return $this->grau_ce;
    }
    public function get_val_ce()
    {
        return $this->val_ce;
    }
    public function get_grau_resp()
    {
        return $this->grau_resp;
    }
    public function get_val_resp()
    {
        return $this->val_resp;
    }

    public function get_result_comp_inst()
    {
        return $this->result_comp_inst;
    }
    public function get_result_escolaridade()
    {
        return $this->result_escolaridade;
    }
    public function get_result_comp_espec()
    {
        return $this->result_comp_espec;
    }
    public function get_result_resp()
    {
        return $this->result_resp;
    }
    
    public function get_command()
    {
        return $this->command;
    }

    function requestParams()
    {
        $this->id = $_POST["cd_avaliacao_selected_hidden"];
        $this->command = utf8_decode( $_POST["ajax_command_hidden"] );
        $this->cd_usuario_logado = $_SESSION["Z"];
    }

    function load()
    {
        $service = new service_projetos($this->db);

        $filtro = new entity_projetos_avaliacao_capa_extended();
        $filtro->set_cd_avaliacao_capa( $this->id );
        $this->capas = $service->avaliacao_capa_FetchAll( $filtro );
        $this->capa = $this->capas[0];
        
        $find_aval=false;
        foreach($this->capa->avaliacoes as $avaliacao)
        {
            // encontra avaliação do tipo 'A' que indica que foi realizada pelo Avaliado
            if (!is_null($avaliacao))
            {
                if ($avaliacao->get_tipo()=="C" && $avaliacao->get_cd_usuario_avaliador()==$this->cd_usuario_logado)
                {
                    $this->avaliacao = $avaliacao;
                    $find_aval=true;
                    break;
                }
            }
        }
        $filtro = null;
        $service = null;

        $dal = new DBConnection();
        $dal->loadConnection( $this->db );

        // load grau_ci
        $dal->createQuery("

            SELECT SUM(grau) AS grau_ci 
              FROM projetos.avaliacoes_comp_inst 
             WHERE cd_avaliacao = {cd_avaliacao} 

        ");
        
        $dal->setAttribute("{cd_avaliacao}", (int)$this->avaliacao->get_cd_avaliacao());
        $result_grau_ci = $dal->getResultset();

		// echo $dal->getMessage();

        $row_grau_ci = pg_fetch_array($result_grau_ci);
        $this->grau_ci = $row_grau_ci["grau_ci"];

        // load val_ci
        $dal->createQuery("

            SELECT COUNT(*) AS ocorr_ci 
              FROM projetos.avaliacoes_comp_inst 
             WHERE cd_avaliacao = {cd_avaliacao} 

        ");
        $dal->setAttribute("{cd_avaliacao}", (int)$this->avaliacao->get_cd_avaliacao());
        $result_val_ci = $dal->getResultset();
        $row_val_ci = pg_fetch_array($result_val_ci);
        if ($row_val_ci["ocorr_ci"] == "0") 
        {   
            $this->val_ci = "Não realizada!";
        }
        else 
        {
            $this->val_ci = number_format(($this->grau_ci / $row_val_ci['ocorr_ci']),2);
        }

        if ($this->capa->get_grau_escolaridade() == "" || $this->capa->get_grau_escolaridade()=="0") 
        {   
            $this->val_esc = "Não realizada!";
        }
        else 
        {
            $this->val_esc = $this->capa->get_grau_escolaridade();
        }

        // load grau_ce
        $dal->createQuery("

            SELECT SUM(grau) AS grau_ce 
              FROM projetos.avaliacoes_comp_espec 
             WHERE cd_avaliacao = {cd_avaliacao} 

        ");
        $dal->setAttribute("{cd_avaliacao}", (int)$this->avaliacao->get_cd_avaliacao());
        $result_grau_ce = $dal->getResultset();
        $row_grau_ce = pg_fetch_array($result_grau_ce);
        $this->grau_ce = $row_grau_ce["grau_ce"];

        // load val_ce
        $dal->createQuery("

            SELECT COUNT(*) AS ocorr_ce
              FROM projetos.avaliacoes_comp_espec
             WHERE cd_avaliacao = {cd_avaliacao} 

        ");
        $dal->setAttribute( "{cd_avaliacao}", (int)$this->avaliacao->get_cd_avaliacao() );
        $result_val_ce = $dal->getResultset();
        $row_val_ce = pg_fetch_array($result_val_ce);
        if ($row_val_ce["ocorr_ce"] == "0") 
        {   
            $this->val_ce = "Não realizada!";
        }
        else 
        {
            $this->val_ce = number_format( ($this->grau_ce / $row_val_ce['ocorr_ce']), 2 );
        }

        // load grau_resp
        $dal->createQuery("

            SELECT SUM(grau) AS grau_resp
              FROM projetos.avaliacoes_responsabilidades 
             WHERE cd_avaliacao = {cd_avaliacao} 

        ");
        $dal->setAttribute("{cd_avaliacao}", (int)$this->avaliacao->get_cd_avaliacao());
        $result_grau_resp = $dal->getResultset();
        $row_grau_resp = pg_fetch_array($result_grau_resp);
        $this->grau_resp = $row_grau_resp["grau_resp"];

        // load val_resp
        $dal->createQuery("

            SELECT COUNT(*) AS ocorr_resp
              FROM projetos.avaliacoes_responsabilidades
             WHERE cd_avaliacao = {cd_avaliacao}

        ");
        $dal->setAttribute( "{cd_avaliacao}", (int)$this->avaliacao->get_cd_avaliacao() );
        $result_val_resp = $dal->getResultset();
        $row_val_resp = pg_fetch_array($result_val_resp);
        if ($row_val_resp["ocorr_resp"] == "0") 
        {   
            $this->val_resp = "Não realizada!";
        }
        else 
        {
            $this->val_resp = number_format( ($this->grau_resp / $row_val_resp['ocorr_resp']), 2 );
        }

         // load collection comp_inst
            $dal->createQuery("

                SELECT ci.cd_comp_inst AS cd_comp_inst
                     , nome_comp_inst
                     , desc_comp_inst
                  FROM projetos.comp_inst ci
                     , projetos.cargos_comp_inst cci
                 WHERE cci.cd_comp_inst = ci.cd_comp_inst 
                   AND cci.cd_cargo = {cd_cargo}
              ORDER BY nome_comp_inst

            ");
            $dal->setAttribute( "{cd_cargo}", $this->capa->get_cd_cargo() );
            $this->result_comp_inst = $dal->getResultset();

            // load collection escolaridade
            $dal->createQuery("

                SELECT 
					f.cd_escolaridade
                     , grau_percentual
                     , nivel
					 , nome_escolaridade
                     , e.ordem
                  FROM 
					projetos.familias_escolaridades f
                     , projetos.cargos c
                     , projetos.escolaridade e
                 WHERE 
					c.cd_cargo = {cd_cargo}
                   AND c.cd_familia = f.cd_familia 
                   AND f.cd_escolaridade = e.cd_escolaridade 
              ORDER BY grau_percentual DESC

            ");
            $dal->setAttribute( "{cd_cargo}", $this->capa->get_cd_cargo() );
            $this->result_escolaridade = $dal->getResultset();

            // load collection comp_espec
            $dal->createQuery("

                SELECT ce.cd_comp_espec
                     , nome_comp_espec
                     , desc_comp_espec 
                  FROM projetos.comp_espec ce
                     , projetos.cargos_comp_espec cce
                 WHERE cce.cd_comp_espec = ce.cd_comp_espec 
				   AND cce.cd_cargo = {cd_cargo}
             ORDER BY nome_comp_espec

            ");
            $dal->setAttribute( "{cd_cargo}", $this->capa->get_cd_cargo() );
            $this->result_comp_espec = $dal->getResultset();

            // load collection responsabilidades
            $dal->createQuery("

                SELECT r.cd_responsabilidade as cd_responsabilidade
                     , nome_responsabilidade
                     , desc_responsabilidade 
                  FROM projetos.responsabilidades r
                     , projetos.cargos_responsabilidades cr
                 WHERE cr.cd_responsabilidade = r.cd_responsabilidade 
                   AND cr.cd_cargo = {cd_cargo}
              ORDER BY nome_responsabilidade

            ");
            $dal->setAttribute( "{cd_cargo}", $this->capa->get_cd_cargo() );
            $this->result_resp = $dal->getResultset();

        $dal = null;

    }

    public function get_id()
    {
        return $this->id;
    }
    
    public function get_row()
    {
        return $this->row;
    }

    public function get_cd_usuario_logado()
    {
        return $this->cd_usuario_logado;
    }

    public function get_nome_usuario_logado()
    {
        if ($this->nome_usuario_logado=="") {
            $nome = "";
            $dal = new DBConnection();
            $dal->loadConnection($this->db);
            $dal->createQuery( "
                SELECT nome 
                  FROM projetos.usuarios_controledi  
                  WHERE codigo = {codigo} 
            " );
            $dal->setAttribute( "{codigo}", (int)$this->cd_usuario_logado );
            $result = $dal->getResultset();
            $row = pg_fetch_array($result);
            $nome = $row["nome"];
            $row = null;
            $dal = null;
            $this->nome_usuario_logado = $nome;
        }
        return $this->nome_usuario_logado;
    }
    
    public function get_avaliacao()
    {
        return $this->avaliacao;
    }
    
    public function grau_escolaridade_is_disabled($value_1, $value_2)
    {
        $ret = "";
        if($value_1 == $value_2)
        {
            $ret = "";
        }
        else
        {
            $ret = "disabled";
        }
        return $ret;
    }
    public function grau_escolaridade_valor($value_1, $value_2, $percentual)
    {
        $ret = "";
        if ($value_1 == $value_2)
        {
            $ret = number_format($percentual, 0, '.' ,'.');
        }
        return $ret;
    }
    public function grau_escolaridade_is_checked($nivel_1, $nivel_2, $grau_escolaridade, $percentual)
    {
        $ret = "";
        if ($grau_escolaridade == $percentual && $nivel_1 == $nivel_2)
        {
            $ret = "checked";
        }
        return $ret;
    }
    
    public function comp_inst_checked($cd_comp_inst, $value)
    {
        $dal = new DBConnection();
        $dal->loadConnection( $this->db );
        $dal->createQuery("

            SELECT grau 
              FROM projetos.avaliacoes_comp_inst 
             WHERE cd_avaliacao = {cd_avaliacao} 
               AND cd_comp_inst = {cd_comp_inst}

        ");
        $dal->setAttribute( "{cd_avaliacao}" , (int)$this->avaliacao->get_cd_avaliacao() );
        $dal->setAttribute( "{cd_comp_inst}" , (int)$cd_comp_inst );
        $result = $dal->getResultset();

        if ($reg2 = pg_fetch_array($result))
        {
            if ($reg2['grau'] == $value)
            {
                return "checked";
            }
        }
        else 
        {
            //$tpl->assign('cor_fundo', '#F0E0C7');
        } 
    }

    public function comp_espec_checked($cd_comp_espec, $value)
    {
        $dal = new DBConnection();
        $dal->loadConnection( $this->db );
        $dal->createQuery("

            SELECT grau 
              FROM projetos.avaliacoes_comp_espec
             WHERE cd_avaliacao = {cd_avaliacao} 
               AND cd_comp_espec = {cd_comp_espec}

        ");
        $dal->setAttribute( "{cd_avaliacao}" ,(int) $this->avaliacao->get_cd_avaliacao() );
        $dal->setAttribute( "{cd_comp_espec}" , (int)$cd_comp_espec );
        $result = $dal->getResultset();

        if ($reg2 = pg_fetch_array($result))
        {
            if ($reg2['grau'] == $value)
            {
                return "checked";
            }
        }
        else 
        {
            //$tpl->assign('cor_fundo', '#F0E0C7');
        } 
    }

    public function responsabilidade_checked($cd_responsabilidade, $value)
    {
        $dal = new DBConnection();
        $dal->loadConnection( $this->db );
        $dal->createQuery("

            SELECT grau 
              FROM projetos.avaliacoes_responsabilidades
             WHERE cd_avaliacao = {cd_avaliacao} 
               AND cd_responsabilidade = {cd_responsabilidade}

        ");
        $dal->setAttribute( "{cd_avaliacao}" , (int)$this->avaliacao->get_cd_avaliacao() );
        $dal->setAttribute( "{cd_responsabilidade}" , (int)$cd_responsabilidade );
        $result = $dal->getResultset();

        if ($reg2 = pg_fetch_array($result))
        {
            if ($reg2['grau'] == $value)
            {
                return "checked";
            }
        }
        else 
        {
            //$tpl->assign('cor_fundo', '#F0E0C7');
        } 
    }
    
    public function is_avaliado()
    {
        $ret = (

            ($this->capa->get_status()=="A" && $this->capa->get_cd_usuario_avaliado()==$this->cd_usuario_logado)

        );
        return $ret;
    }
    
    public function is_avaliador()
    {
        $ret = (
         
            ($this->capa->get_status()=="F" && $this->capa->get_cd_usuario_avaliador()==$this->cd_usuario_logado)
        
        );
        return $ret;
    }
    
    public function is_comite()
    {
        $ret = (
         
            ($this->capa->get_status()=="S" && $this->pertence_comite())
        
        );
        return $ret;
    }
    
    public function pertence_comite()
    {
        $ret = false;
        $integrantes = $this->capa->comite;
        
        foreach( $integrantes as $integrante )
        {
            if ($integrante->get_cd_usuario_avaliador()==$this->cd_usuario_logado)
            {
                $ret = true;
                break;
            }
        }
        return $ret;
    }

    public function texto_conceito( $codigo )
    {
        $service = new service_projetos($this->db);
        $e = new entity_public_listas();
        $e->set_codigo( $codigo );
        $service->public_listas_load_by_pk($e);
        return utf8_decode($e->get_descricao()); //'teste de texto de conceito';
    }


} #end_class

$esta = new avaliacao_partial_avaliacao_comite( $db );

?>
    <div id="message_panel"></div>    

    <input type="hidden" name="status_hidden" id="status_hidden" value="<?= $esta->capa->get_status() ?>">
    <input type="hidden" name="status_original_hidden" id="status_original_hidden" value="<?= $esta->capa->get_status() ?>">

    <input type="hidden" name="cd_avaliacao_hidden" id="cd_avaliacao_hidden" value="<?= $esta->get_avaliacao()->get_cd_avaliacao() ?>">

    <table cellpadding="0" cellpadding="0" align="center" style="width:700px">
    <tr>
        <th>
            <? if(  $esta->get_avaliacao()->get_dt_conclusao()=="" && intval($esta->get_avaliacao()->get_cd_avaliacao())>0) { ?>
                <table cellpadding="0" cellspacing="0" style="width:690px" border="0">
                <tr>
                <td align="right" valign="center">
                    <!--
                    <input id="" 
                        name="" 
                        type="button" 
                        value="Salvar e continuar"
                        onclick="thisPage.save_by_comite_Click(this);"
                        command="save_and_continue" 
                        class="botao" 
                        />
					-->	
                    <a  href="javascript:void(0)"
                            id=""
							onclick="thisPage.save_by_comite_Click(this);"
							command="save_and_continue" 
                        ><img src="img/avaliacao_salvar_continuar.png" border="0" /></a>							
						
						&nbsp;
						
                    <a  href="javascript:void(0)"
						id="save_by_comite_button"
                        onclick="thisPage.save_by_comite_Click(this);"
                        command="save_and_send" 
                        ><img src="img/avaliacao_confirmar.png" border="0" /></a>							
						
					<!--	
						<input id="save_by_comite_button"
                        name="save_by_comite_button"
                        type="button"
                        value="Confirmar e encaminhar"
                        onclick="thisPage.save_by_comite_Click(this);"
                        command="save_and_send" 
                        class="botao" 
                        />
                    -->
                </td>
                </tr>
                </table>
            <? } ?>
        </th>
    </tr>


    <tr>
        <td>
            <table style="width:100%" cellpadding="0" cellpadding="0">

<!-- START QUADRO RESUMO -->

                      <tr> 
                        <td colspan="7" bgcolor="#F4F4F4"><table width="100%" border="0" cellspacing="2" cellpadding="3">
                            <tr> 
                              <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Avaliação Núm: </font></td>
                              <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong><?= $esta->get_avaliacao()->get_cd_avaliacao()?></strong></font></td>
                            </tr>
                            <tr> 
                              <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Avaliador:</font></td>
                              <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong><?= $esta->capa->avaliador->get_cd_registro_empregado() ?>
                                - <?= $esta->capa->avaliador->get_nome()?></strong></font></td>
                            </tr>
                            <tr> 
                              <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Avaliado:</font></td>
                              <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong><?= $esta->capa->avaliado->get_cd_registro_empregado()?>
                                - <?= $esta->capa->avaliado->get_nome()?></strong></font></td>
                            </tr>
                            <tr> 
                              <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Cargo:</font></td>
                              <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
                              <strong><?= $esta->capa->cargo->get_desc_cargo() ?></strong></font></td>
                            </tr>
                            <tr>
                              <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Fam&iacute;lia 
                                do cargo:</font></td>
                              <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong><?= $esta->capa->cargo->get_familia()->get_nome_familia()?></strong></font></td>
                            </tr>
                          </table></td>
                      </tr>
                      
                      <tr>
                        <td height="20px"></td>
                      </tr>
                      
                      <!-- START BLOCK : CI -->
                      <tr bgcolor="#BFA260"> 
                        <td valign="top"><strong><font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif">Compet&ecirc;ncias 
                          Institucionais </font></strong></td>
                        <td colspan="6" valign="top" align="right"><font color="#FFFFFF" size="3" face="Arial, Helvetica, sans-serif"><strong> 
                          <?/*= $esta->get_val_ci() */?></strong></font></td>
                      </tr>
                      <!-- END BLOCK : CI -->
                      
                      <tr bgcolor="#BFA260"> 
                        <td valign="top" bgcolor="#F4F4F4"></td>
                        <td align="center" class="font_table_title" title="<?= $esta->texto_conceito('CACI') ?>"><a class="font_table_title" href="javascript:void(0)" onclick="thisPage.load_mensagem_conceito( '<?= $esta->texto_conceito('CACI') ?>' );"><u>A</u></a></td>
                        <td align="center" class="font_table_title" title="<?= $esta->texto_conceito('CBCI') ?>"><a class="font_table_title" href="javascript:void(0)" onclick="thisPage.load_mensagem_conceito( '<?= $esta->texto_conceito('CBCI') ?>' );"><u>B</u></a></td>
                        <td align="center" class="font_table_title" title="<?= $esta->texto_conceito('CCCI') ?>"><a class="font_table_title" href="javascript:void(0)" onclick="thisPage.load_mensagem_conceito( '<?= $esta->texto_conceito('CCCI') ?>' );"><u>C</u></a></td>
                        <td align="center" class="font_table_title" title="<?= $esta->texto_conceito('CDCI') ?>"><a class="font_table_title" href="javascript:void(0)" onclick="thisPage.load_mensagem_conceito( '<?= $esta->texto_conceito('CDCI') ?>' );"><u>D</u></a></td>
                        <td align="center" class="font_table_title" title="<?= $esta->texto_conceito('CECI') ?>"><a class="font_table_title" href="javascript:void(0)" onclick="thisPage.load_mensagem_conceito( '<?= $esta->texto_conceito('CECI') ?>' );"><u>E</u></a></td>
                        <td align="center" class="font_table_title" title="<?= $esta->texto_conceito('CFCI') ?>"><a class="font_table_title" href="javascript:void(0)" onclick="thisPage.load_mensagem_conceito( '<?= $esta->texto_conceito('CFCI') ?>' );"><u>F</u></a></td>
                      </tr>
                      
                      <!-- START BLOCK : comp_inst -->
                      <? while ($rows_ci = pg_fetch_array($esta->get_result_comp_inst())) { ?>
                          <tr bgcolor="#F4F4F4" 
                            onmouseover="this.className='tb_resultado_selecionado';" 
                            onmouseout="this.className='';" 
                            > 
                            <td valign="top">
                                <a href="javascript:void(0);" 
                                    onclick="thisPage.load_mensagem( '1', '<?=$rows_ci["cd_comp_inst"]?>' )"
                                    ><img src="img/img_descricao.jpg" 
                                        border="0" 
                                        title="Clique para saber mais"
                                        ></a>
                                <font size="2" face="Verdana, Arial, Helvetica, sans-serif"><?=$rows_ci["nome_comp_inst"]?></font>
                            </td>
                            <td><input type="radio"
                                name="comp_inst<?=$rows_ci["cd_comp_inst"]?>"  
                                value="0"
                                <?= $esta->comp_inst_checked( $rows_ci["cd_comp_inst"], 0.0000 ) ?>
                                ></td>
                            <td><input type="radio" 
                                name="comp_inst<?=$rows_ci["cd_comp_inst"]?>" 
                                value="20" 
                                <?= $esta->comp_inst_checked( $rows_ci["cd_comp_inst"], 20.0000 ) ?> 
                                ></td>
                            <td><input type="radio" 
                                name="comp_inst<?=$rows_ci["cd_comp_inst"]?>" 
                                value="40" 
                                <?= $esta->comp_inst_checked( $rows_ci["cd_comp_inst"], 40.0000 ) ?>  
                                ></td>
                            <td><input type="radio" 
                                name="comp_inst<?=$rows_ci["cd_comp_inst"]?>" 
                                value="60" 
                                <?= $esta->comp_inst_checked( $rows_ci["cd_comp_inst"], 60.0000 ) ?> 
                                ></td>
                            <td><input type="radio" 
                                name="comp_inst<?=$rows_ci["cd_comp_inst"]?>" 
                                value="80" 
                                <?= $esta->comp_inst_checked( $rows_ci["cd_comp_inst"], 80.0000 ) ?>  
                                ></td>
                            <td><input type="radio" 
                                name="comp_inst<?=$rows_ci["cd_comp_inst"]?>" 
                                value="100" 
                                <?= $esta->comp_inst_checked( $rows_ci["cd_comp_inst"], 100.0000 ) ?>  
                                > 
                            </td>
                          </tr>
                      <? } ?>
                      <!-- END BLOCK : comp_inst -->

                      <tr>
                        <td height="20px"></td>
                      </tr>

                    </table></td>
                </tr>

<!-- END QUADRO RESUMO -->

            </table>

            <BR />

    <br>
    
    <? $thisPage = null; ?>