<?php
include_once("ePrev.DAL.DBConnection.php");
include_once("nextval_sequence.php");

include_once("ePrev.Enums.php");

include_once("ePrev.Entity.php");
include_once("ePrev.Helper.php");
include_once("ePrev.Helper.Projetos.avaliacao_resultado.php");
include_once("ePrev.Helper.ADO.contribuicao_senge.php");
include_once("ePrev.Helper.ADO.contribuicao_sinpro.php");

include_once("ePrev.ADO.Projetos.avaliacao_controle.php");
include_once("ePrev.ADO.Projetos.avaliacao.php");
include_once("ePrev.ADO.Projetos.avaliacao_capa.php");
include_once("ePrev.ADO.Projetos.envia_emails.php");
include_once("ePrev.ADO.Projetos.atividades.php");
include_once("ePrev.ADO.Projetos.usuarios_controledi.php");
include_once("ePrev.ADO.Projetos.familias_cargos.php");
include_once("ePrev.ADO.Projetos.atendimento_recadastro.php");
include_once("ePrev.ADO.Projetos.auto_atendimento_pagamento_impressao.php");

include_once("ePrev.ADO.public.listas.php");
include_once("ePrev.ADO.participantes.php");
include_once("ePrev.ADO.public.taxas.php");
include_once("ePrev.ADO.public.pacotes.php");
include_once("ePrev.ADO.public.contribuicoes_programadas.php");
include_once("ePrev.ADO.public.bloqueto.php");

include_once("ePrev.ADO.expansao.empresas_instituicoes.php");
include_once("ePrev.ADO.expansao.inscritos.php");
include_once("ePrev.ADO.oracle.functions.php");
include_once("ePrev.ADO.consultas.php");

include_once("ePrev.Util.Email.Text.php");
include_once("ePrev.Util.String.php");

/**
 * Classe para Serviços de Divulgação 
 * 
 * @access public
 * @package ePrev
 * @subpackage Service
 */
class service_projetos 
{
    private $db;

    function service_projetos( $_db ) 
    {
        $this->db = $_db;
    }

    function __destruct()
    {
        $this->db = null;
    }
    
    public function correspondenciaGAP_insert( entity_projetos_atendimento_protocolo $entidade)
    {
        $ado = new ADO_projetos_atendimento_protocolo( $this->db );
        
        try
        {
            /*$entidade->setcd_usuario_recebimento( String::IfBlankReturn( $entidade->getcd_usuario_recebimento(), "null" ) );
            $entidade->setcd_empresa( String::IfBlankReturn( $entidade->getcd_empresa(), "null" ) );
            $entidade->setcd_registro_empregado( String::IfBlankReturn( $entidade->getcd_registro_empregado(), "null" ) );
            $entidade->setseq_dependencia( String::IfBlankReturn( $entidade->getseq_dependencia(), "null" ) );*/
            if ($entidade->getcd_atendimento_protocolo()=="0") 
            {
                $bResult = $ado->insert( $entidade );
                $ado = null;
			}
            else
            {
                $bResult = $ado->update( $entidade );
                $ado = null;
            }
        }
        catch(Exception $e)
        {
            $ado = null;
            $bResult = false;
            echo( $e->getMessage() ); 
        }
        
        return $bResult;
    }

    public function correspondenciaGAP_fetchByFilter( helper_correspondencia_gap__fetch_by_filter $filtro )
    {
        $ado = new ADO_projetos_atendimento_protocolo( $this->db );

        try
        {
            $bResult = $ado->fetchByFilter( $filtro );
            $ado = null;
        }
        catch(Exception $e)
        {
            $ado = null;
            $bResult = false;
            echo( $e->getMessage() ); 
        }

        return $bResult;
    }

    public function correspondenciaGAP_delete( $value )
    {
        $ado = new ADO_projetos_atendimento_protocolo( $this->db );

        try
        {
            $bResult = $ado->delete( $value );
            $ado = null;
        }
        catch(Exception $e)
        {
            $ado = null;
            $bResult = false;
            echo( $e->getMessage() ); 
        }
        
        return $bResult;
    }

    public function correspondenciaGAP_receive( $entidade )
    {
        $ado = new ADO_projetos_atendimento_protocolo( $this->db );

        try
        {
            $bResult = $ado->updateReceive( $entidade );
            $ado = null;
        }
        catch(Exception $e)
        {
            $ado = null;
            $bResult = false;
            echo( $e->getMessage() ); 
        }

        return $bResult;
    }

    public function correspondenciaGAP_cancel( $entidade )
    {
        $ado = new ADO_projetos_atendimento_protocolo( $this->db );
        try
        {
            $bResult = $ado->updateCancel( $entidade );
            $ado = null;
        }
        catch(Exception $e)
        {
            $ado = null;
            $bResult = false;
            echo( $e->getMessage() ); 
        }

        return $bResult;
    }

    public function correspondenciaGAP_LoadById( $entidade )
    {
        $ado = new ADO_projetos_atendimento_protocolo( $this->db );
        try
        {
            $result = $ado->loadById( $entidade );
            $ado = null;
        }
        catch(Exception $e)
        {
            $ado = null;
            echo( $e->getMessage() ); 
        }

        return true;
    }

    public function documento_protocolo_LoadById( $entidade )
    {
        $ado = new ADO_projetos_documento_protocolo( $this->db );
        try
        {
            $result = $ado->loadById( $entidade );
            $ado = null;
        }
        catch(Exception $e)
        {
            $ado = null;
            echo( $e->getMessage() );
        }

        return true;
    }

    public function documento_protocolo_fetchByFilter( $filtro )
    {
        $ado = new ADO_projetos_documento_protocolo( $this->db );

        try
        {
            $ado->setFetchByFilter_select_define( '

                  a.cd_documento_protocolo
                , a.ano || \'/\' || a.contador AS ano_seq
                , TO_CHAR(a.dt_cadastro, \'DD/MM/YYYY HH24:MI\') AS dt_cadastro
                , b.guerra AS guerra_cadastro
                , TO_CHAR(a.dt_envio, \'DD/MM/YYYY HH24:MI\') AS dt_envio
                , c.guerra AS guerra_envio
                , c.divisao AS divisao_envio
                , TO_CHAR(a.dt_ok, \'DD/MM/YYYY HH24:MI\') AS dt_ok
                , TO_CHAR(a.dt_indexacao, \'DD/MM/YYYY HH24:MI\') AS dt_indexacao
                , d.guerra AS guerra_ok
                , ( SELECT COUNT(*) FROM projetos.documento_protocolo_item WHERE dt_exclusao IS NULL AND cd_documento_protocolo = a.cd_documento_protocolo ) as quantidade_item
                , ( SELECT COUNT(*) FROM projetos.documento_protocolo_item WHERE dt_exclusao IS NULL AND cd_documento_protocolo = a.cd_documento_protocolo AND fl_recebido=\'S\' ) as quantidade_item_recebido
                , ( SELECT COUNT(*) FROM projetos.documento_protocolo_item WHERE dt_exclusao IS NULL AND cd_documento_protocolo = a.cd_documento_protocolo AND dt_devolucao IS NOT NULL ) as quantidade_item_devolvido

            ' );

            $bResult = $ado->fetchByFilter( $filtro );
            $ado = null;
        }
        catch(Exception $e)
        {
            $ado = null;
            $bResult = false;
            echo( $e->getMessage() ); 
        }

        return $bResult;
    }

    public function documento_protocolo_Send( entity_projetos_documento_protocolo $entidade )
    {
        $ado = new ADO_projetos_documento_protocolo( $this->db );

        try
        {
            $bResult = $ado->updateSend( $entidade );
            $ado = null;
        }
        catch(Exception $e)
        {
            $ado = null;
            $bResult = false;
            echo( $e->getMessage() ); 
        }

        return $bResult;
    }

    public function documento_protocolo_Receive( entity_projetos_documento_protocolo $entidade )
    {
        $ado = new ADO_projetos_documento_protocolo( $this->db );

        try
        {
            $bResult = $ado->updateReceive( $entidade );
            $ado = null;
        }
        catch(Exception $e)
        {
            $ado = null;
            $bResult = false;
            echo( $e->getMessage() ); 
        }

        return $bResult;
    }

    public function documento_protocolo_item_Receber( entity_projetos_documento_protocolo_item $entidade )
    {
        $ado = new ADO_projetos_documento_protocolo( $this->db );

        try
        {
            $bResult = $ado->item_update_recebido( $entidade );
            $ado = null;
        }
        catch(Exception $e)
        {
            $ado = null;
            $bResult = false;
            echo( $e->getMessage() ); 
        }

        return $bResult;
    }

    public function documento_protocolo_Cancel( entity_projetos_documento_protocolo $entidade )
    {
        $ado = new ADO_projetos_documento_protocolo( $this->db );

        try
        {
            $bResult = $ado->updateCancel( $entidade );
            $ado = null;
        }
        catch(Exception $e)
        {
            $ado = null;
            $bResult = false;
            echo( $e->getMessage() ); 
        }

        return $bResult;
    }

    public function documento_protocolo_item_FetchAll_ToGrid( $cd_documento_protocolo )
    {
        $ado = new ADO_projetos_documento_protocolo( $this->db );
        
        try
        {
            $ado->set_select_define( '

                  a.cd_documento_protocolo_item
                , d.cd_tipo_doc || \' - \' || d.nome_documento as documento
                , a.cd_empresa || \'/\' || a.cd_registro_empregado || \'/\' || a.seq_dependencia AS participante
                , c.guerra AS criador
                , TO_CHAR(a.dt_cadastro, \'DD/MM/YYYY HH:MI\') AS dt_cadastro
                , a.ds_processo
                , a.observacao
                , a.nr_folha
                , a.arquivo
                , a.arquivo_nome

            ' );
            $result = $ado->item_FetchAll( $cd_documento_protocolo );
            $ado = null;
        }
        catch(Exception $e)
        {
            $ado = null;
            $result = null;
            echo( $e->getMessage() ); 
        }

        return $result;
    }
    public function documento_protocolo_item_FetchAll( $cd_documento_protocolo, $nao_devolvidos_apenas = false )
    {
        $ado = new ADO_projetos_documento_protocolo( $this->db );

        try
        {
            $result = $ado->item_FetchAll( $cd_documento_protocolo, $nao_devolvidos_apenas );
            $ado = null;
        }
        catch(Exception $e)
        {
            $ado = null;
            $result = null;
            echo( $e->getMessage() ); 
        }

        return $result;
    }

    public function documento_protocolo_item_Insert( entity_projetos_documento_protocolo_item $entidade )
    {
        $ado = new ADO_projetos_documento_protocolo( $this->db );

        try
        {
            $bResult = $ado->item_Insert( $entidade );
            $ado = null;
        }
        catch(Exception $e)
        {
            $ado = null;
            $bResult = false;
            echo( $e->getMessage() ); 
        }

        return $bResult;
    }

    public function documento_protocolo_item_Delete( entity_projetos_documento_protocolo_item $entidade )
    {
        $ado = new ADO_projetos_documento_protocolo( $this->db );

        try
        {
            $bResult = $ado->item_Delete( $entidade );
            $ado = null;
        }
        catch(Exception $e)
        {
            $ado = null;
            $bResult = false;
            echo( $e->getMessage() ); 
        }
        
        return $bResult;
    }

    public function documento_protocolo_Insert( entity_projetos_documento_protocolo $entidade )
    {
        $ado = new ADO_projetos_documento_protocolo( $this->db );

        try
        {
            if (   $entidade->get_cd_usuario_cadastro()!="" ) 
            {
                $bResult = $ado->insert( $entidade );
			}
            $ado = null;
        }
        catch(Exception $e)
        {
            $ado = null;
            $bResult = false;
            echo( $e->getMessage() ); 
        }

        return $bResult;
    }

    public function envia_emails_Send( entity_projetos_envia_emails $entidade )
    {
        $ado = new ADO_projetos_envia_emails( $this->db );

        try
        {
            $bResult = $ado->insert( $entidade );
            $ado = null;
        }
        catch(Exception $e)
        {
            $ado = null;
            $bResult = false;
            echo( $e->getMessage() ); 
        }

        return $bResult;
    }

    public function envia_emails__get( $params )
    {
        $ado = new ADO_projetos_envia_emails( $this->db );

        try
        {
            $retult = $ado->get( $params );
            $ado = null;
        }
        catch( Exception $e )
        {
            $ado = null;
            $retult = false;
            echo( $e->getMessage() ); 
        }

        return $retult;
    }

    //
    //   AVALIAÇÃO - projetos.avaliacao
    //
    
	public function usuario_bloqueado_para_avaliacao($cd_usuario)
	{
		$ado = new ADO_projetos_usuarios_controledi( $this->db );
		
		return $ado->bloqueado_para_avaliacao(intval($cd_usuario));
	}
    
    public function avaliacao_Insert( entity_projetos_avaliacao_extended $entidade )
    {
        $ado = new ADO_projetos_avaliacao( $this->db );
        try
        {
            $bResult = $ado->insert( $entidade );
            $ado = null;
        }
        catch(Exception $e)
        {
            $ado = null;
            $bResult = false;
            echo( $e->getMessage() ); 
        }
        return $bResult;
    }

    public function avaliacao_LoadByPK( $entidade )
    {
        $ado = new ADO_projetos_avaliacao( $this->db );
        try
        {
            $result = $ado->load_by_PK( $entidade );
            $ado = null;
        }
        catch(Exception $e)
        {
            $ado = null;
            echo( $e->getMessage() ); 
        }

        return true;
    }

    public function documento_protocolo_UpdateOrdem( $entidade )
    {
        $ado = new ADO_projetos_documento_protocolo( $this->db );
        $result = null;
        try
        {
            $result = $ado->updateOrdem( $entidade );
            $ado = null;
        }
        catch(Exception $e)
        {
            $ado = null;
            echo( $e->getMessage() ); 
        }

        return $result;
    }
    public function avaliacao_Update($entidade)
    {
        $ado = new ADO_projetos_avaliacao( $this->db );
        $result = null;
        try
        {
            $result = $ado->update( $entidade );
            $ado = null;
        }
        catch(Exception $e)
        {
            $ado = null;
            echo( $e->getMessage() ); 
        }

        return $result;
    }
    public function avaliacao_UpdateCloseAndSend($entidade)
    {
        $ado = new ADO_projetos_avaliacao( $this->db );
        $result = null;
        try
        {
            $result = $ado->update_close_and_send( $entidade );
            $ado = null;
        }
        catch(Exception $e)
        {
            $ado = null;
            echo( $e->getMessage() ); 
        }

        return $result;
    }
	public function avaliacao_Fetch_by_usuario( $cd_usuario )
    {
        $ado = new ADO_projetos_avaliacao( $this->db );
        
        try
        {
            $bResult = $ado->fetch_by_usuario( $cd_usuario );
            $ado = null;
        }
        catch(Exception $e)
        {
            $ado = null;
            $bResult = false;
            echo( $e->getMessage() ); 
        }
        
        return $bResult;
    }

    /**
     * Busca os integrantes do comite de uma avaliação e 
     * responde em uma string com os apelidos separados por vírgula
     */
    public function avaliacao_comite_ToString( $dt_periodo, $cd_usuario_avaliado )
    {
        $ado = new ADO_projetos_avaliacao( $this->db );

        try
        {
            $bResult = $ado->comite_ToString( $dt_periodo, $cd_usuario_avaliado );
            $ado = null;
        }
        catch(Exception $e)
        {
            $ado = null;
            $bResult = false;
            echo( $e->getMessage() ); 
        }

        return $bResult;
    }

    /**
     * service_projetos.avaliacao_capa_FetchAll()
     * Cria array de objetos da estrutura de avaliação (avaliacao_capa, avaliacao, avaliacao_comite, outros)
     * pode ser filtrado por cd_avaliacao_capa ou cd_usuario_avaliado que são informados no objeto
     * entity_projetos_avaliacao_capa_extended recebido por parametro, também pode ser filtrado pela PK (cd_avaliacao_capa)
     * 
     * @param entity_projetos_avaliacao_capa_extended $avaliacao_capa Opcional, parametros para preencher: - set_cd_avaliacao_capa - set_cd_usuario_avaliado
     * 
     * @return Array[]entity_projetos_avaliacao_capa_extended Coleção de objetos de avaliacao_capa
     * 
     * @example controle_projetos/avaliacao_partial_lista.php Lista todas capas de avaliação onde o avaliador ou avaliado seja o usuário logado
     */
    public function avaliacao_capa_FetchAll( entity_projetos_avaliacao_capa_extended $avaliacao_capa )
    {
        $ado = new ADO_projetos_avaliacao_capa($this->db);

        try
        {
            $bResult = $ado->fetch_all( $avaliacao_capa );
            $ado = null;
        }
        catch(Exception $e)
        {
            $ado = null;
            $bResult = false;
            echo( $e->getMessage() ); 
        }

        return $bResult;
    }

    /**
     * service_projetos.avaliacao_capa__fetch_para_promocao()
     * Localiza na base de dados todas avaliações que foram confirmadas pelo superior
     * e atingiram a média para promoção horizontal ou vertical
     * e ainda não foram encaminhadas ao comitê
     * 
     * @return Array[]entity_projetos_avaliacao_capa_extended Coleção de objetos de avaliacao_capa
     * 
     * @example controle_projetos/avaliacao_config_partial_promocao.php - Lista todas capas de avaliação onde o avaliador ou avaliado seja o usuário logado
     */
    public function avaliacao_capa__fetch_para_promocao()
    {
        $ado = new ADO_projetos_avaliacao_capa($this->db);

        try
        {
            $bResult = $ado->fetch_para_promocao();
            $ado = null;
        }
        catch(Exception $e)
        {
            $ado = null;
            $bResult = false;
            echo( $e->getMessage() ); 
        }

        return $bResult;
    }

    public function avaliacao_capa_Insert( entity_projetos_avaliacao_capa_extended $entidade )
    {
        $ado = new ADO_projetos_avaliacao_capa( $this->db );
        if ($entidade->get_cd_avaliacao_capa()=="0" || $entidade->get_cd_avaliacao_capa()=="") 
        {
            $bResult = $ado->insert( $entidade );
            $ado = null;
        }
        return $bResult;
    }

    public function avaliacao_capa_Update( entity_projetos_avaliacao_capa_extended $entidade )
    {
        $ado = new ADO_projetos_avaliacao_capa( $this->db );
        $bResult = $ado->update( $entidade );
        $ado = null;
        return $bResult;
    }

    public function avaliacao_capa_Publicar( entity_projetos_avaliacao_capa_extended $entidade )
    {
        $ado = new ADO_projetos_avaliacao_capa( $this->db );

        try
        {
            $bResult = $ado->publicar( $entidade );
            $ado = null;
            
            $this->avaliacao_capa_envia_email_evento_34($entidade, 'FINALIZAR');
        }
        catch(Exception $e)
        {
            $ado = null;
            $bResult = false;
            echo( $e->getMessage() ); 
        }

        $ado = null;
        return $bResult;
    }

    /**
     * Envia email alertando sobre mudança de status e próximo passo do workflow
     * 
     * @param $_capa entity_projetos_avaliacao_capa_extended que será usado como filtro para carregar o mesmo objeto todo preenchido
     * @param $tipo  string Indique que email será enviado, serve para escolha do template de email. Os tipos podem ser:
     * 	             'SUPERIOR' 
     *               'COMITE' 
     *               'PUBLICAR' 
     *               'FINALIZAR'
     */
    public function avaliacao_capa_envia_email_evento_34( entity_projetos_avaliacao_capa_extended $_capa, $tipo )
    {
        $capa = new entity_projetos_avaliacao_capa_extended();
        $capa->set_cd_avaliacao_capa( $_capa->get_cd_avaliacao_capa() );
        
        // logger::insert("EVENTO 34", $_capa->get_cd_avaliacao_capa());
        
        $capas = $this->avaliacao_capa_FetchAll( $_capa );
        $capa = $capas[0];

        $envia_email = new entity_projetos_envia_emails();
        $ado = new ADO_projetos_envia_emails($this->db);

        $comite = ""; $virgula = "";
        foreach( $capa->comite as $componente )
        {
            if( isset($componente->avaliador) )
            {
                $comite .= $virgula . $componente->avaliador->get_guerra();
                if($componente->get_fl_responsavel()=="S")
                {
                    $comite .= " (responsável)";
                }
                $virgula = ", ";
            }
        }

		if ($tipo=="SUPERIOR")
        {
            if($capa->get_tipo_promocao()=="V") $tipo = 'Sv'; else $tipo = 'S';
            
        	$texto = util_email_text::get_text( util_email_text::$PROJETOS_SUPERIOR );
            
            $texto = str_replace("{nome}", $capa->avaliador->get_nome(), $texto);
            $texto = str_replace("{avaliado}", $capa->avaliado->get_nome(), $texto);
            $texto = str_replace("{gerencia}", $capa->avaliado->get_divisao(), $texto);
            $texto = str_replace("{data}", $capa->get_dt_criacao(), $texto);
            $texto = str_replace("{status}", "Encaminhado ao Superior", $texto);
            $texto = str_replace("{comite}", $comite, $texto);
            $texto = str_replace("{link}", "http://" . $_SERVER['SERVER_NAME'] . "/controle_projetos/avaliacao.php?tipo=".$tipo."&cd_capa=".$capa->get_cd_avaliacao_capa(), $texto);

            $envia_email->set_para( $capa->avaliador->get_usuario() . "@eletroceee.com.br" );
    		$envia_email->set_texto( $texto );
            $envia_email->set_assunto( "Avaliação de competências " . $capa->get_dt_periodo() . " - " . $capa->avaliado->get_guerra() );
            $envia_email->set_cc( "" );
            $envia_email->set_cco( "" );
            $envia_email->set_cd_evento( 34 );
            $ado->insert( $envia_email );
		}
		if ($tipo=="COMITE")
        {
            $separador = "";
            $comite_emails = "";
            foreach($capa->comite as $integrante)
            {
                $comite_emails .= $separador . $integrante->avaliador->get_usuario() . "@eletroceee.com.br";
                $separador = ";"; 
            }
            
            if($capa->get_tipo_promocao()=="V") $tipo = 'Cv'; else $tipo = 'C';
            
            $texto = util_email_text::get_text( util_email_text::$PROJETOS_COMITE );
            
            $texto = str_replace("{avaliado}", $capa->avaliado->get_nome(), $texto);
            $texto = str_replace("{gerencia}", $capa->avaliado->get_divisao(), $texto);
            $texto = str_replace("{data}", $capa->get_dt_criacao(), $texto);
            $texto = str_replace("{status}", "Encaminhado ao Comitê", $texto);
            $texto = str_replace("{comite}", $comite, $texto);
            $texto = str_replace("{link}", "http://" . $_SERVER['SERVER_NAME'] . "/controle_projetos/avaliacao.php?tipo=" . $tipo . "&cd_capa=".$capa->get_cd_avaliacao_capa(), $texto);

            $envia_email->set_para( $comite_emails );
            $envia_email->set_texto( $texto );
            $envia_email->set_assunto( "Avaliação de competências " . $capa->get_dt_periodo() . " - " . $capa->avaliado->get_guerra() );
            $envia_email->set_cc( "" );
            $envia_email->set_cco( "" );
            $envia_email->set_cd_evento( 34 );
            $ado->insert( $envia_email );
        }
        if ($tipo=="PUBLICAR")
        {
            #### MOVIDO PARA TRIGGER NA TABELA projeto.avaliacao ####
			/*
			if ($this->avaliacao_capa_todo_comite_avaliou($capa))
            {
                $texto = util_email_text::get_text( util_email_text::$PROJETOS_PUBLICAR );

                $comite_emails = "";
                $separador = "";

                if( $capa->get_avaliador_responsavel_comite()=="S" )
                {
                	$texto = str_replace("{nome}", $capa->avaliador->get_nome(), $texto);
                    $envia_email->set_para( $capa->avaliador->get_usuario()."@eletroceee.com.br" );
                }
                else
                {
	                foreach($capa->comite as $integrante)
	                {
	                    if ($integrante->get_fl_responsavel()=="S")
	                    {
	                        $texto = str_replace("{nome}", $integrante->avaliador->get_nome(), $texto);
	                        $envia_email->set_para( $integrante->avaliador->get_usuario()."@eletroceee.com.br" );
	                        break;
						}
	                }
                }

                $texto = str_replace("{avaliado}", $capa->avaliado->get_nome(), $texto);
                $texto = str_replace("{gerencia}", $capa->avaliado->get_divisao(), $texto);
                $texto = str_replace("{data}", $capa->get_dt_criacao(), $texto);
                $texto = str_replace("{status}", "Encaminhado ao Comitê", $texto);
                $texto = str_replace("{comite}", $comite, $texto);
                $texto = str_replace("{link}", "http://" . $_SERVER['SERVER_NAME'] . "/controle_projetos/avaliacao.php?tipo=R&cd_capa=".$capa->get_cd_avaliacao_capa(), $texto);

                $envia_email->set_texto( $texto );
                $envia_email->set_assunto( "Avaliação de competências " . $capa->get_dt_periodo() . " - " . $capa->avaliado->get_guerra() );
                $envia_email->set_cc( "" );
                $envia_email->set_cco( "" );
                $envia_email->set_cd_evento( 34 );
                $ado->insert( $envia_email );
			}
			*/
        }
    	if ($tipo=="FINALIZAR")
        {
            // Não deve enviar para promoção Vertical o email pro avaliado
            // informando a publicação do resultado pois a confirmação da
            // promoção depende de aprovação do executivo
        	if( $capa->get_tipo_promocao()=="H" )
            {
	        	#### MIGRADO PARA TRIGGER NA TABELA projetos.avaliacao_capa ####
				/*
				$texto = util_email_text::get_text( util_email_text::$PROJETOS_FINALIZAR );

	            $texto = str_replace("{nome}", $capa->avaliado->get_nome(), $texto);
	            $texto = str_replace("{link}", "http://" . $_SERVER['SERVER_NAME'] . "/controle_projetos/avaliacao.php?tipo=F&cd_capa=" . $capa->get_cd_avaliacao_capa(), $texto);
	
	            $envia_email->set_para( $capa->avaliado->get_usuario() . "@eletroceee.com.br" );
	    		$envia_email->set_texto( $texto );
	            $envia_email->set_assunto( "Avaliação de competências " . $capa->get_dt_periodo() . " - " . $capa->avaliado->get_guerra() );
	            $envia_email->set_cc( "" );
	            $envia_email->set_cco( "" );
	            $envia_email->set_cd_evento( 34 );
	            $ado->insert( $envia_email );
				*/
            }
            
			#### MIGRAR PARA TRIGGER ####
			/*
            // Enviar email pro Superior do resultado de cada integrante do Comitê
			
            if($capa->get_status()=="C")
            {
	            $texto=util_email_text::get_text( util_email_text::$PROJETOS_FINALIZAR_SUPERIOR );
	            $texto=str_replace("{nome}", $capa->avaliador->get_nome(), $texto);

	            $resultados="";
	            $aux=""; $aux2=0; $aux3=0; $aux4=0; $aux5=0; $aux6=0; $aux7=0;
	            $separador="";
				$hlp=new helper_avaliacao_resultado($this->db, $capa->get_cd_avaliacao_capa(),0);
				$hlp->load();
				$integrante = new entity_projetos_avaliacao_comite();
				$avaliacao = new entity_projetos_avaliacao_extended();
				$resultados = "\nCOMPETÊNCIAS INSTITUCIONAIS: \n\n";
				foreach($capa->comite as $integrante)
				{
					foreach($hlp->capa->avaliacoes as $avaliacao)
				    {
						if( $avaliacao->get_cd_usuario_avaliador()==$integrante->get_cd_usuario_avaliador() )
				        {
				        	$aux4++;
							$hlp->load_valores( $avaliacao->get_cd_avaliacao() );
			                $GRAU = $hlp->get_val_ci();
			                break;
						}
			    	}
					$resultados .= $integrante->avaliador->get_nome() . " : " . $GRAU . "\n";
			    	$aux2+=number_format($GRAU,2);
			    	$separador = "+";
				}
	        	foreach($hlp->capa->avaliacoes as $avaliacao)
			    {
			    	if( $avaliacao->get_tipo()=="S" )
			        {
			        	$aux4++;
						$hlp->load_valores( $avaliacao->get_cd_avaliacao() );
		                $aux3 = number_format($hlp->get_val_ci(),2);
		                $aux5 = number_format($hlp->get_val_esc(),2);
		                $aux6 = number_format($hlp->get_val_ce(),2);
		                $aux7 = number_format($hlp->get_val_resp(),2);
		                $resultados .= $capa->avaliador->get_nome() . " (superior) : " . $aux3 . "\n";
		                break;
					}
			    }
			    $aux2+=floatval($aux3);
				$aux2=number_format($aux2,2);
			    $aux = "Total: " . $aux2;
				$aux .= "\nMédia: " . ($aux2/$aux4);
				$aux .= "\n\nESCOLARIDADE:\n\n " . $aux5;
				$aux .= "\n\nCOMPETÊNCIAS ESPECÍFICAS:\n\n " . $aux6;
				$aux .= "\n\nRESPONSABILIDADES:\n\n " . $aux7;
				$aux .= "\n\n\nTOTAIS: ";
				$media1 = number_format((( ($aux2/$aux4)+$aux5 )/2), 2);
				$aux .= "\n\nMÉDIA DE COMP INST COM ESCOLARIDADE (REPRESENTA 40% NA MÉDIA FINAL PONDERADA):\n\n " . $media1;
				$media2 = number_format((( $aux6+$aux7 )/2), 2);
				$aux .= "\n\nMÉDIA DE COMP ESPEC COM RESPONSABILIDADE (REPRESENTA 60% NA MÉDIA FINAL PONDERADA):\n\n " . $media2;
				$aux .= "\n\nMÉDIA FINAL PONDERADA:\n\n " . number_format( ( (($media1*40) + ($media2*60)) / 100 ) , 2 );

				$resultados .= $aux;
	            $texto = str_replace("{resultados}", $resultados, $texto);

	            $envia_email->set_para( $capa->avaliador->get_usuario() . "@eletroceee.com.br" );
	    		$envia_email->set_texto( $texto );
	            $envia_email->set_assunto( "Avaliação de competências " . $capa->get_dt_periodo() . " - " . $capa->avaliado->get_guerra() );
	            $envia_email->set_cc( "" );
	            $envia_email->set_cco( "" );
	            $envia_email->set_cd_evento( 34 );
	            $ado->insert( $envia_email );
            }
			*/
		}
    }

    private function avaliacao_capa_todo_comite_avaliou($_capa)
    {
        $contador=0;
        // Para cada integrante do comite verifica se tem uma avaliação concluída correspondente
        foreach( $_capa->comite as $integrante )
        {
            foreach( $_capa->avaliacoes as $avaliacao )
            {
                if($integrante->get_cd_usuario_avaliador()==$avaliacao->get_cd_usuario_avaliador() 
                   && $avaliacao->get_dt_conclusao()!="")
                {
                    $contador++;
                }
            }
        }

        // retorna verdadeiro se o nro de integrantes for o mesmo que o número de avaliações encontradas
        if($contador==sizeof($_capa->comite))
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    
    public function projetos_atividades_fetch_totais( $ano, $divisao )
    {
        $resultado = null;
        $ado = new ADO_projetos_atividades( $this->db );
        return $ado->fetch_atividades_totais_mes_ano($ano, $divisao);
    }
    
    /**
     * ADO_projetos_atividades.fetch_menor_ano_atividade()
     * Retorna menor ano de cadastro entre os registros de atividade
     */
    public function projetos_atividades_fetch_menor_ano($divisao)
    {
        $ado = new ADO_projetos_atividades( $this->db );
        return $ado->fetch_menor_ano_atividade($divisao);
    }
   
    public function expansao_empresas_instituicoes_comunidades_fetch_nao_incluidas( $empresa )
    {
        $ado = new ADO_expansao_empresas_instituicoes($this->db);
        try
        {
            $return = $ado->fetch_comunidade_nao_incluida( $empresa );
        }
        catch(Exception $e)
        {
            $ado = null;
            $return = false;
            echo( $e->getMessage() ); 
        }
        return $return;

    }
    
    public function expansao_empresas_instituicoes_comunidades_Insert( entity_expansao_empresas_instituicoes_comunidades $entidade )
    {
        $ado = new ADO_expansao_empresas_instituicoes($this->db);
        try
        {
            $return = $ado->insert_comunidade( $entidade );
        }
        catch(Exception $e)
        {
            $ado = null;
            $return = false;
            echo( $e->getMessage() ); 
        }
        return $return;
    }

    public function expansao_empresas_instituicoes_comunidades_Delete( entity_expansao_empresas_instituicoes_comunidades $entidade )
    {
        $ado = new ADO_expansao_empresas_instituicoes($this->db);
        try
        {
            $return = $ado->delete_comunidade( $entidade );
        }
        catch(Exception $e)
        {
            $ado = null;
            $return = false;
            //echo( $e->getMessage() ); 
        }
        return $return;
    }
    
    /**
     * service_projetos.expansao_empresas_instituicoes_comunidades_FetchAll( $cd_empresa )
     * Busca na base informações de comunidades relacionadas a empresa e retorna array do objeto
     * entity_expansao_empresas_instituicoes_comunidades_extended
     * 
     * @param $empresa Código da empresa, parametro obrigatório
     * @return array()entity_expansao_empresas_instituicoes_comunidades_extended Entidade populada
     */
    public function expansao_empresas_instituicoes_comunidades_FetchAll($empresa)
    {
        $ado = new ADO_expansao_empresas_instituicoes($this->db);
        try
        {
            $return = $ado->fetch_all_comunidade( $empresa );
        }
        catch(Exception $e)
        {
            $ado = null;
            $return = false;
            //echo( $e->getMessage() ); 
        }
        return $return;
    }
    
    /**
     * service_projetos.public_listas_load_by_pk( entity_public_listas )
     * Carrega no objeto passado por parametro as informações da tabela entidade.
     * A busca é pela PK portanto retorna o objeto e não um array.
     * 
     * @param entity_public_listas $entidade Objeto por referencia com o atributo codigo preenchido que será populado para retorno
     * @return bool true se sucesso, false se falha
     */
    public function public_listas_load_by_pk( entity_public_listas $entidade )
    {
        $ado = new ADO_public_listas($this->db);
        try
        {
            $ado->load( $entidade );
            $return = true;
        }
        catch(Exception $e)
        {
            $ado = null;
            $return = false;
            //echo( $e->getMessage() ); 
        }
        return $return;
    }
    public function public_listas_alterar_descricao( entity_public_listas $entidade )
    {
        $ado = new ADO_public_listas($this->db);
        try
        {
            $ado->change( $entidade );
            $return = true;
        }
        catch(Exception $e)
        {
            $ado = null;
            $return = false;
            //echo( $e->getMessage() ); 
        }
        return $return;
    }

    public function public_participantes_confirm_id_md5( entity_participantes & $entidade )
    {
        $ado = new ADO_participantes($this->db);
        try
        {
            $ado->load_by_id_md5( $entidade );
            $return = true;
        }
        catch(Exception $e)
        {
            $ado = null;
            $return = false;
        }
        return $return;
    }

    public function public_taxas_get_taxa( $cd_indexador, $data_ref )
    {
        $ado = new ADO_public_taxas($this->db);
        try
        {
            $return = $ado->get_taxa( $cd_indexador, $data_ref );
            $ado = null;
        }
        catch(Exception $e)
        {
            $ado = null;
            $return = 0;
        }
        return $return;
    }

    public function public_pacotes_get_valor_bdl( $cd_pacote, $cd_plano, $cd_empresa, $data_ref )
    {
        $ado = new ADO_public_pacotes($this->db);
        try
        {
            $return = $ado->get_valor_bdl( $cd_pacote, $cd_plano, $cd_empresa, $data_ref );
            $ado = null;
        }
        catch(Exception $e)
        {
            $ado = null;
            $return = 0;
        }
        return $return;
    }
    
    /**
     * public_participantes_get_cobertura_de_risco
     * 
     * Devolve um array com os riscos do participante
     * 
     * @param entity_participantes Entidade com os atributos cd_empresa, cd_registro_empregado e seq_dependencia preenchidos
     * @return array( "nome_risco", "vl_risco" ) Coleção com os riscos do participantes devolvido em array com duas colunas
     */
    public function public_participantes_get_cobertura_de_risco( entity_participantes $participante )
    {
        $ado = new ADO_participantes($this->db);
        try
        {
            $return = $ado->get_cobertura_de_risco( $participante );
            $ado = null;
        }
        catch(Exception $e)
        {
            $ado = null;
            $return = 0;
            throw new Exception( 'Erro ao consultar cobertura de risco' );
        }
        return $return;
    }
    
    public function expansao_inscritos_get_pacote( $cd_empresa, $cd_registro_empregado )
    {
        $ado = new ADO_expansao_inscritos($this->db);
        try
        {
            $return = $ado->get_pacote( $cd_empresa, $cd_registro_empregado );
            $ado = null;
        }
        catch(Exception $e)
        {
            $ado = null;
            $return = 0;
        }
        return $return;
    }
    
    public function oracle_get_custo_administrativo( $cd_empresa, $valor )
    {
        $ado = new ADO_oracle_functions($this->db);
        try
        {
            $return = $ado->get_custo_administrativo( $cd_empresa, $valor );
            $ado = null;
        }
        catch(Exception $e)
        {
            $ado = null;
            $return = 0;
        }
        return $return;
    }
    
    public function avaliacao_generate_queries_to_transaction($cd_avaliacao, $institucionais, $especificas, $responsabilidades )
    {
        $ado = new ADO_projetos_avaliacao($this->db);
        $rst = $ado->generate_queries__comp_inst__insert($cd_avaliacao, $institucionais );
        $rst = $ado->generate_queries__comp_espec__insert($cd_avaliacao, $especificas );
        $rst = $ado->generate_queries__resp__insert($cd_avaliacao, $responsabilidades );
        
        // $ret = $ado->execute_queries( $sql );
        
        $ado = null;

        return $rst;
    }
    
    public function usuario_controledi__fetch_by_name( $nome )
    {
        $ado = new ADO_projetos_usuarios_controledi($this->db);
        try
        {
            $return = $ado->fetch_by_name( $nome );
            $ado = null;
        }
        catch(Exception $e)
        {
            $ado = null;
            $return = 0;
        }
        return $return;
    }
    
    public function avaliacao_capa__insert_integrante_comite(entity_projetos_avaliacao_comite & $entidade)
    {
        $ado = new ADO_projetos_avaliacao_capa($this->db);
        try
        {
        	$return = $ado->insert_integrante_comite( $entidade );
            $ado = null;
        }
        catch(Exception $e)
        {
            $ado = null;
            $return = 0;
            //echo $e->getMessage();
        }
        return $return;
    }
    
    public function avaliacao_capa__delete_integrante_comite( $cd_avaliacao_comite )
    {
        $ado = new ADO_projetos_avaliacao_capa( $this->db );
        try
        {
            $return = $ado->delete_integrante_comite( $cd_avaliacao_comite );
            $ado = null;
        }
        catch(Exception $e)
        {
            $ado = null;
            $return = 0;
            //echo $e->getMessage();
        }
        return $return;
    }

    public function avaliacao_capa__encaminhar_ao_comite( $cd_avaliacao_capa )
    {
        $ado = new ADO_projetos_avaliacao_capa( $this->db );
        try
        {
            $return = $ado->encaminhar_ao_comite( $cd_avaliacao_capa );
            
            $capa = new entity_projetos_avaliacao_capa_extended();
            $capa->set_cd_avaliacao_capa($cd_avaliacao_capa);
            /*$capas = $this->avaliacao_capa_FetchAll( $capa );
            $capa = $capas[0];*/
            
            $this->avaliacao_capa_envia_email_evento_34($capa, "COMITE");
            $ado = null;
        }
        catch(Exception $e)
        {
            $ado = null;
            $return = 0;
            //echo $e->getMessage();
        }
        return $return;
    }

    public function avaliacao_capa__encaminhar_ao_administrador( $cd_avaliacao_capa )
    {
        $ado = new ADO_projetos_avaliacao_capa( $this->db );
        try
        {
            $return = $ado->encaminhar_ao_administrador( $cd_avaliacao_capa );
            $ado = null;
        }
        catch(Exception $e)
        {
            $ado = null;
            $return = 0;
            //echo $e->getMessage();
        }
        return $return;
    }
    
    public function familias_cargos__fetch_all()
    {
        $ado = new ADO_projetos_familias_cargos( $this->db );
        try
        {
            $return = $ado->fetch_all();
            $ado = null;
        }
        catch(Exception $e)
        {
            $ado = null;
            $return = 0;
            //echo $e->getMessage();
        }
        return $return;
    }
    
    public function familias_cargos__salvar_matriz( $matriz )
    {
        $ado = new ADO_projetos_familias_cargos( $this->db );
        try
        {
            $return = $ado->salvar_matriz( $matriz );
            $ado = null;
        }
        catch(Exception $e)
        {
            $ado = null;
            $return = 0;
            //echo $e->getMessage();
        }
        return $return;
    }

    public function familias_cargos__fetch_matriz( $cd_familias_cargos, $faixa )
    {
        $ado = new ADO_projetos_familias_cargos( $this->db );
        try
        {
            $return = $ado->fetch_matriz( $cd_familias_cargos, $faixa );
            $ado = null;
        }
        catch(Exception $e)
        {
            $ado = null;
            $return = 0;
            //echo $e->getMessage();
        }
        return $return;
    }

    /**
     * Carrega toda tabela de matriz_salarial
     * 
     * @return array()entity_projetos_matriz_salarial_extended
     */
    public function familias_cargos__fetch_matriz_all()
    {
        $ado = new ADO_projetos_familias_cargos( $this->db );
        try
        {
            $return = $ado->fetch_matriz_all();
            $ado = null;
        }
        catch(Exception $e)
        {
            $ado = null;
            $return = 0;
            //echo $e->getMessage();
        }
        return $return;
    }
    
    /**
     * Carrega um array de objetos helper_usuarios_agrupados_por_divisao 
     * com todas as divisões e usuários existentes
     * 
     * @return array()helper_usuarios_agrupados_por_divisao Coleção do objeto com todos os registros encontrados na base de dados 
     */
    public function usuarios_controledi__listar_agrupando_por_gerencia()
    {
        $ado = new ADO_projetos_usuarios_controledi( $this->db );
        try
        {
            $return = $ado->listar_agrupando_por_gerencia();
            $ado = null;
        }
        catch(Exception $e)
        {
            $ado = null;
            $return = 0;
            //echo $e->getMessage();
        }
        return $return;
    }

    public function usuarios_controledi__salvar_matriz(entity_projetos_usuario_matriz $usuario_matriz)
    {
        $ado = new ADO_projetos_usuarios_controledi( $this->db );
        try
        {
            $return = $ado->salvar_matriz($usuario_matriz);
            $ado = null;
        }
        catch(Exception $e)
        {
            $ado = null;
            $return = 0;
            //echo $e->getMessage();
        }
        return $return;
    }

    public function familias_cargos__fetch_faixas()
    {
        $ado = new ADO_projetos_familias_cargos( $this->db );
        try
        {
            $return = $ado->fetch_faixas();
            $ado = null;
        }
        catch(Exception $e)
        {
            $ado = null;
            $return = 0;
            //echo $e->getMessage();
        }
        return $return;
    }
    
    /**
     * Definição do responsável pelo comite
     * 
     * @param int $cd_avaliacao_comite Código do integrante do comite da avaliação que foi indicado como responsável. Esse parametro originalmente era único e obrigatório, por compatibilidade segue obrigatório mas só será usado se a $origem for "comite"
     * 
     * @param string $origem "comite" ou "superior". Indica se o integrante do comitê é o superior imediato ou é literalmente um dos indicados do comite
     * 
     * @param int @cd_capa Código da capa de avaliação que será usada caso a $origem seja "superior"
     * 
     * @return boolean Sucesso ou Falha para true ou false
     */
    public function avaliacao_capa__definir_responsavel_comite($cd_avaliacao_comite, $origem="comite", $cd_capa=0)
    {
        $ado = new ADO_projetos_avaliacao_capa( $this->db );
        try
        {
            $return = $ado->definir_responsavel_comite($cd_avaliacao_comite, $origem, $cd_capa);
            $ado = null;
        }
        catch(Exception $e)
        {
            $ado = null;
            $return = 0;
            //echo $e->getMessage();
        }
        return $return;
    }

    
    public function consultas__sinpro_tipo_pagamento_boleto__get_tipo($re, $comp)
    {
        $ado = new ADO_consultas( $this->db );
        try
        {
            $return = $ado->sinpro_tipo_pagamento_boleto__get_tipo( $re, $comp );
            $ado = null;
        }
        catch(Exception $e)
        {
            $ado = null;
            $return = 0;
            //echo $e->getMessage();
        }
        return $return;
    }
    
    public function projetos__avaliacao_controle__get_dt_fechamento($dt_periodo)
    {
        $ado = new ADO_projetos_avaliacao_controle( $this->db );
        try
        {
            $return = $ado->get_dt_fechamento( $dt_periodo );
            $ado = null;
        }
        catch(Exception $e)
        {
            $ado = null;
            $return = 0;
            //echo $e->getMessage();
        }
        return $return;
    }
    
    public function consultas__sinpro_tipo_pagamento_boleto__get( $emp, $re, $seq )
    {
        $ado = new ADO_consultas( $this->db );
        try
        {
            $return = $ado->sinpro_tipo_pagamento_boleto__get( $emp, $re, $seq );
            $ado = null;
        }
        catch(Exception $e)
        {
            $ado = null;
            $return = 0;
            // echo $e->getMessage();
        }
        return $return;
    }

    /**
     * Cria array de objetos da estrutura de avaliação (avaliacao_capa, avaliacao, avaliacao_comite, outros)
     * pode ser filtrado por cd_avaliacao_capa ou cd_usuario_avaliado que são informados no objeto
     * entity_projetos_avaliacao_capa_extended recebido por parametro, também pode ser filtrado pela PK (cd_avaliacao_capa)
     * 
     * @param helper__avaliacao_capa__fetch_by_filter Conjunto de filtros para executar na query principal
     * 
     * @return Array[]helper__avaliacao_capa__fetch_by_filter__entity Coleção de objetos da query executada
     */
    public function projetos__avaliacao_capa__fetch_by_filter( helper__avaliacao_capa__fetch_by_filter__filter $helper )
    {
    	$ado = new ADO_projetos_avaliacao_capa($this->db);
    	try
    	{
    		$return = $ado->fetch_by_filter( $helper );
    	}
    	catch(Exception $e)
    	{
    		$ado = null;
    		$return = 0;
    		// echo $e->getMessage();
    	}

    	return $return;
    }

    public function projetos__avaliacao_capa__listar_todas_avaliadas_pelo_superior()
    {
    	$ado = new ADO_projetos_avaliacao_capa($this->db);
    	try
    	{
    		$return = $ado->listar_todas_avaliadas_pelo_superior();
    	}
    	catch(Exception $e)
    	{
    		$ado = null;
    		$return = 0;
    	}

    	return $return;
    }

    public function avaliacao_capa__media_geral__set( $cd_avaliacao_capa, $GRAU )
    {
    	$ado = new ADO_projetos_avaliacao_capa( $this->db );
    	try
    	{
    		$entidade = new entity_projetos_avaliacao_capa();
    		$entidade->set_cd_avaliacao_capa( $cd_avaliacao_capa );
    		$entidade->set_media_geral( $GRAU );
    		$return = $ado->update( $entidade );
    	}
    	catch( Exception $e )
    	{
    		$ado = null;
    		$return = 0;
    	}

    	return $return;
    }

    /**
     * Criar array com resultados da query
     * 
     * @param $params Array com todos parametros necessários para formar a consulta
     *            $params['mes'] 
     *            $params['ano']
     * @return array(new entity_public_controle_geracao_cobranca()) Coleção do objeto entity_public_controle_geracao_cobranca
     *         OU
     *         retorna FALSE se ocorrer algum erro na consulta
     */
    public function contribuicao_senge__controle_geracao_cobranca__get( $tipo, $params )
    {
    	$helper = new helper_ado_contribuicao_senge( $this->db );
    	try
    	{
    		if( $tipo=="confirmacao" )
    		{
    			$return = $helper->controle_geracao_cobranca__confirmacao__get( $params );
    		}
    		elseif($tipo=="geracao")
    		{
    			$return = $helper->controle_geracao_cobranca__geracao__get( $params );
    		}
    		elseif($tipo=="internet")
    		{
    			$return = $helper->controle_geracao_cobranca__internet__get( $params );
    		}
    	}
    	catch( Exception $e )
    	{
    		$ado = null;
    		$return = false;
    	}

    	return $return;
    }

    /**
     * Totais do primeiro pagamento devolve um array com um Count e um calculo baseado em SUMs da query formada
     * pelas tabelas inscritos_internet, controle_geracao_cobranca, taxas e pacotes
     * 
     * @param $params array com filtros utilizados na query
     *                $param['mes']
     *                $param['ano']
     * 
     * @return array(contador, valor) Array com os valores retornados na query
     *         OU
     *         retorna FALSE se ocorrer algum erro na consulta
     */
    public function contribuicao_senge__totais__get( $tipo, $params )
    {
    	$helper = new helper_ado_contribuicao_senge( $this->db );
    	try
    	{
    		if($tipo=="primeiro_pagamento")
    		{
    			$return = $helper->totais_primeiro_pagamento__get( $params );
    		}
    		elseif($tipo=="bdl")
    		{
    			$return = $helper->totais_bdl__get( $params );
    		}
    		elseif($tipo=="arrecadacao")
    		{
    			$return = $helper->totais_arrecadacao__get( $params );
    		}
    		elseif($tipo=="mensal internet")
    		{
    			$return = $helper->totais_mensal_internet__get( $params );
    		}
    		elseif($tipo=="mensal bdl")
    		{
    			$return = $helper->totais_mensal_bdl__get( $params );
    		}
    		elseif($tipo=="mensal arrecadacao")
    		{
    			$return = $helper->totais_mensal_arrecadacao__get( $params );
    		}
    	}
    	catch( Exception $e )
    	{
    		$ado = null;
    		$return = false;
    	}

    	return $return;
    }
    
    /**
     * Quantidade de emails a enviar para primeiro pagamento
     * considerando apenas registros que contém email
     * 
     * @param $params Array com colunas 'mes' e 'ano'
     *        $params['mes'] : mes de competencia
     *        $params['ano'] : ano de competencia
     */
    public function contribuicao_senge__emails_enviar_primeiro__get( $params )
    {
    	$helper = new helper_ado_contribuicao_senge( $this->db );
    	try
    	{
    		$return = $helper->total_emails_enviar_primeiro( $params );
    	}
    	catch( Exception $e )
    	{
    		$ado = null;
    		$return = false;
    	}

    	return $return;
    }

    public function contribuicao_senge__emails_enviar_mensal__get( $params )
    {
    	$helper = new helper_ado_contribuicao_senge( $this->db );
    	try
    	{
    		$return = $helper->total_emails_enviar_mensal( $params );
    	}
    	catch( Exception $e )
    	{
    		$ado = null;
    		$return = false;
    	}

    	return $return;
    }
    
    public function avaliacao_aspecto__insert( entity_projetos_avaliacao_aspecto $entidade )
    {
    	$ado = new ADO_projetos_avaliacao( $this->db );
    	try
    	{
    		$return = $ado->avaliacao_aspecto__insert( $entidade );
    	}
    	catch( Exception $e )
    	{
    		$ado = null;
    		$return = false;
    	}

    	return $return;
    }

    public function avaliacao_aspecto__update( entity_projetos_avaliacao_aspecto $entidade )
    {
    	$ado = new ADO_projetos_avaliacao( $this->db );
    	try
    	{
    		$return = $ado->avaliacao_aspecto__update( $entidade );
    	}
    	catch( Exception $e )
    	{
    		$ado = null;
    		$return = false;
    	}

    	return $return;
    }
    
    public function avaliacao_aspecto__delete( $pk )
    {
    	$ado = new ADO_projetos_avaliacao( $this->db );
    	try
    	{
    		$return = $ado->avaliacao_aspecto__delete( $pk );
    	}
    	catch( Exception $e )
    	{
    		$ado = null;
    		$return = false;
    	}

    	return $return;
    }
    
    public function avaliacao_aspecto__load_by_pk( entity_projetos_avaliacao_aspecto $entity )
    {
    	$ado = new ADO_projetos_avaliacao( $this->db );
    	try
    	{
    		$return = $ado->avaliacao_aspecto__load_by_pk( $entity );
    	}
    	catch( Exception $e )
    	{
    		$ado = null;
    		$return = false;
    	}

    	return $return;
    }
    
    public function avaliacao_aspecto__clone( $origem, $destino )
    {
    	$ado = new ADO_projetos_avaliacao( $this->db );
    	try
    	{
    		$return = $ado->avaliacao_aspecto__clone( $origem, $destino );
    	}
    	catch( Exception $e )
    	{
    		$ado = null;
    		$return = false;
    	}

    	return $return;
    }

    public function avaliacao__avaliador__clone($cd_avaliacao)
    {
    	$ado = new ADO_projetos_avaliacao( $this->db );
    	try
    	{
    		$return = $ado->avaliador__clone( $cd_avaliacao );
    	}
    	catch( Exception $e )
    	{
    		$ado = null;
    		$return = false;
    	}

    	return $return;
    }

    public function avaliacao_capa__modificar_avaliacao( $acao, $cd_avalicao_capa )
    {
    	$ado = new ADO_projetos_avaliacao_capa( $this->db );
    	try
    	{
    		if($acao=='REABRIR')
    		{
    			$return = $ado->reabrir_avaliacao( $cd_avalicao_capa );
    		}
    		elseif($acao=='ENCERRAR')
    		{
    			$return = $ado->encerrar_avaliacao( $cd_avalicao_capa );
    		}
    		elseif($acao=='EXCLUIR')
    		{
    			$return = $ado->excluir_avaliacao( $cd_avalicao_capa );
    		}
    	}
    	catch( Exception $e )
    	{
    		$ado = null;
    		$return = false;
    	}

    	return $return;    	
    }

    public function public_contribuicoes_programadas__get_valor(entity_participantes $participante)
    {
    	$ado = new ADO_public_contribuicoes_programadas( $this->db );
    	try
    	{
    		$return = $ado->get_valor( $participante->get_cd_empresa(), $participante->get_cd_registro_empregado(), $participante->get_seq_dependencia() );
    	}
    	catch( Exception $e )
    	{
    		$ado = null;
    		$return = 0;
    	}

    	return $return;    	
    }

    public function sinpro__ja_realizou_primeiro_pagamento($participante)
    {
    	$ado = new helper_ado_contribuicao_sinpro($this->db);
    	try
    	{
    		$return = $ado->ja_realizou_primeiro_pagamento( $participante->get_cd_empresa(), $participante->get_cd_registro_empregado(), $participante->get_seq_dependencia() );
    	}
    	catch( Exception $e )
    	{
    		$ado = null;
    		$return = false;
    	}
    	
    	return $return;
    }

    public function sinpro__participante_com_debito_conta($participante)
    {
    	$ado = new helper_ado_contribuicao_sinpro($this->db);
    	try
    	{
    		$return = $ado->participante_com_debito_conta( $participante->get_cd_empresa(), $participante->get_cd_registro_empregado(), $participante->get_seq_dependencia() );
    	}
    	catch( Exception $e )
    	{
    		$ado = null;
    		$return = false;
    	}
    	
    	return $return;
    }

    /**
     * Realiza teste para verificar se o processo de avaliação já está aberto na data corrente.
     * 
     * @return bool True se processo está aberto. False se processo não está aberto.
     */
    public function avaliacao__is_open( entity_projetos_avaliacao_controle & $controle )
    {
    	$ado = new ADO_projetos_avaliacao_controle( $this->db );
    	try
    	{
    		$return = $ado->is_open();
    		$controle->dt_periodo = date('Y');
    		$ado->load_by_year( $controle );
    	}
    	catch( Exception $e )
    	{
    		$ado = null;
    		$return = false;
    	}
    	
    	return $return;
    }
    
    public function usuarios__escolaridades_por_usuario__get($cd_usuario)
    {
    	$ado = new ADO_projetos_usuarios_controledi( $this->db );
    	$return = new hashtable_collection();
    	try
    	{
    		$return = $ado->escolaridades_por_usuario__get($cd_usuario);
    	}
    	catch( Exception $e )
    	{
    		$ado = null;
    	}
    	
    	return $return;
    }

    public function contribuicao_sinpro__participante_primeiro_pagamento($RE, $SEQ)
    {
    	$ado = new helper_ado_contribuicao_sinpro($this->db);
    	try
    	{
    		$return = $ado->participante_primeiro_pagamento($RE, $SEQ);
    	}
    	catch( Exception $e )
    	{
    		$ado = null;
    	}
    	
    	return $return;
    }
    
    /**
     * Retorna classe hashtable_collection com re e competencia criptografada com md5 usando função definida na base
     * 
     * @param array $args Array formado por EMP, RE, SEQ, MES, ANO
     * 
     * @return hashtable_collection Coleção com duas posições: 
     * 			re - combinação extraída do postgres funcoes.cripto_re() dos parametros passados em $args
     * 		  comp - combinação extraída do postgres funcoes.cripto_mes_ano() dos parametros passados em $args
     */
    public function contribuicao__re_competencia_md5($args)
    {
    	$ado = new helper_ado_contribuicao_sinpro($this->db);
    	try
    	{
    		$return = $ado->re_competencia_md5($args);
    	}
    	catch( Exception $e )
    	{
    		$ado = null;
    	}
    	
    	return $return;
    }
    
    public function public_bloqueto__competencias_a_pagar__get($EMP, $RE, $SEQ)
    {
    	$ado = new ado_public_bloqueto($this->db);
    	try
    	{
    		$return = $ado->competencias_a_pagar__get($EMP, $RE, $SEQ);
    	}
    	catch( Exception $e )
    	{
    		$ado = null;
    	}
    	
    	return $return;
    }
    
    public function contribuicao__participante_pagamento_adicional($EMP, $RE, $SEQ)
    {
    	$ado = new helper_ado_contribuicao_sinpro($this->db);
    	try
    	{
    		$return = $ado->participante_pagamento_adicional($EMP, $RE, $SEQ);
    	}
    	catch( Exception $e )
    	{
    		$ado = null;
    	}
    	
    	return $return;
    }

	/**
     * O processo de avaliação para ser completo precisa atender a uma regra definida em 10/6/2008 pelo Gilberto Soares:
     * O Avaliado deve ter sido admitido antes do fechamento do processo de avaliação do ano anterior,
     * caso contrário ele só poderá preencher as expectativas.
     */        
    public function avaliacao__processo_completo( entity_projetos_avaliacao_capa_extended $capa)
	{
		$year = date('Y')-1;
		$data_fechamento = $this->projetos__avaliacao_controle__get_dt_fechamento( $year );

		$a_aux = explode( '/',  $capa->avaliado->usuario_matriz->dt_admissao );
		if( sizeof($a_aux)==3 )
		{
            $dt_admissao = $a_aux[2] . $a_aux[1] . $a_aux[0];
            $a_aux = explode( '/',  $data_fechamento );
            $dt_fechamento = $a_aux[2] . $a_aux[1] . $a_aux[0];
            return ( $dt_admissao < $dt_fechamento );
		}
		else
		{
            return false;
		}
	}

	/**
	 * Resgatar a data limite sem encargos ?em 2 formatos?
	 * ano e mes de competencia
	 * soma do valor_lancamento
	 * e caso a data atual seja superior a data limite sem encargos, retorna a soma de vlr_encargo
	 * para um determinado participante em um determinado mes/ano de competencia
	 * 
	 * @param array $args com os atributos cd_empresa, cd_registro_empregado, seq_dependencia, mes, ano
	 * por exemplo: 
	 * $args = array(
	 *		'cd_empresa'=>enum_public_patrocinadoras::SINPRO,
	 *		'cd_registro_empregado'=>86,
	 *		'seq_dependencia'=>0,
	 *		'mes'=>9,
	 *		'ano'=>2008,
	 *      'comp_md5'=>'5145b3178e453cee06fc378e37609bdc'
	 * 		'codigo_lancamento'=>array(enum_public_codigos_cobrancas::CONTRIBUICAO_SINPRORS_PREV)
	 * 
	 * );
	 * 
	 * @return entity_public_bloqueto Objeto preenchido com consulta ao banco de dados com critérios que atendam a descrição desse método
	 */
	public function bloqueto__infos_para_emissao_pagamento__get($args)
	{
		$return = new entity_public_bloqueto();
		$ado = new ado_public_bloqueto($this->db);
    	try
    	{
    		$return = $ado->infos_para_emissao_pagamento__get($args);
    	}
    	catch( Exception $e )
    	{
    		//echo $e->getMessage();
    		$ado = null;
    	}

    	return $return;
	}

	public function atendimento_recadastro__fetchByFilter( helper_recadastro_gap__fetch_by_filter $filtro )
	{
        $ado = new ADO_projetos_atendimento_recadastro( $this->db );

        try
        {
            $bResult = $ado->fetchByFilter( $filtro );
            $ado = null;
        }
        catch(Exception $e)
        {
            $ado = null;
            $bResult = false;
            echo( $e->getMessage() ); 
        }

        return $bResult;
	}
	
	/**
	 * Método para persistencia na tabela atendimento recadastro
	 * de acordo com preenchimento do parametro será desviado para
	 * comando de INSERT ou UPDATE na tabela
	 *
	 * @param entity_projetos_atendimento_recadastro Valores para persistir preenchidos, 
	 * o campo PK se preenchido, redireciona para comando de UPDATE, se não estiver preenchido
	 * redireciona para comando de INSERT na tabela.
	 * 
	 * @return int 0=sucesso 1=falha genérica 2=falha, chave única violada
	 */
	public function atendimento_recadastro__insert( entity_projetos_atendimento_recadastro $entidade )
	{
        $ado = new ADO_projetos_atendimento_recadastro( $this->db );

        try
        {
            /*$entidade->cd_usuario_recebimento = String::IfBlankReturn( $entidade->cd_usuario_recebimento, "null" );
            $entidade->cd_empresa = String::IfBlankReturn( $entidade->cd_empresa, "null" );
            $entidade->cd_registro_empregado = String::IfBlankReturn( $entidade->cd_registro_empregado, "null" );
            $entidade->seq_dependencia = String::IfBlankReturn( $entidade->seq_dependencia, "null" );*/
            if ($entidade->cd_atendimento_recadastro=="0" OR $entidade->cd_atendimento_recadastro=="") 
            {
                $aux = $ado->insert( $entidade );
                $ado = null;
			}
            else
            {
                $aux = $ado->update( $entidade );
                $ado = null;
            }
			if($aux) $bResult = 0;
        }
        catch(Exception $e)
        {
            $ado = null;
            $bResult = 1;

            if( strpos( $e->getMessage(), 'atendimento_recadastro_cd_empresa_key' ) )
            {
            	$bResult = 2; // 'VIOLAÇÃO DE UNICIDADE';
            }
        }

        return $bResult;
	}

	public function atendimento_recadastro__cancel( entity_projetos_atendimento_recadastro $entidade )
	{
        $ado = new ADO_projetos_atendimento_recadastro( $this->db );
        try
        {
            $bResult = $ado->updateCancel( $entidade );
            $ado = null;
        }
        catch(Exception $e)
        {
            $ado = null;
            $bResult = false;
            echo( $e->getMessage() ); 
        }
        
        return $bResult;
	}
	
	public function atendimento_recadastro__LoadById(entity_projetos_atendimento_recadastro $entidade)
	{
        $ado = new ADO_projetos_atendimento_recadastro( $this->db );
        try
        {
            $result = $ado->loadById( $entidade );
            $ado = null;
        }
        catch(Exception $e)
        {
            $ado = null;
            echo( $e->getMessage() ); 
        }

        return true;
	}

	public function auto_atendimento_pagamento_impressao__get($params)
	{
		$ado = new ADO_projetos_auto_atendimento_pagamento_impressao( $this->db );
        try
        {
            $result = $ado->get( $params );
            $ado = null;
        }
        catch(Exception $e)
        {
            $ado = null;
            $result = false;
            echo( $e->getMessage() );
        }

        return $result;
	}
}
?>