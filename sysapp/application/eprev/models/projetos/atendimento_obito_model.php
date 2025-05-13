<?php

class Atendimento_obito_model extends Model
{

    function __construct()
    {
        parent::Model();
    }

    function listar(&$result, $args=array())
    {
        $qr_sql = "
            SELECT ao.cd_atendimento_obito, 
                   ao.cd_empresa, 
                   ao.cd_registro_empregado, 
                   ao.seq_dependencia,
                   p.nome,
                   TO_CHAR(p.dt_obito, 'DD/MM/YYYY') AS dt_obito,
                   TO_CHAR(p.dt_dig_obito, 'DD/MM/YYYY') AS dt_dig_obito,
                   TO_CHAR((SELECT MAX(aoa.dt_inclusao) 
              FROM projetos.atendimento_obito_acompanhamento aoa
             WHERE aoa.dt_exclusao          IS NULL
               AND aoa.cd_atendimento_obito = ao.cd_atendimento_obito), 'DD/MM/YYYY HH24:MI:SS') AS dt_acompanha,
                   TO_CHAR(ao.dt_encerrado, 'DD/MM/YYYY HH24:MI:SS') AS dt_encerrado,
                   ao.cd_usuario_encerrado,
                   uc.nome AS usuario_encerrado
              FROM projetos.atendimento_obito ao
              JOIN public.participantes p
                ON p.cd_empresa            = ao.cd_empresa
               AND p.cd_registro_empregado = ao.cd_registro_empregado
               AND p.seq_dependencia       = ao.seq_dependencia
              LEFT JOIN projetos.usuarios_controledi uc
                ON uc.codigo = ao.cd_usuario_encerrado
             WHERE ao.dt_exclusao IS NULL
             " . ((trim($args['cd_empresa']) != "") ? " AND ao.cd_empresa = " . intval($args['cd_empresa']) : "") . "
             " . ((trim($args['cd_registro_empregado']) != "") ? " AND ao.cd_registro_empregado = " . intval($args['cd_registro_empregado']) : "") . "
             " . ((trim($args['seq_dependencia']) != "") ? " AND ao.seq_dependencia = " . intval($args['seq_dependencia']) : "") . "
             " . ((trim($args['nome']) != "") ? " AND UPPER(p.nome) like UPPER('%" . trim($args['nome']) . "%')" : "") . "		
             " . (((trim($args['dt_obito_ini']) != "") and (trim($args['dt_obito_fim']) != "")) ? " AND DATE_TRUNC('day', p.dt_obito) BETWEEN TO_DATE('" . $args['dt_obito_ini'] . "', 'DD/MM/YYYY') AND TO_DATE('" . $args['dt_obito_fim'] . "', 'DD/MM/YYYY')" : "") . "						 
             " . (((trim($args['dt_dig_obito_ini']) != "") and (trim($args['dt_dig_obito_fim']) != "")) ? " AND DATE_TRUNC('day', p.dt_dig_obito) BETWEEN TO_DATE('" . $args['dt_dig_obito_ini'] . "', 'DD/MM/YYYY') AND TO_DATE('" . $args['dt_dig_obito_fim'] . "', 'DD/MM/YYYY')" : "") . "						 
";

        #echo "<pre style='text-align:left;'>$qr_sql</pre>";exit;
        $result = $this->db->query($qr_sql);
    }

    function cadastro(&$result, $args=array())
    {
        $qr_sql = "
            SELECT ao.cd_atendimento_obito, 
                   ao.cd_empresa, 
                   ao.cd_registro_empregado, 
                   ao.seq_dependencia,
                   p.nome,
                   TO_CHAR(p.dt_obito, 'DD/MM/YYYY') AS dt_obito,
                   TO_CHAR(p.dt_dig_obito, 'DD/MM/YYYY') AS dt_dig_obito,
                   TO_CHAR((SELECT MAX(aoa.dt_inclusao) 
                              FROM projetos.atendimento_obito_acompanhamento aoa
                             WHERE aoa.dt_exclusao          IS NULL
                               AND aoa.cd_atendimento_obito = ao.cd_atendimento_obito), 'DD/MM/YYYY HH24:MI:SS') AS dt_acompanha,
                   TO_CHAR(ao.dt_encerrado, 'DD/MM/YYYY HH24:MI:SS') AS dt_encerrado,
                   ao.cd_usuario_encerrado,
                   uc.nome AS usuario_encerrado,
                   ao.dt_exclusao,
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
                   p.unidade_federativa
              FROM projetos.atendimento_obito ao
              JOIN public.participantes p
                ON p.cd_empresa            = ao.cd_empresa
               AND p.cd_registro_empregado = ao.cd_registro_empregado
               AND p.seq_dependencia       = ao.seq_dependencia
              LEFT JOIN projetos.usuarios_controledi uc
                ON uc.codigo = ao.cd_usuario_encerrado
             WHERE ao.cd_atendimento_obito = " . intval($args['cd_atendimento_obito']) . "
";

        #echo "<pre style='text-align:left;'>$qr_sql</pre>";exit;
        $result = $this->db->query($qr_sql);
    }

    function acompanhamentoListar(&$result, $args=array())
    {
        $qr_sql = "
            SELECT aoa.cd_atendimento_obito_acompanhamento, 
                   TO_CHAR(aoa.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
                   aoa.cd_usuario_inclusao,
                   uc.nome AS ds_usuario_inclusao,
                   aoa.acompanhamento
              FROM projetos.atendimento_obito_acompanhamento aoa
              JOIN projetos.usuarios_controledi uc
                ON uc.codigo = aoa.cd_usuario_inclusao					  
             WHERE aoa.dt_exclusao IS NULL
               AND aoa.cd_atendimento_obito = " . intval($args['cd_atendimento_obito']) . "
             ORDER BY aoa.dt_inclusao DESC
		          ";

        #echo "<pre style='text-align:left;'>$qr_sql</pre>";exit;
        $result = $this->db->query($qr_sql);
    }

    function salvar(&$result, $args=array())
    {
        $retorno = 0;

        if (intval($args['cd_atendimento_obito']) > 0)
        {
            ##INSERT
            $qr_sql = " 
                INSERT INTO projetos.atendimento_obito_acompanhamento
                     (
                       cd_atendimento_obito,
                       acompanhamento, 
                       cd_usuario_inclusao
                     )
                VALUES 
                     (
                       " . (intval($args['cd_atendimento_obito']) == 0 ? "DEFAULT" : $args['cd_atendimento_obito']) . ",
                       " . (trim($args['acompanhamento']) == "" ? "DEFAULT" : "'" . $args['acompanhamento'] . "'") . ",
                       " . (intval($args['cd_usuario']) == 0 ? "DEFAULT" : $args['cd_usuario']) . "
                     );			
					  ";
            $this->db->query($qr_sql);
        }

        #echo "<pre>$qr_sql</pre>";
        #exit;

        return $retorno;
    }

    function encerrar(&$result, $args=array())
    {
        if (intval($args['cd_atendimento_obito']) > 0)
        {
            $qr_sql = " 
                UPDATE projetos.atendimento_obito
                   SET dt_encerrado         = CURRENT_TIMESTAMP,
                       cd_usuario_encerrado = " . $args['cd_usuario'] . "
                 WHERE cd_atendimento_obito = " . intval($args['cd_atendimento_obito']) . "
					  ";
            $this->db->query($qr_sql);
        }
    }

    function dependenteListar(&$result, $args=array())
    {
        $qr_sql = "
            SELECT p.cd_empresa,
                   p.cd_registro_empregado,
                   p.seq_dependencia,
                   p.nome,
                   TO_CHAR(p.dt_nascimento, 'DD/MM/YYYY') AS dt_nascimento, 
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
                   md.descricao_motivo_desligamento
              FROM projetos.atendimento_obito ao
              JOIN public.dependentes d
                ON d.cd_empresa            = ao.cd_empresa
               AND d.cd_registro_empregado = ao.cd_registro_empregado
              LEFT JOIN public.participantes p
                ON p.cd_registro_empregado = d.cd_registro_empregado 
               AND p.seq_dependencia       = d.seq_dependencia 
               AND p.cd_empresa            = d.cd_empresa 
              LEFT JOIN public.grau_parentescos gp 
                ON gp.cd_grau_parentesco   = d.cd_grau_parentesco 
              LEFT JOIN public.motivo_desligamentos md
                ON md.cd_motivo_desligamento = d.cd_motivo_desligamento
             WHERE  ao.dt_exclusao IS NULL
               AND ao.cd_atendimento_obito = " . intval($args['cd_atendimento_obito']) . "
             ORDER BY p.seq_dependencia ASC
		          ";
        
        #echo "<pre style='text-align:left;'>$qr_sql</pre>";exit;
        $result = $this->db->query($qr_sql);
    }

}

?>