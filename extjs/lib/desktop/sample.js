/*
 * Ext JS Library 2.2.1
 * Copyright(c) 2006-2009, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://extjs.com/license
 */

// Sample desktop configuration
MyDesktop = new Ext.app.App({
	init :function(){
		Ext.QuickTips.init();
	},

	getModules : function(){
		return [
			new MyDesktop.Atividade()
			, new MyDesktop.Cadastro()
			, new MyDesktop.Ecrm()
			, new MyDesktop.Gestao()
			, new MyDesktop.Intranet()
			, new MyDesktop.Plano()
			, new MyDesktop.Servico()
		];
	},

    // config for the start menu
    getStartConfig : function(){
        return {
            title: 'Carlos - GI',
            iconCls: 'user',
            toolItems: [{
                text:'Configurações',
                iconCls:'settings',
                scope:this
            },'-',{
                text:'Logout',
                iconCls:'logout',
                scope:this
            }]
        };
    }
});

MyDesktop.Atividade = Ext.extend(Ext.app.Module, {
    init : function()
    {
        this.launcher = {
            text: 'Atividades',
            iconCls: 'bogus',
            handler: function() {
				return false;
			},
            menu: {
                items:[{
                    text: '+ Atividades',
                    iconCls:'bogus',
                    handler : this.createWindow,
                    scope: this,
                    windowId: windowIndex
                    },{
                    text: 'Minhas',
                    iconCls:'bogus',
                    handler : this.createWindow,
                    scope: this,
                    windowId: windowIndex
                    },{
                    text: 'Nova Atividade',
                    iconCls:'bogus',
                    handler : this.createWindow,
                    scope: this,
                    windowId: windowIndex
                    },{
                    text: 'Registro Operacional',
                    iconCls:'bogus',
                    handler : this.createWindow,
                    scope: this,
                    windowId: windowIndex
                    },{
                    text: 'Controles',
                    iconCls:'bogus',
                    handler : this.createWindow,
                    scope: this,
                    windowId: windowIndex
                	}
                ]
            }
        }
    }
});

MyDesktop.Cadastro= Ext.extend(Ext.app.Module, {
    init : function(){
        this.launcher = {
            text: 'Cadastros',
            iconCls: 'bogus',
            handler: function() {
				return false;
			},
            menu: {
                items:[{
                    text: 'Áreas de interesse',
                    iconCls:'bogus',
                    handler : this.createWindow,
                    scope: this,
                    windowId: windowIndex
                    },{
                    text: 'Contratos',
                    iconCls:'bogus',
                    handler : this.createWindow,
                    scope: this,
                    windowId: windowIndex
                    },{
                    text: 'Correspondências',
                    iconCls:'bogus',
                    handler : this.createWindow,
                    scope: this,
                    windowId: windowIndex
                    },{
                    text: 'Divisões',
                    iconCls:'bogus',
                    handler : this.createWindow,
                    scope: this,
                    windowId: windowIndex
                    }
                    ,{
                    text: 'Documentos',
                    iconCls:'bogus',
                    handler : this.createWindow,
                    scope: this,
                    windowId: windowIndex
                	}
                    ,{
                    text: 'Equipamentos de informática',
                    iconCls:'bogus',
                    handler : this.createWindow,
                    scope: this,
                    windowId: windowIndex
                	}
                    ,{
                    text: 'Meus Relatórios',
                    iconCls:'bogus',
                    handler : this.createWindow,
                    scope: this,
                    windowId: windowIndex
                	}
                    ,{
                    text: 'Projetos',
                    iconCls:'bogus',
                    handler : this.createWindow,
                    scope: this,
                    windowId: windowIndex
                	}
                    ,{
                    text: 'Publicações',
                    iconCls:'bogus',
                    handler : this.createWindow,
                    scope: this,
                    windowId: windowIndex
                	}
                    ,{
                    text: 'Serviços',
                    iconCls:'bogus',
                    handler : this.createWindow,
                    scope: this,
                    windowId: windowIndex
                	}
                    ,{
                    text: 'Softwares',
                    iconCls:'bogus',
                    handler : this.createWindow,
                    scope: this,
                    windowId: windowIndex
                	}
                    ,{
                    text: 'Recursos Humanos',
                    iconCls:'bogus',
                    handler : this.createWindow,
                    scope: this,
                    windowId: windowIndex
                	}
                    ,{
                    text: 'Tarefas',
                    iconCls:'bogus',
                    handler : this.createWindow,
                    scope: this,
                    windowId: windowIndex
                	}
                ]
            }
        }
    }
});

MyDesktop.Ecrm= Ext.extend(Ext.app.Module, {
    init : function(){
        this.launcher = {
            text: 'eCRM',
            iconCls: 'bogus',
            handler: function() {
				return false;
			},
            menu: {
                items:[{
                    text: 'Áreas de interesse',
                    iconCls:'bogus',
                    handler : this.createWindow,
                    scope: this,
                    windowId: windowIndex
                    },{
                    text: 'Contratos',
                    iconCls:'bogus',
                    handler : this.createWindow,
                    scope: this,
                    windowId: windowIndex
                    },{
                    text: 'Correspondências',
                    iconCls:'bogus',
                    handler : this.createWindow,
                    scope: this,
                    windowId: windowIndex
                    },{
                    text: 'Divisões',
                    iconCls:'bogus',
                    handler : this.createWindow,
                    scope: this,
                    windowId: windowIndex
                    }
                    ,{
                    text: 'Documentos',
                    iconCls:'bogus',
                    handler : this.createWindow,
                    scope: this,
                    windowId: windowIndex
                	}
                    ,{
                    text: 'Equipamentos de informática',
                    iconCls:'bogus',
                    handler : this.createWindow,
                    scope: this,
                    windowId: windowIndex
                	}
                    ,{
                    text: 'Meus Relatórios',
                    iconCls:'bogus',
                    handler : this.createWindow,
                    scope: this,
                    windowId: windowIndex
                	}
                    ,{
                    text: 'Projetos',
                    iconCls:'bogus',
                    handler : this.createWindow,
                    scope: this,
                    windowId: windowIndex
                	}
                    ,{
                    text: 'Publicações',
                    iconCls:'bogus',
                    handler : this.createWindow,
                    scope: this,
                    windowId: windowIndex
                	}
                    ,{
                    text: 'Serviços',
                    iconCls:'bogus',
                    handler : this.createWindow,
                    scope: this,
                    windowId: windowIndex
                	}
                    ,{
                    text: 'Softwares',
                    iconCls:'bogus',
                    handler : this.createWindow,
                    scope: this,
                    windowId: windowIndex
                	}
                    ,{
                    text: 'Recursos Humanos',
                    iconCls:'bogus',
                    handler : this.createWindow,
                    scope: this,
                    windowId: windowIndex
                	}
                    ,{
                    text: 'Tarefas',
                    iconCls:'bogus',
                    handler : this.createWindow,
                    scope: this,
                    windowId: windowIndex
                	}
                ]
            }
        }
    }
});

MyDesktop.Gestao = Ext.extend(Ext.app.Module, {
    init : function(){
        this.launcher = {
            text: 'Gestão',
            iconCls: 'bogus',
            handler: function() {
				return false;
			},
            menu: {
                items:[{
                    text: 'Áreas de interesse',
                    iconCls:'bogus',
                    handler : this.createWindow,
                    scope: this,
                    windowId: windowIndex
                    },{
                    text: 'Contratos',
                    iconCls:'bogus',
                    handler : this.createWindow,
                    scope: this,
                    windowId: windowIndex
                    },{
                    text: 'Correspondências',
                    iconCls:'bogus',
                    handler : this.createWindow,
                    scope: this,
                    windowId: windowIndex
                    },{
                    text: 'Divisões',
                    iconCls:'bogus',
                    handler : this.createWindow,
                    scope: this,
                    windowId: windowIndex
                    }
                    ,{
                    text: 'Documentos',
                    iconCls:'bogus',
                    handler : this.createWindow,
                    scope: this,
                    windowId: windowIndex
                	}
                    ,{
                    text: 'Equipamentos de informática',
                    iconCls:'bogus',
                    handler : this.createWindow,
                    scope: this,
                    windowId: windowIndex
                	}
                    ,{
                    text: 'Meus Relatórios',
                    iconCls:'bogus',
                    handler : this.createWindow,
                    scope: this,
                    windowId: windowIndex
                	}
                    ,{
                    text: 'Projetos',
                    iconCls:'bogus',
                    handler : this.createWindow,
                    scope: this,
                    windowId: windowIndex
                	}
                    ,{
                    text: 'Publicações',
                    iconCls:'bogus',
                    handler : this.createWindow,
                    scope: this,
                    windowId: windowIndex
                	}
                    ,{
                    text: 'Serviços',
                    iconCls:'bogus',
                    handler : this.createWindow,
                    scope: this,
                    windowId: windowIndex
                	}
                    ,{
                    text: 'Softwares',
                    iconCls:'bogus',
                    handler : this.createWindow,
                    scope: this,
                    windowId: windowIndex
                	}
                    ,{
                    text: 'Recursos Humanos',
                    iconCls:'bogus',
                    handler : this.createWindow,
                    scope: this,
                    windowId: windowIndex
                	}
                    ,{
                    text: 'Tarefas',
                    iconCls:'bogus',
                    handler : this.createWindow,
                    scope: this,
                    windowId: windowIndex
                	}
                ]
            }
        }
    }
});

MyDesktop.Intranet = Ext.extend(Ext.app.Module, {
    init : function(){
        this.launcher = {
            text: 'Intranet',
            iconCls: 'bogus',
            handler: function() {
				return false;
			},
            menu: {
                items:[{
                    text: 'Áreas de interesse',
                    iconCls:'bogus',
                    handler : this.createWindow,
                    scope: this,
                    windowId: windowIndex
                    },{
                    text: 'Contratos',
                    iconCls:'bogus',
                    handler : this.createWindow,
                    scope: this,
                    windowId: windowIndex
                    },{
                    text: 'Correspondências',
                    iconCls:'bogus',
                    handler : this.createWindow,
                    scope: this,
                    windowId: windowIndex
                    },{
                    text: 'Divisões',
                    iconCls:'bogus',
                    handler : this.createWindow,
                    scope: this,
                    windowId: windowIndex
                    }
                    ,{
                    text: 'Documentos',
                    iconCls:'bogus',
                    handler : this.createWindow,
                    scope: this,
                    windowId: windowIndex
                	}
                    ,{
                    text: 'Equipamentos de informática',
                    iconCls:'bogus',
                    handler : this.createWindow,
                    scope: this,
                    windowId: windowIndex
                	}
                    ,{
                    text: 'Meus Relatórios',
                    iconCls:'bogus',
                    handler : this.createWindow,
                    scope: this,
                    windowId: windowIndex
                	}
                    ,{
                    text: 'Projetos',
                    iconCls:'bogus',
                    handler : this.createWindow,
                    scope: this,
                    windowId: windowIndex
                	}
                    ,{
                    text: 'Publicações',
                    iconCls:'bogus',
                    handler : this.createWindow,
                    scope: this,
                    windowId: windowIndex
                	}
                    ,{
                    text: 'Serviços',
                    iconCls:'bogus',
                    handler : this.createWindow,
                    scope: this,
                    windowId: windowIndex
                	}
                    ,{
                    text: 'Softwares',
                    iconCls:'bogus',
                    handler : this.createWindow,
                    scope: this,
                    windowId: windowIndex
                	}
                    ,{
                    text: 'Recursos Humanos',
                    iconCls:'bogus',
                    handler : this.createWindow,
                    scope: this,
                    windowId: windowIndex
                	}
                    ,{
                    text: 'Tarefas',
                    iconCls:'bogus',
                    handler : this.createWindow,
                    scope: this,
                    windowId: windowIndex
                	}
                ]
            }
        }
    }
});

MyDesktop.Plano = Ext.extend(Ext.app.Module, {
    init : function(){
        this.launcher = {
            text: 'Plano',
            iconCls: 'bogus',
            handler: function() {
				return false;
			},
            menu: {
                items:[{
                    text: 'Áreas de interesse',
                    iconCls:'bogus',
                    handler : this.createWindow,
                    scope: this,
                    windowId: windowIndex
                    },{
                    text: 'Contratos',
                    iconCls:'bogus',
                    handler : this.createWindow,
                    scope: this,
                    windowId: windowIndex
                    },{
                    text: 'Correspondências',
                    iconCls:'bogus',
                    handler : this.createWindow,
                    scope: this,
                    windowId: windowIndex
                    },{
                    text: 'Divisões',
                    iconCls:'bogus',
                    handler : this.createWindow,
                    scope: this,
                    windowId: windowIndex
                    }
                    ,{
                    text: 'Documentos',
                    iconCls:'bogus',
                    handler : this.createWindow,
                    scope: this,
                    windowId: windowIndex
                	}
                    ,{
                    text: 'Equipamentos de informática',
                    iconCls:'bogus',
                    handler : this.createWindow,
                    scope: this,
                    windowId: windowIndex
                	}
                    ,{
                    text: 'Meus Relatórios',
                    iconCls:'bogus',
                    handler : this.createWindow,
                    scope: this,
                    windowId: windowIndex
                	}
                    ,{
                    text: 'Projetos',
                    iconCls:'bogus',
                    handler : this.createWindow,
                    scope: this,
                    windowId: windowIndex
                	}
                    ,{
                    text: 'Publicações',
                    iconCls:'bogus',
                    handler : this.createWindow,
                    scope: this,
                    windowId: windowIndex
                	}
                    ,{
                    text: 'Serviços',
                    iconCls:'bogus',
                    handler : this.createWindow,
                    scope: this,
                    windowId: windowIndex
                	}
                    ,{
                    text: 'Softwares',
                    iconCls:'bogus',
                    handler : this.createWindow,
                    scope: this,
                    windowId: windowIndex
                	}
                    ,{
                    text: 'Recursos Humanos',
                    iconCls:'bogus',
                    handler : this.createWindow,
                    scope: this,
                    windowId: windowIndex
                	}
                    ,{
                    text: 'Tarefas',
                    iconCls:'bogus',
                    handler : this.createWindow,
                    scope: this,
                    windowId: windowIndex
                	}
                ]
            }
        }
    }
});

MyDesktop.Servico = Ext.extend(Ext.app.Module, {
    init : function(){
        this.launcher = {
            text: 'Serviços',
            iconCls: 'bogus',
            handler: function() {
				return false;
			},
            menu: {
                items:[{
                    text: 'Áreas de interesse',
                    iconCls:'bogus',
                    handler : this.createWindow,
                    scope: this,
                    windowId: windowIndex
                    },{
                    text: 'Contratos',
                    iconCls:'bogus',
                    handler : this.createWindow,
                    scope: this,
                    windowId: windowIndex
                    },{
                    text: 'Correspondências',
                    iconCls:'bogus',
                    handler : this.createWindow,
                    scope: this,
                    windowId: windowIndex
                    },{
                    text: 'Divisões',
                    iconCls:'bogus',
                    handler : this.createWindow,
                    scope: this,
                    windowId: windowIndex
                    }
                    ,{
                    text: 'Documentos',
                    iconCls:'bogus',
                    handler : this.createWindow,
                    scope: this,
                    windowId: windowIndex
                	}
                    ,{
                    text: 'Equipamentos de informática',
                    iconCls:'bogus',
                    handler : this.createWindow,
                    scope: this,
                    windowId: windowIndex
                	}
                    ,{
                    text: 'Meus Relatórios',
                    iconCls:'bogus',
                    handler : this.createWindow,
                    scope: this,
                    windowId: windowIndex
                	}
                    ,{
                    text: 'Projetos',
                    iconCls:'bogus',
                    handler : this.createWindow,
                    scope: this,
                    windowId: windowIndex
                	}
                    ,{
                    text: 'Publicações',
                    iconCls:'bogus',
                    handler : this.createWindow,
                    scope: this,
                    windowId: windowIndex
                	}
                    ,{
                    text: 'Serviços',
                    iconCls:'bogus',
                    handler : this.createWindow,
                    scope: this,
                    windowId: windowIndex
                	}
                    ,{
                    text: 'Softwares',
                    iconCls:'bogus',
                    handler : this.createWindow,
                    scope: this,
                    windowId: windowIndex
                	}
                    ,{
                    text: 'Recursos Humanos',
                    iconCls:'bogus',
                    handler : this.createWindow,
                    scope: this,
                    windowId: windowIndex
                	}
                    ,{
                    text: 'Tarefas',
                    iconCls:'bogus',
                    handler : this.createWindow,
                    scope: this,
                    windowId: windowIndex
                	}
                ]
            }
        }
    }
});

/*
 * Example windows
 */
MyDesktop.GridWindow = Ext.extend(Ext.app.Module, {
    id:'grid-win',
    init : function(){
        this.launcher = {
            text: 'Grid Window -',
            iconCls:'icon-grid',
            handler : this.createWindow,
            scope: this
        }
    },

    createWindow : function(){
        var desktop = this.app.getDesktop();
        var win = desktop.getWindow('grid-win');
        if(!win){
            win = desktop.createWindow({
                id: 'grid-win',
                title:'Grid Window',
                width:740,
                height:480,
                iconCls: 'icon-grid',
                shim:false,
                animCollapse:false,
                constrainHeader:true,

                layout: 'fit',
                items:
                    new Ext.grid.GridPanel({
                        border:false,
                        ds: new Ext.data.Store({
                            reader: new Ext.data.ArrayReader({}, [
                               {name: 'company'},
                               {name: 'price', type: 'float'},
                               {name: 'change', type: 'float'},
                               {name: 'pctChange', type: 'float'}
                            ]),
                            data: Ext.grid.dummyData
                        }),
                        cm: new Ext.grid.ColumnModel([
                            new Ext.grid.RowNumberer(),
                            {header: "Company", width: 120, sortable: true, dataIndex: 'company'},
                            {header: "Price", width: 70, sortable: true, renderer: Ext.util.Format.usMoney, dataIndex: 'price'},
                            {header: "Change", width: 70, sortable: true, dataIndex: 'change'},
                            {header: "% Change", width: 70, sortable: true, dataIndex: 'pctChange'}
                        ]),

                        viewConfig: {
                            forceFit:true
                        },
                        //autoExpandColumn:'company',

                        tbar:[{
                            text:'Add Something',
                            tooltip:'Add a new row',
                            iconCls:'add'
                        }, '-', {
                            text:'Options',
                            tooltip:'Blah blah blah blaht',
                            iconCls:'option'
                        },'-',{
                            text:'Remove Something',
                            tooltip:'Remove the selected item',
                            iconCls:'remove'
                        }]
                    })
            });
        }
        win.show();
    }
});



MyDesktop.TabWindow = Ext.extend(Ext.app.Module, {
    id:'tab-win',
    init : function(){
        this.launcher = {
            text: 'Tab Window',
            iconCls:'tabs',
            handler : this.createWindow,
            scope: this
        }
    },

    createWindow : function(){
        var desktop = this.app.getDesktop();
        var win = desktop.getWindow('tab-win');
        if(!win){
            win = desktop.createWindow({
                id: 'tab-win',
                title:'Tab Window',
                width:740,
                height:480,
                iconCls: 'tabs',
                shim:false,
                animCollapse:false,
                border:false,
                constrainHeader:true,

                layout: 'fit',
                items:
                    new Ext.TabPanel({
                        activeTab:0,

                        items: [{
                            title: 'Tab Text 1',
                            header:false,
                            html : '<p>Something useful would be in here.</p>',
                            border:false
                        },{
                            title: 'Tab Text 2',
                            header:false,
                            html : '<p>Something useful would be in here.</p>',
                            border:false
                        },{
                            title: 'Tab Text 3',
                            header:false,
                            html : '<p>Something useful would be in here.</p>',
                            border:false
                        },{
                            title: 'Tab Text 4',
                            header:false,
                            html : '<p>Something useful would be in here.</p>',
                            border:false
                        }]
                    })
            });
        }
        win.show();
    }
});



MyDesktop.AccordionWindow = Ext.extend(Ext.app.Module, {
    id:'acc-win',
    init : function(){
        this.launcher = {
            text: 'Accordion Window',
            iconCls:'accordion',
            handler : this.createWindow,
            scope: this
        }
    },

    createWindow : function(){
        var desktop = this.app.getDesktop();
        var win = desktop.getWindow('acc-win');
        if(!win){
            win = desktop.createWindow({
                id: 'acc-win',
                title: 'Accordion Window',
                width:250,
                height:400,
                iconCls: 'accordion',
                shim:false,
                animCollapse:false,
                constrainHeader:true,

                tbar:[{
                    tooltip:{title:'Rich Tooltips', text:'Let your users know what they can do!'},
                    iconCls:'connect'
                },'-',{
                    tooltip:'Add a new user',
                    iconCls:'user-add'
                },' ',{
                    tooltip:'Remove the selected user',
                    iconCls:'user-delete'
                }],

                layout:'accordion',
                border:false,
                layoutConfig: {
                    animate:false
                },

                items: [
                    new Ext.tree.TreePanel({
                        id:'im-tree',
                        title: 'Online Users',
                        loader: new Ext.tree.TreeLoader(),
                        rootVisible:false,
                        lines:false,
                        autoScroll:true,
                        tools:[{
                            id:'refresh',
                            on:{
                                click: function(){
                                    var tree = Ext.getCmp('im-tree');
                                    tree.body.mask('Loading', 'x-mask-loading');
                                    tree.root.reload();
                                    tree.root.collapse(true, false);
                                    setTimeout(function(){ // mimic a server call
                                        tree.body.unmask();
                                        tree.root.expand(true, true);
                                    }, 1000);
                                }
                            }
                        }],
                        root: new Ext.tree.AsyncTreeNode({
                            text:'Online',
                            children:[{
                                text:'Friends',
                                expanded:true,
                                children:[{
                                    text:'Jack',
                                    iconCls:'user',
                                    leaf:true
                                },{
                                    text:'Brian',
                                    iconCls:'user',
                                    leaf:true
                                },{
                                    text:'Jon',
                                    iconCls:'user',
                                    leaf:true
                                },{
                                    text:'Tim',
                                    iconCls:'user',
                                    leaf:true
                                },{
                                    text:'Nige',
                                    iconCls:'user',
                                    leaf:true
                                },{
                                    text:'Fred',
                                    iconCls:'user',
                                    leaf:true
                                },{
                                    text:'Bob',
                                    iconCls:'user',
                                    leaf:true
                                }]
                            },{
                                text:'Family',
                                expanded:true,
                                children:[{
                                    text:'Kelly',
                                    iconCls:'user-girl',
                                    leaf:true
                                },{
                                    text:'Sara',
                                    iconCls:'user-girl',
                                    leaf:true
                                },{
                                    text:'Zack',
                                    iconCls:'user-kid',
                                    leaf:true
                                },{
                                    text:'John',
                                    iconCls:'user-kid',
                                    leaf:true
                                }]
                            }]
                        })
                    }), {
                        title: 'Settings',
                        html:'<p>Something useful would be in here.</p>',
                        autoScroll:true
                    },{
                        title: 'Even More Stuff',
                        html : '<p>Something useful would be in here.</p>'
                    },{
                        title: 'My Stuff',
                        html : '<p>Something useful would be in here.</p>'
                    }
                ]
            });
        }
        win.show();
    }
});

// for example purposes
var windowIndex = 0;

MyDesktop.BogusModule = Ext.extend(Ext.app.Module, {
    init : function(){
        this.launcher = {
            text: 'Window '+(++windowIndex),
            iconCls:'bogus',
            handler : this.createWindow,
            scope: this,
            windowId:windowIndex
        }
    },

    createWindow : function(src){
        var desktop = this.app.getDesktop();
        var win = desktop.getWindow('bogus'+src.windowId);
        if(!win){
            win = desktop.createWindow({
                id: 'bogus'+src.windowId,
                title:src.text,
                width:640,
                height:480,
                html : '<p>Something useful would be in here.</p>',
                iconCls: 'bogus',
                shim:false,
                animCollapse:false,
                constrainHeader:true
            });
        }
        win.show();
    }
});


MyDesktop.BogusMenuModule = Ext.extend(MyDesktop.BogusModule, {
    init : function(){
        this.launcher = {
            text: 'Bogus Submenu',
            iconCls: 'bogus',
            handler: function() {
				return false;
			},
            menu: {
                items:[{
                    text: 'Bogus Window '+(++windowIndex),
                    iconCls:'bogus',
                    handler : this.createWindow,
                    scope: this,
                    windowId: windowIndex
                    },{
                    text: 'Bogus Window '+(++windowIndex),
                    iconCls:'bogus',
                    handler : this.createWindow,
                    scope: this,
                    windowId: windowIndex
                    },{
                    text: 'Bogus Window '+(++windowIndex),
                    iconCls:'bogus',
                    handler : this.createWindow,
                    scope: this,
                    windowId: windowIndex
                    },{
                    text: 'Bogus Window '+(++windowIndex),
                    iconCls:'bogus',
                    handler : this.createWindow,
                    scope: this,
                    windowId: windowIndex
                    },{
                    text: 'Bogus Window '+(++windowIndex),
                    iconCls:'bogus',
                    handler : this.createWindow,
                    scope: this,
                    windowId: windowIndex
                	},{
                	text: 'Atividades',
                	iconCls:'bogus',
                	handler:function(){
                		return false;
                	},
                	menu:{
                		items:[{
                			text: 'teste teste',
                			iconCls:'bogus',
                			handler: this.createWindow,
                			scope:this,
                			windowId:windowIndex
                		}]}
            	}]
            }
        }
    }
});

// Array data for the grid
Ext.grid.dummyData = [
    ['3m Co',71.72,0.02,0.03,'9/1 12:00am'],
    ['Alcoa Inc',29.01,0.42,1.47,'9/1 12:00am'],
    ['American Express Company',52.55,0.01,0.02,'9/1 12:00am'],
    ['American International Group, Inc.',64.13,0.31,0.49,'9/1 12:00am'],
    ['AT&T Inc.',31.61,-0.48,-1.54,'9/1 12:00am'],
    ['Caterpillar Inc.',67.27,0.92,1.39,'9/1 12:00am'],
    ['Citigroup, Inc.',49.37,0.02,0.04,'9/1 12:00am'],
    ['Exxon Mobil Corp',68.1,-0.43,-0.64,'9/1 12:00am'],
    ['General Electric Company',34.14,-0.08,-0.23,'9/1 12:00am'],
    ['General Motors Corporation',30.27,1.09,3.74,'9/1 12:00am'],
    ['Hewlett-Packard Co.',36.53,-0.03,-0.08,'9/1 12:00am'],
    ['Honeywell Intl Inc',38.77,0.05,0.13,'9/1 12:00am'],
    ['Intel Corporation',19.88,0.31,1.58,'9/1 12:00am'],
    ['Johnson & Johnson',64.72,0.06,0.09,'9/1 12:00am'],
    ['Merck & Co., Inc.',40.96,0.41,1.01,'9/1 12:00am'],
    ['Microsoft Corporation',25.84,0.14,0.54,'9/1 12:00am'],
    ['The Coca-Cola Company',45.07,0.26,0.58,'9/1 12:00am'],
    ['The Procter & Gamble Company',61.91,0.01,0.02,'9/1 12:00am'],
    ['Wal-Mart Stores, Inc.',45.45,0.73,1.63,'9/1 12:00am'],
    ['Walt Disney Company (The) (Holding Company)',29.89,0.24,0.81,'9/1 12:00am']
];