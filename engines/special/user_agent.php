<?php
function user_agent_result(): array
{
    return array(
        "special_response" => array(
            "response" => $_SERVER["HTTP_USER_AGENT"],
            "source" => null
        )
    );
}
