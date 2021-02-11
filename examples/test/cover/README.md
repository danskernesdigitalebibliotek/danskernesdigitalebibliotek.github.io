# Default covers 
## Using provider logo and text overlay
> 2021-02-10T16:47:52 / Erik Bachmann

IF a cover is not provided by Cover Service 
THEN try to build a generic cover based on  provider logo with title / creator and providor info as a text overlay

Logos must be generic and build before loading [See: /logo](/logo) with uniq ID's like ISSN or source (acSource).

In this example simple provider logos (300x300px) are used. 

Meta data and provider are matched using:
1. 'isPartOfISSN' - on article from a journal
1. 'identifierISSN' - on the jounal itself
1. 'acSource' - on reference to webpage

On no match a default background image (Here maked "Uden forside") is displayed.

A prebuild example is in [articles.html](articles.html) build using a [PHP script](articles.php) and simple [CSS-styling](mystyle.css)

Run to rebuild:
```
php articles.php >articles.html
```
or simply use output from `articles.php`.