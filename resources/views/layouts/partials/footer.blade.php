<footer class="mt-auto py-4 text-white" style="background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <div class="d-flex align-items-center mb-3">
                    <i class="bi bi-mortarboard-fill me-2" style="font-size: 1.5rem;"></i>
                    <span class="fw-bold fs-5">Quiz System</span>
                </div>
                <p class="small opacity-90">
                    Nền tảng quản lý và tổ chức thi trắc nghiệm trực tuyến hiện đại, 
                    hỗ trợ giáo dục và đào tạo chuyên nghiệp.
                </p>
            </div>
            <div class="col-md-3">
                <h6 class="fw-bold mb-3">Liên kết</h6>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="{{ route('home') }}" class="text-white text-decoration-none small opacity-75 hover-opacity-100">Trang chủ</a></li>
                    <li class="mb-2"><a href="#" class="text-white text-decoration-none small opacity-75 hover-opacity-100">Hướng dẫn sử dụng</a></li>
                    <li class="mb-2"><a href="#" class="text-white text-decoration-none small opacity-75 hover-opacity-100">Hỗ trợ kỹ thuật</a></li>
                    <li class="mb-2"><a href="#" class="text-white text-decoration-none small opacity-75 hover-opacity-100">Liên hệ</a></li>
                </ul>
            </div>
            <div class="col-md-3">
                <h6 class="fw-bold mb-3">Hỗ trợ</h6>
                <ul class="list-unstyled">
                    <li class="small opacity-90 mb-2"><i class="bi bi-envelope me-1"></i> support@quiz.edu.vn</li>
                    <li class="small opacity-90 mb-2"><i class="bi bi-telephone me-1"></i> (84) 24 3xxx xxxx</li>
                    <li class="small opacity-90 mb-2"><i class="bi bi-clock me-1"></i> T2-T6: 8:00-17:30</li>
                </ul>
            </div>
        </div>
        <hr class="my-4 border-white" style="opacity: 0.2;">
        <div class="row align-items-center">
            <div class="col-md-6">
                <p class="small mb-0 opacity-75">
                    © {{ date('Y') }} Quiz System. All rights reserved.
                </p>
            </div>
            <div class="col-md-6 text-md-end">
                <small class="opacity-75">
                    Phiên bản {{ config('app.version', '1.0.0') }} • 
                    <a href="#" class="text-white text-decoration-none">Chính sách bảo mật</a> • 
                    <a href="#" class="text-white text-decoration-none">Điều khoản sử dụng</a>
                </small>
            </div>
        </div>
    </div>
</footer>

<style>
.hover-opacity-100:hover {
    opacity: 1 !important;
    transition: opacity 0.2s ease;
}
</style>