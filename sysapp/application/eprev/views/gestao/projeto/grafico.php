<html>
    <head>
        <title>Cronograma de Projeto - <?= $row['ds_projeto'] ?></title>
        <style type="text/css">
            @font-face {
                font-family: 'YanoneKaffeesatzRegular';
                src: url('<?php echo base_url(); ?>fonts/Yanone_Kaffeesatz/yanonekaffeesatz-regular-webfont.eot');
                src: url('<?php echo base_url(); ?>fonts/Yanone_Kaffeesatz/yanonekaffeesatz-regular-webfont.eot?#iefix') format('embedded-opentype'),
                     url('<?php echo base_url(); ?>fonts/Yanone_Kaffeesatz/yanonekaffeesatz-regular-webfont.woff') format('woff'),
                     url('<?php echo base_url(); ?>fonts/Yanone_Kaffeesatz/yanonekaffeesatz-regular-webfont.ttf') format('truetype'),
                     url('<?php echo base_url(); ?>fonts/Yanone_Kaffeesatz/yanonekaffeesatz-regular-webfont.svg#YanoneKaffeesatzRegular') format('svg');
                font-weight: normal;
                font-style: normal;
            }   
        </style>
        <link rel="stylesheet" type="text/css" href="<?= base_url() ?>js/jquery-plugins/jquery.ganttView/jquery.ganttView.css" />
        <script type="text/javascript" src="<?= base_url() ?>js/jquery-plugins/jquery.ganttView/lib/jquery-1.4.2.js"></script>
        <script type="text/javascript" src="<?= base_url() ?>js/jquery-plugins/jquery.ganttView/lib/date.js"></script>
        <script type="text/javascript" src="<?= base_url() ?>js/jquery-plugins/jquery.ganttView/lib/jquery-ui-1.8.4.js"></script>
        <script type="text/javascript" src="<?= base_url() ?>js/jquery-plugins/jquery.ganttView/jquery.ganttView.js"></script>
        <script type="text/javascript" src="<?= base_url() ?>js/jquery-plugins/jquery.ganttView/example/data.js"></script>
        <style>
            * {
                margin: 0;
                padding: 0;
            }

            body {
                font-family: Arial;
            }

            .font_yannoka {
                font-family: 'YanoneKaffeesatzRegular';
            }

            #header {
                margin: 2px;
            }

            #header_one img {
                width: 300px;
            }

            #titulo {
                margin-top:-50px;
            }

            #acoes {
                position:relative; 
                float:left;
                left: 20px;
            }

            #pagina {
                height: 93%;
                width: 100%;
                position: relative;
            }

            #pagina .well-black {
                overflow-y : auto;
                overflow-x : auto;
            }

            #div_processo {
                margin-left: 300px;
            }
            .well {
                background-color: #ffffff;
                border: 1px solid #e3e3e3;
                border-radius: 4px;
                box-shadow: 0 1px 1px rgba(0, 0, 0, 0.05) inset;
                margin-bottom: 20px;
                min-height: 20px;
                padding: 19px;
            }
            .text-center {
              text-align:center;
            }
        </style>
    </head>
    <body>
        <div id="header">
            <div id="header_one">
                <div class="text-left">
                    <img src="<?=base_url() ?>img/certificado_logo_fundacao.png"/>
                </div>
                <div id="titulo" class="text-center">
                    <h1 class="font_yannoka">Cronograma de Projeto - <?= $row['ds_projeto'] ?></h1>
                </div>
            </div>
            <br/>
        </div>
        <br/>
        <div id="pagina">
            <div id="pagina_item" class="span12 well" style="height:93%; margin-bottom:0px;">
                <div id="slide_indicador" style="width:100%;">
                    <div id="ganttChart"></div>
                </div>
            </div>
        </div>
        <script>
            $(function () {
                var data = [
                    <? foreach($collection as $key => $item): ?>
                    {
                        id    : <?= $item['cd_projeto_cronograma'] ?>, 
                        name  : "<?= $item['ds_projeto_cronograma'] ?>", 
                        series: [
                            { 
                                name  : "Planejado", 
                                start : new Date(<?= $item['ano_planejado_ini'] ?>, <?= mes_date_js($item['mes_planejado_ini']) ?>, <?= $item['dia_planejado_ini'] ?>), 
                                end   : new Date(<?= $item['ano_planejado_fim'] ?>, <?= mes_date_js($item['mes_planejado_fim']) ?>, <?= $item['dia_planejado_fim'] ?>) 
                            }
                        ]
                    }<?= (isset($collection[($key+1)]) ? ',' : '') ?>
                    <? endforeach; ?>
                ];

                $("#ganttChart").ganttView({ 
                    data: data,
                    slideWidth: '100%',
                    behavior: {
                        draggable: false,
                        resizable: false,
                        onClick: function (data) { 
                            var msg = "Data Início : " + data.start.toString("dd/MM/yyyy") + "\n Data Fim : " + data.end.toString("dd/MM/yyyy");

                            alert(msg);
                        }
                    }
                });
                
                // $("#ganttChart").ganttView("setSlideWidth", 600);
            });
        </script>
    </body>
</html>