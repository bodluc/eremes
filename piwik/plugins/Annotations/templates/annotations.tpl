<div class="annotations">

{if empty($annotations)}

<div class="empty-annotation-list">{'Annotations_NoAnnotations'|translate}</div>

{/if}

<table>
{foreach from=$annotations item=annotation}
{include file="Annotations/templates/annotation.tpl"}
{/foreach}
<tr class="new-annotation-row" style="display:none" data-date="{$startDate}">
	<td class="annotation-meta">
		<div class="annotation-star">&nbsp;</div>
		<div class="annotation-period-edit">
			<a href="#">{$startDate}</a>
			<div class="datepicker" style="display:none"/>
		</div>
	</td>
	<td class="annotation-value">
		<input type="text" value="" class="new-annotation-edit" placeholder="{'Annotations_EnterAnnotationText'|translate}"/><br/>
		<input type="button" class="submit new-annotation-save" value="{'General_Save'|translate}"/>
		<input type="button" class="submit new-annotation-cancel" value="{'General_Cancel'|translate}"/>
	</td>
	<td class="annotation-user-cell"><span class="annotation-user">{$userLogin}</span></td>
</tr>
</table>

</div>
