<!-- MODULE NivoSlider Products -->
    <link rel="stylesheet" href="{$base_dir_ssl}{$module_dir}/themes/default/default.css" type="text/css" media="screen" />
    <link rel="stylesheet" href="{$base_dir_ssl}{$module_dir}nivo-slider.css" type="text/css" media="screen" />

    <div id="nivoslider-wrapper" style="background:url({$p[0].path}) no-repeat;height:460px">
        <div class="slider-wrapper theme-default">
            <div id="slider" class="nivoSlider">
        {foreach from=$p item=slide}
            {if $slide.active}
            <a href="{$slide.link}" title="{$slide.name}"><img src="{$slide.path}" alt="{$slide.name}" title="#html{$slide.id_product}"/></a>
            {/if}
        {/foreach}
            </div>
            {foreach from=$p item=slide}
            {if $slide.active}
            
                <div id="html{$slide.id_product}" class="nivo-html-caption" >
                    <strong>{$slide.name}</strong>
                    <a href="{$slide.link}" title="Sprawdź detale produktu">
                    <div class="price">
                        <p class="p1">{$slide.price} zł</p>
                    </div>
                    </a>
                    <div class="logo" style="background:url({$base_dir_ssl}img/m/{$slide.id_manufacturer}-nivo.jpg);">
                    </div>
                </div>
            
            {/if}
            {/foreach}
        </div>

    </div>


    

<!-- END MODULE NivoSlider Products -->