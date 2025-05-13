<?php
class ri_sat_venda_plano_pos_vendas_empresas_model extends Model
{
    function __construct()
    {
        parent::Model();
    }

    function listar(&$result, $args=array())
    {
        $qr_sql = "
            SELECT i.cd_ri_sat_venda_plano_pos_vendas_empresas,
                   i.cd_indicador_tabela,
                   i.dt_referencia,
                   TO_CHAR(i.dt_referencia,'YYYY') AS ano_referencia,
                   TO_CHAR(i.dt_referencia,'MM/YYYY') AS mes_referencia,
                   TO_CHAR(i.dt_inclusao,'DD/MM/YYYY') AS dt_inclusao,
                   TO_CHAR(i.dt_exclusao,'DD/MM/YYYY') AS dt_exclusao,
                   i.cd_usuario_inclusao,
                   i.cd_usuario_exclusao,
                   i.fl_media,
                   i.observacao,
                   i.nr_valor_1,
                   i.nr_valor_2,
                   i.nr_percentual_f,
                   i.nr_meta,
                   p.sigla AS empresa,
                   i.cd_empresa
              FROM indicador_plugin.ri_sat_venda_plano_pos_vendas_empresas i
              JOIN public.patrocinadoras p
                ON p.cd_empresa = i.cd_empresa
             WHERE i.dt_exclusao IS NULL
               AND (i.fl_media = 'S' OR i.cd_indicador_tabela = ".intval($args['cd_indicador_tabela']).")
             ORDER BY i.dt_referencia ASC, p.sigla ASC;";

        $result = $this->db->query($qr_sql);
    }
    
    function empresa(&$result, $args=array())
    {
        $qr_sql = "
            SELECT cd_empresa AS value,
                   sigla AS text
              FROM public.patrocinadoras
             WHERE cd_empresa NOT IN (4, 5)
             ORDER BY nome_empresa;";
        
        $result = $this->db->query($qr_sql);
    }
	
    function carrega_referencia(&$result, $args=array())
    {
        $qr_sql = "
            SELECT nr_meta, 
                   cd_indicador_tabela 
              FROM indicador_plugin.ri_sat_venda_plano_pos_vendas_empresas 
             WHERE dt_exclusao IS NULL 
             ORDER BY dt_referencia DESC 
             LIMIT 1;";

        $result = $this->db->query($qr_sql);
    }
	
    function carrega(&$result, $args=array())
    {
        $qr_sql = "
            SELECT cd_ri_sat_venda_plano_pos_vendas_empresas,
                   cd_indicador_tabela,
                   TO_CHAR(dt_referencia,'DD/MM/YYYY') AS dt_referencia,
                   nr_valor_1,
                   nr_valor_2,
                   nr_percentual_f,
                   nr_meta,
                   fl_media,
                   observacao,
                   cd_empresa
              FROM indicador_plugin.ri_sat_venda_plano_pos_vendas_empresas
             WHERE cd_ri_sat_venda_plano_pos_vendas_empresas = ".intval($args['cd_ri_sat_venda_plano_pos_vendas_empresas']).";";

        $result = $this->db->query($qr_sql);	
    }	

    function salvar(&$result, $args=array())
    {
        if(intval($args['cd_ri_sat_venda_plano_pos_vendas_empresas']) == 0)
        {
            $qr_sql = "
                INSERT INTO indicador_plugin.ri_sat_venda_plano_pos_vendas_empresas 
                     (
                        dt_referencia, 
                        cd_empresa,
                        nr_valor_1, 
                        nr_valor_2, 
                        nr_meta, 
                        cd_indicador_tabela, 
                        fl_media, 
                        observacao,
                        cd_usuario_inclusao,
                        cd_usuario_alteracao
                     ) 
                VALUES 
                     ( 
                        ".(trim($args['dt_referencia']) == "" ? "DEFAULT" : "TO_DATE('".trim($args["dt_referencia"])."', 'DD/MM/YYYY')").",
                        ".(trim($args['cd_empresa']) == "" ? "DEFAULT" : intval($args['cd_empresa'])).",
                        ".(trim($args['nr_valor_1']) == "" ? "DEFAULT" : floatval($args['nr_valor_1'])).",
                        ".(trim($args['nr_valor_2']) == "" ? "DEFAULT" : floatval($args['nr_valor_2'])).",
                        ".(trim($args['nr_meta']) == "" ? "DEFAULT" : floatval($args['nr_meta'])).",
                        ".(intval($args['cd_indicador_tabela']) == 0 ? "DEFAULT" : intval($args['cd_indicador_tabela'])).",
                        ".(trim($args['fl_media']) == "" ? "''" : "'".trim($args["fl_media"])."'").",
                        ".(trim($args['observacao']) == "" ? "DEFAULT" : "'".trim($args["observacao"])."'").",
                        ".intval($args['cd_usuario']).",
                        ".intval($args['cd_usuario'])."
                     );";
        }
        else
        {
            $qr_sql = "
                UPDATE indicador_plugin.ri_sat_venda_plano_pos_vendas_empresas
                   SET dt_referencia        = ".(trim($args['dt_referencia']) == "" ? "DEFAULT" : "TO_DATE('".trim($args["dt_referencia"])."', 'DD/MM/YYYY')").",
                       cd_empresa             = ".(trim($args['cd_empresa']) == "" ? "DEFAULT" : intval($args['cd_empresa'])).",
                       nr_valor_1           = ".(trim($args['nr_valor_1']) == "" ? "DEFAULT" : floatval($args['nr_valor_1'])).",
                       nr_valor_2           = ".(trim($args['nr_valor_2']) == "" ? "DEFAULT" : floatval($args['nr_valor_2'])).",
                       nr_meta              = ".(trim($args['nr_meta']) == "" ? "DEFAULT" : floatval($args['nr_meta'])).",
                       cd_indicador_tabela  = ".(intval($args['cd_indicador_tabela']) == 0 ? "DEFAULT" : intval($args['cd_indicador_tabela'])).",
                       fl_media             = ".(trim($args['fl_media']) == "" ? "''" : "'".trim($args["fl_media"])."'").",
                       observacao           = ".(trim($args['observacao']) == "" ? "DEFAULT" : "'".trim($args["observacao"])."'").",
                      cd_usuario_alteracao = ".intval($args['cd_usuario']).",
                       dt_alteracao         = CURRENT_TIMESTAMP
                 WHERE cd_ri_sat_venda_plano_pos_vendas_empresas = ".intval($args['cd_ri_sat_venda_plano_pos_vendas_empresas']).";";
        }

        $result = $this->db->query($qr_sql);
    }

    function excluir(&$result, $args=array())
    {
        $qr_sql = " 
            UPDATE indicador_plugin.ri_sat_venda_plano_pos_vendas_empresas
               SET dt_exclusao         = CURRENT_TIMESTAMP, 
                   cd_usuario_exclusao = ".intval($args['cd_usuario'])."
             WHERE cd_ri_sat_venda_plano_pos_vendas_empresas = ".intval($args['cd_ri_sat_venda_plano_pos_vendas_empresas']).";"; 

        $result = $this->db->query($qr_sql);
    }
	
    function fechar_periodo(&$result, $args=array())
    {
        $qr_sql = "
            UPDATE indicador.indicador_tabela 
               SET dt_fechamento_periodo         = CURRENT_TIMESTAMP, 
                   cd_usuario_fechamento_periodo = ".$args['cd_usuario']." 
             WHERE cd_indicador_tabela = ".intval($args['cd_indicador_tabela']).";";

        $result = $this->db->query($qr_sql);
    }
}
?>