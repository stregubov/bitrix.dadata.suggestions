<?php


namespace Itech\Location;


final class DaDataSuggestions
{
    private const URL = "https://suggestions.dadata.ru/suggestions/api/4_1/rs/suggest/address";
    private $token;
    private static $instance;

    private function __construct()
    {
        $this->token = "my-token";
    }

    public static function getInstance(): DaDataSuggestions
    {
        if (static::$instance === null) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    public function __clone()
    {
    }

    public function __wakeup()
    {
    }

    public function getSuggestions(string $q, string $restrictionType, string $cityFiasId = null): ?object
    {
        $restrictions = [
            "city" => [
                "from_bound" => [
                    "value" => "city",
                ],
                "to_bound" => [
                    "value" => "city",
                ],
            ],
            "street" => [
                "from_bound" => [
                    "value" => "street",
                ],
                "to_bound" => [
                    "value" => "street",
                ],
            ]
        ];

        $ch = curl_init(self::URL);
        $body = [
            "query" => $q,
            "locations" => [
                ["region_iso_code" => "UA-43"],
                ["region_iso_code" => "UA-40"]
            ],
            "restrict_value" => true
        ];

        if ($restrictionType == "street") {
            $body["locations"]["city_fias_id"] = $cityFiasId;
        }
        $body = array_merge($body, $restrictions[$restrictionType]);

        $payload = json_encode($body);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            "Accept: application/json",
            "Authorization: Token $this->token"
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);

        return json_decode($result);
    }
}
