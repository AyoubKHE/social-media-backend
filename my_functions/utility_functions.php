<?php
function dd($value)
{
    var_dump($value);
    die();
}

function buildError(string $error, string $errorFile, int $errorLine): array
{
    return array(
        "Description" => $error,
        "File" => $errorFile,
        "Line" => $errorLine,
    );
}

function validatePath(string $request_uri,string $path_input)
{

    $url_parts = explode("/api/", $request_uri);

    $path = $url_parts[1];

    if ((!empty($_SERVER["QUERY_STRING"]) && $path[strlen($path_input)] !== "?")
        || (empty($_SERVER["QUERY_STRING"]) && $path !== $path_input)
    ) {

        $err = buildError(
            "Invalide path.",
            __FILE__,
            __LINE__
        );

        $response = json_encode(
            array(
                "Errors" => $err
            )
        );

        die($response);
    }
}
