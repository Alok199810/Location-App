<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function index()
    {
        $mapsKey = config('services.google.maps_key');
        return view('location', compact('mapsKey'));
    }

    public function calculate(Request $request)
    {
        try {
            $pickup    = urlencode($request->pickup);
            $drop      = urlencode($request->drop);
            $matrixKey = env('GOOGLE_MATRIX_KEY');

            $url = "https://maps.googleapis.com/maps/api/distancematrix/json"
                 . "?origins={$pickup}&destinations={$drop}"
                 . "&mode=driving&key={$matrixKey}";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            $response = curl_exec($ch);
            $curlError = curl_error($ch);
            curl_close($ch);

            if ($curlError) {
                return response()->json([
                    'success' => false,
                    'message' => 'Curl error: ' . $curlError
                ]);
            }

            $data = json_decode($response, true);

            if (
                isset($data['rows'][0]['elements'][0]['status']) &&
                $data['rows'][0]['elements'][0]['status'] === 'OK'
            ) {
                return response()->json([
                    'success'  => true,
                    'distance' => $data['rows'][0]['elements'][0]['distance']['text'],
                    'duration' => $data['rows'][0]['elements'][0]['duration']['text'],
                    'pickup'   => $request->pickup,
                    'drop'     => $request->drop,
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'API Error: ' . ($data['status'] ?? 'Unknown error'),
                'raw'     => $data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Exception: ' . $e->getMessage()
            ]);
        }
    }
}