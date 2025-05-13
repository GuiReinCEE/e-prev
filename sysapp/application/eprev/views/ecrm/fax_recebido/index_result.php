<?php
#echo "<PRE>".print_r($ar_lista,true)."</PRE>";exit;

if(gerencia_in(Array('GAP')))
{
    echo  br().'
    <CENTER>
    <input id="btProtInterno" type="button" onclick="protocoloInterno();" value="Novo Protocolo Interno" class="botao" >
    <input id="btProtDigitalizacao" type="button" onclick="protocoloDigitalizacao();" value="Novo Protocolo Digitalização (Digital)" class="botao" >
    </CENTER>
    ';
}

$body=array();
$head = array( 
    '<input type="checkbox"  id="checkboxCheckAll" onclick="checkAll();" title="Clique para Marcar ou Desmarcar Todos">',
    'Dt Recebido',
    'Fax',
    'Documento',
    'Nome',
    'Ramal',
    'Destino',
    'Email',
    'Msg',
    'Acompanhamento',
    ''
);

foreach($ar_lista as $ar_item)
{
    $id = $ar_item['cd_fax']."_";

    $campo_check = array(
            'name'        => $id.'chk',
            'id'          => $id.'chk',
            'value'       => $ar_item['cd_fax'],
            'checked'     => FALSE
            );

    $campo_doc = array(
                    'id_codigo'  => $id.'id_codigo',
                    'id_nome'    => $id.'nome_documento',
                    'formulario' =>FALSE
                    );		

    $body[] = array(
                form_checkbox($campo_check),
                $ar_item['dt_inclusao'],
                anchor("ecrm/fax_recebido/ver/".$ar_item["cd_fax"],str_replace("recvq/","",$ar_item["arquivo"]),array('title' => 'Visualizar FAX','target' => '_blank')),
                form_default_tipo_documento($campo_doc),
                form_default_participante(
                                            array($id.'cd_empresa',$id.'cd_registro_empregado',$id.'seq_dependencia', $id.'nome_participante')
                                            ,''
                                            , false
                                            , true
                                            , true
                                            , ''
                                            , false
                                            )
                .br(1)
                .form_input(array('name' => $id.'nome_participante', 'id' => $id.'nome_participante'), '', 'style="width:300px;"'),			
                $ar_item['ramal'],			
                $ar_item['destino'],			
                array($ar_item['email'],'text-align:left;'),
                array($ar_item['msg'],'text-align:left;'),
                array(nl2br($ar_item['acompanhamento']),'text-align:justify;'),
                anchor("ecrm/fax_recebido/acompanhamento/".$ar_item["cd_fax"],'[acompanhamento]'),
            );
}

$ar_oculta = Array(0,3,4,5,6,7);

if(gerencia_in(Array('GAP')))
{
    $ar_oculta = Array(5,7);
}

if(gerencia_in(Array('GI')))
{
    $ar_oculta = Array();
}

$this->load->helper('grid');
$grid = new grid();
$grid->id_tabela  = 'tabela_fax';
$grid->head       = $head;
$grid->body       = $body;
$grid->col_oculta = $ar_oculta;

echo $grid->render();

echo "<BR><BR><BR>";
?>