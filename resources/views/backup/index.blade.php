<!DOCTYPE html>
<html>
<head>
    <title>Database Backup</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
                        <div id="alert-container"></div>
                        
                        <div class="alert alert-info">
                            <strong>Note:</strong> This will create a backup file in storage/app/backups/ folder.
                        </div>
                        
                        <button id="backup-btn" class="btn btn-primary btn-lg w-100">
                            <span id="btn-text">Create Backup</span>
                            <span id="btn-spinner" class="spinner-border spinner-border-sm ms-2 d-none" role="status"></span>
                        </button>
                    </div>
                </div>

                <div class="card mt-4" id="backup-list">
                    <div class="card-header">
                        <h5>Previous Backups</h5>
                    </div>
                    <div class="card-body" id="backup-table-container">
                        @if(count($backups) > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Filename</th>
                                        <th>Size</th>
                                        <th>Created</th>
                                    </tr>
                                </thead>
                                <tbody id="backup-tbody">
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
                        @else
                        <div class="text-center text-muted" id="no-backups">
                            No previous backups found.
                        </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        document.getElementById('backup-btn').addEventListener('click', function() {
            const btn = this;
            const btnText = document.getElementById('btn-text');
            const spinner = document.getElementById('btn-spinner');
            const alertContainer = document.getElementById('alert-container');
            
            // Show loading state
            btn.disabled = true;
            btnText.textContent = 'Creating...';
            spinner.classList.remove('d-none');
            alertContainer.innerHTML = '';
            
            // Make AJAX request
            fetch('{{ route("backup.run") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({})
            })
            .then(response => response.json())
            .then(data => {
                // Reset button state
                btn.disabled = false;
                btnText.textContent = 'Create Backup';
                spinner.classList.add('d-none');
                
                if (data.success) {
                    // Show success message
                    alertContainer.innerHTML = '<div class="alert alert-success">' + data.message + '</div>';
                    
                    // Update backup list
                    updateBackupList();
                } else {
                    // Show error message
                    alertContainer.innerHTML = '<div class="alert alert-danger">' + data.message + '</div>';
                }
            })
            .catch(error => {
                // Reset button state
                btn.disabled = false;
                btnText.textContent = 'Create Backup';
                spinner.classList.add('d-none');
                
                // Show error message
                alertContainer.innerHTML = '<div class="alert alert-danger">An error occurred. Please try again.</div>';
            });
        });
        
        function updateBackupList() {
            fetch('{{ route("backup.show") }}', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                const container = document.getElementById('backup-table-container');
                
                if (data.backups && data.backups.length > 0) {
                    let tableHtml = `
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Filename</th>
                                        <th>Size</th>
                                        <th>Created</th>
                                    </tr>
                                </thead>
                                <tbody>`;
                    
                    data.backups.forEach(backup => {
                        tableHtml += `
                            <tr>
                                <td>${backup.name}</td>
                                <td>${backup.size}</td>
                                <td>${backup.date}</td>
                            </tr>`;
                    });
                    
                    tableHtml += `
                                </tbody>
                            </table>
                        </div>`;
                    
                    container.innerHTML = tableHtml;
                } else {
                    container.innerHTML = '<div class="text-center text-muted">No previous backups found.</div>';
                }
            });
        }
    </script>
</body>
</html>
