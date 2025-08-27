<!DOCTYPE html>
<html>
<head>
    <title>Database Backup</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header text-center">
                        <h4>Database Backup</h4>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif
                        
                        @if(session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif
                        
                        <div class="alert alert-info">
                            <strong>Note:</strong> This will create a backup file in storage/app/backups/ folder.
                        </div>
                        
                        <form method="POST" action="{{ route('backup.run') }}">
                            @csrf
                            <button type="submit" class="btn btn-primary btn-lg w-100">Create Backup</button>
                        </form>
                    </div>
                </div>

                @if(count($backups) > 0)
                <div class="card mt-4">
                    <div class="card-header">
                        <h5>Previous Backups</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Filename</th>
                                        <th>Size</th>
                                        <th>Created</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($backups as $backup)
                                    <tr>
                                        <td>{{ $backup['name'] }}</td>
                                        <td>{{ $backup['size'] }}</td>
                                        <td>{{ $backup['date'] }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @else
                <div class="card mt-4">
                    <div class="card-body text-center text-muted">
                        No previous backups found.
                    </div>
                </div>
                @endif

            </div>
        </div>
    </div>
</body>
</html>
