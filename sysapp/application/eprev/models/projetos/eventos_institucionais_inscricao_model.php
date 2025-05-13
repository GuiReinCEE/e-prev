<?php

class Eventos_institucionais_inscricao_model extends Model
{

    function __construct()
    {
        parent::Model();
    }

    function listar(&$result, $args=array())
    {
        $qr_sql = "
            SELECT inscricao.cd_eventos_institucionais_inscricao,
                   MD5(inscricao.cd_eventos_institucionais_inscricao::TEXT) AS cd_eventos_institucionais_inscricao_md5,
                   inscricao.nome as inscrito,
                   CASE WHEN UPPER(TRIM(inscricao.observacao)) LIKE UPPER('Acompanha o participante %/%/%') AND inscricao.tipo = 'A'
                        THEN CAST(COALESCE((SELECT pg_split[1] 
                                              FROM funcoes.pg_split(TRIM(REPLACE(TRIM(UPPER(inscricao.observacao)),UPPER('Acompanha o participante'),'')), '/')),'0') AS INTEGER)
                        ELSE cd_empresa
                   END AS cd_empresa,
                   CASE WHEN UPPER(TRIM(inscricao.observacao)) LIKE UPPER('Acompanha o participante %/%/%') AND inscricao.tipo = 'A'
                        THEN CAST(COALESCE((SELECT pg_split[2] 
                                              FROM funcoes.pg_split(TRIM(REPLACE(TRIM(UPPER(inscricao.observacao)),UPPER('Acompanha o participante'),'')), '/')),'0') AS INTEGER)
                        ELSE cd_registro_empregado
                   END AS cd_registro_empregado,
                   CASE WHEN UPPER(TRIM(inscricao.observacao)) LIKE UPPER('Acompanha o participante %/%/%') AND inscricao.tipo = 'A'
                        THEN CAST(COALESCE((SELECT pg_split[3] 
                                              FROM funcoes.pg_split(TRIM(REPLACE(TRIM(UPPER(inscricao.observacao)),UPPER('Acompanha o participante'),'')), '/')),'0') AS INTEGER)
                        ELSE seq_dependencia
                   END AS seq_dependencia,		
                   TO_CHAR(inscricao.dt_cadastro, 'DD/MM/YYYY') AS dt_cadastro,
                   inscricao.cadastro_por,
                   evento.nome as evento,
                   inscricao.tipo,
                   inscricao.fl_selecionado,
                   inscricao.fl_desclassificado,
                   inscricao.ds_motivo,
                   inscricao.tp_inscrito,
                   inscricao.fl_presente,
				   inscricao.empresa,
				   inscricao.cargo,
				   inscricao.email,
                   TO_CHAR(inscricao.dt_confirma,'DD/MM/YYYY HH24:MI:SS') AS dt_confirma,
                   inscricao.observacao,
                   inscricao.cpf,
                   inscricao.telefone
              FROM projetos.eventos_institucionais_inscricao inscricao
              JOIN projetos.eventos_institucionais evento 
                ON evento.cd_evento = inscricao.cd_eventos_institucionais
             WHERE inscricao.dt_exclusao IS NULL
                " . (((trim($args['dt_inscricao_inicio']) != "") and (trim($args['dt_inscricao_fim']) != "")) ? "AND CAST(inscricao.dt_cadastro AS DATE) BETWEEN TO_DATE('" . trim($args['dt_inscricao_inicio']) . "','DD/MM/YYYY') AND TO_DATE('" . trim($args['dt_inscricao_fim']) . "','DD/MM/YYYY')" : "") . "
                " . (intval($args['cd_eventos_institucionais']) > 0 ? "AND inscricao.cd_eventos_institucionais = " . intval($args['cd_eventos_institucionais']) : "") . "
                " . (trim($args['tipo']) != "" ? "AND inscricao.tipo = '" . trim($args['tipo']) . "'" : "") . "
                " . (trim($args['fl_presente']) != "" ? "AND inscricao.fl_presente = '" . trim($args['fl_presente']) . "'" : "") . "
                " . (trim($args['tp_inscrito']) != "" ? "AND inscricao.tp_inscrito = '" . trim($args['tp_inscrito']) . "'" : "") . "
				" . (trim($args['cd_empresa']) != "" ? "AND inscricao.cd_empresa = " . intval($args['cd_empresa'])  : "") . "
				" . (trim($args['seq_dependencia']) != "" ? "AND inscricao.seq_dependencia = " . intval($args['seq_dependencia']) : "") . "
				" . (trim($args['cd_registro_empregado']) != "" ? "AND inscricao.cd_registro_empregado = " . intval($args['cd_registro_empregado']) : "") . "
				" . (trim($args['nome']) != "" ? "AND UPPER(funcoes.remove_acento(inscricao.nome)) LIKE UPPER('%' || funcoes.remove_acento('" . trim(str_replace(' ','%',utf8_decode($args['nome']))) . "') || '%')" : "") . "";
        //echo "<pre>$qr_sql</pre>";
        $result = $this->db->query($qr_sql);
    }

    function lista_evento(&$result, $args=array())
    {
        $qr_sql = "
            SELECT e.cd_evento AS value,
                   e.nome AS text
              FROM projetos.eventos_institucionais e
             WHERE e.cd_evento = " . intval($args['cd_eventos_institucionais']) . "
                OR
                 (
                     e.cd_tipo='EVEI'
                 AND dt_exclusao IS NULL
                 AND (
                     CASE WHEN COALESCE(e.qt_inscricao,0) = 0
                            OR e.qt_inscricao >
                            (
                                SELECT COUNT(*)
                                  FROM projetos.eventos_institucionais_inscricao eii
                                 WHERE eii.dt_exclusao IS NULL
                                   AND eii.cd_eventos_institucionais = e.cd_evento
                            )
                          THEN 'S'
                          ELSE 'N'
                      END
                      ) = 'S'
                  AND (
                       CASE WHEN CURRENT_TIMESTAMP BETWEEN COALESCE(e.dt_ini_inscricao,CURRENT_TIMESTAMP)
                             AND COALESCE(e.dt_fim_inscricao,CURRENT_TIMESTAMP)
                             AND CURRENT_TIMESTAMP < e.dt_inicio
                        THEN 'S'
                        ELSE 'N'
                        END
                       ) = 'S'
		)
            ORDER BY e.nome";
        $result = $this->db->query($qr_sql);
    }

    function descricao_evento(&$result, $args=array())
    {
        $qr_sql = "
            SELECT e.nome,
                   TO_CHAR(e.dt_inicio,'DD/MM/YYYY') AS dt_inicio,
                   e.local_evento,
                   c.nome_cidade AS nome_cidade
              FROM projetos.eventos_institucionais e
              JOIN expansao.cidades c
                ON c.cd_municipio_ibge=e.cd_cidade
               AND sigla_uf='RS'
             WHERE cd_evento=" . intval($args['cd_eventos_institucionais']);
        $result = $this->db->query($qr_sql);
    }

    function salvar(&$result, $args=array())
    {

        $retorno = 0;

        if (intval($args["cd_eventos_institucionais_inscricao"]) > 0)
        {
            $retorno = $args["cd_eventos_institucionais_inscricao"];

            $qr_sql = "
               UPDATE projetos.eventos_institucionais_inscricao
                  SET cd_eventos_institucionais = " . intval($args["cd_eventos_institucionais"]) . ",
                      cd_empresa            = " . intval($args["cd_empresa"]) . ",
                      cd_registro_empregado = " . intval($args['cd_registro_empregado']) . ",
                      seq_dependencia       = " . intval($args['seq_dependencia']) . ",
                      nome                  = UPPER(funcoes.remove_acento('" . trim($args['nome']) . "')),
                      cpf                   = '" . $args['cpf'] . "',
                      telefone              = '" . $args['telefone'] . "',
                      email                 = '" . $args['email'] . "',
                      observacao            = '" . $args['observacao'] . "',
                      tipo                  = '" . $args['tipo'] . "',
                      endereco              = '" . $args['endereco'] . "',
                      cidade                = '" . $args['cidade'] . "',
                      cep                   = '" . $args['cep'] . "',
                      uf                    = " . ($args['uf'] == "" ? "DEFAULT" : "'" . $args['uf'] . "'") . ",
                      fl_desclassificado    = '" . $args['desclassificado'] . "',
                      fl_selecionado        = '" . $args['selecionado'] . "',
                      ds_motivo             = " . ($args['motivo'] == "" ? "DEFAULT" : "'" . $args['motivo'] . "'") . ",
                      empresa               = '" . trim($args['empresa']) . "',
                      tp_inscrito           = " . ($args['identificacao'] == "" ? "DEFAULT" : "'" . $args['identificacao'] . "'") . " 
                WHERE cd_eventos_institucionais_inscricao = " . intval($args["cd_eventos_institucionais_inscricao"]);

            $this->db->query($qr_sql);
        }
        else
        {

            $new_id = intval($this->db->get_new_id("projetos.eventos_institucionais_inscricao", "cd_eventos_institucionais_inscricao"));
            $retorno = $new_id;
            $qr_sql = "
                    INSERT INTO projetos.eventos_institucionais_inscricao
                         (
                            cd_eventos_institucionais_inscricao,
                            cd_eventos_institucionais,
                            cd_empresa,
                            cd_registro_empregado,
                            seq_dependencia,
                            nome,
                            cpf,
                            telefone,
                            email,
                            observacao,
                            dt_cadastro,
                            cadastro_por,
                            tipo,
                            endereco,
                            cidade,
                            cep,
                            uf,
                            fl_desclassificado,
                            fl_selecionado,
                            ds_motivo,
                            tp_inscrito,
                            empresa
                         )
                    VALUES
                         (
                            " . intval($new_id) . ",
                            " . intval($args['cd_eventos_institucionais']) . ",
                            " . intval($args['cd_empresa']) . ",
                            " . intval($args['cd_registro_empregado']) . ",
                            " . intval($args['seq_dependencia']) . ",
                            UPPER(funcoes.remove_acento('" . trim($args['nome']) . "')),
                            '" . $args['cpf'] . "',
                            '" . $args['telefone'] . "',
                            '" . $args['email'] . "',
                            '" . $args['observacao'] . "',
                            CURRENT_TIMESTAMP,
                            '" . $args['cadastro_por'] . "',
                            '" . $args['tipo'] . "',
                            '" . $args['endereco'] . "',
                            '" . $args['cidade'] . "',
                            '" . $args['cep'] . "',
                            " . ($args['uf'] == "" ? "DEFAULT" : "'" . $args['uf'] . "'") . ",
                            '" . $args['desclassificado'] . "',
                            '" . $args['selecionado'] . "',
                            " . ($args['motivo'] == "" ? "DEFAULT" : "'" . $args['motivo'] . "'") . ",
                            " . intval($args['identificacao']) . ",
                            '" . $args['empresa'] . "'
                         );";
            $this->db->query($qr_sql);

            $qr_sql = "SELECT email_texto,
                              email_assunto
                         FROM projetos.eventos_institucionais
                        WHERE cd_evento = " . intval($args['cd_eventos_institucionais']);
            $result = $this->db->query($qr_sql);
            $email = $result->row_array();

            if ((trim($args['email']) != "") and (trim($email['email_texto']) != "") and (trim($email['email_assunto']) != ""))
            {
                $qr_sql = "
                   INSERT INTO projetos.envia_emails
                             (
                               dt_envio,
                               de,
                               para,
                               cc,
                               cco,
                               assunto,
                               texto,
                               cd_empresa,
                               cd_registro_empregado,
                               seq_dependencia,
                               cd_evento
                             )
                        VALUES
                             (
                               CURRENT_TIMESTAMP,
                               'Fundação CEEE',
                               '" . $args['email'] . "',
                               '',
                               '',
                               '" . trim($email['email_assunto']) . "',
                               '" . trim($email['email_texto']) . "',
                               " . (trim($args['cd_empresa']) == "" ? 'DEFAULT' : $args['cd_empresa']) . ",
                               " . (trim($args['cd_registro_empregado']) == "" ? 'DEFAULT' : $args['cd_registro_empregado']) . ",
                               " . (trim($args['seq_dependencia']) == "" ? 'DEFAULT' : $args['seq_dependencia']) . ",
                               59
                             );
                   ";
                $this->db->query($qr_sql);
            }
        }
        return $retorno;
    }

    function carrega(&$result, $args=array())
    {
        $qr_sql = "
            SELECT eii.cd_eventos_institucionais_inscricao,
                   eii.cd_eventos_institucionais,
                   eii.cd_empresa,
                   eii.cd_registro_empregado,
                   eii.seq_dependencia,
                   eii.tipo,
                   eii.tp_inscrito AS identificacao,
                   eii.nome,
                   eii.cpf,
                   eii.telefone,
                   eii.email,
                   eii.endereco,
                   eii.cidade,
                   eii.uf,
                   eii.cep,
                   eii.observacao AS obs,
                   eii.fl_selecionado AS selecionado,
                   eii.fl_desclassificado AS desclassificado,
                   eii.ds_motivo AS motivo,
                   eii.empresa AS empresa,
				   CASE WHEN CURRENT_TIMESTAMP BETWEEN COALESCE(e.dt_ini_inscricao,CURRENT_TIMESTAMP)
                             AND COALESCE(e.dt_fim_inscricao,CURRENT_TIMESTAMP)
                             AND CURRENT_TIMESTAMP < e.dt_inicio
                        THEN 'S'
                        ELSE 'N'
                        END AS fl_perm
              FROM projetos.eventos_institucionais_inscricao eii
			  JOIN projetos.eventos_institucionais e
			    ON eii.cd_eventos_institucionais = e.cd_evento
             WHERE eii.cd_eventos_institucionais_inscricao=" . intval($args['cd_eventos_institucionais_inscricao']);

        $result = $this->db->query($qr_sql);
    }

    function anexo(&$result, $args=array())
    {
        $qr_sql = "
            SELECT cd_eventos_institucionais_inscricao_anexo, 
                   cd_eventos_institucionais_inscricao, 
                   ds_arquivo, 
                   ds_arq_fisico, 
                   nr_tamanho, 
                   tipo
              FROM projetos.eventos_institucionais_inscricao_anexo		
             WHERE cd_eventos_institucionais_inscricao = " . intval($args['cd_eventos_institucionais_inscricao']) . ";
				  ";

        $result = $this->db->query($qr_sql);
    }

    function delete(&$result, $args=array())
    {
        $qr_sql = "
            UPDATE projetos.eventos_institucionais_inscricao
               SET dt_exclusao = CURRENT_TIMESTAMP
             WHERE cd_eventos_institucionais_inscricao = " . intval($args['cd_eventos_institucionais_inscricao']);

        $this->db->query($qr_sql);
    }
    
    function participacoes(&$result, $args=array())
    {
        $qr_sql = "
            SELECT e.nome,
                   ei.fl_presente,
                   TO_CHAR(e.dt_inicio,' DD/MM/YYYY ') AS dt_inicio
              FROM projetos.eventos_institucionais_inscricao ei
              JOIN projetos.eventos_institucionais e
                ON e.cd_evento = ei.cd_eventos_institucionais
             WHERE ei.dt_exclusao IS NULL
               AND e.dt_exclusao IS NULL
               AND ei.cd_registro_empregado = ".intval($args['cd_registro_empregado'])."
               AND ei.cd_empresa            = ".intval($args['cd_empresa'])."
               AND ei.seq_dependencia       = ".intval($args['seq_dependencia'])."
            ORDER BY e.dt_inicio DESC";

         $result = $this->db->query($qr_sql);
    }

}

?>