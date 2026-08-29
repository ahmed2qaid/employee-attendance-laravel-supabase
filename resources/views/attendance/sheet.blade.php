@extends('layouts.app')
@section('title','كشف دوام '.$employee->name)
@section('content')
<a href="{{ route('attendance.index') }}" class="back-link"><i class="bi bi-arrow-right"></i>رجوع إلى الرئيسية</a>
<div class="hero-panel">
  <div class="row align-items-center g-3">
    <div class="col-lg-8">
      <div class="text-uppercase fw-bold small mb-2" style="color:#bfdbfe;letter-spacing:.7px">كشف دوام الموظف</div>
      <h2>{{ $employee->name }}</h2>
      <p>{{ $start->format('d/m/Y') }} — {{ $end->format('d/m/Y') }}</p>
    </div>
    <div class="col-lg-4 text-lg-start text-center"><i class="bi bi-person-workspace" style="font-size:4rem;color:#fff"></i></div>
  </div>
</div>
<div class="row g-3 mb-4">
  <div class="col-6 col-xl"><div class="card app-card summary-card"><div class="card-body"><small>إجمالي الحضور</small><h4>{{ $summary['hours_whole'] }}س {{ $summary['minutes_remainder'] }}د</h4></div></div></div>
  <div class="col-6 col-xl"><div class="card app-card summary-card"><div class="card-body"><small>أيام العمل</small><h4>{{ number_format($summary['equivalent_days'],6) }}</h4></div></div></div>
  <div class="col-6 col-xl"><div class="card app-card summary-card"><div class="card-body"><small>الوقت الإضافي</small><h4>{{ intdiv($summary['overtime_minutes'],60) }}س {{ $summary['overtime_minutes']%60 }}د</h4></div></div></div>
  <div class="col-6 col-xl"><div class="card app-card summary-card"><div class="card-body"><small>الغياب / المعلق</small><h4>{{ $summary['absent_days'] }} / {{ $summary['pending_days'] }}</h4></div></div></div>
  <div class="col-6 col-xl"><div class="card app-card summary-card"><div class="card-body"><small>التأخير</small><h4>{{ $summary['late_minutes'] }} د</h4></div></div></div>
  <div class="col-6 col-xl"><div class="card app-card summary-card"><div class="card-body"><small>الانصراف المبكر</small><h4>{{ $summary['early_minutes'] }} د</h4></div></div></div>
</div>
<form method="POST" action="{{ route('attendance.save') }}">
@csrf
<input type="hidden" name="employee_id" value="{{ $employee->id }}">
<input type="hidden" name="start_date" value="{{ $start->format('Y-m-d') }}">
<input type="hidden" name="end_date" value="{{ $end->format('Y-m-d') }}">
<div class="card app-card mb-3">
  <div class="card-body p-lg-4">
    <div class="d-flex justify-content-between align-items-start gap-3 mb-3"><div><h5 class="section-title mb-1">نظام الورديات</h5><div class="section-subtitle">اختر وردية واحدة أو ورديتين بالتناوب الأسبوعي.</div></div><span class="badge text-bg-primary-subtle text-primary-emphasis px-3 py-2"><i class="bi bi-arrow-repeat ms-1"></i>إعداد الوردية</span></div>
    <div class="row g-3">
      <div class="col-md-3"><label class="form-label">عدد الورديات</label><select class="form-select" name="shift_mode"><option value="single" @selected(($employee->shift_mode?:'single')==='single')>وردية واحدة</option><option value="dual" @selected(($employee->shift_mode?:'single')==='dual')>ورديتان بالتناوب أسبوعيًا</option></select></div>
      <div class="col-md-3"><label class="form-label">الوردية 1</label><select class="form-select" name="shift_one"><option value="morning" @selected(($employee->shift_one?:'morning')==='morning')>صباحي 06:00–14:00</option><option value="evening" @selected(($employee->shift_one?:'morning')==='evening')>مسائي 14:00–22:00</option></select></div>
      <div class="col-md-3"><label class="form-label">الوردية 2</label><select class="form-select" name="shift_two"><option value="morning" @selected(($employee->shift_two?:'evening')==='morning')>صباحي 06:00–14:00</option><option value="evening" @selected(($employee->shift_two?:'evening')==='evening')>مسائي 14:00–22:00</option></select></div>
      <div class="col-md-3"><label class="form-label">بداية دورة التناوب</label><input class="form-control" type="date" name="rotation_start" value="{{ optional($employee->rotation_start)->format('Y-m-d') ?: $start->format('Y-m-d') }}"></div>
    </div>
    <div class="row g-3 mt-1">
      <div class="col-md-6"><label class="form-label">سماح التأخير العام</label><input class="form-control" value="{{ $lateGrace }} دقيقة" disabled></div>
      <div class="col-md-6"><label class="form-label">سماح الانصراف المبكر العام</label><input class="form-control" value="{{ $earlyGrace }} دقيقة" disabled></div>
    </div>
    <div class="policy-note mt-3"><i class="bi bi-info-circle-fill ms-2"></i>الوقت الإضافي = كل دقيقة بعد نهاية الوردية المجدولة لذلك اليوم.</div>
  </div>
</div>
<div class="card app-card overflow-hidden">
  <div class="d-flex justify-content-between align-items-center px-4 py-3 border-bottom"><div><h5 class="section-title mb-0">تفاصيل أيام الدوام</h5><div class="section-subtitle">أدخل الحضور والانصراف وسيتم حساب النتائج تلقائيًا.</div></div><i class="bi bi-table text-primary fs-4"></i></div>
  <div class="table-responsive"><table class="table table-hover align-middle mb-0"><thead><tr><th>#</th><th>التاريخ</th><th>اليوم</th><th>الوردية</th><th>الحضور</th><th>الانصراف</th><th>مدة الحضور</th><th>إضافي</th><th>تأخير</th><th>مبكر</th><th>الحالة</th><th>أيام</th></tr></thead><tbody>
  @foreach($days as $i=>$day)
    @php($a=$day['attendance']) @php($hours=intdiv($day['minutes'],60)) @php($mins=$day['minutes']%60) @php($eq=$day['minutes']/480) @php($arabicDays=['Saturday'=>'السبت','Sunday'=>'الأحد','Monday'=>'الاثنين','Tuesday'=>'الثلاثاء','Wednesday'=>'الأربعاء','Thursday'=>'الخميس','Friday'=>'الجمعة'])
    <tr class="{{ $day['is_friday']?'friday-row':'' }}"><td>{{ $i+1 }}</td><td class="fw-bold">{{ $day['date']->format('d/m/Y') }}</td><td>{{ $arabicDays[$day['date']->format('l')] }}</td>
    @if($day['is_friday'])
      <td colspan="9" class="text-center"><span class="badge text-bg-secondary"><i class="bi bi-calendar-x ms-1"></i>إجازة الجمعة</span></td>
    @else
      <td><span class="fw-bold">{{ $day['shift']['label'] }}</span><div class="small text-secondary">{{ $day['shift']['in'] }}–{{ $day['shift']['out'] }}</div></td>
      <td><input type="time" class="form-control form-control-sm time-input" name="rows[{{ $day['key'] }}][check_in]" value="{{ $a?->check_in?substr($a->check_in,0,5):'' }}"></td>
      <td><input type="time" class="form-control form-control-sm time-input" name="rows[{{ $day['key'] }}][check_out]" value="{{ $a?->check_out?substr($a->check_out,0,5):'' }}"></td>
      <td>{{ $day['minutes']? $hours.'س '.$mins.'د':'—' }}</td><td>{{ $day['overtime'] ? intdiv($day['overtime'],60).'س '.($day['overtime']%60).'د' : '—' }}</td><td>{{ $day['late']?:'—' }}</td><td>{{ $day['early']?:'—' }}</td>
      <td>@if($day['status']==='present')<span class="badge text-bg-success">حضور</span>@elseif($day['status']==='pending')<span class="badge text-bg-warning">معلق</span>@else<span class="badge text-bg-danger">غياب</span>@endif</td><td>{{ $day['minutes']?number_format($eq,6):'—' }}</td>
    @endif</tr>
  @endforeach
  </tbody></table></div>
</div>
<div class="sticky-save mt-3"><div class="card app-card"><div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2"><div class="section-subtitle"><i class="bi bi-shield-check ms-1"></i>راجع البيانات ثم احفظ التغييرات.</div><button class="btn btn-primary px-5"><i class="bi bi-save2 ms-2"></i>حفظ الدوام ونظام الورديات</button></div></div></div>
</form>
@endsection
