---
layout: post
title: "Using XPath with Firebug"
date: 2009-02-24 16:56

---
I'd been scratching my head all afternoon trying to work out why using the 'Copy to XPath' feature of Firebug on an element was not returning any results when used with PHP's DOMXPath->query.

It was because Firebug puts in 'tbody' in the elements where DOMXPath doesn't like them. So strip out the tbodys in the XPath path and you should be set. There also seems to be a slight discrepancy in the the numbering of the final element if there are multiple elements. eg: 

     /table/tr/td/p/span[2]

That wouldn't target the element I intended - but if I changed it to 

     /table/tr/td/p/span[1]

Then that seemed to work. I haven't found out a logical method to this yet, but it may be something to try if XPath seems to return odd results