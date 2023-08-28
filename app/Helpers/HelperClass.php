<?php
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use App\Exceptions;

/**
 * [storeCustomLogs description]
 * @param  array/string  $data  [data to be logged]
 * @param  string        $fileName    [custom file name]
 * @return void          [no return]
 */
function storeCustomLogs($data, $fileName)
{
    Log::build([
        'driver' => 'daily',
        'path'   => storage_path('logs/' . $fileName . '.log'),
    ])->info($data);
}

function strtoLowerWithTrim($value)
{
    return strtolower(str_replace(' ', '', $value));
}


function requestHttpApi($api = '', $method = 'Get', $body = [], $headers = [])
{
    $data = [];
    try {
        $client = new Client();
        $base_uri = env('DRUPAL_API');
        logger($base_uri . $api);
        $request = $client->request($method, $base_uri . $api, [
            'headers' => [
                'Content-Type' => 'application/json'
            ] + $headers,
            'json'    => $body,
        ]);
        $response = $request->getBody()->getContents();
        $data = json_decode($response, true);
    } catch (\GuzzleHttp\Exception\RequestException $e) {
        logger([$e->getMessage()]);
        return ['data' => [], 'status' => 500, 'error' => [$e->getMessage()]];
    }
    return ['data' => $data, 'status' => 200, 'error' => ''];
}

//-----------------------FILES RELATED-------------------------------//

function getFolderPath($folderName = '', $storageOrPublic = 1)
{
    $serverToken = str_replace(['http://', 'https://'], ['', ''], env('APP_URL'));
    $folderName = $folderName ? $folderName : strtotime('now');
    $baseName = $storageOrPublic ? storage_path() . '/app/public' : base_path() . '/public';
    $dirName = $baseName . '/' . strtok($serverToken, '.') . '/' . $folderName;
    if (!is_dir($baseName)) {
        mkdir($baseName);
    }
    if (!is_dir($dirName)) {
        mkdir($dirName, 0777, true);
    }
    return $dirName;
}

function getRelativeFolderPath($folderName = '')
{
    $serverToken = str_replace(['http://', 'https://'], ['', ''], env('APP_URL'));
    $folderName = $folderName ? $folderName : strtotime('now');
    $baseName = '/storage/' . strtok($serverToken, '.');
    return $baseName . '/' . $folderName;
}

//--------------------------------------------------------------------//


function removeDefaultValuesFields($tab_key = 'tab_2', $bbrightFields = '')
{
    $defaultIds = config('copdefaultfields.' . $tab_key);
    $defaultIds = array_keys($defaultIds);
    return array_diff($bbrightFields, $defaultIds, );
}

/**
 * [sortArrayByKey description]
 * @param  [type]  $array               [description]
 * @param  [type]  $key                 [description]
 * @param  integer $order               [1 => acending order, 2=> desceding order]
 * @return [type]         [description]
 */
function sortArrayByKey($array, $key, $order = 1)
{
    usort($array, fn($a, $b) => $a[$key] <=> $b[$key]);
    return $array;
}

function array_sort_by_column(&$arr, $col, $dir = SORT_ASC)
{
    $sort_col = array();
    foreach ($arr as $key => $row) {
        $sort_col[$key] = $row[$col];
    }

    array_multisort($sort_col, $dir, $arr);
}

function return_lat_lng($address)
{
    $lat = $lng = $status = '';
    //$address = preg_replace("/\s+/", "%20", $street . '+' . $housenumber . '+' . $bus . '+' . $city);
    $api_key = 'AIzaSyAMSD-__Ie0dy1gQMGksKTqzOAWlhNc2Ms';
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL            => 'https://maps.googleapis.com/maps/api/geocode/json?address=' . $address . '&key=' . $api_key,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING       => '',
        CURLOPT_MAXREDIRS      => 10,
        CURLOPT_TIMEOUT        => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST  => 'GET',
    )
    );

    $response = curl_exec($curl);
    curl_close($curl);
    $decodedResponse = json_decode($response, TRUE);
    $status = $decodedResponse['status'];
    if ($decodedResponse['status'] == 'OK') {
        $geoData = $decodedResponse['results'][0];
        $lat = $geoData['geometry']['location']['lat'];
        $lng = $geoData['geometry']['location']['lng'];
    }
    return ['lat' => $lat, 'lng' => $lng, 'status' => $status];
}