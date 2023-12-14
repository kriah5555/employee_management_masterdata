<?php
use Spatie\TranslationLoader\LanguageLine;
use App\Models\User\User;
use Illuminate\Support\Facades\Http;
use App\Models\Tenant;
use Illuminate\Support\Facades\Request;

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
        // $url = 'https://dev.indii-2.0.i-manager.infanion.com/api/create-user';
        try {
            // Build the HTTP request
            $request = Http::withHeaders($headers);

            if ($method === 'GET') {
                $response = $request->get($url);
            } elseif ($method === 'POST') {
                $response = $request->post($url, $data);
            } elseif ($method === 'PUT') {
                $response = $request->put($url, $data);
            } elseif ($method === 'DELETE') {
                $response = $request->delete($url);
            } else {
                // Handle other HTTP methods as needed
                return null;
            }

            // Check if the request was successful (status code 200)
            if ($response->successful()) {
                // Get the JSON response data
                return $response->json();
            } else {
                throw new Exception("API error");
            }
        } catch (\Exception $e) {
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

if (!function_exists('collectionToValueLabelFormat')) {
    function collectionToValueLabelFormat($collection, $valueKey = 'id', $labelKey = 'name')
    {
        return generateValueLabelArray($collection->toArray(), $valueKey, $labelKey);
    }
}


if (!function_exists('generateValueLabelArray')) {
    function generateValueLabelArray($array, $valueKey = 'id', $labelKey = 'name')
    {
        return array_map(function ($item) use ($valueKey, $labelKey) {
            return [
                'value' => $item[$valueKey],
                'label' => $item[$labelKey],
            ];
        }, $array);
    }
}

if (!function_exists('getValueLabelOptionsFromConfig')) {
    function getValueLabelOptionsFromConfig($key)
    {
        $values = config($key);
        return array_map(function ($value, $label) {
            return [
                'value' => $value,
                'label' => $label,
            ];
        }, array_keys($values), $values);
    }
}

if (!function_exists('getKeyNameOptionsFromConfig')) {
    function getKeyNameOptionsFromConfig($key)
    {
        $values = config($key);
        return array_map(function ($value, $label) {
            return [
                'key'  => $value,
                'name' => $label,
            ];
        }, array_keys($values), $values);
    }
}

if (!function_exists('associativeToDictionaryFormat')) {
    function associativeToDictionaryFormat($associativeArray, $valueKey = 'id', $labelKey = 'value')
    {
        $dict = [];
        foreach ($associativeArray as $key => $value) {
            // Your custom function logic here, which can use both $key and $value.
            $dict[] = [
                $valueKey => $key,
                $labelKey => $value,
            ];
        }
        return $dict;
    }
}

if (!function_exists('formatModelName')) {
    function formatModelName($modelName)
    {
        return ucfirst(strtolower(preg_replace('/(?<!^)([A-Z])/', ' $1', $modelName)));
    }
}

if (!function_exists('setTenantDB')) {
    function setTenantDB($tenant_id)
    {
        $tenant_id = empty($tenant_id) ? request()->header('tenant', '') : $tenant_id; # to get tenant id from header
        $tenant = Tenant::find($tenant_id);
        if ($tenant) {
            tenancy()->initialize($tenant);
            config(['database.connections.tenant_template.database' => $tenant->database_name]);
        }
    }
}

if (!function_exists('getActiveTenantId')) {
    function getActiveTenantId()
    {
        return tenancy()->tenant->id;
    }
}

if (!function_exists('getCompanyId')) {
    function getCompanyId()
    {
        return Request::header('Company-Id');
    }
}

if (!function_exists('formatToEuropeCurrency')) { # will convert number format to europe currency format
    function formatToEuropeCurrency($currency)
    {
        // $parts = explode('.', $currency);
        // $decimal = 0;
        // if (count($parts) == 2) {
        //     $decimal = strlen($parts[1]);
        // }

        $currency = str_replace(',', '.', $currency);
        return number_format($currency, 4, ',', '.');
    }
}

if (!function_exists('formatToEuropeHours')) { # will convert number format to europe currency format
    function formatToEuropeHours($currency)
    {
        return str_replace('.', ',', $currency);
    }
}

if (!function_exists('formatToCommonHours')) { # will convert number format to europe currency format
    function formatToCommonHours($currency)
    {
        return str_replace(',', '.', $currency);
    }
}

if (!function_exists('formatToNumber')) { # will convert europe currency format to number format
    function formatToNumber($number)
    {
        return (float) str_replace(['.', ','], ['', '.'], $number);
    }
}

if (!function_exists('replaceTokens')) {
    /**
     * Replace tokens in a string with corresponding values from an associative array.
     *
     * @param string $content The string containing tokens to be replaced.
     * @param array $tokenData Associative array with tokens as keys and values as replacements.
     * @return string The string after replacing tokens with values.
     */
    function replaceTokens($content, $tokenData)
    {
        $configTokens = array_keys(config('tokens.EMPLOYEE_TOKENS'));

        $values = array_values($tokenData);

        return str_replace($configTokens, $values, $content);
    }
}

if (!function_exists('makeTenantFolderPath')) {
    function makeTenantFolderPath($tenantName)
    {
        $tenantPath = storage_path() . '/company/' . $tenantName;
        if (!is_dir($tenantPath)) {
            mkdir($tenantPath, 0777, true);
        }
        return $tenantPath;
    }
}

if (!function_exists('makeTenantDatabaseName')) {
    function makeTenantDatabaseName($companyName, $companyId)
    {
        return 'tenant_' . strtolower(preg_replace('/[^a-zA-Z0-9_]/', '_', $companyName) . '_' . $companyId);
    }
}

if (!function_exists('getTenantFolderPath')) {
    function getTenantFolderPath($tenantName)
    {
        $tenantPath = storage_path() . '/company/' . $tenantName;
        if (!is_dir($tenantPath)) {
            mkdir($tenantPath, 0777, true);
        }
        return $tenantPath;
    }
}

if (!function_exists('getWeekDates')) {
    function getWeekDates($weekNo, $year)
    {
        $dates = [];
        $startDate = new DateTime();
        $startDate->setISODate($year, $weekNo, 1); // Set to the first day of the given week
        for ($i = 0; $i < 7; $i++) {
            $date = $startDate->format('Y-m-d');
            $dates[$date] = $date;
            $startDate->modify('+1 day');
        }
        return $dates;
    }
}

