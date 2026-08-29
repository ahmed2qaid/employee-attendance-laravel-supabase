@extends('layouts.app')
@section('title','البدء - نظام الدوام')
@section('content')
<div class="hero-panel">
  <div class="row align-items-center g-3">
    <div class="col-lg-8">
      <div class="text-uppercase fw-bold small mb-2" style="color:#bfdbfe;letter-spacing:.7px">Laravel + Supabase PostgreSQL</div>
      <h2>إدارة دوام الموظفين بسهولة ووضوح</h2>
      <p>أنشئ كشف الدوام، اختر الفترة، وراجع الحضور والورديات المتناوبة والتأخير والوقت الإضافي من مكان واحد.</p>
    </div>
    <div class="col-lg-4 text-lg-start text-center"><i class="bi bi-calendar2-week" style="font-size:4rem;color:#fff"></i></div>
  </div>
</div>
<div class="row g-4">
  <div class="col-lg-8">
    <div class="card app-card h-100">
      <div class="card-body p-lg-4">
        <div class="d-flex justify-content-between align-items-start gap-3 mb-4">
          <div><h4 class="section-title mb-1">إنشاء كشف دوام</h4><div class="section-subtitle">اختر الموظف والفترة، وسيتم توليد الأيام واستثناء الجمعة تلقائيًا.</div></div>
          <span class="badge text-bg-primary-subtle text-primary-emphasis px-3 py-2">كشف جديد</span>
        </div>
        @if($employees->isEmpty())
          <div class="alert alert-warning mb-0"><i class="bi bi-exclamation-triangle-fill ms-2"></i>أضف موظفًا أولًا لبدء إنشاء كشف الدوام.</div>
        @else
          <form method="GET" action="{{ route('attendance.sheet') }}" class="row g-3">
            <div class="col-12"><label class="form-label">الموظف</label><select class="form-select" name="employee_id" required><option value="">اختر الموظف</option>@foreach($employees as $employee)<option value="{{ $employee->id }}">{{ $employee->name }}</option>@endforeach</select></div>
            <div class="col-md-6"><label class="form-label">من تاريخ</label><input class="form-control" type="date" name="start_date" value="2026-07-26" required></div>
            <div class="col-md-6"><label class="form-label">إلى تاريخ</label><input class="form-control" type="date" name="end_date" value="2026-08-25" required></div>
            <div class="col-12 pt-2"><button class="btn btn-primary px-4"><i class="bi bi-arrow-left-circle ms-2"></i>فتح كشف الدوام</button></div>
          </form>
        @endif
      </div>
    </div>
  </div>
  <div class="col-lg-4">
    <div class="card app-card mb-4">
      <div class="card-body p-lg-4">
        <h5 class="section-title mb-1">إضافة موظف</h5><div class="section-subtitle mb-3">أضف اسم الموظف وسيتم تطبيق قواعد الدوام العامة عليه.</div>
        <form method="POST" action="{{ route('employees.store') }}" class="d-flex gap-2">@csrf<input class="form-control" name="name" placeholder="اسم الموظف" required><button class="btn btn-success flex-shrink-0"><i class="bi bi-person-plus ms-1"></i>إضافة</button></form>
      </div>
    </div>
    <div class="card app-card">
      <div class="card-body p-lg-4">
        <div class="d-flex justify-content-between align-items-center mb-2"><h5 class="section-title mb-0">الموظفون</h5><span class="badge text-bg-light">{{ $employees->count() }}</span></div>
        @forelse($employees as $employee)
          <div class="employee-row d-flex justify-content-between align-items-center gap-2"><span class="fw-bold"><i class="bi bi-person-circle text-primary ms-2"></i>{{ $employee->name }}</span><form method="POST" action="{{ route('employees.destroy',$employee) }}">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash3"></i></button></form></div>
        @empty
          <div class="text-secondary py-3 text-center">لا يوجد موظفون حتى الآن.</div>
        @endforelse
      </div>
    </div>
  </div>
</div>
<div class="card app-card mt-4">
  <div class="card-body p-lg-4">
    <h5 class="section-title mb-3">قواعد الحساب</h5>
    <div class="rules-grid">
      <div class="rule-box"><strong><i class="bi bi-sunrise text-primary ms-1"></i>الوردية الصباحية</strong><span>06:00 → 14:00</span></div>
      <div class="rule-box"><strong><i class="bi bi-moon-stars text-primary ms-1"></i>الوردية المسائية</strong><span>14:00 → 22:00</span></div>
      <div class="rule-box"><strong><i class="bi bi-hourglass-split text-primary ms-1"></i>اليوم الكامل</strong><span>8 ساعات = 480 دقيقة</span></div>
      <div class="rule-box"><strong><i class="bi bi-calendar-x text-primary ms-1"></i>إجازة الجمعة</strong><span>مستثناة تلقائيًا من الحساب</span></div>
    </div>
  </div>
</div>
@endsection
