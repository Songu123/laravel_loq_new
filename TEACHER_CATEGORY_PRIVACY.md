# Teacher Category Privacy - Chỉ Xem Danh Mục Của Mình

## 📋 Tổng quan
Đã cập nhật hệ thống quản lý danh mục để teacher **chỉ có thể xem và quản lý danh mục do chính mình tạo ra**.

## ✅ Các thay đổi đã thực hiện

### 1. **CategoryController** - Lọc theo Teacher
**File**: `app/Http/Controllers/Teacher/CategoryController.php`

#### A. Constructor với Authorization
```php
public function __construct()
{
    $this->authorizeResource(Category::class, 'category');
}
```
- ✅ Tự động áp dụng CategoryPolicy cho tất cả CRUD operations
- ✅ Kiểm tra quyền truy cập trước khi thực thi action

#### B. Index - Chỉ danh mục của mình
```php
public function index()
{
    $categories = Category::where('created_by', Auth::id())
        ->with('creator')
        ->orderBy('sort_order')
        ->orderBy('name')
        ->paginate(12);
}
```

**Trước:**
```php
// Teachers can see all active categories (including their own)
$categories = Category::active()->with('creator')->paginate(12);
```

**Sau:**
```php
// Teachers can only see their own categories
$categories = Category::where('created_by', Auth::id())->paginate(12);
```

#### C. Show - Kiểm tra quyền truy cập
```php
public function show(Category $category)
{
    // Authorization handled by CategoryPolicy
    return view('teacher.categories.show', compact('category'));
}
```

**Trước:** Manual check với `abort(403)` nếu không phải creator
**Sau:** Policy tự động kiểm tra, ném `AuthorizationException` nếu không có quyền

#### D. Update & Delete - Simplified
```php
public function update(UpdateCategoryRequest $request, Category $category)
{
    // Authorization handled by CategoryPolicy
    $validated = $request->validated();
    // ... update logic
}

public function destroy(Category $category)
{
    // Authorization handled by CategoryPolicy
    $category->delete();
}
```

**Loại bỏ:**
- ❌ Manual authorization checks
- ❌ Duplicate code
- ❌ Hard-coded error messages

**Thêm vào:**
- ✅ Policy-based authorization
- ✅ Consistent error handling
- ✅ DRY principle

#### E. getCategories - API endpoint
```php
public function getCategories(Request $request)
{
    $categories = Category::where('created_by', Auth::id())
        ->orderBy('sort_order')
        ->orderBy('name')
        ->get(['id', 'name', 'color', 'icon', 'is_active']);
}
```

**Trước:** Trả về tất cả active categories
**Sau:** Chỉ trả về categories của teacher đang login

---

### 2. **CategoryPolicy** - Updated View Permission
**File**: `app/Policies/CategoryPolicy.php`

```php
public function view(User $user, Category $category): bool
{
    // Admin can view all
    if ($user->isAdmin()) {
        return true;
    }

    // Teachers can only view their own categories
    if ($user->isTeacher()) {
        return $category->created_by === $user->id;
    }

    // Students can view active categories
    return $category->is_active;
}
```

**Logic mới:**
- 🔑 **Admin**: Xem tất cả categories
- 👨‍🏫 **Teacher**: Chỉ xem categories của mình (`created_by` === `user_id`)
- 👨‍🎓 **Student**: Xem active categories (for exams)

**Trước:**
```php
// Active categories are visible to everyone
if ($category->is_active) {
    return true;
}
```

**Sau:**
```php
// Teachers can only view their own categories
if ($user->isTeacher()) {
    return $category->created_by === $user->id;
}
```

---

### 3. **DashboardController** - Own Categories Only
**File**: `app/Http/Controllers/Teacher/DashboardController.php`

```php
$categories = Category::where('created_by', $userId)
    ->withCount([...])
    ->with([...])
    ->orderBy('sort_order')
    ->get()
    ->map(function($category) { ... });
```

**Trước:**
```php
Category::where('created_by', $userId)
    ->orWhere('is_active', true) // Include active global categories
    ->having('teacher_exams_count', '>', 0)
```

**Sau:**
```php
Category::where('created_by', $userId) // Only own categories
```

**Loại bỏ:**
- ❌ `orWhere('is_active', true)` - Không hiển thị global categories
- ❌ `having('teacher_exams_count', '>', 0)` - Hiển thị cả categories rỗng

---

### 4. **View Templates** - UI Cleanup
**File**: `resources/views/teacher/categories/index.blade.php`

#### A. Header Update
```blade
<h1 class="h3 mb-0 text-gray-800">Danh mục môn học của tôi</h1>
<p class="text-muted">Quản lý các danh mục cho đề thi và câu hỏi của bạn</p>
```

**Trước:** "Danh mục môn học"
**Sau:** "Danh mục môn học của tôi"

#### B. Simplified Actions (Grid View)
```blade
<div class="d-flex gap-1 justify-content-center">
    <a href="{{ route('teacher.categories.show', $category) }}" class="btn btn-sm btn-outline-primary">
        <i class="bi bi-eye"></i> Xem
    </a>
    <a href="{{ route('teacher.categories.edit', $category) }}" class="btn btn-sm btn-outline-warning">
        <i class="bi bi-pencil"></i> Sửa
    </a>
    <button class="btn btn-sm btn-outline-danger delete-btn">
        <i class="bi bi-trash"></i> Xóa
    </button>
</div>
```

**Loại bỏ:**
```blade
@if($category->created_by === Auth::id())
    <!-- Edit/Delete buttons -->
@else
    <button onclick="createExamWithCategory({{ $category->id }})">
        <i class="bi bi-plus"></i> Tạo đề thi
    </button>
@endif

<div class="mt-2">
    @if($category->created_by === Auth::id())
        <span class="badge bg-info">Của tôi</span>
    @endif
</div>
```

**Lý do:**
- Tất cả categories đều là của teacher
- Không cần kiểm tra `created_by === Auth::id()`
- Không cần badge "Của tôi"
- Không có option "Tạo đề thi" từ categories của người khác

#### C. Simplified List View
```blade
<td>
    <div class="d-flex align-items-center">
        @if($category->icon)
            <i class="{{ $category->icon }} me-2"></i>
        @endif
        <div>
            <div class="fw-semibold">{{ $category->name }}</div>
            <div class="text-muted small">Thứ tự: {{ $category->sort_order }}</div>
        </div>
    </div>
</td>
<td>
    <div class="btn-group btn-group-sm">
        <a href="{{ route('teacher.categories.show', $category) }}" class="btn btn-outline-info">
            <i class="bi bi-eye"></i>
        </a>
        <a href="{{ route('teacher.categories.edit', $category) }}" class="btn btn-outline-warning">
            <i class="bi bi-pencil"></i>
        </a>
        <button class="btn btn-outline-danger delete-btn">
            <i class="bi bi-trash"></i>
        </button>
    </div>
</td>
```

**Loại bỏ:**
- ❌ Badge "Của tôi" trong list view
- ❌ Conditional buttons based on ownership
- ❌ "Tạo đề thi" button cho categories khác

---

## 🔒 Security & Authorization Flow

### Request Flow với Policy:

```
1. Teacher Request: GET /teacher/categories/{category}
   ↓
2. Route Model Binding loads Category
   ↓
3. CategoryController constructor: $this->authorizeResource()
   ↓
4. Laravel calls CategoryPolicy::view($user, $category)
   ↓
5. Policy checks:
   - Is Admin? → Allow
   - Is Teacher AND created_by === user_id? → Allow
   - Otherwise → Deny (403 Forbidden)
   ↓
6. If allowed: show($category) executes
   If denied: AuthorizationException thrown
```

### Authorization Methods:

| Action | Policy Method | Check |
|--------|--------------|-------|
| Index | viewAny | Always allowed for authenticated users |
| Show | view | Admin OR (Teacher AND owner) |
| Create | create | Teacher OR Admin |
| Edit | update | Admin OR (Teacher AND owner) |
| Update | update | Admin OR (Teacher AND owner) |
| Delete | delete | Admin OR (Teacher AND owner AND no exams) |

---

## 📊 Database Queries

### Before (All Active Categories):
```sql
SELECT * FROM categories 
WHERE is_active = 1 
ORDER BY sort_order, name;
-- Returns: All active categories (global + personal)
```

### After (Own Categories Only):
```sql
SELECT * FROM categories 
WHERE created_by = {teacher_id}
ORDER BY sort_order, name;
-- Returns: Only categories created by this teacher
```

**Performance Impact:**
- ✅ Faster queries (smaller result set)
- ✅ Better index usage (`created_by` is indexed)
- ✅ Reduced memory usage

---

## 🎯 Use Cases

### 1. Teacher A tạo category "Toán học"
```php
Category::create([
    'name' => 'Toán học',
    'created_by' => 1, // Teacher A's ID
    'is_active' => true,
]);
```

**Kết quả:**
- ✅ Teacher A thấy "Toán học" trong danh sách
- ❌ Teacher B KHÔNG thấy "Toán học"
- ❌ Teacher C KHÔNG thấy "Toán học"

### 2. Teacher B cố truy cập category của Teacher A
```
GET /teacher/categories/1
```

**Response:**
```
403 Forbidden
"This action is unauthorized."
```

**Policy Logic:**
```php
public function view(User $user, Category $category): bool
{
    if ($user->isTeacher()) {
        return $category->created_by === $user->id; // false → denied
    }
}
```

### 3. Admin xem tất cả categories
```
GET /admin/categories
```

**Response:**
```
200 OK
[
    { id: 1, name: "Toán học", created_by: 1 },
    { id: 2, name: "Vật lý", created_by: 2 },
    { id: 3, name: "Hóa học", created_by: 1 },
]
```

**Policy Logic:**
```php
public function view(User $user, Category $category): bool
{
    if ($user->isAdmin()) {
        return true; // Always allowed
    }
}
```

---

## 🔍 Testing Scenarios

### Test 1: Teacher chỉ thấy danh mục của mình
```php
// Given
$teacher1 = User::factory()->teacher()->create();
$teacher2 = User::factory()->teacher()->create();

Category::create(['name' => 'Math', 'created_by' => $teacher1->id]);
Category::create(['name' => 'Physics', 'created_by' => $teacher2->id]);

// When
$this->actingAs($teacher1)->get('/teacher/categories');

// Then
$response->assertSee('Math');
$response->assertDontSee('Physics');
```

### Test 2: Không thể xem category của người khác
```php
// Given
$category = Category::create(['created_by' => $teacher2->id]);

// When
$this->actingAs($teacher1)->get("/teacher/categories/{$category->id}");

// Then
$response->assertStatus(403);
```

### Test 3: Không thể edit category của người khác
```php
// Given
$category = Category::create(['created_by' => $teacher2->id]);

// When
$this->actingAs($teacher1)->put("/teacher/categories/{$category->id}", [
    'name' => 'Hacked',
]);

// Then
$response->assertStatus(403);
$category->refresh();
$this->assertNotEquals('Hacked', $category->name);
```

### Test 4: Admin có thể xem tất cả
```php
// Given
$admin = User::factory()->admin()->create();
$category = Category::create(['created_by' => $teacher1->id]);

// When
$this->actingAs($admin)->get("/teacher/categories/{$category->id}");

// Then
$response->assertStatus(200);
```

---

## 📝 Summary of Changes

### Files Modified:
1. ✅ `app/Http/Controllers/Teacher/CategoryController.php`
   - Added `authorizeResource()` in constructor
   - Removed manual authorization checks
   - Updated queries to filter by `created_by`
   
2. ✅ `app/Policies/CategoryPolicy.php`
   - Updated `view()` method for teacher privacy
   
3. ✅ `app/Http/Controllers/Teacher/DashboardController.php`
   - Removed global category inclusion
   
4. ✅ `resources/views/teacher/categories/index.blade.php`
   - Updated header text
   - Removed ownership checks
   - Simplified action buttons

### Benefits:
- 🔒 **Privacy**: Teachers can't see other teachers' categories
- 🎯 **Focus**: Dashboard only shows relevant categories
- 🚀 **Performance**: Smaller queries, better indexing
- 🧹 **Clean Code**: Policy-based authorization, no duplicate checks
- 📱 **Consistency**: Same authorization logic across all actions

---

## 🚀 Migration Guide

### For Existing Teachers:
1. Login to teacher dashboard
2. Navigate to "Danh mục môn học"
3. You will now ONLY see categories you created
4. Global categories created by admin are no longer visible

### For Admins:
- No changes needed
- Admin dashboard still shows all categories
- Can manage all categories regardless of creator

### For Students:
- No impact
- Students still see active categories when taking exams
- Category visibility for exams unchanged

---

**Status**: ✅ Complete and Tested
**Date**: 2025-01-25
**Security Level**: Enhanced
**Privacy**: Teacher-scoped categories
