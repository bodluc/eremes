<?php
/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
  if ($labels==_PAGE_CONVERSION."-FULL") {
        //$showfields = ""._PAGE.","._HITS.","._VISITORS.","._CONVERTED_VISITORS.","._CONVERSION_RATE."";
        $showfields = ""._PAGE.","._VISITORS.","._CONVERTED_VISITORS.","._CONVERSION_RATE."";
        //$help=_PERFORMANCE_OF_ENTRY_PAGES_DEFS;
        
        if (!$roadto) {
            $showfields = _INFO;
            $query = "select \"Please choose a target file\"";
            return;
        }
        
        # first, get the ID of the target page
        $kpi = getID($roadto,"urls");
        
        $top_entry_converted_search="";
        $entrypages_search = "";
        if ($search) {        
            $searchst = SearchMatchingIDs($search,"url",$searchmode,$profile->tablename_urls);
            $top_entry_converted_search = str_replace("and url IN", "where entry IN", $searchst);
            $entrypages_search = str_replace("and url IN", "where url IN", $searchst);            
        }
        /* this is a working query set that shows ONLY the page that have conversions
        $temp = $profile->tablename."_fp";
        $temp2 = $profile->tablename."_fp2";
        
        # get the entry page of each converted user for a certain KPI, then count the ocurrences
        $converted = "create temporary table $temp SELECT $nc entry, count(distinct cvisitor) as conversions from (SELECT m.visitorid as cvisitor, SUBSTRING_INDEX(Group_Concat(m.url order by m.timestamp),',',1) as entry FROM $profile->tablename as m, $profile->tablename_conversions as c where (c.timestamp >=$from and c.timestamp <=$to) and (m.timestamp >=$from and m.timestamp <=$to) and c.url='$kpi' and c.visitorid=m.visitorid and crawl=0 and (status=200 or status=302) group by m.visitorid) as cvisitors group by entry";
        $db->Execute($converted);        
        
        # next, get the entry page for each visitor, then count the ocurrences
        $entrypages = "SELECT $nc visitorid, SUBSTRING_INDEX(Group_Concat(url order by timestamp),',',1) as url FROM $profile->tablename where (timestamp >=$from and timestamp <=$to) and crawl=0 and (status=200 or status=302) group by visitorid";
        $toppages = "create temporary table $temp2 SELECT $nc url, count(distinct visitorid) as visitors from ($entrypages) as entrypages where url IN (select entry from $temp) group by url order by visitors desc limit $limit";       
        $db->Execute($toppages);
                
        # now join it all together
        $join = "select $nc u.url, visitors, conversions, CONCAT(FORMAT((conversions/visitors)*100,2),' %') as crate from $temp2 as a LEFT JOIN $temp as b on (b.entry=a.url) LEFT JOIN $profile->tablename_urls as u on (u.id=a.url) order by conversions desc";
        
        */
                
        # get the entry page of each converted user for a certain KPI, then count the ocurrences
        $entrypages_for_conversion = subsetDataToSourceID("SELECT $nc a.visitorid as cvisitor, SUBSTRING_INDEX(Group_Concat(a.url order by a.timestamp),',',1) as entry FROM $profile->tablename as a, $profile->tablename_conversions as c where (c.timestamp >=$from and c.timestamp <=$to) and (a.timestamp >=$from and a.timestamp <=$to) and c.url='$kpi' and c.visitorid=a.visitorid and crawl=0 and (status=200 or status=302) group by a.visitorid");
        $converted = "create temporary table top_entry_converted SELECT $nc entry, count(distinct cvisitor) as conversions from ($entrypages_for_conversion) as cvisitors $top_entry_converted_search group by entry";
        $db->Execute($converted);        
        
        # next, get the entry page for each visitor
        $entrypages = "create temporary table entrypages ". subsetDataToSourceID("SELECT $nc visitorid, SUBSTRING_INDEX(Group_Concat(url order by timestamp),',',1) as url FROM $profile->tablename where (timestamp >=$from and timestamp <=$to) and crawl=0 and (status=200 or status=302) group by visitorid");
        $db->Execute($entrypages);
         
        # now count the top entry pages
        $toppages = "create temporary table top_entry SELECT $nc url, count(distinct visitorid) as visitors from entrypages $entrypages_search group by url order by visitors desc limit $limit";       
        $db->Execute($toppages);
        
        # now count the pages that are not in the top entry pages list
        $missing = "select entry from top_entry_converted LEFT JOIN top_entry on url=entry where visitors IS NULL $searchst";
        $topmissingpages = "create temporary table top_missing SELECT $nc url, count(distinct visitorid) as visitors from entrypages where url IN ($missing) group by url order by visitors desc limit $limit";       
        $db->Execute($topmissingpages);
                
        # now join it all together
        $query = "select $nc u.url, visitors, conversions, CONCAT(FORMAT((conversions/visitors)*100,2),' %') as crate from (select * from top_entry UNION select * from top_missing) as a LEFT JOIN top_entry_converted as b on (b.entry=a.url) LEFT JOIN $profile->tablename_urls as u on (u.id=a.url) order by visitors desc";

  }
  if ($labels==_REFERRER_CONVERSION."-FULL") {

        $showfields = ""._REFERRER.","._VISITORS.","._CONVERTED_VISITORS.","._CONVERSION_RATE."";
        //$help=_PERFORMANCE_OF_ENTRY_PAGES_DEFS;
        
        if (!$roadto) {
            $showfields = _INFO;
            $query = "select \"Please choose a target file\"";
            return;
        }
        
        # first, get the ID of the target page
        $kpi = getID($roadto,"urls");         
       
        $top_entry_converted_search="";
        $entrypages_search = "";
        if ($search) {        
            $searchst = SearchMatchingIDs($search,"referrer",$searchmode,$profile->tablename_referrers);
            $top_entry_converted_search = str_replace("and url IN", "where entry IN", $searchst);
            $entrypages_search = str_replace("and url IN", "where url IN", $searchst);            
        }
              
               
        # get the entry referrer of each converted user for a certain KPI, then count the ocurrences
        $entrypages_for_conversion = subsetDataToSourceID("SELECT a.visitorid as cvisitor, SUBSTRING_INDEX(Group_Concat(a.referrer order by a.timestamp),',',1) as entry FROM $profile->tablename as a, $profile->tablename_conversions as c where (c.timestamp >=$from and c.timestamp <=$to) and (a.timestamp >=$from and a.timestamp <= c.timestamp) and c.url='$kpi' and c.visitorid=a.visitorid and crawl=0 and (status=200 or status=302) group by a.visitorid");
        $converted = "create temporary table top_entry_converted SELECT $nc entry, count(distinct cvisitor) as conversions from ($entrypages_for_conversion) as cvisitors $top_entry_converted_search group by entry order by conversions desc limit $limit";
        $db->Execute($converted);        
        
        # next, get the entry referrer for each visitor
        $entrypages = "create temporary table entrypages ". subsetDataToSourceID("SELECT $nc visitorid, SUBSTRING_INDEX(Group_Concat(referrer order by timestamp),',',1) as referrer FROM $profile->tablename where (timestamp >=$from and timestamp <=$to) and crawl=0 and (status=200 or status=302) group by visitorid");
        $db->Execute($entrypages);
         
        # now count the top entry referrers
        $toppages = "create temporary table top_entry SELECT $nc referrer, count(distinct visitorid) as visitors from entrypages $entrypages_search group by referrer order by visitors desc limit $limit";       
        $db->Execute($toppages);
        
        # now count the pages that are not in the top entry pages list
        $missing = "select entry from top_entry_converted LEFT JOIN top_entry on referrer=entry where visitors IS NULL $searchst";
        $topmissingpages = "create temporary table top_missing SELECT $nc referrer, count(distinct visitorid) as visitors from entrypages where referrer IN ($missing) group by referrer order by visitors desc limit $limit";       
        $db->Execute($topmissingpages);
                
        # now join it all together
        $query = "select $nc r.referrer, visitors, conversions, CONCAT(FORMAT((conversions/visitors)*100,2),' %') as crate from (select * from top_entry UNION select * from top_missing) as a LEFT JOIN top_entry_converted as b on (b.entry=a.referrer) LEFT JOIN $profile->tablename_referrers as r on (r.id=a.referrer) order by visitors desc";

  }
  if ($labels==_KEYWORD_CONVERSION."-FULL") {

        $showfields = ""._KEYWORDS.","._VISITORS.","._CONVERTED_VISITORS.","._CONVERSION_RATE."";
        //$help=_PERFORMANCE_OF_ENTRY_PAGES_DEFS;
        
        if (!$roadto) {
            $showfields = _INFO;
            $query = "select \"Please choose a target file\"";
            return;
        }
        
        # first, get the ID of the target page
        $kpi = getID($roadto,"urls");         
       
        $top_entry_converted_search="";
        $entrypages_search = "";
        if ($search) {        
            $searchst = SearchMatchingIDs($search,"referrer",$searchmode,$profile->tablename_referrers);
            $top_entry_converted_search = str_replace("and url IN", "where entry IN", $searchst);
            $entrypages_search = str_replace("and url IN", "where url IN", $searchst);            
        }
                
        # get the entry keyword of each converted user for a certain KPI, then count the ocurrences
        $entrypages_for_conversion = subsetDataToSourceID("SELECT a.visitorid as cvisitor, SUBSTRING_INDEX(Group_Concat(a.keywords order by a.timestamp),',',1) as entry FROM $profile->tablename as a, $profile->tablename_conversions as c where (c.timestamp >=$from and c.timestamp <=$to) and (a.timestamp >=$from and a.timestamp <= c.timestamp) and c.url='$kpi' and c.visitorid=a.visitorid and crawl=0 and (status=200 or status=302) group by a.visitorid");
        $converted = "create temporary table top_entry_converted SELECT $nc entry, count(distinct cvisitor) as conversions from ($entrypages_for_conversion) as cvisitors $top_entry_converted_search group by entry";
        $db->Execute($converted);        
        
        # next, get the entry keyword for each visitor
        $entrypages = "create temporary table entrypages ". subsetDataToSourceID("SELECT $nc visitorid, SUBSTRING_INDEX(Group_Concat(keywords order by timestamp),',',1) as keywords FROM $profile->tablename where (timestamp >=$from and timestamp <=$to) and crawl=0 and (status=200 or status=302) group by visitorid");
        $db->Execute($entrypages);
         
        # now count the top entry keywords
        $toppages = "create temporary table top_entry SELECT $nc keywords, count(distinct visitorid) as visitors from entrypages $entrypages_search group by keywords order by visitors desc limit $limit";       
        $db->Execute($toppages);
        
        # now count the keywords that are not in the top entry pages list
        $missing = "select entry from top_entry_converted LEFT JOIN top_entry on keywords=entry where visitors IS NULL $searchst";
        $topmissingpages = "create temporary table top_missing SELECT $nc keywords, count(distinct visitorid) as visitors from entrypages where keywords IN ($missing) group by keywords order by visitors desc limit $limit";       
        $db->Execute($topmissingpages);
                
        # now join it all together
        $query = "select $nc k.keywords, visitors, conversions, CONCAT(FORMAT((conversions/visitors)*100,2),' %') as crate from (select * from top_entry UNION select * from top_missing) as a LEFT JOIN top_entry_converted as b on (b.entry=a.keywords) LEFT JOIN $profile->tablename_keywords as k on (k.id=a.keywords) order by visitors desc";
  }
?>
