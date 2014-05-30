
 <div id="slider-wrapper" style="margin:0 auto">
			<div id="slider3" >
             {foreach from=$p item=slide}
				<div data-title="Quote" class="quote" >
					<div style="width:535px;height:369px;margin:0 auto">
                    <a href="{$slide.link}"><img src="{$slide.path}"/></a>
                     </div>
				</div>
         {/foreach}
    </div>
</div> 
