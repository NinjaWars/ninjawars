<h1>Ninja List</h1>

<div id='player-list'>
{if $ninja_count eq 0}
  <!-- Search found nothing to display -->
  <p class='notice'>No ninja to display.</p>
  <p><a href="/list?hide={$hide|escape:'url'}"><i class='fas fa-list'></i> Full Ninja List</a></p>
{/if}

  <div class='list-all-players-search centered'>
    <form action="/list" method="get">
			<div class="input-group">
				<input class='form-control list-search formButton' type="search" name="searched" required=required placeholder="search" value="{$searched|escape}">
				<span class="input-group-btn">
					<button class="btn btn-default" type="submit"><i class='fa fa-search' title='Search by ninja name'></i></button>
        	<input type="hidden" name="hide" value="{$hide|escape}">
{if !$searched}
					<a class="btn btn-default" href="/list?page={$page|escape:'url'}&amp;hide={if $hide == "dead"}none{else}dead{/if}&amp;searched={$searched|escape:'url'}">
						{if $hide == "dead"}Show{else}Hide{/if} {$dead_count} dead
					</a>
{/if}
{if $searched}
				<a class="btn btn-default" href="/list">Clear <i class="fas fa-times"></i></a>
{/if}

				</span>
			</div><!-- /input-group -->
		</form>
  </div>

  <!-- The player list navigation section -->
{include file='list.nav.tpl'}

{* Direct inject svg *}
{$shuriken_svg='
<!-- Created with Inkscape (http://www.inkscape.org/) -->
<svg id="nw-shuriken-svg2" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns="http://www.w3.org/2000/svg" height="95.245mm" width="113.09mm" version="1.1" xmlns:cc="http://creativecommons.org/ns#" xmlns:dc="http://purl.org/dc/elements/1.1/" viewBox="0 0 400.69856 337.48064">
 <metadata id="metadata7">
  <rdf:RDF>
   <cc:Work rdf:about="">
    <dc:format>image/svg+xml</dc:format>
    <dc:type rdf:resource="http://purl.org/dc/dcmitype/StillImage"/>
    <dc:title/>
   </cc:Work>
  </rdf:RDF>
 </metadata>
 <g id="layer1" transform="translate(-5.2544 -5.2344)">
  <path id="path3441" d="m144.14 326.16c0.48651-9.1094 1.6844-25.281 2.6619-35.938 4.0025-43.632 5.5573-63.935 4.9689-64.887-1.2529-2.0273 1.8122-15.671 3.9686-17.665 1.2165-1.1251 4.4618-2.7018 7.2118-3.5038 7.1811-2.0943 16.182-9.1913 20.069-15.824 3.157-5.3871 3.2605-6.0499 1.7734-11.361-2.1376-7.6348-10.663-16.894-18.993-20.626-5.2595-2.3567-8.7564-2.9586-17.224-2.9647-9.2599-0.006-11.497 0.44897-17.411 3.5468-5.0286 2.6338-7.9426 3.3976-11.25 2.9489-6.88-0.96-110.86-39.05-113.73-41.68-1.4857-1.3648-1.2923-1.7238 1.25-2.3206 7.2558-1.7033 105.04-10.666 116.54-10.681 4.7915-0.006 7.7468 1.0071 14.375 4.9299 16.245 9.6142 32.819 10.182 44.914 1.5393 8.5889-6.1378 3.9538-17.395-11.417-27.727-4.8839-3.2831-8.6651-6.7937-9.0822-8.4321-0.81344-3.1955 2.9762-69.157 4.034-70.215 0.37992-0.37992 1.6367 0.50479 2.7928 1.966s13.352 15.147 27.102 30.414c13.75 15.266 25.758 28.984 26.684 30.484 1.5035 2.4358 1.2591 3.4101-2.2857 9.1125l-3.9692 6.3851 2.4886 4.8301c4.9956 9.6958 14.871 16.435 28.077 19.161 8.3562 1.7246 15.043 0.91205 20.673-2.5121 3.371-2.05 3.9578-3.1399 3.9578-7.3515 0-2.7195 0.61957-5.3275 1.3768-5.7955 1.5059-0.93069 5.9993-1.4776 50.498-6.1469 16.5-1.7313 31.547-3.4155 33.438-3.7426 2.0262-0.35052 3.4375-0.0304 3.4375 0.77963 0 1.3533-10.357 13.183-33.38 38.123l-11.709 12.685-15.268 0.43981c-18.37 0.52917-23.738 2.6746-24.424 9.7616-1.3945 14.409 13.424 27.943 37.132 33.915 7.7812 1.9601 11.037 4.8338 28.899 25.511 9.8746 11.431 49.753 56.129 61.047 68.426 6.5751 7.1587 2.5757 5.8573-35.422-11.526-11.688-5.3469-34.679-15.549-51.091-22.671-16.565-7.1882-30.112-13.79-30.449-14.838-1.9668-6.1094-7.7798-15.944-11.985-20.278-11.099-11.437-31.263-15.766-43.465-9.3304-11.952 6.3036-14.165 16.173-6.7591 30.134 7.1823 13.538 7.4173 12.928-16.478 42.783-11.469 14.329-28.944 36.178-38.832 48.553-9.8885 12.375-19.063 23.766-20.389 25.312-4.7513 5.5458-5.3005 3.8149-4.3624-13.75zm96.701-170.99c7.5222-7.008-2.0546-20.674-16.605-23.694-8.0302-1.6671-17.548 0.26442-19.527 3.9628-2.7918 5.2165 2.6313 13.661 12.618 19.648 6.1507 3.6873 19.595 3.735 23.514 0.0834z"/>
 </g>
</svg>'}

  <!-- Table header -->
  <table class="playerTable outer-table">
		<thead>
			<tr class='playerTableHead'>
				<th>Rank</th><th>Name</th><th>Level</th><th>Class</th><th>Clan</th>
			</tr>
		</thead>
	  <!--  Loop over and display each of the players in a table row format -->
{foreach from=$ninja_rows key=row item=ninja}
		<!-- Darken row if dead, change a little on odd vs. even -->
		<tr class="playerRow {$ninja.alive_class} {$ninja.odd_or_even}">
		  <td class="playerCell rankCell">{$ninja.player_rank|escape}</td>
		  <td class="playerCell nameCell">
		  	<a href="/player?player_id={$ninja.player_id|escape:"url"}" target='main'>{$ninja.uname|escape}</a>
		  </td>
		  <!-- Level category as a static resource -->
		  <td class="playerCell levelCell">
		  	<span class='{$ninja.level|level_label|css_classify}'>{$ninja.level|level_label} [{$ninja.level|escape}]</span>
		  </td>
		  <td class="playerCell classCell">
		    <!-- Display an image of the right colored shuriken. -->
		    <span class='class-name {$ninja.class_theme}'>
					<span class='svg-shuriken'>
						{$shuriken_svg}
					</span>
		      {$ninja.class|escape}
		    </span>
		  </td>
		  <td class="playerCell clanCell">
		    {if $ninja.clan_id}<a href='/clan/view?clan_id={$ninja.clan_id|escape:"url"}'>{/if}{$ninja.clan_name|escape}{if $ninja.clan_id}</a>{/if}
		  </td>
		</tr>
{/foreach}
		<tfoot>
			<tr>
				<td colspan=5>
					<!-- Active Lurker List -->
					{if $active_ninjas}
						{include file='list.active.tpl' active_ninja=$active_ninjas}
					{/if}
				</td>
			</tr>
		</tfoot>
	</table><!-- End the player table -->

	<!-- Display the nav again -->
{include file='list.nav.tpl'}
   </div> <!-- End of player list -->
