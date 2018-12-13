<!DOCTYPE html>

<html>
    <head>

    </head>
    <body>
        <?php
            function title_asc($a, $b)
            { return strcmp($a->title, $b->title); }
            
            function title_desc($a, $b)
            { return strcmp($b->title, $a->title); }
            
            
            function price_asc($a, $b)
            {
                if (floatval($a->price) < floatval($b->price))
                { return -1; }
                else if (floatval($a->price)  > floatval($b->price))
                { return 1; }
                else
                { return 0; }
                
            }
            
            function price_desc($a, $b)
            {
                if (floatval($a->price) < floatval($b->price))
                { return 1; }
                else if (floatval($a->price) > floatval($b->price))
                { return -1; }
                else
                { return 0; }
            }            
            ?>     
        
    </body>
</html>
