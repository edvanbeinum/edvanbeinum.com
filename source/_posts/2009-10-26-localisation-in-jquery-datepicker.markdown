---
layout: post
title: "Localisation in jQuery Datepicker"
date: 2009-10-26 12:21

---
I just lost 2 hours of my life trying to work out why the 'localize calendar' example in the <a href="http://jqueryui.com/demos/datepicker/#localization">jQueryUI Datepicker docs</a> wasn't working for me. I eventually found that you need to include some extra localisation files which are completely <strong>undocumented</strong>. I was working on a German version and I needed: 

    <script type="text/javascript" src="http://jqueryui.com/ui/i18n/ui.datepicker-de.js"></script>

View Source on this page http://jqueryui.com/demos/datepicker/localization.html for the full details.</p>