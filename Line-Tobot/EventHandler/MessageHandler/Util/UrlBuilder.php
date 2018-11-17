<?php
class UrlBuilder{
    public static function buildUrl(\Slim\Http\Request $req, array $paths)
    {
        $baseUri = $req->getUri()->getBaseUrl();
        foreach ($paths as $path) {
            $baseUri .= '/' . urlencode($path);
        }
        return $baseUri;
    }
}