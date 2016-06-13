/**
* A "share" social bar
*
*
**/

<section id="social" class="media">
            	<a href="http://twitter.com/share?url=$permalink_encoded&amp;text=$the_title" target="_blank" onclick="window.open(this.href,'','menubar=no,toolbar=no,width=800,height=300')">
                <div class="item">
                	<div class="icon" style="background-color:#4b9fe0;background-image:url('/images\/twitter_icn.svg');"></div>
                    <div class="count socialcount" data-source="twitter" data-url="$permalink_encoded">Tweet</div></div></a>
                <a href="https://www.facebook.com/sharer/sharer.php?u=$permalink" target="_blank" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=300,width=600');return false;">
                <div class="item">
                	<div class="icon" style="background-color:#3b5998;background-image:url('/images/facebook_icn.svg');"></div>
                    	<div class="count socialcount" data-source="facebook" data-url="$permalink"></div>Share</div></a>
            </section>