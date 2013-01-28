---
layout: post
title: "Using dashes in XML tags and attributes?"
date: 2009-10-15 07:50

---
I came across an XML file that had dashed attribute names:

    <node attribute-name="dashed" /> 

This makes PHP's parser a bit unhappy if you try and access the attribute using:

    $xml->attribute()->attribute-name

Fix it with some curly brackets: 

    $node->attribute()->{'attribute-name'}