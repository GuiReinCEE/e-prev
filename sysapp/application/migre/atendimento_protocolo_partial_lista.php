<? header("Content-Type: text/html; charset=iso-8859-1"); ?>
<?
    include_once('inc/sessao.php');
    include_once('inc/conexao.php');
    include_once('inc/ePrev.Util.String.php');
    include_once('inc/ePrev.Service.Projetos.php');
    include_once('inc/ePrev.ADO.Projetos.atendimento_protocolo.php');

    include 'oo/start.php';
    using( array( 
    			  'projetos.atendimento_protocolo_tipo' 
    			, 'projetos.atendimento_protocolo_discriminacao' 
    			, 'projetos.mala_direta_integracao' 
    			, 'projetos.usuarios_controledi' 
    ) );

    class atendimento_protocolo_partial_lista
    {
        private $db;
        private $filtrar;
        private $filtro;
        private $divisao;
        private $allow_confirm;
        private $allow_cancel;
        private $allow_edit;
        private $allow_view;

        function atendimento_protocolo_partial_lista( $_db, $_divisao )
        {
            $this->db = $_db;

            $this->filtro = new helper_correspondencia_gap__fetch_by_filter();

            $this->divisao = $_divisao;
            $this->requestParams();

            $this->allow_view = true;                                       // Todos podem visualizar o registro
            $this->allow_confirm = ( $_divisao=="GAD" || $_divisao=="GI" ); // Apenas GAD pode receber correspondencia
            $this->allow_edit = ( $_divisao=="GAP" || $_divisao=="GI" ); 	// Apenas GAP pode editar correspondencia
            $this->allow_cancel = ( $_divisao=="GAP" || $_divisao=="GAD" || $_divisao=="GI" ); // GAP e GAD podem cancelar correspondencia
        }

        function __destruct()
        {
            $this->db = null;
        }

        function requestParams()
        {
		
			if (isset($_REQUEST["filtrar_hidden"]))
            {
                $this->filtrar = $_REQUEST["filtrar_hidden"];
			}

            if (isset($_REQUEST["filtro__cd_atendimento_protocolo_tipo__select"]))
            {
                $this->filtro->setcd_atendimento_protocolo_tipo( $_REQUEST["filtro__cd_atendimento_protocolo_tipo__select"] );
			}
            if (isset($_REQUEST["filtro__cd_atendimento_protocolo_discriminacao__select"]))
            {
                $this->filtro->setcd_atendimento_protocolo_discriminacao( $_REQUEST["filtro__cd_atendimento_protocolo_discriminacao__select"] );
			}
			if (isset($_REQUEST["FiltroEmpresaText"]))
            {
                $this->filtro->setcd_empresa( $_REQUEST["FiltroEmpresaText"] );
			}
            if (isset($_REQUEST["FiltroREText"]))
            {
                $this->filtro->setcd_registro_empregado( $_REQUEST["FiltroREText"] );
			}
            if (isset($_REQUEST["FiltroSeqText"]))
            {
                $this->filtro->setseq_dependencia( $_REQUEST["FiltroSeqText"] );
			}
            if (isset($_REQUEST["FiltroNomeText"]))
            {
                $this->filtro->set_nome( $_REQUEST["FiltroNomeText"] );
			}
            if (isset($_REQUEST["FiltroDataGapText"]))
            {
                $this->filtro->dt_criacao__inicial = $_REQUEST["FiltroDataGapText"];
			}
            if (isset($_REQUEST["FiltroDataGap_final_Text"]))
            {
                $this->filtro->dt_criacao__final= $_REQUEST["FiltroDataGap_final_Text"];
			}
            if (isset($_REQUEST["FiltroHoraGapText"]))
            {
                $this->filtro->hr_criacao__inicial = $_REQUEST["FiltroHoraGapText"];
			}
            if (isset($_REQUEST["FiltroHoraGap_final_Text"]))
            {
                $this->filtro->hr_criacao__final= $_REQUEST["FiltroHoraGap_final_Text"];
			}
        	if (isset($_REQUEST["filtro__cd_usuario_criacao__select"]))
            {
                $this->filtro->setcd_usuario_criacao( $_REQUEST["filtro__cd_usuario_criacao__select"] );
			}
			
            if (isset($_REQUEST["filtro__cd_atendimento__text"]))
            {
                $this->filtro->setcd_atendimento( $_REQUEST["filtro__cd_atendimento__text"] );
			}
            if (isset($_REQUEST["filtro__cd_encaminhamento__text"]))
            {
                $this->filtro->setcd_encaminhamento( $_REQUEST["filtro__cd_encaminhamento__text"] );
			}

            if ($this->filtrar!="true" && $this->divisao == "GAD")
            {
                $this->filtro->setdt_recebimento( "null" );
                $this->filtro->setdt_cancelamento( "null" );
			}
            else
            {
                if ( isset($_REQUEST["FiltroDataGadText"]) )
                {
                    $this->filtro->setdt_recebimento( $_REQUEST["FiltroDataGadText"] );
				}
            }

            if($this->filtrar!="true")
            {
                $this->filtro->dt_criacao__inicial = strftime("%d/%m/%Y", mktime ( 0, 0, 0, date('m'), date('d')-7, date('Y') ));;
                $this->filtro->dt_criacao__final = date('d/m/Y');
            }
        }

        public function loadLista()
        {
            $entity = new entity_projetos_atendimento_protocolo();
            $service = new service_projetos( $this->db );

            $result = $service->correspondenciaGAP_fetchByFilter( $this->filtro );

            $idx = 0;
            $collection = array();
            while($row = pg_fetch_array($result))
            {
            	$collection[$idx] = $row;
            	$idx++;
            }

            $service = null;

            return $collection;
        }

        public function getFiltro()
        {
            return $this->filtro;
        }

        public function getAllowView()
        {
            return $this->allow_view;
        }
        public function getAllowCancel()
        {
            return $this->allow_cancel;
        }
        public function getAllowConfirm()
        {
            return $this->allow_confirm;
        }
        public function getAllowEdit()
        {
            return $this->allow_edit;
        }

        public function dropdown_remetente()
        {
        	$col = usuarios_controledi::select_special( usuarios_controledi::$SPECIAL_ATENDIMENTO_PROTOCOLO );
        	return $col;
        }

        public function dropdown_tipo()
        {
        	$col = atendimento_protocolo_tipo::select();
        	return $col;
        }

        public function dropdown_discriminacao()
        {
        	$col = atendimento_protocolo_discriminacao::select();
        	return $col;
        }
    }

    $thisPage = new atendimento_protocolo_partial_lista($db, $D);
    $resultado = $thisPage->loadLista();
    
    //var_dump($resultado);exit;

    $dd_remetente = $thisPage->dropdown_remetente();
    $dd_tipo = $thisPage->dropdown_tipo();
	$dd_discriminacao = $thisPage->dropdown_discriminacao();
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
						<table class="tb_cadastro_saida" style="width:100%" cellpadding="0" cellpadding="0">
						<tr>
							<th>EMP/RE/SEQ:</th>
							<td><input id="filtro_empresa_text" name="FiltroEmpresaText" style="width:50px" title="Código da Empresa" value="<?=$thisPage->getFiltro()->getcd_empresa()?>" />
							<input id="filtro_re_text" name="FiltroREText" style="width:70px" title="Registro do Empregado com dígito (apenas números)" value="<?=$thisPage->getFiltro()->getcd_registro_empregado()?>" />
							<input id="filtro_seq_text" name="FiltroSeqText" style="width:50px" title="Sequência de dependência" value="<?=$thisPage->getFiltro()->getseq_dependencia()?>" /></td>
						</tr>
						<tr>
							<th>Nome:</th>
							<td><input id="filtro_nome_text" name="FiltroNomeText" style="width:300px" title="Nome do destinatário" value="<?=$thisPage->getFiltro()->get_nome()?>" /></td>
						</tr>
						<tr>
							<th>Remessa (Data - Hora):</th>
							<td>
							    <input id="filtro_dtgap_text" name="FiltroDataGapText" style="width:100px" value="<?=$thisPage->getFiltro()->dt_criacao__inicial?>" OnKeyDown="mascaraData(this,event);" />
							    - <input id="filtro_hrgap_text" name="FiltroHoraGapText" style="width:50px" value="<?=$thisPage->getFiltro()->hr_criacao__inicial?>" OnBlur="verificaHora(this);" />
							até <input id="filtro_dtgap_final_text" name="FiltroDataGap_final_Text" style="width:100px" value="<?=$thisPage->getFiltro()->dt_criacao__final?>" OnKeyDown="mascaraData(this,event);" />
								- <input id="filtro_hrgap_final_text" name="FiltroHoraGap_final_Text" style="width:50px" value="<?=$thisPage->getFiltro()->hr_criacao__final?>" OnBlur="verificaHora(this);" />
							</td>
						</tr>
						<tr>
							<th>Remetente:</th>
							<td>
								<select
			                		id="filtro__cd_usuario_criacao__select" 
			                		name="filtro__cd_usuario_criacao__select"
			                		class="required"
			                	>
								<option value="">::Todos::</option>
								<? $selected=""; ?>								
								<? foreach( $dd_remetente->items as $item ) : ?>
		                			
									<? $selected = ($thisPage->getFiltro()->getcd_usuario_criacao()==$item->codigo)?'selected':''; ?>
		                			<option <?= $selected; ?> value="<?= $item->codigo; ?>"><?= $item->nome; ?></option>
		                			
		                		<? endforeach; ?>
		                		</select>

							</td>
						</tr>
						<tr>
							<th>Dt Recebimento (GAD):</th>
							<td><input id="filtro_dtgad_text" name="FiltroDataGadText" style="width:100px" value="<?=($thisPage->getFiltro()->getdt_recebimento()=="null")?"":$thisPage->getFiltro()->getdt_recebimento()?>" /></td>
						</tr>
						<tr>
							<th>Tipo:</th>
							<td>
								<select
			                		id="filtro__cd_atendimento_protocolo_tipo__select" 
			                		name="filtro__cd_atendimento_protocolo_tipo__select"
			                		class="required"
			                	>
								<option value="">::Todos::</option>
								<? $selected=""; ?>								
								<? foreach( $dd_tipo->items as $item ) : ?>
		                			
									<? $selected = ($thisPage->getFiltro()->getcd_atendimento_protocolo_tipo()==$item->cd_atendimento_protocolo_tipo)?'selected':''; ?>
		                			<option <?= $selected; ?> value="<?= $item->cd_atendimento_protocolo_tipo; ?>"><?= $item->nome; ?></option>
		                			
		                		<? endforeach; ?>
		                		</select>

							</td>
						</tr>
						<tr>
							<th>Discriminação:</th>
							<td>
							<select 
		                		id="filtro__cd_atendimento_protocolo_discriminacao__select" 
		                		name="filtro__cd_atendimento_protocolo_discriminacao__select"
		                		class="required"
		                	>
		                	
		                		<option value="">::Todos::</option>
		                		<? $selected=""; ?>
								<? foreach( $dd_discriminacao->items as $item ) : ?>

				                    <? $selected = ($thisPage->getFiltro()->getcd_atendimento_protocolo_discriminacao()==$item->cd_atendimento_protocolo_discriminacao)?'selected':''; ?>
			                		<option <?= $selected; ?> value="<?= $item->cd_atendimento_protocolo_discriminacao; ?>"><?= $item->nome; ?></option>

			                    <? endforeach; ?>
		                    </select>
							</td>
						</tr>
						<tr>
							<th>Protocolo/Encaminhamento:</th>
							<td>
								<input id="filtro__cd_atendimento__text" name="filtro__cd_atendimento__text" style="width:70px" title="Código do atendimento" value="<?=$thisPage->getFiltro()->getcd_atendimento()?>" />
								<input id="filtro__cd_encaminhamento__text" name="filtro__cd_encaminhamento__text" style="width:70px" title="Código do encaminhamento" value="<?=$thisPage->getFiltro()->getcd_encaminhamento()?>" />
							</td>
						</tr>
						<tr>
							<th>Exportar resultado para:</th>
							<td>
								<input type="button" class="botao" value="Exportar para PDF" onclick="thisPage.export_click('pdf');" />
								<input type="button" class="botao" value="Integra Mala Direta" onclick="thisPage.export_click('mala');" />
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
														   urlPartial="atendimento_protocolo_partial_lista.php"
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
		<div style='font-family:arial;font-size:12px;'><b>Quantidade:</b> <?php echo sizeof($resultado); ?></div>
		<table align='center' class='tb_lista_resultado'>
			<tr>
				<th align='center'>
					EMP/RED/SEQ
				</th>
				<th align='center'>
					Nome/Destino
				</th>
				<th align='center'>
					Tipo
				</th>
				<th align='center'>
					Protocolo
				</th>
				<th align='center'>
					Discriminação
				</th>
				<th align='center'>
					Remetente
				</th>
				<th align='center'>
					Remetido em
				</th>
				<th align='center'>
					Recebido GAD por
				</th>
				<th align='center'>
					Recebido GAD em
				</th>
				<th align='center'>
					Cancelado em
				</th>
				<th align='center'></th>
				<th align='center'></th>
				<th align='center'></th>
			</tr>
			<? 
			$bgcolor="#ffffff"; 
			foreach ( $resultado as $row )
			{
			?>
			<tr bgcolor="<?php if($bgcolor!="#ffffff") $bgcolor="#ffffff"; else $bgcolor="#f4f4f4"; echo($bgcolor); ?>">
				<? if ($row["cd_registro_empregado"]=="") { ?>
					<td></td>
				<? } else { ?>
					<td><?= $row["cd_empresa"] . "/" . $row["cd_registro_empregado"] . "/" . $row["seq_dependencia"] ?></td>
				<? } ?>
				<td><? if($row["nome"]!=""){ echo($row["nome"] . " / "); } ?><?= $row["destino"] ?></td>
				<td><?= $row["tipo_nome"] ?></td>
				<td><?php if($row["cd_atendimento"]!="") echo $row["cd_atendimento"] . "/" . $row["cd_encaminhamento"]; ?></td>
				<td><? echo $row["discriminacao_nome"]; if( $row["identificacao"]!='' ) {echo ' - ' . $row["identificacao"];} ?></td>
				<td><?= $row["nome_gap"] ?></td>
				<td><?= $row["dt_criacao"] ?></td>
				<td><?= $row["nome_gad"] ?></td>
				<td><?= $row["dt_recebimento"] ?></td>
				<td><?= $row["dt_cancelamento"] ?></td>
				<td>
				
					<? if( ($row["dt_recebimento"]=="" && $row["dt_cancelamento"]=="") && $thisPage->getAllowConfirm() ) { ?>
						<a href="javascript:void(0);"
							onClick="thisPage.receber_Click(this);" 
							urlPartial="atendimento_protocolo_partial_form_receive.php"
							receberId="<?= $row["cd_atendimento_protocolo"]?>"
							><img
								src="img/btn_confirmar.gif" 
								border="0" 
								title="Confirmar recebimento"
								/></a>
					<? } ?>
					
				</td>
				<td>
					<? if( ($row["dt_recebimento"]=="" && $row["dt_cancelamento"]=="") && $thisPage->getAllowCancel() ) { ?>
						<a href="javascript:void(0)" 
							onclick="thisPage.load_cancelar(this);" 
							correspondenciaId="<?= $row["cd_atendimento_protocolo"]?>"
							><img src="img/btn_deletar.gif" border="0" title="Cancelar correspondência" /></a>
					<? } ?>
				</td>
				<td>
					<? if( ($row["dt_recebimento"]=="" && $row["dt_cancelamento"]=="") && $thisPage->getAllowEdit() ) { ?>
						<a href="javascript:void(0)" 
							onclick="thisPage.details_Click(this);" 
							urlPartial="atendimento_protocolo_partial_form.php"
							command="editar"
							correspondenciaId="<?= $row["cd_atendimento_protocolo"]?>"
							><img src="img/btn_manutencao.jpg" 
								border="0" 
								title="Editar correspondência"
								width="20"
								height="20" 
								/></a>
					<? } else if ($thisPage->getAllowView()) { ?>
						<a href="javascript:void(0)" 
							onclick="thisPage.details_Click(this);" 
							urlPartial="atendimento_protocolo_partial_form.php"
							command="ver_detalhe"
							correspondenciaId="<?= $row["cd_atendimento_protocolo"]?>"
							><img src="img/btn_ver_correspondencia.gif"
								border="0"
								title="Visualizar correspondência"
								/></a>
					<? } ?>
							
				</td>
			</tr>
			<? } ?>
		</table>
		<div style='font-family:arial;font-size:12px;'><b>Quantidade:</b> <?php echo sizeof($resultado); ?></div>

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
                                    title="Clique aqui para cancelar a correspondência"
                                    urlPartial="atendimento_protocolo_partial_form_cancel.php"
                                    onclick="thisPage.cancelar_Click(this);"
                                    />
                                <input type="button" 
                                    name="desistir_cancelamento" 
                                    id="desistir_cancelamento" 
                                    value="Desistir"
                                    title="Clique aqui para Desistir de cancelar a correspondência"
                                    onclick="$('confirm_cancel').hide();"
                                    />
                            </td>
                            </tr>
                            </table>
                        </td></tr></table>

                    </div>