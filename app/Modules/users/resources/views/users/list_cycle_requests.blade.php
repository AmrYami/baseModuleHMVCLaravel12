{{-- Section for List of Requests --}}
<div class="card">
    <div class="card-body">
        <h4>Your Cycle Requests</h4>
        @if($cycleRequests->isEmpty())
            <p>No requests found.</p>
        @else
            <table class="table">
                <thead>
                <tr>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Requested From</th>
                    <th>Created At</th>
                </tr>
                </thead>
                <tbody>
                @foreach($cycleRequests as $request)
                    <tr>
                        <td>{{ $request->title }}</td>
                        <td>{{ $request->description }}</td>
                        <td>{{ $request->requestFrom->name }}</td>
                        <td>{{ $request->created_at->format('Y-m-d') }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
