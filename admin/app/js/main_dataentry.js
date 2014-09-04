Application.mainApplication = function(){

  var viewport;
  var menuStrumenti;
  var permessiUtente;
  
  function loadUser(){
    var resp;
    Ext.Ajax.request({
      url:'users/getinfo',
      success:function(response,options){
        resp=Ext.decode(response.responseText);
        Identity.info.setInfoUtente(resp.data);
        var arr=Identity.info.getInfoUtente();
        var x=Ext.ComponentMgr.get("toolbarApplication");
        x.addText({text:'<b>'+arr.nome+' '+arr.cognome+'</b> - ['+arr.role+' - '+arr.organization+']'});
        x.doLayout(true);
      },
      scope:this
    });
  }
  
  function createUI(){
    var menuStrumentiItems=new Array();
    var buttonsToolBar=new Array();
    var strumenti;
    loadUser();
    
    menuStrumentiItems.push({
      text:'Archivio contatti',
      icon:'css/icons/vcard.png',
      iconCls:'x-btn-text-icon',
      width:250,
      handler:function(){
        var contattiWin=new Application.contatti.WinContatti();
        contattiWin.show();
      },
      scope:this
     },{
      text:'Cambia password',
      icon:'css/icons/key.png',
      iconCls:'x-btn-text-icon',
      width:250,
      handler:function(){
        var editPasswordWin=new Application.users.WinEditPassword();
        editPasswordWin.show(Identity.info.getInfoUtente().ID);
      },
      scope:this
    });
    
  	menuStrumenti = new Ext.menu.Menu({
	  id: 'mainMenu',
      items:menuStrumentiItems
	});
    
    strumenti=new Ext.Button({
      text: 'Strumenti',
      icon:'css/icons/cog.png',
      iconCls:'x-btn-text-icon',
      width: 120,
      menu: menuStrumenti
    });
     buttonsToolBar.push(
      strumenti,
      '-',
      new Ext.Button({
	      text: 'Logout',
        width:80,
	      icon:'css/icons/stop.png',
	      iconCls:'x-btn-text-icon',
	      handler:function(){
	      Ext.Msg.show({
	        title:'Uscire ?',
	        msg: 'Uscire dal programma ?',
	        buttons: Ext.Msg.YESNO,
	        fn:function(btn){
	          if (btn=='yes'){
	            Ext.Ajax.request({
	              url:'index/logout',
	              success:function(){
	                location.href = "";
	              }
	            });
	          }
	        },
	        icon: Ext.MessageBox.QUESTION
	      });
	      },
	      scope:this
	    })
    ,'-');
    
	  toolb= new Ext.Toolbar({
	    id:'toolbarApplication',
      cls:'omicrontoolbar-border-top',
	    buttons:buttonsToolBar
	  });
	  
	  viewport = new Ext.Viewport({
	    layout: 'border',
	    items: [{
	      region: 'north',
	      xtype: 'panel',
	      border: true,
	      height: 86,
        bodyStyle:'background:#CDCDCD',
	      html:'<div class="header_container"><div class="header_logo">'+
             '<div class="header_omicron"><span id="rtime">...</span><br/><br/>' +
             'powered by <a href="http://www.omicronmedia.com" target="_blank">Omicronmedia</a>' +
             '</div></div></div>'
        }, {
	      region: 'center',
	      xtype: 'panel',
	      tbar:toolb,
	      bodyStyle: 'background:#fff;'
	    },{
        region:'south',
        xtype:'panel',
        height:20,
        border:false,
        html:'<div id="infoCassa" class="cassa_information"></div>'
      }]
	  });
	  
	  Ext.get("loading").fadeOut({
	    duration: 0.5,
	    remove: true
	  });
	  Ext.get("loading-mask").remove({
	    duration: 0,
	    remove: true
	  });
  }
  
  return {  
    
    init: function(){
    
      Ext.QuickTips.init();
      Ext.form.Field.prototype.msgTarget = 'side';
      
      Ext.apply(Ext.form.DateField.prototype, {
        format: "d/m/Y",
        altFormats: "Y-m-d H:i:s|j-n-y|j-n-Y|j/n/Y|d/m/Y|d-m-y|d-m-Y|j/n|j-n|d/m|d-m|dm|dmy|dmY|d|j|Y-m-d"
      });
     
      Ext.Ajax.request({
        url:"permissions/jspermissions",
        success:function(response,options){
          Pm.p.setPermissions(Ext.decode(response.responseText));
          createUI();
        },
        scope:this
      });
    }
  }
}
();
Ext.onReady(Application.mainApplication.init, Application.mainApplication, true);