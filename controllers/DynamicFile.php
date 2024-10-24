<?php



namespace App\Controllers;



use \Solenoid\Core\MVC\Controller;

use \Solenoid\HTTP\Request;



class DynamicFile extends Controller
{
    # Returns [void]
    public function get ()
    {
        switch ( Request::fetch()->url->path )
        {
            case '/robots.txt':
                // (Getting the value)
                $base_url = Request::fetch()->url->fetch_base();



                // (Setting the header)
                header('Content-Type: text/plain');

                // Printing the value
                echo
                    <<<EOD
                    User-agent: MagiBot
                    Allow: /

                    User-agent: *
                    Disallow: /

                    Sitemap: $base_url/sitemap.xml
                    EOD
                ;
            break;

            case '/sitemap.xml':
                // (Setting the header)
                header('Content-Type: application/xml');

                // Printing the value
                echo
                    <<<EOD
                    <?xml version="1.0" encoding="UTF-8"?>

                    <urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">

                    <url>
                        <loc>http://www.example.com/</loc>
                        <lastmod>2005-01-01</lastmod>
                    </url>

                    </urlset> 
                    EOD
                ;
            break;
        }
    }
}



?>