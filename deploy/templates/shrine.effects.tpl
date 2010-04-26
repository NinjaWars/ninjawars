<h1>Shrine Effects</h1>

<div id='heal-result' style="margin-top: 10px;">


{if $error}
    <div class='ninja-notice'>{$error}</div>
{else}
    {if $resurrect_requested}
        <p>What once was dead shall rise again.</p>
        {if $turn_taking_resurrect}
		    <p class='ninja-notice'>Since you have no kills, your resurrection will cost you part of your life time.</p>
		<p>Current Turns: {$startingTurns}</div>
		<p class='ninja-notice'>Adjusted Turns after returning to life: {$final_turns}</p>
		    
        {elseif $kill_taking_resurrect}
		<p>Current Kills: {$startingKills}</p>
		<p class='ninja-notice'>Adjusted Kills after returning to life: {$final_kills}</p>
		{else}
		<!-- Resurrect that took no kills, for low levels. -->
		<p>You have returned to life!</p>
		{/if}
	{/if}
    {if $heal_requested}
        <p>A monk tends to your wounds and you are {if $fully_healed}fully healed{else}healed to {$finalHealth} health{/if}.</p>
    {elseif $poison_cure_requested}
        <p>You have been cured!</p>
    {/if}

{/if}

</div> <!-- End of heal-result div -->

<a href="shrine.php">Heal Again?</a>