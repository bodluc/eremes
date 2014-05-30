{if $product}
	<meta property="og:title" content="{$product->name|escape:'htmlall':'UTF-8'}"/>
    <meta property="og:type" content="product"/>
    <meta property="og:image" content="{$image_link}"/>
    <meta property="og:description" content="{$description|escape:htmlall:'UTF-8'}"/>
    <meta property="og:url" content="{$url}"/>
{/if}
