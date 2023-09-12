<?php
use Spatie\TranslationLoader\LanguageLine;
use App\Models\User;
use Exception;

if (!function_exists('returnResponse')) {
    function returnResponse($data, $status_code)
    {
        if (array_key_exists('message', $data)) {
            $data['message'] = is_array($data['message']) ? $data['message'] : [$data['message']];
        }
        return response()->json($data, $status_code);
    }
}

if (!function_exists('hasDuplicates')) {
    function hasDuplicates(array $array): bool
    {
        $seen = [];
        foreach ($array as $number) {
            if (isset($seen[$number])) {
                return true; // Duplicate found
            }
            $seen[$number] = true;
        }
        return false; // No duplicates found
    }
}

if (!function_exists('t')) {
    function t($stringKey, $locale = '')
    {
        // app()->setLocale('nl');

        $locale = !empty($locale) ? $locale : app()->getLocale();
        $translation = LanguageLine::where('key', $stringKey)->first();

        if ($translation && isset($translation->text[$locale])) {
            return $translation->text[$locale] ?? $stringKey;
        } else {
            // If translation doesn't exist create new entry
            $text = [];
            foreach (config('app.available_locales') as $locale) {
                $text[$locale] = $stringKey;
            }

            $translation = LanguageLine::firstOrNew([
                'key' => $stringKey,
            ]);

            $translation->text = $text;
            $translation->group = 'custom';
            $translation->save();

            return $stringKey; // Return the original string if no translation exists
        }
    }
}
if (!function_exists('generateUniqueUsername')) {
    function generateUniqueUsername($username)
    {
        $newUsername = $username;
        $counter = 1;

        while (User::where('username', $newUsername)->exists()) {
            $newUsername = $username . $counter;
            $counter++;
        }

        return $newUsername;
    }
}

if (!function_exists('generateRandomPassword')) {
    function generateRandomPassword($length = 12)
    {
        // Define the characters that can be used in the password
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%&';

        // Get the total number of characters available
        $charCount = strlen($characters);

        // Initialize the password variable
        $password = '';

        // Generate the random password
        for ($i = 0; $i < $length; $i++) {
            // Get a random index within the range of available characters
            $randomIndex = mt_rand(0, $charCount - 1);

            // Append the randomly selected character to the password
            $password .= $characters[$randomIndex];
        }

        return $password;
    }
}

if (!function_exists('makeApiRequest')) {
    function makeApiRequest($url, $method = 'GET', $data = [], $headers = [])
    {
        $client = new \GuzzleHttp\Client();

        $options = [
            'json'    => $data,
            'headers' => $headers,
        ];

        $response = $client->request($method, $url, $options);
        if ($response->getStatusCode() == 200 || $response->getStatusCode() == 201) {
            return json_decode($response->getBody(), true);
        } else {
            throw new Exception("API error");
        }
    }
}

if (!function_exists('microserviceRequest')) {
    function microserviceRequest($route, $method = 'GET', $data = [], $headers = [])
    {
        $apiGatewayUrl = config('app.service_gateway_url');
        $url = $apiGatewayUrl . $route;
        return makeApiRequest($url, $method, $data, $headers);
    }
}
