<?php /* Smarty version 2.6.26, created on 2013-02-09 15:11:05
         compiled from Goals/templates/title_and_evolution_graph.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'translate', 'Goals/templates/title_and_evolution_graph.tpl', 4, false),array('modifier', 'money', 'Goals/templates/title_and_evolution_graph.tpl', 19, false),array('function', 'sparkline', 'Goals/templates/title_and_evolution_graph.tpl', 9, false),)), $this); ?>
<a name="evolutionGraph" graphId="<?php echo $this->_tpl_vars['nameGraphEvolution']; ?>
"></a>

<?php if ($this->_tpl_vars['displayFullReport']): ?>
	<h2><?php if (isset ( $this->_tpl_vars['goalName'] )): ?><?php echo ((is_array($_tmp='Goals_GoalX')) ? $this->_run_mod_handler('translate', true, $_tmp, $this->_tpl_vars['goalName']) : smarty_modifier_translate($_tmp, $this->_tpl_vars['goalName'])); ?>
<?php else: ?><?php echo ((is_array($_tmp='Goals_GoalsOverview')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
<?php endif; ?></h2>
<?php endif; ?>
<?php echo $this->_tpl_vars['graphEvolution']; ?>


<div id='leftcolumn' <?php if (! $this->_tpl_vars['isWidget']): ?>style='width:33%'<?php endif; ?>>
	<div class="sparkline"><?php echo smarty_function_sparkline(array('src' => $this->_tpl_vars['urlSparklineConversions']), $this);?>

	<?php if (isset ( $this->_tpl_vars['ecommerce'] )): ?> <strong><?php echo $this->_tpl_vars['nb_conversions']; ?>
</strong> <?php echo ((is_array($_tmp='General_EcommerceOrders')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
 <img src='themes/default/images/ecommerceOrder.gif'> 
	<?php else: ?><?php echo ((is_array($_tmp='Goals_Conversions')) ? $this->_run_mod_handler('translate', true, $_tmp, "<strong>".($this->_tpl_vars['nb_conversions'])."</strong>") : smarty_modifier_translate($_tmp, "<strong>".($this->_tpl_vars['nb_conversions'])."</strong>")); ?>

	<?php endif; ?>
		 <?php if (isset ( $this->_tpl_vars['goalAllowMultipleConversionsPerVisit'] ) && $this->_tpl_vars['goalAllowMultipleConversionsPerVisit']): ?>
		 	(<?php echo ((is_array($_tmp='VisitsSummary_NbVisits')) ? $this->_run_mod_handler('translate', true, $_tmp, "<strong>".($this->_tpl_vars['nb_visits_converted'])."</strong>") : smarty_modifier_translate($_tmp, "<strong>".($this->_tpl_vars['nb_visits_converted'])."</strong>")); ?>
)
		 <?php endif; ?>
	</div>
	<?php if ($this->_tpl_vars['revenue'] != 0 || isset ( $this->_tpl_vars['ecommerce'] )): ?>
		<div class="sparkline"><?php echo smarty_function_sparkline(array('src' => $this->_tpl_vars['urlSparklineRevenue']), $this);?>

		<?php $this->assign('revenue', ((is_array($_tmp=$this->_tpl_vars['revenue'])) ? $this->_run_mod_handler('money', true, $_tmp, $this->_tpl_vars['idSite']) : smarty_modifier_money($_tmp, $this->_tpl_vars['idSite']))); ?>
		<?php if (isset ( $this->_tpl_vars['ecommerce'] )): ?><strong><?php echo $this->_tpl_vars['revenue']; ?>
</strong> <?php echo ((is_array($_tmp='General_TotalRevenue')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>

		<?php else: ?><?php echo ((is_array($_tmp='Goals_OverallRevenue')) ? $this->_run_mod_handler('translate', true, $_tmp, "<strong>".($this->_tpl_vars['revenue'])."</strong>") : smarty_modifier_translate($_tmp, "<strong>".($this->_tpl_vars['revenue'])."</strong>")); ?>

		<?php endif; ?>
		</div>
	<?php endif; ?>
	<?php if (isset ( $this->_tpl_vars['ecommerce'] )): ?>
		<div class="sparkline"><?php echo smarty_function_sparkline(array('src' => $this->_tpl_vars['urlSparklineAverageOrderValue']), $this);?>

		<strong><?php echo ((is_array($_tmp=$this->_tpl_vars['avg_order_revenue'])) ? $this->_run_mod_handler('money', true, $_tmp, $this->_tpl_vars['idSite']) : smarty_modifier_money($_tmp, $this->_tpl_vars['idSite'])); ?>
</strong> <?php echo ((is_array($_tmp='General_AverageOrderValue')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</div>
	<?php endif; ?>
	
</div>
<div id='leftcolumn' <?php if (! $this->_tpl_vars['isWidget']): ?>style='width:33%'<?php endif; ?>>
	<div class="sparkline"><?php echo smarty_function_sparkline(array('src' => $this->_tpl_vars['urlSparklineConversionRate']), $this);?>

	<?php if (isset ( $this->_tpl_vars['ecommerce'] )): ?><?php ob_start(); ?><?php echo ((is_array($_tmp='General_EcommerceOrders')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
<?php $this->_smarty_vars['capture']['default'] = ob_get_contents();  $this->assign('ecommerceOrdersText', ob_get_contents());ob_end_clean(); ?>
		<?php echo ((is_array($_tmp='Goals_ConversionRate')) ? $this->_run_mod_handler('translate', true, $_tmp, "<strong>".($this->_tpl_vars['conversion_rate'])."</strong> ".($this->_tpl_vars['ecommerceOrdersText'])) : smarty_modifier_translate($_tmp, "<strong>".($this->_tpl_vars['conversion_rate'])."</strong> ".($this->_tpl_vars['ecommerceOrdersText']))); ?>

	<?php else: ?>
		<?php echo ((is_array($_tmp='Goals_OverallConversionRate')) ? $this->_run_mod_handler('translate', true, $_tmp, "<strong>".($this->_tpl_vars['conversion_rate'])."</strong>") : smarty_modifier_translate($_tmp, "<strong>".($this->_tpl_vars['conversion_rate'])."</strong>")); ?>

	<?php endif; ?>
	</div>
	<?php if (isset ( $this->_tpl_vars['ecommerce'] )): ?>
		<div class="sparkline"><?php echo smarty_function_sparkline(array('src' => $this->_tpl_vars['urlSparklinePurchasedProducts']), $this);?>

		<strong><?php echo $this->_tpl_vars['items']; ?>
</strong> <?php echo ((is_array($_tmp='General_PurchasedProducts')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</div>
	<?php endif; ?>
</div>
<?php if (isset ( $this->_tpl_vars['ecommerce'] )): ?>
<div id='rightcolumn'  <?php if (! $this->_tpl_vars['isWidget']): ?>style='width:30%'<?php endif; ?>>
	<div>
		<img src='themes/default/images/ecommerceAbandonedCart.gif'> <i><?php echo ((is_array($_tmp='General_AbandonedCarts')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</i>
	</div>
	
	<div class="sparkline"><?php echo smarty_function_sparkline(array('src' => $this->_tpl_vars['cart_urlSparklineConversions']), $this);?>

	<?php ob_start(); ?><?php echo ((is_array($_tmp='Goals_AbandonedCart')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
<?php $this->_smarty_vars['capture']['default'] = ob_get_contents();  $this->assign('ecommerceAbandonedCartsText', ob_get_contents());ob_end_clean(); ?>
	<strong><?php echo $this->_tpl_vars['cart_nb_conversions']; ?>
</strong> <?php echo ((is_array($_tmp='General_VisitsWith')) ? $this->_run_mod_handler('translate', true, $_tmp, $this->_tpl_vars['ecommerceAbandonedCartsText']) : smarty_modifier_translate($_tmp, $this->_tpl_vars['ecommerceAbandonedCartsText'])); ?>

	</div>
	
	<div class="sparkline"><?php echo smarty_function_sparkline(array('src' => $this->_tpl_vars['cart_urlSparklineRevenue']), $this);?>

	<?php ob_start(); ?><?php echo ((is_array($_tmp=$this->_tpl_vars['cart_revenue'])) ? $this->_run_mod_handler('money', true, $_tmp, $this->_tpl_vars['idSite']) : smarty_modifier_money($_tmp, $this->_tpl_vars['idSite'])); ?>
<?php $this->_smarty_vars['capture']['default'] = ob_get_contents();  $this->assign('revenue', ob_get_contents());ob_end_clean(); ?>
	<?php ob_start(); ?><?php echo ((is_array($_tmp='Live_GoalRevenue')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
<?php $this->_smarty_vars['capture']['default'] = ob_get_contents();  $this->assign('revenueText', ob_get_contents());ob_end_clean(); ?>
	<strong><?php echo $this->_tpl_vars['revenue']; ?>
</strong> <?php echo ((is_array($_tmp='Goals_LeftInCart')) ? $this->_run_mod_handler('translate', true, $_tmp, $this->_tpl_vars['revenueText']) : smarty_modifier_translate($_tmp, $this->_tpl_vars['revenueText'])); ?>

	</div>
	
	<div class="sparkline"><?php echo smarty_function_sparkline(array('src' => $this->_tpl_vars['cart_urlSparklineConversionRate']), $this);?>

	<strong><?php echo $this->_tpl_vars['cart_conversion_rate']; ?>
</strong> <?php echo ((is_array($_tmp='General_VisitsWith')) ? $this->_run_mod_handler('translate', true, $_tmp, $this->_tpl_vars['ecommerceAbandonedCartsText']) : smarty_modifier_translate($_tmp, $this->_tpl_vars['ecommerceAbandonedCartsText'])); ?>

	</div>
</div>
<?php endif; ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "CoreHome/templates/sparkline_footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
