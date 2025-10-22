<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Google OAuth - LOQ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0"><i class="bi bi-bug me-2"></i>Google OAuth Debug Tool</h4>
                    </div>
                    <div class="card-body">
                        <!-- Status Check -->
                        <div class="alert alert-info">
                            <h5><i class="bi bi-info-circle me-2"></i>Environment Check</h5>
                            <table class="table table-sm mb-0">
                                <tr>
                                    <td><strong>Client ID:</strong></td>
                                    <td><code><?php echo substr(env('GOOGLE_CLIENT_ID'), 0, 20); ?>...</code></td>
                                    <td>
                                        <?php if(env('GOOGLE_CLIENT_ID')): ?>
                                            <span class="badge bg-success"><i class="bi bi-check"></i> Set</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger"><i class="bi bi-x"></i> Not Set</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Client Secret:</strong></td>
                                    <td><code>GOCSPX-***</code></td>
                                    <td>
                                        <?php if(env('GOOGLE_CLIENT_SECRET')): ?>
                                            <span class="badge bg-success"><i class="bi bi-check"></i> Set</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger"><i class="bi bi-x"></i> Not Set</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Redirect URI:</strong></td>
                                    <td><code><?php echo env('GOOGLE_REDIRECT'); ?></code></td>
                                    <td>
                                        <?php if(env('GOOGLE_REDIRECT')): ?>
                                            <span class="badge bg-success"><i class="bi bi-check"></i> Set</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger"><i class="bi bi-x"></i> Not Set</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <!-- Routes Check -->
                        <div class="alert alert-secondary">
                            <h5><i class="bi bi-signpost me-2"></i>Routes</h5>
                            <ul class="mb-0">
                                <li>
                                    <strong>Redirect to Google:</strong> 
                                    <a href="<?php echo url('/auth/google'); ?>" target="_blank">
                                        <?php echo url('/auth/google'); ?>
                                    </a>
                                </li>
                                <li>
                                    <strong>Callback:</strong> 
                                    <code><?php echo url('/auth/google/callback'); ?></code>
                                </li>
                            </ul>
                        </div>

                        <!-- Test Buttons -->
                        <div class="d-grid gap-3">
                            <a href="<?php echo url('/auth/google'); ?>" class="btn btn-danger btn-lg">
                                <svg class="me-2" width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48">
                                    <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
                                    <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>
                                    <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/>
                                    <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
                                </svg>
                                Test Google OAuth Login
                            </a>

                            <a href="<?php echo url('/login'); ?>" class="btn btn-primary">
                                <i class="bi bi-box-arrow-in-right me-2"></i>
                                Go to Login Page
                            </a>
                        </div>

                        <!-- Instructions -->
                        <div class="mt-4">
                            <h5><i class="bi bi-lightbulb me-2"></i>Troubleshooting Steps</h5>
                            <ol>
                                <li>Click "Test Google OAuth Login" button above</li>
                                <li>You should be redirected to Google sign-in page</li>
                                <li>Select your Google account</li>
                                <li>If successful, you'll be redirected to dashboard</li>
                                <li>If error occurs, check <code>storage/logs/laravel.log</code></li>
                            </ol>
                        </div>

                        <!-- Common Issues -->
                        <div class="alert alert-warning mt-3">
                            <h6><i class="bi bi-exclamation-triangle me-2"></i>Common Issues:</h6>
                            <ul class="mb-0 small">
                                <li><strong>redirect_uri_mismatch:</strong> Google Console redirect URI ≠ .env GOOGLE_REDIRECT</li>
                                <li><strong>Access blocked:</strong> OAuth consent screen not configured</li>
                                <li><strong>App isn't verified:</strong> Add email to Test users in Google Console</li>
                                <li><strong>Client unauthorized:</strong> Enable Google+ API in Google Console</li>
                            </ul>
                        </div>

                        <!-- Documentation Link -->
                        <div class="text-center mt-3">
                            <p class="text-muted small mb-0">
                                <i class="bi bi-file-text me-1"></i>
                                See <code>GOOGLE_OAUTH_TROUBLESHOOTING.md</code> for detailed guide
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Session Messages -->
                <?php if(session('error')): ?>
                <div class="alert alert-danger mt-3">
                    <i class="bi bi-x-circle me-2"></i>
                    <?php echo session('error'); ?>
                </div>
                <?php endif; ?>

                <?php if(session('success')): ?>
                <div class="alert alert-success mt-3">
                    <i class="bi bi-check-circle me-2"></i>
                    <?php echo session('success'); ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
