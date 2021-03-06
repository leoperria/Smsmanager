<?php
  $utente=Zend_Registry::get('aaac');
  $LOADER_CONFIG_ADMINISTRATOR=array(
    "JAVASCRIPT_FILES"=>array(
       "js/permissions.js",
       "js/identity.js",
       "js/utils.js",
       "js/lib/OOSubmit.js",
       "js/lib/Ext.ux.grid.CellActions.js",
       "js/lib/Ext.ux.grid.RowActions.js",
       "js/lib/Ext.ux.grid.Search.js",
       "js/lib/Ext.ux.RowFitLayout.js",
       "js/lib/Ext.ux.BoxSelect.js",
       "js/lib/Ext.ux.grid.GridSummary.js",
       "js/lib/Ext.ux.UploadDialog.js",
       "js/lib/MessageWindow.js",
       "js/lib/PagingStore.js",
       "js/lib/GroupSummary.js",
       "js/lib/miframe-min.js",
       "js/generic_grid.js",
       "js/generic_form.js",
       "js/main_administrator.js",
       "js/users/users_main.js",
       "js/users/edit_user.js",
       "js/users/edit_password.js",
       "js/permissions/permissions_main.js",
       "js/permissions/edit_permission.js",
       "js/permissions/edit_area_permission.js",
       "js/roles/roles_main.js",
       "js/roles/edit_role.js",
       "js/roles/edit_role_permissions.js",
       "js/contatti/contatti_main.js",
  	   "js/contatti/edit_contatto.js",
       "js/contatti/importa_contatti.js",
       "js/organizations/organizations_main.js",
       "js/organizations/organizations_edit.js",
       "js/organizations/users_main.js",
       "js/organizations/edit_user.js",
       "js/organizations/edit_password.js",
       "js/organizations/contatti_inseriti_main.js",
       "js/organizations/contatti_organization_main.js",
       "js/organizations/internalresource_organization.js",
       "js/dataentry/dataentry_main.js",
       "js/dataentry/edit_dataentry.js",
       "js/dataentry/organizations_list.js",
       "js/dataentry/contatti_inseriti_main.js",
       "js/campagne/campagne_main.js",
       "js/campagne/edit_campagna.js",
       "js/ricariche/ricariche_main.js",
       "js/ricariche/edit_ricarica.js",
       "js/ricariche/impostazioni_ricariche.js",
       "js/ricariche/edit_costo_ricarica.js",
       "js/gruppi/gruppi_main.js",
       "js/gruppi/edit_gruppo.js"
    ),
    "CSS_FILES"=>array(
       "/lib/ext3/resources/css/ext-all.css",
       "/lib/ext3/resources/css/xtheme-gray.css",    
       "/css/main.css"
    )
  );
  
  $LOADER_CONFIG_ESERCENTE=array(
    "JAVASCRIPT_FILES"=>array(
       "js/permissions.js",
       "js/identity.js",
       "js/utils.js",
       "js/lib/OOSubmit.js",
       "js/lib/Ext.ux.grid.CellActions.js",
       "js/lib/Ext.ux.grid.RowActions.js",
       "js/lib/Ext.ux.grid.Search.js",
       "js/lib/Ext.ux.RowFitLayout.js",
       "js/lib/Ext.ux.BoxSelect.js",
       "js/lib/Ext.ux.grid.GridSummary.js",
       "js/lib/Ext.ux.UploadDialog.js",
       "js/lib/MessageWindow.js",
       "js/lib/PagingStore.js",
       "js/lib/GroupSummary.js",
       "js/lib/miframe-min.js",
       "js/generic_grid.js",
       "js/generic_form.js",
       "js/main_esercente.js",
       "js/users/users_main.js",
       "js/users/edit_user.js",
       "js/users/edit_password.js",
       "js/contatti/contatti_main.js",
       "js/contatti/edit_contatto.js",
       "js/contatti/importa_contatti.js",
       "js/campagne/campagne_main.js",
       "js/campagne/edit_campagna.js",
       "js/gruppi/gruppi_main.js",
       "js/gruppi/edit_gruppo.js",
       "js/ricariche/gateway_ricariche.js",
    ),
    "CSS_FILES"=>array(
       "/lib/ext3/resources/css/ext-all.css",
       "/lib/ext3/resources/css/xtheme-gray.css",    
       "/css/main.css"
    )
  );
  
  $LOADER_CONFIG_DATAENTRY=array(
    "JAVASCRIPT_FILES"=>array(
       "js/permissions.js",
       "js/identity.js",
       "js/utils.js",
       "js/lib/OOSubmit.js",
       "js/lib/Ext.ux.grid.CellActions.js",
       "js/lib/Ext.ux.grid.RowActions.js",
       "js/lib/Ext.ux.grid.Search.js",
       "js/lib/Ext.ux.RowFitLayout.js",
       "js/lib/Ext.ux.BoxSelect.js",
       "js/lib/Ext.ux.grid.GridSummary.js",
       "js/lib/Ext.ux.UploadDialog.js",
       "js/lib/MessageWindow.js",
       "js/lib/PagingStore.js",
       "js/lib/GroupSummary.js",
       "js/lib/miframe-min.js",
       "js/generic_grid.js",
       "js/generic_form.js",
       "js/main_dataentry.js",
       "js/users/users_main.js",
       "js/users/edit_password.js",
       "js/contatti/contatti_main.js",
       "js/contatti/edit_contatto.js",
       "js/contatti/importa_contatti.js"
    ),
    "CSS_FILES"=>array(
       "/lib/ext3/resources/css/ext-all.css",
       "/lib/ext3/resources/css/xtheme-gray.css",    
       "/css/main.css"
    )
  );
switch($utente->getCurrentUser()->ID_role){
  case Constants::AMMINISTRATORE:
   $LOADER_CONFIG=$LOADER_CONFIG_ADMINISTRATOR;
  break;
  case Constants::ESERCENTE:
   $LOADER_CONFIG=$LOADER_CONFIG_ESERCENTE;
  break;
  case Constants::DATAENTRY:
  case Constants::DATAENTRY_INTERNO:
   $LOADER_CONFIG=$LOADER_CONFIG_DATAENTRY;
  break; 
}