<?php
/**
 * Classe para acesso a dados de divulgacao.divulgacao 
 * 
 * @access public
 * @package ePrev
 * @subpackage ADO
 * @require ePrev.Util.Message.php
 * @require ePrev.DAL.DBConnection.php
 */
class ADO_participantes {

    // DAL
    private $db;
    private $dal;

    function ADO_participantes( $_db ) {
        $this->db = $_db;
        $this->dal = new DBConnection();
        $this->dal->loadConnection( $this->db );
    }

    function __destruct(){
        $this->dal = null;
    } 

    public function loadByRE( $cd_empresa, $cd_registro_empregado, $cd_seq_dependencia )
    {
        $sRet = "";
        $this->dal->createQuery("

            SELECT col_rowid, cd_empresa, cd_registro_empregado, seq_dependencia, 
				   cd_estado_civil, cd_grau_de_instrucao, nome, dt_nascimento, sexo, 
				   cd_instituicao, cd_agencia, cd_instituicao_pode_ter_conta_, cd_agencia_pode_ter_conta_debi, 
                   TRIM(TO_CHAR(cep, '00000')) AS cep, TRIM(TO_CHAR(complemento_cep, '000')) AS complemento_cep
				   , tipo_folha, dt_obito, logradouro, bairro, 
				   cidade, unidade_federativa, ddd, telefone, ramal, conta_folha, 
				   conta_debitos, cpf_mf, email, dt_dig_obito, bloqueio_ender, dt_inicio_beneficio, 
				   cd_registro_patroc, dt_recadastramento, dt_envio_recadastramento, 
				   tipo_recadastramento, cd_plano, quant_dep_economico, data_alteracao_dep_economico, 
				   celular, fax, motivo_devolucao_correio, dt_alteracao_endereco, 
				   sigla_pais, dt_inclusao, usu_inclusao, dt_alteracao, usu_alteracao, 
				   opcao_ir, dt_opcao_ir, dt_adesao_instituidor, cd_grau_depen_instituidor, 
				   ddd_outro, telefone_outro, email_profissional, dt_envio_certificado, 
				   dt_recebimento_compl_apos
              FROM participantes
             WHERE cd_empresa = {cd_empresa}
               AND cd_registro_empregado = {cd_registro_empregado}
               AND seq_dependencia = {seq_dependencia}

        ");

        $this->dal->setAttribute( "{cd_empresa}", (int)$cd_empresa );
        $this->dal->setAttribute( "{cd_registro_empregado}", (int)$cd_registro_empregado );
        $this->dal->setAttribute( "{seq_dependencia}", (int)$cd_seq_dependencia );

        $result = $this->dal->getResultset();
        
        if ($result) 
        {
			if ( $row = pg_fetch_array($result) )
            {
                $sRet = $row["nome"];
            }
		}

        if ($this->dal->haveError()) 
        {
            throw new Exception("Erro em ADO_participantes.loadByRE($cd_empresa, $cd_registro_empregado, $cd_seq_dependencia) ao executar comando SQL de consulta. ".$this->dal->getMessage());
        }

        return $sRet;
    }

    /**
     * Carrega a entidade passada por parametro com as informações da base
     * obrigatório preenchimento dos atributos: 
     *   - $entidade->set_cd_empresa()
     *   - $entidade->set_cd_registro_empregado()
     *   - $entidade->set_seq_dependencia()
     * @param entity_participantes $entidade Por referencia, chega ao método com a PK preenchida e recebe as demais informações da base 
     */
    public function load( entity_participantes $entidade )
    {
        $sRet = "";
        $this->dal->createQuery("

            SELECT cd_empresa
                 , cd_registro_empregado
                 , seq_dependencia
                 , nome
                 , logradouro
                 , bairro
                 , cidade
                 , unidade_federativa
                 , TRIM(TO_CHAR(cep, '00000')) AS cep
				 , TRIM(TO_CHAR(complemento_cep, '000')) AS complemento_cep
                 , ddd
                 , telefone
                 , coalesce( email , email_profissional ) as email
                 , email_profissional
              FROM participantes
             WHERE cd_empresa = {cd_empresa}
               AND cd_registro_empregado = {cd_registro_empregado}
               AND seq_dependencia = {seq_dependencia}

        ");

        $this->dal->setAttribute( "{cd_empresa}", (int)$entidade->get_cd_empresa() );
        $this->dal->setAttribute( "{cd_registro_empregado}", (int)$entidade->get_cd_registro_empregado() );
        $this->dal->setAttribute( "{seq_dependencia}", (int)$entidade->get_seq_dependencia() );

        $result = $this->dal->getResultset();

        if ( $result ) 
        {
            if ( $row = pg_fetch_array($result) )
            {
                $entidade->set_nome( $row["nome"] );
                $entidade->set_logradouro( $row["logradouro"] );
                $entidade->set_bairro( $row["bairro"] );
                $entidade->set_cidade( $row["cidade"] );
                $entidade->set_unidade_federativa( $row["unidade_federativa"] );
                $entidade->set_cep( $row["cep"] );
                $entidade->set_complemento_cep( $row["complemento_cep"] );
                $entidade->set_ddd( $row["ddd"] );
                $entidade->set_telefone( $row["telefone"] );
                $entidade->set_email( $row["email"] );
                $entidade->set_email_profissional( $row["email_profissional"] );
            }
        }

        if ($this->dal->haveError()) 
        {
            throw new Exception( "Erro em ADO_participantes.load( entidade ) ao executar comando SQL de consulta. " . $this->dal->getMessage() );
        }

        return $sRet;
    }

    /**
     * Carrega a entidade passada por parametro com as informações da base
     * obrigatório preenchimento dos atributos: 
     *   - $entidade->set_col_rowid()   // com id_md5 composto por emp re seq fomatados
     * @param entity_participantes $entidade Por referencia, chega ao método com a PK preenchida e recebe as demais informações da base 
     */
    public function load_by_id_md5( entity_participantes $entidade )
    {
        $sRet = "";
        $this->dal->createQuery("

            SELECT cd_empresa
                 , cd_registro_empregado
                 , seq_dependencia
                 , cd_plano
                 , nome
                 , logradouro
                 , bairro
                 , cidade
                 , unidade_federativa
                 , TRIM(TO_CHAR(cep, '00000')) AS cep
                 , TRIM(TO_CHAR(complemento_cep, '000')) AS complemento_cep
                 , cpf_mf
              FROM participantes
             WHERE funcoes.cripto_re(cd_empresa, cd_registro_empregado, seq_dependencia) = '{id_md5}'

        ");

        $this->dal->setAttribute( "{id_md5}", $entidade->get_col_rowid() );

        $result = $this->dal->getResultset();

        if ( $result ) 
        {
            if ( $row = pg_fetch_array($result) )
            {
                $entidade->set_cd_empresa( $row["cd_empresa"] );
                $entidade->set_cd_registro_empregado( $row["cd_registro_empregado"] );
                $entidade->set_seq_dependencia( $row["seq_dependencia"] );
                $entidade->set_cd_plano( $row["cd_plano"] );
                $entidade->set_nome( $row["nome"] );
                $entidade->set_logradouro( $row["logradouro"] );
                $entidade->set_bairro( $row["bairro"] );
                $entidade->set_cidade( $row["cidade"] );
                $entidade->set_unidade_federativa( $row["unidade_federativa"] );
                $entidade->set_cep( $row["cep"] );
                $entidade->set_complemento_cep( $row["complemento_cep"] );
                $entidade->set_cpf_mf( $row["cpf_mf"] );
            }
        }

        if ($this->dal->haveError()) 
        {
            throw new Exception( "Erro em ADO_participantes.load_by_id_md5( entidade ) ao executar comando SQL de consulta. " . $this->dal->getMessage() );
        }

        return $sRet;
    }
    
    /**
     * Resgata em array da tabela de apolicies, os riscos de um participante
     */
    public function get_cobertura_de_risco( entity_participantes $participante)
    {
        $this->dal->createQuery("

            SELECT a.nome AS nome_risco,
                   ap.premio AS vl_risco
              FROM public.apolices_participantes ap
              JOIN public.apolices a
                ON a.cd_apolice = ap.cd_apolice
             WHERE ap.cd_empresa            = {cd_empresa}
               AND ap.cd_registro_empregado = {cd_registro_empregado}
               AND ap.seq_dependencia       = {seq_dependencia}
               AND ap.dt_exclusao           IS NULL

        ");

        $this->dal->setAttribute( "{cd_empresa}", (int)$participante->get_cd_empresa() );
        $this->dal->setAttribute( "{cd_registro_empregado}", (int)$participante->get_cd_registro_empregado() );
        $this->dal->setAttribute( "{seq_dependencia}", (int)$participante->get_seq_dependencia() );

        $result = $this->dal->getResultset();
        // echo $this->dal->getMessage();
        
        $resultados = null;
        $risco = array();
        if ( $result ) 
        {
            while ( $row = pg_fetch_array($result) )
            {
                $risco[sizeof($risco)] = array( "nome_risco" => $row['nome_risco'], "vl_risco" => $row['vl_risco'] );
            }
        }

        if ($this->dal->haveError()) 
        {
            throw new Exception( "Erro em ADO_participantes.get_cobertura_de_risco( entidade ) ao executar comando SQL de consulta. " . $this->dal->getMessage() );
        }

        return $risco;
    }
}
?>