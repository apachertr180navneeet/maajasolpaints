<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Mobile Number Form</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <div class="container mt-5">
        <h4>Delete Painter Point User Account</h4>
        {{--  <pre>{{ print_r(session()->all(), true) }}</pre>  --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        
        <form method="POST" action="{{ url('/api/delete/') }}">
            @csrf
            <div class="mb-3">
            <label for="mobile" class="form-label">Mobile Number</label>
            <input type="tel" class="form-control" id="mobile" name="mobile" placeholder="Enter 10-digit mobile number" required pattern="[0-9]{10}">
            </div>
            <button type="submit" class="btn btn-primary">Delete User</button>
        </form>
    </div>      
</body>
</html>
