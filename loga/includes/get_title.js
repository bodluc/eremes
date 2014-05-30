/* Logaholic Web Analytics software             Copyright(c) 2005-2011 Logaholic B.V.
 *                                                               All rights Reserved.
 * This code is subject to the Logaholic license. Unauthorized copying is prohibited.
 * support@logaholic.com                         http://www.logaholic.com/License.txt
*/ 
//this part will add titles to reports containing page urls on the fly
$(document).ready(function(){
    elements = $('.pathpart');
   
    elements.each(function() {
        var obj = $(this);
        
        if (obj.text()!="") {
            var url = "includes/get_title.php?conf=" + conf_name + "&url=" + obj.text();            
            $.ajax({
                url: url,
                async: true,
                success: function(data){
                    if (data != "") {
                        title = "<span class='pagetitle'> (" + data + ")</span>";
                        obj.html(obj.html() + title); 
                    } 
                }
            }); 
        }
    });
});
