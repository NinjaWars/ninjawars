    {$header}
    
    
    <!-- Version {$version} -->

    <div id='content'>
      <div id='left-column'>
            
        <div id='ninjawars-home' class='header-section'>
            <div id='logged-in-bar'>
                <div>
                  <a target="main" href="player.php?player={$user_id}">{$username}</a>
                </div>
                <div>
                  <a target="main" href="messages.php">mailbox</a>
                </div>
                <div>
                  <span id='logged-in-bar-health'> </span>
                </div>

            </div>
        </div>      
      
          <div id="quick-stats" class="boxes">
            <div class="box-title centered">
              <a href="#" class="show-hide-link" onclick="toggle_visibility('quickstats-and-switch-stats');">
                Quick Stats <img class="show-hide-icon" src="images/show_and_hide.png" alt="+/-">
              </a>
            </div>
            <div id="quickstats-and-switch-stats">
              <div class="centered quickstats-container">
                <a href="quickstats.php" target="quickstats">Player</a> 
                | <a href="quickstats.php?command=viewinv" target="quickstats">Inventory</a>
              </div>
              <div id="quickstats-frame-container">
                <iframe id="quickstats" src="quickstats.php" name="quickstats">
                  Quick Stats Iframe Display section (Iframes Not supported by this browser)
                </iframe>
              </div>
            </div><!-- End of quickstats and switch container -->
          </div><!-- End of quickstats section. -->
          
          <div id="actions" class="boxes active">
            <div class="box-title">
              <a href="#" class="show-hide-link" onclick="toggle_visibility('actions-menu');">
                Actions <img class="show-hide-icon" src="{$IMAGE_ROOT}show_and_hide.png" alt="+/-">
              </a>
            </div>
            <ul class="basemenu" id="actions-menu">
              <li>
                <ul class="submenu">
                  <li>
                    <a href="inventory_mod.php?item=Speed%20Scroll&amp;selfTarget=1&amp;link_back=inventory" 
                    target="main">Speed</a>
                  </li>
                  <li>
                    <a href="inventory_mod.php?item=Stealth%20Scroll&amp;selfTarget=1&amp;link_back=inventory"
                     target="main">Stealth</a>
                  </li>
                </ul>
              </li>
              <li id='heal-link'><a href="shrine_mod.php?heal_and_resurrect=1" target="main">Heal</a></li>
            </ul>
          </div>
        
          <div id='vicious-killer' class='boxes'>
            <div class='box-title'>
              <a href='#' class='show-hide-link' onclick="toggle_visibility('vicious-killer-menu');">
                Fast Killer:<img class='show-hide-icon' src='{$IMAGE_ROOT}show_and_hide.png' alt='+/-'>
              </a>
            </div>
            <a id='vicious-killer-menu' href='player.php?player={$vicious_killer}' target='main'>{$vicious_killer}</a>
          </div><!-- End of vicious killer div -->

          <div id="music" class="boxes passive">
            <div class="box-title">
              <a href="#" class="show-hide-link music" onclick="toggle_visibility('music-player');">
                Music <img class="show-hide-icon" src="{$IMAGE_ROOT}show_and_hide.png" alt="+/-">
              </a>
            </div>

            <object type="audio/x-midi" data="{$WEB_ROOT}music/samsho.mid" id="music-player">
              <param name="src" value="{$WEB_ROOT}music/samsho.mid">
              <param name="autoplay" value="true">
              <param name="autoStart" value="0">
              <a href="{$WEB_ROOT}music/samsho.mid">
                Play <img class="play-button" src="{$IMAGE_ROOT}bullet_triangle_green.png" alt="&gt;">
              </a>
            </object>
          </div>
          
          <!-- Recent Events & Recent Mail will get put in here via javascript -->
          <div id='recent-events'></div>
          <div id='recent-mail'></div>

      </div>  
      
      
      
      <!-- CENTRAL COLUMN STARTS HERE -->
      
      
      
      <div id='center-column'>

      
      <div id='menu-bar' class='header-section'>
        <div id='reactive-panel'>

            <div id='category-bar'>
              <ul>
                <li id='status-actions'>
                  <img src='/images/ninja_silhouette_50px.png' alt=''>
                  <a href='events.php' target='main'>Status</a>
                </li>
                <li id='combat-actions'>
                  <img src='/images/50pxShuriken.png' alt=''>
                  <a href='enemies.php' target='main'>Combat</a>
                </li>
                <li id='village-actions'>
                  <img src='/images/pagodaIcon_60px.png' alt=''>
                  <a href='attack_player.php' target='main'>Village</a>
                </li>
              </ul>
            </div>
            <div id='subcategory-bar'>
                <ul id='self-subcategory'>
                  <li><a href="stats.php" target="main">Stats</a></li>
                  <li><a href="skills.php" target="main">Skills</a></li>
                  <li><a href="messages.php" target="main">Mail</a></li>
                  <li><a href="inventory.php" target="main">Items</a></li>
                  <!-- Profile -->
                  <!-- Settings -->
                </ul>
                <ul id='combat-subcategory'>
                  <li><a href="list_all_players.php" target="main">Ninjas</a></li>
                  <li><a href="clan.php" target="main">Clan</a></li>
                  <li><a href="duel.php" target="main">Duels</a></li>
                </ul>
                <ul id='village-subcategory'>
                  <li><a href="shop.php" target="main">Shop</a></li>
                  <li><a href="work.php" target="main">Work</a></li>
                  <li><a href="doshin_office.php" target="main">Doshin <img src="images/doshin.png" alt=""></a></li>
                </ul>
            </div>
        </div>
        
      </div><!-- End of menu-bar -->


          <div id="main-frame-container"><!-- THE MAIN CONTENT DISPLAY SECTION -->
            <iframe id="main" name="main" class="main-iframe" src="{$main_src}">
              Main Content Display Section (Frames Not Supported)
            </iframe>
          </div><!-- End of mainFrame div -->
          
      </div> <!-- End of center-column -->




      <!-- RIGHTMOST COLUMN STARTS HERE -->


      <div id='right-column'>
      
      
        <div id='ninja-stats' class='header-section'>
        
        <span id='logout'>
            <a href="index.php?logout=true">LOGOUT <img class="logout-stop" src="{$IMAGE_ROOT}icons/stop.png" alt=""></a>
        </span>

          <div id='ninja-count-menu' class='boxes passive'>
            <a href="list_all_players.php" target="main">
              <span id='nin1'>Ni</span><span id='nin2'>nj</span><span id='nin3'>as</span> 
              <img src="images/smallArrows.png" alt="&gt;&gt;&gt;">
            </a>
            {$players_online} Online / {$player_count}
          </div>
        
        <div id="ninja-search" class="boxes active">
            <form id="player_search" action="list_all_players.php" target="main" method="get" name="player_search">
              <div>
                Find A Ninja:
                <input id="searched" type="text" maxlength="50" name="searched" class="textField">
                <input id="hide" type="hidden" name="hide" value="dead">
                <input type="submit" value="find" class="formButton">
              </div>
            </form>
          </div>
        
        </div><!-- End of ninja-stats -->
        
        
          <div id='index-chat'>
              <div id="village-chat" class="boxes active">
                <div class="box-title centered">
                  <a href="#" class="show-hide-link" onclick="toggle_visibility('chat-and-switch');">
                    Chat <img class="show-hide-icon" src="images/show_and_hide.png" alt="+/-">
                  </a>
                </div>
                <div id="chat-and-switch">
                  <div class="chat-switch centered">
                    <a href="village.php" target="main">Full Chat <img src="images/chat.png" alt=""> </a>
                    <a href="mini_chat.php?chat_length=20" target="mini_chat">Refresh</a>
                  </div>
    <!-- TODO: move chat submit box out here. -->
                  <div id="mini-chat-frame-container" class='chat-collapsed'>
                    <iframe id="mini_chat" name="mini_chat" src="mini_chat.php">
                      Mini Chat Iframe Display Section (Iframes not supported by this browser)
                    </iframe>
                  </div>
                  <div id="expand-chat">
                    <a href="mini_chat.php?chatlength=360" target="mini_chat">
                      View more messages <img class="show-hide-icon" src="images/show_and_hide.png" alt="+/-">
                    </a>
                  </div>
                </div>
              </div>
          </div> <!-- End of index-chat --> 

          <div id='ooc-links'>
            
          <div id="links" class="boxes passive">
            <div class="box-title">
              <a href="#" class="show-hide-link links-menu" onclick="toggle_visibility('links-menu');">
                Out of Character <img class="show-hide-icon" src="{$IMAGE_ROOT}show_and_hide.png" alt="+/-">
              </a>
            </div>
            <div id='links-menu'>
            <ul>
              <li><a href="about.php" target="main">Tutorial</a></li>
              <li><a href="duel.php" target="main">Duels</a></li>
              <!--  <a href="vote.php" target="main">Vote For NW </a>  -->
              <li>
                <a href="http://ninjawars.proboards19.com/index.cgi?action=calendar" target="_blank" class="extLink">
                  Calendar <img class="extLink" src="{$IMAGE_ROOT}externalLinkGraphic.gif" alt=""></a>
              </li>
              <li>
                  <a href="http://ninjawars.pbwiki.com/" target="_blank" class="extLink">Wiki</a> <img class="extLink" src="{$IMAGE_ROOT}externalLinkGraphic.gif" alt="">
              </li>
              <li>
                
              </li>
            </ul>
          </div><!-- End of links-menu -->
            
          </div>

            
          </div><!-- End of ooc-links -->
          
          
          
      </div> <!-- End of right column -->
      
      <div id='index-footer'>
<!-- TODO: make this absolute, floating at the page bottom as per facebook's bar. -->
        <!-- Substitute dynamic "catchphrases" here eventually -->
        "There was going to be a NinjaWars2, but NinjaWars1 stabbed it." |
        <a href="tutorial.php" target="main">Intro</a> |
        <a href="rules.php" target="main">Rules</a> |
        <a href='staff.php' target='main'>Staff</a> |
          <a href="http://ninjawars.proboards19.com/index.cgi?board=ann" target="_blank" class="extLink">News</a> 
             <img class="extLink" src="{$IMAGE_ROOT}externalLinkGraphic.gif" alt=""> |
        <a href="http://ninjawars.proboards19.com" target="_blank" class="extLink">Forum</a> 
          <img class="extLink"  src="{$IMAGE_ROOT}externalLinkGraphic.gif" alt="">
             
      </div>
      
    </div> <!-- End of content div -->
    
<!-- Validated as of Oct, 2009 -->

    <!-- Version: {$version} -->

  </body>
</html>
