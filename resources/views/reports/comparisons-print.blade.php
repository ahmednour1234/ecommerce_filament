@include('reports.print', [
    'title' => $title,
    'headers' => $headers,
    'rows' => $rows,
    'summary' => $summary ?? [],
    'metadata' => $metadata ?? [],
])

