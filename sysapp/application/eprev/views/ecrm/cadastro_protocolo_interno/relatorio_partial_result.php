<?php
$body = array();
$head = array(
	'Protocolo', 
	'Envio', 
	'Redirecionamento', 
	'Destino', 
	'Encerrado por', 
	'Recebimento', 
	'Participante', 
	'', 
	'Doc', 
	'Tipo de documento', 
	'Folhas', 
	'Arquivo', 
	'Obs do Recebimento'
);

foreach ($collection as $item)
{
    $arquivo = '';
    if ($item['arquivo'] != '')
    {
        $arquivo = anchor(base_url() . 'up/documento_recebido/' . $item['arquivo'], $item['arquivo_nome'], array('target' => '_blank'));
    }

    $body[] = array(
      anchor("ecrm/cadastro_protocolo_interno/detalhe/" . $item["cd_documento_recebido"], $item["nr_documento_recebido"])
      , $item['dt_envio']
      , $item['dt_redirecionamento']
      , ( ($item['nome_grupo'] != '') ? $item['nome_grupo'] : $item['divisao_usuario_destino'] . '-' . $item['guerra_usuario_destino'] )
      , $item['usuario_encerrado']
      , $item['dt_ok']
      , $item['cd_empresa'] . '/' . $item['cd_registro_empregado'] . '/' . $item['seq_dependencia']
      , $item['nome_participante']
      , $item['cd_tipo_doc']
      , $item['nome_documento']
      , $item['nr_folha']
      , $arquivo
      , $item['ds_observacao_recebimento']
    );
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>