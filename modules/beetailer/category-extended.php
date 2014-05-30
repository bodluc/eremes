<?php
/* Rewrited in order to get all the subcategories, not only the published ones */
class CategoryExtended extends CategoryCore
  {
    /* We rewrite it because we want to get the disabled categories as well */
    function recurseLiteCategTree($maxDepth = 3, $currentDepth = 0, $id_lang = NULL, $excludedIdsArray = NULL)
    {
      global $link;

      if (!(int)$id_lang)
        $id_lang = _USER_ID_LANG_;

      $children = array();
      /* getSubCategories having false as second parameter returns all the categories */
      if (($maxDepth == 0 OR $currentDepth < $maxDepth) AND $subcats = $this->getSubCategories((int)$id_lang, false) AND sizeof($subcats))
        foreach ($subcats AS &$subcat)
        {
          if (!$subcat['id_category'])
            break;
          elseif (!is_array($excludedIdsArray) || !in_array($subcat['id_category'], $excludedIdsArray))
          {
            $categ = new CategoryExtended((int)$subcat['id_category'], (int)$id_lang);
            $children[] = $categ->recurseLiteCategTree($maxDepth, $currentDepth + 1, (int)$id_lang, $excludedIdsArray);
          }
        }

      return array(
        'id' => (int)$this->id_category,
        'link' => $link->getCategoryLink((int)$this->id, $this->link_rewrite),
        'name' => $this->name,
        'desc'=> $this->description,
        'children' => $children
      );
    }
}

