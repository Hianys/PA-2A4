namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MapController extends Controller
{
public function routeBetweenCities(Request $request)
{
$fromLat = $request->input('from_lat');
$fromLng = $request->input('from_lng');
$toLat = $request->input('to_lat');
$toLng = $request->input('to_lng');

$apiKey = env('ORS_API_KEY');

$url = "https://api.openrouteservice.org/v2/directions/driving-car";

$response = Http::get($url, [
'api_key' => $apiKey,
'start' => "$fromLng,$fromLat",
'end' => "$toLng,$toLat",
]);

return $response->json();
}
}
