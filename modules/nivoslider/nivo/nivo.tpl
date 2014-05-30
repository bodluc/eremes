<!-- MODULE NivoSlider Products -->

<div id="tmnivoslider">
    <div id="slider" style="background:{$p[0].link}">
        {foreach from=$p item=slide}
            <a href="{$slide.link}"><img src="{$slide.path}" rel="{$slide.path}" alt="" title="#htmlcaption{$slide.id_product}"></a>
        {/foreach}
        </div>
        <div id="htmlcaption1271" class="nivo-html-caption" >
        <a href="car-audio/car-audio/radia-samochodowe/p/Alpine-INA-W910R">
            <h2>ALPINE</h2>
            <h3>INA-W910R</h3>
            <h4>INA-W910R ACE Navi jest wielofunkcyjnym urządzeniem oferującym najlepszej jakości rozrywkę i nawigację dostępną w samochodzie. ACE Navi ma wszystko czego trzeba, aby w maksymalnym stopniu ułatwić prowadzenie pojazdu i czerpać z tego największą przyjemność.</h4>
            <h5>Już od 4999 PLN</h5>
            </div>
        </a>
         <div id="htmlcaption74" class="nivo-html-caption" >
        <a href="car-audio/glosniki-samochodowe/p/Focal-PC-130">
            <h2>FOCAL</h2>
            <h3>PC 130</h3>
            <h4>głośnik coaxialny 13cm</h4><h4>regulowane nachylenie tweetera</h4><h4>moc maksymalna: 120W</h4><h4>moc nominalna: 60W</h4>
            <h5>Już od 599 PLN</h5>
            </div>
        </a>
        <div id="htmlcaption117" class="nivo-html-caption">
        <a href="/car-audio/wzmacniacze/2-kanalowe/p/DLS-A3">
            <h2>DLS</h2>
            <h3>A3</h3>
            <h4>     Moc przy 4 ohm 2 x 150 W RMS</h4><h4>            Moc przy 2 ohm 2 x 270 W RMS</h4><h4>            Moc przy 1 ohm 2 x 425 W RMS</h4><h4>            Moc w mostku 4 ohm 550 W RMS</h4><h4>            Moc w mostku 2 ohm 870 W RMS</h4><h4>            Moc w mostku 1.33 ohm 1000 W RMS</h4><h4>
            Damping factor > 200</h4>
            <h5>Już od 3299 PLN</h5>
        </a>
        </div>
        <div id="htmlcaption491" class="nivo-html-caption">
        <a href="/car-audio/subwoofery/p/RE-Audio-SE-12-D2">
            <h2>RE AUDIO</h2>
            <h3>SE-12 D2</h3>
            <h4>Średnica 12"/30CM</h4><h4>Moc głośnika 600/1800W</h4><h4>Impedancja 2X2OHM</h4>
            <h5>Już od 1099 PLN</h5>
        </a>
        </div>

    </div>
<div id="tmmanufacturer">
    <div class="block_content">
    <ul>
    {foreach from=$m item=logo}
        <li><a href="/car-audio/marka/{$logo.link_rewrite}" title="{$logo.description}"><img src="{$base_dir_ssl}img/m/{$logo.id_manufacturer}-nivo.jpg" alt="{$logo.name}"></a></li>
    {/foreach}
                </ul>
    </div>
</div>
