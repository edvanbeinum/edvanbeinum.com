---
layout: post
title: "Case-insensitive value matching in XPath from PHP"
date: 2010-04-05 07:29

---
This is a clunky workaround, and ideally you should specify your DTD a lot closer if you have any control over it. In one case we had though, we were receiving crappy data from a client where the case of a value could not be predicted.Here is a way to match case-insensitively using XPath's translate method<code>$xml->xpath('//example[translate(@name, \\'ABCDEFGHIJKLMNOPQRSTUVWXYZ\\', <br /> \\'abcdefghijklmnopqrstuvwxyz\\') = \\'strtolower($name)\\']/.');</code>So, the value of the attribute 'name' in the 'example' node is parsed for any uppercase letters and they are replaced with lowercase letters. The match is then carried out as usual against a lower-cased $name.