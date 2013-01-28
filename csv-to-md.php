<?php

$h = fopen('habari__posts.csv', 'r');

while(($row = fgetcsv($h)) !== FALSE) {
  $template = "---
layout: post
title: \"%s\"
date: %s

---
%s";


    $post = sprintf($template, $row[3], strftime("%F %R", $row[9]), $row[5]);
    file_put_contents(strftime('source/_posts/' . "%F", $row[9]) . '-' . $row[1] . '.markdown', $post);
    echo "Written $row[1] to file";
}
