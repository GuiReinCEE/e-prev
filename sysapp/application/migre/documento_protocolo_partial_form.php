<? header("Content-Type: text/html; charset=iso-8859-1"); ?>
<?
    include_once('inc/sessao.php');
    include_once('inc/conexao.php');

    include_once('inc/ePrev.Entity.php');

    include_once('inc/ePrev.ADO.tipo_documentos.php');
    include_once('inc/ePrev.ADO.Projetos.documento_protocolo.php');

    include_once('inc/ePrev.Service.Public.php');
    include_once('inc/ePrev.Service.Projetos.php');

    include_once('inc/ePrev.UserControl.Grid.php');

    class documento_protocolo_partial_form
    { #begin_class
        private $db;
        private $entidade;
        private $command;
        private $cd_documento_protocolo;
        private $filtro_documento;
        
        function documento_protocolo_partial_form( $_db )
        {
            $this->db = $_db;
            
            $this->entidade = new entity_projetos_documento_protocolo();
            
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
            if (isset($_POST["IDText"])) {
                $this->entidade->set_cd_documento_protocolo( $_POST["IDText"] );
			}
            if (isset($_POST["cd_comando_text"])) {
                $this->entidade->set_cd_documento_protocolo( $_POST["cd_comando_text"] );
			}
            if (isset($_POST["ordem_itens_select"])) {
                $this->entidade->set_ordem_itens( utf8_decode( $_POST["ordem_itens_select"] ) );
			}
            if (isset($_REQUEST["command"])) {
                $this->command = $_REQUEST["command"];
			}
            if (isset($_REQUEST["id"])) {
                $this->cd_documento_protocolo = $_REQUEST["id"];
			}
            if (isset($_POST["filtro_nome_documento_text"])) {
                $this->filtro_documento = utf8_decode( $_POST["filtro_nome_documento_text"] );
			}
        }

        function load()
        {
            $service = new service_projetos( $this->db );
            
            $this->entidade->set_cd_documento_protocolo($this->cd_documento_protocolo);
            $service->documento_protocolo_LoadById( $this->entidade );
            
            $result = null;
            $service = null;
            
            return $result;
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
            return $this->cd_documento_protocolo;
        }
        
        public function getEntidade()
        {
            return $this->entidade;
        }
        
        public function getItens()
        {
            $result = null;
            if( $this->entidade->get_cd_documento_protocolo() )
            {
                $service = new service_projetos( $this->db );
                $result = $service->documento_protocolo_item_FetchAll_ToGrid( $this->entidade->get_cd_documento_protocolo() );
                $service = null;
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

        public function selected_ordem( $value )
        {
            $ret = "";
            if ($value==$this->entidade->get_ordem_itens())
            {
				$ret = " selected ";
			}
            else
            {
				$ret = "";
            }
            return $ret;
        }

        private function update_ordem_itens()
        {
            $service = new service_projetos( $this->db );
            $service->documento_protocolo_UpdateOrdem( $this->entidade );
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
					<th align='center'>Processo</th>
					<th align='center'>Criador</th>
					<th align='center'>Data</th>
					<th align='center'>Observação</th>
					<th align='center'>Folhas</th>
					<th align='center'></th>
				</tr>
			";

			while( $row = pg_fetch_array($itens) ) :
				$participante_exibir = "";

				if($participante != $row['participante'])
				{
					$bgcolor = ($bgcolor=="#ffffff")?"#eeeeee":"#ffffff";
					$participante = $row['participante'];
					$participante_exibir = $participante;
				}

				echo "
	                <tr bgcolor='".$bgcolor."'>
	                <td>".$participante_exibir."</td>
	                <td>".$row['documento']."</td>
	                <td>".$row['ds_processo']."</td>
	                <td>".$row['criador']."</td>
	                <td>".$row['dt_cadastro']."</td>
	                <td>".$row['observacao']."</td>
	                <td>".$row['nr_folha']."</td>
	                <td>
	            ";

				if( $this->entidade->get_dt_ok()=="" ):

					echo "
						<table cellpadding='0' cellspacing='0' border='0'>
	                    <tr>

	                        <td style='border: 0px solid #CCCCCC;'>
	                            <a href='javascript:void(0)'
	                                onclick='thisPage.excluir_item_Click(this);'
	                                registroId='".$row['cd_documento_protocolo_item']."'
	                                ><img src='img/btn_deletar.gif'
	                                        border='0'
	                                        title='Excluir o documento'
	                                        /></a>
	                        </td>

	                    </tr>
	                    </table>
	                ";

				endif;

	            echo "
	                </td>
	                </tr>
				";

			endwhile;

			echo "    
	            </table>
	        ";
        }
    } #end_class

    $thisPage = new documento_protocolo_partial_form( $db );

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
    // Itens
    // $grid = new ePrev_UserControl_Grid();
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
    <td align="right" valign="center">

        <? if($thisPage->getEntidade()->get_cd_documento_protocolo()!="") { ?>

            <? if( $thisPage->getEntidade()->get_dt_envio()=="" ) { ?>
                <a href='javascript:void(0);'
                    id="enviar_protocolo_interna_img"
                    onClick='thisPage.enviar_Click(this);' 
                    urlPartial='documento_protocolo_partial_form_send.php'
                    registroId='<?= $thisPage->getEntidade()->get_cd_documento_protocolo() ?>'
                    ><img 
                        width="27px"
                        src='img/btn_documento_enviar.png' 
                        border='0'
                        title='Enviar protocolo'
                        /></a>
            <? } ?>

            <? if( $thisPage->getEntidade()->get_dt_ok()=="" && $thisPage->getEntidade()->get_dt_exclusao()=="" ) { ?>

                <a href='javascript:void(0)' 
                    onclick='thisPage.cancelar_Click(this);'
                    urlPartial="documento_protocolo_partial_form_cancel.php" 
                    registroId='<?= $thisPage->getEntidade()->get_cd_documento_protocolo() ?>'
                    ><img src='img/btn_exclusao.jpg' 
                    border='0'  
                    title='Excluir protocolo de documentos' 
                    /></a>

            <? } ?>

        <? } ?>
        
    </td>
    </tr>
    </table>
    </center>

    <table cellpadding="0" cellpadding="0" align="center" style="width:700px">
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
                            urlPartial="documento_protocolo_partial_form_save.php"
                            contentPartial="message_panel"
                            /></a>
                            -->
                    <? endif; ?>
                </th>
            </tr>
            <tr style="display:">
                <th>ID:</th>
                <td><input id="cd_documento_protocolo_text" 
                    name="cd_documento_protocolo_text" 
                    style="width:50px" 
                    title="Código" 
                    class="passed"
                    readonly
                    value="<?= $thisPage->getEntidade()->get_cd_documento_protocolo() ?>" 
                    /></td>
            </tr>
            <tr>
                <th>Ano/Seq:</th>
                <td><input id="ano_text" 
                        name="ano_text" 
                        style="width:50px" 
                        maxlenght="255" 
                        class="normal"
                        value="<?= $thisPage->getEntidade()->get_ano() ?>"
                        readonly
                        />
                    <input id="contador_text" 
                        name="contador_text" 
                        style="width:50px" 
                        maxlenght="255" 
                        class="normal"
                        value="<?= $thisPage->getEntidade()->get_contador() ?>"
                        readonly
                        /> (gerado automaticamente)</td>
            </tr>

            <? if($thisPage->getId()!="") { ?>
                <tr>
                    <th>Dt Cadastro:</th>
                    <td><?= $thisPage->getEntidade()->get_dt_cadastro() ?></td>
                </tr>
                <tr>
                    <th>Cadastrado por:</th>
                    <td>
                        <?= $thisPage->getEntidade()->get_usuario_cadastro()->get_guerra() ?>
                    </td>
                </tr>

                <tr>
                    <th>Dt Envio:</th>
                    <td>
                        <?= $thisPage->getEntidade()->get_dt_envio() ?>
                    </td>
                </tr>

                <tr>
                    <th>Enviado por:</th>
                    <td>
                        <?= $thisPage->getEntidade()->get_usuario_envio()->get_guerra() ?>
                    </td>
                </tr>
                <tr>
                    <th>Dt Confirmação:</th>
                    <td><?= $thisPage->getEntidade()->get_dt_ok() ?></td>
                </tr>
                <tr>
                    <th>Confirmado por:</th>
                    <td><?= $thisPage->getEntidade()->get_usuario_ok()->get_guerra() ?></td>
                </tr>
            <? } ?>
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
                    <table class="tb_cadastro_saida" style="width:100%" cellpadding="0" cellpadding="0">

                        <tr>
                            <th colspan="2" align="right">
                                <? if( $thisPage->getEntidade()->get_dt_envio()=="" ) { ?>
                                    Adicionar Documento ao Protocolo
                                <? } else {?>
                                    Documentos do Protocolo
                                <? } ?>
                            </th>
                        </tr>
                        
                        <tr <? if( $thisPage->getEntidade()->get_dt_envio()!="" ) { echo( "style='display:none'" ); } ?>>
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
                                    value="<?/* if(isset($_REQUEST["cd_tipo_doc"])) echo($_REQUEST["cd_tipo_doc"]); */?>"
                                    loadContent="nome_documento_text"
                                    urlPartial="documento_protocolo_partial_form.php"
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
                                        value="<? /*if(isset($_REQUEST["nome_documento"])) echo($_REQUEST["nome_documento"]); */?>" 
                                /><div id="item_cd_tipo_doc_text_message" class="error" style="display:none">Campo obrigatório</div></td>
                        </tr>
                        <tr <? if( $thisPage->getEntidade()->get_dt_envio()!="" ) { echo( "style='display:none'" ); } ?>>
                            <th>EMP/RE/SEQ:</th>
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
	                                urlPartial="documento_protocolo_partial_form.php"
	                                args="command=load_participante_by_re"
	                                loadContent="nome_participante_text"
	                                emp="item_cd_empresa_text"
	                                re="item_cd_registro_empregado_text"
	                                seq="item_seq_dependencia_text"
	                                onblur="thisPage.reComplete_Blur(this);"
	                                value="<?/* if(isset($_REQUEST["emp"])) echo($_REQUEST["emp"]); */?>"
	                                class="passed"
	                                />
                            <input id="item_cd_registro_empregado_text" 
                                name="item_cd_registro_empregado_text" 
                                style="width:70px" 
                                title="Registro do Empregado com dígito (apenas números)" 
                                class="passed" 
                                onkeypress="mascara(this,soNumeros); return thisPage.handleEnter(this, event);"
                                urlPartial="documento_protocolo_partial_form.php"
                                args="command=load_participante_by_re"
                                loadContent="nome_participante_text"
                                emp="item_cd_empresa_text"
                                re="item_cd_registro_empregado_text"
                                seq="item_seq_dependencia_text"
                                onblur="thisPage.reComplete_Blur(this);"
                                value="<?/* if(isset($_REQUEST["re"])) echo($_REQUEST["re"]); */?>"
                                />
                            <input id="item_seq_dependencia_text"
                                name="item_seq_dependencia_text"
                                style="width:50px"
                                title="Sequência de dependência"
                                class="passed"
                                onkeypress="mascara(this,soNumeros); return thisPage.test_enter(this, event);"
                                urlPartial="documento_protocolo_partial_form.php"
                                args="command=load_participante_by_re"
                                loadContent="nome_participante_text"
                                emp="item_cd_empresa_text"
                                re="item_cd_registro_empregado_text"
                                seq="item_seq_dependencia_text"
                                onblur="thisPage.reComplete_Blur(this);"
                                value="<?/* if(isset($_REQUEST["seq"])) echo($_REQUEST["seq"]); */?>"
                                /> <input id="nome_participante_text" style="width:300px" class="passed" readonly value="<?/*if(isset($_REQUEST["nome_participante"])) echo($_REQUEST["nome_participante"]); */?>" /><div id="item_cd_empresa_text_message" class="error" style="display:none">Participante não encontrado</div></td>
                        </tr>
                        <tr <? if( $thisPage->getEntidade()->get_dt_envio()!="" ) { echo( "style='display:none'" ); } ?>>
                            <th>Número do processo:</th>
                            <td>
                            	<input id="processo_radio" name="preferencia_radio" type="radio" onclick="$('item_ds_processo').focus();" />
                            	<input id="item_ds_processo" name="item_ds_processo" style="width:200px;" title="Número do processo" value="" class="passed" maxlength="50" onkeypress="return thisPage.test_enter(this, event);" />
                            </td>
                        </tr>
                        <tr <? if( $thisPage->getEntidade()->get_dt_envio()!="" ) { echo( "style='display:none'" ); } ?>>
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
                        <tr <? if( $thisPage->getEntidade()->get_dt_envio()!="" ) { echo( "style='display:none'" ); } ?>>
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
        
                        <tr>
                            <th colspan="2" align="right">
                                <br>
                                <? if( $thisPage->getEntidade()->get_dt_envio()=="" ) { ?>
                                    <input type="button"
                                    	class="botao" 
                                        id="adicionar_item_button"
                                        name="adicionar_item_button"
                                        value="Adicionar documento"
                                        onclick="thisPage.adicionar_item_Click(this)"
                                        urlPartial="documento_protocolo_partial_form_add_item.php"
                                        contentPartial="message_panel"
                                        /><br>
                                    <br>
                                <? } ?>
                                <table class="tb_cadastro_saida" style="width:100%" cellpadding="0" cellpadding="0">
                                <tr>
                                    <th style="border: 0px solid #CCCCCC;">
                                        <input type="hidden" 
                                            name="cd_documento_protocolo_item_selected" 
                                            id="cd_documento_protocolo_item_selected"
                                            value="" 
                                            />
                                        <div id='lista_documentos_incluidos_div'>

							                <? $thisPage->lista_documento_render($itens); ?>
							                                                        
                                        </div>
                                        <br />
                                        Ordenar lista de itens por: 
                                        <select name="ordem_itens_select" onchange="thisPage.ordem_itens_Change(this);" registroId="<?= $thisPage->getEntidade()->get_cd_documento_protocolo() ?>" />
                                            <option value="" <?= $thisPage->selected_ordem( "" ) ?>>::Selecione::</option>
                                            <option value="C" <?= $thisPage->selected_ordem( "C" ) ?>>Cadastro</option>
                                            <option value="P" <?= $thisPage->selected_ordem( "P" ) ?>>Participante</option>
                                            <option value="T" <?= $thisPage->selected_ordem( "T" ) ?>>Tipo de documento</option>
                                            <option value="S" <?= $thisPage->selected_ordem( "S" ) ?>>Processo</option>
                                        </select>
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
            <td align="center">
                
            </td>
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