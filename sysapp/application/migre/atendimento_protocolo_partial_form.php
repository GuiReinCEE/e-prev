<? header("Content-Type: text/html; charset=iso-8859-1"); ?>
<?
    include_once('inc/sessao.php');
    include_once('inc/conexao.php');
    
    include_once('inc/ePrev.ADO.Projetos.atendimento_protocolo.php');
    
    include_once('inc/ePrev.Service.Public.php');
    include_once('inc/ePrev.Service.Projetos.php');

    include 'oo/start.php';
    using( array( 
    			  'projetos.atendimento_protocolo_tipo' 
    			, 'projetos.atendimento_protocolo_discriminacao' 
    			, 'projetos.atendimento_encaminhamento' 
    ) );

    class atendimento_protocolo_partial_form
    {
        private $db;
        private $entidade;
        private $command;
        private $cd_atendimento_protocolo;

        function atendimento_protocolo_partial_form( $_db )
        {
            $this->db = $_db;
            
            $this->entidade = new entity_projetos_atendimento_protocolo();
            
            $this->requestParams();
            
            if ($this->command=="load_participante_by_re") 
            {
				$this->loadParticipanteByRE();
			}
			if ($this->command=="carregar_atendimento_encaminhamento")
			{
				$emp = $_POST["emp"];
				$re = $_POST["re"];
				$seq = $_POST["seq"];

				$this->carregar_atendimento_encaminhamento($emp, $re, $seq);
				exit;
			}
			if ($this->command=="carregar_texto_encaminhamento")
			{
				$pk = explode(",", $_POST["pk_atendimento_encaminhamento"]);
				if( sizeof($pk)==2 && $pk[0]!="" && $pk[1]!="" )
				{
					$this->carregar_texto_encaminhamento($pk[0], $pk[1]);
				}
				else
				{
					echo "";
				}

				exit;
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
            $this->entidade->setdt_criacao(date("d/m/Y"));
            if (isset($_POST["IDText"]))
            {
                $this->entidade->setcd_atendimento_protocolo( $_POST["IDText"] );
			}
            if (isset($_REQUEST["id"]))
            {
                $this->cd_atendimento_protocolo = $_REQUEST["id"];
			}
            if (isset($_REQUEST["command"]))
            {
                $this->command = $_REQUEST["command"];	
			}
            if (isset($_POST["command"]) && $_POST["command"]!="")
            {
                $this->command = $_POST["command"];	
			}
        }

        function load()
        {
            $service = new service_projetos( $this->db );

            $this->entidade->setcd_atendimento_protocolo($this->cd_atendimento_protocolo);
            $service->correspondenciaGAP_LoadById( $this->entidade );

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

		public function carregar_atendimento_encaminhamento( $emp, $re, $seq, $cd_atendimento=0, $cd_encaminhamento=0 )
		{
			if($cd_atendimento=="")
			{
				$cd_atendimento = 0;
			}
			if($cd_encaminhamento=="")
			{
				$cd_encaminhamento = 0;
			}

			$collection = array();
			if($emp!="")
			{
				$collection = atendimento_encaminhamento::select_01( $emp, $re, $seq, $cd_atendimento, $cd_encaminhamento );
			}

			$output = "
			<select
                id='cd_atendimento_encaminhamento__select' 
                name='cd_atendimento_encaminhamento__select'
                class='passed'
                onBlur=''
                onkeypress='return thisPage.handleEnter(this, event);'
                onchange='carregar_texto_encaminhamento();'
                >
                <option value=''>::selecione::</option>
            ";

			foreach( $collection as $item )
			{
				if( $item["cd_atendimento"]==$cd_atendimento && $item["cd_encaminhamento"]==$cd_encaminhamento )
				{
					$selected = " SELECTED ";
				}
				else
				{
					$selected = "";
				}
				$output .= "<option $selected value='".$item["cd_atendimento"].",".$item["cd_encaminhamento"]."'>".$item["cd_atendimento"] . "/" . $item["cd_encaminhamento"] ."</option>";
			}

			$output .= "</select>";

			echo $output;
		}

		public function carregar_texto_encaminhamento($cd_atendimento="", $cd_encaminhamento="")
		{
			$collection = atendimento_encaminhamento::select_02( $cd_atendimento, $cd_encaminhamento );

			echo $collection[0]["texto_encaminhamento"];
		}

        public function getId()
        {
            return $this->cd_atendimento_protocolo;
        }

        public function getEntidade()
        {
            return $this->entidade;
        }

        public function dropdown_tipo()
        {
        	$col = new e_atendimento_protocolo_tipo_collection();
        	$col = atendimento_protocolo_tipo::select();
        	return $col;
        }

        public function dropdown_discriminacao()
        {
        	$col = atendimento_protocolo_discriminacao::select();
        	return $col;
        }
    }

    $thisPage = new atendimento_protocolo_partial_form($db);

    if ($thisPage->getCommand()=="load_participante_by_re") 
    {
        $thisPage = null;
		exit();
	}

	$dd_tipo = $thisPage->dropdown_tipo();
	$dd_discriminacao = $thisPage->dropdown_discriminacao();
?>

    <table cellpadding="0" cellpadding="0" align="center">
    <tr>
        <td>
            <table class="tb_cadastro_saida" style="width:100%" cellpadding="0" cellpadding="0" >
            <tr>
                <th colspan="2" align="right">
                    <? if($thisPage->getCommand()!="ver_detalhe") { ?>
                        <a href="javascript:void(0)"><img id="save_image" 
                                   src="img/btn_salvar.jpg" 
                                   border="0" 
                                   onclick="thisPage.save_Click(this);" 
                                   urlPartial="atendimento_protocolo_partial_form_save.php"
                                   contentPartial="message_panel"
                                   /></a>
                    <? } ?>
                </th>
            </tr>
            <tr style="display:none">
                <th>ID:</th>
                <td><input id="cd_atendimento_protocolo_text" 
                    name="cd_atendimento_protocolo_text" 
                    style="width:50px" 
                    title="Código" 
                    readonly
                    value="<?= $thisPage->getId() ?>" 
                    /></td>
            </tr>
            <tr>
                <th>EMP/RE/SEQ:</th>
                <td><input id="cd_empresa_text" 
                    name="cd_empresa_text" 
                    style="width:50px" 
                    title="Código da Empresa" class="normal"
                    onkeypress="mascara(this,soNumeros); return thisPage.handleEnter(this, event);"
                    urlPartial="atendimento_protocolo_partial_form.php"
                    args="command=load_participante_by_re"
                    emp="cd_empresa_text"
                    re="cd_registro_empregado_text"
                    seq="seq_dependencia_text"
                    onblur="thisPage.reComplete_Blur(this);"
                    value="<?= $thisPage->getEntidade()->getcd_empresa() ?>"
                    />
                <input id="cd_registro_empregado_text" 
                    name="cd_registro_empregado_text" 
                    style="width:70px" 
                    title="Registro do Empregado com dígito (apenas números)" 
                    class="normal" 
                    onkeypress="mascara(this,soNumeros); return thisPage.handleEnter(this, event);"
                    urlPartial="atendimento_protocolo_partial_form.php"
                    args="command=load_participante_by_re"
                    emp="cd_empresa_text"
                    re="cd_registro_empregado_text"
                    seq="seq_dependencia_text"
                    onblur="thisPage.reComplete_Blur(this);"
                    value="<?= $thisPage->getEntidade()->getcd_registro_empregado() ?>"
                    />
                <input id="seq_dependencia_text"
                    name="seq_dependencia_text"
                    style="width:50px"
                    title="Sequência de dependência"
                    class="normal"
                    onkeypress="mascara(this,soNumeros); return thisPage.handleEnter(this, event);"
                    urlPartial="atendimento_protocolo_partial_form.php"
                    args="command=load_participante_by_re"
                    emp="cd_empresa_text"
                    re="cd_registro_empregado_text"
                    seq="seq_dependencia_text"
                    onblur="thisPage.reComplete_Blur(this);"
                    value="<?= $thisPage->getEntidade()->getseq_dependencia() ?>"
                    /> </td>
            </tr>
            <tr>
                <th>Nome:</th>
                <td><input id="nome_participante_text" 
                        name="nome_participante_text"
                        class="required"
                        style="width:480px;"
                        value="<?= $thisPage->getEntidade()->get_nome() ?>"
                        onBlur="thisPage.input_Blur( this )"
                        maxlenght="250"
                        onkeypress="return thisPage.handleEnter(this, event);"
                        />
                        <div id="nome_participante_text_message" class="error" style="display:none">Campo obrigatório</div>
                </td>
            </tr>
            <tr>
                <th>Destino:</th>
                <td><input id="destino_text" 
                    name="destino_text" 
                    style="width:480px" 
                    maxlenght="255" 
                    class="normal"
                    value="<?= $thisPage->getEntidade()->getdestino() ?>"
                    onkeypress="return thisPage.handleEnter(this, event);"
                    /></td>
            </tr>
            <tr>
                <th>Protocolo/encaminhamento:</th>
                <td>
					<div id="cd_atendimento_encaminhamento__div">
                	<?php $thisPage->carregar_atendimento_encaminhamento( $thisPage->getEntidade()->getcd_empresa(), $thisPage->getEntidade()->getcd_registro_empregado(), $thisPage->getEntidade()->getseq_dependencia(), $thisPage->getEntidade()->getcd_atendimento(), $thisPage->getEntidade()->getcd_encaminhamento() ); ?>
                	</div>
                	<div id="texto_encaminhamento__div"><?php $thisPage->carregar_texto_encaminhamento($thisPage->getEntidade()->getcd_atendimento(), $thisPage->getEntidade()->getcd_encaminhamento()); ?></div>
				</td>
            </tr>
            <tr>
                <th>Tipo:</th>
                <td>

                	<select
                		id="cd_atendimento_protocolo_tipo__select" 
                		name="cd_atendimento_protocolo_tipo__select"
                		class="required"
                		onBlur="thisPage.input_Blur( this )"
                		onkeypress="return thisPage.handleEnter(this, event);"
                	>
                		<option value="">::selecione::</option>
	                    <? foreach( $dd_tipo->items as $item ) : ?>
                		<? $selected = ($thisPage->getEntidade()->getcd_atendimento_protocolo_tipo()==$item->cd_atendimento_protocolo_tipo)?'selected':''; ?>
                		
                			<option <?= $selected; ?> value="<?= $item->cd_atendimento_protocolo_tipo; ?>"><?= $item->nome; ?></option>
                			
                		<? endforeach; ?>
                	</select>
                    <div id="cd_atendimento_protocolo_tipo__select_message" class="error" style="display:none">Campo obrigatório</div>
                </td>
            </tr>
            <tr>
                <th>Discriminação:</th>
                <td>
                	<select 
                		id="cd_atendimento_protocolo_discriminacao__select" 
                		name="cd_atendimento_protocolo_discriminacao__select"
                		class="required"
                		onBlur="thisPage.input_Blur( this )"
                		onkeypress="return thisPage.handleEnter(this, event);"
                	>
                		<? foreach( $dd_discriminacao->items as $item ) : ?>
                		
		                    <? $selected = ($thisPage->getEntidade()->getcd_atendimento_protocolo_discriminacao()==$item->cd_atendimento_protocolo_discriminacao)?'selected':''; ?>
	                		<option <?= $selected; ?> value="<?= $item->cd_atendimento_protocolo_discriminacao; ?>"><?= $item->nome; ?></option>

	                    <? endforeach; ?>
                	</select><br />
                	<input id="identificacao_text" 
                        name="identificacao_text" 
                        style="width:480px" 
                        maxlenght="255" 
                        class="passed" 
                        value="<?= $thisPage->getEntidade()->getidentificacao() ?>"
                        onkeypress="return thisPage.handleEnter(this, event);"
                        />
                    <div id="cd_atendimento_protocolo_discriminacao__select_message" class="error" style="display:none">Campo obrigatório</div>
                </td>
            </tr>
            <tr>
                <th>Dt Remessa:</th>
                <td>
                    <input 
                        id="dt_criacao_text" 
                        name="dt_criacao_text" 
                        style="width:200px" 
                        maxlenght="255" 
                        readonly 
                        class="normal"
                        value="<?= $thisPage->getEntidade()->getdt_criacao() ?>"
                        onkeypress="return thisPage.handleEnter(this, event);" 
                        />
                </td>
            </tr>

            <? if( $thisPage->getId()!="" ) { ?>
                <tr>
                    <th>Recebido na GAD por:</th>
                    <td>
                        <input 
                            id="cd_usuario_recebimento_text" 
                            name="cd_usuario_recebimento_text" 
                            style="width:300px"
                            maxlenght="255"
                            readonly
                            class="normal"
                            value="<?= $thisPage->getEntidade()->getUsuarioRecebimento()->get_guerra() ?>"
                            onkeypress="return thisPage.handleEnter(this, event);" 
                            />
                    </td>
                </tr>
                <tr>
                    <th>Recebido na GAD em:</th>
                    <td>
                        <input 
                            id="dt_recebimento_text" 
                            name="dt_recebimento_text" 
                            style="width:300px" 
                            maxlenght="255" 
                            readonly 
                            class="normal"
                            value="<?= $thisPage->getEntidade()->getdt_recebimento() ?>"
                            onkeypress="return thisPage.handleEnter(this, event);" 
                            />
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
                            value="<?= $thisPage->getEntidade()->getdt_cancelamento() ?>"
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
                            value="<?= $thisPage->getEntidade()->getmotivo_cancelamento() ?>"
                            onkeypress="return thisPage.handleEnter(this, event);"
                            />
                    </td>
                </tr>
            <? } ?>

            <tr>
                <th colspan="2" align="right">
                    <? if($thisPage->getCommand()!="ver_detalhe") { ?>
                        <input
                        	type="button"
                        	onclick="thisPage.save_Click(this);"
                        	urlPartial="atendimento_protocolo_partial_form_save.php"
							contentPartial="message_panel"
							class="botao"
							value="Salvar"
							style="width:100px"
                        />
                    <? } ?>
                </th>
            </tr>

            <tr>
                <th colspan="2" align="right"><div id="message_panel"></div></th>
            </tr>
            </table>
        </td>
        <td align="center" valign="center"></td>
    </tr>
    </table>
    
    <? $thisPage = null; ?>