<?php
class contato_dependente_model extends Model
{
    function __construct()
    {
        parent::Model();
    }
    
	function listar(&$result, $args=array())
    {
        $qr_sql = "
            SELECT cd.cd_contato_dependente, 
                   cd.cd_empresa || '/' || cd.cd_registro_empregado || '/' || cd.seq_dependencia AS re,
                   p.nome,
                   TO_CHAR(cd.dt_inclusao, 'DD/MM/YYYY') AS dt_inclusao,
                   (SELECT COUNT(*)
                      FROM projetos.contato_dependente cd2
                      JOIN public.dependentes d
                        ON d.cd_empresa            = cd2.cd_empresa
                       AND d.cd_registro_empregado = cd2.cd_registro_empregado
                     WHERE cd2.dt_exclusao IS NULL
                       AND cd2.cd_contato_dependente = cd.cd_contato_dependente
                       AND 0 = (CASE WHEN (SELECT COUNT(*)
                                             FROM projetos.contato_dependente_acompanhamento a
                                            WHERE a.dt_exclusao IS NULL
                                              AND a.cd_contato_dependente = cd.cd_contato_dependente
                                              AND a.seq_dependencia = cd2.seq_dependencia) > 0 
                                     THEN 0
                                     ELSE COALESCE(d.cd_motivo_desligamento, 0)
                                  END)) AS qt_dependente,
                   (SELECT COUNT(DISTINCT a.seq_dependencia)
                      FROM projetos.contato_dependente_acompanhamento a
                     WHERE a.dt_exclusao IS NULL
                       AND a.cd_contato_dependente = cd.cd_contato_dependente) AS qt_dependente_contatado,
				   TO_CHAR(p.dt_obito, 'DD/MM/YYYY') AS dt_obito
              FROM projetos.contato_dependente cd
              JOIN public.participantes p
				ON p.cd_empresa            = cd.cd_empresa
			   AND p.cd_registro_empregado = cd.cd_registro_empregado
			   AND p.seq_dependencia       = cd.seq_dependencia
             WHERE cd.dt_exclusao IS NULL
               ".(trim($args['cd_empresa']) != '' ? "AND cd.cd_empresa = ".intval($args['cd_empresa']) : "")."
               ".(trim($args['cd_registro_empregado']) != '' ? "AND cd.cd_registro_empregado = ".intval($args['cd_registro_empregado']) : "")."
               ".(trim($args['seq_dependencia']) != '' ? "AND cd.seq_dependencia = ".intval($args['seq_dependencia']) : "")."
               ".(trim($args["nome"]) != "" ? "AND UPPER(funcoes.remove_acento(p.nome)) LIKE UPPER(funcoes.remove_acento('%".trim($args["nome"])."%'))" : "")."
               ".(((trim($args['dt_ini']) != "") and  (trim($args['dt_fim']) != "")) ? "AND 0 < (SELECT COUNT(*)
                                                                                                   FROM projetos.contato_dependente_acompanhamento A
                                                                                                  WHERE a.cd_contato_dependente = cd.cd_contato_dependente
                                                                                                    AND a.dt_exclusao IS NULL
                                                                                                    AND DATE_TRUNC('day', a.dt_inclusao) BETWEEN TO_DATE('".$args['dt_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_fim']."', 'DD/MM/YYYY'))" : "").";";
        $result = $this->db->query($qr_sql);
    }
    
    function verifica_cadastro(&$result, $args=array())
    {
        $qr_sql = "
					SELECT cd_contato_dependente
					  FROM projetos.contato_dependente
					 WHERE dt_exclusao IS NULL
					   AND cd_empresa            = ".intval($args['cd_empresa'])."
					   AND cd_registro_empregado = ".intval($args['cd_registro_empregado'])."
					   AND seq_dependencia       = ".intval($args['seq_dependencia']).";
			      ";
        $result = $this->db->query($qr_sql);
    }
    
    function salvar(&$result, $args=array())
    {
        $cd_contato_dependente = intval($this->db->get_new_id("projetos.contato_dependente", "cd_contato_dependente"));
        
        $qr_sql = "
            INSERT INTO projetos.contato_dependente
                 (
                   cd_contato_dependente, 
                   cd_empresa, 
                   cd_registro_empregado, 
                   seq_dependencia, 
                   cd_usuario_inclusao, 
                   cd_usuario_alteracao
                 )
            VALUES 
                 (
                   ".intval($cd_contato_dependente).",
                   ".intval($args['cd_empresa']).",
                   ".intval($args['cd_registro_empregado']).",
                   ".intval($args['seq_dependencia']).",
                   ".intval($args['cd_usuario']).",
                   ".intval($args['cd_usuario'])."
                 );";
        
        $result = $this->db->query($qr_sql);
        
        return $cd_contato_dependente;
    }
    
    
    function carrega(&$result, $args=array())
    {
        $qr_sql = "
            SELECT cd.cd_contato_dependente,
                   cd.cd_empresa,
                   cd.cd_registro_empregado,
                   cd.seq_dependencia,
                   cd.cd_empresa || '/' || cd.cd_registro_empregado || '/' || cd.seq_dependencia AS re,
                   p.nome,
                   p.email,
                   p.email_profissional,
                   p.bairro,
                   p.cidade || ' - ' || p.unidade_federativa AS cidade,
                   CASE WHEN p.telefone != 0 THEN '(' || p.ddd || ') ' || p.telefone
                        ELSE ''
                   END AS telefone,
                   CASE WHEN p.celular != 0 THEN '(' || p.ddd || ') ' || p.celular
                        ELSE ''
                   END AS celular,
                   TO_CHAR(p.cep,'FM00000') || '-' || TO_CHAR(p.complemento_cep,'FM000') AS cep,
                   p.endereco || ' , ' || p.nr_endereco || ' ' || p.complemento_endereco AS endereco,
				   TO_CHAR(p.dt_obito, 'DD/MM/YYYY') AS dt_obito
              FROM projetos.contato_dependente cd
              JOIN public.participantes p
				ON p.cd_empresa            = cd.cd_empresa
			   AND p.cd_registro_empregado = cd.cd_registro_empregado
			   AND p.seq_dependencia       = cd.seq_dependencia
             WHERE cd.cd_contato_dependente = ".intval($args['cd_contato_dependente']).";";
        
        $result = $this->db->query($qr_sql);
    }
    
   function listar_dependente(&$result, $args=array())
   {
       $qr_sql = "
            SELECT p.cd_empresa,
                   p.cd_registro_empregado,
                   p.seq_dependencia,
                   p.nome,
                   TO_CHAR(p.dt_nascimento, 'DD/MM/YYYY') AS dt_nascimento, 
				   EXTRACT(years FROM AGE(CURRENT_DATE - (CURRENT_DATE - p.dt_nascimento)))::INTEGER AS nr_idade,
                   p.sexo,
                   d.cd_grau_parentesco,
                   gp.descricao_grau_parentesco,
                   p.telefone,
                   p.celular,
                   p.ddd,
                   p.ramal,
                   p.email,
                   p.email_profissional,
                   p.endereco,
                   p.nr_endereco,
                   p.complemento_endereco,
                   p.bairro,
                   p.cep,
                   p.complemento_cep,
                   p.cidade,
                   p.unidade_federativa,
                   d.cd_motivo_desligamento,
                   CASE WHEN d.id_pensionista = 'S' THEN 'Sim'
                        ELSE 'Não'
                   END AS pensionista,
                   CASE WHEN d.id_pensionista = 'S' THEN 'label label-success'
                        ELSE 'label'
                   END AS class_pensionista,
                   TO_CHAR((SELECT hb.dib
                              FROM habilita_beneficios hb
                              JOIN participantes p1
                                ON p1.cd_empresa            = hb.cd_empresa
                               AND p1.cd_registro_empregado = hb.cd_registro_empregado
                               AND p1.seq_dependencia       = hb.seq_dependencia
                               AND p1.tipo_folha            = hb.tifo_tipo_folha
                             WHERE hb.cd_empresa            = d.cd_empresa
                               AND hb.cd_registro_empregado = d.cd_registro_empregado
                               AND hb.seq_dependencia       = d.seq_dependencia ), 'DD/MM/YYYY') AS dt_dib,
                   TO_CHAR((SELECT hb.dt_inclusao 
                              FROM habilita_beneficios hb
                              JOIN participantes p1
                                ON p1.cd_empresa            = hb.cd_empresa
                               AND p1.cd_registro_empregado = hb.cd_registro_empregado
                               AND p1.seq_dependencia       = hb.seq_dependencia
                               AND p1.tipo_folha            = hb.tifo_tipo_folha
                             WHERE hb.cd_empresa            = d.cd_empresa
                               AND hb.cd_registro_empregado = d.cd_registro_empregado
                               AND hb.seq_dependencia       = d.seq_dependencia ), 'DD/MM/YYYY HH24:MI:SS') AS dt_habilita,
                   TO_CHAR((SELECT hb.dt_incl_beneficio 
                              FROM habilita_beneficios hb
                              JOIN participantes p1
                                ON p1.cd_empresa            = hb.cd_empresa
                               AND p1.cd_registro_empregado = hb.cd_registro_empregado
                               AND p1.seq_dependencia       = hb.seq_dependencia
                               AND p1.tipo_folha            = hb.tifo_tipo_folha
                             WHERE hb.cd_empresa            = d.cd_empresa
                               AND hb.cd_registro_empregado = d.cd_registro_empregado
                               AND hb.seq_dependencia       = d.seq_dependencia ), 'DD/MM/YYYY HH24:MI:SS') AS dt_folha
              FROM public.dependentes d
			  LEFT JOIN projetos.contato_dependente cd
                ON cd.cd_empresa            = d.cd_empresa
               AND cd.cd_registro_empregado = d.cd_registro_empregado			  
              LEFT JOIN public.participantes p
                ON p.cd_registro_empregado = d.cd_registro_empregado 
               AND p.seq_dependencia       = d.seq_dependencia 
               AND p.cd_empresa            = d.cd_empresa 
              LEFT JOIN public.grau_parentescos gp 
                ON gp.cd_grau_parentesco   = d.cd_grau_parentesco 
             WHERE cd.dt_exclusao IS NULL
               AND (
					cd.cd_contato_dependente = ".intval($args['cd_contato_dependente'])."
			        OR
					(d.cd_empresa = ".intval($args['cd_empresa'])." AND d.cd_registro_empregado = ".intval($args['cd_registro_empregado']).")
				   )
			   
               AND 0 = (CASE WHEN (SELECT COUNT(*)
                                     FROM projetos.contato_dependente_acompanhamento a
                                    WHERE a.dt_exclusao IS NULL
                                      AND a.cd_contato_dependente = cd.cd_contato_dependente
                                      AND a.seq_dependencia = d.seq_dependencia) > 0 
                             THEN 0
                             ELSE COALESCE(d.cd_motivo_desligamento, 0)
                        END)
             ORDER BY p.seq_dependencia ASC;";
   
       $result = $this->db->query($qr_sql);
   }
   
   function carrega_dependente(&$result, $args=array())
   {
       $qr_sql = "
            SELECT p.cd_empresa,
                   p.cd_registro_empregado,
                   p.seq_dependencia,
                   p.cd_empresa || '/' || p.cd_registro_empregado || '/' || p.seq_dependencia AS re,
                   p.nome,
                   p.email,
                   p.email_profissional,
                   p.bairro,
                   p.cidade || ' - ' || p.unidade_federativa AS cidade,
                   CASE WHEN p.telefone != 0 THEN '(' || p.ddd || ') ' || p.telefone
                        ELSE ''
                   END AS telefone,
                   CASE WHEN p.celular != 0 THEN '(' || p.ddd || ') ' || p.celular
                        ELSE ''
                   END AS celular,
                   TO_CHAR(p.cep,'FM00000') || '-' || TO_CHAR(p.complemento_cep,'FM000') AS cep,
                   p.endereco || ' , ' || p.nr_endereco || ' ' || p.complemento_endereco AS endereco
              FROM public.participantes p
		     WHERE p.cd_empresa            = ".$args['cd_empresa']."
			   AND p.cd_registro_empregado = ".$args['cd_registro_empregado']."
			   AND p.seq_dependencia       = ".$args['seq_dependencia'].";";
        
        $result = $this->db->query($qr_sql);
   }
   
   function salvar_acompanhamento(&$result, $args=array())
   {
       $qr_sql = "
           INSERT INTO projetos.contato_dependente_acompanhamento
                (
                    cd_contato_dependente,
                    cd_empresa,
                    cd_registro_empregado,
                    seq_dependencia,
                    ds_contato_dependente_acompanhamento, 
                    cd_contato_dependente_retorno,
                    cd_usuario_inclusao, 
                    cd_usuario_alteracao
                )
           VALUES
                (
                    ".intval($args['cd_contato_dependente']).",
                    ".intval($args['cd_empresa']).",
                    ".intval($args['cd_registro_empregado']).",
                    ".intval($args['seq_dependencia']).",
                    ".(trim($args['ds_contato_dependente_acompanhamento']) != '' ? str_escape($args['ds_contato_dependente_acompanhamento']) : "DEFAULT").",
                    ".(trim($args['cd_contato_dependente_retorno']) != '' ? intval($args['cd_contato_dependente_retorno']) : "DEFAULT").",
                    ".intval($args['cd_usuario']).",
                    ".intval($args['cd_usuario'])."
                );";
       
       $result = $this->db->query($qr_sql);
   }
   
   function listar_acompanhamento(&$result, $args=array())
   {
       $qr_sql = "
           SELECT a.cd_contato_dependente_acompanhamento,
                  a.ds_contato_dependente_acompanhamento,
                  TO_CHAR(a.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
                  uc.nome,
                  cdr.ds_contato_dependente_retorno
             FROM projetos.contato_dependente_acompanhamento a
             JOIN projetos.usuarios_controledi uc
               ON uc.codigo = a.cd_usuario_inclusao
             LEFT JOIN projetos.contato_dependente_retorno cdr
               ON cdr.cd_contato_dependente_retorno = a.cd_contato_dependente_retorno
            WHERE a.dt_exclusao IS NULL
              AND a.cd_contato_dependente = ".intval($args['cd_contato_dependente'])."
              AND a.cd_empresa            = ".intval($args['cd_empresa'])."
              AND a.cd_registro_empregado = ".intval($args['cd_registro_empregado'])."
              AND a.seq_dependencia       = ".intval($args['seq_dependencia'])."
            ORDER BY a.dt_inclusao DESC;";
       
       $result = $this->db->query($qr_sql);
   }
   
   function listar_acompanhamento_dependente(&$result, $args=array())
   {
       $qr_sql = "
           SELECT a.cd_contato_dependente_acompanhamento,
                  a.ds_contato_dependente_acompanhamento,
                  TO_CHAR(a.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
				  cdr.ds_contato_dependente_retorno
             FROM projetos.contato_dependente_acompanhamento a
			 LEFT JOIN projetos.contato_dependente_retorno cdr
               ON cdr.cd_contato_dependente_retorno = a.cd_contato_dependente_retorno
            WHERE a.dt_exclusao IS NULL
              AND a.cd_contato_dependente = ".intval($args['cd_contato_dependente'])."
            ORDER BY a.dt_inclusao DESC;";
       
       $result = $this->db->query($qr_sql);
   }
}
?>