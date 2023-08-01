<?php
function trim_or_empty($str) {
    if(gettype($str) == "string") return trim($str)
    return ""
}