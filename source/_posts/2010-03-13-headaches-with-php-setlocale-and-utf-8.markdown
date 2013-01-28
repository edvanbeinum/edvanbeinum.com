---
layout: post
title: "Headaches with PHP's setlocale() and UTF-8"
date: 2010-03-13 07:36

---
I've been working on a German language site that uses PHP's date() function to output formatted dates in various places. I had set character encoding to UTF-8 - <a href="http://annevankesteren.nl/2009/09/web-character-encodings">the only sensible choice</a>
    header('Content-Type: text/html; charset=UTF-8');

but then using <a href="http://php.net/manual/en/function.setlocale.php">setlocale</a>

    setlocale(LC_ALL, 'de_DE');

rendered month names where an umlaut was necessary with the dreaded <a href="http://en.wikipedia.org/wiki/Replacement_character">�</a> the secret, not explicitly mentioned in the PHP manual was:

    setlocale(LC_ALL, 'de_DE.UTF-8');

If you want the locale to be used in date formatting, use <a href="http://www.php.net/manual/en/function.strftime.php">strfromtime()</a> rather than date().