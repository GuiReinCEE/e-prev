<? header("Content-Type: text/html; charset=iso-8859-1"); ?>
<?
    include_once('inc/sessao.php');
    include_once('inc/conexao.php');

    include_once('inc/ePrev.Service.Projetos.php');

    class avaliacao_partial_form
    { #begin_class
        private $db;
        private $command;
        private $id;
        private $nome_usuario_logado;
        private $cd_usuario_logado;
        private $row;

        private $entidade;
        private $capas;
        public $Capa;

        function avaliacao_partial_form( $_db )
        {
            $this->db = $_db;
            $this->entidade = new entity_projetos_avaliacao();
            $this->Capa = new entity_projetos_avaliacao_capa_extended();
            
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

        public function get_command()
        {
            return $this->command;
        }

        function requestParams()
        {
            if (isset($_POST["cd_avaliacao_selected_hidden"]))
            {
                $this->id = $_POST["cd_avaliacao_selected_hidden"];
			}
            if (isset($_POST["ajax_command_hidden"]) && $_POST["ajax_command_hidden"]!='null' ) {
                $this->command = utf8_decode( $_POST["ajax_command_hidden"] );
			}
            $this->cd_usuario_logado = $_SESSION["Z"];
        }

        function load()
        {
            $service = new service_projetos( $this->db );
            $filtro = new entity_projetos_avaliacao_capa_extended();
            $filtro->set_cd_avaliacao_capa( $this->id );
            $this->capas = $service->avaliacao_capa_FetchAll( $filtro );
            
            $this->Capa = $this->capas[0];
            
            $this->capas = null;
            $filtro = null;
            $service = null;
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
        
        public function get_entidade()
        {
            return $this->entidade;
        }
        
        public function select_superiores_render()
        {
        	$superior = "0";
            $dal = new DBConnection();
            $dal->loadConnection( $this->db );
            $dal->createQuery( "
                SELECT tipo, divisao
                  FROM projetos.usuarios_controledi 
                 WHERE codigo = {codigo}
            " );
            $dal->setAttribute( "{codigo}", (int)$this->cd_usuario_logado );
            $result = $dal->getResultset();
            $row = pg_fetch_array($result);
            $tipo = $row["tipo"];

            if ($tipo=="U" || $tipo=="N")
            {
                $dal->createQuery( "
                    SELECT codigo, nome
                      FROM projetos.usuarios_controledi 
                     WHERE tipo = 'G'
                       AND divisao = '{divisao}'
                " );
                $dal->setAttribute( "{divisao}", $row["divisao"] );
                $result = $dal->getResultset();
                if($rows = pg_fetch_array($result))
                {
					$superior = $rows["codigo"];
				}

                $dal->createQuery( "
                    SELECT codigo, nome
                      FROM projetos.usuarios_controledi 
                     WHERE tipo = 'G'
                  ORDER BY nome ASC
                " );
                $result = $dal->getResultset();
                echo( "
                        <select id='cd_usuario_avaliador_select' 
                            name='cd_usuario_avaliador_select' 
                            class='required'
                            style='width:300px'
                            onblur='thisPage.input_Blur( this )'
                    	>" );
                echo("<option></option>");
                while($rows=pg_fetch_array($result))
                {
                    $selected = "";
                    if ($superior==$rows["codigo"])
                    {
                        $selected = "selected";
					}
                    echo("<option " . $selected . " value='" . $rows["codigo"] . "'>" . $rows["nome"] . "</option>");
                }
                echo( "</select>" );
                echo( "<div id='cd_usuario_avaliador_select_message' style='display:none' class='error'>Informe o avaliador</div>" );
                $rows = null;
			}
            else if($tipo=="G")
            {
                $dal->createQuery( "
                    SELECT DISTINCT u.codigo, u.nome
                      FROM projetos.usuarios_controledi u, projetos.divisoes d 
                     WHERE d.codigo = '{d.codigo}'
                       AND d.area = u.diretoria
                       AND u.tipo = 'D'
                " );
                $dal->setAttribute( "{d.codigo}", $row["divisao"] );
                $result = $dal->getResultset();
                $superior = "0";
                if($rows = pg_fetch_array($result))
                {
                    $superior = $rows["codigo"];
                }

                $dal->createQuery( "
                    SELECT DISTINCT u.codigo, u.nome
                      FROM projetos.usuarios_controledi u, projetos.divisoes d 
                     WHERE d.area = u.diretoria
                       AND u.tipo = 'D'
                  ORDER BY u.nome
                " );

                $result = $dal->getResultset();
                echo( "
                        <select id='cd_usuario_avaliador_select' 
                            name='cd_usuario_avaliador_select' 
                            class='required'
                            style='width:300px'
                            onblur='thisPage.input_Blur( this )'
                    >" );
                echo("<option></option>");
                while($rows=pg_fetch_array($result))
                {
                    $selected = "";
                    if ($superior==$rows["codigo"])
                    {
                        $selected = "selected";
                    }
                    echo("<option " . $selected . " value='" . $rows["codigo"] . "'>" . $rows["nome"] . "</option>");
                }
                echo( "</select>" );
                echo( "<div id='cd_usuario_avaliador_select_message' style='display:none' class='error'>Informe o avaliador</div>" );
            }

            return $superior;
        }

        public function allow_commands()
        {
            $status = $this->Capa->get_status();
            $cd_usuario_avaliado = $this->Capa->get_cd_usuario_avaliado();
            return ( ($status=="A" && $cd_usuario_avaliado==$this->cd_usuario_logado) || $this->id=="" );
        }

    } #end_class

    $thisPage = new avaliacao_partial_form( $db );

?>
    <input type="hidden" name="status_hidden" id="status_hidden" value="<?= $thisPage->Capa->get_status() ?>">
    <input type="hidden" name="status_original_hidden" id="status_original_hidden" value="<?= $thisPage->Capa->get_status() ?>">

    <table cellpadding="0" cellpadding="0" align="center" style="width:700px" style="">
    <tr>
        <th>
            <table cellpadding="0" cellspacing="0" style="width:690px" border="0">
            <tr>
            <td align="right" valign="center">
                <? if( $thisPage->allow_commands() ) { ?>
                    <? if( $thisPage->get_id()=="" ) { ?>
                    <a href="javascript:void(0)"><img id="save_image"
                               src="img/btn_salvar.jpg" 
                               border="0" 
                               onclick="thisPage.start_Click(this);" 
                               urlPartial="avaliacao_partial_form_save.php"
                               contentPartial="message_panel"
                               /></a>
                    <? } ?>
                    <?/*- if( $thisPage->get_id()!="" ) { */?>
                        <!--<a href='javascript:void(0)' onclick='thisPage.cancelar_Click(this);' registroId='<?/*= $thisPage->get_id() */?>' ><img src='img/btn_exclusao.jpg' border='0' title='Excluir Avaliação' /></a>-->
                    <?/* } */?>
                <? } ?>
            </td>
            </tr>
            </table>
        </th>
    </tr>
    <tr>
        <td>
            <table class="tb_cadastro_saida" style="width:100%" cellpadding="0" cellpadding="0">
            <tr style="display:none;">
                <th>ID:</th>
                <td>
                    <input type="text" 
                        id="cd_avaliacao_text" 
                        name="cd_avaliacao_text" 
                        class="passed"
                        value="<?= $thisPage->Capa->get_cd_avaliacao_capa() ?>" 
                        readonly
                        style="width:100px"
                        />
                </td>
            </tr>
            <tr>
                <th>Ano:</th>
                <td>
                    <input id="dt_periodo_text"
                        name="dt_periodo_text"
                        maxlenght="4"
                        class="required"
                        <? if( $thisPage->get_id()!="" ) { ?>
                            value="<?= $thisPage->Capa->get_dt_periodo() ?>"
                        <? } else { ?>
                            value="<?= date('Y') ?>"
                        <? } ?>
                        style="width:100px"
                        onBlur="thisPage.input_Blur( this )"
                        readonly="readonly"
                        <? if( $thisPage->get_id()!="" ) echo("disabled"); ?>
                        /> <div id="dt_periodo_text_message" style="display:none" class="error">Informe o ano da avaliação</div>
                </td>
            </tr>
            <tr>
                <th>Avaliador:</th>
                <td>
                    <?
                    if( $thisPage->get_id()!="" ) { ?>

                        <input class="required" value="<?= $thisPage->Capa->avaliador->get_nome() ?>" disabled="disabled" style="width:300px" />

                    <? } else { ?>
                    
                        <? $thisPage->select_superiores_render() ?>
                        
                    <? } ?>
                </td>
            </tr>
            <tr>
                <th>Avaliado:</th>
                <td>
                    <input id="nome_usuario_avaliado"
                        name="nome_usuario_avaliado"
                        maxlenght="100"
                        class="required"
                        <? if ($thisPage->get_id()=="") { ?>
                            value="<?= $thisPage->get_nome_usuario_logado() ?>"
                        <? } else { ?>
                            value="<?= $thisPage->Capa->avaliado->get_nome() ?>"
                        <? } ?>
                        style="width:300px"
                        readonly
                        <? if( $thisPage->get_id()!="" ) echo("disabled"); ?>
                        />
                </td>
            </tr>

            </table>

            <BR />

            <? if( $thisPage->allow_commands() ) { ?>
                <? if( $thisPage->get_id()=="" ) { ?>
                    <center><input 
                        type="button" 
                        value="Iniciar Avaliação"
                        style="width:200" 
                        onclick="thisPage.start_Click(this);"
                        class="botao"
                    /></center>
                <? } else { ?>
                    <center><input 
                        type="button" 
                        value="Continuar"
                        style="width:200"
                        onclick="thisPage.continue_Click(this);"
                        class="botao"
                    /><!--&nbsp<input 
                        type="button" 
                        value="Fechar e Encaminhar"
                        style="width:200" 
                        onclick="thisPage.close_and_send_Click(this);"
                        class="botao"
                    />--></center><br />
                <? } ?>
            <? } ?>

        </td>
        <td align="center" valign="center"></td>
    </tr>
    </table>

    <br>

    <div id="message_panel"></div>

    <? $thisPage = null; ?>