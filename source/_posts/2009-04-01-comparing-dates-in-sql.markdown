---
layout: post
title: "Comparing dates in SQL"
date: 2009-04-01 06:39

---
Just a quick code snippet that returns all rows whose date field is older than two days ago

    SELECT * FROM table_name WHERE date_column < DATE_SUB(CURDATE(); INTERVAL 2 DAY)