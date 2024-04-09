<?php

namespace App\Http\Controllers;

use App\Exports\SearchResultsExport;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ValueSERPController extends Controller
{
    public function index()
    {
        return view('search');
    }

    public function search(Request $request)
    {
        //     $queries = $request->input('queries');

        //     $results = [];
        //     foreach ($queries as $query) {
        //         $results[$query] = $this->getSearchResults($query);
        //     }

        //     return view('results', compact('results'));
        // }
        $validator = Validator::make($request->all(), [
            'queries' => 'required|array|min:1|max:5',
            'queries.*' => 'string|max:255', // Adjust as needed
        ], [
            'queries.required' => 'At least one search query is required.',
            'queries.array' => 'Invalid input format for search queries.',
            'queries.min' => 'Please provide at least one search query.',
            'queries.max' => 'You can provide a maximum of five search queries.',
            'queries.*.string' => 'Invalid search query format.',
            'queries.*.max' => 'Each search query must not exceed 255 characters.', // Adjust as needed
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }


        try {
            // Loop through each query and fetch search results
            $results = [];
            foreach ($request->queries as $query) {
                $searchResults = $this->getSearchResults($query);
                $results[$query] = $searchResults;
            }

            return view('results')->with('results', $results)->with('queries', $request->queries);
        } catch (RequestException $e) {
            // Handle API request failures
            $errorMessage = "Error fetching search results: " . $e->getMessage();
            return back()->withErrors([$errorMessage])->withInput();
        } catch (\Exception $e) {
            // Handle other unexpected errors
            $errorMessage = "An unexpected error occurred: " . $e->getMessage();
            return back()->withErrors([$errorMessage])->withInput();
        }
    }



    public function export(Request $request)
    {

        try {
            $queries = $request->input('queries');

            // Decode the JSON data
            $decodedQueries = json_decode($queries);
            // dd($decodedQueries);
            $data = [];
            foreach ($decodedQueries as $query) {

                $searchResults = $this->getSearchResults($query);

                if (!empty($searchResults['organic_results'])) {
                    foreach ($searchResults['organic_results'] as $result) {
                        $data[] = [
                            'title' => $result['title'],
                            'link' => $result['link'],
                            'snippet' => $result['snippet'] ?? '',
                        ];
                    }
                }
            }

            if (empty($data)) {
                throw new \Exception('No search results found.');
            }


            $fileName = 'search_results_' . time() . '.csv';
            $filePath = 'exports/' . $fileName;
            return Excel::download(new SearchResultsExport($data), $fileName);
        } catch (\Exception $e) {
            return back()->withErrors([$e->getMessage()]);
        }
    }

    private function getSearchResults($query)
    {
        $client = new Client([
            'timeout' => 120, // Set timeout to 120 seconds (adjust as needed)
        ]);

        $apiKey = "407A29623F874C258C144312DFB8C402";
        $url = "https://api.valueserp.com/search?api_key={$apiKey}&q={$query}";

        // dd($url);
        $response = $client->get($url);
        $results = json_decode($response->getBody(), true);

        return $results;
    }
}
