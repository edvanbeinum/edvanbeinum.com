---
layout: post
title: "Concatenate all files in a folder into a single text file"
date: 2009-05-29 07:56

---
In Linux, assuming xargs is available:

    (ls | xargs cat) > newfilename.txt