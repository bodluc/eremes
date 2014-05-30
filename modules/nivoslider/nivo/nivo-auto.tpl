<!-- MODULE NivoSlider Products -->
    <link rel="stylesheet" href="{$base_dir_ssl}{$module_dir}/themes/default/default.css1" type="text/css" media="screen" />
    <link rel="stylesheet" href="{$base_dir_ssl}{$module_dir}nivo.css" type="text/css" media="screen" />

<div id="tmnivoslider">     
    <div id="slider">
        {foreach from=$p item=slide}
            <a href="{$slide.link}"><img src="{$slide.path}" rel="{$slide.path}" alt="" title="#htmlcaption{$slide.id_product}"></a>
        {/foreach}
        </div>
        {foreach from=$p item=slide}
        <div id="htmlcaption{$slide.id_product}" class="nivo-html-caption">
            <h2>{$slide.name}</h2>
            <h3>iPad® 2</h3>
            <h4>{$slide.description_short}</h4>
            <h5>Już od {$slide.price} PLN</h5>
            <a class="slide_btn" href="product.php?id_product=12"></a>
        </div>
        {/foreach}
    </div>