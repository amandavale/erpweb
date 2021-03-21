{if count($err) > 0}

<table align="center" width="90%" border="0" cellpadding="10" cellspacing="0">

	<tr>
		<td align="center">

		<table width="100%" border="0" cellpadding="0" cellspacing="3" class="tb4cantos">

			<tr>
      	<td class="dest_erro">ATEN&Ccedil;&Atilde;O!</td>
      </tr>

			<tr>
				<td>
					<ul>
		  				{foreach from=$err item=val}
		          	<li>{$val}</li>
		  				{/foreach}
          </ul>
      	</td>
      </tr>

		</table>
		
		</td>

	</tr>

</table>

{/if}
