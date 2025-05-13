<?php
class Contrato_aditivo_model extends Model
{
    function __construct()
    {
        parent::Model();
    }

    public function get($cd_empresa, $cd_registro_empregado, $seq_dependencia)
    {
        $qr_sql = "
            SELECT nome,
                   funcoes.format_cpf(TO_CHAR(p.cpf_mf, 'FM00000000000')) AS cpf_mf
              FROM public.participantes p
             WHERE p.cd_empresa            = ".intval($cd_empresa)."
               AND p.cd_registro_empregado = ".intval($cd_registro_empregado)."
               AND p.seq_dependencia       = ".intval($seq_dependencia).";";

        return $this->db->query($qr_sql)->row_array();
    }

}