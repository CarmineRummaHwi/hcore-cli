<?php


namespace hcore\cli;


class HtaccessManager
{
    public static $instance;

    private $htaccess;
    private $htaccess_path;

    private $use_www;
    private $use_https;
    private $resource_path;

    /**
     * @param string $htaccess_path
     * @return HtaccessManager|null
     */
    public static function getInstance(string $htaccess_path) {
        if(file_exists($htaccess_path)){
            if(!self::$instance) {
                self::$instance = new HtaccessManager();
                self::$instance->resource_path = dirname(dirname(__DIR__)) . "/resources";
            }
            self::$instance->htaccess_path = $htaccess_path;
            self::$instance->htaccess = file_get_contents($htaccess_path);
            return self::$instance;
        }else{
            return null;
        }
    }

    public function read() : string {
        return $this->htaccess;
    }

    /**
     * @return self
     */
    public function enableHttpRedirect() : self{
        $pattern = '/(?<=### BEGIN http-redirect)(?s)(.*?)(?=### END http-redirect)/m';
        $replace = <<<RULES
\nRewriteCond %{HTTP_HOST} !^www\.
RewriteRule ^(.*)$ http://www.%{HTTP_HOST}/$1 [R=301,L]\n
RULES;
        $this->htaccess = Utilities::replaceMatch($pattern, $this->htaccess, $replace);
        $this->use_www = true;
        return $this;
    }

    /**
     * @return self
     */
    public function disableHttpRedirect() : self {
        $pattern = '/(?<=### BEGIN http-redirect)(?s)(.*?)(?=### END http-redirect)/m';
        $replace = <<<RULES
\n
RULES;
        $this->htaccess = Utilities::replaceMatch($pattern, $this->htaccess, $replace);
        $this->use_www = false;
        return $this;
    }

    /**
     * @return self
     */
    public function enableHttpsRedirect() : self{
        $pattern = '/(?<=### BEGIN https-redirect)(?s)(.*?)(?=### END https-redirect)/m';
        $prefix = $this->use_www ? "www." : "";
        $replace = <<<RULES
\nRewriteCond %{HTTPS} =off [OR]
RewriteCond %{HTTP_HOST} !^www\. [OR]
RewriteCond %{THE_REQUEST} ^[A-Z]{3,9}\ /index\.(html|php)
RewriteCond %{HTTP_HOST} ^(www\.)?(.+)$
RewriteRule ^(index\.(html|php))|(.*)$ https://{$prefix}%2/$3 [R=301,L]\n
RULES;
        $this->htaccess = Utilities::replaceMatch($pattern, $this->htaccess, $replace);
        $this->use_https = true;
        return $this;
    }

    /**
     * @return self
     */
    public function disableHttpsRedirect() : self {
        $pattern = '/(?<=### BEGIN https-redirect)(?s)(.*?)(?=### END https-redirect)/m';
        $replace = <<<RULES
\n
RULES;
        $this->htaccess = Utilities::replaceMatch($pattern, $this->htaccess, $replace);
        $this->use_https = true;
        return $this;
    }

    public function addToBanlist(string $referer) : void {
        $pattern = '/(?<=### BEGIN security-banlist)(?s)(.*?)(?=### END security-banlist)/m';
        $base_banlist = trim(Utilities::getMatch($pattern, $this->htaccess));

        if(false !== strpos($base_banlist, "RewriteCond %{HTTP_REFERER} {$referer} [NC")){
            return;
        }

        if(strlen($base_banlist) == 0){
            $base_banlist = file_get_contents($this->resource_path . "/htaccess-security-banlist");
            $banlist_item = "\n\tRewriteCond %{HTTP_REFERER} {$referer} [NC]\n\t";
            $subpattern = '/(?<=### BEGIN banlist-items)(?s)(.*?)(?=### END banlist-items)/m';
            $base_banlist = Utilities::replaceMatch($subpattern, $base_banlist, $banlist_item);
        }else{
            $subpattern = '/(?<=### BEGIN banlist-items)(?s)(.*?)(?=### END banlist-items)/m';
            $current_list = Utilities::getMatch($subpattern, $base_banlist);
            if(false !== $index = strrpos($current_list, "[NC]")) {
                $current_list = substr_replace($current_list, "[NC,OR]", $index, 4);
                $current_list .= "RewriteCond %{HTTP_REFERER} {$referer} [NC]\n\t";
                $subpattern = '/(?<=### BEGIN banlist-items)(?s)(.*?)(?=### END banlist-items)/m';
                $base_banlist = "\n".Utilities::replaceMatch($subpattern, $base_banlist, $current_list)."\n";
            }
        }
        $this->htaccess = Utilities::replaceMatch($pattern, $this->htaccess, $base_banlist);
    }

    public function removeFromBanlist(string $referer) : void {
        $subpattern = '/(?<=### BEGIN banlist-items)(?s)(.*?)(?=### END banlist-items)/m';
        $current_list = Utilities::getMatch($subpattern, $this->htaccess);

        $item_or = "RewriteCond %{HTTP_REFERER} {$referer} [NC,OR]";
        $item_end = "RewriteCond %{HTTP_REFERER} {$referer} [NC]";
        if(false !== $index = strpos($current_list, $item_or)) {
            $current_list = substr_replace($current_list, "", $index, strlen($item_or));
        }else if (false !== $index = strpos($current_list, $item_end)) {
            $current_list = substr_replace($current_list, "", $index, strlen($item_end));
            if(false !== $index = strrpos($current_list, "[NC,OR]")) {
                $current_list = substr_replace($current_list, "[NC]", $index, 8);
                $this->htaccess = Utilities::replaceMatch($subpattern, $this->htaccess, $current_list);
            }
        }
    }

    public function enableSecurityCleaner() : void{
        $pattern = '/(?<=### BEGIN security-cleaner)(?s)(.*?)(?=### END security-cleaner)/m';
        $replace = "\n".trim(file_get_contents($this->resource_path . "/htaccess-security-cleaner"))."\n";
        $this->htaccess = Utilities::replaceMatch($pattern, $this->htaccess, $replace);
    }

    public function disableSecurityCleaner() : void{
        $pattern = '/(?<=### BEGIN security-cleaner)(?s)(.*?)(?=### END security-cleaner)/m';
        $replace = <<<RULES
\n
RULES;
        $this->htaccess = Utilities::replaceMatch($pattern, $this->htaccess, $replace);
    }

    public function enableHtaccessCache() : void{
        $pattern = '/(?<=### BEGIN htaccess-cache)(?s)(.*?)(?=### END htaccess-cache)/m';
        $replace = file_get_contents($this->resource_path . "/htaccess-cache");
        $this->htaccess = Utilities::replaceMatch($pattern, $this->htaccess, $replace);
    }

    public function disableHtaccessCache() : void{
        $pattern = '/(?<=### BEGIN htaccess-cache)(?s)(.*?)(?=### END htaccess-cache)/m';
        $replace = <<<RULES
\n
RULES;
        $this->htaccess = Utilities::replaceMatch($pattern, $this->htaccess, $replace);
    }

    public function enableHtaccessSecurity() : void{
        $pattern = '/(?<=### BEGIN htaccess-security)(?s)(.*?)(?=### END htaccess-security)/m';
        $replace = "\n".trim(file_get_contents($this->resource_path . "/htaccess-security"))."\n";
        $this->htaccess = Utilities::replaceMatch($pattern, $this->htaccess, $replace);
    }

    public function disableHtaccessSecurity() : void{
        $pattern = '/(?<=### BEGIN htaccess-security)(?s)(.*?)(?=### END htaccess-security)/m';
        $replace = <<<RULES
\n
RULES;
        $this->htaccess = Utilities::replaceMatch($pattern, $this->htaccess, $replace);
    }

    public function enableHtaccessSecurityAdvanced() : void{
        $pattern = '/(?<=### BEGIN security-advanced)(?s)(.*?)(?=### END security-advanced)/m';
        $replace = "\n".trim(file_get_contents($this->resource_path . "/htaccess-security-advanced"))."\n";
        $this->htaccess = Utilities::replaceMatch($pattern, $this->htaccess, $replace);
    }

    public function disableHtaccessSecurityAdvanced() : void{
        $pattern = '/(?<=### BEGIN security-advanced)(?s)(.*?)(?=### END security-advanced)/m';
        $replace = <<<RULES
\n
RULES;
        $this->htaccess = Utilities::replaceMatch($pattern, $this->htaccess, $replace);
    }

    public function addCorsRule($domain) : void{
        $pattern = '/(?<=### BEGIN security-cors)(?s)(.*?)(?=### END security-cors)/m';
        $cors_string = trim(Utilities::getMatch($pattern, $this->htaccess));
        if(strlen($cors_string) == 0){
            $cors_string = "\nHeader add Access-Control-Allow-Origin '".$domain."'\n";
            $this->htaccess = Utilities::replaceMatch($pattern, $this->htaccess, $cors_string);
        }else{
            $subpattern = '/(?<=\nHeader add Access-Control-Allow-Origin \')(?s)(.*?)(?=\'\n)/m';
            $domains = explode("|", trim(Utilities::getMatch($subpattern, $this->htaccess)));
            if(!in_array($domain, $domains)){
                $domains[] = $domain;
                $cors_string = "\nHeader add Access-Control-Allow-Origin '".implode("|", $domains)."'\n";
                $this->htaccess = Utilities::replaceMatch($pattern, $this->htaccess, $cors_string);
            }
        }
    }

    public function removeCorsRule($domain) : void{
        $pattern = '/(?<=### BEGIN security-cors)(?s)(.*?)(?=### END security-cors)/m';
        $cors_string = trim(Utilities::getMatch($pattern, $this->htaccess));
        if(strlen($cors_string) > 0){
            $subpattern = '/(?<=\nHeader add Access-Control-Allow-Origin \')(?s)(.*?)(?=\'\n)/m';
            $domains = explode("|", trim(Utilities::getMatch($subpattern, $this->htaccess)));
            if (false !== $key = array_search($domain, $domains)) {
                unset($domains[$key]);
                if(sizeof($domains) > 0) {
                    $cors_string = "\nHeader add Access-Control-Allow-Origin '" . implode("|", $domains) . "'\n";
                }else{
                    $cors_string = "\n";
                }
                $this->htaccess = Utilities::replaceMatch($pattern, $this->htaccess, $cors_string);
            }
        }
    }

    public function addXFrameOptions($domain) : void{
        $pattern = '/(?<=### BEGIN x-frame-options)(?s)(.*?)(?=### END x-frame-options)/m';
        $base_xframe = trim(Utilities::getMatch($pattern, $this->htaccess));

        if(strlen($base_xframe) == 0){
            $base_xframe = "\n".trim(file_get_contents($this->resource_path . "/htaccess-x-frame-options"))."\n";
        }

        $subpattern = '/(?<=### BEGIN x-frame-items)(?s)(.*?)(?=### END x-frame-items)/m';
        $optionlist = trim(Utilities::getMatch($subpattern, $base_xframe));

        $xframe_item = "\n\tHeader always set X-Frame-Options {$domain}";
        if(strlen($optionlist) == 0){
            $optionlist .= $xframe_item;
        }else{
            if(false !== strpos($optionlist, "Header always set X-Frame-Options {$domain}")){
                return;
            }
            $optionlist .= $xframe_item;
        }

        $base_xframe = Utilities::replaceMatch($subpattern, $base_xframe, "\n\t".$optionlist."\n\t");
        $this->htaccess = Utilities::replaceMatch($pattern, $this->htaccess, "\n".$base_xframe."\n");
    }

    public function removeXFrameOptions($domain) : void{
        $pattern = '/(?<=### BEGIN x-frame-options)(?s)(.*?)(?=### END x-frame-options)/m';
        $base_xframe = trim(Utilities::getMatch($pattern, $this->htaccess));

        $subpattern = '/(?<=### BEGIN x-frame-items)(?s)(.*?)(?=### END x-frame-items)/m';
        $optionlist = trim(Utilities::getMatch($subpattern, $base_xframe));

        if(strlen($optionlist) == 0) {
            return;
        }

        $optionlist = str_replace("\tHeader always set X-Frame-Options {$domain}\n", "", $optionlist);
    }

    public function enableSecurityPolicy() : void{
        $pattern = '/(?<=### BEGIN security-policy)(?s)(.*?)(?=### END security-policy)/m';
        $replace = "\n".trim(file_get_contents($this->resource_path . "/htaccess-content-security-policy"))."\n";
        $this->htaccess = Utilities::replaceMatch($pattern, $this->htaccess, $replace);
    }

    public function disableSecurityPolicy() : void{
        $pattern = '/(?<=### BEGIN security-policy)(?s)(.*?)(?=### END security-policy)/m';
        $replace = "\n";
        $this->htaccess = Utilities::replaceMatch($pattern, $this->htaccess, $replace);
    }

    public function save() : void{
        file_put_contents($this->htaccess_path, $this->htaccess);
    }
}
