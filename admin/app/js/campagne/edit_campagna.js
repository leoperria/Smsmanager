Application.campagne.WinEdit = function(orgid){
  this.orgid=orgid;
  this.init();
};
Ext.extend(Application.campagne.WinEdit, Ext.util.Observable, {

  win:null,
  id:null,
  formPanel:null,
  dataCreazioneField:null,
  nameField:null,
  testoField:null,
  comboSesso:null,
  comboProvince:null,
  capField:null,
  comboComparazione:null,
  storeGruppi:null,
  comboGruppi:null,
	  
  init: function(){
    this.buildForm();
    this.addEvents({"updated" : true});    
    
    this.win = new Ext.Window({
	  title:'Modifica/aggiungi campagna',
      iconCls: 'icon-shield',
	  width: 400,
	  height: 450,
	  plain:true,
	  modal:true,
	  border:false,
	  constrainHeader:true,
	  shim:false,
	  animCollapse:false,
	  buttonAlign:'right',
	  maximizable:false,
	  items:[this.formPanel],
	  buttons: [{
	    text: 'Salva',
	    handler:this.saveForm,
	    scope:this
	  },{
	    text: 'Chiudi',
	    handler:this.hide,
	    scope:this
	  }]
	});
    
  },

  show: function(id){
    this.id=id;
    if(this.id!='new'){
      this.loadForm(this.id);
    }else{
      var d=new Date();
      this.dataCreazioneField.setValue(d.format('d/m/Y'));    
    }
    this.orgidField.setValue(this.orgid);
    this.win.show();
  },
  
  loadForm:function(id){
    this.formPanel.getForm().load({
      url:'campagne/get',
      params:{
        id:id
      },
      success:function(){},
      failure: function(form,action){
        if (action.result.errorMessages){
          var errMsg=action.result.errorMessages.join("<br/>");
          Ext.MessageBox.show({
            title: 'Problema...',
            msg: errMsg,
            buttons: Ext.MessageBox.OK,
            icon: Ext.MessageBox.WARNING,
            fn:function(btn){
              if (action.result.closeAfterErrors){
                this.hide();
              }
            },
            scope:this
          });
        }
      },
      scope:this,
      waitMsg:'Caricamento...'
    });
  },
  
  saveForm:function(){
    if(this.formPanel.getForm().isValid()){
        this.formPanel.getForm().submit({
          url:'campagne/save',
          params:{
            id:this.id
          },
          waitMsg: 'Salvataggio in corso...',
          success: function(form,action){
            if(action.result.message){
              Ext.Msg.alert('Messaggio',action.result.message);
            }
            this.fireEvent('updated');
            this.hide();
          },
          failure: function(form,action){
          if (action.result.errorMessages){
            var errMsg=action.result.errorMessages.join("<br/>");
            Ext.MessageBox.show({
	            title: 'Problema...',
	            msg: errMsg,
	            buttons: Ext.MessageBox.OK,
	            icon: Ext.MessageBox.WARNING,
	            fn:function(btn){
	              if (action.result.closeAfterErrors){
		          	this.hide();
		          }
	            },
	            scope:this
	        });
          }
        },
        scope:this
      });
    }
  },

  hide: function(){
    Ext.QuickTips.init();
    this.win.close();
  },
  
  buildForm:function(){
	
	this.orgidField=new Ext.form.Hidden({
	  name:'orgid'
	});
	
    this.dataCreazioneField=new Ext.form.DateField({
      fieldLabel: 'Data creazione',
      name:'data_campagna'
    });
	
	this.nameField=new Ext.form.TextField({
        fieldLabel: 'Nome campagna',
        name:'nome_campagna'
    });
	
	this.testoField=new Ext.form.TextArea({
		fieldLabel:'Testo SMS',
		name:'testo'
	});
	
  	var store=new Ext.data.JsonStore({
        url: 'province/listcombo',
        root: 'results',
        fields: [{name:'ID_provincia'},{name:'provincia'}]
    });
    store.load();
    
    this.comboProvince=new Ext.form.ComboBox({
        name:'ID_provincia',
        editable:true,
        fieldLabel:'Provincia',
        store:store,
        triggerAction:'all',
        forceSelection:true,
        displayField:'provincia',
        valueField:'ID_provincia',
        hiddenName:'ID_provincia'
      });
    
    this.storeGruppi=new Ext.data.JsonStore({
      url: 'gruppi/listcombo',
      baseParams:{orgid:this.orgid},
      root: 'results',
      fields: [{name:'ID_gruppo'},{name:'descrizione'}]
    });
    this.storeGruppi.load();
    this.comboGruppi=new Ext.form.ComboBox({
      name:'ID_gruppo',
      editable:true,
      fieldLabel:'Gruppo',
      store:this.storeGruppi,
      triggerAction:'all',
      forceSelection:true,
      displayField:'descrizione',
      valueField:'ID_gruppo',
      hiddenName:'ID_gruppo'
    });
    
    this.comboSesso=new Ext.form.ComboBox({
        name:'sesso',
        editable:false,
        fieldLabel:'Sesso',
        store:new Ext.data.SimpleStore({
            fields: ['sesso', 'descrizione'],
            data :[
              ['','-----------------'],
              ['M','Maschio'],
              ['F','Femmina']
            ]
          }),
        mode:'local',
        triggerAction:'all',
        forceSelection:true,
        displayField:'descrizione',
        valueField:'sesso',
        hiddenName:'sesso'
    });
    
    this.comboComparazione=new Ext.form.ComboBox({
    	name:'comparazione',
    	editable:false,
        fieldLabel:'Con età',
        store:new Ext.data.SimpleStore({
          fields:['comparazione','descrizione'],
          data:[
            ['','----------------'],
            ['<=','minore o uguale a ( <= )'],
            ['>=','maggiore o uguale a ( >= )'],
            ['=','uguale a ( = )']
          ]
        }),
        mode:'local',
        triggerAction:'all',
        forceSelection:true,
        displayField:'descrizione',
        valueField:'comparazione',
        hiddenName:'comparazione'
    });
    
    this.dataNascitaField=new Ext.form.NumberField({
        fieldLabel: 'Anni',
        name:'eta'
    });
    
    this.capField=new Ext.form.TextField({
        fieldLabel:'C.A.P.',
        name:'cap'
     });
    
    this.formPanel= new Ext.FormPanel({
	  baseCls: 'x-plain', 
	  bodyStyle: 'padding: 10px 10px 0 10px;',
	  plugins: [new Ext.ux.OOSubmit()],
	  items:[
	    new Ext.form.FieldSet({
	      title:'Dati Generali',
	      autoHeight:true,
	      border:false,
	      defaults: {anchor:'90%',msgTarget:'side'},
	      defaultType: 'textfield',
	      labelWidth: 100,
	      items:[
	        this.orgidField,
	        this.dataCreazioneField,
            this.nameField,
            this.testoField
	      ]
		}),
		new Ext.form.FieldSet({
		  title:'Filtro target',
		  autoHeight:true,
		  border:false,
		  defaultType: 'textfield',
		  defaults: {anchor:'90%',msgTarget:'side'},
		  labelWidth: 100,
		  items:[
		    this.comboGruppi,
			this.comboSesso,
			this.comboProvince,
			this.capField,
			this.comboComparazione,
			this.dataNascitaField
		  ]
		})
	  ]
    });
  }
});