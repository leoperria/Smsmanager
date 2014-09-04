Ext.namespace("Application.ricariche");
Application.ricariche.WinGatewayRicariche = function(){
  this.init();
};

Ext.extend(Application.ricariche.WinGatewayRicariche, Ext.util.Observable, {
	win:null,
	  
	  init: function(){
	    this.buildFramePanel();
	    this.win = new Ext.Window({
	      title: 'Acquista pacchetti SMS',
	      iconCls: 'icon-shield',
	      width: 700,
	      height: 500,
	      layout: 'border',
	      plain:true,
	      modal:false,
	      border:false,
	      constrainHeader:true,
	      shim:false,
	      animCollapse:false,
	      buttonAlign:'right',
	      closeAction:'hide',
	      maximizable:true,
	      items:[this.framePanel],
	      buttons:[{
	        text:'Chiudi',
	        handler:this.hide,
	        scope:this
	      }]
	    });
        
	  },
	  
	  show: function(){
	    this.win.show();
	    this.framePanel.setSrc("acquisto_pacchetti.php");
	  },

	  hide: function(){
	    Ext.QuickTips.init();
	    this.win.close();
	  },
	  
	  buildFramePanel:function(){
		this.framePanel=new Ext.ux.ManagedIframePanel({
	      region:'center',
	      height:500,
	      collapsible:false,
	      split:true,
	      autoScroll:true
	    });
	  }
});