{include file='navbar.tpl'}

<div class="container">
  {if !empty($WIDGETS)}
  <div class="row">
    <div class="col-md-9">
  {/if}
  <div class="card">
	<div class="card-block">
	  <div class="table-responsive">
		<table class="table table-striped table-bordered table-hover dataTables-users" style="width:100%">
		  <thead>
			<tr>
			  <th>{$USERNAME}</th>
			  <th>{$GROUP}</th>
			  <th>{$CREATED}</th>
			</tr>
		  </thead>
		  <tbody>
			{foreach from=$MEMBERS item=member}
			  <tr>
			    <td><a href="{$member.profile}"><img src="{$member.avatar}" class="rounded" style="height:35px; width:35px;" alt="{$member.nickname}" /></a> <a style="color:{$member.group_colour};" href="{$member.profile}">{$member.nickname}</a></td>
				<td>{$member.group}</td>
				<td>{$member.joined}</td>
			  </tr>
			{/foreach}
		  </tbody>
		</table>
	  </div>
	</div>
  </div>
  {if !empty($WIDGETS)}
  </div>
  <div class="col-md-3">
  {foreach from=$WIDGETS item=widget}
    {$widget}<br /><br />
  {/foreach}
  </div>
  </div>
  {/if}
</div>

{include file='footer.tpl'}