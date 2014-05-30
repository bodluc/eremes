<?php /* Smarty version 2.6.26, created on 2013-02-03 17:53:27
         compiled from SitesManager/templates/DisplayAlternativeTags.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'url', 'SitesManager/templates/DisplayAlternativeTags.tpl', 42, false),)), $this); ?>
<h3>Image Tracker code</h3>

The Simple Image Tracker code can be used when Javascript is disallowed. 
<br/><div class='toggleHelp' id='imageTracker' style='display:none'><a name='image'>› Display Image Tracker code </a></div>

<div class='imageTracker'>
<p>Some websites like MySpace or eBay will not allow users to add Javascript to their profile but accept HTML. In this case, you can still track visits with Piwik using the simple Image Tracker.
<br/>
<b>Note</b>: the code doesn't use Javascript so <b>Piwik will not be able to track some user information</b>
 such as search keywords, referrer websites, screen resolutions, plugin support and page titles.
</p>
<code>
&lt;!-- Piwik Image Tracker --&gt;<br/>
&lt;img src="<?php if (isset ( $this->_tpl_vars['piwikUrlRequest'] )): ?><?php echo $this->_tpl_vars['piwikUrlRequest']; ?>
<?php else: ?><?php echo $this->_tpl_vars['piwikUrl']; ?>
<?php endif; ?>piwik.php?idsite=<?php echo $this->_tpl_vars['idSite']; ?>
&amp;amp;rec=1" style="border:0" alt="" /&gt;<br/>
&lt;!-- End Piwik --&gt;<br/>
</code>
<br/>
The following parameters can also be passed to the image URL:
<ul> 
	<li><i>rec</i> - (required) The parameter &rec=1 is required to force the request to be recorded</li>
	<li><i>idsite</i> - (required) Defines the Website ID being tracked</li>
	<li><i>action_name</i> - Defines the custom Page Title for this page view</li>
	<li><i>urlref</i> - The Referrer URL: must be set to the referrer URL used before landing on the page containing the Image tracker. For example, in PHP this value is accessible via <pre>$_SERVER['HTTP_REFERER']</pre></li>
	<li><i>idgoal</i> - The request will trigger the given Goal</li>
	<li><i>revenue</i> - Used with idgoal, defines the custom revenue for this conversion</li>
	<li><i>and more!</i> - There are many more parameters you can set beyond the main ones above. See the <a href='http://piwik.org/docs/tracking-api/reference/'>Tracking API documentation page</a>.</li>
</ul>
</div>

<h3>Piwik Tracking API (Advanced users)</h3>
It is also possible to call the Piwik Tracking API using your favorite programming language. 
<br/><div class='toggleHelp' id='trackingAPI' style='display:none'><a name='image'>› Display Piwik Tracking API documentation </a></div>
<div class='trackingAPI'>
<p>
The Piwik Tracking API allows to trigger visits (page views and Goal conversions) from any environment (Desktop App, iPhone or Android app, Mobile website, etc.).
</p>

<p>We currently provide a <b>PHP client</b> to call the API from your PHP projects. 
If you would like to contribute a version of the client in another programming language (Python, Java, Ruby, Perl, etc.) please <a target='_blank' href='http://dev.piwik.org/'>create a ticket</a> in our developer area (please attach the client code to the ticket).
</p><p>Follow these instructions to get started with the Tracking API:
<ul style='list-style-type:decimal;'>
<li><a href='<?php if (isset ( $this->_tpl_vars['piwikUrlRequest'] )): ?><?php echo $this->_tpl_vars['piwikUrlRequest']; ?>
<?php else: ?><?php echo $this->_tpl_vars['piwikUrl']; ?>
<?php endif; ?><?php echo smarty_function_url(array('action' => 'downloadPiwikTracker'), $this);?>
' target='_blank'>Click here to download the file PiwikTracker.php</a>
</li><li>Upload the PiwikTracker.php file in the same path as your project files
</li><li>Copy the following code, then paste it onto every page you want to track.
<code>
&lt;?php <br/>
// -- Piwik Tracking API init -- <br/>
require_once "/path/to/PiwikTracker.php";<br/>
PiwikTracker::$URL = '<?php if (isset ( $this->_tpl_vars['piwikUrlRequest'] )): ?><?php echo $this->_tpl_vars['piwikUrlRequest']; ?>
<?php else: ?><?php echo $this->_tpl_vars['piwikUrl']; ?>
<?php endif; ?>';<br/>
 ?&gt;
</code>
</li><li>Choose a Tracking method, then paste the code onto every page you want to track.

<ul>
<li><b>Method 1: Advanced Image Tracker</b>
<br/>
<p>The client is used to generate the tracking URL that is wrapped inside a HTML &lt;img src=''&gt; code. 
<br/>Paste this code before the &lt;/body&gt; code in your pages.
<code>
&lt;?php <br/>
// Example 1: Tracks a pageview for Website id = <?php echo $this->_tpl_vars['idSite']; ?>
<br/>
echo '&lt;img src="'. str_replace("&amp;","&amp;amp;", Piwik_getUrlTrackPageView( $idSite = <?php echo $this->_tpl_vars['idSite']; ?>
, $customTitle = 'This title will appear in the report Actions > Page titles')) . '" alt="" /&gt;';<br/>
// Example 2: Triggers a Goal conversion for Website id = <?php echo $this->_tpl_vars['idSite']; ?>
 and Goal id = 2<br/>
//            $customRevenue is optional and is set to the amount generated by the current transaction (in online shops for example)<br/>
echo '&lt;img src="'. str_replace("&amp;","&amp;amp;", Piwik_getUrlTrackGoal( $idSite = <?php echo $this->_tpl_vars['idSite']; ?>
, $idGoal = 2, $customRevenue = 39)) . '" alt="" /&gt;';<br/>
 ?&gt;
</code>
<br/>
The Advanced Image Tracker method is similar to using the standard Javascript Tracking code. However, some user settings are not detected (resolution, local time, plugins and cookie support).
</p>
 
 </li>
 <li><b>Method 2: HTTP Request</b>
 <br/>
<p>You can also query the Piwik Tracker API remotely via HTTP. 
This is useful for environment where you can't execute HTML nor Javascript.
<br/>Paste this code anywhere in your code where you wish to track a user interaction.
 
<code>
&lt;?php <br/>
$piwikTracker = new PiwikTracker( $idSite = <?php echo $this->_tpl_vars['idSite']; ?>
 );<br/>
// You can manually set the visitor details (resolution, time, plugins, etc.)  <br/>
// See all other ->set* functions available in the PiwikTracker.php file<br/>
$piwikTracker->setResolution(1600, 1400);<br/><br/>
// Sends Tracker request via http<br/>
$piwikTracker->doTrackPageView('Document title of current page view');<br/><br/>
// You can also track Goal conversions<br/>
$piwikTracker->doTrackGoal($idGoal = 1, $revenue = 42);<br/>
 ?&gt;
</code>
</p>
</li></ul>
</li>
</ul>
</p>
<?php if (! isset ( $this->_tpl_vars['calledExternally'] ) || ! $this->_tpl_vars['calledExternally']): ?>
	<p>
	Read more about the Piwik Tracking API <a href='http://piwik.org/docs/tracking-api/' target='_blank'>in the documentation</a>
	</p>
<?php endif; ?>
</div>