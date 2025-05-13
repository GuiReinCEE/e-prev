<? header("Content-Type: text/html; charset=iso-8859-1"); ?>
<?
    include_once('inc/sessao.php');
    include_once('inc/conexao.php');

    include_once('inc/ePrev.ADO.tipo_documentos.php');
    include_once('inc/ePrev.ADO.Projetos.documento_protocolo.php');

    include_once('inc/ePrev.Service.Public.php');
    include_once('inc/ePrev.Service.Projetos.php');

    include 'oo/start.php';

    using( array(
	    'projetos.documento_recebido'
	    , 'projetos.documento_recebido_item'
	    , 'projetos.usuarios_controledi'
	    , 'projetos.divisoes'
    ) );

    class documento_recebido_partial_form
    { 
    	#begin_class
        private $db;
        private $command;
        private $cd_documento_recebido;
        private $filtro_documento;
        public $entidade;

        function __construct( $_db )
        {
            $this->db = $_db;

            $this->requestParams();
            if ($this->command=="load_participante_by_re") 
            {
				$this->loadParticipanteByRE();
			}
            if ($this->command=="load_tipo_documento") 
            {
                $this->tipo_documentos_Load();
            }
            if ($this->command=="ver_detalhe") 
            {
                $this->load();
			}
            if ($this->command=="editar") 
            {
                $this->load();
            }
            if ($this->command=="load_filtro_documentos") 
            {
                $this->render_filtro_documentos();
            }
            if ($this->command=="update_ordem_itens") 
            {
                $this->update_ordem_itens();
            }
            if ($this->command=="carregar_usuario") 
            {
                $this->carregar_usuario();
            }
            if ($this->command=="receber_protocolo") 
            {
                $this->receber_protocolo();
            }
            if ($this->command=="redirecionar_protocolo") 
            {
                $this->redirecionar_protocolo();
            }
        }

        function __destruct()
        {
            $this->db = null;
        }

        function carregar_usuario()
        {
        	$collection = t_usuarios_controledi::select_1( $_POST['gerencia'] );

        	$output = "<select id='cd_usuario_destino' name='cd_usuario_destino'>";
        	foreach( $collection as $item )
        	{
	        	$output .= "<option value='".$item['codigo']."'>".$item['nome']."</option>\n";
        	}
        	$output .= "</select>";

        	echo $output;
        }

        function receber_protocolo()
        {
        	$ret = documento_recebido::receber_protocolo( (int)$_POST['cd_documento_recebido'], (int)$_SESSION['Z'], utf8_decode($_POST['observacao_recebimento']) );

        	if($ret==true) echo "1";
        }
        function redirecionar_protocolo()
        {
        	$ret = documento_recebido::redirecionar_protocolo( (int)$_POST['cd_documento_recebido'], (int)$_POST['cd_usuario_destino'] );

        	if($ret==true) echo "1";
        }

        public function getCommand()
        {
            return $this->command;
        }

        function requestParams()
        {
            if (isset($_POST["IDText"])) {
                $this->entidade['cd_documento_recebido']=(int)$_POST["IDText"];
			}
            if (isset($_POST["cd_comando_text"])) {
                $this->entidade['cd_documento_recebido'] =(int)$_POST["cd_comando_text"];
			}
            if (isset($_REQUEST["command"]))
            {
                $this->command = $_REQUEST["command"];
			}
            if (isset($_REQUEST["id"])) {
                $this->cd_documento_recebido = (int)$_REQUEST["id"];
			}
            if (isset($_POST["filtro_nome_documento_text"])) {
                $this->filtro_documento = utf8_decode( $_POST["filtro_nome_documento_text"] );
			}
        }

        function load()
        {
            $item = documento_recebido::carregar( (int)$this->cd_documento_recebido );
            $this->entidade = $item;
        }

        public function loadParticipanteByRE()
        {
            $service = new service_public($this->db);
            echo(
                  $service->participantes_LoadByRE( 
                                                    $_REQUEST["emp"]
                                                  , $_REQUEST["re"]
                                                  , $_REQUEST["seq"] 
                                                  ) 
                );
            $service = null;
        }

        public function tipo_documentos_Load()
        {
            $service = new service_public($this->db);
            echo( 
                  $service->tipo_documentos_Load( $_REQUEST["cd_tipo_doc"] ) 
                );
            $service = null;
        }

        public function getId()
        {
            return $this->cd_documento_recebido;
        }

        public function getItens()
        {
            $result = null;
            if( $this->entidade['cd_documento_recebido'] )
            {
                $result = documento_recebido_item::select_1( $this->entidade['cd_documento_recebido'] );
            }
            return $result;
        }

        public function render_filtro_documentos()
        {
            // Documentos para lista
            $service = new service_public( $this->db );
            $filtro = $this->filtro_documento;

            $result = $service->tipo_documentos_FetchAll( $filtro );

            $service = null;
            echo( '                         <table bgcolor="white" width="100%" class="tb_lista_resultado">' . "\n"  );
            echo( '                            <tr>' . "\n"  );
            echo( '                                <th><b>Código</b></td>' . "\n"  );
            echo( '                                <th><b>Documento</b></td>' . "\n"  );
            echo( '                            </tr>' . "\n"  );
            $bgcolor = '';
            while( $docs = pg_fetch_array( $result ) ) 
            {
                $bgcolor = ($bgcolor=="#ffffff")?"#f4f4f4":"#ffffff";
                echo( '                         <tr bgcolor="'.$bgcolor.'">' . "\n" );
                echo( '                             <td class="td_border"><a href="javascript:void(0)">'. $docs["cd_tipo_doc"] . '</a></td>' . "\n"  );    
                echo( '                             <td class="td_border"><a href="javascript:void(0)" onclick="thisPage.select_documento(' . $docs["cd_tipo_doc"] . ', \'' . addslashes( str_replace("\"", "", $docs["nome_documento"]) ) . '\')">' . addslashes(  str_replace("\"", "", $docs["nome_documento"])  ) . '</a></td>' . "\n"  );
                echo( '                         </tr>' . "\n"  );
            }
            echo( '                         </table>' );
            $result = null;
        }

        private function update_ordem_itens()
        {
            $service = new service_projetos( $this->db );
            $service->documento_recebido_UpdateOrdem( $this->entidade );
            $service = null;

            $this->render_filtro_documentos();
        }

        public function lista_documento_render($itens)
        {
        	echo "
		    	<table align='center' class='tb_lista_resultado'>
				<tr>
					<th align='center'>Participante</th>												
					<th align='center'>Documento</th>												
					<th align='center'>Criador</th>
					<th align='center'>Data</th>
					<th align='center'>Observação</th>
					<th align='center'>Folhas</th>
					<th align='center'>Arquivo</th>
					<th align='center'></th>
				</tr>
			";

			foreach( $itens as $item )
			{
				$bgcolor = ($bgcolor=="#ffffff")?"#eeeeee":"#ffffff";

				if( $item['arquivo']!='' )
				{
					$arquivo = "<a href='".base_url()."up/documento_recebido/".$item['arquivo']."' target='_blank'>ver arquivo</a>";
				}
				else
				{
					$arquivo='';
				}

				if( intval($item['cd_empresa'].$item['cd_registro_empregado'].$item['seq_dependencia'])==0 )
				{
					$participante=$item['nome'];
				}
				else
				{
					$participante=$item['cd_empresa'].'/'.$item['cd_registro_empregado'].'/'.$item['seq_dependencia'];
				}

				echo "
	                <tr bgcolor='" . $bgcolor . "'>
	                <td>" . $participante . "</td>
	                <td>" . $item['nome_documento'] . "</td>
	                <td>" . $item['criador'] . "</td>
	                <td>" . $item['dt_cadastro'] . "</td>
	                <td>" . $item['observacao'] . "</td>
	                <td>" . $item['nr_folha'] . "</td>
	                <td>" . $arquivo . "</td>
	                <td>
	            ";

				if( $this->entidade['dt_ok']=="" )
				{
					echo "
						<table cellpadding='0' cellspacing='0' border='0'>
	                    <tr>

	                        <td style='border: 0px solid #CCCCCC;'>
	                            <a href='javascript:void(0)'
	                                onclick='thisPage.excluir_item_Click(this);'
	                                registroId='".$item['cd_documento_recebido_item']."'
	                                ><img src='img/btn_deletar.gif'
										border='0'
										title='Excluir o documento'
										/></a>
	                        </td>

	                    </tr>
	                    </table>
	                ";
				}

	            echo "
	                </td>
	                </tr>
				";
			}

			echo "    
	            </table>
	        ";
			
			echo("<input type='hidden' value='".sizeof($itens)."' id='count_docs' />");
			
        }
    } #end_class

    $thisPage = new documento_recebido_partial_form( $db );

    if ($thisPage->getCommand()=="load_participante_by_re") 
    {
        $thisPage = null;
		exit();
	}
    if ($thisPage->getCommand()=="load_tipo_documento") 
    {
        $thisPage = null;
		exit();
	}
    if ($thisPage->getCommand()=="load_filtro_documentos") 
    {
        $thisPage = null;
		exit();
	}
    if ($thisPage->getCommand()=="update_ordem_itens") 
    {
        $thisPage = null;
		exit();
	}
    
	if ($thisPage->getCommand()=="carregar_usuario") 
    {
        $thisPage = null;
		exit();
	}
	if ($thisPage->getCommand()=="receber_protocolo") 
    {
        $thisPage = null;
		exit();
	}
	if ($thisPage->getCommand()=="redirecionar_protocolo") 
    {
        $thisPage = null;
		exit();
	}

	// Itens
    $itens = $thisPage->getItens();

    if ($thisPage->getCommand()=="load_list_only") 
    {
		$thisPage->lista_documento_render($itens);
        $thisPage = null;
        exit();
    }
?>
    <center>
    <table cellpadding="0" cellspacing="0" style="width:690px" border="0">
    <tr>
    <td align="right" valign="middle">
    
        <? if($thisPage->entidade['cd_documento_recebido']!="") { ?>

            <? if( $thisPage->entidade['dt_envio']=="" ) { ?>
                <!-- <a href='javascript:void(0);'
                    id="enviar_protocolo_interna_img"
                    onClick='escolher_usuario(false);' 
                    ><img 
                        width="27px"
                        src='img/btn_documento_enviar.png' 
                        border='0'
                        title='Enviar protocolo'
                        /></a> -->

                <!-- <a href='javascript:void(0);'
                    id="enviar_protocolo_interna_img"
                    onClick='thisPage.enviar_Click(this);' 
                    urlPartial='documento_recebido_partial_form_send.php'
                    registroId='<?php echo $thisPage->entidade['cd_documento_recebido']; ?>'
                    ><img 
                        width="27px"
                        src='img/btn_documento_enviar.png' 
                        border='0'
                        title='Enviar protocolo'
                        /></a> -->
            <? } ?>

            <?php /* if( $thisPage->entidade['dt_ok']=="" && $thisPage->entidade['dt_exclusao']=="" ) : */ ?>

                <!-- <a href='javascript:void(0)' 
                    onclick='thisPage.cancelar_Click(this);'
                    urlPartial="documento_recebido_partial_form_cancel.php" 
                    registroId='<?php /*echo $thisPage->entidade['cd_documento_recebido'];*/ ?>'
                    ><img src='img/btn_exclusao.jpg' 
                    border='0'  
                    title='Excluir protocolo de documentos' 
                    /></a> -->

            <?php /* endif; */ ?>

        <? } ?>

    </td>
    </tr>
    </table>
    </center>

    <div id="usuario_div" style="display:none;">
    	<table align="center">
    	<tr>
    	<td>
    	Gerências:</td><td>
    	<?php $collection = divisoes::select_1(); ?>
    	<select id="gerencia_select" name="gerencia_select" onchange="carregar_usuarios();">
    		<option value="">::selecione::</option>
    		<?php foreach($collection as $item): ?>
    		<option value="<?php echo $item['codigo']; ?>"><?php echo $item['codigo']; ?></option>
    		<?php endforeach; ?>
    	</select></td>
    	</tr>
    	<tr><td>Usuários:</td>
    	<td>
    	<div id="usuario_select_div"></div>
    	</td>
    	</tr>
    	<tr><td></td>
    	<td>
    	<input 
    		id="enviar_protocolo_button"
    		type="button"
    		class="botao" 
    		value="Enviar" 
    		onClick='thisPage.enviar_Click(this);' 
			urlPartial='documento_recebido_partial_form_send.php'
			registroId="<?php echo $thisPage->entidade['cd_documento_recebido']; ?>"
			style="display:none;" 
    	/>
    	<input 
    		id="redirecionar_protocolo_button"
    		type="button"
    		class="botao" 
    		value="Redirecionar" 
    		onClick='redirecionar_protocolo( "<?php echo $thisPage->entidade['cd_documento_recebido']; ?>" );' 
			style="display:none;"
    	/>
    	</td></tr></table>
    </div>

    <table id="documento_table" cellpadding="0" cellpadding="0" align="center" style="width:700px">
    <tr>
        <td>
            <table class="tb_cadastro_saida" style="width:100%" cellpadding="0" cellpadding="0">
            <tr>
                <th colspan="2" align="right">
                    <? if($thisPage->getCommand()!="ver_detalhe"): ?>
						<!--
						<a href="javascript:void(0)"><img id="save_image"
						src="img/btn_salvar.jpg" 
						border="0" 
						onclick="thisPage.save_Click(this);" 
						urlPartial="documento_recebido_partial_form_save.php"
						contentPartial="message_panel"
						/></a>
						-->
                    <? endif; ?>
                </th>
            </tr>
            <tr style="display:">
                <th>ID:</th>
                <td><input id="cd_documento_recebido_text" 
                    name="cd_documento_recebido_text" 
                    style="width:50px" 
                    title="Código" 
                    class="passed"
                    readonly
                    value="<?php echo $thisPage->entidade['cd_documento_recebido']; ?>" 
                    /></td>
            </tr>
            <tr>
                <th>Ano/Seq:</th>
                <td>
                	<input id="ano_text" 
                        name="ano_text" 
                        style="width:50px" 
                        maxlenght="255" 
                        class="normal"
                        value="<?php echo $thisPage->entidade['nr_ano']; ?>"
                        readonly
                        />
                    <input id="contador_text" 
                        name="contador_text" 
                        style="width:50px" 
                        maxlenght="255" 
                        class="normal"
                        value="<?php echo $thisPage->entidade['nr_contador']; ?>"
                        readonly
                        /> (gerado automaticamente)
				</td>
            </tr>
            <tr>
                <th>Tipo de protocolo</th>
                <td>

                    <?php if($thisPage->getId()!="") echo $thisPage->entidade['ds_tipo']; ?>

                    <?php if($thisPage->getId()==""): ?> 
	                	<SELECT id="cd_documento_recebido_tipo" name="cd_documento_recebido_tipo">
		                	<option value="">::Selecione::</option>
		                	<option value="<?php echo enum_projetos_documento_recebido_tipo::CENTRAL_ATENDIMENTO ?>">Central de Atendimento</option>
		                	<option value="<?php echo enum_projetos_documento_recebido_tipo::FAX ?>">Fax</option>
		                	<option value="<?php echo enum_projetos_documento_recebido_tipo::MALOTE ?>">Malote</option>
		                	<option value="<?php echo enum_projetos_documento_recebido_tipo::EMAIL ?>">Email</option>
	                	</SELECT>
                	<?php endif; ?>
                </td>
            </tr>

            <? if($thisPage->getId()!=""): ?>
                <tr>
                    <th>Dt Cadastro:</th>
                    <td><?php echo $thisPage->entidade['dt_cadastro']; ?></td>
                </tr>
                <tr>
                    <th>Cadastrado por:</th>
                    <td>
                        <?php echo $thisPage->entidade['guerra_usuario_cadastro']; ?>
                    </td>
                </tr>

                <tr>
                    <th>Dt Envio:</th>
                    <td>
                        <?php echo $thisPage->entidade['dt_envio']; ?>
                    </td>
                </tr>

                <tr>
                    <th>Enviado por:</th>
                    <td>
                        <?php echo $thisPage->entidade['guerra_usuario_envio']; ?>
                    </td>
                </tr>
                <!-- <tr>
                    <th>Dt Confirmação:</th>
                    <td><?php echo $thisPage->entidade['dt_ok']; ?></td>
                </tr>
                <tr>
                    <th>Confirmado por:</th>
                    <td><?= $thisPage->entidade['guerra_usuario_ok']; ?></td>
                </tr> -->
                <tr>
                    <th>Destino:</th>
                    <td><?= $thisPage->entidade['guerra_usuario_destino']; ?></td>
                </tr>

            <? endif; ?>
            
            <tr>
                <th colspan="2" align="right"><div id="message_panel"></div></th>
            </tr>
            </table>
            <BR />

            <!-- BEGIN : LISTA DE DOCUMENTO DO PROTOCOLO  -->
            <? if($thisPage->getId()=="") { ?>
                <center><div id="novo_protocolo_div"><br><br><input class="botao" type="button" 
                    id="novo_protocolo_button" 
                    name="novo_protocolo_button" 
                    value="Novo protocolo"
                    onclick="thisPage.novo_protocolo_Click(this);"
                    style="height:50px; width:300px" 
                    /></div></center>
            <? } ?>
            <table 
                id="documentos_table" 
                cellpadding="0" 
                cellpadding="0" 
                align="center" 
                <? if($thisPage->getId()=="") { ?>
                    style="width:100%;display:NONE;"
                <? } ?>
                >
            <tr>
                <td>

		            <?php if($_SESSION['Z']==$thisPage->entidade['cd_usuario_destino'] && $thisPage->entidade['dt_ok']=='' && $thisPage->entidade['dt_redirecionamento']=='' ) : ?>
		                <center>
		                <input value="Receber" type="button" id="receber_button" name="receber_button" class="botao" onclick="receber_protocolo( '<?php echo $thisPage->entidade['cd_documento_recebido'] ?>' );" />
		                <input value="Redirecionar" type="button" id="redirecionar_button" name="redirecionar_button" class="botao" onclick="redirecionar();" />
		                </center><br />
					<?php endif; ?>

	                <? if( $thisPage->entidade['dt_envio']=="" ) { ?>
	                	<center><input type="button" class="botao" id="enviar_protocolo_interna_img" onClick='escolher_usuario(false);' value="Enviar o protocolo" /></center>
	                	<br />
		            <? } ?>

                    <table class="tb_cadastro_saida" style="width:100%" cellpadding="0" cellpadding="0">

                        <tr>
                            <th colspan="2" align="right">
                                <?php if( $thisPage->entidade['dt_envio']=="" ) : ?>
                                    Adicionar Documento ao Protocolo
                                <?php else : ?>
                                    Documentos do Protocolo
                                <?php endif; ?>
                            </th>
                        </tr>

                        <tr <? if( $thisPage->entidade['dt_envio']!="" ) { echo( "style='display:none'" ); } ?>>
                            <th>Documento: </th>
                            <td><input id='tipo_doc_radio' 
                                    name='preferencia_radio' 
                                    type='radio'
                                    onclick="$('item_cd_tipo_doc_text').focus();" 
                                    />
                                <input id="item_cd_tipo_doc_text"
                                    name="item_cd_tipo_doc_text"
                                    style="width:50px"
                                    maxlenght="50"
                                    class="passed"
                                    value=""
                                    loadContent="nome_documento_text"
                                    urlPartial="documento_recebido_partial_form.php"
                                    args="command=load_tipo_documento"
                                    onblur="thisPage.tipo_documento_Blur(this);"
                                    onkeypress="mascara(this,soNumeros); return thisPage.test_enter(this, event);"
                                /><a href="javascript:void(0);" 
                                    onclick="thisPage.consultar_documentos_Click( this )"
                                    extra="show_panel"
                                    ><img src="img/img_lente_peq.gif" 
                                    id="buscar_documento_button"
                                    border="0" 
                                    width="16px"
                                    name="buscar_documento_button" 
                                /></a> <input id="nome_documento_text" 
                                        style="width:412px" 
                                        readonly 
                                        class="passed" 
                                        value="" 
                                /><div id="item_cd_tipo_doc_text_message" class="error" style="display:none">Campo obrigatório</div></td>
                        </tr>
                        <tr <? if( $thisPage->entidade['dt_envio']!="" ) { echo( "style='display:none'" ); } ?>>
                            <th nowrap>EMP/RE/SEQ/NOME:</th>
                            <td><input id='re_radio' 
                                    name='preferencia_radio' 
                                    type='radio'
                                    onclick="$('item_cd_empresa_text').focus();" 
                                    />
                                <input id="item_cd_empresa_text" 
	                                name="item_cd_empresa_text" 
	                                style="width:50px" 
	                                title="Código da Empresa" 
	                                onkeypress="mascara(this,soNumeros); return thisPage.handleEnter(this, event);"
	                                urlPartial="documento_recebido_partial_form.php"
	                                args="command=load_participante_by_re"
	                                loadContent="nome_participante_text"
	                                emp="item_cd_empresa_text"
	                                re="item_cd_registro_empregado_text"
	                                seq="item_seq_dependencia_text"
	                                onblur="thisPage.reComplete_Blur(this);"
	                                value=""
									class='normal'
	                                />
                            <input id="item_cd_registro_empregado_text" 
                                name="item_cd_registro_empregado_text" 
                                style="width:70px" 
                                title="Registro do Empregado com dígito (apenas números)" 
                                onkeypress="mascara(this,soNumeros); return thisPage.handleEnter(this, event);"
                                urlPartial="documento_recebido_partial_form.php"
                                args="command=load_participante_by_re"
                                loadContent="nome_participante_text"
                                emp="item_cd_empresa_text"
                                re="item_cd_registro_empregado_text"
                                seq="item_seq_dependencia_text"
                                onblur="thisPage.reComplete_Blur(this);"
                                value=""
								class='normal'
                                />
                            <input id="item_seq_dependencia_text"
                                name="item_seq_dependencia_text"
                                style="width:50px"
                                title="Sequência de dependência"
                                onkeypress="mascara(this,soNumeros); return thisPage.test_enter(this, event);"
                                urlPartial="documento_recebido_partial_form.php"
                                args="command=load_participante_by_re"
                                loadContent="nome_participante_text"
                                emp="item_cd_empresa_text"
                                re="item_cd_registro_empregado_text"
                                seq="item_seq_dependencia_text"
                                onblur="thisPage.reComplete_Blur(this);"
                                value=""
								class='normal'
                                /> <input title='Nome do participante' id="nome_participante_text" name="nome_participante_text" style="width:300px" class="normal" value="" /></td>
                        </tr>
                        <tr <? if( $thisPage->entidade['dt_envio']!="" ) { echo( "style='display:none'" ); } ?>>
                            <th>Observações:</th>
                            <td>
                            	<input id="item_observacao" 
	                                name="item_observacao" 
	                                style="width:507px" 
	                                title="Observação" 
	                                value=""
	                                class="passed"
	                                onkeypress="return thisPage.test_enter(this, event);"
	                                maxlength="100"
                                />

                            </td>
                        </tr>
                        <tr <? if( $thisPage->entidade['dt_envio']!="" ) { echo( "style='display:none'" ); } ?>>
                            <th>Nº de Folhas:</th>
                            <td>
                            	<input id="item_nr_folha" 
	                                name="item_nr_folha" 
	                                style="width:100px" 
	                                title="Nº de Folhas" 
	                                value="1"
	                                class="passed"
	                                onkeypress="mascara(this,soNumeros);return thisPage.test_enter(this, event);"
	                                maxlength="5"
                                />
                                
                            </td>
                        </tr>

						<tr<? if( $thisPage->entidade['dt_envio']!="" ) { echo( " style='display:none'" ); } ?>>
                            <th>Arquivo: (.pdf)</th>
                            <td>
								<INPUT TYPE="hidden" NAME="item_arquivo" ID="item_arquivo">
								<INPUT TYPE="hidden" NAME="item_arquivo_nome" ID="item_arquivo_nome">
								<div id='arquivo_div'></div>
								<div id='arquivo_upload_div'>
									<SPAN id='resetar_file'><INPUT TYPE="file" NAME="arquivo" id='arquivo' SIZE='40'></SPAN>
									<INPUT CLASS='botao' TYPE="button" VALUE="ANEXAR" ONCLICK='enviar_arquivo(this.form, "<?php echo base_url(); ?>index.php/geral/upload/arquivo/documento_recebido/sucesso/falha")'>
								</div>
								<IFRAME NAME='upload_iframe' style='display:none;'></IFRAME>
                            </td>
                        </tr>
        
                        <tr>
                            <th colspan="2" align="right">
                                <br>
                                <? if( $thisPage->entidade['dt_envio']=="" ) : ?>
                                    <input type="button"
                                    	class="botao" 
                                        id="adicionar_item_button"
                                        name="adicionar_item_button"
                                        value="Adicionar documento"
                                        onclick="thisPage.adicionar_item_Click(this)"
                                        urlPartial="documento_recebido_partial_form_add_item.php"
                                        contentPartial="message_panel"
                                        /><br>
                                    <br>
                                <? endif; ?>
                                <table class="tb_cadastro_saida" style="width:100%" cellpadding="0" cellpadding="0">
                                <tr>
                                    <th style="border: 0px solid #CCCCCC;">
                                        <input type="hidden" 
                                            name="cd_documento_recebido_item_selected" 
                                            id="cd_documento_recebido_item_selected"
                                            value="" 
                                            />
                                        <div id='lista_documentos_incluidos_div'>

							                <? $thisPage->lista_documento_render($itens); ?>

                                        </div>
                                        <br />
                                    </th>
                                </tr>
                                </table>
                                <br><br>
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

            <!--  END : LISTA DE DOCUMENTO DO PROTOCOLO  -->

        </td>
        <td align="center" valign="center"></td>
    </tr>
    </table>

    <br>

    <div id="lista_documentos_div" 
         style="
			display:NONE;
			margin-top: 30px;
			margin-left: 0px;
			left: 200;
			top: 0;
			position: absolute;
         "
    >
        <table bgcolor="#DAE9F7" border="1" bordercolor="#000000" cellpadding="1" cellspacing="0">
        <tr><td>
            <table class="tb_lista_resultado">
            <tr>
                <td>Filtro:<br><input type="text"
                    name="filtro_nome_documento_text"
                    id="filtro_nome_documento_text"
                    style="width:300px"
                    maxlenght="255"
                    /><input type="button" 
                    name="consultar_documentos_button" 
                    id="consultar_documentos_button" 
                    value="Consultar"
                    title="Clique aqui para filtrar o documento"
                    urlPartial="atendimento_protocolo_partial_form.php"
                    onclick="thisPage.consultar_documentos_Click(this);"
                    extra=""
                    />
                <input type="button" 
                    name="fechar_button" 
                    id="fechar_button" 
                    value="Fechar"
                    title="Clique aqui para fechar"
                    onclick="Effect.Fade('lista_documentos_div');"
                    /></td>
            </tr>
            <tr>
            <td align="center"> <!--  --> </td>
            </tr>
            <tr>
                <td>
                    <div    id="lista_documentos_grid_div"
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

    <? $thisPage = null; ?>