---
layout: post
title: "XHTML-valid new window links"
date: 2009-02-27 09:46

---
<p>	I always have to look this up. Run this on page load and give all 'a' tags the attribute rel="external" to create XHTML valid links that open in a new window. Goodbye target="_blank" 
   

    function externalLinks() {
        if (!document.getElementsByTagName) return; 
        var anchors = document.getElementsByTagName("a"); 
        for (var i=0; i<anchors.length) {
            var anchor = anchors[i]; 
            if (anchor.getAttribute("href") && anchor.getAttribute("rel") == "external") {
               anchor.target = "_blank"; 
            } 
        }
     }     
