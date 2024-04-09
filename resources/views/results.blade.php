<!-- resources/views/results.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Search Results</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
        }

        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 800px;
            margin: 20px auto;
        }

        .result {
            margin-bottom: 20px;
        }

        .title {
            font-size: 18px;
            font-weight: bold;
            color: #1a0dab;
            margin-bottom: 5px;
        }

        .link {
            color: #1a0dab;
            text-decoration: none;
        }

        .snippet {
            color: #545454;
            margin-bottom: 10px;
        }

        hr {
            border: none;
            border-top: 1px solid #ccc;
            margin: 10px 0;
        }

        a {
            color: #1a0dab;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <button id="exportCsv">Export CSV</button>
        @foreach($results as $query => $result)
            <div class="result">
                <h2>Search Results for: "{{ $query }}"</h2>
                @foreach($result['organic_results'] as $item)
                    <div class="item">
                        <a href="{{ $item['link'] }}" class="title">{{ $item['title'] }}</a><br>
                        <a href="{{ $item['link'] }}" class="link">{{ $item['link'] }}</a><br>
                        <p class="snippet">{{ $item['snippet'] ?? '' }}</p>
                        <hr>
                    </div>
                @endforeach
            </div>
        @endforeach
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#exportCsv').on('click', function() {
                var queries = {!! json_encode($queries) !!};
                var csrfToken = $('meta[name="csrf-token"]').attr('content'); // Fetch CSRF token from meta tag
                var url = "{{ route('export', ['queries' => 'PLACEHOLDER']) }}"; // Use Laravel URL generation

                // Replace the placeholder with the JSON encoded queries
                url = url.replace('PLACEHOLDER', encodeURIComponent(JSON.stringify(queries)));
                
                // Redirect to the URL
                window.location.href = url;
            });
        });
    </script>
</body>
</html>