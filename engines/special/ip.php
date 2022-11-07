<?php
function ip_result(): array
{
    return array(
        "special_response" => array(
            "response" => $_SERVER["REMOTE_ADDR"],
            "source" => null
        )
    );
}