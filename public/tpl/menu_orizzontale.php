<ul id="menu_bar">
  <?foreach($obj->getChildObjects() as $ch):?>
    <?if(!$ch->get('hidden')): ?>
      <?echo "<li"?>
        <?=$ch->getConfig()->get("selected") ? " class=\"selected\"":""?>
        <?=$ch->getConfig()->get("rss")? "class=\"rss\"":""?>
      <?echo ">" ?>
        <a href="<?=url($ch->get("url"))?>" <?=$ch->getConfig()->get("rss")? "onclick=\"window.open(this.href); return false;\"":""?> ><?=$ch->get("title")?></a>
      <?echo "</li>"?>
    <?endif;?>
  <?endforeach ?>
</ul>

    
    
