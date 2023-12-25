# Divi Page Break Navigation `<!--nextpage-->`
It's been years, as far as I searching the forum, since 2014, Divi Theme Builder break the WordPress default module for split post, which is using the `<!--nextpage-->`.

This code snippet simply show the splitted post navigation to appear in a code module via Divi Theme Builder so the viewers can navigate to the splitted post that does not appear automatically in Divi.

# How To Use?
1. Install WP Code Snippet.
2. Add new custom snippet.
4. Copy the whole code from [pagebreak.php](/Divi%20Page%20Break/pagebreak.php).
4. Choose `Universal Snippet`.
5. Choose insert method, short code.
6. Copy the short code, and paste into code module inside Divi Theme Builder for All Post, (single post page).
7. It is best to put it before or after `Post Content` module.
8. Please note, that since this is a code snippet, you cannot insert the same code module twice.
9. If you want to insert it twice, change the function name `divi_is_paginated_post` into `divi_is_paginated_post2` and so on.

Since 25rd December 2023.