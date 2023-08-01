<?php
function trim_or_empty($str) {
    if(isset($str) && gettype($str) == "string") return trim($str);
    return "";
}