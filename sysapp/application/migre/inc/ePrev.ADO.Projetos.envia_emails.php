<?php
class ADO_projetos_envia_emails {

    // DAL
    private $db;
    private $dal;
    private $select_define = "";
    
    function ADO_projetos_envia_emails( $_db )
    {
        $this->db = $_db;
        $this->dal = new DBConnection();
        $this->dal->loadConnection( $this->db );
    }

    function __destruct()
    {
        $this->dal = null;
    } 

    public function set_select_define( $value )
    {
        $this->select_define = $value;
    }

    public function fetchAll()
    {
        $result = null;

        return $result;
    }
    
    public function setFetchByFilter_select_define($value)
    {
        $this->select_define = $value;
    }
    
    public function fetchByFilter( entity_projetos_documento_protocolo $filtro )
    {
        $result = null;

        return $result;
    }
    
    public function loadById( $entidade )
    {
        $entidade = null;
        return true;
    }

    public function insert( entity_projetos_envia_emails $entidade )
    {
        $bReturn = false;

        $this->dal->createQuery("

                 INSERT INTO projetos.envia_emails
                 ( 
                     dt_envio, 
                     de, 
                     para, 
                     cc, 
                     cco,
                     assunto, 
                     texto, 
                     tipo_mensagem,
                     cd_evento                
                 ) 
                 VALUES
                 (
                     CURRENT_TIMESTAMP,
                     'Fundação CEEE',
                     '{para}',
                     '{cc}',
                     '{cco}',
                     '{assunto}',
                     '{texto}',
                     'html',
                     {cd_evento}
                 )

        ");

        $this->dal->setAttribute( "{para}", $entidade->get_para() );
        $this->dal->setAttribute( "{cc}", $entidade->get_cc() );
        $this->dal->setAttribute( "{cco}", $entidade->get_cco() );
        $this->dal->setAttribute( "{assunto}", $entidade->get_assunto() );
        $this->dal->setAttribute( "{texto}", $entidade->get_texto() );
        if ($entidade->get_cd_evento()=="")
        {
			$this->dal->setAttribute( "{cd_evento}", "null" );
		}
		else
		{
	        $this->dal->setAttribute( "{cd_evento}", (int)$entidade->get_cd_evento() );
		}

        $result = $this->dal->executeQuery();

        if ($this->dal->haveError())
        {
            throw new Exception('Erro em ADO_projetos_envia_emails.insert() ao executar comando SQL de consulta.' . $this->dal->getMessage() . '');
        }
        else
        {
            $bReturn = true;
        }
        return $bReturn;
    }

    public function update( entity_projetos_documento_protocolo $entidade )
    {
        $bReturn = true;
        return $bReturn;
    }

    public function delete( $value )
    {
        $bReturn = false;
        return $bReturn;
    }

    public function get($params)
    {
		$collection = array();

        $this->dal->createQuery("

			SELECT cd_email
			     , TO_CHAR(dt_envio, 'DD/MM/YYYY HH24:MI:SS') AS dt_envio
			     , de
			     , para
			     , cc
			     , cco
			     , assunto
			     , texto
			     , TO_CHAR(dt_email_enviado, 'DD/MM/YYYY HH24:MI:SS') AS dt_email_enviado
			     , TO_CHAR(dt_schedule_email, 'DD/MM/YYYY HH24:MI:SS') AS dt_schedule_email
			     , arquivo_anexo
			     , div_solicitante
			     , cd_divulgacao
			     , cd_plano
			     , cd_empresa
			     , cd_registro_empregado
			     , seq_dependencia
			     , tipo_mensagem
			     , cd_evento
			     , TO_CHAR(le.dt_email, 'DD/MM/YYYY HH24:MI') AS dt_retorno
			  FROM projetos.envia_emails ee
	     LEFT JOIN projetos.log_email le
                ON CAST(le.nr_msg AS INTEGER) = ee.cd_email
			 WHERE cd_evento = {cd_evento}
			   AND DATE_TRUNC('month', dt_envio) = '{ano}-{mes}-01'
			   AND cd_empresa <> 9999
			   {retorno}

		");

        if(isset($params['tipo']))
        {
        	// CASO RELATÓRIO APENAS PARA ENVIADOS
        	if($params['tipo']=="enviado")
        	{
		        $this->dal->setAttribute( "{retorno}", " AND le.dt_email IS NULL " );
        	}
        	// CASO RELATÓRIO APENAS PARA RETORNADOS
        	elseif($params['tipo']=="retornado")
        	{
        		$this->dal->setAttribute( "{retorno}", " AND le.dt_email IS NOT NULL " );
        	}
        	// CASO NÃO INFORMADO O PARAMETRO TIPO APESAR DE SETADO
        	else
        	{
        		$this->dal->setAttribute( "{retorno}", " " );
        	}
        }
        // CASO NÃO SETADO PARAMETRO TIPO
        else
        {
        	$this->dal->setAttribute( "{retorno}", " " );
        }

        $this->dal->setAttribute( "{mes}", (int)$params['mes'] );
        $this->dal->setAttribute( "{ano}", (int)$params['ano'] );
        $this->dal->setAttribute( "{cd_evento}", (int)$params['cd_evento'] );

        $result = $this->dal->getResultset();
        // echo '<pre>' . $this->dal->getMessage() . '</pre>';

        if( $result )
        {
            while( $row = pg_fetch_array($result) )
            {
                $item = new entity_projetos_envia_emails_extended();
            	foreach( $item as $key=>$value )
            	{
            		eval( '$item->'.$key.' = $row['.$key.'];' );
            	}
				$collection[sizeof($collection)] = $item;
            }
		}

        if( $this->dal->haveError() )
        {
            throw new Exception( 'Erro em ADO_projetos_envia_emails.get() ao executar comando SQL de consulta. '.$this->dal->getMessage() );
        }

        pg_free_result($result);
        $result = null;
        $row = null;

        return $collection;
    }
}
?>