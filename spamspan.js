/*
	--------------------------------------------------------------------------
	(c) 2007 Lawrence Akka
	 - jquery version of the spamspan code (c) 2006 SpamSpan (www.spamspan.com)

	This program is distributed under the terms of the GNU General Public
	Licence version 2, available at http://www.gnu.org/licenses/gpl.txt
	--------------------------------------------------------------------------
*/


// load SpamSpan
if (Drupal.jsEnabled) {
   $(function () {

// get each span with class spamSpanMainClass

       $("span." + spamSpanMainClass).each(function (index) {
// for each such span, set mail to the relevant value, removing spaces	
	    var mail = ($("span." + spamSpanUserClass, this).text() + 
	    	"@" + 
	    	$("span." + spamSpanDomainClass, this).text())
	    	.replace(/\s+/g, '')
	    	.replace(/[\[\(\{]?[dD][oO0][tT][\}\)\]]?/g, '.');		
	    var anchorText = $("span" +  spamSpanAnchorTextClass, this).text();
// create the <a> element, and replace the original span contents
   	    $(this).after(
		$("<a></a>")
		.attr("href", "mailto:" + mail)
		.html(anchorText ? anchorText : mail)
		.addClass("spamspan")
		).remove();
	} );
    } )
}
