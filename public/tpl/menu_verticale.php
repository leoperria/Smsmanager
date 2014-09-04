<?
// V.0.1 - Super preliminare... non gestisce altro che due livelli di menu
// PESSIMA IMPLEMENTAZIONE

$node=$ctx->getNode();

if (!function_exists("isNodeOwner")) {
  function isNodeOwner($node,$item){
     return startsWith($item->get("url"),"/node/") && 
            intval(substr($item->get("url"),6))==$node->getId() && 
            $item->get("node_owner");
  }
}

// Esplora tutto il menu
$menu=array();
if(intval(substr($obj->get("url"),6))==$node->getId()){
  $ctx->setVar("breadCrumbId",$obj->getId());
  $ctx->setVar("breadCrumbLevel",0);
}
foreach($obj->getChildObjects() as $menuItem){
   if(!$menuItem->get('hidden')){
   $nItem=array(
     "url"=>$menuItem->get("url"),
     "title"=>$menuItem->get("title"),
     "open"=>isNodeOwner($node,$menuItem),
     "children"=>array()
   );
   if(intval(substr($menuItem->get("url"),6))==$node->getId()){
    $ctx->setVar("breadCrumbId",$menuItem->getId());
    $ctx->setVar("breadCrumbLevel",1);
   }
   
   if ($menuItem->hasChildObjects()){
     $children=array();
     foreach($menuItem->getChildObjects() as $subMenuItem){
       $child=array(
         "url"=>$subMenuItem->get("url"),
         "title"=>$subMenuItem->get("title"),
         "open"=>isNodeOwner($node,$subMenuItem),
       );
       if ($child["open"]){
         $nItem["open"]=true;
       }
       $children[]=$child;
       if(intval(substr($subMenuItem->get("url"),6))==$node->getId()){
         $ctx->setVar("breadCrumbId",$subMenuItem->getId());
         $ctx->setVar("breadCrumbLevel",2);
       }
     }
     if ($nItem["open"]){     
       $nItem["children"]=$children;
     }
     
   }
   $menu[]=$nItem;
   }
}

?>
<div class="submenu">
   <?if($obj->hasProperty("url") && $obj->get("url")!=null):?>
     <h2><a href="<?=url($obj->get("url"))?>"><?=strtoupper($obj->get("title"));?></a></h2>
   <?else:?>
     <h2><?=strtoupper($obj->get("title"))?></h2>
   <?endif?>
   <ul>
     <?foreach($menu as $item):?>
       <li>
         <?if ($item["open"]) {$cssClass="selected";} else{$cssClass="";}?>
         <a class="<?=$cssClass?>" href="<?=url($item["url"])?>"><?=$item["title"]?></a>
         <?if($item["open"] && count($item["children"])>0 ):?>
           <ul>
           <?foreach($item["children"] as $sitem):?>
             <li>
             <?if ($sitem["open"]) {$cssClass="selected";} else{$cssClass="";}?>
             <a class="<?=$cssClass?>" href="<?=url($sitem["url"])?>"><?=$sitem["title"]?></a>
             </li>
           <?endforeach?>
           </ul>
         <?endif?>
       </li>
     <?endforeach?>
   </ul>
</div>