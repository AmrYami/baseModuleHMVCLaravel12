{{-- Section for my Requests --}}
<div class="card">
    <div class="card-body">
        <h4>My requests</h4>
        @if($myRequests->isEmpty())
            <p>No requests found.</p>
        @else
            <table class="table">
                <thead>
                <tr>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Requested To</th>
                    <th>Created At</th>
                </tr>
                </thead>
                <tbody>
                @foreach($myRequests as $req)
                    <tr>
                        <td>{{ $req->title }}</td>
                        <td>{{ $req->description }}</td>
                        <td>{{ $req->requestTo->name }}</td>
                        <td>{{ $req->created_at->format('Y-m-d') }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
