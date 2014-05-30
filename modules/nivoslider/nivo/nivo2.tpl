<!-- MODULE NivoSlider Products -->
    <link rel="stylesheet" href="{$base_dir_ssl}{$module_dir}/themes/default/default.css" type="text/css" media="screen" />
    <link rel="stylesheet" href="{$base_dir_ssl}{$module_dir}nivo-slider.css" type="text/css" media="screen" />

    <div id="nivoslider-wrapper" style="background:url({$p[0].path}) no-repeat;">
        <div class="slider-wrapper theme-default" >
            <div id="slider" class="nivoSlider" style="width:100%;height:370px">
        {foreach from=$p item=slide}
            {if $slide.active}
            <img height="370" width="500" src="{$slide.path}" data-thumb="{$slide.path}" alt="{$slide.name}" title="#html{$slide.id_product}"/>
            {/if}
        {/foreach}
            </div>
            {foreach from=$p item=slide}
            {if $slide.active}
                <div id="html{$slide.id_product}" class="nivo-html-caption" >
                    {$slide.path}
                </div>
            {/if}
            {/foreach}
        </div>

    </div>


    

<!-- END MODULE NivoSlider Products -->