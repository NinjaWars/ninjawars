      	<div id='footer-top-bar'>
        <span id='nw-catchphrases'>
{literal}
          <script type="text/javascript">
            $().ready(function (){
                var footer = $('#index-footer');
                //Hide the second two sections.
                var footerBottoms = footer.find('#footer-middle-bar, #footer-bottom-bar').hide();
                var catchphrases = $('#nw-catchphrases span');
                var rand = Math.floor(Math.random()*catchphrases.size());
                // Choose random index.
                catchphrases.hide().eq(rand).show();
                // Hide all, show one at random.
                
                // When any of the three sections are hovered, show the bottom two.
        // Only change the display of the bottom sections if another event doesn't over-ride.
                footer.hover(
                	function(){
                		footerBottoms.stop(true, true).slideDown()
                		footer.css({'bottom':'0'}); // Ensure it sticks to the bottom.
                	}, 
                	function(){footerBottoms.stop(true, true).delay(2000).slideUp()}
                );
                
            });
          </script>
{/literal}
        <!-- These catchphrases will be displayed randomly. -->
          <span style="display:none">There was going to be a NinjaWars2, but NinjaWars1 stabbed it.</span>
          <span style="display:none">Join a clan, promote multiple stab wounds.</span>
          <span style="display:none">Annoy the Emperor, kill Samurai.</span>
          <span style="display:none">Some theorize that poison is actually liquified ninja.</span>
          <span style="display:none">Helping ninja stab people since 2003.</span>
          <span style="display:none">Fact: Ninja can just click faster.</span>
          <span style="display:none">Always watch for snakes in the high grass.</span>
          <span style="display:none">Some days you find the oni, some days, they find you.</span>
          <span style="display:none">Only the most adventuresome gambler can pay back his own debts.</span>
          <span style="display:none">Before you embark on a journey of revenge, dig two graves. Samurai are fat.</span>
          <span style="display:none">Shall I teach you about knowledge?  Watch out for that shuriken, first.</span>
          <span style="display:none">They are not better than you, it is simply that they are automatons.</span>
          <span>Oni are actually quite friendly, if you get to know them.</span>
        </span>
         |
        <a href="tutorial.php" target="main">Help</a> |
        <a href="staff.php" target="main">Staff</a> |
        <a href="rules.php" target="main">Rules</a> |
        <a href="http://ninjawars.proboards.com/index.cgi?board=ann" target="_blank" class="extLink">News</a> |
        <a href="http://ninjawars.pbwiki.com/" target="_blank" class="extLink">Wiki</a> |
        <a href="http://ninjawars.proboards.com" target="_blank" class="extLink">Forum</a> |
        <a href="http://ninjawars.proboards.com/index.cgi?action=display&board=suggcomp&thread=1174" target="_blank" class="extLink">Feedback</a>
        </div>
