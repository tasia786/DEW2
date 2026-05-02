<?php
class Response
{
    public static function sendData(array $data)
    {
        if (isset($_GET['output']) && $_GET['output'] == "csv") {
            self::csv($data);
        } else {
            self::json($data);
        }
    }

    public static function json(mixed $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        //header('Access-Control-Allow-Origin: *'); // necesar pentru AJAX
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    public static function csv(mixed $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: text/csv; charset=utf-8');
        header("Content-Disposition: attachment; filename=download.csv");

        $file = fopen('php://output', 'w');

        if (!empty($data)) {
            fputcsv($file, array_keys($data[0]), escape:"\\");

            foreach ($data as $dataElement) {
                fputcsv($file, array_values($dataElement), escape:"\\");
            }
        }

        fclose($file);
        exit;
    }

    public static function error(string $message, int $status = 500): void
    {
        self::json(['error' => $message], $status);
    }

    public static function badRequest(string $message, int $status = 400): void
    {
        self::json(['bad_request' => $message], $status);
    }
}
