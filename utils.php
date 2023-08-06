<?php
function trim_or_empty($str) {
    if(gettype($str) == "string") return trim($str);
    return "";
}

function starts_with($str, $target) {
    $b = strlen($str);
    $a = strlen($target);
    if($b < $a) return false;
    return substr($str, 0, $a) == $target;
}

function div_editable_remove($body) {
    $body_spl = explode('<', $body);
    $body = "";
    $end = count($body_spl);
    for($i = 0; $i < $end; $i++) {
        $tmp = $body_spl[$i];
        if($i > 0) {
            if(starts_with($tmp, "div>") || starts_with($tmp, "/div>"))
                $body = $body."<";
            else {
                $body = $body."&lt;";
                $tmp = str_replace('>', '&gt;', $tmp);
            }
        }
        $body = $body.$tmp;
    }
    return $body;
}