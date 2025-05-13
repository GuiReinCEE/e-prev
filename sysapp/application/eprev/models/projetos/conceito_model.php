<?php
class conceito_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

    function carrega( &$result, $args=array() )
	{
        $qr_sql = "
            SELECT descricao,
                   codigo,
                   0 AS nr_order
              FROM public.listas
             WHERE codigo = 'CACI'
             UNION
            SELECT descricao,
                   codigo,
                   1 AS nr_order
              FROM public.listas
             WHERE codigo = 'CBCI'
             UNION
            SELECT descricao,
                   codigo,
                   2 AS nr_order
              FROM public.listas
             WHERE codigo = 'CCCI'
             UNION
            SELECT descricao,
                   codigo,
                   3 AS nr_order
              FROM public.listas
             WHERE codigo = 'CDCI'
             UNION
            SELECT descricao,
                   codigo,
                   4 AS nr_order
              FROM public.listas
             WHERE codigo = 'CECI'
             UNION
            SELECT descricao,
                   codigo,
                   5 AS nr_order
              FROM public.listas
             WHERE codigo = 'CFCI'
             UNION
            SELECT descricao,
                   codigo,
                   6 AS nr_order
              FROM public.listas
             WHERE codigo = 'CACE'
             UNION
            SELECT descricao,
                   codigo,
                   8 AS nr_order
              FROM public.listas
             WHERE codigo = 'CBCE'
             UNION
            SELECT descricao,
                   codigo,
                   9 AS nr_order
              FROM public.listas
             WHERE codigo = 'CCCE'
             UNION
            SELECT descricao,
                   codigo,
                   10 AS nr_order
              FROM public.listas
             WHERE codigo = 'CDCE'
             UNION
            SELECT descricao,
                   codigo,
                   11 AS nr_order
              FROM public.listas
             WHERE codigo = 'CECE'
             UNION
            SELECT descricao,
                   codigo,
                   12 AS nr_order
              FROM public.listas
             WHERE codigo = 'CFCE'
             UNION
            SELECT descricao,
                   codigo,
                   13 AS nr_order
              FROM public.listas
             WHERE codigo = 'CARE'
             UNION
            SELECT descricao,
                   codigo,
                   14 AS nr_order
              FROM public.listas
             WHERE codigo = 'CBRE'
             UNION
            SELECT descricao,
                   codigo,
                   15 AS nr_order
              FROM public.listas
             WHERE codigo = 'CCRE'
             UNION
            SELECT descricao,
                   codigo,
                   16 AS nr_order
              FROM public.listas
             WHERE codigo = 'CDRE'
             UNION
            SELECT descricao,
                   codigo,
                   17 AS nr_order
              FROM public.listas
             WHERE codigo = 'CERE'
             UNION
            SELECT descricao,
                   codigo,
                   18 AS nr_order
              FROM public.listas
             WHERE codigo = 'CFRE'
             ORDER BY nr_order
            ";

        $result = $this->db->query($qr_sql);
    }

    function salvar( &$result, $args=array() )
    {
        foreach ($args as $item)
        {
            $qr_sql = "
				UPDATE public.listas
                   SET descricao = '".trim($item['descricao'])."'
                 WHERE codigo = '".$item['codigo']."'";
            $result = $this->db->query($qr_sql);
        }
    }
}
?>