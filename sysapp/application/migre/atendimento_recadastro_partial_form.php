<?php
	header("Content-Type: text/html; charset=iso-8859-1");

    include_once('inc/sessao.php');
    include_once('inc/conexao.php');

    include_once('inc/ePrev.ADO.Projetos.atendimento_recadastro.php');

    include_once('inc/ePrev.Service.Public.php');
    include_once('inc/ePrev.Service.Projetos.php');

    class atendimento_recadastro_partial_form
    {
        private $db;
        private $entidade;
        private $command;
        private $cd_atendimento_recadastro;

        function atendimento_recadastro_partial_form( $_db )
        {
            $this->db = $_db;

            $this->entidade = new entity_projetos_atendimento_recadastro();

            $this->requestParams();

            if ($this->command=="load_participante_by_re") 
            {
				$this->loadParticipanteByRE();
			}
            if ($this->command=="ver_detalhe") 
            {
                $this->load();
			}
            if ($this->command=="editar") 
            {
                $this->load();
            }
        }

        function __destruct()
        {
            $this->db = null;
        }

        public function getCommand()
        {
            return $this->command;
        }

        function requestParams()
        {
            $this->entidade->dt_criacao = date("d/m/Y");
            if (isset($_POST["IDText"]))
            {
                $this->entidade->cd_atendimento_recadastro = $_POST["IDText"];
			}
            if (isset($_REQUEST["command"]))
            {
                $this->command = $_REQUEST["command"];	
			}
            if (isset($_REQUEST["id"]))
            {
                $this->cd_atendimento_recadastro = $_REQUEST["id"];
			}
        }

        function load()
        {
            $service = new service_projetos( $this->db );

            $this->entidade->cd_atendimento_recadastro = $this->cd_atendimento_recadastro;
            $service->atendimento_recadastro__LoadById( $this->entidade );

            $result = null;
            $service = null;

            return $result;
        }

        public function loadParticipanteByRE()
        {
            $service = new service_public($this->db);
            $entidade = new entity_participantes();

            $entidade->set_cd_empresa( $_REQUEST["emp"] );
            $entidade->set_cd_registro_empregado( $_REQUEST["re"] );
            $entidade->set_seq_dependencia( $_REQUEST["seq"] );
            $service->participantes_Load( $entidade );

            echo( $entidade->get_nome() );
            echo( "|" );
            echo( $entidade->get_logradouro() );
            echo( ", " ); 
            echo( $entidade->get_bairro() );
            echo( "," );
            echo( $entidade->get_cidade() );

            $entidade = null;
            $service = null;
        }

        public function getId()
        {
            return $this->cd_atendimento_recadastro;
        }

        public function getEntidade()
        {
            return $this->entidade;
        }
    }

    $thisPage = new atendimento_recadastro_partial_form($db);

    if ($thisPage->getCommand()=="load_participante_by_re") 
    {
        $thisPage = null;
		exit();
	}
?>

    <table cellpadding="0" cellpadding="0" align="center">
    <tr>
        <td>
            <table class="tb_cadastro_saida" style="width:100%" cellpadding="0" cellpadding="0" >

            <tr>
                <th colspan="2">

                    <? if($thisPage->getCommand()!="ver_detalhe") { ?>
                        <a href="javascript:thisPage.save_Click($('save_image'));"><img id="save_image" 
                           src="img/btn_salvar.jpg" 
                           border="0"
                           urlPartial="atendimento_recadastro_partial_form_save.php"
                           contentPartial="message_panel" 
                           /></a>
                    <? } ?>

                </th>
            </tr>

            <tr style="display:none;">
                <th>ID:</th>
                <td><input id="cd_atendimento_recadastro_text" 
                    name="cd_atendimento_recadastro_text" 
                    style="width:50px" 
                    title="Código" 
                    readonly
                    value="<?php echo $thisPage->getId(); ?>" 
                    /></td>
            </tr>
            <tr>
                <th>EMP/RE/SEQ:</th>
                <td><input id="cd_empresa_text" 
                    name="cd_empresa_text" 
                    style="width:50px" 
                    title="Código da Empresa" 
                    class="required"
                    onkeypress="mascara(this,soNumeros); return thisPage.handleEnter(this, event);"
                    urlPartial="atendimento_recadastro_partial_form.php"
                    args="command=load_participante_by_re"
                    emp="cd_empresa_text"
                    re="cd_registro_empregado_text"
                    seq="seq_dependencia_text"
                    onblur="thisPage.reComplete_Blur(this);thisPage.input_Blur(this);"
                    value="<?= $thisPage->getEntidade()->cd_empresa ?>"
                    />
                <input id="cd_registro_empregado_text" 
                    name="cd_registro_empregado_text" 
                    style="width:70px" 
                    title="Registro do Empregado com dígito (apenas números)" 
                    class="required" 
                    onkeypress="mascara(this,soNumeros); return thisPage.handleEnter(this, event);"
                    urlPartial="atendimento_recadastro_partial_form.php"
                    args="command=load_participante_by_re"
                    emp="cd_empresa_text"
                    re="cd_registro_empregado_text"
                    seq="seq_dependencia_text"
                    onblur="thisPage.reComplete_Blur(this);thisPage.input_Blur(this);"
                    value="<?= $thisPage->getEntidade()->cd_registro_empregado ?>"
                    />
                <input id="seq_dependencia_text"
                    name="seq_dependencia_text"
                    style="width:50px"
                    title="Sequência de dependência"
                    class="required"
                    onkeypress="mascara(this,soNumeros); return thisPage.handleEnter(this, event);"
                    urlPartial="atendimento_recadastro_partial_form.php"
                    args="command=load_participante_by_re"
                    emp="cd_empresa_text"
                    re="cd_registro_empregado_text"
                    seq="seq_dependencia_text"
                    onblur="thisPage.reComplete_Blur(this);thisPage.input_Blur(this);"
                    value="<?= $thisPage->getEntidade()->seq_dependencia ?>"
                    />
                    <div id="cd_empresa_text_message" class="error" style="display:none">Emp obrigatória</div>
                    <div id="cd_registro_empregado_text_message" class="error" style="display:none">RE obrigatório</div>
                    <div id="seq_dependencia_text_message" class="error" style="display:none">Seq obrigatório</div>
                    </td>
            </tr>
            <tr>
                <th>Nome:</th>
                <td><input id="nome_participante_text" 
                        name="nome_participante_text"
                        class="required"
                        style="width:480px;"
                        value="<?= $thisPage->getEntidade()->nome ?>"
                        onBlur="thisPage.input_Blur( this )"
                        maxlenght="250"
                        readonly
                        onkeypress="return thisPage.handleEnter(this, event);"
                        />
                        <div id="nome_participante_text_message" class="error" style="display:none">Campo obrigatório</div>
                </td>
            </tr>
            <tr>
                <th>Dt Criação:</th>
                <td>
                    <input 
                        id="dt_criacao_text" 
                        name="dt_criacao_text" 
                        style="width:200px" 
                        maxlenght="255" 
                        readonly 
                        class="normal"
                        value="<?= $thisPage->getEntidade()->dt_criacao ?>"
                        onkeypress="return thisPage.handleEnter(this, event);"
                        />
                </td>
            </tr>
            <tr>
                <th>Observações:</th>
                <td>
                	<textarea id="observacao_text" name="observacao_text" class="normal" style="width:480px;height:50px;"><?= $thisPage->getEntidade()->observacao ?></textarea>
                </td>
            </tr>
            <tr>
                <th>Serviço Social:</th>
                <td>
                	<textarea id="servico_social_text" name="servico_social_text" class="normal" style="width:480px;height:50px;"><?= $thisPage->getEntidade()->servico_social ?></textarea>
                </td>
            </tr>
            <tr>
                <th>Ano:</th>
                <td>
                    <input id="dt_periodo_text"
                        name="dt_periodo_text"
                        maxlenght="4"
                        class="required"
                        <? if( $thisPage->getId()!="" ) { ?>
                            value="<?= $thisPage->getEntidade()->dt_periodo; ?>"
                        <? } else { ?>
                            value="<?= date('Y') ?>"
                        <? } ?>
                        style="width:100px"
                        onBlur="thisPage.input_Blur( this )"
                        readonly="readonly"
                        /> <div id="dt_periodo_text_message" style="display:none" class="error">Informe o ano do recadastro.</div>
                </td>
            </tr>

            <? if( $thisPage->getId()!="" ) { ?>
            	
            	<tr>
                    <th>Atualizado em:</th>
                    <td>
                        <?php
                        if($thisPage->getEntidade()->dt_atualizacao!='')
                        {
                        	echo $thisPage->getEntidade()->dt_atualizacao . ' por ' . $thisPage->getEntidade()->nome_usuario_atualizacao;
                        }
                        ?>
                        	
                    </td>
                </tr>
            	
                <tr>
                    <th>Cancelado em:</th>
                    <td>
                        <input 
                            id="dt_cancelamento_text" 
                            name="dt_cancelamento_text" 
                            style="width:300px" 
                            maxlenght="255" 
                            readonly 
                            class="normal"
                            value="<?= $thisPage->getEntidade()->dt_cancelamento ?>"
                            onkeypress="return thisPage.handleEnter(this, event);" 
                            />
                    </td>
                </tr>
                <tr>
                    <th>Motivo Cancelamento:</th>
                    <td>
                        <input 
                            id="motivo_cancelamento_text"
                            name="motivo_cancelamento_text"
                            style="width:300px"
                            maxlenght="255"
                            readonly
                            class="normal"
                            value="<?= $thisPage->getEntidade()->motivo_cancelamento ?>"
                            onkeypress="return thisPage.handleEnter(this, event);"
                            />
                    </td>
                </tr>
                
            <? } ?>

            <tr>
                <th colspan="2" align="right"><div id="message_panel"></div></th>
            </tr>

            <tr>
                <th colspan="2" align="right">

                    <? if($thisPage->getCommand()!="ver_detalhe") { ?>
                        <input type="button"
                        	id="salvar_button"
                        	name="salvar_button"
                        	class="botao"
                        	value="Salvar"
							urlPartial="atendimento_recadastro_partial_form_save.php"
							contentPartial="message_panel"
							style="width:100px;height:20px;font-weight:bold;"
							onclick="thisPage.save_Click($('save_image'));"
                         />
                    <? } ?>

                </th>
            </tr>
            </table>
        </td>
        <td align="center" valign="center"></td>
    </tr>
    </table>

<?
$thisPage = null;
?>