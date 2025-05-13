<?php
class Atividade_atendimento_cenario_legal_model extends Model
{
    function __construct()
    {
        parent::Model();
    }

    public function get_gerencia($cd_cenario, $cd_gerencia = '')
    {
        $qr_sql = "
            SELECT codigo AS value, 
                   nome AS text
              FROM funcoes.get_gerencias_vigente('DIV, CON', '".$cd_gerencia."')
             WHERE codigo NOT IN (SELECT DISTINCT area FROM projetos.atividades WHERE cd_cenario = ".intval($cd_cenario)." AND area != '".$cd_gerencia."')
             ORDER BY nome;";

        return $this->db->query($qr_sql)->result_array();
    }

    public function conclui_atividade($args = array())
    {
        $qr_sql = "
            UPDATE projetos.atividades 
               SET sistema                  = ".intval($args['sistema']).",
                   pertinencia              = ".(trim($args['pertinencia']) != ''? intval($args['pertinencia']) : "DEFAULT").",
                   ds_justificativa_cenario = ".(trim($args['ds_justificativa_cenario']) != ''? str_escape($args['ds_justificativa_cenario']) : "DEFAULT")."
             WHERE numero = ".intval($args['numero']).";

            INSERT INTO projetos.atividade_historico 
                  ( 
                    cd_atividade, 
                    cd_recurso, 
                    dt_inicio_prev,
                    status_atual,
                    observacoes 
                   )
              VALUES 
                   ( 
                    ".intval($args['numero']).", 
                    ".intval($args['cd_usuario']).",
                    CURRENT_TIMESTAMP,
                    DEFAULT,
                    'Troca de Status'
                   );";

        $this->db->query($qr_sql);
    }

    public function atualiza_cenario($args = array())
    {
        $qr_sql = "
            UPDATE projetos.cenario 
               SET pertinencia          = ".(trim($args['pertinencia']) != '' ? intval($args['pertinencia']) : "DEFAULT").",
                   cd_usuario_alteracao = ".intval($args['cd_usuario']).",
                   dt_alteracao         = CURRENT_TIMESTAMP
             WHERE cd_cenario = ".intval($args['cd_cenario']).";";

        $this->db->query($qr_sql); 
    }

    public function encerra_atividade($args = array())
    {
        $qr_sql = "
            UPDATE projetos.atividades 
               SET dt_fim_real  = CURRENT_TIMESTAMP,
                   status_atual = 'RAGC'
             WHERE numero      = ".intval($args['numero'])."
               AND dt_fim_real IS NULL;

            INSERT INTO projetos.envia_emails 
                 ( 
                    dt_envio, 
                    de,
                    para, 
                    cc,    
                    cco, 
                    assunto,
                    texto,
                    cd_evento
                 )
            VALUES
                 ( 
                    CURRENT_TIMESTAMP, 
                    'Fundacao CEEE',
                    '".trim($args['para'])."',
                    '',
                    '',
                    'Reencaminhamento de divisão de atividade do Cenário Legal',
                    'A atividade ".intval($args['numero'])." referente Cenário Legal ".intval($args['cd_cenario'])." foi reencaminhada da ".trim($args['area_antiga'])." para ".trim($args['cd_gerencia_destino']).".',
                    131
                 );";

        $this->db->query($qr_sql);
    }

    public function get_usuario_gerencia_destino($cd_gerencia_destino)
    {
        $qr_sql = "
            SELECT codigo, 
                   usuario, 
                   guerra, 
                   nome, 
                   divisao
              FROM projetos.usuarios_controledi 
             WHERE divisao = '".trim($cd_gerencia_destino)."' 
               AND indic_03 = '*' 
               AND NOT tipo IN ('X');";

        return $this->db->query($qr_sql)->result_array();
    }

    public function nova_atividade($args = array())
    {
        $numero = intval($this->db->get_new_id('projetos.atividades', 'numero'));

        $qr_sql = "
            INSERT INTO projetos.atividades 
                 (
                    numero, 
                    tipo,           
                    dt_cad,
                    descricao,
                    area,
                    divisao,
                    status_atual,
                    tipo_solicitacao,
                    titulo,
                    cod_solicitante,
                    cod_atendente,
                    cd_cenario
                 )                 
            VALUES 
                 (
                    ".intval($numero).",
                    'L',            
                    CURRENT_TIMESTAMP,  
                    ".str_escape($args['descricao']).", 
                    ".str_escape($args['cd_gerencia_destino']).",
                    'FC',
                    'AIGC',
                    'VP',
                    'Verificação de Procedência',
                    ".intval($args['cod_solicitante']).",
                    ".intval($args['cod_atendente']).",
                    ".intval($args['cd_cenario'])."
                 );

            INSERT INTO projetos.atividade_historico 
                 ( 
                    cd_atividade, 
                    cd_recurso, 
                    dt_inicio_prev,
                    status_atual,
                    observacoes 
                 )
            VALUES 
                 ( 
                    ".intval($numero).",
                    ".intval($args['cd_usuario']).",
                    CURRENT_TIMESTAMP,
                    'RAGC',
                    'Atividade criada por reencaminhamento, Atividade origem: ".intval($args['numero'])."'
                 );";

        $this->db->query($qr_sql);

        return $numero;
    }    

    public function historico_encerra_atividade($args = array())
    {
        $qr_sql = "
            INSERT INTO projetos.atividade_historico 
                 ( 
                    cd_atividade, 
                    cd_recurso, 
                    dt_inicio_prev,
                    status_atual,
                    observacoes 
                 )
            VALUES 
                 ( 
                    ".intval($args['numero']).", 
                    ".intval($args['cd_usuario']).", 
                    CURRENT_TIMESTAMP,
                    'RAGC',
                    'Reencaminhamento para ".trim($args['cd_gerencia_destino']).", Atividade(s): ".implode(', ', $args['atividades'])."'
                 );
                 
            INSERT INTO projetos.atividade_acompanhamento
                 (
                    cd_atividade, 
                    cd_usuario_inclusao,
                    ds_atividade_acompanhamento
                 )
            VALUES 
                 (
                    ".intval($args['numero']).", 
                    ".intval($args['cd_usuario']).", 
                    'Reencaminhamento para ".trim($args['cd_gerencia_destino']).", Atividade(s): ".implode(', ', $args['atividades'])."'
                 ); ";
    
        $this->db->query($qr_sql);
    }

    public function cenario_plano_acao($args = array())
    {
        $cd_cenario_plano_acao = intval($this->db->get_new_id('projetos.cenario_plano_acao', 'cd_cenario_plano_acao'));

        $qr_sql = "
            INSERT INTO projetos.cenario_plano_acao
                 (
                    cd_cenario_plano_acao,
                    cd_cenario,
                    cd_atividade,
                    cd_gerencia_responsavel,
                    cd_usuario_inclusao, 
                    cd_usuario_alteracao
                 )
            VALUES 
                 (
                    ".$cd_cenario_plano_acao.",
                    ".intval($args['cd_cenario']).", 
                    ".intval($args['numero']).", 
                    '".trim($args['cd_gerencia_destino'])."',
                    ".intval($args['cd_usuario']).", 
                    ".intval($args['cd_usuario'])." 
                 );";

        $this->db->query($qr_sql);

        return $cd_cenario_plano_acao;
    }

    public function salvar_pendencia_gestao($args = array())
    {
        $qr_sql = "
            INSERT INTO gestao.pendencia_gestao
                 (
                    cd_reuniao_sistema_gestao_tipo,
                    cd_superior,
                    ds_item,
                    cd_usuario_responsavel,
                    dt_prazo,
                    cd_cenario,
                    cd_atividade,
                    cd_usuario_inclusao,
                    cd_usuario_alteracao
                 )
            VALUES
                 (
                    ".intval($args['cd_reuniao_sistema_gestao_tipo']).",
                    (SELECT area FROM projetos.divisoes WHERE codigo = '".trim($args['cd_gerencia_destino'])."'),
                    ".(trim($args['ds_item']) != '' ? str_escape($args['ds_item']) : "DEFAULT").",
                    ".intval($args['cd_usuario']).",
                    ".(trim($args['dt_prazo']) != ''? "TO_DATE('".$args['dt_prazo']."', 'DD/MM/YYYY')" : "DEFAULT").",
                    ".(trim($args['cd_cenario']) != '' ? intval($args['cd_cenario']) : "DEFAULT").",
                    ".(trim($args['cd_atividade']) != '' ? intval($args['cd_atividade']) : "DEFAULT").",
                    ".intval($args['cd_usuario']).",
                    ".intval($args['cd_usuario'])."
                 );";

        $this->db->query($qr_sql);

        return $cd_pendencia_gestao;
    }

    function email_nova_atividade(&$result, $args=array())
    {
        $qr_sql = "
            SELECT a.numero,
                   a.descricao, 
                   u1.usuario AS atendente, 
                   u1.nome AS nome_atendente
              FROM projetos.atividades a
              JOIN projetos.usuarios_controledi u1
                ON u1.codigo = a.cod_atendente
             WHERE a.numero = ".intval($args["numero_nova_atividade"]).";";

        $row = $this->db->query($qr_sql)->row_array();

        $assunto = 'Nova atividade solicitada - nº '.intval($args["numero_nova_atividade"]);

        $para = $row["atendente"].'@eletroceee.com.br';

        $mensage = '
            Prezada(o) '.$row["nome_atendente"].'

            Foi enviada uma solicitação de Verificação de Procedência (Cenário Legal):
            Atendente: '.$row['nome_atendente'].'
            Atividade: '.$row['numero'].'
            -------------------------------------------------------------
            Descrição: 
            '.$row['descricao'].'
            -------------------------------------------------------------
            Esta mensagem foi enviada pelo Controle de Atividades.';

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
                    cd_evento
                 ) 
            VALUES 
                 ( 
                    CURRENT_TIMESTAMP, 
                    'Cenário Legal', 
                    ".str_escape($para).", 
                    '', 
                    '', 
                    ".str_escape($assunto).", 
                    ".str_escape($mensage).",
                    131
                 );";

        $result = $this->db->query($qr_sql);
    }
}	