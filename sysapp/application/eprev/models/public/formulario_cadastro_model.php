<?php
class formulario_cadastro_model extends Model
{
    function __construct()
    {
        parent::Model();
    }
        
    function verifica_plano_patrocinadora($args=Array())
    {
        $qr_sql = "SELECT COUNT(*)
                     FROM public.planos_patrocinadoras
                    WHERE cd_plano   = ". intval($args['cd_plano'])."
                      AND cd_empresa = ". intval($args['cd_empresa']);

        return $this->db->query($qr_sql)->row_array();
    }

    function get_campos_pdf($args=Array())
    {
        $qr_sql = "SELECT p.cd_registro_empregado,
                          TO_CHAR(COALESCE(t.dt_admissao),'DD') AS dt_admissao_dia,
                          TO_CHAR(COALESCE(t.dt_admissao),'MM') AS dt_admissao_mes,
                          TO_CHAR(COALESCE(t.dt_admissao),'YYYY') AS dt_admissao_ano,
                          p.nome,
                          TO_CHAR(COALESCE(p.dt_nascimento),'DD/MM/YYYY') AS dt_nascimento,
                          p.cd_estado_civil,
                          p.sexo AS fl_sexo,
                          funcoes.format_cpf(TO_CHAR(p.cpf_mf,'FM00000000000')) AS cpf_mf,
                          p.endereco,
                          p.nr_endereco,
                          p.complemento_endereco,
                          p.bairro,
                          p.cidade,
                          p.unidade_federativa,
                          p.cep, 
                          TRIM(TO_CHAR(p.complemento_cep,'000')) AS complemento_cep,
                          '(' || TRIM(TO_CHAR(COALESCE(p.ddd,0),'00')) || ') ' ||  p.telefone AS telefone_1, 
                          '(' || TRIM(TO_CHAR(COALESCE(p.ddd_celular,0),'00')) || ') ' ||  p.celular AS telefone_2, 
                          COALESCE(p.email,'') || ' / ' || COALESCE(p.email_profissional,'') AS email, 
                          COALESCE(p.email,'') As email_1,
                          COALESCE(p.email_profissional,'') As email_2,
                          g.descricao_grau_instrucao,
                          p.cd_agencia, 
                          p.conta_folha,
                          if.razao_social_nome,
                          p.cd_instituicao
                     FROM public.participantes p
                     LEFT JOIN public.titulares t
                       ON t.cd_empresa            = p.cd_empresa 
                      AND t.cd_registro_empregado = p.cd_registro_Empregado
                      AND t.seq_dependencia       = p.seq_dependencia
                     LEFT JOIN public.estado_civils ec
                       ON ec.cd_estado_civil = p.cd_estado_civil 
                     LEFT JOIN public.grau_instrucaos g
                       ON g.cd_grau_de_instrucao = p.cd_grau_de_instrucao 
                     LEFT JOIN public.instituicao_financeiras if 
                       ON if.cd_instituicao = p.cd_instituicao 
                      AND if.cd_agencia     = '0' 
                    WHERE p.cd_empresa            = ". intval($args['cd_empresa'])."
                      AND p.cd_registro_empregado = ". intval($args['cd_registro_empregado'])."
                      AND p.seq_dependencia       = ". intval($args['seq_dependencia']);

         return $this->db->query($qr_sql)->row_array();
    }
	
    function get_participante($args=Array())
    {
        $qr_sql = "
					SELECT p.cd_empresa,
					       p.cd_registro_empregado,
						   p.seq_dependencia,
					       p.nome,
						   funcoes.format_cpf(p.cpf_mf) AS cpf,
						   d.nro_documento,
						   TO_CHAR(d.dt_expedicao,'DD/MM/YYYY') AS dt_expedicao,
						   d.orgao_expedidor
					  FROM public.participantes p
					  LEFT JOIN public.documentos d 
						ON d.cd_empresa            = p.cd_empresa
					   AND d.cd_registro_empregado = p.cd_registro_empregado
					   AND d.seq_dependencia       = p.seq_dependencia
					   AND d.cd_tipo_doc           = 1
					 WHERE p.cd_empresa            = ".intval($args['cd_empresa'])."
					   AND p.cd_registro_empregado = ".intval($args['cd_registro_empregado'])."
					   AND p.seq_dependencia       = ".intval($args['seq_dependencia'])."		
				  ";

        return $this->db->query($qr_sql)->row_array();
    }	
	
    function familiaFichaInscricao($args=Array())
    {
        $qr_sql = "
					SELECT c.nome,
						   c.fl_sexo,
						   c.cpf, 
						   TO_CHAR(c.dt_nascimento,'DD/MM/YYYY') AS dt_nascimento,  
						   c.telefone_1, 
						   c.telefone_2, 
						   c.email_1, 
						   c.email_2, 
						   c.endereco,
						   c.complemento,
						   c.bairro,
						   c.cidade, 
						   c.uf, 
						   c.cep, 
						   c.observacao, 
						   c.dt_inclusao, 
						   c.dt_alteracao
					  FROM familia_previdencia.cadastro_ficha_inscricao_vw c
					 WHERE MD5(c.cd_cadastro::TEXT) = '".trim($args['cd_cadastro'])."'
					   AND c.tp_cadastro = '".trim($args['tp_cadastro'])."'
		          ";
		#echo $qr_sql; exit;
        return $this->db->query($qr_sql)->row_array();
    }

    function sengeFichaInscricao($args=Array())
    {
        $qr_sql = "
					SELECT c.nome,
						   c.fl_sexo,
						   c.cpf, 
						   TO_CHAR(c.dt_nascimento,'DD/MM/YYYY') AS dt_nascimento,  
						   c.telefone_1, 
						   c.telefone_2, 
						   c.email_1, 
						   c.email_2, 
						   c.endereco,
						   c.complemento,
						   c.bairro,
						   c.cidade, 
						   c.uf, 
						   c.cep, 
						   c.observacao, 
						   c.dt_inclusao, 
						   c.dt_alteracao
					  FROM senge_previdencia.cadastro_ficha_inscricao_vw c
					 WHERE MD5(c.cd_cadastro::TEXT) = '".trim($args['cd_cadastro'])."'
		          ";
		#echo $qr_sql; exit;
        return $this->db->query($qr_sql)->row_array();
    }

    function sinproFichaInscricao($args=Array())
    {
        $qr_sql = "
					SELECT c.nome,
						   c.fl_sexo,
						   c.cpf, 
						   TO_CHAR(c.dt_nascimento,'DD/MM/YYYY') AS dt_nascimento,  
						   c.telefone_1, 
						   c.telefone_2, 
						   c.email_1, 
						   c.email_2, 
						   c.endereco,
						   c.complemento,
						   c.bairro,
						   c.cidade, 
						   c.uf, 
						   c.cep, 
						   c.observacao, 
						   c.dt_inclusao, 
						   c.dt_alteracao
					  FROM sinprors_previdencia.cadastro_ficha_inscricao_vw c
					 WHERE MD5(c.cd_cadastro::TEXT) = '".trim($args['cd_cadastro'])."'
		          ";
		#echo $qr_sql; exit;
        return $this->db->query($qr_sql)->row_array();
    }	
}

?>