<? header("Content-Type: text/html; charset=iso-8859-1"); ?>
<?
    include_once('inc/sessao.php');
    include_once('inc/conexao.php');
    include_once('inc/ePrev.Entity.php');
    include_once('inc/ePrev.Service.Projetos.php');
    include_once('inc/ePrev.ADO.Projetos.documento_protocolo.php');
    include_once('inc/ePrev.UserControl.Grid.php');

    include('oo/start.php');
    using( array('projetos.documento_protocolo') );

    class documento_protocolo_partial_lista
    { 	// begin_class
        private $db;
        private $filtrar;
        private $filtro;
        private $listar_pendentes;
        private $divisao;
        private $allow_send;
        private $allow_ok;
        private $allow_edit;
        private $allow_view;
        
        private $ajax_command;
        
        public $DIVISAO_GB = "GB"; //"GB";
        public $DIVISAO_GAD = "GAD"; //"GAD";
        public $DIVISAO_GAP = "GAP"; // "GAP";
        
        function documento_protocolo_partial_lista( $_db, $_divisao )
        {
            $this->db = $_db;

            $this->filtro = new entity_projetos_documento_protocolo();
            $this->divisao = $_divisao;

            $this->requestParams();

            // Desvio do fluxo em virtude de comandos ajax
            if ($this->ajax_command=="receber_item")
            {
				// recebimento de item, comando executado na lista de itens de um protocolo
                $this->receber_item();
			}
			elseif($this->ajax_command=="confirmar_indexacao")
			{
				$this->confirmar_indexacao();
			}
            else
            {
                // segue fluxo normal
                $this->allow_view = true; // Todos podem visualizar o registro
    
                $this->allow_send = ( $_divisao==$this->DIVISAO_GAP  || $_divisao==$this->DIVISAO_GB ); // Apenas GAP E GB pode enviar
                $this->allow_ok   = ( $_divisao==$this->DIVISAO_GAD); // Apenas GAD pode confirmar recebimento (OK)
                $this->allow_edit = ( $_divisao==$this->DIVISAO_GAP || $_divisao==$this->DIVISAO_GB ); // Apenas GAP e GB pode editar protocolo
                $this->allow_view = ( $_divisao==$this->DIVISAO_GAP || $_divisao==$this->DIVISAO_GB ); // GAP, GAD E GB podem visualizar o protocolo
            }
        }

        function __destruct()
        {
            $this->db = null;
        }

        function confirmar_indexacao()
        {
        	$cd_documento_protocolo = intval( $_POST["cd_comando_text"] );
        	$ret = documento_protocolo::confirmar_indexacao( $cd_documento_protocolo );
        	
        	if($ret)
        	{
        		echo "1";
        	}
        }

        public function get_ajax_command()
        {
            return $this->ajax_command;
        }

        function requestParams()
        {
            $this->filtrar      = isset( $_POST["filtrar_hidden"] ) ? $_POST["filtrar_hidden"] : "";
            $this->ajax_command = isset( $_POST["command"] ) ? $_POST["command"] : "";

            if (isset($_POST["filtro_ano_text"]))
            {
                $this->filtro->set_ano( $_POST["filtro_ano_text"] );
			}
            if (isset($_POST["filtro_contador_text"]))
            {
                $this->filtro->set_contador( $_POST["filtro_contador_text"] );
			}
            $this->listar_pendentes = isset( $_POST["listar_pendentes_checkbox"] ) ? $_POST["listar_pendentes_checkbox"] : "";
        }
        
        function receber_item()
        {
        	$recebido = '';
        	if(isset($_POST['fl_recebido_' . $_POST["cd_comando_text"] ]))
        	{
        		$recebido = $_POST['fl_recebido_' . $_POST["cd_comando_text"] ];
	        	if($recebido) $recebido='S'; else $recebido='N';
        	}
        	
            $item = new entity_projetos_documento_protocolo_item();
            $item->set_cd_documento_protocolo_item( $_POST["cd_comando_text"] );
            $item->set_fl_recebido( $recebido );

            $service = new service_projetos( $this->db );
            $service->documento_protocolo_item_Receber( $item );
            
            $service = null;
            $item = null;
        }

        public function loadLista()
        {
            $entity = new entity_projetos_documento_protocolo();
            $service = new service_projetos($this->db);

            if ( ($this->filtrar!="true" && $this->divisao == $this->DIVISAO_GAD) || $this->listar_pendentes=="S" )
            {
                $this->filtro->set_dt_ok( "null" );
                $this->filtro->set_dt_indexacao( "null" );
                $this->filtro->set_dt_envio( "NOT null" );
            }

            $result = $service->documento_protocolo_fetchByFilter( $this->filtro );
            $service = null;

            return $result;
        }

        public function get_listar_pendentes()
        {
            return $this->listar_pendentes;
        }
        
        public function getFiltro()
        {
            return $this->filtro;
        }
        
        public function getAllowView()
        {
            return $this->allow_view;
        }
        public function getAllowDelete()
        {
            return $this->allow_delete;
        }
        public function getAllowOK()
        {
            return $this->allow_ok;
        }
        public function getAllowEdit()
        {
            return $this->allow_edit;
        }
        public function getAllowSend()
        {
            return $this->allow_send;
        }

        public function getGridItem($id)
        {
            $grid = new ePrev_UserControl_Grid();
            
            $service = new service_projetos( $this->db );
            $itens = $service->documento_protocolo_item_FetchAll( $id );
            $service = null;

            $grid->loadResult( $itens );
            $grid->doRender();
            $grid = null;
            
            return true;
        }

        public function getItemResult($id)
        {
            $service = new service_projetos( $this->db );
            $rst = $service->documento_protocolo_item_FetchAll( $id );
            $service = null;
            return $rst;
        }

        public function getDivisao()
        {
            return $this->divisao;
        }
        public function getFiltrar()
        {
            return $this->filtrar;
        }

        public function permitir_confirmacao_indexacao()
        {
        	return ( $this->divisao==$this->DIVISAO_GAD );
        }
    } // end_class

    $thisPage = new documento_protocolo_partial_lista($db, $D);

    // desvio de fluxo
    if ($thisPage->get_ajax_command()=="receber_item")
    {
        $thisPage = null;
		exit();
	}
    if ($thisPage->get_ajax_command()=="confirmar_indexacao")
    {
        $thisPage = null;
		exit();
	}

    $resultado = $thisPage->loadLista();
?>
                    <!-- ---------------------- -->
                    <table style="width:100%">
                        <tr>
                        <td>
                            <table align="center">
                                <tr><td>
        
                                    <table cellpadding="0" cellspacing="0" border="1">
                                    <tr>
                                        <td>



<table width="100%" cellpadding="0" cellpadding="0">
<tr>
	<th bgcolor="#DAE9F7">
		<a href="javascript:void(0)" onclick="thisPage.showHide_Click(this);">Filtros (clique para exibir/esconder)</a>
	</th>
</tr>
<tr id="tr_filtro_form" style="display:">
	<td>
		<table cellpadding="0" cellpadding="0" width="100%">
		<tr>
			<td>
				<table class="tb_cadastro_saida" 
					style="width:100%" 
					cellpadding="0" 
					cellpadding="0"
					>
				<tr>
					<th><label for="filtro_ano_text">Ano/Sequencia:</label></th>
					<td>
						<input id="filtro_ano_text" 
							name="filtro_ano_text" 
							style="width:50px" 
							title="Ano do protocolo" 
							value="<?=$thisPage->getFiltro()->get_ano()?>" 
							onkeypress="mascara(this,soNumeros)"
							/>
						<input id="filtro_contador_text" 
							name="filtro_contador_text" 
							style="width:70px" 
							title="Sequêncial do protocolo" 
							value="<?= $thisPage->getFiltro()->get_contador() ?>"
							onkeypress="mascara(this,soNumeros)"
							/>
					</td>
				</tr>
				<tr style="display:none;">
					<th><label for="filtro_dt_inicio_text">Período (Dt Cadastro) dd/mm/aaaa:</label></th>
					<td>
						<input name="filtro_dt_inicio_text" id="filtro_dt_inicio_text" style="width:100px;" title="Data de Início" /> até <input name="filtro_dt_fim_text" id="filtro_dt_fim_text" style="width:100px;" title="Data final" />
					</td>
				</tr>
				<tr>
					<th><label for="listar_pendentes_checkbox">Listar apenas pendentes de confirmação pela GAD:</label></th>
					<td>
						<input id="listar_pendentes_checkbox" name="listar_pendentes_checkbox" type="checkbox" title="Listar apenas protocolos pendentes de recebimento pela GAD" value="S" 
						<? if( $thisPage->get_listar_pendentes()=="S" || ($thisPage->getFiltrar()!="true" && $thisPage->getDivisao()==$thisPage->DIVISAO_GAD) ) : ?>
							CHECKED
						<? endif; ?>
						/>
					</td>
				</tr>
				</table>
			</td>

			<td align="center" valign="center"><a 
				href="javascript:void(0)"><input type="hidden" 
				   name="filtrar_hidden" 
				   id="filtrar_hidden"
				   value="<?=$_POST["filtrar_hidden"]?>"
				   ><img id="filtrar_button"
				   src="img/btn_atualizar.jpg"
				   border="0"
				   onclick="thisPage.filtrar_Click(this);"
				   urlPartial="documento_protocolo_partial_lista.php"
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




<table align='center' class='tb_lista_resultado'>
	<tr>
		<td colspan="10" align="center">
		
		
		
			<table width="100%" border="0">
				<tr>
					<? if( $thisPage->getAllowEdit() ) { ?>
						<td style="width:20px"><img src='img/btn_manutencao.jpg' border='0' title='Editar protocolo de documentos' width='20' height='20' /></td>
						<td><font color="black">Editar protocolo</font></td>
					<? } ?>
					<? if( $thisPage->getAllowSend() ) { ?>
						<td align="right"><font color="black">Enviar protocolo para GAD</font></td>
						<td style="width:20px" align="right"><img src='img/btn_documento_enviar.png' border='0' title='Enviar protocolo' /> </td>
					<? } ?>
				</tr>
				<tr>
					<? if( $thisPage->getAllowView() ) { ?>
						<td style="width:20px"><img src='img/btn_ver_correspondencia.gif' border='0' title='Ver detalhes do protocolo de documentos' width='20' height='20' /> </td>
						<td><font color="black">Ver detalhes do protocolo</td>
					<? } ?>
					<? if( $thisPage->getAllowOk() ) { ?>
						<td align="right"></td>
						<td style="width:20px" align="right"></td>
					<? } ?>
				</tr>
				<tr>
					<? if( $thisPage->getAllowView() ) { ?>
						<td style="width:20px"></td>
						<td><font color="black"></td>
					<? } ?>
					<? if( $thisPage->getAllowOk() ) { ?>
						<td align="right"></td>
						<td style="width:20px" align="right"></td>
					<? } ?>
				</tr>
				<tr>
					<? if( $thisPage->getAllowView() ) { ?>
						<td style="width:20px"></td>
						<td><font color="black"></td>
					<? } ?>
					<? if( $thisPage->getAllowOk() ) : ?>
						<td align="right"></td>
						<td style="width:20px" align="right"></td>
					<? endif; ?>
				</tr>
			</table>

		</td>
	</tr>
	<tr>
		<th align='center'></th>
		<th align='center' class="td_border">Ano/Sequencia</th>
		<th align='center' class="td_border">Dt Cadastro</th>
		<th align='center' class="td_border">Cadastrado por</th>
		<th align='center' class="td_border">Dt Envio</th>
		<th align='center' class="td_border">Enviado por</th>
		<th align='center' class="td_border">Dt Confirmação</th>
		<th align='center' class="td_border">Confirmado por</th>
		<th align='center' class="td_border" title="Quantidade de ítens indexados">Qtd Indexados</th>
		<th align='center' class="td_border" title="Quantidade de ítens devolvidos">Qtd Devolvidos</th>
		<th align='center'></th>
	</tr>

	<?php $bgcolor=""; while ($row = pg_fetch_array($resultado)) : ?>

		<?php if(intval($row["quantidade_item_devolvido"])==0) $color=""; else $color="red"; ?>

		<tr style="color:<?php echo $color; ?>" bgcolor="<? if($bgcolor!="#ffffff") $bgcolor="#ffffff"; else $bgcolor="#f4f4f4"; echo($bgcolor); ?>" id="tr_row_protocolo_<?= $row["cd_documento_protocolo"] ?>">

			<td class="td_border">

				<!-- EXIBIR DETALHE - edit -->
				<? if( $thisPage->getAllowEdit() && $row["dt_ok"]=="" ) : ?>
					<a href='javascript:void(0)' onclick='thisPage.details_Click(this);' urlPartial='documento_protocolo_partial_form.php' command='editar' registroId='<?= $row["cd_documento_protocolo"] ?>' ><img src='img/btn_manutencao.jpg' border='0' title='Editar protocolo de documentos' width='20' height='20' /></a>
				<? endif;?>

				<!-- EXIBIR DETALHE - view -->
				<? if($thisPage->getAllowEdit() && $row["dt_ok"]!="" ) : ?>
					<a href='javascript:void(0)' onclick='thisPage.details_Click(this);' urlPartial='documento_protocolo_partial_form.php' command='ver_detalhe' registroId='<?= $row["cd_documento_protocolo"] ?>'><img src='img/btn_ver_correspondencia.gif' border='0' title='Ver detalhes do protocolo de documentos' width='20' height='20' /></a>
				<? endif; ?>

			</td>

			<td align='center' class="td_border"><?= $row["ano_seq"]?></td>
			<td align='center' class="td_border"><?= $row["dt_cadastro"]?></td>
			<td align='center' class="td_border"><?= $row["guerra_cadastro"]?></td>
			<td align='center' class="td_border"><?= $row["dt_envio"]?></td>
			<td align='center' class="td_border"><?= $row["guerra_envio"] . "/" . $row["divisao_envio"] ?></td>
			<td align='center' class="td_border"><?= $row["dt_ok"]?></td>
			<td align='center' class="td_border"><?= $row["guerra_ok"]?></td>
			<td align='center' class="td_border" title="Quantidade de ítens indexados"><?php echo $row["quantidade_item_recebido"] . '/' . $row["quantidade_item"]; ?></td>
			<td align='center' class="td_border" title="Quantidade de ítens devolvidos"><?php echo $row["quantidade_item_devolvido"]; ?></td>

			<!-- COMANDOS -->
			<td class="td_border">

				<!-- ENVIAR -->
				<?php if( $thisPage->getAllowSend() && $row["dt_envio"]=="" ) : ?>
					<a href='javascript:void(0);' onClick='thisPage.enviar_Click(this);' urlPartial='documento_protocolo_partial_form_send.php' registroId='<?= $row["cd_documento_protocolo"] ?>' ><img src='img/btn_documento_enviar.png' border='0' title='Enviar protocolo' /></a>
				<?php endif; ?>

				<!-- RECEBER -->
				<?php if( $thisPage->getAllowOK() && ($row["dt_ok"]=="") ) : ?>
					<input type="button" value="Receber" class="botao" style="width:100;" onclick="document.location='documento_protocolo_partial_item.php?cd=<?php echo $row['cd_documento_protocolo']; ?>'" />
					<!-- <a href="documento_protocolo_partial_item.php?cd=<?php echo $row['cd_documento_protocolo']; ?>"><img src="img/table_detalhe.png" border="0" title="Listar ítens do protocolo" /></a> -->
				<?php endif; ?>

				<?php if( $thisPage->getAllowOK() && $row["dt_envio"]!="" && $row["dt_ok"]=="" ) : ?>

					<?php if( $row["quantidade_item_recebido"] == $row["quantidade_item"] ): ?>
						<!-- <a href="javascript:void(0);" onClick="thisPage.receber_Click(this);" urlPartial="documento_protocolo_partial_form_receive.php" registroId="<?= $row["cd_documento_protocolo"] ?>"><img src="img/btn_confirmar.gif" border="0" title="Confirmar recebimento do protocolo" /></a> -->
					<?php endif; ?>

				<?php endif; ?>

				<?php if( $thisPage->getAllowOK() && $row["dt_ok"]!="" && $row["dt_indexacao"]=="" ): ?>
					<input type="button" value="Indexação" class="botao" style="width:100;" onclick="document.location='documento_protocolo_partial_indexar.php?cd=<?php echo $row['cd_documento_protocolo']; ?>'" />
					<!-- <a href="documento_protocolo_partial_indexar.php?cd=<?php echo $row['cd_documento_protocolo']; ?>"><img src="img/confirmar_digitalizacao.png" border="0" title="Listar ítens do protocolo" /></a> -->
				<?php endif; ?>

			</td>
			<!-- COMANDOS -->

		</tr>

	<? endwhile; ?>

</table>





                                        </td>
                                    </tr>

                                    </table>
                                </td></tr>
                            </table>
                        </td>
                        </tr>
                    </table>
                    <div id="message_panel"></div>
                    <!-- --------------------------- -->

                    <div id="confirm_cancel" style="display:none;">
                        <table bgcolor="#DAE9F7" border="1" bordercolor="#000000" cellpadding="1" cellspacing="0">
                        <tr><td>
                            <table>
                            <tr>
                                <td>Motivo:<br><input type="text"
                                    name="motivo_cancelamento_text"
                                    id="motivo_cancelamento_text"
                                    style="width:300px"
                                    maxlenght="255"
                                    /></td>
                            </tr>
                            <tr>
                            <td align="center">
                                <input type="button" 
                                    name="confirmar_cancelamento" 
                                    id="confirmar_cancelamento" 
                                    value="Confirmar"
                                    title="Clique aqui para excluir o protocolo"
                                    urlPartial="documento_protocolo_partial_form_cancel.php"
                                    onclick="thisPage.cancelar_Click(this);"
                                    />
                                <input type="button" 
                                    name="desistir_cancelamento" 
                                    id="desistir_cancelamento" 
                                    value="Desistir"
                                    title="Clique aqui para Desistir de cancelar o protocolo"
                                    onclick="$('confirm_cancel').hide();"
                                    />
                            </td>
                            </tr>
                            </table>
                        </td></tr></table>

                    </div>