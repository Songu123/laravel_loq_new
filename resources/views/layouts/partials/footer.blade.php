<footer class="bg-light border-top mt-auto py-4">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <div class="d-flex align-items-center mb-3">
                    <i class="bi bi-mortarboard-fill text-primary me-2"></i>
                    <span class="fw-bold text-primary">LOQ - Hệ thống Trắc nghiệm</span>
                </div>
                <p class="text-muted small">
                    Nền tảng quản lý và tổ chức thi trắc nghiệm trực tuyến hiện đại, 
                    hỗ trợ giáo dục và đào tạo chuyên nghiệp.
                </p>
            </div>
            <div class="col-md-3">
                <h6 class="fw-bold mb-3">Liên kết</h6>
                <ul class="list-unstyled">
                    <li><a href="#" class="text-muted text-decoration-none small">Trang chủ</a></li>
                    <li><a href="#" class="text-muted text-decoration-none small">Hướng dẫn sử dụng</a></li>
                    <li><a href="#" class="text-muted text-decoration-none small">Hỗ trợ kỹ thuật</a></li>
                    <li><a href="#" class="text-muted text-decoration-none small">Liên hệ</a></li>
                </ul>
            </div>
            <div class="col-md-3">
                <h6 class="fw-bold mb-3">Hỗ trợ</h6>
                <ul class="list-unstyled">
                    <li class="text-muted small"><i class="bi bi-envelope me-1"></i> support@loq.edu.vn</li>
                    <li class="text-muted small"><i class="bi bi-telephone me-1"></i> (84) 24 3xxx xxxx</li>
                    <li class="text-muted small"><i class="bi bi-clock me-1"></i> T2-T6: 8:00-17:30</li>
                </ul>
            </div>
        </div>
        <hr class="my-4">
        <div class="row align-items-center">
            <div class="col-md-6">
                <p class="text-muted small mb-0">
                    © {{ date('Y') }} LOQ System. Phát triển bởi team phát triển LOQ.
                </p>
            </div>
            <div class="col-md-6 text-md-end">
                <small class="text-muted">
                    Phiên bản {{ config('app.version', '1.0.0') }} • 
                    <a href="#" class="text-muted">Chính sách bảo mật</a> • 
                    <a href="#" class="text-muted">Điều khoản sử dụng</a>
                </small>
            </div>
        </div>
    </div>
</footer>