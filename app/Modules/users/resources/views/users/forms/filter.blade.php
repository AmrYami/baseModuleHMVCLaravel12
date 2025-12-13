<div class="card-header pt-6">
    <form method="GET" action="{{ url()->current() }}" class="w-100">
        <div class="row align-items-end">
            <div class="col-md-3 mb-3">
                <label class="form-label">Name (search in first,second,last names)</label>
                <input type="text" name="name" value="{{ request('name') }}" class="form-control"
                    placeholder="Search by name">
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label">mobile</label>
                <input type="text" name="mobile" value="{{ request('mobile') }}" class="form-control"
                    placeholder="Search by mobile">
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label">email</label>
                <input type="text" name="email" value="{{ request('email') }}" class="form-control"
                    placeholder="Search by email">
            </div>

            <div class="col-md-3 mb-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="ki-duotone ki-magnifier fs-2"></i> Filter
                </button>
                <a href="{{ url()->current() }}" class="btn btn-light">
                    <i class="ki-duotone ki-cross fs-2"></i> Reset
                </a>
            </div>
        </div>
    </form>
</div>
