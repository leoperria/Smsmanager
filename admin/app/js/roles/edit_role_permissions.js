Application.roles.WinEditPermessi = function(id){
  this.id=id;
  this.pFieldEditor=null;
  this.vField=null;
  this.init({
    winConfig:{title:'Gestione permessi',width:540,maximizable:false},
    deleteUrl:'permissions/delete'
  });
};

Ext.extend(Application.roles.WinEditPermessi, Application.apiGrid.WinList, {

  buildGridPanel: function(){
      
    this.pFieldEditor = new Ext.form.ComboBox({
       triggerAction: 'all',
       lazyRender:true,
       lazyInit:false,
       editable:false,
       mode:'local',
       forceSelection:true,
       store:[[true,'Si'],[false,'No']], 
       listClass:'x-combo-list-small',
       listeners: {
         'select':{ fn: function(){
            this.edit()
           }, scope:this 
         }
       }
    });
    
    this.vField=new Ext.form.TextField({
      allowBlank:true,
      listeners: {
        'change':{fn: function(filed, evnt){{this.edit()}
            }, scope:this 
        }  
      }
    });
    
    return this.gridPanel=new Ext.grid.EditorGridPanel({
      region:'center',
      selModel:new Ext.grid.RowSelectionModel({singleSelect:true}),
      clicksToEdit:1,
      autoScroll: true,
      store:new Ext.data.GroupingStore({
	      proxy: new Ext.data.HttpProxy({url: 'roles/getpermissions'}),
	       baseParams:{
	        id:this.id
	      },
	      reader:new Ext.data.JsonReader({
	      root:'results',
	      totalProperty:'totalCount',
	      id:'p_ID'
	    },[
	        {name:"a_ID",type:"int"},
	        {name:"a_name"},
	        {name:"a_UIname"},
	        {name:'p_ID',type:'int'},
	        {name:'p_ID_area',type:'int'},
	        {name:'p_sortid',type:'int'},
	        {name:'p_name'},
	        {name:'p_UIname'},
	        {name:'p_type'},
	        {name:'p_default_value'},
	        {name:'r_ID',type:'int'},
	        {name:'r_ID_role',type:'int'},
	        {name:'r_ID_permission',type:'int'},
	        {name:'r_value'}
	      ]),
	      remoteSort:true,
	      remoteGroup:true,
	      groupField:'a_UIname'
	    }),
      columns:[
        { header:'Area', width:80, dataIndex:'a_UIname'},
        { header:'UIname', width:120, dataIndex:'p_UIname'},
        { header:'Type', width:100, dataIndex:'p_type',renderer:function(value){return value==0 ? 'Permesso':'Parametro';}},
        { header:'Attivo', width:80, dataIndex:'r_ID',align:'center',renderer:function(value){return value!=''? "Si":"No";},editor:this.pFieldEditor},
        { header:'Valore', width:120, dataIndex:'r_value',editor:this.vField}
      ],
      tbar:[{
        text:'Help',
        icon:'css/icons/help.png',
        iconCls:'x-btn-text-icon',
        tooltip:{title:'Help',text:'Aiuto nella configurazione dei permessi'},
        handler: function(){
          var winHelper=new Ext.ux.window.MessageWindow({
            title: 'Configurazione dei permessi',
            autoDestroy: true,
            height:500,
            autoHide: true,
            bodyStyle: 'text-align:left',
            closeOnBodyClick:false,
            help: false,
            html: '<div class="help_container">' +
                  '<p>Le voci contrassegnate con l\'asterisco (*) sono i permessi consigliati solo al gruppo dell\'amministratore</p>'+
                  '<p><b>Campagne Pubblicitarie</b></p>'+
                  '<ul class="lista_help">' +
                  '<li><b>Accesso (solo combobox)</b>: se settato su SI l\'utente di questo gruppo potr&agrave; visualizzare la lista delle campagne pubblicitarie per rilevare le statistiche a fine operazione di acquisto.</li>' +
                  '<li><b>Visualizza lista tabella*</b>: se settato su SI l\'utente di questo gruppo avr&agrave; accesso alla finestra con la lista delle campagne pubblicitarie</li>' +
                  '<li><b>Modifica*</b>: se settato su SI l\'utente di questo gruppo potr&agrave; modificare, creare ed eliminare le voci delle campagne pubblicitarie' +
                  '</ul>'+
                  '<p><b>Cassa Sede</b></p>'+
                  '<ul class="lista_help">' +
                  '<li><b>Accesso</b>: se settato su SI l\'utente di questo gruppo potr&agrave; aggiungere movimenti, inserire dei versamenti per la sua sede, registrare i movimenti derivanti dalle operazioni di acquisto.</li>' +
                  '</ul>' +
                  '<p><b>Clienti</b></p>'+
                  '<ul class="lista_help">' +
                  '<li><b>Accesso</b>: se settato su SI l\'utente di questo gruppo potr&agrave; accedere alle funzioni riguardanti i clienti (esempio registrazione nuovo cliente).</li>' +
                  '<li><b>Visualizza</b>: se settato su SI l\'utente di questo gruppo potr&agrave; visualizzare i dati dei clienti (esempio ricerca dei clienti all\'atto dell\'acquisto dell\'oro).</li>' +
                  '<li><b>Modifica</b>: se settato su SI l\'utente di questo gruppo potr&agrave; aggiungere e modificare i dati dei clienti (esempio, aggiungere un documento durante le operazioni di acquisto).</li>' +
                  '</ul>' +
                  '<p><b>Descrizione oggetti</b></p>'+
                  '<ul class="lista_help">' +
                  '<li><b>Accesso completo*</b>: se settato su SI l\'utente di questo gruppo potr&agrave; accedere alla schermata che permette di inserire, modificare, eliminare le descrizioni degli oggetti nell\'archivio oggetti.</li>' +
                  '</ul>' +
                  '<p><b>Gestione oggetti</b></p>'+
                  '<ul class="lista_help">' +
                  '<li><b>Accesso completo*</b>: se settato su SI l\'utente di questo gruppo potr&agrave; accedere alle informazioni e le funzioni riguardanti tutti i magazzini di tutte le sedi e i processi di evasione dei magazzini (<b>sconsigliato</b>).</li>' +
                  '</ul>' +
                  '<p><b>Magazzino sedi</b></p>'+
                  '<ul class="lista_help">' +
                  '<li><b>Accesso completo*</b>: se settato su SI l\'utente di questo gruppo potr&agrave; accedere alle informazioni e le funzioni riguardanti tutti i magazzini di tutte le sedi e i processi di evasione dei magazzini (<b>sconsigliato</b>).</li>' +
                  '</ul>' +
                  '<p><b>Oggetti</b></p>'+
                  '<ul class="lista_help">' +
                  '<li><b>Accesso</b>: se settato su SI l\'utente di questo gruppo potr&agrave; accedere alle funzioni di aggiunta di oggetti in magazzino (esempio, aggiungere oggetti durante l\'acquisto dell\'oro).</li>' +
                  '<li><b>Visualizza tutto*</b>: se settato su SI l\'utente di questo gruppo potr&agrave; visualizzare tutti gli oggetti presenti nelle sedi (<b>sconsigliato</b>).</li>' +
                  '</ul>' +
                  '<p><b>Operazioni</b></p>'+
                  '<ul class="lista_help">' +
                  '<li><b>Accesso normale</b>: se settato su SI l\'utente di questo gruppo potr&agrave; accedere alle funzioni riguardanti le operazioni di cassa (esempio, acquisto oro).</li>' +
                  '<li><b>Visualizza operazioni sede</b>: se settato su SI l\'utente di questo gruppo potr&agrave; visualizzare una lista di operazioni della sua sede.</li>' +
                  '<li><b>Modifica operazioni sede</b>: se settato su SI l\'utente di questo gruppo potr&agrave; aggiungere e modificare le operazioni di acquisto o i versamenti in cassa della sua sede.</li>' +
                  '<li><b>Stampa operazione acquisto</b>: se settato su SI l\'utente di questo gruppo potr&agrave; effettuare la stampa delle operazioni di acquisto e le relative ricevute per il cliente.</li>' +
                  '<li><b>Versamento cassa</b>: se settato su SI l\'utente di questo gruppo potr&agrave; aggiungere e modificare versamenti in cassa.</li>' +
                  '<li><b>Giorni storico visibile</b>: se settato su SI l\'utente di questo gruppo potr&agrave; visualizzare uno storico di giorni X relativo alle operazioni effettuate.</li>' +
                  '<li><b>Visualizza permessi editabilit&agrave;*</b>: se settato su SI l\'utente di questo gruppo potr&agrave; visualizzare i permessi di editabilit&agrave; delle operazioni gi&agrave; effettuate.</li>' +
                  '<li><b>Modifica permessi editabilit&agrave;*</b>: se settato su SI l\'utente di questo gruppo potr&agrave; modificare i permessi di editabilit&agrave; delle operazioni gi&agrave; effettuate.</li>' +
                  '</ul>' +
                  '<p><b>Permessi</b></p>'+
                  '<ul class="lista_help">' +
                  '<li><b>Accesso impostazioni*</b>: se settato su SI l\'utente di questo gruppo potr&agrave; accedere alle funzioni riguardanti le impostazioni dei permessi per i gruppi di utenti (<b>sconsigliato</b>).</li>' +
                  '<li><b>Visualizza impostazioni*</b>: se settato su SI l\'utente di questo gruppo potr&agrave; visualizzare la lista dei permessi di tutti i gruppi di utenti (<b>sconsigliato</b>).</li>' +
                  '<li><b>Modifica impostazioni*</b>: se settato su SI l\'utente di questo gruppo potr&agrave; aggiungere, modificare e eliminare i permessi di tutti i gruppi di utenti(<b>sconsigliato</b>).</li>' +
                  '</ul>' +
                  '<p><b>Primanota</b></p>'+
                  '<ul class="lista_help">' +
                  '<li><b>Accesso propria sede</b>: se settato su SI l\'utente di questo gruppo potr&agrave; accedere alle funzioni riguardanti le operazioni di primanota (esempio, aggiungere una voce o un movimento relativo alla sua sede).</li>' +
                  '<li><b>Visualizza propria sede</b>: se settato su SI l\'utente di questo gruppo potr&agrave; visualizzare una lista di movimenti della prima nota della sua sede.</li>' +
                  '<li><b>Modifica propria sede</b>: se settato su SI l\'utente di questo gruppo potr&agrave; aggiungere, modificare ed eliminare le operazioni di acquisto o i versamenti in cassa della sua sede (ma non quelle degli acquisti oro).</li>' +
                  '<li><b>Numero di giorni visibili</b>: se settato su SI l\'utente di questo gruppo potr&agrave; visualizzare uno storico di giorni X relativo alle voci in primanota.</li>' +
                  '</ul>' +
                  '<p><b>Ruoli</b></p>'+
                  '<ul class="lista_help">' +
                  '<li><b>Accesso impostazioni*</b>: se settato su SI l\'utente di questo gruppo potr&agrave; accedere alle funzioni di gestione dei gruppi di utenti (<b>sconsigliato</b>).</li>' +
                  '<li><b>Visualizza impostazioni*</b>: se settato su SI l\'utente di questo gruppo potr&agrave; visualizzare la lista dei gruppi di utenti dell\'impresa (<b>sconsigliato</b>).</li>' +
                  '<li><b>Modifica impostazioni*</b>: se settato su SI l\'utente di questo gruppo potr&agrave; aggiungere, modificare, ed eliminare gruppi di utenti dell\'impresa (<b>sconsigliato</b>)</li>' +
                  '</ul>' +
                  '<p><b>Sedi</b></p>'+
                  '<ul class="lista_help">' +
                  '<li><b>Accesso impostazioni*</b>: se settato su SI l\'utente di questo gruppo potr&agrave; accedere alle funzioni riguardanti le sedi dell\'impresa (<b>sconsigliato</b>).</li>' +
                  '<li><b>Visualizza impostazioni*</b>: se settato su SI l\'utente di questo gruppo potr&agrave; visualizzare la lista di tutte le sedi e i relativi stati di cassa(<b>sconsigliato</b>).</li>' +
                  '<li><b>Modifica impostazioni*</b>: se settato su SI l\'utente di questo gruppo potr&agrave; modificare i dati e le quotazioni di tutte le sedi (<b>sconsigliato</b>).</li>' +
                  '</ul>' +
                  '<p><b>Utenti</b></p>'+
                  '<ul class="lista_help">' +
                  '<li><b>Accesso al programma</b>: se settato su SI l\'utente di questo gruppo potr&agrave; accedere al sistema.</li>' +
                  '<li><b>Visualizza stato di login</b>: se settato su NO l\'utente di questo gruppo non potr&agrave; utilizzare il sistema anche se riuscir&agrave; ad entrare.</li>' +
                  '<li><b>Modifica utenti*</b>: se settato su SI l\'utente di questo gruppo potr&agrave; aggiungere, modificare ed eliminare nuovi utenti e operatori(<b>sconsigliato</b>).</li>' +
                  '<li><b>Modifica propria password</b>: se settato su SI l\'utente di questo gruppo potr&agrave; cambiare la sua password.</li>' +
                  '<li><b>Durata password</b>: se settato su SI e impostato un numero di giorni la password dell\'utente di questo gruppo non sar&agrave; pi&ugrave; valida dopo il periodo indicato.</li>' +
                  '</ul></div>',
            iconCls: 'goldmanager-help-icon',
            pinState: 'pin',
            textUnpin:'Click per chiudere',
            showFx: {
              duration: 0.25,
              mode: 'standard',
              useProxy: false
            },
            width: 400
          });
          winHelper.show(Ext.getDoc());
        },
        scope:this
      },'-'],
      view: new Ext.grid.GroupingView({
        groupTextTpl: '{text}',
        startCollapsed:true
      })
    });
  },
  
  edit:function(){
    var allow;
    if(this.pFieldEditor.getValue()){
      allow=this.pFieldEditor.getValue();
    }else if(this.vField.getValue()==''){
      allow=true;
    }
    Ext.Ajax.request({
      url:'roles/modpermesso',
      params:{
        idPermission:this.gridPanel.getSelectionModel().getSelected().id,
        allow:allow,
        value:this.vField.getValue(),
        type:this.gridPanel.getSelectionModel().getSelected().data.p_type,
        idRole:this.id
      },
      success:function(response,options){
        this.refreshPanel();
      },
      scope:this
    });
  },
  
  refreshPanel:function(){
    this.gridPanel.getStore().load();
  }
});